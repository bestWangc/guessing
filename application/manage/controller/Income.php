<?php

namespace app\manage\controller;

use app\tools\M3result;

class Income extends Base
{

    //获取首页图表数据
    public function getChartsInfo(){

        //本月1号
        $monFirst = mktime(0,0,0,date('m'),1,date('Y'));
        $result = db('group_income')
            ->where('user_id',$this->uid)
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
        if(!empty($finalData)){
            $finalData['monthBonus'] = array_sum($finalData['countAll']);
        }
        return jsonRes(1,'成功',$finalData);
    }
}
