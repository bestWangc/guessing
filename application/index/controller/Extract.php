<?php
namespace app\index\controller;

use app\tools\M3result;
use think\facade\Request;
use think\Db;

class Extract extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    //月提现
    public function doMoneyExtract(Request $request)
    {
        $m3_result = new M3result();
        $ext_money = $request::post('ext_money/d',0);
        $real_money = (int)bcmul($ext_money, '0.9');

        if(empty($ext_money)){
            $m3_result->code = 0;
            $m3_result->msg = '提现金额不能为0';
            return json($m3_result->toArray());
        }

        //检车是否绑定支付宝
        $alipayId = self::checkAlipay($this->uid);
        if(empty($alipayId)){
            $m3_result->code = 0;
            $m3_result->msg = '未绑定支付宝';
            return json($m3_result->toArray());
        }

        //检查余额是否足够
        $userAmount = User::getSurplus('money',$this->uid);
        if($userAmount < $ext_money){
            $m3_result->code = 0;
            $m3_result->msg = '余额不足';
            return json($m3_result->toArray());
        }

        /*$extractCount = Db::name('extract')
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

        $res = Db::name('extract')->insert($data);

        if($res){
            $updateAmount = Db::name('users')
                ->where('id',$this->uid)
                ->setField('frozen_money',$ext_money);
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

    //检查是否绑定支付宝
    public static function checkAlipay($user_id)
    {
        $alipayId = Db::name('alipay')
            ->where('user_id',$user_id)
            ->value('id');
        if(!empty($alipayId)) return $alipayId;
        return '';
    }
}
