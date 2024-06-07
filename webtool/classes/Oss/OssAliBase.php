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
 
use OSS\OssClient;
use OSS\Core\OssException; 
use OSS\Http\RequestCore;
use OSS\Http\ResponseCore;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class OssAliBase
{
    public static $obj; 
    public static $oss_url = "";
    public static $bucket  = "";


    /**
    * 获取私有签名URL，1小时过期
    */
    public static function getUrl($url,$bucket = ''){
        $arr = self::_comm_sts(); 
        if(!$arr['aliyun_RoleArn']){
            return cdn_url().$url;
        }
        $accessId = $arr['accessId'];
        $accessKey = $arr['accessKey'];
        $SecurityToken = $arr['SecurityToken'];
        $endPoint = $arr['endPoint']; 
        $options= array("response-content-disposition"=>"inline",); 
        $ossClient = new OssClient($accessId, $accessKey, $endPoint, false, $SecurityToken);   
        $signedUrl = $ossClient->signUrl($bucket, $url, 3600, "GET",$options); 
        return $signedUrl;  
    }

    /**
    * 生成需要用户上传的PUT URL，一般情况不需要

    use OSS\Http\RequestCore;
    https://help.aliyun.com/document_detail/32106.html
    // 使用签名URL上传文件。
    // 填写上传的字符串。
    $content = "Hello OSS";
    $request = new RequestCore($signedUrl);
    // 生成的签名URL以PUT的方式访问。
    $request->set_method('PUT');
    $request->add_header('Content-Type', '');
    $request->add_header('Content-Length', strlen($content));
    $request->set_body($content);
    $request->send_request();
    $res = new ResponseCore($request->get_response_header(),
        $request->get_response_body(), $request->get_response_code());
    if ($res->isOK()) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    };                 
    */
    public static function putUrl($url,$bucket = ''){
        $arr = self::_comm_sts(); 
        $accessId = $arr['accessId'];
        $accessKey = $arr['accessKey'];
        $SecurityToken = $arr['SecurityToken'];
        $endPoint = $arr['endPoint']; 
        $ossClient = new OssClient($accessId, $accessKey, $endPoint, false, $SecurityToken);   
        $signedUrl = $ossClient->signUrl($bucket, $url, 3600, "PUT"); 
        return $signedUrl;  
    }
    /**
    * RAM 
    */ 
    public static function _comm_sts(){ 
        $accessId  = get_config('aliyun_accessId');
        $accessKey  = get_config('aliyun_accessKey');
        $endPoint  = get_config('aliyun_endPoint');
        $bucket  = get_config('aliyun_bucket'); 
        if(get_config('aliyun_RoleArn')){
            $str = $endPoint;
            $str = str_replace("https://","",$str);
            $str = str_replace("http://","",$str);
            $str = substr($str,strpos($str,'oss-')+4);  
            $RegionId = substr($str,0,strpos($str,'.'));   
            //构建一个阿里云客户端，用于发起请求。
            //设置调用者（RAM用户或RAM角色）的AccessKey ID和AccessKey Secret。
            AlibabaCloud::accessKeyClient($accessId, $accessKey)
                                    ->regionId($RegionId)
                                    ->asDefaultClient();
            //设置参数，发起请求。 
            $result = AlibabaCloud::rpc()
                                  ->product('Sts')
                                  ->scheme('https') // https | http
                                  ->version('2015-04-01')
                                  ->action('AssumeRole')
                                  ->method('POST')
                                  ->host('sts.aliyuncs.com')
                                  ->options([
                                        'query' => [
                                          'RegionId' => $RegionId,
                                          'RoleArn' => $c['aliyun_RoleArn'],
                                          'RoleSessionName' => $c['aliyun_RoleSessionName'],
                                        ],
                                    ])
                                  ->request();
            $info = $result->toArray()['Credentials'];
            $SecurityToken = $info['SecurityToken'];  
            $accessId = $info['AccessKeyId'];  
            $accessKey = $info['AccessKeySecret'];
            $Expiration = $info['Expiration']; 
        } 
        return [
            'aliyun_RoleArn'=>$c['aliyun_RoleArn'],
            'accessId'=>$accessId,
            'accessKey'=>$accessKey,
            'SecurityToken'=>$SecurityToken,
            'endPoint'=>$endPoint,
            'Expiration'=>$Expiration,
            'RegionId'=>$RegionId,
        ];
    }

    public static function initAliyun()
    {
        if (!self::$obj) { 
            $accessId  = get_config('aliyun_accessId');
            $accessKey = get_config('aliyun_accessKey');
            $endPoint  = get_config('aliyun_endPoint');
            self::$bucket = get_config('aliyun_bucket');  
            self::$obj = new OssClient($accessId, $accessKey, $endPoint); 
        }
        return self::$obj;
    }
    /**
     * 上传文件到阿里云OSS
     *
     * @param string $file
     * @param string $content
     * @return void
     */
    public static function upload($file, $object,$options = [])
    {    
        if(strpos($file,WWW_PATH) === false){
            $file = WWW_PATH.$file; 
        }  
        if(!file_exists($file)){  
            trace("oss upload:".$file." not exist",'error'); 
            return ;
        }
        if(substr($object,0,1)=='/'){
            $object = substr($object,1);
        }
        $content = file_get_contents($file); 
        $mime = mime_content_type($file);  
        $options['content-type'] = $mime;        
        $ossClient = self::initAliyun();  
        if(!$ossClient){
            trace("ossClient Error",'error'); 
            return;
        } 
        $bucket_name = self::$bucket;  
        //所有bucket
        $bucketListInfo = $ossClient->listBuckets();
        $bucketList = $bucketListInfo->getBucketList();
        $arr = [];
        foreach($bucketList as $bucket) {
            $name = (string)$bucket->getName();
            $arr[$name] = $name;
        }  
        if(!$arr[$bucket_name]){
            $ossClient->createBucket($bucket_name); 
        }  
        $res = $ossClient->putObject($bucket_name, $object, $content,$options);  
        if($res['info']['url']){
            $url = $res['info']['url'];
            $domain1 = get_config('aliyun_domain');
            if($domain1){
                $url = substr($url,strpos($url,"://")+3);
                $url = substr($url,strpos($url,"/"));
                $url = $domain1.$url;
                add_oss_info('oss',$file,$url);
                return $url;
            }
            add_oss_info('oss',$file,$url);
            return $url;
        }else{
            return ;
        } 
    } 
    public static function lists(){
        $ossClient = self::initAliyun();  
        $listObjectInfo = $ossClient->listObjects(self::$bucket, [
            OssClient::OSS_MAX_KEYS=>1000,
            'delimiter' => '',
        ]); 
        $objectList = $listObjectInfo->getObjectList();
        $key = []; 
        foreach ($objectList as $objectInfo) {
           $key[] = $objectInfo->getKey();
        }  
        return $key;
    }


}
