<?php

namespace app\api\model;

use app\common\model\TimeModel;

class OrderSpec extends TimeModel
{

    protected $name = "mall_order_spec";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    

}