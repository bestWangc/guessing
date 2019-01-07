<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Db;
// use think\Request;
use think\facade\Request;

class Order extends Base
{

    public function index(){
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
            ->field('o.id,o.goods_num,o.status,g.name as goods_name,g.price,g.success_price,g.pic_url')
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

    /*public function info(){
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->field('u.name as account,u.parent_id,u.tel,u.email,ali.alipay_account,ali.alipay_name,a.name as uname,a.phone,a.details')
            ->where('u.id',$this::$uid)
            ->find();
        $this->assign('info',$res);
        return $this->fetch();
    }*/

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
}
