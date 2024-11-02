<?php
// 用户
namespace app\api\controller;
 
use app\common\controller\ApiController;
use think\App;
use app\api\model\Identity;
use app\api\model\Order;
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

use app\api\model\Goods as GoodsModel;

class Goods extends ApiController
{
    // 详情
    public function info(){
        $params = $this->request->param();
        // $post = $this->request->post();
        $goods_id = $params['goods_id'] ??0;
        if (!$goods_id) {
            return msg(100,'商品参数错误','');
        }
        $goodsModel = new GoodsModel;
        $goodsData = $goodsModel->where('status' ,1)->find($goods_id);
        if (empty($goodsData)) {
            return msg(100,'商品错误','');
        }
        $identityModel = new Identity();
        $where = [
            ['goods_id','=',$goodsData['id']],
            ['type','=',2]
        ];
        $identity = $identityModel->where($where)->field('name,head_image')->find();
        $goodsData['identity'] = $identity;
        if($this->identity['type'] == 5){
            $orderModel = new Order();
            $goodsData['is_order'] = $orderModel->where([['user_id','=',$this->identity['user_id']],['pay_status','=',1]])->count();
        }
        return msg(200,'获取成功',$goodsData);
    }    
}