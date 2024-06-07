<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\ImgToPaper as Api;

/**
* @Apidoc\Title("API 照片放到纸张上")
*/
class ApiImgToPaper extends ApiController
{  
    public $api;
    public $guest = false;
    public function init(){
        parent::init();
        $this->api = new Api;
    }
    /**
    * @Apidoc\Title("1图") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImgToPaper")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("img1",type="string",require=true,desc="") 
    * @Apidoc\Query("type",type="string",require=true,desc="chusheng hukou")   
    */
    public function create_1(){
        $img1 = $this->input['img1'];
        $type = $this->input['type'];
        return json(['url'=>$this->api->create_1($img1,$type)]);
    }
    /**
    * @Apidoc\Title("2图") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImgToPaper")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("img1",type="string",require=true,desc="") 
    * @Apidoc\Query("img2",type="string",require=true,desc="") 
    * @Apidoc\Query("type",type="string",require=true,desc="id_card ")   
    */
    public function create_2(){
        $img1 = $this->input['img1'];
        $img2 = $this->input['img2'];
        $type = $this->input['type'];
        return json(['url'=>$this->api->create_2($img1,$img2,$type),'time'=>now()]);
    }
    /**
    * @Apidoc\Title("4或8图") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ImgToPaper")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("text",type="string",require=true,desc="") 
    * @Apidoc\Query("img1",type="string",require=true,desc="") 
    * @Apidoc\Query("type",type="string",require=true,desc="1 1x 2 2x") 
    * @Apidoc\Query("num",type="string",require=true,desc="4 8 ")   
    */
    public function create_3(){
        $text = $this->input['text'];
        $img1 = $this->input['img1']; 
        $type = $this->input['type'];
        $num = $this->input['num'];
        return json(['url'=>$this->api->create_3($text,$img1,$type,$num)]);
    }

}