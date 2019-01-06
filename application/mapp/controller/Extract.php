<?php

namespace app\mapp\controller;

use app\tools\M3result;

class Extract extends Base
{
    public function index(){

        $money = $this->getSurplus('money-frozen_money as surplus_money');
        $this->assign([
            'money' => $money
        ]);
        return $this->fetch();
    }

    //提现
    public function doMoneyExtract(){
        $m3_result = new M3result();
        $ext_money = input('ext_money/d',0);
        $real_money = (int)bcmul($ext_money, '0.9');

        if(empty($ext_money)){
            $m3_result->code = 0;
            $m3_result->msg = '提现金额不能为0';
            return json($m3_result->toArray());
        }

        $alipayId = $this->checkAlipay($this->uid);
        if(!$alipayId){
            $m3_result->code = 0;
            $m3_result->msg = '未绑定支付宝';
            return json($m3_result->toArray());
        }

        $userAmount = $this->getSurplus('money-frozen_money as money');
        if($userAmount < $ext_money){
            $m3_result->code = 0;
            $m3_result->msg = '余额不足';
            return json($m3_result->toArray());
        }

        /*$extractCount = db('extract')
            ->where('user_id', $this->uid)
            ->count();
        if($extractCount){
            $m3_result->code = 0;
            $m3_result->msg = '还有提现未处理，请等待';
            return json($m3_result->toArray());
        }*/

        $data = [
            'user_id' => $this->uid,
            'amount' => $ext_money,
            'real_amount' => $real_money,
            'way' => '支付宝',
            'alipay_id' => $alipayId,
            'status' => 2,
            'created_date' => time()
        ];

        $res = db('extract')
            ->insert($data);

        if($res){
            $updateAmount = db('users')
                ->where('id',$this->uid)
                ->setInc('frozen_money',$ext_money);
            if($updateAmount){
                $m3_result->code = 1;
                $m3_result->msg = '提交成功';
                return json($m3_result->toArray());
            }
        }

        $m3_result->code = 0;
        $m3_result->msg = '提现失败';
        return json($m3_result->toArray());
    }

    //提现记录
    public function record(){
        $info = db('extract')
            ->where('user_id',$this->uid)
            ->where('status',1)
            ->field('created_date,amount,refuse_reason')
            ->select();

        $this->assign('amount_record',$info);
        return $this->fetch();
    }


    //金币换钱
    public function goldToMoney(){
        $goldNum = $this->getsurplus('gold-frozen_gold as surplus_gold');
        $this->assign('gold',$goldNum);
        return $this->fetch();
    }

    //执行金币兑换
    public function doGoldToMoney(){
        $m3_result = new M3result();
        $ext_gold = input('ext_gold',0);
        if(empty($ext_gold)){
            $m3_result->code = 0;
            $m3_result->msg = '兑换金币不能为0';
            return json($m3_result->toArray());
        }

        $alipayId = $this->checkAlipay($this->uid);
        if(!$alipayId){
            $m3_result->code = 0;
            $m3_result->msg = '未绑定支付宝';
            return json($m3_result->toArray());
        }

        $userGold = $this->getSurplus('gold-frozen_gold');
        if($userGold < $ext_gold){
            $m3_result->code = 0;
            $m3_result->msg = '金币不足';
            return json($m3_result->toArray());
        }

        $data=[
            'user_id' => $this->uid,
            'created_date' => time(),
            'purpose' => 3,
            'status' => 2,
            'gold' => $ext_gold
        ];

        $res = db('apply')
            ->insert($data);
        if($res){
            $updateGold = db('users')
                ->where('id',$this->uid)
                ->setInc('frozen_gold',$ext_gold);
            if($updateGold){
                $m3_result->code = 1;
                $m3_result->msg = '提交成功';
                return json($m3_result->toArray());
            }
        }

        $m3_result->code = 0;
        $m3_result->msg = '兑换失败';
        return json($m3_result->toArray());
    }


    //兑换记录
    public function goldRecord(){
        $info = db('apply')
            ->where('user_id',$this->uid)
            ->where('status',1)
            ->field('created_date,gold')
            ->select();

        $this->assign('gold_record',$info);
        return $this->fetch();
    }

    //检车是否绑定支付宝
    public function checkAlipay($user_id){
        $alipayId = db('alipay')
            ->where('user_id',$user_id)
            ->field('id')
            ->find();
        if(!empty($alipayId)) return $alipayId['id'];
        return '';
    }

    //获取剩余金币或者金额
    public function getSurplus($field){
        $num = db('users')
            ->where('id',$this->uid)
            ->value($field);
        return $num;
    }
}
