<?php

namespace app\api\model;

use app\common\model\TimeModel;

class User extends TimeModel
{

    protected $name = "company_user";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    
}