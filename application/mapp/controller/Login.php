<?php

namespace app\mapp\controller;

use think\Controller;

class Login extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function register(){
        return $this->fetch();
    }

    //找回密码
    public function findpw(){
        return '找回密码';
    }

}
