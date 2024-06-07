<?php
declare (strict_types = 1);

namespace app\webtool\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class CleanFile extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('webtool_clean_file')
            ->setDescription('清理uploads/tmp文件');
    }

    protected function execute(Input $input, Output $output)
    {
        //24小时
        $time = 3600*24;
        $dir = WWW_PATH.'/uploads/tmp/'.date("Y-m-d",time()-86400);
        exec("rm -rf ".$dir);
        // 指令输出
        $output->writeln('清理文件完成');
    }
}
