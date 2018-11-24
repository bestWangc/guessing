<?php

namespace app\mapp\controller;

use app\tools\M3result;

class Order extends Base
{
    public function index(){

        $orderInfo = $this->getOrderInfo($this->uid);

        $this->assign([
            'orderInfo' => $orderInfo
        ]);
        return $this->fetch();
    }

    //获取当前用户order信息
    public function getOrderInfo($user_id){
        $orderInfo = db('order')
            ->alias('o')
            ->join('goods g','g.id = o.goods_id','left')
            ->join('award_info ai','ai.id = o.award_id','left')
            ->where('user_id',$user_id)
            ->field('o.id,o.goods_num,o.amount,o.guessing,o.status,o.created_date,o.refuse_reason,g.name,g.price,g.pic_url,g.success_price,ai.win,ai.term_num')
            ->order('o.created_date desc')
            ->select();
        return $orderInfo;
    }

    //提货、退货
    public function takeGoods(){
        $m3_result = new M3result();
        $order_id = input('order_id','');
        $purpose = input('purpose',0);
        if(empty($order_id)){
            $m3_result->code = 0;
            $m3_result->msg = '订单编号不能为空';
            return json($m3_result->toArray());
        }
        $applyOrderNum = db('apply')
            ->where('order_id',$order_id)
            ->count();
        if($applyOrderNum > 0){
            $m3_result->code = 0;
            $m3_result->msg = '订单请勿重复提交';
            return json($m3_result->toArray());
        }
        $addressInfo = db('address')
            ->where('user_id',$this->uid)
            ->field('id')
            ->count();
        if(!$addressInfo){
            $m3_result->code = 0;
            $m3_result->msg = '未设置收货地址';
            return json($m3_result->toArray());
        }

        if ($purpose == 2){
            //查询余额是否够10元
            $balance = db('users')
                ->where('user_id',$this->uid)
                ->field('money')
                ->find();
            if($balance['money'] < 10){
                $m3_result->code = 0;
                $m3_result->msg = '金额不足10元，无法提货';
                return json($m3_result->toArray());
            }
        }

      $data=[
            'order_id' => $order_id,
            'user_id' => $this->uid,
            'created_date' => time(),
            'purpose' => $purpose,
            'status' => 2
        ];
        $res = db('apply')
            ->insert($data);

        if($res){
            $changeOrderStatus = db('order')->where('id',$order_id)->setField('status',$purpose);
            if($changeOrderStatus){
                $m3_result->code = 1;
                $m3_result->msg = '提交成功';
                return json($m3_result->toArray());
            }
        }

        $m3_result->code = 0;
        $m3_result->msg = '未知错误，请重试';
        return json($m3_result->toArray());
    }

    //转为金币
    public function toGold(){
        $m3_result = new M3result();
        $order_id = input('order_id','');
        if(empty($order_id)){
            $m3_result->code = 0;
            $m3_result->msg = '订单编号不能为空';
            return json($m3_result->toArray());
        }

        $orderInfo = db('order o')
            ->join('award_info ai','ai.id = o.award_id','left')
            ->field('o.amount,o.guessing,ai.win')
            ->where('o.id',$order_id)
            ->find();

        $gold = $orderInfo['amount'];
        $oldGold = db('users')
            ->where('id', $user_id)
            ->value('gold');
        $gold = intval(($gold+$oldGold)/10);
        $res = db('users')
            ->where('id',$this->uid)
            ->setField('gold',$gold);

        if($res){
            $toStatus = db('order')->where('id',$order_id)->setField('status',5);
            if($toStatus){
                $m3_result->code = 1;
                $m3_result->msg = '成功';
                return json($m3_result->toArray());
            }
        }
        $m3_result->code = 0;
        $m3_result->msg = '失败';
        return json($m3_result->toArray());
    }


    //提现记录
    public function record(){
        return $this->fetch();
    }

}
