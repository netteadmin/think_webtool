<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss; 
/**
 * https://github.com/qiniu/php-sdk
 */ 
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
class OssQiniu{
	public $bucket;
	public $up;
	protected function init(){
		$accessKey = get_config("QINIU_ACCESS_KEY");
		$secretKey = get_config("QINIU_SECRET_KEY");
		$this->bucket = get_config("QINIU_BUCKET");
		// 初始化Auth状态
		$auth = new Auth($accessKey, $secretKey);
		$this->up = new UploadManager();
		return $auth;
	}
	/**
	* 信息
	*/
	public function info(){
		$auth = $this->init();
		$config = new Config();
		$bucketManager = new BucketManager($auth, $config);
		list($ret, $err) = $bucketManager->bucketInfo($this->bucket);
		$d = []; 
		//大小有问题
		$d['size'] = \lib\Str::size($ret['storage_size']);
		return $d;
	}
	/**
	* 上传
	*/
	public function upload($file,$object){
		if(strpos($file,WWW_PATH) === false){
            $file = WWW_PATH.$file; 
        }   
        if(!file_exists($file)){  
            trace("OssQiniu upload:".$file." not exist",'error'); 
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
		$auth = $this->init();
		//覆盖上传
		$expires = 3600;
		$returnBody = '{"key":"$(key)","hash":"$(etag)","fsize":$(fsize),"name":"$(x:name)"}';
		$policy = array(
		    'returnBody' => $returnBody,
		    'callbackBodyType' => 'application/json',
		    'scope'=>$this->bucket
		); 
		$token = $auth->uploadToken($this->bucket);
		list($ret, $err) = $this->up->putFile($token,$object,$file);
	  	$k = $ret['key'];
	  	if($k){
	  		$domain = get_config("QINIU_DOMAIN");
	  		$url = $domain.'/'.$k;
	  		add_oss_info('qiniu',$file,$url);
	  		return $url;
	  	} 
	}
	/**
	* 列表
	*/
	public function lists(){
		$auth = $this->init();
		// 要列取文件的公共前缀
		$prefix = '';
		// 上次列举返回的位置标记，作为本次列举的起点信息。
		$marker = '';
		// 本次列举的条目数
		$limit = 100;
		$delimiter = '/';
		$bucketManager = new BucketManager($auth);
		list($ret, $err) = $bucketManager->listFiles($this->bucket, $prefix, $marker, $limit, $delimiter);
		return $ret;
	}

}
