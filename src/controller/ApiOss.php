<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\rpc\Oss;

/**
* @Apidoc\Title("API 云存储（收费）")
*/
class ApiOss extends ApiController
{   
    public $api; 
    public $guest = false; 
    public function init(){
        parent::init();
        $this->api = new Oss;  
        global $remote_to_local_path;
        $remote_to_local_path = '/uploads/saved/'.date("Y-m-d");
    }
    /**
    * @Apidoc\Title("通用")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("type",type="string",require=true,desc="类型 jd ali  upyun qiniu azure s3 huawei tx")    
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $type = $input['type'];
        $res = $this->api->upload($local_url,$remote_url,$type);
        return json($res);
    }
    /**
    * @Apidoc\Title("上传至京东云")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://jdcloud.com/ <br>OSS访问域名和地域 https://docs.jdcloud.com/cn/object-storage-service/oss-endpont-list")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function jd_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->jd_upload($local_url,$remote_url);
        return json($res);
    }
    /**
    * @Apidoc\Title("上传至阿里云OSS")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br> https://aliyun.com/")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function ali_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->ali_upload($local_url,$remote_url);
        return json($res);
    } 
    /**
    * @Apidoc\Title("上传至又拍云对象存储")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss<br>https://console.upyun.com/dashboard/")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function upyun_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->upyun_upload($local_url,$remote_url);
        return json($res);
    }
    /**
    * @Apidoc\Title("上传至七牛")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://portal.qiniu.com/kodo/bucket/overview")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function qiniu_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->qiniu_upload($local_url,$remote_url);
        return json($res);
    }

    /**
    * @Apidoc\Title("上传至华为云")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://www.huaweicloud.com/product/obs.html")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function huawei_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->huawei_upload($local_url,$remote_url);
        return json($res);
    }
    /**
    * @Apidoc\Title("上传至AmazonS3")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://aws.amazon.com/cn/")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function s3_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->s3_upload($local_url,$remote_url);
        return json($res);
    }
    /**
    * @Apidoc\Title("上传至AzureBlob")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://azure.microsoft.com/zh-cn/free/")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function azure_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->azure_upload($local_url,$remote_url);
        return json($res);
    } 

    /**
    * @Apidoc\Title("上传至百度Bos")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://console.bce.baidu.com/bos/#/bos/new/overview")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function baidu_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->baidu_upload($local_url,$remote_url);
        return json($res);
    }  
    /**
    * @Apidoc\Title("上传至腾讯云COS")
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Oss <br>https://cloud.tencent.com/ 测试发现只有这家第二天就收费，其他未产生费用")
    * @Apidoc\Method("POST") 
    * @Apidoc\Query("local_url",type="string",require=true,desc="本地URL，不包含http部分")    
    * @Apidoc\Query("remote_url",type="string",require=true,desc="保存至远程地址")    
    */
    public function tx_upload(){  
        $input = $this->input;
        $local_url = $input['local_url'];
        $remote_url = $input['remote_url'];
        $res = $this->api->tx_upload($local_url,$remote_url);
        return json($res);
    }

}