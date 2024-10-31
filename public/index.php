<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;
//跨域

require __DIR__ . '/../vendor/autoload.php';
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Request-Method: GET,POST,OPTIONS");
// 声明全局变量
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', __DIR__ . DS . '..' . DS);

// 判断是否安装程序
if (!is_file(ROOT_PATH . 'config' . DS . 'install' . DS . 'lock' . DS . 'install.lock')) {
    exit(header("location:/install.php"));
}
// 设置OPTIONS请求的处理
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // 如果是预检请求，直接返回
    exit();
}
// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();
$response->send();

$http->end($response);
