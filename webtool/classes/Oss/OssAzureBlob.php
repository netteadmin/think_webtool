<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss;  
/** 
* https://github.com/Azure/azure-sdk-for-php/
* 
* https://azure.microsoft.com/zh-cn/free/
*/ 
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
class OssAzureBlob
{ 
	public $blobRestProxy;
	public $bucket;
	protected function init(){
		$this->bucket = get_config('AZURE_CONTAINER');
		$connectionString="Endpoint=".get_config('AZURE_ENDPOINT').";SharedSecretIssuer=".get_config('AZURE_ISSUER').";SharedSecretValue=".get_config('AZURE_SECRET'); 
		$this->blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	}
    /**
     * 上传
     *
     * @param string $local_url 本地URL
     * @param string $remote_url 上传到远程地址
     * @return array
     */
    public function upload($file,$object = ''){
        $this->init();
    	if(strpos($file,WWW_PATH) === false){
            $file = WWW_PATH.$file; 
        }  
        if(!file_exists($file)){  
            trace("oss upload:".$file." not exist",'error'); 
            return ;
        }
        if(!$object){
        	$object = create_oss_remote_url($file);
        }
        if(substr($object,0,1)=='/'){
            $object = substr($object,1);
        }
        $content = file_get_contents($file);  
        $res = $this->blobRestProxy->createBlockBlob($this->bucket, $object, $content); 
        pr($res);
        return $res;
    }
    

 
}
