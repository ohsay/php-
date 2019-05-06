<?php
/**
 * 后台管理系统首页
 */
namespace app\admin\controller;

use think\Db;
use think\Session;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function welcome(){
        return $this->fetch();
    }
}
