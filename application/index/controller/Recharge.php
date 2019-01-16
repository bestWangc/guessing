<?php
namespace app\index\controller;

use think\facade\Request;
use app\service\controller\Recharge as sRecharge;

class Recharge extends Base
{
    public function index(){
        return $this->fetch();
    }

    public function createRecharge(Request $request)
    {
        $rMoney = $request::post('recharge_money/d',0);
        $rWay = $request::post('recharge_way/d');

        $res = sRecharge::createRecharge($this::$uid,$rMoney,$rWay);
        return $res;
    }
}
