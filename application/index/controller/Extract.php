<?php
namespace app\index\controller;

use app\tools\Other;
use think\facade\Request;
use think\Db;

class Extract extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    //提现记录
    public function record(Request $request)
    {
        $type = $request::param('type');
        $this->assign('type',$type);
        return $this->fetch();
    }

    //余额提现
    public function doMoneyExtract(Request $request)
    {
        $ext_money = $request::post('ext_money/d',0);
        $real_money = (int)bcmul($ext_money, '0.9');

        $errorNum = Other::checkText($ext_money);
        if($errorNum > 0){
            return jsonRes(1,'请输入正确的提现金额');
        }

        //检车是否绑定支付宝
        $alipayId = self::checkAlipay($this::$uid);
        if(empty($alipayId)){
            return jsonRes(1,'未绑定支付宝');
        }

        //检查余额是否足够
        $userAmount = User::getSurplus('money-frozen_money as money',$this::$uid);
        if($userAmount < $ext_money){
            return jsonRes(1,'余额不足');
        }

        $data = [
            'user_id' => $this::$uid,
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
                ->where('id',$this::$uid)
                ->setInc('frozen_money',$ext_money);
            if($updateAmount){
                return jsonRes(0,'提交成功');
            }
        }

        return jsonRes(1,'提交失败');
    }

    // 金币提现
    public function doGoldExtract(Request $request)
    {
        $ext_gold = $request::post('ext_gold/d',0);

        //验证格式
        $errorNum = Other::checkText($ext_gold);
        if($errorNum > 0){
            return jsonRes(1,'兑换金币数量不正确');
        }

        $alipayId = $this->checkAlipay($this::$uid);
        if(!$alipayId){
            return jsonRes(1,'未绑定支付宝');
        }

        //检查余额是否足够
        $userAmount = User::getSurplus('gold-frozen_gold as gold',$this::$uid);
        if($userAmount < $ext_gold){
            return jsonRes(1,'金币不足');
        }

        $data=[
            'user_id' => $this::$uid,
            'created_date' => time(),
            'purpose' => 3,
            'status' => 2,
            'gold' => $ext_gold
        ];

        $res = Db::name('apply')->insert($data);
        if($res){
            $updateGold = Db::name('users')
                ->where('id',$this::$uid)
                ->setInc('frozen_gold',$ext_gold);
            if($updateGold){
                return jsonRes(0,'提交成功');
            }
        }

        return jsonRes(1,'兑换失败');
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

    //余额提现记录数据
    public function moneyRecordData(Request $request)
    {
        $page = $request::get('page',1);
        $limit = $request::get('limit',15);

        $res = Db::name('extract')
            ->where('user_id',$this::$uid)
            ->field('id,amount,way,status,created_date,refuse_reason')
            ->order('created_date desc')
            ->paginate($limit,false,[
                'page' => $page
            ]);

        $total = $res->total();

        $res = $res->all();
        if(!empty($res)){
            foreach ($res as $key => &$val) {
                $val['created_date'] = date('Y-m-d H:i:s',$val['created_date']);
                switch ($val['status']){
                    case 0:
                        $val['status'] = '失败';
                        break;
                    case 1:
                        $val['status'] = '成功';
                        break;
                    default:
                        $val['status'] = '待处理';
                }
            }
        }

        return jsonLayRes(0,'success',$total,$res);
    }

    //金币提现记录数据
    public function goldRecordData(Request $request)
    {
        $page = $request::get('page',1);
        $limit = $request::get('limit',15);
        $res = Db::name('apply')
            ->where('user_id',$this::$uid)
            ->where('purpose',3)
            ->field('id,gold as amount,status,created_date')
            ->order('created_date desc')
            ->paginate($limit,false,[
                'page' => $page
            ]);
        $total = $res->total();

        $res = $res->all();
        if(!empty($res)){
            foreach ($res as $key => &$val) {
                $val['created_date'] = date('Y-m-d H:i:s',$val['created_date']);
                switch ($val['status']){
                    case 0:
                        $val['status'] = '失败';
                        break;
                    case 1:
                        $val['status'] = '成功';
                        break;
                    default:
                        $val['status'] = '待处理';
                }
                $val['way'] = '支付宝';
            }
        }

        return jsonLayRes(0,'success',$total,$res);
    }
}
