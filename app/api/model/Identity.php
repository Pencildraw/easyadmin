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
    // 用户类型 {select}  (1:供应商,2:经销商,3:业务员,4:店铺,5:普通用户)
    public function typeList(){
        return ['5'=>'普通用户' ,'1'=>'供应商' ,'2'=>'经销商' ,'3'=>'业务员','4'=>'店铺'];
    }
    //身份信息 
    public function identityInfo($where){
        return $this->field('id,name,phone,email,status,create_time,dealer_id,goods_id,qrcode_image,type,address,head_image,binding_status,user_id,shop_address')
            ->where($where)->find();
    }
}