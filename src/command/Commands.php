<?php
declare (strict_types = 1);

namespace app\webtool\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output; 
class Commands extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Commands')
            ->setDescription('命令行处理，如图片去背景');
    }

    protected function execute(Input $input, Output $output)
    {
        while(true){
            $all = db_get("webtool_cmd_job",[
                'status'=>'wait', 
            ]); 
            foreach($all as $v){  
                db_update("webtool_cmd_job",['status'=>'complete','updated_at'=>now()],['id'=>$v['id']]);  
                echo now()."开始处理>> \n\n";   
                $cmd = $v['cmd'];
                echo $cmd."\n";
                exec($cmd);
                echo "处理完成\n"; 
            } 
            sleep(1);
        }
        
    }
}
