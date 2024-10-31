<?php

namespace app\admin\controller\dealer;

use app\common\controller\AdminController;
use EasyAdmin\annotation\ControllerAnnotation;
use EasyAdmin\annotation\NodeAnotation;
use think\App;

use app\admin\model\mall\goods;
use app\admin\model\company\user;
use think\facade\Config;

/**
 * @ControllerAnnotation(title="company_salesman")
 */
class salesman extends AdminController
{

    use \app\admin\traits\Curd;

    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new \app\admin\model\company\Identity();
        
    }

    /**
     * @NodeAnotation(title="列表")
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            if (input('selectFields')) {
                return $this->selectList();
            }
            list($page, $limit, $where) = $this->buildTableParames();
            $count = $this->model
                ->where($where)
                ->where('type',3)
                ->count();
            $list = $this->model
                ->where($where)
                ->where('type',3)
                ->page($page, $limit)
                ->order($this->sort)
                ->select();
            $data = [
                'code'  => 0,
                'msg'   => '',
                'count' => $count,
                'data'  => $list,
            ];
            return json($data);
        }
        $this->layoutBgColor();
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="添加")
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);

            $post['password'] = empty($post['password']) ?'123456':$post['password'];
            $post['password'] = md5(md5($post['password']));
            // $post['binding_status'] = empty($post['user_id']) ?0:1;
            // print_r($post); exit;
            $create_time = time();

            $this->model->startTrans();
            try {
                // 添加
                $post['create_time'] = $create_time;
                $insertGetId = $this->model->insertGetId($post);
                if (!$insertGetId) {
                    $this->model->rollback();
                    throw new \Exception('添加业务员失败');
                }
                $identity = $this->model->find($insertGetId);
                // 生成二维码
                $qrcodeService = new \app\admin\service\QrcodeService;
                // generate($identity_id = 0 ,$type = 0 ,$goods_id = 1) 
                $result = $qrcodeService->generate($identity->id ,$identity->type); 
                // print_r($result); exit;
                if (!$result['code']) {
                    $this->model->rollback();
                    throw new \Exception($result['msg']);
                }
                $identity->qrcode_image = Config::get('app')['const_data']['web_url'].$result['qrcode_image'];
                if (!$identity->save()) {
                    $this->model->rollback();
                    throw new \Exception('二维码绑定失败');
                }
                // 绑定用户
                // if (!empty($post['user_id'])) {
                //     $userModel = new user;
                //     $userData['identity_id'] = $insertGetId;
                //     $userData['binding_status'] = 1;
                //     $userData['name'] = $post['name'];
                //     $userData['password'] = $post['password'];
                //     if (!$userModel->where('id' ,$post['user_id'])->save($userData)) {
                //         $this->model->rollback();
                //         throw new \Exception('用户绑定失败');
                //     }
                // }
                
            } catch (\Exception $e) {
                $this->model->rollback();
                $this->error('保存失败:'.$e->getMessage());
            }
            $this->model->commit();
            $this->success('保存成功');
        }

        $typeList = $this->model->getTypeList();
        $this->assign('typeList' ,$typeList);
        $goodsModel = new goods;
        $goods = $goodsModel->where('status' ,1)->select();
        $this->assign('goods' ,$goods);
        // $userModel = new user;
        $identity = $this->model->where('status' ,1)->where('type' ,2)->find();
        $this->assign('identity' ,$identity);
        return $this->fetch();
    }

    /**
     * @NodeAnotation(title="编辑")
     */
    public function edit($id)
    {
        $row = $this->model->find($id);
        empty($row) && $this->error('数据不存在');
        if ($this->request->isPost()) {
            $post = $this->request->post();
            $rule = [];
            $this->validate($post, $rule);
            try {
                if (!empty($post['password'])) {
                    // 校验密码
                    $pattern = "/^[a-zA-Z0-9]+$/";  //密码只包含数字 字母
                    if (!preg_match($pattern, $post['password'])) {  
                        $this->error('密码请只输入数字|字母');
                    } 
                    $post['password'] = md5(md5($post['password'])); 
                }
                $post['update_time'] = time();
                $save = $row->save($post);
            } catch (\Exception $e) {
                $this->error('保存失败');
            }
            $save ? $this->success('保存成功') : $this->error('保存失败');
        }
        // $userModel = new user;
        // $user = $userModel->where('status' ,1)->where('binding_status' ,0)->select();
        // $this->assign('user' ,$user);
        $this->assign('row', $row);
        return $this->fetch();
    }
    
}