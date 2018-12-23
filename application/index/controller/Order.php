<?php

namespace app\index\controller;


use think\Db;

class Order extends Base
{

    public function index(){
        return $this->fetch();
    }

    //订单排行
    public function rank(){
        return $this->fetch();
    }

    public function info(){
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->field('u.name as account,u.parent_id,u.tel,u.email,ali.alipay_account,ali.alipay_name,a.name as uname,a.phone,a.details')
            ->where('u.id',$this->uid)
            ->find();
        $this->assign('info',$res);
        return $this->fetch();
    }
}
