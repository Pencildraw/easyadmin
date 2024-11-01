<?php
// 命名空间定义
namespace app\index\controller;
 
use app\common\controller\IndexController;
use think\App;


class Index extends IndexController
{
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        parent::initialize();
    }
    public function index(){
        return $this->fetch();
    }
}