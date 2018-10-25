<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use \simple_html_dom\simple_html_dom;
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
        $termInfo = db('award_info')
            ->order('id desc')
            ->field('term_num')
            ->find();

        $data = [
            'term_num' => $termInfo['term_num']+1,
            'created_date' => time()
        ];
        db('award_info')->insert($data);
        $output->writeln('new term end.');
        return true;
    }
}