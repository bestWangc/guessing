<?php
namespace app\mapp\controller;

use think\facade\Request;
use think\Db;

class Extract extends Base
{
    public function index()
    {
        $money = $this->getSurplus('money-frozen_money as surplus_money');
        $this->assign([
            'money' => $money
        ]);
        return $this->fetch();
    }

    //提现
    public function doMoneyExtract(Request $request)
    {

        $ext_money = $request::param('ext_money/d',0);
        $real_money = (int)bcmul($ext_money, '0.9');

        if(empty($ext_money)){
            return jsonRes(1,'提现金额不能为0');
        }

        $alipayId = $this->checkAlipay($this->uid);
        if(!$alipayId){
            return jsonRes(1,'未绑定支付宝');
        }

        $userAmount = $this->getSurplus('money-frozen_money as money');
        if($userAmount < $ext_money){
            return jsonRes(1,'余额不足');
        }

        $data = [
            'user_id' => $this->uid,
            'amount' => $ext_money,
            'real_amount' => $real_money,
            'way' => '支付宝',
            'alipay_id' => $alipayId,
            'status' => 2,
            'created_date' => time()
        ];

        $res = Db::name('extract')
            ->insert($data);

        if($res){
            $updateAmount = Db::name('users')
                ->where('id',$this->uid)
                ->setInc('frozen_money',$ext_money);
            if($updateAmount){
                return jsonRes(0,'提交成功');
            }
        }

        return jsonRes(1,'提交失败');
    }

    //提现记录
    public function record()
    {
        $info = Db::name('extract')
            ->where('user_id',$this->uid)
            ->where('status',1)
            ->field('created_date,amount,refuse_reason')
            ->select();

        $this->assign('amount_record',$info);
        return $this->fetch();
    }


    //金币换钱
    public function goldToMoney()
    {
        $goldNum = $this->getsurplus('gold-frozen_gold as surplus_gold');
        $this->assign('gold',$goldNum);
        return $this->fetch();
    }

    //执行金币兑换
    public function doGoldToMoney(Request $request)
    {
        $ext_gold = $request::param('ext_gold',0);
        if(empty($ext_gold)){
            return jsonRes(1,'兑换金币不能为0');
        }

        $alipayId = $this->checkAlipay($this->uid);
        if(!$alipayId){
            return jsonRes(1,'未绑定支付宝');
        }

        $userGold = $this->getSurplus('gold-frozen_gold');
        if($userGold < $ext_gold){
            return jsonRes(1,'金币不足');
        }

        $data=[
            'user_id' => $this->uid,
            'created_date' => time(),
            'purpose' => 3,
            'status' => 2,
            'gold' => $ext_gold
        ];

        $res = Db::name('apply')
            ->insert($data);
        if($res){
            $updateGold = Db::name('users')
                ->where('id',$this->uid)
                ->setInc('frozen_gold',$ext_gold);
            if($updateGold){
                return jsonRes(0,'提交成功');
            }
        }

        return jsonRes(1,'兑换失败');
    }


    //兑换记录
    public function goldRecord()
    {
        $info = Db::name('apply')
            ->where('user_id',$this->uid)
            ->where('status',1)
            ->field('created_date,gold')
            ->select();

        $this->assign('gold_record',$info);
        return $this->fetch();
    }

    //检查是否绑定支付宝
    public static function checkAlipay($user_id)
    {
        $alipayId = Db::name('alipay')
            ->where('user_id',$user_id)
            ->value('id');

        if(!empty($alipayId)) return $alipayId;
        return '';
    }

    //获取剩余金币或者金额
    public function getSurplus($field)
    {
        $num = Db::name('users')
            ->where('id',$this->uid)
            ->value($field);
        return $num;
    }
}
