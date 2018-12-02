<?php

namespace app\index\controller;


class User extends Base
{

    public function index(){
        return $this->fetch();
    }
}
