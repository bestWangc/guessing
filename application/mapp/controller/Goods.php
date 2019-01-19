<?php
namespace app\mapp\controller;

use think\facade\Request;
use think\Db;

class Goods extends Base
{
    public function index(Request $request)
    {
        $goods_id = $request::param('goods_id',0);

        $goods_info = $this->getGoodsInfo($goods_id);
        // $termInfo = controller('reward')->getAwardInfo(1);

        $this->assign([
            'goods_id' => $goods_id,
            'goods_info' => $goods_info
        ]);
        return $this->fetch();
    }

    //获取商品详细信息
    public function getGoodsInfo($goods_id)
    {
        $info = Db::name('goods')
            ->where('id', $goods_id)
            ->field('id,name,price,success_price,pic_url')
            ->find();
        return $info;
    }
}
