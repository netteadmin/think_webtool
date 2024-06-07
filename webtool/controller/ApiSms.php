<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\rpc\Sms as Api;

/**
* @Apidoc\Title("API 短信（收费）")
*/
class ApiSms extends ApiController
{  
    public $api;
    public $guest = false; 
    public function init(){
        parent::init();
        $this->api = new Api; 
    }

     /**
    * @Apidoc\Title("短信信息") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Sms")
    * @Apidoc\Method("POST")     
    * @Apidoc\Returned("total",type="string",desc="总数")   
    * @Apidoc\Returned("sms_less",type="string",desc="剩余")   
    * @Apidoc\Returned("sms_used",type="string",desc="已用")   
    */
    public function get_info(){ 
         return json_success(['data'=>$this->api->get_info()]);
    }
    /**
    * @Apidoc\Title("发送") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Sms")
    * @Apidoc\Method("POST")    
    * @Apidoc\Query("phone",type="string",require=true,desc="手机号")    
    * @Apidoc\Query("message",type="string",require=true,desc="发送信息")    
    * @Apidoc\Query("sign",type="string",require=true,desc="签名")    
    */
    public function send(){
         $input = $this->input;
         $phone = $input['phone'];
         $message = $input['message'];
         $sign = $input['sign'];
         return json($this->api->send($phone,$message,$sign));
    }

    /**
    * @Apidoc\Title("阿里云短信发送") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Sms")
    * @Apidoc\Method("POST")    
    * @Apidoc\Query("phone",type="string",require=true,desc="手机号")    
    * @Apidoc\Query("template",type="string",require=true,desc="")    
    * @Apidoc\Query("data",type="string",require=true,desc="json数据")    
    */
    public function send_ali(){
         $input = $this->input;
         $phone = $input['phone'];
         $template = $input['template'];
         $data = $input['data']; 
         $data = json_decode($data,true); 
         if(!$data){
            return json_error(['data'=>json_encode(['name'=>123456])]);
         }
         if(!$phone || !$template){
            return json_error(['msg'=>'参数异常']);
         }
         $res = $this->api->send_ali($phone,$template,$data);
         if($res){
            return json_success(['data'=>$res]);
         }else{
            return json_error(['msg'=>$this->api->msg]);
         } 
    }

    /**
    * @Apidoc\Title("腾讯云短信发送") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Sms")
    * @Apidoc\Method("POST")    
    * @Apidoc\Query("phone",type="string",require=true,desc="手机号")    
    * @Apidoc\Query("template",type="string",require=true,desc="")    
    * @Apidoc\Query("data",type="string",require=true,desc="json数据")    
    */
    public function send_qcloud(){
         $input = $this->input;
         $phone = $input['phone'];
         $template = $input['template']; 
         $data = $input['data']; 
         $data = json_decode($data,true); 
         if(!$data){
            return json_error(['data'=>json_encode(['name'=>123456])]);
         }
         if(!$phone || !$template){
            return json_error(['msg'=>'参数异常']);
         } 
         $res = $this->api->send_qcloud($phone,$template,$data);
         if($res){
            return json_success(['data'=>$res]);
         }else{
            return json_error(['msg'=>$this->api->msg]);
         } 
    }

}