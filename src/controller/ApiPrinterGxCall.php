<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController;  
use hg\apidoc\annotation as Apidoc;   
 
class ApiPrinterGxCall extends ApiController
{    
    public $guest = true;  
    public function index(){
        $input = $this->input;  
        trace("gx call back.",'info');
        trace($input,'info');
        $job_id = $input['job_id'];
        $job_status = $input['job_status'];
        trace('job_id:'.$job_id,'info');
        trace('job_status:'.$job_status,'info');
        if($job_status == 'finished'){
            set_webtool_printer_finish($job_id);
        }
    }
}