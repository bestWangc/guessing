<?php

namespace app\index\controller;

use think\Db;
// use think\Request;
use think\facade\Request;

class Order extends Base
{

    public function index(){
        return $this->fetch();
    }

    public function info(){
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->field('u.name as account,u.parent_id,u.tel,u.email,ali.alipay_account,ali.alipay_name,a.name as uname,a.phone,a.details')
            ->where('u.id',$this->uid)
            ->find();
        $this->assign('info',$res);
        return $this->fetch();
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
}
