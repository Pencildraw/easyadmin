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

use think\App;
use think\facade\Config;
use EasyAdmin\auth\Node;
use Endroid\QrCode\QrCode;  
use Endroid\QrCode\Writer\PngWriter;  
use Endroid\QrCode\Encoding\Encoding;  
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;  

class QrcodeService
{
    /**
     * @NodeAnotation(title="生成二维码")
     */
    public function generate($level = 0 ,$goods_id = 1)  
    {  
        $prcode_url = Config::get('app')['const_data']['prcode_url'];
        // 自定义参数  
        // $text = 'http://www.jingxiaoshang.com/api/index/index?param1=value1&param2=value2';  
        $text = $prcode_url.'?level='.$level.'&goods_id='.$goods_id;  
  
        // 创建 QrCode 对象  
        $qrCode = QrCode::create($text)  
            ->setEncoding(new Encoding('UTF-8'))  
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())  
            ->setSize(300);  
  
        // 创建 PngWriter 对象  
        $writer = new PngWriter();  
  
        // 将二维码写入文件（可选）  
        $result = $writer->write($qrCode);  
        $qrcode_image = '/qrcode/'.time().rand(1000,9999).'.png';
        $result->saveToFile(public_path() .$qrcode_image);
        // if (!$result->saveToFile(public_path() .$qrcode_image)) {
        //     return [
        //         'code' => -1,
        //         'msg' => '二维码生成失败',
        //     ];
        // }
        return [
            'code' => 1,
            'msg' => '二维码生成成功',
            'qrcode_image' => $qrcode_image
        ];
          
        // $result->saveToFile(public_path() .'/upload/'. 'qrcode.png');  
  
        // 或者直接输出二维码图像（可选）  
        // header('Content-Type: '.$result->getMimeType());  
        // echo $result->getString();  exit;
    }
}