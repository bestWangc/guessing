<?php

namespace app\manage\controller;


class Income extends Base
{

    public function index(){
        return $this->fetch();
    }
}
