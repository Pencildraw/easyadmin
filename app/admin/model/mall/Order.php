<?php

namespace app\admin\model\mall;

use app\common\model\TimeModel;
use think\Model; 

class Order extends TimeModel
{

    protected $name = "mall_order";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;

    // 未支付订单不展示
    // protected $pay_status = "pay_status";
    // protected $defaultPayStatus=0;

    public function statusList(){
        return ['0'=>'待发货' ,'1'=>'待收货' ,'2'=>'已完成'];
    }
    
}