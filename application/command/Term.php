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

        $nowTime = date('Y-m-d/H:i',time());
        var_dump($nowTime);
        $nowTimeArr = explode('/',$nowTime);
        var_dump($nowTimeArr);
        $currentDate = explode('-',$nowTimeArr[0]);
        $currentTime = explode(':',$nowTimeArr[1]);
        $currentMinute = 0;
        if((int)$currentTime[1] > 30) $currentMinute = 30;

        $postData = [
            "txtYear" => (int)$currentDate[0],
            "txtMonth" => (int)$currentDate[1],
            "txtDay" => (int)$currentDate[2],
            "txtFromHH" => (int)$currentTime[0],
            "txtFromMinute" => $currentMinute,
            "txtToHH" => (int)$currentTime[0],
            "txtToMinute" => $currentMinute
        ];

        /*{
            "txtYear": 2019,
	"txtMonth": 2,
	"txtDay": 12,
	"txtFromHH": 16,
	"txtFromMinute": 8,
	"txtToHH": 17,
	"txtToMinute": 0
}*/
        $postData = json_encode($postData);
        echo $postData;

        // $url = 'https://chart.cp.360.cn/zst/ssccq';
        $url = 'http://www.cndgv.com/';

        $output->writeln('term start.');
        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, TRUE);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
        curl_setopt ($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData);
        $response = curl_exec($ch);
        curl_close($ch);

        $html_dom = new simple_html_dom(); //new simple_html_dom对象
        $html_dom->load($response, true); //加载html
        $table_tr = $html_dom->find('.main_detail_word td'); //获取tr
        // $count = count($table_tr);
        var_dump($table_tr[0]->plaintext);
        var_dump($table_tr[1]->plaintext);
        var_dump($table_tr[2]->plaintext);
        die;

        foreach ($table_tr as $key => $value) {
            $output->writeln($value);
            // if($key == ($count-2) || $key == ($count-1)){
            // if($key >= ($count-5)){
            //     $term_num = $value->children(0)->plaintext;
                $term_num = $value->plaintext;

                var_dump($term_num);
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
        // }
        $output->writeln('term end.');
        $html_dom->clear();
        return true;
    }
}