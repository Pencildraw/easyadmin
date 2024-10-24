<?php
// 用户
namespace app\api\controller;
 
// use app\common\controller\AdminController;
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

class Order extends BaseController
{
    // 下单
    public function create_order(){
        $params = $this->request->param();
    }    
}