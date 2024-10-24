<?php

namespace app\admin\model\company;

use app\common\model\TimeModel;

class user extends TimeModel
{

    protected $name = "company_user";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;


    
    
    public function getTypeList()
    {
        return ['1'=>'供应商','2'=>'经销商','3'=>'业务员','4'=>'店铺','0'=>'普通用户',];
    }


}