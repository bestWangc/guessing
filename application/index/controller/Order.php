<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Db;
use think\facade\Request;

class Order extends Base
{

    public function index()
    {
        return $this->fetch();
    }

    // 订单详情页面
    public function record()
    {
        return $this->fetch();
    }

    //获取订单记录
    public static function getList(Request $request)
    {
        $page = $request::param('page',1);
        $limit = $request::param('limit',10);

        $m3_result = new M3result();
        $orders = Db::name('order')
            ->alias('o')
            ->join('goods g','g.id = o.goods_id')
            ->join('award_info ai','ai.id = o.award_id')
            ->where('user_id',parent::$uid)
            ->field('o.id,o.goods_num,o.status,o.amount,o.goods_id,o.guessing,FROM_UNIXTIME(o.created_date) AS created_date,g.name as goods_name,g.price,g.success_price,g.pic_url,ai.win,ai.term_num')
            ->order('o.created_date desc')
            ->paginate($limit,false,[
                'page' => $page
            ]);
        $total = $orders->total();
        $orders = $orders->getCollection()->all();
        $m3_result->code = 0;
        $m3_result->count = $total;
        $m3_result->data = $orders;
        $m3_result->msg = 'success';
        return json($m3_result->toLayArray());
    }

    //订单排行
    public function rank(Request $request){
        $type = $request::param('type');

        $rankData = [];
        switch ($type){
            case '3':
                $title = '月榜';
                $month = strtotime("-1 month");
                $month = strtotime(date("Y-m-d",$month));
                $rankData = $this->getRankings($month);
                break;
            case '2':
                $title = '周榜';
                $week = strtotime("-1 week");
                $week = strtotime(date("Y-m-d",$week));
                $rankData = $this->getRankings($week);
                break;
            default:
                $title= '日榜';
                $day = strtotime(date("Y-m-d"),time());
                $rankData = $this->getRankings($day);
        }

        $this->assign([
            'type' => $type,
            'title' => $title,
            'rankData' => $rankData
        ]);
        return $this->fetch();
    }

    /**
     * @param $time
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRankings($time){
        $data = Db::name('order')
            ->alias('o')
            ->leftJoin('users u','u.id = o.user_id')
            ->where('o.created_date','>', $time)
            ->field('u.name,u.photo, count(o.id) as orderNum, sum(o.goods_num) as goods_num, sum(o.amount) as amounts')
            ->group('o.user_id')
            ->order('amounts desc')
            ->limit(15)
            ->select();
        return $data;
    }

    //提货、退货
    public function takeGoods()
    {
        $m3_result = new M3result();
        $order_id = input('order_id','');
        $purpose = input('purpose',0);
        if(empty($order_id)){
            $m3_result->code = 0;
            $m3_result->msg = '订单编号不能为空';
            return json($m3_result->toArray());
        }
        $applyOrderNum = db('apply')
            ->where('order_id',$order_id)
            ->count();
        if($applyOrderNum > 0){
            $m3_result->code = 0;
            $m3_result->msg = '订单请勿重复提交';
            return json($m3_result->toArray());
        }
        $addressInfo = db('address')
            ->where('user_id',$this->uid)
            ->field('id')
            ->count();
        if(!$addressInfo){
            $m3_result->code = 0;
            $m3_result->msg = '未设置收货地址';
            return json($m3_result->toArray());
        }

        if ($purpose == 2){
            //查询余额是否够10元
            $balance = db('users')
                ->where('user_id',$this->uid)
                ->field('money')
                ->find();
            if($balance['money'] < 10){
                $m3_result->code = 0;
                $m3_result->msg = '金额不足10元，无法提货';
                return json($m3_result->toArray());
            }
        }

        $data=[
            'order_id' => $order_id,
            'user_id' => $this->uid,
            'created_date' => time(),
            'purpose' => $purpose,
            'status' => 2
        ];
        $res = db('apply')
            ->insert($data);

        if($res){
            $changeOrderStatus = db('order')->where('id',$order_id)->setField('status',$purpose);
            if($changeOrderStatus){
                $m3_result->code = 1;
                $m3_result->msg = '提交成功';
                return json($m3_result->toArray());
            }
        }

        $m3_result->code = 0;
        $m3_result->msg = '未知错误，请重试';
        return json($m3_result->toArray());
    }

}
