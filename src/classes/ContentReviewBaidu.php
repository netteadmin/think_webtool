<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
class ContentReviewBaidu { 
    public $text;
    public $image;
    public $info; 
    public function  init(){  
        // 你的 APPID AK SK
        $APP_ID = get_config("baidu_yun_app_id");
        $API_KEY = get_config("baidu_yun_app_key");
        $SECRET_KEY = get_config("baidu_yun_app_secret"); 
        $this->text  = new \AipContentCensor($APP_ID, $API_KEY, $SECRET_KEY); 
        $this->image = new \AipImageCensor($APP_ID, $API_KEY, $SECRET_KEY); 
         
    }

    protected function get_init_txt(){
        if(!$this->text){
            $this->init();
        } 
        return $this->text; 
    }

    protected function get_init_image(){
        if(!$this->text){
            $this->init();
        } 
        return $this->image; 
    }
    /**
    * 内容审核
    */
    public function text($word){
        if(!$word){return true;} 
        $client = $this->get_init_txt();
        $nid = webtool_log('百度内容审核','百度内容审核文本',[$word]);
        $res = $client->textCensorUserDefined($word);
        $this->info = $res; 
        if($res['conclusionType'] == 1){
            update_webtool_log($nid,['msg'=>'通过','api_data'=>$res]);
            return true;
        }else{
            update_webtool_log($nid,['msg'=>'未通过','api_data'=>$res],'error');
            return false;
        }       
    }
    /**
    * 图片审核
    */
    public function image($local_file){
        if(strpos($local_file,'://')!==false){
            $local_url = download_file($local_file); 
            $local_file = WWW_PATH.$local_url;
        }else{
            $name = str_replace(WWW_PATH,'',$local_file); 
            $local_file = WWW_PATH.$name;
        } 
        if(!file_exists($local_file)){
            return true;
        }
        $log = str_replace(WWW_PATH,'',$local_file);
        $nid = webtool_log('百度内容审核','百度内容审核图片',[$log]); 
        $client = $this->get_init_txt();
        $res = $client->imageCensorUserDefined(file_get_contents($local_file));
        $this->info = $res;  
        if($res['conclusionType'] == 1){
            update_webtool_log($nid,['msg'=>'通过','api_data'=>$res]);
            return true;
        }else{
            update_webtool_log($nid,['msg'=>'未通过','api_data'=>$res],'error');
            return false;
        }
    } 

}