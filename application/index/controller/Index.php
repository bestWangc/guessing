<?php
namespace app\index\controller;

use think\Db;

class Index extends Base
{
    public function index()
    {
        $awardInfo = Db::name('award_info')
            ->whereNotNull('result')
            ->order('id desc')
            ->limit(10)
            ->field('term_num,result,updated_date,win')
            ->select();
        if(!empty($awardInfo)){
            foreach ($awardInfo as $key => &$value){
                $value['updated_date'] = date('Y-m-d H:i:s',$value['updated_date']);
                $value['win'] = $value['win'] ? '丰年' : '瑞雪';
            }
        }
        $this->assign('awardInfo',$awardInfo);
        return $this->fetch();
    }

}
