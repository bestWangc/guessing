<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-5
 * Time: 11:00
 */

namespace app\manage\controller;

use think\App;
use think\Controller;

class Base extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->checkLogin();
    }

    public function checkLogin(){
        //seeion没有user_id 重新登录
        if(!session('user_id')) $this->redirect('/manage/login');
    }
}