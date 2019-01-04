<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;
use think\facade\Session;
use think\Request;

class Logout extends Base
{
    public function index(Request $request)
    {
        $type = $request->param('type',0);
        switch ($type){
            case 1:
                Session::delete('user_id');
                break;
            case 2:
                Session::delete('u_id');
                break;
            case 3:
                Session::delete('uid');
                break;
        }
        session(null);
        $m3_result = new M3result();
        $m3_result->code = 1;
        $m3_result->msg = '注销成功';
        return json($m3_result->toArray());
    }
}