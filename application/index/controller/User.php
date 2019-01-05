<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Db;
use think\facade\Log;
use think\facade\Session;
use think\facade\Request;

class User extends Base
{
    public function index()
    {
        $userInfo = $this->getSimpleInfo();
        $this->assign([
            'uname' => $userInfo['account'],
            'rname' => $userInfo['role_name'],
            'money' => $userInfo['money'],
            'gold' => $userInfo['gold'],
            'phone' => $userInfo['tel'],
            'alipayAccount' => $userInfo['alipay_account'],
            'alipayName' => $userInfo['alipay_name'],
            'email' => $userInfo['email'],
            'expressName' => $userInfo['express_name'],
            'expressPhone' => $userInfo['phone'],
            'expressDetails' => $userInfo['details']
        ]);
        return $this->fetch();
    }

    //获取简单用户信息
    public function getSimpleInfo()
    {
        $fieldList = 'u.name as account,u.money-u.frozen_money as money,u.gold-u.frozen_gold as gold,u.tel,u.email,ali.alipay_account,ali.alipay_name,ul.role_name,a.name as express_name,a.phone,a.details';
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->join('user_role ul','ul.id = u.role','left')
            ->field($fieldList)
            ->where('u.id',$this->uid)
            ->find();
        return $res;
    }

    //获取剩余金币或者金额
    public static function getSurplus($field,$uid)
    {
        $num = Db::name('users')
            ->where('id',$uid)
            ->value($field);
        return $num;
    }

    // 保存用户信息
    public function saveUserInfo(Request $request)
    {
        $m3_result = new M3result();
        $tel = $request::post('tel');
        $pattern = '/^1[34578]\d{9}$/';
        if(!preg_match($pattern,$tel)){
            $m3_result->code = 0;
            $m3_result->msg = '请填写正确的手机号';
            return json($m3_result->toArray());
        }
        $email = $request::post('email');
        if(empty($email) || !strpos($email,'@')){
            $m3_result->code = 0;
            $m3_result->msg = '请填写正确的邮箱';
            return json($m3_result->toArray());
        }
        $alipayAccount = $request::post('alipayAccount');
        $alipayName = $request::post('alipayName');
        $expressName = $request::post('expressName');
        $expressPhone = $request::post('expressPhone');
        $expressAddress = $request::post('expressAddress');
        if(empty($alipayAccount) || empty($alipayName)){
            $m3_result->code = 0;
            $m3_result->msg = '请填写支付宝收款信息';
            return json($m3_result->toArray());
        }

        if(empty($expressAddress) || empty($expressName)){
            $m3_result->code = 0;
            $m3_result->msg = '请填写收货人信息';
            return json($m3_result->toArray());
        }
        if(!preg_match($pattern,$expressPhone)){
            $m3_result->code = 0;
            $m3_result->msg = '请填写正确的收货人手机号';
            return json($m3_result->toArray());
        }

        Db::startTrans();
        try {
            $data = [
                'tel' => $tel,
                'email' => $email
            ];
            Db::name('users')->where('id',$this->uid)->update($data);

            $data = [
                'alipay_name' => $alipayName,
                'alipay_account' => $alipayAccount
            ];
            $alipayId = Extract::checkAlipay($this->uid);
            if(empty($alipayId)){
                $data['user_id'] = $this->uid;
                Db::name('alipay')->insert($data);
            } else {
                Db::name('alipay')->where('user_id',$this->uid)->update($data);
            }

            $data = [
                'name' => $expressName,
                'phone' => $expressPhone,
                'details' => $expressAddress
            ];
            $addressId = Address::checkAddress($this->uid);
            if(empty($addressId)){
                $data['user_id'] = $this->uid;
                Db::name('address')->insert($data);
            } else {
                Db::name('address')->where('user_id',$this->uid)->update($data);
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
}
