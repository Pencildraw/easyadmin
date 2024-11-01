<?php

// +----------------------------------------------------------------------
// | EasyAdmin
// +----------------------------------------------------------------------
// | PHP交流群: 763822524
// +----------------------------------------------------------------------
// | 开源协议  https://mit-license.org 
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zhongshaofa/EasyAdmin
// +----------------------------------------------------------------------

namespace app\admin\service;

use think\facade\Config;
use EasyWeChat\Factory;

class PayService
{
    /**
     * @NodeAnotation(title="生成二维码")
     * identity_id      身份ID
     * type             类别
     * goods_id         商品ID
     */
    public function refund(){
        // 获取请求参数  
        $transactionId = 4200002496202410312617916834; // 微信订单号  
        $outRefundNo = 1730373553883757;    // 商户退款单号  
        $totalFee = 100;           // 原订单金额  
        $refundFee = 100;         // 退款金额  

        // 配置微信支付  
        $config = Config::get('easywechat.payment');  
        $config = [
                'app_id'        => Config::get('app')['const_data']['appid'],         // 必填，公众号的唯一标识  
                'mch_id'        => Config::get('app')['const_data']['mch_id'],         // 必填，商户号  
                'key'           => Config::get('app')['const_data']['secret_key'],        // 必填，API密钥  
                'cert_client'   => 'path/to/your/apiclient_cert.pem', // 可选，商户证书路径  
                'cert_key'      => 'path/to/your/apiclient_key.pem',  // 可选，商户证书密钥路径  
                'notify_url'    => Config::get('app')['const_data']['refund_notify_url'], // 可选，异步通知地址  
                // 其他配置项...  
                ];
        $app = Factory::payment($config);  

        // 发起退款请求  参数分别为：微信订单号、商户退款单号、订单金额、退款金额、其他参数
        $result = $app->refund->byTransactionId($transactionId, $outRefundNo, $totalFee, $refundFee, [  
            // 'out_refund_no' => $outRefundNo,  
            // 'total_fee'     => $totalFee,  
            // 'refund_fee'    => $refundFee,  
            'refund_desc'   => 'Refund description', // 退款原因  
            // 'notify_url'    => $config['notify_url'], // 可选，退款结果通知网址  
        ]);
        print_r($result); exit;
        // 返回结果  
        if ($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS') {  
            // return Json::create(['status' => 'success', 'message' => 'Refund success', 'data' => $result]);  
        } else {  
            // return Json::create(['status' => 'fail', 'message' => 'Refund failed', 'data' => $result]);  
        }  
    }
}