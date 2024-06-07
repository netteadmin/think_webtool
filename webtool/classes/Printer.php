<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use app\webtool\classes\PrinterGx;
use app\webtool\classes\PrinterLianke;
/**
 * 统一处理打印功能
 * 且统一了参数
 */
class Printer{  
    /**
     * 获取数据库中任务状态
     * order_id
     * order_detail_id
     */
    public function get_job_db($option = []){
       $where['order_id'] = $option['order_id'];
       $where['order_detail_id'] = $option['order_detail_id']; 
       $res = db_get("webtool_printer_task",$where,1);
       return $res['status'];
    }
    /**
     * 获取打印机状态
     */
    public function get_status($option = []){
        $drive = $option['drive'];
        $res = ''; 
        switch($drive){
            case "gx":
                $obj = new PrinterGx; 
                $res = $obj->get_status(['printer_id'=>$option['printer_id']]);
                break;
            case "lianke":
                $obj = new PrinterLianke; 
                $res = $obj->get_status([
                    'device_id'=>$option['key1'],
                    'device_key'=>$option['key2'],
                ]);
                break;
        } 
        return $res;
    }
    /**
     * 获取任务状态
     */
    public function get_job($option = []){
        $drive = $option['drive'];
        switch($drive){
            case "gx":
                $obj = new PrinterGx; 
                $res = $obj->get_job([],$option['printer_id'],$option['job_id']);
                break;
            case "lianke":
                $obj = new PrinterLianke; 
                $res = $obj->get_job([],$option['job_id']);
                break;
        }
        return $res;
    }
    /**
    * 添加任务 
    */
    public function add_job($option = []){
        $printer = $option['printer'];
        $drive = $printer['drive'];
        $api_io_id = $printer['api_io_id'];
        $api_io_model = $printer['api_io_model']; 
        $order_id = $option['order_id']; 
        $order_detail_id = $option['order_detail_id'];   
        $key1 = $printer['key1'];
        $key2 = $printer['key2'];
        $url = $option['url'];  
        $_config = $option['config'];
        $type = $_config['type'];   
        trace("printer drive:".$drive,'info'); 
        switch ($drive){
            case 'gx':
                $file_name = md5($url).'.'.get_ext($url);  
                $obj = new PrinterGx; 
                $res = $obj->add_job([
                    'url'=>$url,
                    'order_id'=>$order_id,
                    'order_detail_id'=>$order_detail_id,
                ],$api_io_id, $file_name, $option);  
                break; 
            case 'lianke':  
                trace("printer drive lianke 1",'info');
                $obj = new PrinterLianke();  
                trace("printer drive lianke 2",'info'); 
                $res = $obj->add_job([ 
                    'url'=>$url,
                    'order_id'=>$order_id,
                    'order_detail_id'=>$order_detail_id,
                    'device_id'=>$key1,
                    'device_key'=>$key2,
                ],$api_io_model, $url, $option);  
                trace("printer drive lianke 3",'info'); 
                break; 
        }
        return $res;


    }

    /**
     * 获取数据库中取消任务状态
     * order_id
     * order_detail_id
     */
    public function close_job_db($option = []){
       $where['order_id'] = $option['order_id'];
       $where['order_detail_id'] = $option['order_detail_id']; 
       $res = db_get("webtool_printer_task",$where,1);
       $job_id = $res['job_id'];
       $printer_id = $res['printer_id'];
       $drive = $res['drive'];
       $this->close_job([],$drive,$printer_id,$task_id);
    }

    /**
    * 取消任务 
    */
    public function close_job($option = [],$drive='gx',$printer_id,$task_id){
        switch ($drive){
            case 'gx':
                $file_name = md5($url).'.'.get_ext($url);  
                $obj = new PrinterGx; 
                $res = $obj->cancel_job([],$printer_id,$task_id);    
                break; 
            case 'lianke':   
                $obj = new PrinterLianke();   
                $res = $obj->cancel_job([],$printer_id,$task_id);   
                break; 
        } 
    }


} 