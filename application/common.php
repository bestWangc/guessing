<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\tools\M3result;
// 应用公共文件
function statusTrans($status){
    $res = '';
    switch ($status){
        case 0:
            $res = '失败';
            break;
        case 1:
            $res = '成功';
            break;
        case 2:
            $res = '待处理';
            break;
    }
    return $res;
}

//返回json
function jsonRes($code,$msg,$data=[]){
    $m3_result = new M3result();
    $m3_result->code = $code;
    $m3_result->msg = $msg;
    $m3_result->data = $data;
    return json($m3_result->toArray());
}