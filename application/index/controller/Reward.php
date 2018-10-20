<?php

namespace app\index\controller;


class Reward extends Base
{
    public function index()
    {
        $user_id = session('user_id');

        $userInfo = controller('user')->getUserInfo($user_id);
        $award = $this->getAwardInfo(30);

        $this->assign([
            'name' => $userInfo['name'],
            'gold' => $userInfo['gold']-$userInfo['frozen_gold'],
            'user_photo' => $userInfo['photo'],
            'lastTermNum' => $award[0]['term_num'],
            'award' => $award

        ]);
        return $this->fetch();
    }

    //获取开奖信息
    public function getAwardInfo($limit){
        $award = db('award_info')
            ->field('id,term_num,result,created_date,win')
            ->order('id desc')
            ->limit($limit)
            ->select();
        return $award;
    }
}
