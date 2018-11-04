<?php

namespace app\manage\controller;

use app\tools\M3result;

class Extract extends Base
{

    public function index(){
        $choseUid = input('choseUid/d',0);

        $this->assign('choseUid',$choseUid);
        return $this->fetch();
    }
    //提现记录
    public function extractLog(){
        $uid = input('post.choseUid/d');
        $where = ['se.user_id'=>$uid];

        if(!$uid){
            $where = ['su.parent_id'=>$this->uid];
        }
        $m3_result = new M3result();
        $field = 'se.id as extract_id,su.name,se.amount,se.real_amount,se.way,sa.alipay_account,sa.alipay_name,se.status,se.created_date';
        $result = db('extract')
            ->alias('se')
            ->join('users su','su.id = se.user_id')
            ->join('alipay sa','sa.id = se.alipay_id')
            ->where($where)
            ->field($field)
            ->order('se.created_date desc')
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

    //提现申请
    public function applyFor(){
        return $this->fetch();
    }
    //提现申请详细信息
    public function applyDetails(){
        $m3_result = new M3result();
        $result = db('extract')
            ->alias('se')
            ->join('users su','su.id = se.user_id')
            ->join('alipay sa','sa.id = se.alipay_id')
            ->where('se.status',2)
            ->field('se.id,su.name,se.user_id,se.amount,se.real_amount,se.way,se.status,se.created_date,sa.alipay_account,sa.alipay_name')
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

    //提现申请操作
    public function operation(){
        $id = input('post.id',0);
        $operate = input('post.operate');
        $amount = input('post.amount',0);
        $user_id = input('post.user_id');
        $m3_result = new M3result();

        if(!$id || is_null($operate) || is_null($user_id)){
            $m3_result->code = 0;
            $m3_result->msg = '参数不够，请重试';
            return json($m3_result->toArray());
        }
        $data = [
            'status' => $operate,
            'updated_date' => time()
        ];
        $res = db('extract')->where('id',$id)->update($data);
        if(!!$operate){
            $result = db('users')
                ->dec('money',$amount)
                ->where('id',$user_id)
                ->update();
            if(!$result){
                $m3_result->code = 0;
                $m3_result->msg = '充值未成功';
                return json($m3_result->toArray());
            }
        }
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
