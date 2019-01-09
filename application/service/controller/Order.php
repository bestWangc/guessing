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

        $uid = '';
        if($type == 1){
            $uid = Session::get('user_id');
        } elseif ($type == 2){
            $uid = Session::set('u_id');
        }
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
}