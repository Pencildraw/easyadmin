<?php
// 用户
namespace app\api\controller;
 
use app\common\controller\ApiController;
use think\App;
use think\facade\Env;
// use app\admin\service\ConfigService;
use app\BaseController;
use think\facade\Config;
use think\facade\Cache;  
use think\facade\Http;  
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class WebLogin extends ApiController
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
        $post = $this->request->post();
        
        $rule = [
            'name|必要条件'       => 'require',
            'password|必要条件'       => 'require',
            'openid|必要条件'       => 'require',
        ];
        $this->validate($post, $rule);
        $name = $post['name'] ??'';
        $password = $post['password'] ??'';
        if (!$name || !$password) {
            return msg(100,'参数错误',$post); 
        }
        $whereIs = [
            'name' => $name,
            'password' => md5(md5($password)),
            'status' => 1
        ];
        $identityModel = new \app\api\model\Identity;
        if ($identityModel->where('name',$name)->where('status',1)->count() <1) {
            return msg(100,'不存在账号或已禁用: '.$name,''); 
        }
        $row = $identityModel->where($whereIs)->find();
        // print_r($row); exit;
        // print_r(config('app.jwt.key')); exit;
        $data['token'] = '';
        if (empty($row)) {
            return msg(100,'登录失败',''); 
        } else {
            $userModel = new \app\api\model\User;
            $user = $userModel->where('id',$row['user_id'])->where('status',1)->find();
            if (empty($user)) {
                return msg(100,'不存在用户或已禁用: '.$row['phone'],''); 
            }
            $userModel->startTrans();
            try {
                $user->openid = $post['openid'];
                $user->save();
            } catch (\Exception $e) {
                $userModel->rollback();
                return msg(100,'保存失败:'.$e->getMessage(),'');
            }
            $userModel->commit();
            $data['token'] = JWT::encode(array('id'=>$row['id'],'user_id'=>$row['user_id'],'phone'=>$row['phone'],'type'=>$row['type']), config('app.jwt.key'), 'HS256');
            return msg(200,'登录成功',$data); 
        }
    }
    public function getToken()
    {
        $post = $this->request->post();
        $rule = [
            'openid|必要条件'       => 'require',
        ];
        $this->validate($post, $rule);

        $userModel = new \app\api\model\User;
        $user = $userModel->where(['openid'=>$post['openid'],'status'=>1])->find();

        $data['token'] = '';
        if($user && $user['id']){
            $identityModel = new \app\api\model\Identity;
            if ($identityModel->where('user_id',$user['id'])->where('status',1)->count() <1) {
                return msg(100,'不存在账号或已禁用: '.$user['name'],''); 
            }
            $row = $identityModel->where('user_id',$user['id'])->find();
            $data['token'] = JWT::encode(array('id'=>$row['id'],'user_id'=>$row['user_id'],'phone'=>$row['phone'],'type'=>$row['type']), config('app.jwt.key'), 'HS256');
        }
        return msg(200,'操作成功',$data);
    }

    public function exchange()  
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
            return msg(100,'获取失败',$result['errmsg']); 
        }  
 
        // $openid = $result['openid'];  
        // $sessionKey = $result['session_key'];  
 
        // 在这里你可以将 openid 和 session_key 存储在你的数据库中，或者进行其他处理  
        return msg(200,'获取成功',$result);
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
            return msg(100,'获取失败',$userInfo['errmsg']);
            // return json(['code' => -1, 'msg' => '获取失败' ,'error' => $userInfo['errmsg']]);  
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