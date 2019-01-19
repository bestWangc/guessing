<?php
namespace app\mapp\controller;

use think\Db;

class Rankings extends Base
{
    public function index()
    {
        $day = strtotime(date("Y-m-d"),time());
        $dayRank = $this->getRankings($day);

        $week = strtotime("-1 week");
        $weekRank = $this->getRankings($week);

        $month = strtotime("-1 month");
        $monthRank = $this->getRankings($month);
        $this->assign([
            'dayRank' => $dayRank,
            'weekRank' => $weekRank,
            'monthRank' => $monthRank,

        ]);
        return $this->fetch();
    }

    //获取日订单，周订单，月订单最多的人信息
    public function getRankings($time)
    {
        $data = Db::name('order')
            ->alias('o')
            ->leftJoin('users u','u.id = o.user_id')
            ->where('o.created_date','>', $time)
            ->field('u.name,u.photo, sum(o.goods_num) as goods_num, sum(o.amount) as amounts')
            ->group('o.user_id')
            ->order('amounts desc')
            ->limit(15)
            ->select();
        return $data;
    }
}
