<?php

namespace app\manage\controller;


class User extends Base
{

    public function index(){
        return $this->fetch();
    }

    //修改密码
    public function changePwd(){
        $this->assign([
            'uid' => session('user_id'),
            'uname' => session('user_name')
        ]);
        return $this->fetch('changePwd');
    }

    //抢购记录
    public function buyLog(){
        return $this->fetch();
    }

}
