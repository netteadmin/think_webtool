<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc; 
use app\webtool\rpc\Pdf; 

/**
* @Apidoc\Title("API PDF（收费）")
*/
class ApiPdf extends ApiController
{ 
     
    public $pdf;
    public $guest = false;
    public function init(){
        parent::init();
        $this->pdf = new Pdf;
    }
    /**
    * @Apidoc\Title("文档转PDF") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Pdf<br>https://market.aliyun.com/products/56928005/cmapi00044564.html")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="远程文档地址")    
    */
    public function to_pdf()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'请先上传文件']);
        } 
        $token  = $this->pdf->to_pdf($url);
        if(is_string($token)){  
            return json_success($token);
        }else{
            return json_error($token);
        }
    }
    /**
    * @Apidoc\Title("文档转PDF-结果") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Pdf")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("token",type="string",require=true,desc="用to_pdf生成的token查寻")    
    */
    public function get_to_pdf()
    { 
        $token = $this->input['token'];
        if(!$token){
            return json_error(['msg'=>'参数异常']);
        } 
        $res  = $this->pdf->get_to_pdf($token);
        return json_success($res); 
    }

    /**
    * @Apidoc\Title("PDF转图片") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Pdf<br>https://market.aliyun.com/products/56928005/cmapi00045684.html")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="远程文档地址")    
    */
    public function pdf_to_image()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'请先上传文件']);
        }
        $token = $this->pdf->pdf_to_image($url);
        if(is_string($token)){  
            return json_success($token);
        }else{
            return json_error($token);
        }
    }
    /**
    * @Apidoc\Title("PDF转图片-结果") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Pdf")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("token",type="string",require=true,desc="用pdf_to_image生成的token查寻")    
    */
    public function get_pdf_to_image()
    { 
        $token = $this->input['token'];
        if(!$token){
            return json_error(['msg'=>'参数异常']);
        } 
        $res  = $this->pdf->get_pdf_to_image($token);
        return json_success(['data'=>$res]); 
    }
}