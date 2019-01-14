<?php

namespace app\index\controller;

use app\tools\M3result;
use think\Db;
use think\Exception;
use think\facade\Log;
use think\facade\Session;
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
        $this->assign([
            'userInfo' => $userInfo,
            'orderInfo' => $orderListInfo
        ]);
        return $this->fetch();
    }

    //获取简单用户信息
    public function getSimpleInfo()
    {
        $fieldList = 'u.name as account,u.parent_id,u.money-u.frozen_money as money,u.gold-u.frozen_gold as gold,u.tel,u.email,ali.alipay_account,ali.alipay_name,ali.alipay_pic,ul.role_name,a.name as express_name,a.phone,a.details';
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
        $alipayPic = $request::file('alipayPic');

        if(empty($alipayAccount) || empty($alipayName)){
            $m3_result->code = 0;
            $m3_result->msg = '请填写支付宝收款信息';
            return json($m3_result->toArray());
        }

        if(is_null($alipayPic)){
            $alipayPic = $request::post('alipayPic');
            if(empty($alipayPic)){
                $m3_result->code = 0;
                $m3_result->msg = '请上支付宝收款二维码';
                return json($m3_result->toArray());
            }
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
            Db::name('users')->where('id',$this::$uid)->update($data);

            if(gettype($alipayPic) != 'string'){
                $upload = $this->uploadPic($alipayPic,$this::$uid.'alipay');
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

    public function uploadPic($file,$name)
    {
        $fileName = '/uploads/alipay/';
        $name = md5($name);
        $info = $file->validate(['ext'=>'jpg,png'])->move('./uploads/alipay',$name);
        if($info){
            $fileName .= $info->getSaveName();
            return $fileName;
        }

        return '';
    }
}
