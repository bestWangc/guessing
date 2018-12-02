<?php

namespace app\index\controller;

use think\Controller;

class Base extends Controller
{

    protected $uid;
    protected function initialize()
    {
        parent::initialize();
        $this->checkLogin();
        $this->uid = session('uid');
    }

    public function checkLogin(){
        //seeion没有user_id 重新登录
        if(!session('uid')) $this->redirect('/index/login');
    }

}