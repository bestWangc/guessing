<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;


class Login extends Base
{
    public function index(){
        $userName = input('username', "");
        $userPwd = input('userpwd', "");

        $m3_result = new M3result();
        if(empty($userName) || empty($userPwd)){
            $m3_result->code = 0;
            $m3_result->msg = '用户名或者密码不能为空';
            return json($m3_result->toArray());
        }
        $userArr = db('users')
            ->where('name',$userName)
            ->whereOr('email',$userName)
            ->field('id,name,passwd,role,status')
            ->find();

        if(empty($userArr['id'])){
            $m3_result->code = 0;
            $m3_result->msg = '用户名不存在';
            return json($m3_result->toArray());
        }
        if(!$userArr['status']){
            $m3_result->code = 0;
            $m3_result->msg = '用户未激活';
            return json($m3_result->toArray());
        }
        if($userPwd = md5($userPwd.'jfn') == $userArr['passwd']){
            //将user id 存入session中
            session('user_id',$userArr['id']);
            session('user_role',$userArr['role']);
            session('user_name',$userArr['name']);

            $m3_result->code = 1;
            $m3_result->msg = '登录成功';
            return json($m3_result->toArray());
        }else{
            $m3_result->code = 0;
            $m3_result->msg = '登录失败';
            return json($m3_result->toArray());
        }
    }
}