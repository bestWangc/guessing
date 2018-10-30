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

    //充值页面
    public function applyFor(){
        return $this->fetch();
    }

    //充值申请详细数据
    public function applyDetails(){
        $m3_result = new M3result();
        $result = db('recharge')
            ->alias('sr')
            ->join('users su','su.id = sr.user_id')
            ->where('sr.status',2)
            ->field('sr.id,su.name,sr.amount,sr.way,sr.status,sr.created_date')
            ->order('sr.created_date asc')
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
        $res = db('recharge')->where('id',$id)->update($data);
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
