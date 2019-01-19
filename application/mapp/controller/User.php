<?php

namespace app\mapp\controller;

use think\facade\Request;
use think\Db;
use think\Exception;

class User extends Base
{
    public function index()
    {
        $userInfo = $this->getUserInfo();

        $adressNum = Db::name('address')
            ->where('user_id', $this->uid)
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
    public function getUserInfo()
    {
        $userInfo = Db::name('users')
            ->where('id', $this->uid)
            ->field('name,gold,photo,money,tel,email,frozen_money,frozen_gold')
            ->find();
        return $userInfo;
    }

    //支付宝信息
    public function alipay()
    {
        $alipayInfo = Db::name('alipay')
            ->where('user_id',$this->uid)
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

        if(empty($alipay_account) || empty($alipay_name)){
            return jsonRes(1,'信息内容不能为空');
        }
        if(is_null($alipayPic)){
            $alipayPic = $request::post('alipayPic');
            if(empty($alipayPic)){
                return jsonRes(1,'请选择支付宝收款二维码');
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
            return jsonRes(0,'保存成功');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return jsonRes(1,'错误，请重试');
        }
    }

    //添加手机号
    public function addTel(Request $request)
    {
        $tel_num = $request::param('tel_num','');

        if(empty($tel_num)){
            return jsonRes(1,'手机号不能为空');
        }
        $pattern = '/^1[34578]\d{9}$/';
        if(!preg_match($pattern,$tel_num)){
            return jsonRes(1,'请填写正确的手机号');
        }

        $res = Db::name('users')
            ->where('id', $this->uid)
            ->setField('tel', $tel_num);
        if($res){
            return jsonRes(0,'保存成功');
        }

        return jsonRes(1,'没有任何修改');
    }
}
