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
        $orderList = $this->orderModel::with('orderList')->where('user_id',$this->identity['user_id'])->select()->toArray();
        if (empty($orderList)) {
            return msg(100,'获取失败',''); 
        } else {
            return msg(200,'获取成功',$orderList);
        }
        

    }

    // 列表
    public function info(){
        $post = $this->request->post();
        $rule = [
            'order_id|订单参数'       => 'require',
        ];
        $this->validate($post, $rule,[]);
        $orderList = $this->orderModel::with('orderList')->where('id',$post['order_id'])->where('user_id',$this->identity['user_id'])->find()->toArray();
        if (empty($orderList)) {
            return msg(100,'获取失败',''); 
        } else {
            return msg(200,'获取成功',$orderList);
        }
        

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
        ];
        // $message = [
        //     'user_name.max' => ':attribute不能超过5位!',
        // ];
        $this->validate($post, $rule,[]);
        // 主订单
        $orderData = [
            'order_status' => 0,
            'user_id' => $this->identity['user_id'], //用户ID
            'order_name' => $post['order_name'],
            'order_phone' => $post['order_name'],
            'order_address' => $post['order_name'],
            'total_amount' => $post['total_amount'],
            'order_amount' => $post['total_amount'],
            'ok_amount' => $post['ok_amount'],
            // 'supplier_id' => $this->supplier_id, //供应商ID
            // 'dealer_id' => $this->dealer_id, //经销商ID
            'identity_id' => $this->identity['id'], //经销商ID
            'remark' => $post['remark'] ??'',
        ];
        // 订单商品
        $goodsModel = new goodsModel();
        $goodsData = $goodsModel->where('status',1)->find($post['goods_id']);
        if (empty($goodsData)) {
            return msg(100,'商品不存在或已失效',$post); 
        }
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
        ];

        // $orderModel = new orderModel;
        //事务
        $this->orderModel->startTrans();
        try {

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

        } catch (\Exception $e) {
            $this->orderModel->rollback();
            return msg(100,'保存失败:'.$e->getMessage(),$post); 
        }
        $this->orderModel->commit();
        return msg(200,'保存成功',['order_id'=>$insertGetId]);

    }    
}