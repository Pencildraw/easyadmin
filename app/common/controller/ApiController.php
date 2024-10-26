<?php

namespace app\common\controller;

use app\BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Db;

/**
 * 控制器基础类
 */
class ApiController extends BaseController
{
    // protected $supplier_id = 1; //供应商ID
    // protected $dealer_id = 6; //经销商ID
    // protected $user_id = 1; //用户ID
    protected $identity = []; //身份信息
    // 初始化
    protected function initialize()
    {
        parent::initialize();
        //验证登录
        $token = $this->request->header('x-access-token');
        // var_dump($token); exit;
        if(!isset($token) || $token == ''){
            echo json_encode(['code'=>100,'msg'=>'请先登录','data'=>'']);
            exit;
        }
        // 验证身份
        $identity = JWT::decode($token, new Key(config('app.jwt.key'), 'HS256'));
        $identityInfo = (new \app\api\model\Identity())->identityInfo(objToArray($identity));
        if(!$identityInfo){
            echo json_encode(['code'=>100,'msg'=>'请先登录','data'=>'']);
            exit;
        }
        // 全局身份信息
        $this->identity = objToArray($identity);
    }
    // public function checkUser()
    // {
    //     //强制post
    //     if (!$this->request->isPost()){
    //         if(!$this->checkLogin(1)){
    //             echo '别瞎搞';
    //             exit;
    //         }
    //     }
    //     //验证登录
    //     try {
    //         $token = $this->request->header('x-access-token');
    //         if(!isset($token) || $token == ''){
    //             return false;
    //         }
    //         $user = JWT::decode($token, new Key(config('app.jwt.key'), 'HS256'));

    //         $userInfo = (new \app\api\model\User())->userWhereInfo(objToArray($user));
    //         if(!$userInfo){
    //             return false;
    //         }
    //         $this->user = $userInfo;
    //     } catch (\Exception $e) {
    //         return false;
    //     }

    //     return true;
    // }
    /**
     * api验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    public function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        try {
            parent::validate($data, $validate, $message, $batch);
        } catch (\Exception $e) {
            echo json_encode(['code'=>100,'data'=>'','msg'=>$e->getError()]);
            exit;
        }
    }
    /**
     * 验证登录及排除
     * @return true
     */
    public function checkLogin($type = 2)
    {
        $controller = parse_name(app()->request->controller());
        $action = parse_name(app()->request->action());
        $current = $controller.'/'.$action;
        // 验证登录
        if($type == 1){
            return in_array($current, config('app.get_request'));
        }else{
            return in_array($current, config('app.no_login'));
        }
    }
    /**
     * 解析和获取模板内容 用于输出
     * @param string $template
     * @param array $vars
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
    }
    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    // 去重数组空值
    public function remove_empty_arrays($data = []){
        foreach ($data as &$value) {
            if (empty($value)) unset($value);
        }
        return $data;
    }
}
