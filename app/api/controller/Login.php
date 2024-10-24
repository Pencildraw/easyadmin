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

class Login extends BaseController
{
    protected $appid;  
    protected $appsecret;  
    protected $access_token_url;  
    protected $limit_page;  
  
    public function __construct(App $app)
    {
        parent::__construct($app);

        // 控制器初始化
        $this->initialize();
        // 初始化时自定义方法
    }

    // 初始化
    public function initialize()
    {
        // return json([
        //     'code'  => -1,
        //     'msg'   => '小程序未登录,请登录!',
        // ]);
        // exit();
        $this->limit_page = Config::get('app')['const_data']['api_limit'];  //limit_page 
        $this->appid = Config::get('app')['const_data']['appid'];  //config-appid全局常量
        $this->appsecret = Config::get('app')['const_data']['appsecret'];  //config-appsecret 全局常量
        $this->access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";  

    }

    /**
     * 小程序登录
     */
    public function login()  
    {  
        $code = $this->request->param('code');
        // 使用微信官方API获取session_key和openid  
        $result = $this->callWechatApi($code); // 假设你已经实现了这个函数  
        // print_r($result);
        // exit;
        if ($result && isset($result['session_key'], $result['openid'])) {  
            // 保存session_key和openid到session或数据库等操作  
            // ...  
            return json(['code' => 0, 'msg' => '登录成功', 'data' => $result]);  
        } else {  
            return json(['code' => -1, 'msg' => '登录失败']);  
        }
    } 

    /**
     * 获取用户openid
     */
    function callWechatApi($code)  
    {  
        // 实现调用微信API的逻辑，返回结果  
        $client = new \GuzzleHttp\Client();
        // $path = "https://api.weixin.qq.com/sns/jscode2session?appid=wxc674d80bd7c327a3&secret=bba22a3c5b9c6ec01a574fa0d43088b3&js_code=" . $code;
        $path = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->appsecret}&js_code=" . $code;

        // 调取接口
        $result = $client->post($path, ['connect_timeout' => 5, 'http_errors' => true, 'verify' => false, 'json' => []]);
        return json_decode($result->getBody(), true);
    }

    /**
     * 获取用户openid
     */
    public function get_openid()
    {
        $code = input('post.code',null);

        if(empty($code)){
            return json(['code' => -1, 'msg' => 'code错误!']);
        }

        $client = new \GuzzleHttp\Client();
        $path = "https://api.weixin.qq.com/sns/jscode2session?appid={$this->appid}&secret={$this->appsecret}&js_code=" . $code;

        // 调取接口
        $result = $client->post($path, ['connect_timeout' => 5, 'http_errors' => true, 'verify' => false, 'json' => []]);
        $result = json_decode($result->getBody(), true);

            if ($result && isset($result['session_key'], $result['openid'])) {  
                return json(['code' => 0, 'msg' => '获取成功', 'data' => $result]);  
            } else {  
                return json(['code' => -1, 'msg' => '获取失败']);  
            }
    }

    /**
     * 获取用户手机号
     */
    public function get_user_mobile()
    {
        $code = input('post.code',null);
        
        $access_token = Cache::get('mini_access_token'); // 获取access_token

        if(empty($code)){
            return json(['code' => -1, 'msg' => 'code错误!']);  
        }
        
        if(empty($access_token)){
            $access_token = $this->get_access_token();
        }

        $client = new \GuzzleHttp\Client();
        $path = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=" . $access_token;

        // 调取接口
        $result = $client->post($path, ['connect_timeout' => 5, 'http_errors' => true, 'verify' => false, 'json' => ['code' => $code]]);
        $result = json_decode($result->getBody(), true);
        // print_r($result); exit;
        if ($result && isset($result['phone_info'])) {  
            // 保存手机号phone_info->purePhoneNumber 数据库等操作  
            // ...  
            return json(['code' => 0, 'msg' => '获取成功', 'data' => $result]);  
        } else {  
            return json(['code' => -1, 'msg' => '获取失败']);  
        }
    }

    /**
     * 获取小程序accesstoken
     */
    private function get_access_token()
    {
        $access_token = Cache::get('mini_access_token');
        
        if(empty($access_token)){
            $client = new \GuzzleHttp\Client();

            $result = $client->get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}", ['connect_timeout' => 5, 'http_errors' => true, 'verify' => false]);
            $result = json_decode($result->getBody(), true);

            $access_token = $result['access_token'];

            if($access_token){
                Cache::set('mini_access_token',$access_token,7200);
            }
        }
        
        return $access_token;
    }
}