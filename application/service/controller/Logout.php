<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:48
 */

namespace app\service\controller;

use app\tools\M3result;


class Logout extends Base
{
    public function index(){
        session(null);
        $m3_result = new M3result();
        $m3_result->code = 1;
        $m3_result->msg = '注销成功';
        return json($m3_result->toArray());
    }
}