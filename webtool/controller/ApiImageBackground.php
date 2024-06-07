<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\ImageBackground as Api;

/**
* @Apidoc\Title("API 照片处理")
*/
class ApiImageBackground extends ApiController
{ 
     
    public $api;
    public $guest = false;
    public function init(){
        parent::init();
        $this->api = new Api;
    }
    /**
    * @Apidoc\Title("图片移除背景") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImageBackground")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")    
    */
    public function remove_bg(){
        $url = $this->input['url'];
        $url = $this->api->remove_bg($url);
        if(!$url){
            return json_error(['msg'=>'图片不存在']);
        }
        $url['time'] = now();
        return json_success($url);
    }

    /**
    * @Apidoc\Title("提取头像") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImageBackground")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")    
    */
    public function get_avatar(){
        $url = $this->input['url'];
        $url = $this->api->get_avatar($url);
        if(!$url){
            return json_error(['msg'=>'图片不存在']);
        }
        $url['time'] = now();
        return json_success($url); 
    }

    /**
    * @Apidoc\Title("图片换底色") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImageBackground")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")    
    * @Apidoc\Query("color",type="string",require=true,desc="")    
    * @Apidoc\Query("size",type="string",require=true,desc=" 1 1x 2 2x")    
    */
    public function add_bg(){
        $url = $this->input['url'];
        $color = $this->input['color']?:'blue';
        $size = $this->input['size']?:'1';
        $url = $this->api->add_bg($url,$color,$size);
        if(!$url){
            return json_error(['msg'=>'图片不存在']);
        }
        $url['time'] = now();
        return json_success($url); 
    }

}