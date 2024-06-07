<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss; 
/**
 * https://github.com/upyun/php-sdk
 */ 
use UpYun;
class OssUpyun{

	protected function init(){   
		$client = new UpYun(get_config("UPYUN_SERVER_NAME"), get_config("UPYUN_ACCOUNT"), get_config("UPYUN_PASSWORD"));
		return $client;
	}
	/**
	* 上传
	*/
	public function upload($file,$object = ''){
		$client = $this->init();
		if(strpos($file,WWW_PATH) === false){
            $file = WWW_PATH.$file; 
        }   
        if(!file_exists($file)){  
            trace("OssUpyun upload:".$file." not exist",'error'); 
            return ;
        }
        if(!$object){
        	$object = create_oss_remote_url($file);
        }
        if(substr($object,0,1)!='/'){
            $object = '/'.$object;
        } 
        $content = file_get_contents($file); 
        $mime = mime_content_type($file);  
        $options['content-type'] = $mime;  
		$res = $client->writeFile($object, $content);
		if($res){
			$url = get_config("UPYUN_DOMAIN").$object;
			add_oss_info('upyun',$file,$url);
			return $url;
		}
		
	}
	/**
	* 信息
	*/
	public function info(){
		$client = $this->init();
		$info['size'] = \lib\Str::size($client->getFolderUsage());
		return $info;
	}
	/**
	* 列表
	*/
	public function lists($path = '/')
	{
	    $client = $this->init();  
	    $all = $client->getList($path);
	    $list = []; 
	    foreach ($all as $v) { 
	        if ($v['type'] == 'folder') {  
	            $new_path = $path . $v['name'] . '/';
	            $list = array_merge($list, $this->lists($new_path));
	        } else {
	            $list[] = $path.$v['name'];
	        }
	    } 
	    return $list;
	}

	  
	/**
	* 删除
	*/
	public function delete_all(){
		if(!is_cli()) {return;}
		$client = $this->init();
		$all = $this->lists();
		if($all){
			$dirs = [];
			foreach($all as $v){
				$dirs[] = get_dir($v); 
				$client->delete($v); 
			}
			foreach($dirs as $dir){
				$client->delete($dir);
			}
		} 
	}


}
