<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // 应用地址
    'app_host'         => Env::get('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 开启应用快速访问
    'app_express'      => true,
    // 默认应用
    'default_app'      => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map'          => [
        Env::get('easyadmin.admin', 'admin') => 'admin',
    ],
    // 后台别名
    'admin_alias_name' => Env::get('easyadmin.admin', 'admin'),
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ['common'],
    // 异常页面的模板文件
    // 'exception_tmpl'   => Env::get('app_debug') == 1 ? app()->getThinkPath() . 'tpl/think_exception.tpl' : app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'think_exception.tpl',
    // http异常页面的模板文件
    // 'http_exception_template' => [
    //     404 => base_path('common' . DIRECTORY_SEPARATOR . 'tpl') . '404.html'
    // ],
    // 跳转页面的成功模板文件
    'dispatch_success_tmpl'   => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
    // 跳转页面的失败模板文件
    'dispatch_error_tmpl'   => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
    // 错误显示信息,非调试模式有效
    // 'error_message'    => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'   => true,
    // 静态资源上传到OSS前缀
    'oss_static_prefix'   => Env::get('easyadmin.oss_static_prefix', 'static_easyadmin'),
    // 自定义全局常量
    'const_data'        => [
        // api limit默认10条数据
        'api_limit' => '10',
        'appid' => 'wx49d709d931c6a99b',
        'appsecret' => 'd084a523745c32c3c09b1db5515e1ce5',
        // 'appid' => 'wxcedbd1bfa93490ba',
        // 'appsecret' => 'db83283b65d7f1db31b04a07123bcc9c',
        'mch_id' => '1656083885', 
        'secret_key' => 'DHWZUu2Z3zBhsN34Kh2xcqkF4qNWHgR3', //V2、V3秘钥
        'notify_url' => 'https://hosj4bb2pabe-5768.beijing-02.dayunet.com/api/order/orderNotify', //支付回调
        'prcode_url' => 'https://hosj4bb2pabe-5768.beijing-02.dayunet.com/api/goods/info', //二维码路径
        'web_url' => 'https://hosj4bb2pabe-5768.beijing-02.dayunet.com', //项目路径
    ],
];
