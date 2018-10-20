<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;

class Register extends Base
{
    public function index(){
        $userName = input('username', "wangc");
        $userPwd = input('userpwd', "123456");
        $email = input('email', "123456");
        $parent_id = input('parent', 1);

        $m3_result = new M3result();
        if(empty($userName) || empty($userPwd) || empty($email)){
            $m3_result->status = 0;
            $m3_result->msg = '信息填写不全，请重试';
            return $m3_result->toJson();
        }

        $data = [
            'name' => $userName,
            'passwd' => md5($userPwd),
            'role' => 1,
            'parent_id' => $parent_id,
            'created_date' => time()
        ];
        $creatUser = db('users')->insert($data);

        if($creatUser){
            $m3_result->status = 1;
            $m3_result->msg = '注册成功';
            return $m3_result->toJson();
        }

        $m3_result->status = 0;
        $m3_result->msg = '注册失败，请重试';
        return $m3_result->toJson();
    }
}