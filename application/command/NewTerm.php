<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

class NewTerm extends Command
{
    protected function configure()
    {
        $this->setName('new_term')->setDescription('整点和半点在award_info新建一条term');
    }

    protected function execute(Input $input, Output $output)
    {

        $output->writeln('new term start.');

        $term_num = date('ymdHi',time());
        $data = [
            'term_num' => $term_num,
            'created_date' => time()
        ];

        $status = false;
        while (!$status){
            $status = $this->insertDB($data);
        }

        $output->writeln('new term end.');
        return true;
    }

    protected function insertDB($data)
    {
        Db::startTrans();
        $res = false;
        try {
            $res = Db::name('award_info')->insert($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return $res;
    }
}