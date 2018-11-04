<?php

namespace app\manage\controller;


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
        return jsonRes(1,'成功',$result);
    }

    //提现申请
    public function applyFor(){
        return $this->fetch();
    }
    //提现申请详细信息
    public function applyDetails(){
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
        return jsonRes(1,'成功',$result);
    }

    //提现申请操作
    public function operation(){
        $id = input('post.id',0);
        $operate = input('post.operate');
        $amount = input('post.amount',0);
        $user_id = input('post.user_id');

        if(!$id || is_null($operate) || is_null($user_id)){
            return jsonRes(0,'参数不够，请重试');
        }
        $data = [
            'status' => $operate,
            'updated_date' => time()
        ];

        if(!!$operate){
            $result = db('users')
                ->dec('money',$amount)
                ->dec('frozen_money',$amount)
                ->where('id',$user_id)
                ->update();
            if(!$result){
                return jsonRes(0,'充值未成功');
            }
        }
        $res = db('extract')->where('id',$id)->update($data);
        if($res){
            return jsonRes(1,'成功');
        }
        return jsonRes(0,'失败，请重试');
    }
}
