<?php
namespace app\api\controller;
 
use app\common\controller\ApiController;
use think\App;
use think\facade\Env;
// use app\admin\service\ConfigService;
use app\BaseController;
use app\common\constants\AdminConstant;
use app\common\service\AuthService;
use EasyAdmin\tool\CommonTool;
use think\facade\View;
use think\Model;
use think\Request;
use think\facade\Config;
use think\facade\Cache;  
use think\facade\Http;  
use app\api\model\User as userModel;
use app\api\model\Identity as identityModel;

class Identity extends ApiController
{
    protected $identityModel;
    // 初始化
    protected function initialize()
    {
        $this->identityModel = new identityModel;
    }

    // 详情
    public function info(){
        $identityData = $this->identityModel->alias('i')
            ->leftJoin('company_user u' ,'i.user_id = u.id')
            ->where('i.user_id',$this->identity['user_id'])
            ->where('i.status',1)
            ->where('u.status',1)
            ->field('i.id,i.name,i.phone,i.email,i.status,i.create_time,i.dealer_id,i.goods_id,i.qrcode_image,i.type,i.address,i.head_image,i.binding_status,i.user_id,i.shop_address')
            ->find()->toArray();
        if (empty($identityData)) {
            return msg(100,'获取失败',''); 
        } else {
            $typeList = $this->identityModel->typeList();
            $identityData['type_title'] = $typeList[$identityData['type']] ??'';
            return msg(200,'获取成功',$identityData);
        }
    }

    // 修改
    public function update(){

        $post = $this->remove_empty_arrays($this->request->post());
        // 校验密码
        $pattern = "/^[a-zA-Z0-9]+$/";  //密码只包含数字 字母
        if (!preg_match($pattern, $post['new_password']) || !preg_match($pattern, $post['new_password'])) {  
            return msg(100,'请输入规范密码(字母,数字)','');
        } else if ($post['new_password'] <> $post['confirm_password']) {
            return msg(100,'密码不一致','');
        } else {
            $data['password'] = md5(md5($post['new_password']));
        }

        // 店铺地址
        if (!empty($post['shop_address'])) {
            $data['shop_address'] = $post['shop_address'];
        }
        //事务
        $this->identityModel->startTrans();
        try {
            $this->identityModel->where('user_id',$this->identity['user_id'])->save($data);
        } catch (\Exception $e) {
            $this->identityModel->rollback();
            return msg(100,'保存失败',$post); 
        }
        $this->identityModel->commit();
        return msg(200,'保存成功','');
    }    
}