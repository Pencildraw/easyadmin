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
        return msg(200,'获取成功',$goodsData);
    }    
}