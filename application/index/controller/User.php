<?php

namespace app\index\controller;

use think\Db;
use think\Exception;
use think\facade\Request;

class User extends Base
{
    public function index(Request $request)
    {
        $userInfo = $this->getSimpleInfo();
        $orderInfo = Order::getList($request);
        $orderInfo = $orderInfo->getData();
        $orderListInfo = [];
        if($orderInfo['code'] == 0){
            $orderListInfo = $orderInfo['data'];
        }
        $link = $request::server('HTTP_HOST');
        $link .= '/register';
        $this->assign([
            'userInfo' => $userInfo,
            'orderInfo' => $orderListInfo,
            'link' => $link
        ]);
        return $this->fetch();
    }

    //获取简单用户信息
    public function getSimpleInfo()
    {
        $fieldList = 'u.id,u.name as account,u.parent_id,u.money-u.frozen_money as money,u.gold-u.frozen_gold as gold,u.tel,u.email,ali.alipay_account,ali.alipay_name,ali.alipay_pic,ul.role_name,a.name as express_name,a.phone,a.details';
        $res = Db::name('users')
            ->alias('u')
            ->join('address a','a.user_id = u.id','left')
            ->join('alipay ali','ali.user_id = u.id','left')
            ->join('user_role ul','ul.id = u.role','left')
            ->field($fieldList)
            ->where('u.id',$this::$uid)
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
        $tel = $request::post('tel');
        $pattern = '/^1[34578]\d{9}$/';
        if(!preg_match($pattern,$tel)){
            return jsonRes(1,'请填写正确的手机号');
        }
        $email = $request::post('email');
        if(empty($email) || !strpos($email,'@')){
            return jsonRes(1,'请填写正确的邮箱');
        }

        $alipayAccount = $request::post('alipayAccount');
        $alipayName = $request::post('alipayName');
        $expressName = $request::post('expressName');
        $expressPhone = $request::post('expressPhone');
        $expressAddress = $request::post('expressAddress');
        $alipayPic = $request::file('alipayPic');

        if(empty($alipayAccount) || empty($alipayName)){
            return jsonRes(1,'请填写支付宝收款信息');
        }

        if(is_null($alipayPic)){
            $alipayPic = $request::post('alipayPic');
            if(empty($alipayPic)){
                return jsonRes(1,'请选择支付宝收款二维码');
            }
        }

        if(empty($expressAddress) || empty($expressName)){
            return jsonRes(1,'请填写收货人信息');
        }
        if(!preg_match($pattern,$expressPhone)){
            return jsonRes(1,'请填写正确的收货人手机号');
        }
        Db::startTrans();
        try {
            $data = [
                'tel' => $tel,
                'email' => $email
            ];
            Db::name('users')->where('id',$this::$uid)->update($data);

            if(gettype($alipayPic) != 'string'){
                $upload = uploadPic($alipayPic,$this::$uid.'alipay');
            }else{
                $upload = $alipayPic;
            }

            if(!empty($upload)){
                $data = [
                    'alipay_name' => $alipayName,
                    'alipay_account' => $alipayAccount,
                    'alipay_pic' => $upload
                ];
                $alipayId = Extract::checkAlipay($this::$uid);
                if(empty($alipayId)){
                    $data['user_id'] = $this::$uid;
                    Db::name('alipay')->insert($data);
                } else {
                    Db::name('alipay')->where('user_id',$this::$uid)->update($data);
                }
            }else{
                throw new Exception('error');
            }

            $data = [
                'name' => $expressName,
                'phone' => $expressPhone,
                'details' => $expressAddress
            ];
            $addressId = Address::checkAddress($this::$uid);
            if(empty($addressId)){
                $data['user_id'] = $this::$uid;
                Db::name('address')->insert($data);
            } else {
                Db::name('address')->where('user_id',$this::$uid)->update($data);
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

}
