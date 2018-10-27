<?php

namespace app\manage\controller;

use app\tools\M3result;

class Recharge extends Base
{

    public function index(){
        $choseUid = input('choseUid/d',0);

        $this->assign('choseUid',$choseUid);
        return $this->fetch();
    }

    public function rechargeLog(){
        $uid = input('post.choseUid/d');
        $where = ['sr.user_id'=>$uid];

        if(!$uid){
            $where = ['su.parent_id'=>$this->uid];
        }
        $m3_result = new M3result();
        $field = 'sr.id as recharge_id,su.name,sr.amount,sr.way,sr.status,sr.created_date';

        $result = db('recharge')
            ->alias('sr')
            ->join('users su','su.id = sr.user_id')
            ->where($where)
            ->field($field)
            ->order('sr.created_date desc')
            ->select();
        if(!empty($result)){
            foreach ($result as $key => &$value){
                $value['status'] = statusTrans($value['status']);
                $value['created_date'] = date('Y-m-d H:i:s',$value['created_date']);
            }
        }
        $m3_result->code = 1;
        $m3_result->msg = 'success';
        $m3_result->data = $result;
        return json($m3_result->toArray());
    }

    //充值申请
    public function applyFor(){
        return $this->fetch();
    }

}
