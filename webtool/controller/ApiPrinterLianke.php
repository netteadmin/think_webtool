<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\PrinterLianke as Printer;

/**
* @Apidoc\Title("API 打印机(链科)（收费）")
*/
class ApiPrinterLianke extends ApiController
{ 
    protected $printer;
    protected $debug = true;
    public function init(){
        parent::init();
        $device_id = $this->input['device_id'];
        $device_key = $this->input['device_key'];
        if(!$device_id || !$device_key){
            echo json_encode(['code'=>250,'msg'=>'device_id必须'],JSON_UNESCAPED_UNICODE);exit;
        }
        $p = new Printer;
        $this->printer = $p->init($device_id,$device_key,$this->debug);
    }
    /**
    * @Apidoc\Title("获取端口信息-在线掉线") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")  
    * @Apidoc\Returned("info.online",type="string",desc="1在线")    
    * @Apidoc\Returned("info.is_expire",type="string",desc="false未过期 true过期")    

    */
    public function get_info(){
        $d = $this->printer->get_info();
        return json_success(['data'=>$d['data']]);
    }
    /**
    * @Apidoc\Title("获取端口下打印机列表") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")  
    * @Apidoc\Returned("row[].printer_name",type="string",desc="")    
    * @Apidoc\Returned("row[].driver_name",type="string",desc="get_printer_par参数用这个")    
    * @Apidoc\Returned("row[].driver_type",type="string",desc="")    
    */
    public function get_printer_list(){
        $d = $this->printer->get_printer_list();
        return json_success(['data'=>$d['data']]);
    }
    /**
    * @Apidoc\Title("获取某个打印机参数") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")  
    * @Apidoc\Query("driver_name",type="string",require=true,desc="设备名称")     
    * @Apidoc\Returned("bins",type="array",desc="{后进纸器: 261,自动选择: 7,进纸器 1: 258}")     
    * @Apidoc\Returned("color",type="string",desc="{彩色: 2,黑白: 1}")     
    * @Apidoc\Returned("copies",type="int",desc="支持最多复印分数")       
    */
    public function get_printer_par(){
        $printer_model = $this->input['driver_name'];
        if(!$printer_model){
            return json_error(['msg'=>'参数异常']);
        }
        $d = $this->printer->get_printer_par([],$printer_model); 
        return json_success(['data'=>$d]);
    } 

    /**
    * @Apidoc\Title("提交打印任务") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")  
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")   
    * @Apidoc\Query("jobFile",type="string",require=true,desc="(必填) 
    任务文件，多个URL请用换行符(\n)隔开")  
    * @Apidoc\Query("printerModel",type="string",require=false,desc="打印机型号（对应打印机列表接口printer_list的driver_name参数）默认为自动识别结果")    
    * @Apidoc\Query("jpAutoScale",type="string",default=4,require=true,desc="(必填) 自动缩放4 : 自适应(推荐)0 : 原图打印(由于dpi原因，可能会过小)1: 宽度优先(超出时裁剪高度)2: 高度优先(超出时裁剪宽度)'
3 : 拉伸全图")   
    * @Apidoc\Query("devicePort",type="string",default=1,require=false,desc="设备端口，默认为USB1接口，填写值为数字，多个USB口设备必填")  
    * @Apidoc\Query("dmOrientation",type="string",default=1,require=false,desc="打印纸张方向1：竖向2：横向")    
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
            'printerModel'=>$printerModel,
            'dmOrientation'=>$dmOrientation,
            'dmCopies'=>$dmCopies,
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
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")  
    * @Apidoc\Query("device_port",type="string",default=1,require=true,desc="")     
    * @Apidoc\Query("task_id",type="string",require=true,desc="任务ID")      
    */
    public function get_job(){
        $device_port = $this->input['device_port']?:1;
        $task_id = $this->input['task_id'];
        if(!$task_id){
            return json_error(['msg'=>'参数异常']);
        }
        $d = $this->printer->get_job([],$device_port,$task_id); 
        return json($d);
    } 

    /**
    * @Apidoc\Title("取消打印任务") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PrinterLianke<br>https://cloud.wisiyilink.com/ ")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("device_id",type="string",require=true,desc="device_id")    
    * @Apidoc\Query("device_key",type="string",require=true,desc="device_key")  
    * @Apidoc\Query("device_port",type="string",default=1,require=true,desc="")     
    * @Apidoc\Query("task_id",type="string",require=true,desc="任务ID")  
    */
    public function cancel_job(){
        $device_port = $this->input['device_port']?:1;
        $task_id = $this->input['task_id'];
        if(!$task_id){
            return json_error(['msg'=>'参数异常']);
        }
        $d = $this->printer->cancel_job([],$device_port,$task_id); 
        return json($d);
    } 
}