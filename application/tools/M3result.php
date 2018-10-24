<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-8-11
 * Time: 10:47
 */

namespace app\tools;


class M3result
{
    public $code = 0;
    public $msg = '';
    public $data = [];

    public function toArray(){
        return [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data
        ];
    }
}