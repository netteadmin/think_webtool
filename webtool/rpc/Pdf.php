<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\rpc;  
use app\webtool\classes\DocToPdf;
use app\webtool\classes\PdfToImage;
/**
* /rpc/webtool/Pdf
* $url = 'http://tool/rpc/webtool/Pdf';
* $client = rpc_client($url); 
*/
class Pdf{     
    /**
     * 文档转PDF
     */
    public function to_pdf($url){
        $url = urldecode($url);
        $token  = DocToPdf::covert($url);
        if(is_string($token)){ 
            $res  = DocToPdf::get($token);
            return ['data'=>['token'=>$token]+$res];
        }else{
            return $token;
        }
    }
    /**
     * 文档转PDF-结果
     */
    public function get_to_pdf($token){
        $res  = DocToPdf::get($token);
        return $res;
    }
    /**
     * PDF转图片
     */
    public function pdf_to_image($url){
        $url = urldecode($url);
        $token  = PdfToImage::covert($url);
        if(is_string($token)){ 
            $res  = PdfToImage::get($token)?:[];
            return ['data'=>['token'=>$token]+$res];
        }
        return $token; 
    }
    /**
     * PDF转图片-结果
     */
    public function get_pdf_to_image($token){
        $res  = PdfToImage::get($token);
        return $res;
    }
}