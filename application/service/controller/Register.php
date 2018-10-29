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
        $userName = input('username', "");
        $userPwd = input('userpwd', "");
        $email = input('email', "");
        $parent_id = input('parent', 1);

        $m3_result = new M3result();
        if(empty($userName) || empty($userPwd) || empty($email)){
            $m3_result->code = 0;
            $m3_result->msg = '信息填写不全，请重试';
            return json($m3_result->toArray());
        }
        $oldUserInfo = db('users')
            ->where('name',$userName)
            ->count('id');

        if(!!$oldUserInfo){
            $m3_result->code = 0;
            $m3_result->msg = '帐号已存在，请重试';
            return json($m3_result->toArray());
        }

        $data = [
            'name' => $userName,
            'passwd' => md5($userPwd.'jfn'),
            'role' => 0,
            'photo' => '/uploads/photo_1.jpg',
            'parent_id' => $parent_id,
            'created_date' => time()
        ];
        $creatUser = db('users')->insert($data);

        if($creatUser){
            $m3_result->code = 1;
            $m3_result->msg = '注册成功';
            return json($m3_result->toArray());
        }

        $m3_result->code = 0;
        $m3_result->msg = '注册失败，请重试';
        return json($m3_result->toArray());
    }
}