<?php

namespace app\manage\controller;


class Extract extends Base
{

    public function index(){
        return $this->fetch();
    }

    //提现申请
    public function applyFor(){
        return $this->fetch();
    }
}
