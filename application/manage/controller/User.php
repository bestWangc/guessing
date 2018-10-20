<?php

namespace app\manage\controller;


class User extends Base
{

    public function index(){
        return $this->fetch();
    }

    //修改密码
    public function changePwd(){
        return $this->fetch('changePwd');
    }

    //抢购记录
    public function buyLog(){
        return $this->fetch();
    }

}
