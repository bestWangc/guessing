<?php
namespace app\index\controller;

use think\Db;

class Address extends Base
{
    public static function checkAddress($uid)
    {
        $addressId = Db::name('address')
            ->where('user_id',$uid)
            ->value('id');
        if(!empty($addressId)) return $addressId;
        return '';
    }
}
