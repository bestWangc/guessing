<?php
namespace app\mapp\controller;

class Index extends Base
{
    public function index()
    {
        $userInfo = controller('user')->getUserInfo($this->uid);

        //获取开奖信息
        $award = controller('reward')->getAwardInfo(10);

        $resultAttr = [];
        $result = $award[1]['result'];
        $resultLength = strlen($result);
        for($i=0;$i<$resultLength;$i++){
            array_push($resultAttr,$result{$i});
        }

        //获取商品信息
        $goodsInfo = db('goods')
            ->field('id,name,price,success_price,pic_url')
            ->select();

        //最近的购买
        $orderInfo = db('order')
            ->alias('o')
            ->leftJoin('goods g','g.id = o.goods_id')
            ->where('user_id', $this->uid)
            ->field('o.goods_num,o.amount,o.created_date,g.name as goods_name')
            ->order('o.id desc')
            ->limit(5)
            ->select();

        $this->assign([
            'name' => $userInfo['name'],
            'gold' => $userInfo['gold']-$userInfo['frozen_gold'],
            'user_photo' => $userInfo['photo'],
            'lastTermNum' => $award[1]['term_num'],
            'nowTermNum' => $award[0]['term_num'],
            'result' => $resultAttr,
            'goodsInfo' => $goodsInfo,
            'orderInfo' => $orderInfo,
            'award' => $award

        ]);
        return $this->fetch();
    }

}
