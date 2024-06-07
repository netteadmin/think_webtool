<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss; 
/**
 * https://github.com/tencentyun/cos-php-sdk-v5
 */ 

class OssCos{
	public $bucket;
	protected function init(){
		$this->bucket = get_config('COS_BUCKET');
		// SECRETID 和 SECRETKEY 请登录访问管理控制台进行查看和管理
		$secretId = get_config('COS_SECRET_ID'); //用户的 SecretId，建议使用子账号密钥，授权遵循最小权限指引，降低使用风险。子账号密钥获取可参考https://cloud.tencent.com/document/product/598/37140
		$secretKey = get_config('COS_SECRET_KEY'); //用户的 SecretKey，建议使用子账号密钥，授权遵循最小权限指引，降低使用风险。子账号密钥获取可参考https://cloud.tencent.com/document/product/598/37140
		$region = get_config('COS_REGION'); //用户的 region，已创建桶归属的 region 可以在控制台查看，https://console.cloud.tencent.com/cos5/bucket
		$cosClient = new \Qcloud\Cos\Client(
		    array(
		        'region' => $region,
		        'schema' => 'https', //协议头部，默认为 http
		        'credentials'=> array(
		            'secretId'  => $secretId ,
		            'secretKey' => $secretKey)));
		return $cosClient;
	}

	public function create_bucket($bucket_name){
		$cosClient = $this->init();
		try {
		    $bucket = $bucket_name; //存储桶名称 格式：BucketName-APPID
		    $result = $cosClient->createBucket(array('Bucket' => $bucket));
		    //请求成功
		    print_r($result);
		} catch (\Exception $e) {
		    //请求失败
		    echo($e);
		}
	} 
	/**
	 * 上传文件
	 * @param $file 本地/uploads/……
	 * @param $object 远程保存路径
	 */
	public function upload($file,$object = ''){
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
        $mime = mime_content_type($file);  
        $options['content-type'] = $mime; 

		$cosClient = $this->init();
		# 上传文件
		## putObject(上传接口，最大支持上传5G文件)
		### 上传内存中的字符串
		try {
		    $bucket = $this->bucket; //存储桶名称 格式：BucketName-APPID 
		    $res = $cosClient->putObject(array(
		        'Bucket' => $bucket,
		        'Key' => $object,
		        'Body' => $content));
		    try {
			    $url = $cosClient->getObjectUrlWithoutSign($bucket, $object);
			    $domain = get_config("COS_DOMAIN");
			    if($domain){
			    	$url = $domain.get_url_remove_http($url); 
			    }
			    add_oss_info('cos',$file,$url);
			    return $url;
			} catch (\Exception $e) {
			    
			}  
		} catch (\Exception $e) {
		     
		}
	}

	/**
	* 列表
	*/
	public function lists(){
		$cosClient = $this->init();
		$keys = [];
		try {
		    $bucket = $this->bucket; //存储桶名称 格式：BucketName-APPID
		    $prefix = ''; //列出对象的前缀
		    $marker = ''; //上次列出对象的断点
		    while (true) {
		        $result = $cosClient->listObjects(array(
		            'Bucket' => $bucket,
		            'Marker' => $marker,
		            'MaxKeys' => 1000 //设置单次查询打印的最大数量，最大为1000
		        ));
		        if (isset($result['Contents'])) {
		            foreach ($result['Contents'] as $rt) {
		                $keys[] = $rt['Key'];
		            }
		        }
		        $marker = $result['NextMarker']; //设置新的断点
		        if (!$result['IsTruncated']) {
		            break; //判断是否已经查询完
		        }
		    }
		} catch (\Exception $e) {
		    echo($e->getMessage());exit;
		} 
		return $keys;
	}
	/**
	* 删除所有
	*/
	public function delete_all(){
		if(!is_cli()) {return;}
		$all = $this->lists();
		$cosClient = $this->init();
		$bucket = $this->bucket; //存储桶，格式：BucketName-APPID  
		if($all){
			$keys = [];
			foreach($all as $key){
				$keys[] = ['Key' => $key];
			}
			$res = $cosClient->deleteObjects(array(
		        'Bucket' => $bucket,
		        'Objects' => $keys,
		    ));
		} 
	}


 	/**
 	 * 下载文件
 	 */
 	public function download($object){
 		$cosClient = $this->init();
 		$result = $cosClient->getObject(array(
			        'Bucket' => $bucket,
			        'Key' => $object));
		$body = $result['Body'];
		return $body;
	}
}

