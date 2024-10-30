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
        //继承验证、登录通用方法
        parent::initialize();
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    public function initialize()
    {
        $this->limit_page = Config::get('app')['const_data']['api_limit'];  //limit_page 
        $this->appid = Config::get('app')['const_data']['appid'];  //config-appid全局常量
        $this->appsecret = Config::get('app')['const_data']['appsecret'];  //config-appsecret 全局常量
        $this->mch_id = Config::get('app')['const_data']['mch_id'];  //config-appsecret 全局常量
        // $this->access_token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";  
    }

    // 预支付信息
    public function unifiedOrder()
    {
        $post = $this->request->post();
        
        $rule = [
            'order_id|必要条件'       => 'require',
        ];
        $this->validate($post, $rule);
        
        $openid = \app\api\model\User::where('id',$this->identity['user_id'])->value('openid');
        if (!$openid) {
            return msg(100,'无效用户',$post);
        }
        $order = \app\api\model\Order::where('id',$post['order_id'])->find();
        if (empty($order)) {
            return msg(100,'无效订单',$post);
        }
        // 设置统一下单请求参数
        $data = [
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'nonce_str' => $order->order_sn,
            'body' => '禾惠6', //默认商品名称
            'out_trade_no' => $order->order_sn, // 订单号
            'total_fee' => $order->total_amount *100, // 总金额，单位为分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url' => Config::get('app')['const_data']['notify_url'],
            'trade_type' => 'JSAPI',
            'openid' => $openid, // 用户标识
        ];
        // print_r(Config::get('app')['const_data']['secret_key']); exit;
        
        // 生成签名
        $data['sign'] = $this->generateSign($data);
        
        // 转换为XML格式
        $xmlData = $this->arrayToXml($data);
        
        // 发送统一下单请求
        $response = $this->curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', $xmlData);
        
        // 解析响应数据
        $responseData = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        // print_r($responseData); exit;
        
        if ($responseData->return_code == 'SUCCESS' && $responseData->result_code == 'SUCCESS') {
            // 返回前端需要的参数
            $payData = [
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
            ];
            // $paySign   = md5("appId=$appId&nonceStr=$nonceStr&package=prepay_id=$prepay_id&signType=MD5&timeStamp=$timeStamp&key=$key"); // 这个地方就是我所说的二次签名！

            // "data": {
            //     "nonceStr": "xXOgmoYvy9nBpCDx",
            //     "timeStamp": "1701314412",
            //     "package": "prepay_id=wx30112013748902240b3fdbd40d09ee0000",
            //     "paySign": "95210211d4b875f302cad8a97cbe1360",
            //     "signType": "MD5"
            // },
            $payData = [
                'nonceStr'  => $responseData->nonce_str,
                'timeStamp' => time(),
                'package'   => 'prepay_id=' . $responseData->prepay_id,
                "signType"  => "MD5"
            ];
            return msg(200,'下单成功',$payData);
        } else {
            // Log::error('统一下单失败: ' . $responseData->return_msg);
            return msg(100,'下单失败','');
        }
    }
    
    private function generateSign($data)
    {
        ksort($data);
        $string = urldecode(http_build_query($data)) . '&key=' . Config::get('app')['const_data']['secret_key'];
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