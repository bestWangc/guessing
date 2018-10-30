<?php

namespace app\manage\controller;

use app\tools\M3result;

class Apply extends Base
{
    //申请详情
    public function goldApply(){
        return $this->fetch();
    }


    //申请详细信息
    public function applyDetails(){
        $purpose = input('post.purpose');

        $m3_result = new M3result();
        $result = db('apply')
            ->alias('sa')
            ->join('users su','su.id = sa.user_id')
            ->join('alipay sa','sa.id = se.alipay_id')
            ->where('se.status',2)
            ->field('se.id,su.name,se.amount,se.real_amount,se.way,se.status,se.created_date,sa.alipay_account,sa.alipay_name')
            ->order('se.created_date asc')
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
}
