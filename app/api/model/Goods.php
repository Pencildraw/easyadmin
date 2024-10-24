<?php

namespace app\api\model;

use app\common\model\TimeModel;

class Goods extends TimeModel
{

    protected $name = "mall_goods";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    

}