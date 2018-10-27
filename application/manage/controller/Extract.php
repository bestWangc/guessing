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
                switch ($value['status']){
                    case 0:
                        $value['status'] = '失败';
                        break;
                    case 1:
                        $value['status'] = '成功';
                        break;
                    case 2:
                        $value['status'] = '待处理';
                        break;
                }
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
}
