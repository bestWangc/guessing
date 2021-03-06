<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;

class Goods extends Controller
{
    public function index()
    {
        $goodsInfo = $this->getGoodsInfo();
        $this->assign('goodsInfo',$goodsInfo);
        return $this->fetch();
    }

    //单页商品详情
    public function singleProd(Request $request)
    {
        $goodsId = $request::param('id',1);
        $goodsInfo = [];
        if(!empty($goodsId)){
            $goodsInfo = $this->getGoodsInfo($goodsId);
            $goodsInfo = $goodsInfo[0];
        }
        $this->assign('goodsInfo',$goodsInfo);
        return $this->fetch('single');
    }

    //获取商品信息
    public function getGoodsInfo($id = null){
        if(empty($id)){
            $where = '';
        }else{
            $where = [
                'id' => $id
            ];
        }
        $res = Db::name('goods')
            ->where($where)
            ->field('id,name,price,success_price,pic_url,success_price-price as dprice')
            ->select();
        return $res;
    }
}
