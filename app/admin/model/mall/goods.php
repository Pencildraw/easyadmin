<?php

namespace app\admin\model\mall;

use app\common\model\TimeModel;

class goods extends TimeModel
{

    protected $name = "mall_goods";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    

}