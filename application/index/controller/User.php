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
            'adressNum' => $adressNum
        ]);
        return $this->fetch();
    }

    //获取user信息
    //获取用户信息
    public function getUserInfo(){
        $user_id = session('user_id');
        $userInfo = db('users')
            ->where('id', $user_id)
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

        $user_id = session('user_id');
        $m3_result = new M3result();
        if(empty($alipay_account) || empty($alipay_name)){
            $m3_result->status = 0;
            $m3_result->msg = '信息内容不能为空';
            return $m3_result->toJson();
        }

        $data=[
            'user_id' => $user_id,
            'alipay_name' => $alipay_name,
            'alipay_account' => $alipay_account
        ];

        $oldInfo = db('alipay')->where('user_id',$user_id)->find();
        if(empty($oldInfo)){
            $res = db('alipay')
                ->insert($data);
        }else{
            $res = db('alipay')
                ->where('user_id', $user_id)
                ->update($data);
        }
        if($res){
            $m3_result->status = 1;
            $m3_result->msg = '保存成功';
            return $m3_result->toJson();
        }

        $m3_result->status = 0;
        $m3_result->msg = '保存失败，请重试';
        return $m3_result->toJson();
    }

    //添加手机号
    public function addTel(){
        $tel_num = input('tel_num','');
        $user_id = session('user_id');
        $m3_result = new M3result();
        if(empty($tel_num)){
            $m3_result->status = 0;
            $m3_result->msg = '手机号不能为空';
            return $m3_result->toJson();
        }
        $res = db('users')
            ->where('id', $user_id)
            ->setField('tel', $tel_num);
        if($res){
            $m3_result->status = 1;
            $m3_result->msg = '保存成功';
            return $m3_result->toJson();
        }
        $m3_result->status = 0;
        $m3_result->msg = '没有任何修改';
        return $m3_result->toJson();
    }
}
