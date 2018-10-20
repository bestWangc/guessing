<?php

namespace app\index\controller;

use app\tools\M3result;

class Recharge extends Base
{
    public function index(){
        return $this->fetch();
    }

    //创建充值记录
    public function createRecharge(){
        //充值金额，默认是0
        $moeny_amount = input('moeny_amount',0);
        //充值方式，0为微信，1为支付宝
        $pay_way = input('pay_way',0);

        $user_id = session('user_id');

        $m3_result = new M3result();
        if($moeny_amount == 0){
            $m3_result->status = 0;
            $m3_result->msg = '充值金额不能为0';
            return $m3_result->toJson();
        }
        $data = [
            'user_id' => $user_id,
            'amount' => $moeny_amount,
            'status' => 2,
            'created_date' => time(),
            'way' => $pay_way ? '支付宝' : '微信'
        ];
        $res = db('recharge')->insert($data);
        if($res){
            $m3_result->status = 1;
            $m3_result->msg = '充值记录创建成功';
            return $m3_result->toJson();
        }
        $m3_result->status = 0;
        $m3_result->msg = '未知错误，请重试';
        return $m3_result->toJson();
    }
}