<?php

namespace app\mapp\controller;

class News extends Base
{
    public function index()
    {
        $orderInfo = controller('order')->getOrderInfo($this->uid);

        $this->assign([
            'orderInfo' => $orderInfo
        ]);
        return $this->fetch();
    }
}
