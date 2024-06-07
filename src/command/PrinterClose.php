<?php
declare (strict_types = 1);

namespace app\webtool\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\webtool\classes\Printer as PrinterServer;
class PrinterClose extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('printer') 
            ->addOption('job_id', null,Option::VALUE_REQUIRED, "登录帐号")
            ->setDescription('取消打印');
    }

    protected function execute(Input $input, Output $output)
    {
        if(!$input->hasOption('job_id')){
            $output->error('缺少参数,格式： --job_id 任务ID');
            return;
        }
        $job_id = $input->getOption('job_id');
        close_webtool_printer_job($job_id);  
    }
}