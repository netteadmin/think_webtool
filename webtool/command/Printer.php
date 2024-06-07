<?php
declare (strict_types = 1);

namespace app\webtool\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use app\webtool\classes\Printer as PrinterServer;
class Printer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('printer')
            ->setDescription('更新打印状态');
    }

    protected function execute(Input $input, Output $output)
    {
        while(true){
            $all = db_get("webtool_printer_task",[
                'status'=>'progress',
                'created_at[>]'=> date("Y-m-d H:i:s",time()-3600)
            ]);
            $obj = new PrinterServer;
            foreach($all as $v){   
                $order_detail_id = $v['order_detail_id'];
                echo now()."开始处理>> \n\n";         
                $res = $obj->get_job($v); 
                $info = $res['data']['info'];
                $job_state = $info['job_state'];
                trace("获取order_detail_id:".$order_detail_id,'info');
                trace("获取状态job_state:".$job_state,'info');
                trace($res,'info');
                if($job_state == 'finished'){
                    db_update("webtool_printer_task",[
                        'status'=>'complete'
                    ],['id'=>$v['id']]);
                    echo ">>>> ".$v['job_id'] ." <<<< 打印任务完成\n";
                    trace("order_detail_id:".$order_detail_id."  job_state:".$job_state."完成",'info');
                } else{
                    echo "order_detail_id > ".$order_detail_id." job_id>".$v['job_id']." job_state>".$job_state."\n";
                }
                echo "\n\n";
            } 
            sleep(1);
        }
        
    }
}
