<?php

namespace app\api\model;

use app\common\model\TimeModel;

class Identity extends TimeModel
{

    protected $name = "company_identity";

    protected $deleteTime = "delete_time";

    protected $defaultSoftDelete=0;
    
    public function identityUser(){
        return $this->hasMany('app\api\model\User','id','user_id')->where('status',1);
    }
    // 用户类型 {select}  (1:供应商,2:经销商,3:业务员,4:店铺,0:普通用户)
    public function typeList(){
        return ['0'=>'普通用户' ,'1'=>'供应商' ,'2'=>'经销商' ,'3'=>'业务员','4'=>'店铺'];
    }
}