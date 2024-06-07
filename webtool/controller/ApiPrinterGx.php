<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\PrinterGx as Printer;

/**
* @Apidoc\Title("API 打印机(佳能)（收费）")
*/
class ApiPrinterGx extends ApiController
{ 
    protected $printer;
    protected $debug = true;
    public function init(){
        parent::init();
        $device_id = $this->input['device_id'];  
        $this->printer = new Printer;
        if($device_id){
            $this->printer = $this->printer->init($device_id);
        } 
    }
    /**
    * @Apidoc\Title("获取端口信息-在线掉线") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterGx ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")     
    * @Apidoc\Returned("info.online",type="string",desc="1在线")    
    * @Apidoc\Returned("info.is_expire",type="string",desc="false未过期 true过期")    
    */
    public function get_info(){
        $device_id = $this->input['device_id'];
        $d = $this->printer->get_info(['device_id'=>$device_id]);
        return $d;
    } 
    /**
    * @Apidoc\Title("获取某个打印机参数") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterGx ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("printer_id",type="string",require=true,desc="printer_id")    
    */
    public function get_printer_par(){
        $printer_id = $this->input['printer_id'];
        if(!$printer_id){
            return json_error(['msg'=>'参数异常']);
        }
        $d = $this->printer->get_printer_par([],$printer_id); 
        return json_success(['data'=>$d]);
    } 

    /**
    * @Apidoc\Title("提交打印任务") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterGx ")
    * @Apidoc\Method("POST")  
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")   
    * @Apidoc\Query("jobFile",type="string",require=true,desc="(必填) 
    任务文件，多个URL请用换行符(\n)隔开")  
    * @Apidoc\Query("printerModel",type="string",require=false,desc="printer_id")    
    * @Apidoc\Query("dmCopies",type="string",default=1,require=false,desc="打印份数1：打印一份,最大不能超过copies")    
    * @Apidoc\Query("dmColor",type="string",default=1,require=false,desc="打印颜色1：黑白2：彩色")     
    * @Apidoc\Query("dmPaperSize",type="string",default=9,require=true,desc="(必填) 打印纸张尺寸9：A4
11：A5 可取值：获取打印机参数：Capabilities -> Papers")   
    * @Apidoc\Returned("task_id",type="string",desc="任务ID")       
    */
    public function add_job(){
        $input = $this->input;
        $printerModel = $input['printerModel'];
        $devicePort = $input['devicePort'];
        $jobFile = $input['jobFile'];
        $dmOrientation = $input['dmOrientation']?:1;
        $dmCopies = $input['dmCopies']?:1;
        $dmColor = $input['dmColor']?:1;
        $dmPaperSize = $input['dmPaperSize']?:9; 
        if(!$jobFile || !$printerModel){
            return json_error(['msg'=>'参数异常']);
        }
        $file = download_file($jobFile,true);   
        $optional_array = [
            'printer_id'=>$printerModel,
            'dmOrientation'=>$dmOrientation,
            'copies'=>$dmCopies,
            'dmColor'=>$dmColor,
            'dmPaperSize'=>$dmPaperSize,
            'devicePort'=>$devicePort,
            'callbackUrl'=>'',
        ];   
        $d = $this->printer->add_job([],$printerModel, $file, $optional_array); 
        if($d){
            return json_success(['data'=>['task_id'=>$d]]);
        } 
        return json_error(['msg'=>'操作失败']);
    } 

    /**
    * @Apidoc\Title("获取打印任务状态") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterGx ")
    * @Apidoc\Method("POST")      
    * @Apidoc\Query("printer_id",type="string",default=1,require=true,desc="")     
    * @Apidoc\Query("task_id",type="string",require=true,desc="任务ID")      
    */
    public function get_job(){
        $printer_id = $this->input['printer_id']?:1;
        $task_id = $this->input['task_id'];
        if(!$task_id){
            return json_error(['msg'=>'参数异常']);
        }
        $d = $this->printer->get_job([],$printer_id,$task_id); 
        return json($d);
    } 

    /**
    * @Apidoc\Title("取消打印任务") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterGx ")
    * @Apidoc\Method("POST")    
    * @Apidoc\Query("printer_id",type="string",default=1,require=true,desc="")     
    * @Apidoc\Query("task_id",type="string",require=true,desc="任务ID")  
    */
    public function cancel_job(){
        $printer_id = $this->input['printer_id'];
        $task_id = $this->input['task_id'];
        if(!$task_id){
            return json_error(['msg'=>'参数异常']);
        }
        $par['printer_id'] = $printer_id;  
        $d = $this->printer->cancel_job([],$printer_id,$task_id); 
        return json($d);
    } 
}