<?php
// 命名空间定义
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

class Index extends ApiController
{
    public function __construct(App $app)
    {
        parent::__construct($app);

        // 初始化时自定义方法

    }

    public function index(){
        // return $this->request->param('name');
        return json([
            'code' => 1,
            'msg' => '测试成功',
            'params' => $this->request->param(''),
        ]);
    }
}