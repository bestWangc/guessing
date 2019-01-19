<?php
namespace app\mapp\controller;

use think\Db;

class Order extends Base
{
    public function index()
    {

        $orderInfo = $this->getOrderInfo($this->uid);

        $this->assign([
            'orderInfo' => $orderInfo
        ]);
        return $this->fetch();
    }

    //获取当前用户order信息
    public function getOrderInfo($user_id)
    {
        $orderInfo = Db::name('order')
            ->alias('o')
            ->join('goods g','g.id = o.goods_id','left')
            ->join('award_info ai','ai.id = o.award_id','left')
            ->where('user_id',$user_id)
            ->field('o.id,o.goods_num,o.amount,o.guessing,o.status,o.created_date,o.refuse_reason,g.name,g.price,g.pic_url,g.success_price,ai.win,ai.term_num')
            ->order('o.created_date desc')
            ->select();
        return $orderInfo;
    }

    //提现记录
    public function record(){
        return $this->fetch();
    }

}
