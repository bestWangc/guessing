<?php

namespace app\manage\controller;


use MongoDB\BSON\Decimal128;
use think\Db;

class Apply extends Base
{
    //申请详情
    public function goldApply(){
        return $this->fetch();
    }

    //退货申请
    public function backGoodsApply(){
        return $this->fetch();
    }

    //退货申请
    public function takeGoodsApply(){
        return $this->fetch();
    }


    //金币兑换申请详细信息
    public function goldApplyDetails(){
        $purpose = input('post.purpose');

        $result = db('apply')
            ->alias('sa')
            ->join('users su','su.id = sa.user_id')
            ->where('sa.status',2)
            ->where('sa.purpose',3)
            ->field('sa.id,su.name,sa.user_id,sa.gold,sa.status,sa.created_date')
            ->order('sa.created_date asc')
            ->select();
        if(!empty($result)){
            foreach ($result as $key => &$value){
                $value['status'] = statusTrans($value['status']);
                $value['created_date'] = date('Y-m-d H:i:s',$value['created_date']);
            }
        }
        return jsonRes(1,'成功',$result);
    }

    //申请详细信息
    public function applyGoodsDetails(){
        $purpose = input('post.purpose/d');

        $field = 'sa.id,sa.user_id,su.name,so.id as order_id,sg.name as goods_name,so.goods_num,so.amount,so.goods_num*sg.success_price as success_amonut,so.guessing,sai.term_num,sai.result,sa.status,sa.created_date';
        $result = db('apply')
            ->alias('sa')
            ->join('users su','su.id = sa.user_id')
            ->join('order so','so.id = sa.order_id')
            ->join('goods sg','sg.id = so.goods_id')
            ->join('award_info sai','sai.id = so.award_id')
            ->where('sa.status',2)
            ->where('sa.purpose',$purpose)
            ->field($field)
            ->order('sa.created_date asc')
            ->select();
        if(!empty($result)){
            foreach ($result as $key => &$value){
                $value['status'] = statusTrans($value['status']);
                $value['created_date'] = date('Y-m-d H:i:s',$value['created_date']);
            }
        }
        return jsonRes(1,'成功',$result);
    }

    //操作
    public function operation(){
        $id = input('post.id/d',0);
        $user_id = input('post.user_id/d');
        $reason = input('post.reason','');
        $operate = input('post.operate/d');
        $purpose = input('post.purpose/d');
        $amount = input('post.amount/d') ?? input('post.gold/d');

        if(!$id || is_null($operate) || is_null($user_id) || is_null($amount) || is_null($purpose)){
            return jsonRes(0,'参数不够，请重试');
        }
        $data = [
            'status' => $operate,
            'updated_date' => time()
        ];

        switch ($purpose){
            case 1:
                $order_id = input('post.order_id/d');
                if(!$order_id){
                    return jsonRes(0,'订单id不存在');
                }
                if(!$operate){
                    $res = Db::name('order')
                        ->where('id',$order_id)
                        ->update(['status'=>0,'refuse_reason'=>$reason]);
                    if(!$res){
                        return jsonRes(0,'出现错误，请重试');
                    }
                }else{
                    Db::startTrans();
                    try{
                        $res = Db::name('order')
                            ->where('id',$order_id)
                            ->setField('status',4);
                        $result = Db::name('users')
                            ->where('id',$user_id)
                            ->inc('money',$amount)
                            ->update();
                        // 提交事务
                        Db::commit();
                    }catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                    }
                    if(!$res || !$result){
                        return jsonRes(0,'出错，请重试');
                    }
                }

                break;
            case 2:
                $order_id = input('post.order_id/d');
                if(!$order_id){
                    return jsonRes(0,'订单id不存在');
                }
                if(!$operate) {
                    $res = Db::name('order')
                        ->where('id',$order_id)
                        ->update(['status'=>0,'refuse_reason'=>$reason]);
                    if(!$res){
                        return jsonRes(0,'出现错误，请重试');
                    }
                }else{
                    //判断金币是否充足
                    $balance = Db::name('users')
                        ->where('id',$user_id)
                        ->field('money')
                        ->find();
                    if($balance['money'] < 10){
                        return jsonRes(0,'账户余额不足运费10元');
                    }

                    $addressInfo = Db::name('address')
                        ->where('id',$user_id)
                        ->field('name,phone,details')
                        ->find();
                    if(empty($addressInfo) || empty($addressInfo['name']) || empty($addressInfo['phone']) || empty($addressInfo['details'])){
                        return jsonRes(0,'收货信息不全');
                    }
                    Db::startTrans();
                    try{
                        $result = Db::name('users')
                            ->dec('money',10)
                            ->where('id',$user_id)
                            ->update();
                        $res = Db::name('order')
                            ->where('id',$order_id)
                            ->setField('status',3);
                        // 提交事务
                        Db::commit();
                    }catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                    }
                    if(!$result || !$res){
                        return jsonRes(0,'兑换未成功，请重试');
                    }
                }
                break;
            case 3:
                if($operate){
                    Db::startTrans();
                    try{
                        //判断金币是否充足
                        $balance = db('users')
                            ->where('id',$user_id)
                            ->field('gold,frozen_gold')
                            ->find();
                        if($amount > $balance['gold'] || $amount > $balance['frozen_gold']){
                            return jsonRes(0,'金币余额不足');
                        }
                        $result = Db::name('users')
                            ->dec('gold',$amount)
                            ->dec('frozen_gold',$amount)
                            ->where('id',$user_id)
                            ->update();
                        // 提交事务
                        Db::commit();
                    }catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                    }
                    if(!$result){
                        return jsonRes(0,'兑换未成功，请重试');
                    }
                }
        }

        $res = db('apply')->where('id',$id)->update($data);
        if($res){
            return jsonRes(1,'成功',$res);
        }
        return jsonRes(0,'失败，请重试');
    }
}
