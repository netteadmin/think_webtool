<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController;  
use hg\apidoc\annotation as Apidoc;   
use app\webtool\classes\Pinyin;

/**
* @Apidoc\Title("API 汉字转拼音")
*/
class ApiPinyin extends ApiController
{ 
    public $guest = true;
    protected $api;
    public function init(){
        parent::init();
        $this->api = new Pinyin;
    }
    /**
    * @Apidoc\Title("转拼音") 
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("content",type="string",require=true,desc="内容")     
    */
    public function abc(){ 
       $content = $this->api->abc($this->input['content']); 
       return json_success(['data'=>$content]); 
    }
    /**
    * @Apidoc\Title("转拼音以-链接") 
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("content",type="string",require=true,desc="内容") 
    * @Apidoc\Query("tag",type="string",require=true,desc="-") 
    */
    public function link(){ 
       $content = $this->api->link($this->input['content'],$this->input['tag']); 
       return json_success(['data'=>$content]); 
    }

    /**
    * @Apidoc\Title("首字母") 
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("content",type="string",require=true,desc="内容") 
    * @Apidoc\Query("tag",type="string",require=true,desc="-") 
    */
    public function first(){ 
       $content = $this->api->first($this->input['content']); 
       return json_success(['data'=>$content]); 
    }
}