<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss; 
/**
 * https://github.com/huaweicloud/huaweicloud-sdk-php-obs
 */ 
use Obs\ObsClient;
use Obs\ObsException;
class OssHuawei{
    public $bucket;
    protected function init(){
        $ak = get_config("HUAWEI_OBS_AK"); 
        $sk = get_config("HUAWEI_OBS_SK"); 
        $endpoint = get_config("HUAWEI_OBS_ENDPOINT");  
        $this->bucket = get_config("HUAWEI_OBS_BUCKET");
        $obsClient = ObsClient::factory ( [ 
                'key' => $ak,
                'secret' => $sk,
                'endpoint' => $endpoint,
                'socket_timeout' => 30,
                'connect_timeout' => 10
        ] );
        return $obsClient;
    }

    public function upload($file ,$object = ''){
        $obsClient = $this->init();
        if(strpos($file,WWW_PATH) === false){
            $file = WWW_PATH.$file; 
        }   
        if(!file_exists($file)){  
            trace("OssHuawei upload:".$file." not exist",'error'); 
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
        $res = $obsClient->putObject([
               'Bucket' => $this->bucket,
               'Key' => $object,
               'Body' => $content
        ]);
        $url = $res->get('ObjectURL');
        if($url){
            $domain = get_config("HUAWEI_OBS_DOMAIN");
            if($domain){
                $url = $domain.get_url_remove_http($url); 
            }
            add_oss_info('huawei',$file,$url);
            return $url;
        }
    }
    /**
    * 列表
    */
    public function lists(){
        $obsClient = $this->init();
        $resp = $obsClient -> listObjects([
            'Bucket' => $this->bucket, 
            'MaxKeys' => 100]);
        $keys = [];
        foreach ( $resp ['Contents'] as $content ) {
            $keys[] = $content ['Key']; 
        } 
        return $keys;
    }
    /**
    * 删除所有
    */
    public function delete_all(){
        if(!is_cli()) {return;}
        $obsClient = $this->init();
        $all = $this->lists();
        $keys = [];
        foreach($all as $key){
            $keys[] = ['Key' => $key]; 
        }
        if($keys){
            $obsClient->deleteObjects([
                'Bucket'=>$this->bucket, 
                'Objects'=>$keys,
                'Quiet'=> false,
            ]);
        } 
    }

}