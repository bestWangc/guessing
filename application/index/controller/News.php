<?php

namespace app\index\controller;

class News extends Base
{
    public function index(){
        $user_id = session('user_id');
        $orderInfo = controller('order')->getOrderInfo($user_id);
        $this->assign([
            'orderInfo' => $orderInfo
        ]);
        return $this->fetch();
    }
}
