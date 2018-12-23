<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $goodsInfo = controller('Goods')->getGoodsInfo();
        $this->assign('goodsInfo',$goodsInfo);
        return $this->fetch();
    }

    public function dynamic(){
        return $this->fetch();
    }

}
