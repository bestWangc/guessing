<?php

namespace app\manage\controller;


class Recharge extends Base
{

    public function index(){
        return $this->fetch();
    }

    //抢购记录
    public function buyLog(){
        return $this->fetch();
    }

    //充值申请
    public function applyFor(){
        return $this->fetch();
    }

}
