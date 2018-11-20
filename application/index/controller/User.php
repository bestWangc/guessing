<?php

namespace app\index\controller;

use app\tools\M3result;

class User extends Base
{
    public function index(){
        $userInfo = $this->getUserInfo();

        $adressNum = db('address')
            ->where('user_id', session('user_id'))
            ->count();

        $this->assign([
            'user_photo' => $userInfo['photo'],
            'money' => $userInfo['money']-$userInfo['frozen_money'],
            'email' => $userInfo['email'],
            'tel' => $userInfo['tel'],
            'name' => $userInfo['name'],
            'uid' => $this->uid,
            'adressNum' => $adressNum
        ]);
        return $this->fetch();
    }

    //获取user信息
    //获取用户信息
    public function getUserInfo(){
        $userInfo = db('users')
            ->where('id', $this->uid)
            ->field('name,gold,photo,money,tel,email,frozen_money,frozen_gold')
            ->find();
        return $userInfo;
    }

    //支付宝信息
    public function alipay(){
        $alipayInfo = db('alipay')
            ->where('user_id',session('user_id'))
            ->field('alipay_account,alipay_name')
            ->find();

        if(!empty($alipayInfo)){
            $this->assign([
                'name' => $alipayInfo['alipay_name'],
                'account' => $alipayInfo['alipay_account']
            ]);
        }
        return $this->fetch();
    }

    //添加支付宝信息
    public function alipayInfo(){
        $alipay_account = input('account','');
        $alipay_name = input('name','');

        $m3_result = new M3result();
        if(empty($alipay_account) || empty($alipay_name)){
            $m3_result->code = 0;
            $m3_result->msg = '信息内容不能为空';
            return json($m3_result->toArray());
        }

        $data=[
            'user_id' => $this->uid,
            'alipay_name' => $alipay_name,
            'alipay_account' => $alipay_account
        ];

        $oldInfo = db('alipay')->where('user_id',$this->uid)->find();
        if(empty($oldInfo)){
            $res = db('alipay')
                ->insert($data);
        }else{
            $res = db('alipay')
                ->where('user_id', $this->uid)
                ->update($data);
        }
        if($res){
            $m3_result->code = 1;
            $m3_result->msg = '保存成功';
            return json($m3_result->toArray());
        }

        $m3_result->code = 0;
        $m3_result->msg = '保存失败，请重试';
        return json($m3_result->toArray());
    }

    //添加手机号
    public function addTel(){
        $tel_num = input('tel_num','');
        $m3_result = new M3result();
        if(empty($tel_num)){
            $m3_result->code = 0;
            $m3_result->msg = '手机号不能为空';
            return json($m3_result->toArray());
        }
        $res = db('users')
            ->where('id', $this->uid)
            ->setField('tel', $tel_num);
        if($res){
            $m3_result->code = 1;
            $m3_result->msg = '保存成功';
            return json($m3_result->toArray());
        }
        $m3_result->code = 0;
        $m3_result->msg = '没有任何修改';
        return json($m3_result->toArray());
    }
}
