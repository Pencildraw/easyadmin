<?php
// 用户
namespace app\api\controller;
 
use app\common\controller\ApiController;
use think\App;
use think\facade\Env;
// use app\admin\service\ConfigService;
use app\BaseController;
use app\common\constants\AdminConstant;
use app\common\service\AuthService;
use EasyAdmin\tool\CommonTool;
use think\facade\View;
use think\Model;
use think\Request;
use think\facade\Config;
use think\facade\Cache;  
use think\facade\Http;  
use app\api\model\Goods as goodsModel;
use app\api\model\Order as orderModel;
use app\api\model\OrderSpec as orderSpecModel;
use app\api\model\Identity;

class Order extends ApiController
{
    protected $orderModel;
    // 初始化
    protected function initialize()
    {
        //继承验证、登录通用方法
        parent::initialize();
        $this->orderModel = new orderModel;
    }

    // 列表
    public function list(){
  //      订单列表三种情况,身份为业务员的时候,传type,其他身份不用
//1. 业务员 订单列表
//      我的订单
//      店铺订单
//      店铺详情-全部订单
//2. 店铺 订单列表
//      我的订单
//3. 用户 订单列表
//        ----
        $identity = $this->identity;
        $post = $this->request->post();
        $rule = [
            'page|页数'       => 'require',
            'limit|条数'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        $where = [
            ['pay_status','=',1]
        ];
        //3业务员 salesman_id 4 店铺 shop_id 5用户 user_id
//        var_dump($user);exit;
//        if($identity['type'] == 3){
//            $field = ['salesman_id','=',$identity['id']];
//        }elseif($identity['type'] == 4){
//            $field = ['shop_id','=',$identity['id']];
//        }else{
//            $field = ['user_id','=',$identity['user_id']];
//        }
        if($identity['type'] == 3 && isset($post['type'])){
            if($post['type']== 1){
                $where[] = ['salesman_id','=',$identity['id']];
                $where[] = ['shop_id','=',0];
            }elseif($post['type'] == 2){
                $where[] = ['salesman_id','=',$identity['id']];
                $where[] = ['shop_id','<>',0];
            }elseif($post['type'] == 3){
                $where[] = ['shop_id','=',$post['id']];
            }else{
                $where[] = ['user_id','=',$identity['user_id']];
            }
//            1:我的订单 2全部订单 3店铺订单
        }elseif($identity['type'] == 4){
            $where[] = ['shop_id','=',$identity['id']];
        }else{
            $where[] = ['user_id','=',$identity['user_id']];
        }
        $list = $this->orderModel::with('orderList')
            ->where($where)
            ->field('id,order_name,order_sn,total_amount,goods_num,gift_num
                ,(SELECT shop_name FROM ea_company_identity WHERE ea_mall_order.shop_id=ea_company_identity.id  AND ea_mall_order.pay_status=1) AS identity_shop_name
                ,(SELECT head_image FROM ea_company_identity WHERE ea_mall_order.shop_id=ea_company_identity.id  AND ea_mall_order.pay_status=1) AS identity_head_image
            ')
            // ,(SELECT name FROM ea_company_identity WHERE ea_mall_order.dealer_id=ea_company_identity.id AND ea_mall_order.pay_status=1) AS identity_dealer_name
            ->page($post['page'],$post['limit'])
            ->order('id desc')
            ->select();
            $identityModel = new \app\api\model\Identity();
            $identityDealer = $identityModel->find(1);
            foreach ($list as $key => &$value) {
                $value->identity_dealer_name = $value->identity_shop_name ??$identityDealer->name;
                $value->identity_head_image = $value->identity_head_image ??$identityDealer->head_image;
                // $value->identity_shop_name = $value->identity_shop_name??'';
            }
        $count = $this->orderModel->where($where)->count();
        $data = [
            'rows'  => $list,
            'total' => $count,
        ];
        return msg(200,'获取成功',$data);
    }

    // 详情
    public function info(){
        $post = $this->request->post();
        $rule = [
            'order_id|订单参数'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        $orderData = $this->orderModel::with('orderList')
            ->where('id',$post['order_id'])
            ->field('id,order_name,order_phone,order_address,total_amount,goods_num,gift_num,order_sn,remark,create_time
            ,province,city,area,dealer_id,shop_id')
            ->find();
        $identityModel = new \app\api\model\Identity;
        // $orderData->identity_dealer = $identityModel->where('id',$orderData->dealer_id)->value('name');
        // $orderData->identity_shop = $identityModel->where('id',$orderData->shop_id)->value('name');
        if (!$orderData->shop_id) {
            $identityData = $identityModel->where('id',$orderData->dealer_id)->field('name identity_dealer,head_image')->find();
        } else {
            $identityData = $identityModel->where('id',$orderData->shop_id)->field('shop_name identity_dealer,head_image')->find();
        }
        $orderData->identity_dealer = $identityData->identity_dealer ??'';
        $orderData->head_image = $identityData->head_image ??'';
        $goodsModel = new \app\api\model\Goods();
        $orderData->goods_image = $goodsModel->where('is_default',1)->value('images');

        return msg(200,'获取成功',$orderData);
    }

    // 计算价格 商品 赠品数量
    public function calculation_rules(){
        $post = $this->request->post();
        $rule = [
            'goods_id|商品'       => 'require',
            'num|商品数量'       => 'require',
            'price|商品价格'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        // 订单商品
        $goodsModel = new goodsModel();
        $goodsData = $goodsModel->where('status',1)->find($post['goods_id']);
        if (empty($goodsData)) {
            return msg(100,'商品不存在或已失效',$post); 
        }
        if ($goodsData->price <> $post['price']) {
            return msg(100,'商品价格不一致',$post); 
        }
        // $data['ok_amount'] = $post['num'] * $goodsData->price;
        $data['ok_amount'] = number_format(($post['num'] * $goodsData->price),2,".","");
        // 赠品数量
        if ($post['num'] < 10) {
            $gift_num = 0;
        } else if ($post['num'] >= 50) {
            $gift_num = intval($post['num']/5);
        } else {
            $gift_num = config('app.git_goods')[intval($post['num']/10)*10];
        }
        $data['gift_num'] = $gift_num; //赠品数量
        return msg(200,'获取成功',$data);
    }

    // 下单
    public function create(){
        $post = $this->request->post();
        $rule = [
            'order_name|姓名'       => 'require',
            'order_phone|手机号'       => 'require',
            'order_address|地址'       => 'require',
            'total_amount|订单总金额'       => 'require',
            'ok_amount|支付金额'       => 'require',
            'goods_id|商品'       => 'require',
            'num|商品数量'       => 'require',
            'price|商品价格'       => 'require',
            'goods_name|商品名称'       => 'require',
            'identity_id|二维码所属人'       => 'require',
            'type|二维码所属人类别'       => 'require',
            'province|省'       => 'require',
            'city|市'       => 'require',
            'area|区'       => 'require',
        ];
        // $message = [
        //     'user_name.max' => ':attribute不能超过5位!',
        // ];
        $this->validate($post, $rule,[]);
        if (!in_array($post['type'],[3,4])) {
            return msg(100,'二维码所属人类别不符',$post['type']);
        }
        $create_time = time();
        // 订单商品
        $goodsModel = new goodsModel();
        $goodsData = $goodsModel->where('status',1)->find($post['goods_id']);
        if (empty($goodsData)) {
            return msg(100,'商品不存在或已失效',$post); 
        }
        $total_fee = number_format(($goodsData->price * $post['num']),2,".",""); 
        
        // 主订单
        $orderData = [
            'order_status' => 0,
            'user_id' => $this->identity['user_id'], //用户ID
            'order_name' => $post['order_name'],
            'order_phone' => $post['order_phone'],
            'order_address' => $post['order_address'],
            'total_amount' => $total_fee,
            'order_amount' => $total_fee,
            // 'ok_amount' => $post['ok_amount'],
            'supplier_id' => $this->supplier_id, //供应商ID
            // 'dealer_id' => $this->dealer_id, //经销商ID
            'identity_id' => $this->identity['id'], //经销商ID
            'remark' => $post['remark'] ??'',
            'order_sn' => generateNumber(),
            'province' => $post['province'],
            'city' => $post['city'],
            'area' => $post['area'],
            'create_time' => $create_time,
        ];
        // 商品
        $orderData['goods_num'] = $post['num']; //商品数量
        $specData = [
            'goods_name' => $goodsData['name'],
            'goods_price' => $goodsData['price'],
            'goods_attr' => $goodsData['attr'],
            'purchase_price' => $goodsData['purchase_price'],
            'cate_id' => $goodsData['cate_id'],
            'goods_id' => $goodsData['id'],
            // 'user_id' => 0,
            'goods_num' => $post['num'],
            'salesman_remind' => $goodsData['salesman_remind'],
            'shipping_cost' => $goodsData['shipping_cost'],
            'images' => $goodsData['images'],
            'create_time' => $create_time,
        ];
        // 赠品规则 10-1 20-3 30-5 40-8 ;50以上 买5赠1
        if ($post['num'] < 10) {
            $gift_num = 0;
        } else if ($post['num'] >= 50) {
            $gift_num = intval($post['num']/5);
        } else {
            $gift_num = config('app.git_goods')[intval($post['num']/10)*10];
        }
        $orderData['gift_num'] = $gift_num; //赠品数量
        $specData['gift_num'] = $gift_num; //赠品数量
        //事务
        $this->orderModel->startTrans();
        try {
            $salesman_id = 0;
            $shop_id = 0;
            // 订单绑定身份
            if ($post['type'] == 4) {
                // 店铺
                $identityModel = new \app\api\model\Identity();
                $identity_shop = $identityModel::where('id',$post['identity_id'])->find();
                // $shop_id = $identity_shop->id ??0;
                $shop_id = $post['identity_id'] ??0;
                $salesman_id = $identity_shop->salesman_id ??0;
            } else if($post['type'] == 3) {
                // 业务员
                // $identityModel = new \app\api\model\Identity();
                // $identity_shop = $identityModel::where('id',$post['identity_id'])->find();
                // $shop_id = $identity_shop->id ??0;
                // $salesman_id = $identity_shop->salesman_id ??0;
                $salesman_id = $post['identity_id'] ??0;
            }
            $orderData['salesman_id'] = $salesman_id;
            $orderData['shop_id'] = $shop_id;

            $insertGetId = $this->orderModel->insertGetId($orderData);
            if (!$insertGetId) {
                $this->orderModel->rollback();
                throw new \Exception('订单保存失败');
            }
            // 订单商品
            $specData['order_id'] = $insertGetId;
            $specOrderModel = new orderSpecModel;
            if (!$specOrderModel->insert($specData)) {
                $this->orderModel->rollback();
                throw new \Exception('订单商品保存失败');
            }
            // 订单赠品
            // if (!empty($giftSpecData)) {
            //     $giftSpecData['order_id'] = $insertGetId;
            //     if (!$specOrderModel->insert($giftSpecData)) {
            //         $this->orderModel->rollback();
            //         throw new \Exception('订单赠品保存失败');
            //     }
            // }
            $goodsData->sales_volume += $post['num'];
            $goodsData->update_time = time();
            $goodsData->save();

        } catch (\Exception $e) {
            $this->orderModel->rollback();
            return msg(100,'保存失败:'.$e->getMessage(),$post); 
        }
        $this->orderModel->commit();
        return msg(200,'保存成功',['order_id'=>$insertGetId]);
    }
    // 修改订单
    public function update(){
        $post = $this->request->post();
        $rule = [
            'order_id|订单参数'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        // 校验
        $row = $this->orderModel->find($post['order_id']);
        if (empty($row)) {
            return msg(100,'无效订单',$post);
        }
        // 修改订单
        if (!empty($post['order_name'])) {
            $row->order_name = $post['order_name'];
        }
        if (!empty($post['order_phone'])) {
            $row->order_phone = $post['order_phone'];
        }
        if (!empty($post['order_address'])) {
            $row->order_address = $post['order_address'];
        }
        if (!empty($post['remark'])) {
            $row->remark = $post['remark'];
        }
        //事务
        $this->orderModel->startTrans();
        try {
            $row->save();
        } catch (\Exception $e) {
            $this->orderModel->rollback();
            return msg(100,'保存失败',$post);
        }
        $this->orderModel->commit();
        return msg(200,'保存成功',['order_id'=>$row->id]);
    }
    
    // 微信支付回调
    public function orderNotify(){
        $data = file_get_contents('php://input');
        $file=fopen("file.txt","w");
        if($file){
            fwrite($file,$data);
            fclose($file);
        }
        $message = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($message['result_code'] == 'SUCCESS' && $message['return_code'] == 'SUCCESS') {
            $str = $message['out_trade_no'];
            $pos = strpos($str, "|");

            if ($pos !== false) {
                // 使用 substr 去掉 | 及其之后的部分
                $order_sn = substr($str, 0, $pos);
            } else {
                // 如果没有找到指定的字符串，原样输出
                $order_sn = $str;
            }
            // $openid = $message['openid'];                  // 付款人openID
            $total_fee = ($message['total_fee']) / 100;            // 付款金额
            $transaction_id = $message['transaction_id'];  // 微信支付流水号
            $order = $this->orderModel->where(['order_sn' => $order_sn])->find();
            $payLogModel = new \app\api\model\OrderPayLog(); //支付记录日志
            $create_time = time();
            if($order){
                // $pay_status = 1; //支付类型 {select}  (0:未支付 ,1:已支付)
                //事务开始
                $this->orderModel->startTrans();
                try{
                //   $orderData = [
                    //   'ok_amount'         =>  $total_fee + $order->ok_amount,
                    //   'pay_status'            =>  1,
                    //   'transaction_id'            =>  $transaction_id,
                //   ];
                    $order->ok_amount = $total_fee;
                    $order->pay_status = 1;
                    $order->transaction_id = $transaction_id;
                //   $order->update_time = $create_time;
                    $order->save();
                //   $this->orderModel->where('id', $order->id)->update($orderData);
                    // $userModel = new \app\api\model\User();
                    $payLogData = [
                        'order_id'        =>  $order->id,
                        'user_id'         =>  $order->user_id,
                        'identity_id'     =>  $order->identity_id,
                        'transaction_id'  =>  $transaction_id,
                        'total_fee'       =>  $total_fee,
                        'order_sn'        =>  $order_sn,
                        'pay_status'      =>  1,
                        'create_time'     =>  $create_time,
                    ];
                    $payLogModel->insert($payLogData);
                    // return sprintf("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>");
                } catch (\Exception $e) {
                    $this->orderModel->rollback();
                    // 订单修改失败记录日志
                //   $payLogData = [
                    //   'order_id'        =>  $order->id,
                    //   'user_id'         =>  $order->user_id,
                    //   'identity_id'     =>  $order->identity_id,
                    //   'transaction_id'  =>  $transaction_id,
                    //   'total_fee'       =>  $total_fee,
                    //   'order_sn'        =>  $order_sn,
                    //   'pay_status'      =>  0,
                    //   'create_time'     =>  $create_time,
                //   ];
                //   $payLogModel->insert($payLogData);
                    //   exit;
                    return msg(100,'',$e->getMessage());
                }
                $this->orderModel->commit();
            }
        }
    }

    // 微信退款回调
    public function refundNotify(){
        $data = file_get_contents('php://input');
        $file=fopen("file_refund.txt","w");
        if($file){
            fwrite($file,$data);
            fclose($file);
        }
        // $message = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        // if ($message['result_code'] == 'SUCCESS' && $message['return_code'] == 'SUCCESS') {
        //     $str = $message['out_trade_no'];
        //     $pos = strpos($str, "|");

        //     if ($pos !== false) {
        //         // 使用 substr 去掉 | 及其之后的部分
        //         $order_sn = substr($str, 0, $pos);
        //     } else {
        //         // 如果没有找到指定的字符串，原样输出
        //         $order_sn = $str;
        //     }
        //     // $openid = $message['openid'];                  // 付款人openID
        //     $total_fee = ($message['total_fee']) / 100;            // 付款金额
        //     $transaction_id = $message['transaction_id'];  // 微信支付流水号
        //     $order = $this->orderModel->where(['order_sn' => $order_sn])->find();
        //     $payLogModel = new \app\api\model\OrderPayLog(); //支付记录日志
        //     $create_time = time();
        //     if($order){
        //         // $pay_status = 1; //支付类型 {select}  (0:未支付 ,1:已支付)
        //         //事务开始
        //         $this->orderModel->startTrans();
        //         try{
        //         //   $orderData = [
        //             //   'ok_amount'         =>  $total_fee + $order->ok_amount,
        //             //   'pay_status'            =>  1,
        //             //   'transaction_id'            =>  $transaction_id,
        //         //   ];
        //             $order->ok_amount = $total_fee;
        //             $order->pay_status = 1;
        //             $order->transaction_id = $transaction_id;
        //         //   $order->update_time = $create_time;
        //             $order->save();
        //         //   $this->orderModel->where('id', $order->id)->update($orderData);
        //             // $userModel = new \app\api\model\User();
        //             $payLogData = [
        //                 'order_id'        =>  $order->id,
        //                 'user_id'         =>  $order->user_id,
        //                 'identity_id'     =>  $order->identity_id,
        //                 'transaction_id'  =>  $transaction_id,
        //                 'total_fee'       =>  $total_fee,
        //                 'order_sn'        =>  $order_sn,
        //                 'pay_status'      =>  1,
        //                 'create_time'     =>  $create_time,
        //             ];
        //             $payLogModel->insert($payLogData);
        //             // return sprintf("<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>");
        //         } catch (\Exception $e) {
        //             $this->orderModel->rollback();
        //             // 订单修改失败记录日志
        //         //   $payLogData = [
        //             //   'order_id'        =>  $order->id,
        //             //   'user_id'         =>  $order->user_id,
        //             //   'identity_id'     =>  $order->identity_id,
        //             //   'transaction_id'  =>  $transaction_id,
        //             //   'total_fee'       =>  $total_fee,
        //             //   'order_sn'        =>  $order_sn,
        //             //   'pay_status'      =>  0,
        //             //   'create_time'     =>  $create_time,
        //         //   ];
        //         //   $payLogModel->insert($payLogData);
        //             //   exit;
        //             return msg(100,'',$e->getMessage());
        //         }
        //         $this->orderModel->commit();
        //     }
        // }
    }
}