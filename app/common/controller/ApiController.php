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
    protected $supplier_id = 1; //供应商ID
    protected $dealer_id = 6; //经销商ID
    protected $user_id = 1; //用户ID
    // 初始化
    protected function initialize()
    {
        parent::initialize();
        //强制post
//        if (!$this->request->isPost()){
//            if(!$this->checkLogin(1)){
//                echo '别瞎搞';
//                exit;
//            }
//
//        }

        //验证登录
        // if(!$this->checkLogin(2)){

        //     try {
        //         $token = $this->request->header('x-access-token');
        //         if(!isset($token) || $token == ''){
        //             echo json_encode(['code'=>100,'data'=>'','msg'=>'请先登录']);
        //             exit;
        //         }
        //         $user = JWT::decode($token, new Key(config('app.jwt.key'), 'HS256'));

        //         $userInfo = (new \app\api\model\User())->userWhereInfo(objToArray($user));
        //         if(!$userInfo){
        //             echo json_encode(['code'=>100,'data'=>'','msg'=>'请先登录']);
        //             exit;
        //         }
        //         $this->user = $userInfo;
        //     } catch (\Exception $e) {
        //         echo json_encode(['code'=>100,'data'=>'','msg'=>'请先登录']);
        //         exit;
        //     }
        // }else{
        //     $token = $this->request->header('x-access-token');
        //     if(isset($token) && $token != ''){
        //         $user = JWT::decode($token, new Key(config('app.jwt.key'), 'HS256'));

        //         $userInfo = (new \app\api\model\User())->userWhereInfo(objToArray($user));
        //         if($userInfo){
        //             $this->user = $userInfo;
        //         }
        //     }
        // }
        // $sign = $this->request->header('x-sign');

        // if(isset($sign) && $sign != '' && $sign != '{{sign}}'){
        //     $company = (new \app\api\model\Company())->where('wx_appid_md5',$sign)->field('id,title,wx_appid,wx_secret,mch_id,api_v2_key,cert_path,key_path')->find();
        //     if(!$company){
        //         echo json_encode(['code'=>100,'data'=>'','msg'=>'无效的签名']);
        //         exit;
        //     }
        //     $this->company = $company;
        // }
    }
    public function checkUser()
    {
        //强制post
        if (!$this->request->isPost()){
            if(!$this->checkLogin(1)){
                echo '别瞎搞';
                exit;
            }
        }
        //验证登录
        try {
            $token = $this->request->header('x-access-token');
            if(!isset($token) || $token == ''){
                return false;
            }
            $user = JWT::decode($token, new Key(config('app.jwt.key'), 'HS256'));

            $userInfo = (new \app\api\model\User())->userWhereInfo(objToArray($user));
            if(!$userInfo){
                return false;
            }
            $this->user = $userInfo;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
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
