<?php

namespace app\index\controller;

use think\Controller;

class Base extends Controller
{

    protected static $uid;
    protected function initialize()
    {
        parent::initialize();
        $this->checkLogin();
        $this::$uid = session('u_id');
    }

    public function checkLogin(){
        //seeion没有user_id 重新登录
        if(!session('u_id')) $this->redirect('/index/login');
    }

}