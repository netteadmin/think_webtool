<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc; 
use app\webtool\rpc\PdfFree; 

/**
* @Apidoc\Title("API PDF")
*/
class ApiPdfFree extends ApiController
{ 
     
    public $pdf;
    public $guest = false;
    public function init(){
        parent::init();
        $this->pdf = new PdfFree;
    }
    /**
    * @Apidoc\Title("doc、xls、ppt转pdf") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="文件地址")   
    * @Apidoc\Returned("pages",type="string",require=true,desc="")   
    */
    public function word_to_pdf()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->word_to_pdf($url)); 
    }     

    /**
    * @Apidoc\Title("PDF文件页数") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="文件地址")   
    * @Apidoc\Returned("pages",type="string",require=true,desc="")   
    */
    public function get_pages()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->get_pages($url)); 
    }
    /**
    * @Apidoc\Title("图片、pdf合并为一个PDF") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="文件地址多个以,分隔")   
    * @Apidoc\Returned("url",type="string",require=true,desc="")   
    */
    public function merger()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->merger($url)); 
    }
    /**
    * @Apidoc\Title("生成pdf") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("html_code",type="string",require=true,desc="HTML代码")   
    */
    public function create_html()
    {  
        $html_code = $this->input['html_code'];
        if(!$html_code){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->create_html($option = [],$html_code)); 
    } 

    /**
    * @Apidoc\Title("PDF导出图片") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="文件地址")   
    * @Apidoc\Returned("url",type="string",require=true,desc="")   
    */
    public function pdf_to_image()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->pdf_to_image($url)); 
    }
    /**
    * @Apidoc\Title("图片转为PDF") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/PdfFree")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("url",type="string",require=true,desc="文件地址")    
    * @Apidoc\Returned("url",type="string",require=true,desc="")    
    */
    public function image_to_pdf()
    {  
        $url = $this->input['url'];
        if(!$url){
            return json_error(['msg'=>'参数异常']);
        } 
        return json($this->pdf->image_to_pdf($url)); 
    }
    
    
}