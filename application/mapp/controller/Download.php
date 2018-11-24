<?php
namespace app\mapp\controller;

use think\Controller;
use think\facade\Env;

class Download extends Controller
{
    //下载文件
    public function download()
    {
        $file_url = Env::get('root_path').'/public/uploads/photo_1.jpg';
        if(!file_exists($file_url)){ //检查文件是否存在
            abort(404,'文件未找到');
        }
        return download($file_url, 'my');
    }

}
