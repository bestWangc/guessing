<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Award extends Controller
{
    public function index(){
        $awardInfo = Db::name('award_info')
            ->whereNotNull('result')
            ->order('id desc')
            ->limit(30)
            ->field('term_num,result,updated_date,win')
            ->select();
        if(!empty($awardInfo)){
            foreach ($awardInfo as $key => &$value){
                $value['updated_date'] = date('Y-m-d H:i:s',$value['updated_date']);
            }
        }
        $this->assign('awardInfo',$awardInfo);
        return $this->fetch();
    }
}
