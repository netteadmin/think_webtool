<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController;  
use hg\apidoc\annotation as Apidoc;   
use app\webtool\classes\SensitiveWords;

/**
* @Apidoc\Title("API 过滤敏感词")
*/
class ApiSensitiveWords extends ApiController
{ 
    public $guest = true;
    protected $api;
    public function init(){
        parent::init();
        $this->api = new SensitiveWords;
    }
    /**
    * @Apidoc\Title("过滤敏感词") 
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("content",type="string",require=true,desc="内容") 
    * @Apidoc\Query("replace",type="string",require=true,desc="替换为") 
    */
    public function get(){ 
       $content = $this->api->get($this->input['content'],$this->input['replace']); 
       return json_success(['data'=>$content]); 
    }
    /**
    * @Apidoc\Title("是否合法") 
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("content",type="string",require=true,desc="内容") 
    */
    public function is_legal(){ 
       $flag = $this->api->is_legal($this->input['content']);
       if($flag){
            return json_success();
       }else{
            return json_error();
       } 
    }
}