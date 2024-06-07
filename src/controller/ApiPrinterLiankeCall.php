<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;   

/**
* @Apidoc\Title("API 链科打印机-回调")
*/
class ApiPrinterLiankeCall extends ApiController
{ 
    public $guest = true;
    /**
    * @Apidoc\Title("回调") 
    * @Apidoc\Method("POST")  
    * @Apidoc\Desc("https://cloud.wisiyilink.com/ ")
    */
    public function index(){
       $secret = get_config('printer_lianke_secret');
       trace("链科打印机-回调",'info');
       trace(json_encode($this->input),'info');
       return json_success();
    }
}