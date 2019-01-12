<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Request;
use think\facade\Session;

class Goods extends Controller
{
    public function index(Request $request)
    {
        $goodsId = $request::param('id',1);
        $goodsInfo = [];
        if(!empty($goodsId)){
            $goodsInfo = $this->getGoodsInfo($goodsId);
            $goodsInfo = $goodsInfo[0];
        }
        $this->assign('goodsInfo',$goodsInfo);
        return $this->fetch();
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
