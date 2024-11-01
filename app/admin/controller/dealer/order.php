<?php

namespace app\admin\controller\dealer;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;
use think\facade\Config;
use EasyWeChat\Factory;

/**
 * @ControllerAnnotation(title="mall_order")
 */
class order extends AdminController
{

    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\mall\order();
        
    }
    
    public function refund(){
        // 获取请求参数  
        $transactionId = '4200002364202410312451255908'; // 微信订单号  
        $outRefundNo = '1730372446745918';    // 商户退款单号  
        $totalFee = 100;           // 原订单金额  
        $refundFee = 100;         // 退款金额  

        // 配置微信支付  
        $config = Config::get('easywechat.payment');  
        $config = [
                'app_id'        => Config::get('app')['const_data']['appid'],         // 必填，公众号的唯一标识  
                'mch_id'        => Config::get('app')['const_data']['mch_id'],         // 必填，商户号  
                'key'           => Config::get('app')['const_data']['secret_key'],        // 必填，API密钥  
                // 'cert_client'   => 'path/to/your/apiclient_cert.pem', // 可选，商户证书路径  
                // 'cert_key'      => 'path/to/your/apiclient_key.pem',  // 可选，商户证书密钥路径  
                'notify_url'    => Config::get('app')['const_data']['refund_notify_url'], // 可选，异步通知地址  
                // 其他配置项...  
                ];
                // print_r($config); exit;
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
    
    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        // $payService = new \app\admin\service\PayService;
        // // if ($payService->refund()) {
        // if ($this->refund()) {
        //     return json(['msg' => 'Refund successful']);
        // } else {
        //     return json(['msg' => 'Refund failed']);
        // }
        // exit;
        
        if ($this->request->isAjax()) {
            if (input('selectFields')) {
                return $this->selectList();
            }
            list($page, $limit, $where) = $this->buildTableParames();
            $count = $this->model
                ->where($where)
                ->where('pay_status',1)
                ->count();
            $list = $this->model
                ->field('ea_mall_order.* 
                    ,(SELECT name FROM ea_company_identity WHERE id = ea_mall_order.supplier_id AND ea_mall_order.pay_status=1) AS identity_supplier
                    ,(SELECT phone FROM ea_company_identity WHERE id = ea_mall_order.supplier_id AND ea_mall_order.pay_status=1) AS supplier_phone
                    ,(SELECT name FROM ea_company_identity WHERE id = ea_mall_order.shop_id AND ea_mall_order.pay_status=1) AS identity_shop
                    ,(SELECT phone FROM ea_company_identity WHERE id = ea_mall_order.shop_id AND ea_mall_order.pay_status=1) AS shop_phone
                ')
                // ,(SELECT name FROM ea_company_identity WHERE id = ea_mall_order.shop_id) AS identity_shop 
                ->where($where)
                ->where('pay_status',1)
                ->page($page, $limit)
                ->order($this->sort)
                // ->fetchsql(true)
                ->select();
            // print_r($list); exit;
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        $this->layoutBgColor();
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $this->model->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败:'.$e->getMessage());
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑")
     */
    public function edit($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                $save = $row->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }
}