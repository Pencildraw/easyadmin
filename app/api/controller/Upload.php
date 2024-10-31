<?php
// 命名空间定义
namespace app\api\controller;
 
use app\BaseController;
use think\facade\Config;
use think\facade\Request;  

class Upload extends BaseController
{
    public function uploadImage()  
    {  
        // 获取上传的文件  
        $file = Request::file('file');  
        // 验证文件是否存在  
        if (empty($file)) {  
            return msg(100,'请上传文件','');
        }  
  
        // 定义允许的文件格式  
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];  
        // 获取文件的 MIME 类型  
        $mimeType = $file->getMime();  
        // 检查 MIME 类型是否在允许的列表中  
        if (!in_array($mimeType, $allowedMimeTypes)) {  
            return msg(100,'请上传有效文件: '.'jpeg|jpg|png|gif','');
        }  

        // 定义最大文件大小（例如：2MB）  
        $maxSize = 1 * 1024 * 1024; // 1MB in bytes  
        // 获取文件大小  
        $fileSize = $file->getSize();  
        // 检查文件大小是否超过限制  
        if ($fileSize > $maxSize) {  
            return msg(100,'请上传有效文件: '.'文件限于1MB之内','');
        }  
  
        $image = date('YmdHis',time()) .rand(1000,9999).'.jpg';
        // 移动文件到指定目录  
        $info = $file->move('uploads' ,$image);  
        if ($info) {  
            // 成功上传后获取文件信息  
            $filePath = $info->getFileName();  
            $web_url = Config::get('app')['const_data']['web_url'];
            $image_url = $this->request->domain() .'/uploads/'.$filePath;
            return msg(200,'文件上传成功',['image_url'=>$image_url]);
        } else {  
            // 上传失败  
            return msg(100,'文件上传失败','');
        }  
    }  
}