
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
        '30' => 5,
        '20' => 3,
        '10' => 1,
    ],
    // 不需要验证登录的节点
    'no_login'       => [
        'login/sms_login',
        'login/wx_login',
        'login/get_verifycode',
        'task/upload_file',
        'index/index',
        'index/editor',
        'index/get_company',
        'login/login_title',
        'anchor/ranking',
        'anchor/get_platform',
        'anchor/wx_anchor_auth',
        'user/ranking',
        'kuaishou/kslogin',
        'kuaishou/ksloginpc',
        'kuaishou/login_callback',
        'kuaishou/getvideo',
        'kuaishou/update_video',
        'douyin/dylogin',
        'douyin/login_callback',
        'order/get_order',
        'order/get_order_wx_pay_info',
        'order/get_openid',
        'order/order_verify',
        'order/order_notify',
        'course_order/order_notify',
        'course_manage/course_order_check',
        // 'ranking/ranking_index',
        // 'ranking/ranking_list',
//        'user/get_report',
        'anchor/get_anchor_list',
        'goods/get_goods_by_id',
        'index/kuaidi_order_callback',
        'index/kuaidi_point_callback',
        'image_processing/index',
        'order/get_payment_voucher',
        'user/get_hot_data',
        'task/update_live',
        'anchor/get_data',
        'user/get_data',
        'user/get_anchor_line_y',
        'user/get_anchor_line_x',
        'user/get_openid',
        'image_processing/index',
        'order/order_verify',
        'index/get_menu_list',
        'index/get_data_all',
        'tik_tok_impower/index',
        'tik_tok_impower/impower_call_cack',
        'msg/get_msg_all_num',
        'config/index',
//        'user/add_feedback',impowerCode
        'tik_tok_impower/manage_live_data',
        'tik_tok_impower/get_video_list',
        'tik_tok_impower/impower_code',
        'tik_tok_impower1/impower_code',
        'tik_tok_impower1/create_video_data',
        'tik_tok_impower/dy_login_qrcode',
        'tik_tok_impower/impower_code_img',
        'tik_tok_impower/video_guide',
        'tik_tok_impower/get_video_url',
        'tik_tok_impower/get_video_url2',
        'anchor/get_check_time',
        'kuaishou/get_qr_code',
        'anchor/get_yesterday_data',
        'mall_points_order/order_notify',
        'index/get_video_url',
        'kuaishou/gather',
        'tik_tok_impower/gather',
        'login/get_token',
        'anchor/get_openid',
        'user/get_video_data',
        'index/get_video',
        'index/get_ranking',
        'index/get_menu_list_all',
        'index/set_ranking',
        'index/get_data',
        'mall_order/pay_voucher',
        'mall_order/order_notify',
        'mall_order/order_detail',
        'mall_order/get_order_wx_pay_info',
        'mall_order/pay_proof_details',
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
