<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\rpc; 
use app\webtool\classes\Oss\OssAli;
use app\webtool\classes\Oss\OssCos;
use app\webtool\classes\Oss\OssUpyun;
use app\webtool\classes\Oss\OssQiniu;
use app\webtool\classes\Oss\OssHuawei;
use app\webtool\classes\Oss\OssAmazonS3;
use app\webtool\classes\Oss\OssAzureBlob;
use app\webtool\classes\Oss\OssJd;
use app\webtool\classes\Oss\OssBaidu;

/**
* /rpc/webtool/Oss 
* $url = 'http://tool/rpc/webtool/Oss';
* $client = rpc_client($url); 
*/
class Oss{  
    /**
    * 公共上传
    * @param $type 如 ali tx upyun qiniu azure s3 huawei
    */
    public function upload($local_url,$remote_url = '',$type = ''){
        $type = $type?:get_config('OSS_DEFAULT');
        $me = $type."_upload";
        if(method_exists($this,$me)){
            return $this->$me($local_url,$remote_url);
        }
        return ['code'=>250,'msg'=>'不支持的上传方法','type'=>'error'];
    }
    /**
    * 上传到阿里云OSS 
    */
    public function ali_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssAli;
        $nid = webtool_log('oss','阿里云上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 

    /**
    * 上传到腾讯云COS
    */
    public function tx_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssCos;
        $nid = webtool_log('oss','腾讯云COS上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 

    /**
    * 上传到又拍云对象存储
    */
    public function upyun_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssUpyun;
        $nid = webtool_log('oss','又拍云对象存储上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 

    /**
    * 上传到七牛
    */
    public function qiniu_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssQiniu;
        $nid = webtool_log('oss','七牛上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 

    /**
    * 上传到华为云
    */
    public function huawei_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssHuawei;
        $nid = webtool_log('oss','华为云上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 
   /**
    * 上传到京东云
    */
    public function jd_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssJd;
        $nid = webtool_log('oss','京东云上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    }     
    

    /**
    * 上传到AmazonS3
    */
    public function s3_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssAmazonS3;
        $nid = webtool_log('oss','AmazonS3上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    }  

    /**
    * 上传到Azure Blob
    */
    public function azure_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssAzureBlob;
        $nid = webtool_log('oss','AzureBlob上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    }  

    /**
    * 上传到百度Bos
    */
    public function baidu_upload($local_url,$remote_url){
        if(!$local_url){
            return ['code'=>250,'msg'=>'参数异常','type'=>'error'];
        }
        $local_url = download_file($local_url); 
        $api = new OssBaidu;
        $nid = webtool_log('oss','百度Bos上传',[
            'local_url'=>$local_url,
            'remote_url'=>$remote_url,
        ]);
        $res = $api->upload($local_url,$remote_url); 
        if($res){
            update_webtool_log($nid,['api_data'=>$res],'ok');
            return ['code'=>0,'data'=>$res,'type'=>'success'];
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['code'=>250,'msg'=>'','type'=>'error'];
        }
    } 

}