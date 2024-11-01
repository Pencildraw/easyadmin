<?php

namespace app\common\controller;

use app\BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Db;

/**
 * 控制器基础类
 */
class IndexController extends BaseController
{

    protected function initialize()
    {
        parent::initialize();

    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template
     * @param array $vars
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->app->view->fetch($template, $vars);
    }
    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->app->view->assign($name, $value);
    }

    // 去重数组空值
    public function remove_empty_arrays($data = []){
        foreach ($data as &$value) {
            if (empty($value)) unset($value);
        }
        return $data;
    }
}
