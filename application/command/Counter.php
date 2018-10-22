<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use \simple_html_dom\simple_html_dom;
use think\Db;
use think\facade\Log;

class Counter extends Command
{
    protected function configure()
    {
        $this->setName('counter')->setDescription('统计每日平台流水信息');
    }

    protected function execute(Input $input, Output $output)
    {
        //今日凌晨时间戳
        $time = strtotime(date('Ymd'));
        $uinfo = db('users su')
            ->join('user_role sur','sur.id = su.role')
            ->where('su.role','<>',0)
            ->field('su.id,sur.proportion')
            ->select();

        $output->writeln('count start.');
        if(!empty($uinfo)){
            foreach ($uinfo as $key => $value){
                $result = db('users')
                    ->alias('su')
                    ->join('order so','so.user_id = su.id','left')
                    ->join('award_info sai','sai.id = so.award_id','left')
                    ->join('goods sg','sg.id = so.goods_id','left')
                    ->where('su.parent_id',$value['id'])
                    ->where('so.created_date','>',$time)
                    ->field('so.amount,so.guessing,sai.win,(sg.success_price*so.goods_num) AS success_price')
                    ->select();
                if(!empty($result)){
                    $winArr = [];
                    $lossArr = [];
                    foreach ($result as $k => $v){
                        if($v['guessing'] === $v['win']){
                            array_push($winArr,$v['success_price']-$v['amount']);
                        }else{
                            array_push($lossArr,$v['amount']*0.9);
                        }
                    }

                    //竞猜失败即为平台收入
                    $income = array_sum($lossArr);
                    //竞猜成功即为平台支出
                    $out = array_sum($winArr);
                    $data = [
                        'user_id' => $value['id'],
                        'income' => $income,
                        'out' => $out,
                        'created_date' => time(),
                        'bonus' => ($income-$out) ? ($income-$out)*$value['proportion'] : 0
                    ];
                    $res = db('group_income')->insert($data);

                    if(!$res){
                        Log::error('insert group_income error');
                    }
                }
            }
        }
        $output->writeln('count end.');
        return true;
    }
}