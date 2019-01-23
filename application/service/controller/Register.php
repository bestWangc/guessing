<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;
use think\facade\Request;
use think\Db;

class Register extends Base
{
    public function index(Request $request)
    {
        $userName = $request::param('username', "");
        $userPwd = $request::param('userpwd', "");
        $email = $request::param('email', "");
        $parent_id = $request::param('parent', 1);

        if(empty($userName) || empty($userPwd) || empty($email)){
            return jsonRes(1,'信息填写不全，请重试');
        }
        $oldUserInfo = Db::name('users')
            ->where('name',$userName)
            ->count('id');

        if(!!$oldUserInfo){
            return jsonRes(1,'帐号已存在，请重新填写');
        }

        $data = [
            'name' => $userName,
            'passwd' => md5($userPwd.'jfn'),
            'role' => 0,
            'email' => $email,
            'photo' => '/uploads/photo_1.jpg',
            'parent_id' => $parent_id ? $parent_id : 1,
            'created_date' => time()
        ];
        $creatUser = Db::name('users')->insert($data);

        if($creatUser){
            return jsonRes(0,'注册成功');
        }

        return jsonRes(1,'注册失败，请重试');
    }
}