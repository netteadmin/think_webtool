<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\ContentReviewBaidu as ContentReview;

/**
* @Apidoc\Title("API 百度内容审核（收费）") 
* 
*/
class ApiContentReviewBaidu extends ApiController
{  
    protected $review;

    public function init(){
        parent::init(); 
        $this->review = new ContentReview;
    }
    /**
    * @Apidoc\Title("文本审核") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ContentReviewBaidu")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("text",type="string",require=true,desc="device_id")     
    */
    public function get_text(){
        $text = $this->input['text'];
        $res = $this->review->text($text);
        $info = $this->review->info;
        if($res){
            return json_success(['msg'=>'审核通过','info'=>$info]);    
        }else{
            $msg = $info['data'][0]['msg'];
            if($msg){
                $msg = ",".$msg;
            }
            return json_error(['msg'=>'未通过'.$msg,'info'=>$info]);
        } 
    } 
    /**
    * @Apidoc\Title("图片审核") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/ContentReviewBaidu")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("image",type="string",require=true,desc="远程http://图片或 /uploads/本地图片")     
    */
    public function get_image(){
        $image = $this->input['image'];
        $res = $this->review->image($image);
        $info = $this->review->info; 
        if($res){
            return json_success(['msg'=>'审核通过','info'=>$info]);    
        }else{
            $msg = $info['data'][0]['msg'];
            if($msg){
                $msg = ",".$msg;
            }
            return json_error(['msg'=>'未通过'.$msg,'info'=>$info]);
        } 
    }

}