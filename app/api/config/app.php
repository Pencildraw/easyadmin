
<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    //jwt
    'jwt' =>[
        'key' => 'adnsf2342'
    ],
    // 赠品规则 10-1 20-3 30-5 40-8 50以上  买5赠1
    'git_goods' =>[
        '40' => 8,
        '30' => 5,
        '20' => 3,
        '10' => 1,
    ],
    // 不需要验证登录的节点
    'no_login'       => [
        'order/order_notify',
        'order/refund_notify',
    ],
    'get_request' =>    [
        'kuaishou/kslogin',
        'kuaishou/ksloginpc',
        'kuaishou/getvideo',
        'kuaishou/login_callback',
        'kuaishou/update_video',
        'douyin/dylogin',
        'douyin/login_callback',
        'order/order_notify',
        'course_order/order_notify',
        'course_manage/course_order_check',
        // 'ranking/ranking_index',
        // 'ranking/ranking_list',
        'anchor/get_anchor_list',
        'image_processing/index',
        'order/order_verify',
        'image_processing/index',
        'order/get_payment_voucher',
        'user/get_hot_data',
        'user/get_openid',
        'tik_tok_impower/index',
        'tik_tok_impower/impower_call_cack',
        'tik_tok_impower/manage_live_data',
        'tik_tok_impower/get_video_list',
        'tik_tok_impower/impower_code',
        'tik_tok_impower1/impower_code',
        'tik_tok_impower1/create_video_data',
        'tik_tok_impower/dy_login_qrcode',
        'tik_tok_impower/impower_code_img',
        'tik_tok_impower/get_video_url',
        'tik_tok_impower/get_video_url2',
        'tik_tok_impower/video_guide',
        'mall_points_order/order_notify',
        'kuaishou/gather',
        'tik_tok_impower/gather',
        'index/set_ranking',
        'mall_order/pay_voucher',
        'mall_order/order_notify',
        'mall_order/order_detail',
        'mall_order/get_order_wx_pay_info',
        'mall_order/pay_proof_details',
    ],
];
