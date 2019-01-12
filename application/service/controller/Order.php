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
use think\facade\Session;

class Order extends Base
{
    //转为金币
    public function toGold(Request $request)
    {
        $type = $request::post('type');

        $uid = $this->getUid($type);

        $m3_result = new M3result();
        if(empty($uid)){
            $m3_result->code = 0;
            $m3_result->msg = '错误，请重试';
            return json($m3_result->toArray());
        }

        $order_id = $request::post('order_id','');
        if(empty($order_id)){
            $m3_result->code = 0;
            $m3_result->msg = '订单编号不能为空';
            return json($m3_result->toArray());
        }

        $orderInfo = Db::name('order o')
            ->join('award_info ai','ai.id = o.award_id','left')
            ->field('o.amount,o.guessing,ai.win')
            ->where('o.id',$order_id)
            ->where('status',0)
            ->find();

        $gold = (int)$orderInfo['amount'];
        $gold = (int)($gold/10);

        Db::startTrans();
        try {
            $res = Db::name('users')
                ->where('id',$uid)
                ->lock()
                ->setInc('gold',$gold);
            if($res){
                $toStatus = Db::name('order')
                    ->where('id',$order_id)
                    ->setField('status',5);
                if($toStatus){
                    $m3_result->code = 0;
                    $m3_result->msg = '成功';
                }
            }
            // 提交事务
            Db::commit();
            return json($m3_result->toArray());
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        $m3_result->code = 1;
        $m3_result->msg = '失败';
        return json($m3_result->toArray());
    }

    //提货、退货
    public function takeGoods(Request $request)
    {
        $m3_result = new M3result();
        $type = $request::post('type');
        $uid = $this->getUid($type);

        if(empty($uid)){
            $m3_result->code = 0;
            $m3_result->msg = '错误，请重试';
            return json($m3_result->toArray());
        }
        $order_id = $request::post('order_id','');
        $purpose = $request::post('purpose',0);
        if(empty($order_id)){
            $m3_result->code = 0;
            $m3_result->msg = '订单编号不能为空';
            return json($m3_result->toArray());
        }
        $applyOrderNum = Db::name('apply')
            ->where('order_id',$order_id)
            ->count();
        if($applyOrderNum > 0){
            $m3_result->code = 0;
            $m3_result->msg = '订单已提交，请勿重复操作';
            return json($m3_result->toArray());
        }
        $addressInfo = Db::name('address')
            ->where('user_id',$uid)
            ->field('id')
            ->count();
        if(!$addressInfo){
            $m3_result->code = 0;
            $m3_result->msg = '未设置收货地址';
            return json($m3_result->toArray());
        }

        if ($purpose == 2){
            //查询余额是否够10元
            $balance = Db::name('users')
                ->where('id',$uid)
                ->value('money');
            if((int)$balance < 10){
                $m3_result->code = 0;
                $m3_result->msg = '金额不足10元，无法提货';
                return json($m3_result->toArray());
            }
        }

        $data=[
            'order_id' => $order_id,
            'user_id' => $uid,
            'created_date' => time(),
            'purpose' => $purpose,
            'status' => 2
        ];

        Db::startTrans();
        try {
            $res = Db::name('apply')->insert($data);

            if($res){
                $changeOrderStatus = Db::name('order')
                    ->where('id',$order_id)
                    ->setField('status',$purpose);
                if($changeOrderStatus){
                    $m3_result->code = 1;
                    $m3_result->msg = '提交成功';
                }
            }
            // 提交事务
            Db::commit();
            return json($m3_result->toArray());
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        $m3_result->code = 0;
        $m3_result->msg = '未知错误，请重试';
        return json($m3_result->toArray());
    }

    //从缓存中获取uid
    /*private function getUid($type)
    {
        $uid = '';
        if($type == 1){
            $uid = Session::get('user_id');
        } elseif ($type == 2){
            $uid = Session::get('u_id');
        }
        return $uid;
    }*/
}