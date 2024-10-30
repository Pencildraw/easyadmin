<?php
// 用户
namespace app\api\controller;
 
// use app\common\controller\BaseController;
use think\App;
use think\facade\Env;
// use app\admin\service\ConfigService;
use app\BaseController;
use think\facade\Config;
use think\facade\Cache;  
use think\facade\Http;  
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class WebLogin extends BaseController
{
    protected $appid;  
    protected $appsecret;  
    protected $access_token_url;  
    protected $limit_page;  

    // 初始化
    public function initialize()
    {
        $this->limit_page = Config::get('app')['const_data']['api_limit'];  //limit_page 
        $this->appid = Config::get('app')['const_data']['appid'];  //config-appid全局常量
        $this->appsecret = Config::get('app')['const_data']['appsecret'];  //config-appsecret 全局常量
        $this->access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";  

    }

    // 登录
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
        $identity = $identityModel->where($whereIs)->find();
        // print_r($identity); exit;
        // print_r(config('app.jwt.key')); exit;
        $data['token'] = '';
        if (empty($identity)) {
            return msg(100,'登录失败: 密码错误!',''); 
        } else {
            $userModel = new \app\api\model\User;
            $userModel->startTrans();
            try {
                $user = $userModel->where('openid',$post['openid'])->find();
                if (empty($user)) {
                    // return msg(100,'不存在用户或已禁用: '.$identity['phone'],''); 
                    $userData = [
                        'openid' => $post['openid'],
                        'type' => $identity['type'],
                        'identity_id' => $identity['id'],
                        'binding_status' => 1,
                    ];
                    $insertGetId = $userModel->insertGetId($userData);
                    if (!$insertGetId) {
                        $userModel->rollback();
                        throw new \Exception('用户信息错误');
                    }
                    $user_id = $insertGetId;
                } else {
                    // $user->openid = $post['openid'];
                    $user->type = $identity['type'];
                    $user->identity_id = $identity['id'];
                    $user->binding_status = 1;
                    $user->save();  
                    $user_id = $user->id;
                }
                $identity->user_id = $user_id;
                $identity->binding_status = 1;
                $identity->save(); 
                
            } catch (\Exception $e) {
                $userModel->rollback();
                return msg(100,'保存失败:'.$e->getMessage(),'');
            }
            $userModel->commit();
            $data['token'] = JWT::encode(array('id'=>$identity['id'],'user_id'=>$identity['user_id'],'phone'=>$identity['phone'],'type'=>$identity['type']), config('app.jwt.key'), 'HS256');
            $data['type'] = $identity['type'];
            $data['type_title'] = $identityModel->typeList()[$identity['type']];
            return msg(200,'登录成功',$data); 
        }
    }

    //获取token
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
        if($user && isset($user->id)){
            $identityModel = new \app\api\model\Identity;
            if ($identityModel->where('user_id',$user->id)->where('status',1)->count() <1) {
                // return msg(100,'不存在账号或已禁用: '.$user['name'],'');
                // 普通用户token
                //事务
                $identityModel->startTrans();
                try {
                    $identityData = [
                        'create_time' => time(),
                        'type' => 5,
                        'binding_status' => 1,
                        'user_id' => $user->id,
                    ];
                    $insertGetId = $identityModel->insertGetId($identityData);
                    // 关联用户信息
                    $user->identity_id = $insertGetId;
                    $user->binding_status = 1;
                    if (!$insertGetId || !$user->save()) {
                        $identityModel->rollback();
                        throw new \Exception('保存失败');
                    }

                } catch (\Exception $e) {
                    $identityModel->rollback();
                    return msg(100,'用户身份信息保存失败','');
                }
                $identityModel->commit();
            }
            $row = $identityModel->where('user_id',$user['id'])->find();
            $data['token'] = JWT::encode(array('id'=>$row['id'],'user_id'=>$row['user_id'],'phone'=>$row['phone'],'type'=>$row['type']), config('app.jwt.key'), 'HS256');
            $data['type'] = $row['type'];
            $data['type_title'] = $identityModel->typeList()[$row['type']];
        } else {
            return msg(100,'系统不存在该用户','');
        }
        return msg(200,'操作成功',$data);
    }

    // code换取用户信息
    public function exchange()  
    {  
        $post = $this->request->post();
        $rule = [
            'code|必要条件'       => 'require',
        ];
        $this->validate($post, $rule);
        $code = $this->request->post('code');  
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";  
 
        $response = file_get_contents($url);  
        $result = json_decode($response, true);  
 
        if (isset($result['errcode'])) {  
            // 处理错误  
            return msg(100,'获取失败',$result); 
        }  
 
        // 添加用户信息
        $userModel = new \app\api\model\User();
        //事务
        $userModel->startTrans();
        try {
            $userData = [
                'openid' => $result['openid'],
                'type' => 0,
            ];
            if (!$userModel->insert($userData)) {
                $userModel->rollback();
                throw new \Exception('用户信息错误');
            }
        } catch (\Exception $e) {
            $userModel->rollback();
            return msg(100,'获取失败: '.$e->getMessage(),$result); 
        }
        $userModel->commit();
        return msg(200,'获取成功',$result);
    } 

    // 获取wx用户详细信息
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