<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Controller;
use think\Db;
use think\Exception;

class Goods extends Controller
{
    public function index(){
        /*$goodsId = input('id');
        $goodsInfo = [];
        if(!empty($goodsId)){
            $goodsInfo = $this->getGoodsInfo($goodsId);
            $goodsInfo = $goodsInfo[0];
        }
        $this->assign('goodsInfo',$goodsInfo);*/
        return $this->fetch();
    }

    //购买商品
    public function buyThis(){
        $m3_result = new M3result();
        $uid = session('uid');
        if(empty($uid)){
            $m3_result->code = 0;
            $m3_result->msg = '请登录再购买';
            return json($m3_result->toArray());
        }
        $choose = input('post.choose');
        $buy_num = input('post.buy_num',0);
        $goods_id = input('post.goodsId',0);

        if(empty($buy_num) || empty($goods_id) || empty($goods_price)){
            $m3_result->code = 0;
            $m3_result->msg = '请选择购买数量或者选择升级选项';
            return json($m3_result->toArray());
        }
        $hours = (int)date('H',time());
        if($hours >= 22 || $hours < 10){
            $m3_result->code = 0;
            $m3_result->msg = '竞猜时间为10点-22点，请稍候';
            return json($m3_result->toArray());
        }

        $m3_result->code = 0;
        $m3_result->msg = '失败，请重试';
        $return = json($m3_result->toArray());
        Db::startTrans();
        try {
            $awardInfo = Db::name('award_info')
                ->field('id')
                ->whereNull('result')
                ->order('id desc')
                ->find();
            $award_id = $awardInfo['id'];
            $goodsPrice = Db::name('goods')->where('id',$goods_id)->value('price');
            $amount = $buy_num*$goodsPrice;
            $userMoney = db('users')
                ->where('id',$uid)
                ->field('money,frozen_money')
                ->find();
            if($userMoney['money']-$userMoney['frozen_money'] < $amount){
                $m3_result->code = 0;
                $m3_result->msg = '余额不足，请充值';
                $return = json($m3_result->toArray());
                throw new Exception('error');
            }
            $surplus_money = $userMoney['money']-$amount;
            unset($userMoney);
            $data = [
                'user_id' => $uid,
                'goods_id' => $goods_id,
                'goods_num' => $buy_num,
                'amount' => $amount,
                'created_date' => time(),
                'guessing' => $choose,
                'award_id' => $award_id,
                'status' => 0
            ];
            $res = db('order')->insert($data);
            if($res){
                $changeUserMoney = Db::name('users')
                    ->where('id',$uid)
                    ->update(['money' => $surplus_money]);
                if($changeUserMoney){
                    $m3_result->code = 1;
                    $m3_result->msg = '购买成功';
                    $return = json($m3_result->toArray());
                    throw new Exception('error');
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return $return;
    }

    //获取商品信息
    public function getGoodsInfo($id = null){
        if(empty($id)){
            $where = '';
        }else{
            $where = [
                'id' => $id
            ];
        }
        $res = Db::name('goods')
            ->where($where)
            ->field('id,name,price,success_price,pic_url')
            ->select();
        return $res;
    }
}
