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
        $userName = session('user_name');
        $uid = session('user_id');

        $field = 'su.created_date,su.email,sa.alipay_name,alipay_account,sad.`name`,sad.phone,sad.details';
        $info = db('users')
            ->alias('su')
            ->join('alipay sa','sa.user_id = su.id','left')
            ->join('address sad','sad.user_id = su.id','left')
            ->where('su.id',$uid)
            ->field($field)
            ->find();
        //团队成员数量
        $count = $this->getCountForTeam($uid);
        list($teamAllNum,$teamNewNum,$teamActiveNum) = $count;

        $this->assign([
            'userName' => $userName,
            'createdDate' => $info['created_date'] ?? '未设置',
            'email' => $info['email'] ?? '未设置',
            'alipayName' => $info['alipay_name'] ?? '未设置',
            'alipayAccount' => $info['alipay_account'] ?? '未设置',
            'name' => $info['name'] ?? '未设置',
            'phone' => $info['phone'] ?? '未设置',
            'details' => $info['details'] ?? '未设置',
            'teamAllNum' => $teamAllNum,
            'teamNewNum' => $teamNewNum,
            'teamActiveNum' => $teamActiveNum,
        ]);
        return $this->fetch();
    }

    //获取角色权限名称
    public function getUserRole($user_role){
        $roleName = UserRole::where('id',$user_role)->field('role_name')->find();
        return $roleName['role_name'];
    }

    //计算团队成员数量
    public function getCountForTeam($uid){
        //今日凌晨时间戳
        $time = strtotime(date('Ymd'));
        //今日新增成员
        $sql1 = 'SELECT count(id) FROM ssc_users WHERE parent_id = '.$uid.' AND created_date > '.$time;
        //今日活跃成员
        $sql2 = 'SELECT count(su.id) AS count3 FROM ssc_users su
                INNER JOIN ssc_order so ON so.user_id = su.id AND so.created_date > '.$time.'
                WHERE su.parent_id = '.$uid;
        $result = db('users')
            ->field('count(id) as count')
            ->where('parent_id',$uid)
            ->unionAll($sql1)
            ->unionAll($sql2)
            ->select();
        $result = array_column($result,'count');
        return $result;

    }
}
