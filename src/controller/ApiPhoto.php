<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\IdPhoto as Api;

/**
* @Apidoc\Title("API 智能证件照（收费）")
*/
class ApiPhoto extends ApiController
{ 
     
    public $api;
    public $guest = false;
    public function init(){
        parent::init();
        $this->api = new Api;
    }
    /**
    * @Apidoc\Title("照片规格") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST")   
    */
    public function get_spec(){
        return json_success(['data'=>$this->api->get_spec()]);
    }
    /**
    * @Apidoc\Title("制作证件照-有水印") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("spec_id",type="string",require=true,desc="规格")    
    * @Apidoc\Query("is_fair",type="string",require=true,desc="是否美颜，默认为美颜")    
    * @Apidoc\Query("fair_level",type="string",require=true,desc="美颜等级，分为1,2,3,4,5等级，默认3，只在is_fair为1时有效 ")    
    * @Apidoc\Query("url",type="string",require=true,desc="本地文件,以/开头")    
    * @Apidoc\Returned("top",type="string",desc="顶部图")  
    * @Apidoc\Returned("img",type="string",desc="需要打印的水印图") 
    * @Apidoc\Returned("file_name",type="string",desc="") 
    * @Apidoc\Returned("app_key",type="string",desc="") 
    */
    public function make(){
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        }
        $url = download_file($url); 
        $file = WWW_PATH.$url;
        if(!file_exists($file)){
            return json_error(['msg'=>'文件不存在']);   
        }
        $data = $this->api->make(['file'=>$file]);
        return json_success(['data'=>$data]);
    }

    /**
    * @Apidoc\Title("获取证件照-无水印") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("file_name",type="string",require=true,desc="")       
    * @Apidoc\Returned("top",type="string",desc="顶部图")  
    * @Apidoc\Returned("img",type="string",desc="可打印图片")  
    */
    public function get_make_no_water(){
        $file_name = $this->input['file_name'];
        if(!$file_name){
            return json_error(['msg'=>'参数异常']);
        } 
        $data = $this->api->get_make_no_water($file_name);
        return json_success(['data'=>$data]);
    }

    /**
    * @Apidoc\Title("正装选择") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST")  
    * @Apidoc\Returned("boy",type="string",desc="")  
    * @Apidoc\Returned("girl",type="string",desc="")   
    * @Apidoc\Returned("kid",type="string",desc="")   
    */
    public function get_ai_clothes(){
        return json_success(['data'=>$this->api->get_ai_clothes()]);
    }

    /**
    * @Apidoc\Title("制作剪裁换正装-有水印") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="本地文件,以/开头")   
    * @Apidoc\Query("clothes",type="string",require=true,desc="由ai_clothes返回")   
    * @Apidoc\Query("spec_id",type="string",require=true,desc="规格")    
    * @Apidoc\Query("is_fair",type="string",require=true,desc="是否美颜，默认为美颜")    
    * @Apidoc\Query("fair_level",type="string",require=true,desc="美颜等级，分为1,2,3,4,5等级，默认3，只在is_fair为1时有效 ")     
    * @Apidoc\Returned("top",type="string",desc="顶部图")  
    * @Apidoc\Returned("img",type="string",desc="需要打印的水印图") 
    * @Apidoc\Returned("file_name",type="string",desc="") 
    * @Apidoc\Returned("app_key",type="string",desc="") 
    */
    public function make_ai(){
        $input = $this->input;
        $url = $input['url'];
        $clothes = $input['clothes']; 
        $spec_id = $input['spec_id']; 

        if(!$url){
            return json_error(['msg'=>'参数异常']);
        }
        $url = download_file($url); 
        $file = WWW_PATH.$url;
        if(!file_exists($file)){
            return json_error(['msg'=>'文件不存在']);   
        }
        $data = $this->api->make_ai([ 
            'spec_id'=>$spec_id,
            'file'=>$file,
            'clothes'=>$clothes
        ]);
        return json_success(['data'=>$data]);
    }

    /**
    * @Apidoc\Title("获取剪裁换正装-无水印") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Photo")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("file_name",type="string",require=true,desc="")       
    * @Apidoc\Returned("top",type="string",desc="顶部图")  
    * @Apidoc\Returned("img",type="string",desc="可打印图片")  
    */
    public function get_ai_no_water(){
        $file_name = $this->input['file_name'];
        if(!$file_name){
            return json_error(['msg'=>'参数异常']);
        } 
        $data = $this->api->get_ai_no_water($file_name);
        return json_success(['data'=>$data]);
    }




}