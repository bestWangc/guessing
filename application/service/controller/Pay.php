<?php
namespace app\service\controller;

use app\tools\M3result;
use think\Db;
use think\facade\Log;
use think\facade\Request;

class Pay extends Base
{
    public function payBack(Request $request)
    {
        $allParams = $request::get();

        Log::error('11111111111111');
        Log::error($allParams);
        Log::error('2222222222222');

        return 'SUCCESS';
    }

    public function payFront(Request $request)
    {
        $allParams = $request::get();

        Log::error('11111111111111');
        Log::error($allParams);
        Log::error('2222222222222');

        return 'SUCCESS';
    }
}