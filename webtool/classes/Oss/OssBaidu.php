<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss;  
include include __DIR__.'/BaiduBce.phar';
use BaiduBce\Services\Bos\BosClient; 
use BaiduBce\Services\Bos\BosOptions;
use BaiduBce\Auth\signOptions;
use BaiduBce\Services\Bos\StorageClass; 
/** 
*  百度云BOS
*  https://console.bce.baidu.com/bos/#/bos/new/overview
*  https://cloud.baidu.com/doc/BOS/s/bjwvys425
*/ 
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
class OssBaidu
{  
    public  $bucket;  
    protected $client;  

    public function init()
    {
        if($this->client){
            return;
        }
        $BOS_CONFIG   = [
            'credentials' => [
                'ak' => get_config("baidu_yun_account_key"),
                'sk' => get_config("baidu_yun_account_secret"),
            ],
            'endpoint'=> get_config("baidu_yun_bos_endpoint"),
        ];
        $this->bucket = get_config("baidu_yun_bos_bucket");
        $this->client = new BosClient($BOS_CONFIG);  
    }   
    /**
     * 上传 
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
        $mime = mime_content_type($file);  
        $this->init(); 
        try { 
            $options = array(
                BosOptions::STORAGE_CLASS => StorageClass::STANDARD,
                BosOptions::CONTENT_TYPE  => $mime, 
            );
            $res = $this->client->putObjectFromString($this->bucket,$object,$content,$options);
            $url = $this->get_url($object); 
            $domain = get_config("baidu_yun_bos_domain");
            if($url){
                if($domain){
                    $url = $domain.'/'.get_url_remove_http($object); 
                }
                add_oss_info('baidubos',$file,$url);
                return $url;
            } 
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            trace($msg,'error');
        }
    }
    /** 
    * 生成链接
    */
    public function get_url($key,$is_private = false){
        $this->init();
        $opt = [];
        if($is_private){
            $signOptions = array(
                SignOptions::TIMESTAMP=>new \DateTime(),
                SignOptions::EXPIRATION_IN_SECONDS=>300,
            );
            $opt = [BosOptions::SIGN_OPTIONS => $signOptions];
        } 
        $url = $this->client->generatePreSignedUrl($this->bucket,$key,$opt);
        return $url;
    }

}