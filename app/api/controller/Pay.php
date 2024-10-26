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

class Pay extends ApiController
{
    protected $appid;  
    protected $appsecret;  
    protected $access_token_url;  
    protected $limit_page;  
    protected $mch_id;  
  
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
        $this->limit_page = Config::get('app')['const_data']['api_limit'];  //limit_page 
        $this->appid = Config::get('app')['const_data']['appid'];  //config-appid全局常量
        $this->appsecret = Config::get('app')['const_data']['appsecret'];  //config-appsecret 全局常量
        $this->mch_id = Config::get('app')['const_data']['mch_id'];  //config-appsecret 全局常量
        $this->access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";  

    }

    public function unifiedOrder()
    {
        // 获取请求参数
        $params = $this->request->param();
        
        $openid = '111111';
        // 设置统一下单请求参数
        $data = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'nonce_str' => md5(time()),
            'body' => '商品描述',
            'out_trade_no' => time(), // 订单号
            'total_fee' => $params['total_fee'], // 总金额，单位为分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => Config::get('app')['const_data']['notify_url'],
            'trade_type' => 'JSAPI',
            'openid' => $openid, // 用户标识
        ];
        print_r($data); exit;
        
        // 生成签名
        $data['sign'] = $this->generateSign($data);
        
        // 转换为XML格式
        $xmlData = $this->arrayToXml($data);
        
        // 发送统一下单请求
        $response = $this->curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', $xmlData);
        
        // 解析响应数据
        $responseData = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        if ($responseData->return_code == 'SUCCESS' && $responseData->result_code == 'SUCCESS') {
            // 返回前端需要的参数
            return json([
                'appId' => $responseData->appid,
                'timeStamp' => time(),
                'nonceStr' => $responseData->nonce_str,
                'package' => 'prepay_id=' . $responseData->prepay_id,
                'signType' => 'MD5',
                'paySign' => $this->generateSign([
                    'appId' => $responseData->appid,
                    'timeStamp' => time(),
                    'nonceStr' => $responseData->nonce_str,
                    'package' => 'prepay_id=' . $responseData->prepay_id,
                    'signType' => 'MD5',
                ]),
            ]);
        } else {
            // Log::error('统一下单失败: ' . $responseData->return_msg);
            return json(['error' => '统一下单失败']);
        }
    }
    
    private function generateSign($data)
    {
        ksort($data);
        $string = urldecode(http_build_query($data)) . '&key=' . Config::get('wechat.api_key');
        return strtoupper(md5($string));
    }
    
    private function arrayToXml($data)
    {
        $xml = '<xml>';
        foreach ($data as $key => $value) {
            $xml .= "<$key><![CDATA[$value]]></$key>";
        }
        $xml .= '</xml>';
        return $xml;
    }
    
    private function curlPost($url, $xmlData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
}