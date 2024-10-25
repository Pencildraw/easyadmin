<?php

namespace app\api\model;

use app\common\model\TimeModel;

class Order extends TimeModel
{

    protected $name = "mall_order";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    
    public function orderList(){
        return $this->hasMany('app\api\model\OrderSpec','order_id','id');
    }
}