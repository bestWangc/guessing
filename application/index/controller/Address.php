<?php

namespace app\index\controller;

use app\tools\M3result;

class Address extends Base
{
    public function index(){

        $addressInfo = db('address')
            ->where('user_id',session('user_id'))
            ->field('name,phone,details,postal_code')
            ->find();
        if(!empty($addressInfo)){
            $this->assign([
                'name' => $addressInfo['name'],
                'phone' => $addressInfo['phone'],
                'address' => $addressInfo['details'],
                'code' => $addressInfo['postal_code']
            ]);
        }

        return $this->fetch();
    }

    //添加地址
    public function add(){
        $name = input('reci_name','');
        $tel = input('tel','');
        $address_detail = input('address_detail','');
        $postal_code = input('postal_code','');

        $m3_result = new M3result();
        if(empty($name) || empty($tel) || empty($address_detail) || empty($postal_code)){
            $m3_result->code = 0;
            $m3_result->msg = '信息内容不能为空';
            return json($m3_result->toArray());
        }

        $data=[
            'user_id' => $this->uid,
            'name' => $name,
            'phone' => $tel,
            'details' => $address_detail,
            'postal_code' => $postal_code
        ];

        $oldInfo = db('address')->where('user_id',$this->uid)->find();
        if(empty($oldInfo)){
            $res = db('address')
                ->insert($data);
        }else{
            $res = db('address')
                ->where('user_id', $this->uid)
                ->update($data);
        }
        if($res){
            $m3_result->code = 1;
            $m3_result->msg = '保存成功';
            return json($m3_result->toArray());
        }

        $m3_result->code = 0;
        $m3_result->msg = '保存失败，请重试';
        return json($m3_result->toArray());
    }
}
