<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use \simple_html_dom\simple_html_dom;
use think\Db;
use think\facade\Log;

class Term extends Command
{
    protected function configure()
    {
        $this->setName('term')->setDescription('开奖');
    }

    protected function execute(Input $input, Output $output)
    {
    	//截止运行时间
        $endTime = date('Y-m-d',time()).' 22:10';
        $endTime = strtotime($endTime);
        if(time() > $endTime) return false;

        //$url = 'https://www.km28.com/gp_chart/cqssc/0.html';
        $url = 'https://chart.cp.360.cn/zst/ssccq';

        $output->writeln('term start.');
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, FALSE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
        curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        $html_dom = new simple_html_dom(); //new simple_html_dom对象
        $html_dom->load($response, true); //加载html
        //$table_tr = $html_dom->find('.t_tr2'); //获取tr
        $table_tr = $html_dom->find('#data-tab tr'); //获取tr
        $count = count($table_tr);
        foreach ($table_tr as $key => $value) {
            // if($key == ($count-2) || $key == ($count-1)){
            if($key >= ($count-10)){
                $term_num = $value->children(0)->plaintext;

                $result = $value->children(2)->plaintext;
                $result = explode(' ',$result)[0];

                $output->writeln($term_num);

                if(empty($term_num) || empty($result)){
                    continue;
                }
                $isHasTerm = db('award_info')
                    ->where('term_num',$term_num)
                    ->value('result');
                if(!empty($isHasTerm)) continue;

                $win = substr($result,-1);
                $win = $win%2 ==1 ? 1 : 0;

                $data = [
                    'result' => $result,
                    'win' => $win,
                    'updated_date' => time()
                ];

                $update = db('award_info')
                    ->where('term_num',$term_num)
                    ->update($data);

                if($update) trace(date('Y-m-d H:i:s',time()).'   '.$term_num.'\r\n');
            }
        }
        $output->writeln('term end.');
        $html_dom->clear();
        return true;
    }
}