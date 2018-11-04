<?php

namespace app\manage\controller;

use app\tools\M3result;

class Apply extends Base
{
    //申请详情
    public function goldApply(){
        return $this->fetch();
    }

    //退货申请
    public function backGoodsApply(){
        return $this->fetch();
    }

    //退货申请
    public function takeGoodsApply(){
        return $this->fetch();
    }


    //金币兑换申请详细信息
    public function goldApplyDetails(){
        $purpose = input('post.purpose');

        $m3_result = new M3result();
        $result = db('apply')
            ->alias('sa')
            ->join('users su','su.id = sa.user_id')
            ->where('sa.status',2)
            ->where('sa.purpose',3)
            ->field('sa.id,su.name,sa.gold,sa.status,sa.created_date')
            ->order('sa.created_date asc')
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

    //申请详细信息
    public function applyGoodsDetails(){
        $purpose = input('post.purpose');

        $field = 'sa.id,su.name,so.id as order_id,sg.name as goods_name,so.goods_num,so.amount,so.guessing,sai.term_num,sai.result,sa.status,sa.created_date';
        $m3_result = new M3result();
        $result = db('apply')
            ->alias('sa')
            ->join('users su','su.id = sa.user_id')
            ->join('order so','so.id = sa.order_id')
            ->join('goods sg','sg.id = so.goods_id')
            ->join('award_info sai','sai.id = so.award_id')
            ->where('sa.status',2)
            ->where('sa.purpose',$purpose)
            ->field($field)
            ->order('sa.created_date asc')
            ->select();
        // return $result;
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

    //操作
    public function operation(){
        $id = input('post.id',0);
        $operate = input('post.operate');
        $m3_result = new M3result();

        if(!$id || is_null($operate)){
            $m3_result->code = 0;
            $m3_result->msg = 'id 不存在，请重试';
            return json($m3_result->toArray());
        }
        $data = [
            'status' => $operate,
            'updated_date' => time()
        ];
        $res = db('apply')->where('id',$id)->update($data);
        if($res){
            $m3_result->code = 1;
            $m3_result->msg = '成功';
            return json($m3_result->toArray());
        }
        $m3_result->code = 0;
        $m3_result->msg = '失败，请重试';
        return json($m3_result->toArray());
    }
}
