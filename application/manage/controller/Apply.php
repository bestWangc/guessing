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
