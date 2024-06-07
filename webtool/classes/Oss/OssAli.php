<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss;  
/**
* composer require aliyuncs/oss-sdk-php
* https://github.com/aliyun/aliyun-oss-php-sdk
*/ 
class OssAli
{ 
    /**
     * 上传
     *
     * @param string $local_url 本地URL
     * @param string $remote_url 上传到远程地址
     * @return array
     */
    public function upload($local_url,$remote_url = ''){
        if(!$remote_url){
            $remote_url = create_oss_remote_url($local_url);
        } 
        $res = OssAliBase::upload($local_url, $remote_url); 
        return $res;
    }
    /**
    * 列表
    */
    public function lists(){ 
        $res = OssAliBase::lists(); 
        return $res;
    }

 
}