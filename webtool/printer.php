<?php 
use app\webtool\classes\Printer;

function set_webtool_printer_finish($job_id){
	$res = db_get("webtool_printer_task",['job_id'=>$job_id],1);
    if($res){
        $order_id = $res['order_id'];
        db_update("webtool_printer_task",[
            'status'=>'complete'
        ],['job_id'=>$job_id]); 
        $order = db_get("printer_order",[
            'id'=>$order_id,
        ],1);   
        exec_get_printer_status($order); 
    } 
}

function close_webtool_printer_job($job_id){
    $res = db_get("webtool_printer_task",['job_id'=>$job_id],1);
    if(!$res){ 
        return;
    }
    $printer_id = $res['printer_id'];
    $obj = new Printer;   
    $obj->close_job($option = [],$drive='gx',$printer_id,$job_id);
}