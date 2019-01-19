<?php
namespace app\index\controller;

use think\facade\Request;
use think\Db;
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

    public function record()
    {
        return $this->fetch();
    }

    public function goldRecordData(Request $request)
    {
        $page = $request::get('page',1);
        $limit = $request::get('limit',15);

        $res = Db::name('recharge')
            ->where('user_id',$this::$uid)
            ->field('id,amount,way,status,created_date,updated_date')
            ->order('created_date desc')
            ->paginate($limit,false,[
                'page' => $page
            ]);

        $total = $res->total();

        $res = $res->all();
        if(!empty($res)){
            foreach ($res as $key => &$val) {
                $val['created_date'] = date('Y-m-d H:i:s',$val['created_date']);
                // $val['updated_date'] = date('Y-m-d H:i:s',$val['updated_date']);
                $val['updated_date'] = empty($val['updated_date']) ? '' : date('Y-m-d H:i:s',$val['updated_date']);
                switch ($val['status']){
                    case 0:
                        $val['status'] = '失败';
                        break;
                    case 1:
                        $val['status'] = '成功';
                        break;
                    case 2:
                        $val['status'] = '未付款';
                        break;
                }
                $val['way'] = $val['way'] ? '支付宝' : '微信';
            }
        }
        return jsonLayRes(0,'success',$total,$res);

    }
}
