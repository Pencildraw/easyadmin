<?php
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
use app\api\model\User as userModel;
use app\api\model\Identity as identityModel;

class Identity extends ApiController
{
    protected $identityModel;
    // 初始化
    protected function initialize()
    {
        //继承验证、登录通用方法
        parent::initialize();
        $this->identityModel = new identityModel;
    }

    // 详情
    public function info(){
        $identityData = $this->identityModel->alias('i')
            ->leftJoin('company_user u' ,'i.user_id = u.id')
            ->where('i.user_id',$this->identity['user_id'])
            ->where('i.status',1)
            ->where('u.status',1)
            ->field('i.id,i.name,i.phone,i.email,i.status,i.create_time,i.dealer_id,i.goods_id,i.qrcode_image,i.type,i.address,i.head_image,i.binding_status,i.user_id,i.shop_name,i.shop_address')
            ->find()->toArray();
        if (empty($identityData)) {
            return msg(100,'获取失败',''); 
        } else {
            $typeList = $this->identityModel->typeList();
            $identityData['type_title'] = $typeList[$identityData['type']] ??'';
            // 用户订单汇总
            $orderModel = new \app\api\model\Order();
            if ($orderModel->where('user_id',$this->identity['user_id'])->count() <1) {
                $order['sum_order'] = 0; //订单总数
                $order['sum_amount'] = 0; //订单总销售额
                $order['monther_order'] = 0; //月订单总数
                $order['monther_amount'] = 0; //月订单总销售额
            } else {
                $orderSum = $orderModel->where('user_id',$this->identity['user_id'])
                    ->field('count(id) as sum_order ,sum(ok_amount) as sum_amount')
                    ->where('pay_status',1)
                    ->select()->toArray();
                // 当前月订单 销售额
                $currentDate = strtotime(date('Y').'-'.date('m').'-'.'01'); //当前月份时间戳
                $orderMonther = $orderModel->where('user_id',$this->identity['user_id'])
                    ->where([['create_time','>=',$currentDate]])
                    ->where('pay_status',1)
                    ->field('count(id) as monther_order ,sum(ok_amount) as monther_amount')
                    ->select()->toArray();
                $order['sum_order'] = $orderSum[0]['sum_order']??0;
                $order['sum_amount'] = $orderSum[0]['sum_amount'] ??0.00;
                $order['monther_order'] = $orderMonther[0]['monther_order'] ??0;
                $order['monther_amount'] = $orderMonther[0]['monther_amount'] ??0.00;
            }
            $identityData['order'] = $order;
            
            return msg(200,'获取成功',$identityData);
        }
    }

    // 店铺详情
    public function shopInfo(){

        $post = $this->request->post();
        $rule = [
            'shop_id|店铺'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        $shop_id = $post['shop_id'];
        $identity = $this->identityModel->alias('i')
            // ->leftJoin('company_user u' ,'i.user_id = u.id')
            ->where('i.id',$shop_id)
            ->where('i.type',4)
            ->where('i.status',1)
            // ->where('u.status',1)
            ->field('i.id,i.name,i.phone,i.status,i.create_time,i.dealer_id,i.goods_id,i.qrcode_image,i.type,i.address,i.head_image,i.binding_status,i.user_id,i.shop_name
                ,i.shop_address,province,city,area')
            ->find();
        if (empty($identity)) {
            return msg(100,'获取失败',''); 
        } else {
            $identityData = $identity->toArray();
            $typeList = $this->identityModel->typeList();
            $identityData['type_title'] = $typeList[$identityData['type']] ??'';
            // 用户订单汇总
            $orderModel = new \app\api\model\Order();
            if ($orderModel->where('user_id',$this->identity['user_id'])->count() <1) {
                $order['sum_order'] = 0; //订单总数
                $order['sum_amount'] = 0; //订单总销售额
                $order['monther_order'] = 0; //月订单总数
                $order['monther_order'] = 0; //月订单总销售额
            } else {
                $orderSum = $orderModel->where('shop_id',$shop_id)
                    ->field('count(id) as sum_order ,sum(ok_amount) as sum_amount')
                    ->select()->toArray();
                // 当前月订单 销售额
                $currentDate = strtotime(date('Y').'-'.date('m').'-'.'01'); //当前月份时间戳
                $orderMonther = $orderModel->where('shop_id',$shop_id)
                    ->where([['create_time','>=',$currentDate]])
                    ->field('count(id) as monther_order ,sum(ok_amount) as monther_amount')
                    ->select()->toArray();
                $order['sum_order'] = $orderSum[0]['sum_order']??0;
                $order['sum_amount'] = $orderSum[0]['sum_amount'] ??0.00;
                $order['monther_order'] = $orderMonther[0]['monther_order'] ??0;
                $order['monther_amount'] = $orderMonther[0]['monther_amount'] ??0.00;
            }
            $identityData['order'] = $order;
            $identityData['identity_dealer'] = $this->identityModel->where('id',$identityData['dealer_id'])->value('name');
            
            return msg(200,'获取成功',$identityData);
        }
    }

    // 修改
    public function update(){

        $post = $this->remove_empty_arrays($this->request->post());
        // 校验密码
        $pattern = "/^[a-zA-Z0-9]+$/";  //密码只包含数字 字母
        if (!preg_match($pattern, $post['new_password']) || !preg_match($pattern, $post['new_password'])) {  
            return msg(100,'请输入规范密码(字母,数字)','');
        } else if ($post['new_password'] <> $post['confirm_password']) {
            return msg(100,'密码不一致','');
        } else {
            $data['password'] = md5(md5($post['new_password']));
        }

        // 店铺地址
        if (!empty($post['shop_address'])) {
            $data['shop_address'] = $post['shop_address'];
        }
        //事务
        $this->identityModel->startTrans();
        try {
            $this->identityModel->where('user_id',$this->identity['user_id'])->save($data);
        } catch (\Exception $e) {
            $this->identityModel->rollback();
            return msg(100,'保存失败',$post); 
        }
        $this->identityModel->commit();
        return msg(200,'保存成功','');
    }    

    // 创建
    public function createShop(){
        $post = $this->request->post();
        $rule = [
            'name|名称(账号)'       => 'require',
            'shop_name|店铺名称'       => 'require',
            'password|密码'       => 'require',
            'shop_phone|店铺手机号'       => 'require',
            'shop_address|店铺地址'       => 'require',
            'head_image|头像'       => 'require',
            'province|省'       => 'require',
            'city|市'       => 'require',
            'area|区'       => 'require',
        ];
        // $message = [
        //     'user_name.max' => ':attribute不能超过5位!',
        // ];
        $this->validate($post, $rule,[]);
        $post = trimArray($post);

        $goodsModel = new \app\api\model\Goods;
        $goods_id = $goodsModel::where('is_default',1)->where('status',1)->value('id');
        if (!$goods_id) {
            return msg(100,'关联商品已下架,无法添加店铺',$post);
        }
        $type = 4; //店铺类别
        $post['password'] = empty($post['password']) ?'123456':$post['password'];
        // 店铺信息
        $identityData = [
            'name' => $post['name'],
            'phone' => $post['shop_phone'],
            'supplier_id' => $this->supplier_id,
            // 'dealer_id' => 0,
            // 'salesman_id' => 0,
            'goods_id' => $goods_id,
            // 'qrcode_image' => 0,
            'type' => 4, //店铺
            'head_image' => $post['head_image'],
            // 'binding_status' => 0,
            'password' => $post['password'],
            // 'user_id' => $this->identity['user_id'],
            'salesman_id' => $this->identity['id'], //业务员ID
            'shop_name' => $post['shop_name'],
            'shop_address' => $post['shop_address'],
            'province' => $post['province'],
            'city' => $post['city'],
            'area' => $post['area'],
            'create_time' => time(),
        ];
        // print_r($identityData); exit;
        //事务
        $this->identityModel->startTrans();
        try {
            $insertGetId = $this->identityModel->insertGetId($identityData);
            if (!$insertGetId) {
                $this->identityModel->rollback();
                throw new \Exception('店铺添加错误');
            }
            // 生成商品二维码
            // $insertGetId = 1;
            $prcodeService = new \app\admin\service\QrcodeService;
            $result = $prcodeService->generate($insertGetId ,$type ,$goods_id);
            if (!$result['code'] || empty($result['qrcode_image'])) {
                $this->identityModel->rollback();
                throw new \Exception('店铺商品二维码创建失败');
            }
            $web_url = Config::get('app')['const_data']['web_url'];
            if (!$this->identityModel->where('id',$insertGetId)->update(['qrcode_image'=> $web_url.$result['qrcode_image']])) {
                $this->identityModel->rollback();
                throw new \Exception('店铺修改错误');
            }

        } catch (\Exception $e) {
            $this->identityModel->rollback();
            return msg(100,'保存失败: '.$e->getMessage(),$post); 
        }
        $this->identityModel->commit();
        return msg(200,'保存成功','',$insertGetId);
    }
    //店铺列表
    public function shopList(){
        $post = $this->request->post();
        $rule = [
            'page|页数'       => 'require',
            'limit|条数'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        $where = [
            ['salesman_id','=',$this->identity['id']]
        ];
        $list= $this->identityModel
            ->where($where)
            ->page($post['page'], $post['limit'])
            ->field('id,name,phone,shop_address,head_image,shop_name,user_id')
            ->select();
        $count = $this->identityModel->where($where)->count();
        $data = [
            'rows'  => $list,
            'total' => $count,
        ];
        return msg(200,'获取成功',$data);

    }
}