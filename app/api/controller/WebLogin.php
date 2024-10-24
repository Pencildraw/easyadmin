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

class WebLogin extends BaseController
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

    public function login()  
    {  
        // $code = Request::param('code');  
        $code = $this->request->param('code');  
        // print_r($code); exit;
        // $this->appid = $this->appid;  
        // $this->appsecret = $this->appsecret;  
 
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";  
 
        $response = file_get_contents($url);  
        $result = json_decode($response, true);  
 
        if (isset($result['errcode'])) {  
            // 处理错误  
            return json(['code' => -1, 'msg' => '登录失败' ,'error' => $result['errmsg']]);  
            // return json(['error' => $result['errmsg']], 400);  
        }  
 
        // $openid = $result['openid'];  
        // $sessionKey = $result['session_key'];  
 
        // 在这里你可以将 openid 和 session_key 存储在你的数据库中，或者进行其他处理  
        return json(['code' => 1, 'msg' => '登录成功', 'data' => $result]);  
        // return json(['openid' => $openid, 'session_key' => $sessionKey]);  
    } 

    public function getUserInfo($openid, $sessionKey)  
    {  
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$this->getAccessToken($this->appid, $this->appsecret)}&openid={$openid}&lang=zh_CN";  

        $headers = [  
            "Content-Type: application/json",  
            "Accept: application/json",  
            "X-WX-Session-Key: {$sessionKey}"  
        ];  

        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
        $response = curl_exec($ch);  
        curl_close($ch);  

        $userInfo = json_decode($response, true);  

        if (isset($userInfo['errcode'])) {  
            // 处理错误  
            // return json(['error' => $userInfo['errmsg']], 400);  
            return json(['code' => -1, 'msg' => '获取失败' ,'error' => $userInfo['errmsg']]);  
        }  

        return json($userInfo);  
    }  

    private function getAccessToken()  
    {  
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";  
        $response = file_get_contents($url);  
        $result = json_decode($response, true);  
        return $result['access_token'];  
    }
}