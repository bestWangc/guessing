<?php

namespace app\index\controller;

use think\Db;
use think\facade\Session;

class User extends Base
{

    public function index()
    {
        $userInfo = $this->getSimpleInfo();
        $this->assign([
            'uname' => $userInfo['account'],
            'rname' => $userInfo['role_name'],
            'money' => $userInfo['money'],
            'gold' => $userInfo['gold'],
            'phone' => $userInfo['tel'],
            'alipayAccount' => $userInfo['alipay_account'],
            'email' => $userInfo['email']
        ]);
        return $this->fetch();
    }

    public function getSimpleInfo()
    {
        $fieldList = 'u.name as account,u.money,u.gold,u.tel,u.email,ali.alipay_account,ul.role_name';
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->join('user_role ul','ul.id = u.role','left')
            ->field($fieldList)
            ->where('u.id',$this->uid)
            ->find();
        return $res;
    }
}
