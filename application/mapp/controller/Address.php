<?php
namespace app\mapp\controller;

use think\facade\Request;
use think\Db;

class Address extends Base
{
    public function index()
    {

        $addressInfo = Db::name('address')
            ->where('user_id',session('user_id'))
            ->field('name,phone,details')
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
    public function add(Request $request)
    {
        $name = $request::param('reci_name','');
        $tel = $request::param('tel','');
        $address_detail = $request::param('address_detail','');
        $postal_code = $request::param('postal_code','');

        if(empty($name) || empty($tel) || empty($address_detail) || empty($postal_code)){
            return jsonRes(1,'信息内容不能为空');
        }

        $data=[
            'user_id' => $this->uid,
            'name' => $name,
            'phone' => $tel,
            'details' => $address_detail,
            'postal_code' => $postal_code
        ];

        $oldInfo = Db::name('address')->where('user_id',$this->uid)->find();
        if(empty($oldInfo)){
            $res = Db::name('address')
                ->insert($data);
        }else{
            $res = Db::name('address')
                ->where('user_id', $this->uid)
                ->update($data);
        }
        if($res){
            return jsonRes(0,'保存成功');
        }

        return jsonRes(1,'保存失败，请重试');
    }
}
