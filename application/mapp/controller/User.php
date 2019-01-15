<?php

namespace app\mapp\controller;

use app\tools\M3result;
use think\facade\Request;
use think\Db;
use think\Exception;

class User extends Base
{
    public function index(){
        $userInfo = $this->getUserInfo();

        $adressNum = db('address')
            ->where('user_id', session('user_id'))
            ->count();

        $this->assign([
            'user_photo' => $userInfo['photo'],
            'money' => $userInfo['money']-$userInfo['frozen_money'],
            'email' => $userInfo['email'],
            'tel' => $userInfo['tel'],
            'name' => $userInfo['name'],
            'uid' => $this->uid,
            'adressNum' => $adressNum
        ]);
        return $this->fetch();
    }

    //获取user信息
    //获取用户信息
    public function getUserInfo(){
        $userInfo = db('users')
            ->where('id', $this->uid)
            ->field('name,gold,photo,money,tel,email,frozen_money,frozen_gold')
            ->find();
        return $userInfo;
    }

    //支付宝信息
    public function alipay(){
        $alipayInfo = db('alipay')
            ->where('user_id',session('user_id'))
            ->field('alipay_account,alipay_name,alipay_pic')
            ->find();

        if(!empty($alipayInfo)){
            $this->assign([
                'name' => $alipayInfo['alipay_name'],
                'account' => $alipayInfo['alipay_account'],
                'alipay_pic' => $alipayInfo['alipay_pic']
            ]);
        }
        return $this->fetch();
    }

    //添加支付宝信息
    public function alipayInfo(Request $request)
    {
        $alipay_account = $request::post('account','');
        $alipay_name = $request::post('name','');
        $alipayPic = $request::file('alipayPic');

        $m3_result = new M3result();
        if(empty($alipay_account) || empty($alipay_name)){
            $m3_result->code = 0;
            $m3_result->msg = '信息内容不能为空';
            return json($m3_result->toArray());
        }
        if(is_null($alipayPic)){
            $alipayPic = $request::post('alipayPic');
            if(empty($alipayPic)){
                $m3_result->code = 0;
                $m3_result->msg = '请选择支付宝收款二维码';
                return json($m3_result->toArray());
            }
        }

        Db::startTrans();
        try {

            if(gettype($alipayPic) != 'string'){
                $upload = uploadPic($alipayPic,$this->uid.'alipay');
            }else{
                $upload = $alipayPic;
            }

            if(!empty($upload)){
                $data=[
                    'user_id' => $this->uid,
                    'alipay_name' => $alipay_name,
                    'alipay_account' => $alipay_account,
                    'alipay_pic' => $upload
                ];
                $alipayId = Extract::checkAlipay($this->uid);
                if(empty($alipayId)){
                    $data['user_id'] = $this->uid;
                    Db::name('alipay')->insert($data);
                } else {
                    Db::name('alipay')->where('user_id',$this->uid)->update($data);
                }
            }else{
                throw new Exception('图片格式不正确');
            }
            unset($data);
            // 提交事务
            Db::commit();
            $m3_result->code = 1;
            $m3_result->msg = '保存成功';
            return json($m3_result->toArray());
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $m3_result->code = 0;
            $m3_result->msg = '错误，请重试';
            return json($m3_result->toArray());
        }
    }

    //添加手机号
    public function addTel(){
        $tel_num = input('tel_num','');
        $m3_result = new M3result();
        if(empty($tel_num)){
            $m3_result->code = 0;
            $m3_result->msg = '手机号不能为空';
            return json($m3_result->toArray());
        }
        $res = db('users')
            ->where('id', $this->uid)
            ->setField('tel', $tel_num);
        if($res){
            $m3_result->code = 1;
            $m3_result->msg = '保存成功';
            return json($m3_result->toArray());
        }
        $m3_result->code = 0;
        $m3_result->msg = '没有任何修改';
        return json($m3_result->toArray());
    }

}
