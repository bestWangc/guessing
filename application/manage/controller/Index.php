<?php

namespace app\manage\controller;

use app\manage\model\User;
use app\manage\model\UserRole;

class Index extends Base
{

    public function index(){
        $user_role = session('user_role');
        $role_name = $this->getUserRole($user_role);
        $this->assign([
            'uname' => session('user_name'),
            'roleName' => $role_name,
            'urole' => $user_role
        ]);
        return $this->fetch();
    }

    public function indexMain(){
        return $this->fetch();
    }

    //获取角色权限名称
    public function getUserRole($user_role){
        $roleName = UserRole::where('id',$user_role)->field('role_name')->find();
        return $roleName['role_name'];
    }
}
