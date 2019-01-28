<?php
namespace app\index\controller;

use think\Controller;
use think\facade\Request;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    public function register(Request $request)
    {
        $pid = $request::param('pid',1);
        $this->assign('pid',$pid);
        return $this->fetch();
    }
}
