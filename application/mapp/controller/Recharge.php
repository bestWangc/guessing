<?php

namespace app\mapp\controller;

use app\tools\M3result;
use app\service\controller\Recharge as sRecharge;

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

        if($moeny_amount == 0){
            return jsonRes(1,'充值金额不能为0');
        }
        $data = [
            'user_id' => $this->uid,
            'amount' => $moeny_amount,
            'status' => 2,
            'created_date' => time(),
            'way' => $pay_way ? '支付宝' : '微信'
        ];
        $res = db('recharge')->insert($data);
        if($res){
            return jsonRes(0,'充值记录创建成功');
        }
        return jsonRes(1,'未知错误，请重试');
    }
}