<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Oss;  
/** 
* https://github.com/Azure/azure-sdk-for-php/
* 
* https://aws.amazon.com/cn/
*/ 
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
class OssAmazonS3
{ 
    public $s3;
    public $bucket;
    protected function init(){
        if($this->s3){
            return;
        }
        $this->bucket = get_config("AMAZON_S3_BUCKET");
        $credentials = new Credentials(get_config("AMAZON_S3_KEY"), get_config("AMAZON_S3_SECRET"));  
        $this->s3 = new S3Client([
            'version' => 'latest', 
            'endpoint'=> get_config("AMAZON_S3_ENDPOINT"),
            'region'  => get_config("AMAZON_S3_REGION"),
            'credentials' => $credentials
        ]);
    }
    /**
     * 上传
     * https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-s3-2006-03-01.html#putobject
     * @param string $local_url 本地URL
     * @param string $remote_url 上传到远程地址
     * @return array
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
        $this->init(); 
        try {
            $res = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $object,
                'Body'   => $content,
                'ACL'    => 'public-read',
                'ContentType'=>$mime,
            ]); 
            $url = $res['ObjectURL'];
            $domain = get_config("AMAZON_S3_DOMAIN");
            if($url){
                if($domain){
                    $url = $domain.get_url_remove_http($url); 
                }
                add_oss_info('S3',$file,$url);
                return $url;
            } 
        } catch (\Aws\S3\Exception\S3Exception $e) {
            $msg = $e->getMessage();
            trace($msg,'error');
        }
    }
    /**
     * 下载文件
     * 返回 base64内容
     */
    public function download($object){
        $this->init();
        $res = $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key' => $object
        ]);
        $body = $res['Body'];
        return success_data(base64_encode($body));
    }
    /**
     * 列表
     */
    public function lists(){
        $this->init();
        $all = $this->s3->listObjects([
            'Bucket' => $this->bucket, 
        ])['Contents'];
        $domain = get_config("JD_DOMAIN");
        foreach($all as &$v){
            $v['new_url'] = $domain.'/'.$v['Key'];
        }
        return $all;
    }
    /**
     * 删除一个
     */
    public function delete($Key){
        if(!is_cli()) {return;}
        $this->init();
        $res = $this->s3->deleteObject([
            'Bucket' => $this->bucket, 
            'Key' => $Key, 
        ]); 
    }
    /**
     * 删除所有
     */
    public function delete_all(){
        if(!is_cli()) {return;}
        $all = $this->lists();
        foreach($all as $v){
            $key = $v['Key'];
            $this->delete($key);
        }
    }

}