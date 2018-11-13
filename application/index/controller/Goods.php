<?php

namespace app\index\controller;

use app\tools\M3result;

class Goods extends Base
{
    public function index(){
        $goods_id = input('goods_id',0);

        $goods_info = $this->getGoodsInfo($goods_id);
        $termInfo = controller('reward')->getAwardInfo(1);

        $this->assign([
            'goods_id' => $goods_id,
            'goods_info' => $goods_info,
            'nowTermNum' => $termInfo[0]['term_num'],
            'award_id' => $termInfo[0]['id'],
        ]);
        return $this->fetch();
    }

    //购买商品
    public function buyGoods(){
        $buy_num = input('buy_num',0);

        $goods_id = input('goods_id',0);
        $goods_price = input('goods_price',0);

        $m3_result = new M3result();
        if(empty($buy_num) || empty($goods_id) || empty($goods_price)){
            $m3_result->code = 0;
            $m3_result->msg = '请选择购买数量或者选择升级选项';
            return json($m3_result->toArray());
        }
        $hours = date('h',time());
        if($hours >= 22 || $hours < 10){
            $m3_result->code = 0;
            $m3_result->msg = '竞猜时间为10点-22点，请稍候';
            return json($m3_result->toArray());
        }

        $award_num = input('award_num',0);
        $award_id = input('award_id',0);
        $amount = $buy_num*$goods_price;
        $userMoney = db('users')
            ->where('id',$this->uid)
            ->field('money,frozen_money')
            ->find();
        if($userMoney['money']-$userMoney['frozen_money'] < $amount){
            $m3_result->code = 0;
            $m3_result->msg = '余额不足，请充值';
            return json($m3_result->toArray());
        }
        $surplus_money = $userMoney['money']-$amount;
        unset($userMoney);
        $data = [
            'user_id' => $this->uid,
            'goods_id' => $goods_id,
            'goods_num' => $buy_num,
            'amount' => $amount,
            'created_date' => time(),
            'guessing' => $award_num,
            'award_id' => $award_id,
            'status' => 0
        ];
        $res = db('order')
            ->insert($data);
        if($res){
            $changeUserMoney = db('users')
                ->where('id',$this->uid)
                ->update(['money' => $surplus_money]);
            if($changeUserMoney){
                $m3_result->code = 1;
                $m3_result->msg = '购买成功';
                return json($m3_result->toArray());
            }
        }
        $m3_result->code = 0;
        $m3_result->msg = '失败，请重试';
        return json($m3_result->toArray());

    }

    //获取商品详细信息
    public function getGoodsInfo($goods_id){
        $info = db('goods')
            ->where('id', $goods_id)
            ->field('id,name,price,success_price,pic_url')
            ->find();
        return $info;
    }

    //获取商品总价格
    public function getGoodsCount($goods_id){
    }

}
