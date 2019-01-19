<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-5
 * Time: 11:00
 */

namespace app\mapp\controller;

use think\Controller;

class Base extends Controller
{

    public $uid;
    protected function initialize()
    {
        parent::initialize();
        $this->checkLogin();
        $this->uid = session('user_id');
    }

    public function checkLogin(){
        //seeion没有user_id 重新登录
        if(!session('user_id')) $this->redirect('/mapp/login');
    }
}