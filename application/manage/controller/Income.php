<?php

namespace app\manage\controller;

use app\tools\M3result;

class Income extends Base
{

    //获取首页图表数据
    public function getChartsInfo(){
        $uid = session('user_id');
        //本月1号
        $monFirst = mktime(0,0,0,date('m'),1,date('Y'));
        $result = db('group_income')
            ->where('user_id',$uid)
            ->where('created_date','>',$monFirst)
            ->field('bonus,created_date')
            ->order('created_date asc')
            ->select();
        $finalData = [];
        if(!empty($result)){
            $resultLen = count($result);
            foreach ($result as $key => $value){
                $finalData['countAll'][] = $value['bonus'];
                $finalData['xAxisData'][] = date('d',$value['created_date']);
                if($key == $resultLen-1){
                    $finalData['yesBonus'] = $value['bonus'];
                }
            }
        }
        $finalData['monthBonus'] = array_sum($finalData['countAll']);
        $m3_result = new M3result();
        $m3_result->code = 1;
        $m3_result->msg = 'success';
        $m3_result->data = $finalData;

        return json($m3_result->toArray());
    }
}
