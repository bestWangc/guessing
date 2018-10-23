<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;

class ChangePwd extends Base
{
    public function index(){
        $uid = input('uid', 0);
        $uname = input('uname', "");
        $oldPwd = input('oldPwd', "");
        $newPwd = input('newPwd', "");
        $reNewPwd = input('reNewPwd', "");

        $m3_result = new M3result();
        if(!$uid || empty($uname) || empty($oldPwd) || empty($newPwd) || empty($reNewPwd)){
            $m3_result->status = 0;
            $m3_result->msg = '信息填写不全，请重试';
            return json($m3_result->toArray());
        }
        if($newPwd !== $reNewPwd){
            $m3_result->status = 0;
            $m3_result->msg = '两次密码输入不一致，请重试';
            return json($m3_result->toArray());
        }
        if(strlen($newPwd) < 6){
            $m3_result->status = 0;
            $m3_result->msg = '密码长度不能少于6位';
            return json($m3_result->toArray());
        }

        $newPwd = md5($newPwd.'jfn');

        $userArr = db('users')
            ->where('id',$uid)
            ->where('status',1)
            ->field('passwd')
            ->find();
        if(!empty($userArr)){
            if(md5($oldPwd.'jfn') == $userArr['passwd']){
                $res = db('user')->where('id',$uid)->setField('passwd', $newPwd);
                if(!$res){
                    $m3_result->status = 1;
                    $m3_result->msg = '修改成功';
                    return json($m3_result->toArray());
                }
            }
        }
        $m3_result->status = 0;
        $m3_result->msg = '修改失败，请重试';
        return json($m3_result->toArray());
    }
}