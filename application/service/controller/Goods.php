<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;
use think\Db;
use think\facade\Request;

class Goods extends Base
{
    /**
     * 购买商品
     * @param Request $request
     * @return \think\response\Json
     */
    public function buyGoods(Request $request)
    {
        $m3_result = new M3result();

        $type = $request::post('type');

        $uid = $this->getUid($type);
        if(empty($uid)){
            $m3_result->code = 3;
            $m3_result->msg = '请先登录再购买';
            return json($m3_result->toArray());
        }

        $choose = $request::post('award_num');
        $buy_num = $request::post('buy_num',0);
        $goods_id = $request::post('goods_id',0);

        if(empty($buy_num) || empty($goods_id) || is_null($choose)){
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
            $award_id = Db::name('award_info')
                ->whereNull('result')
                ->order('id desc')
                ->value('id');

            $goodsPrice = Db::name('goods')
                ->where('id',$goods_id)
                ->value('price');

            $amount = $buy_num*$goodsPrice;
            $userMoney = Db::name('users')
                ->where('id',$uid)
                ->field('money,frozen_money,money-frozen_money as dprice')
                ->find();

            if($userMoney['dprice'] < $amount){
                $m3_result->code = 0;
                $m3_result->msg = '余额不足，请充值';
                $return = json($m3_result->toArray());
                throw new Exception('error');
            }

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
            $res = Db::name('order')->insert($data);
            if($res){
                $changeUserMoney = Db::name('users')
                    ->where('id',$uid)
                    ->setDec('money',$amount);
                if($changeUserMoney){
                    $m3_result->code = 1;
                    $m3_result->msg = '购买成功';
                    $return = json($m3_result->toArray());
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
}