<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;   
use app\applet\classes\weixin;
/**
 *  微信小程序
 */ 
class WeMinProgram extends WeProgram
{ 
    public $config;
    public $app;
    public $api;
    public $utils; 
    public function init($opt = []){ 
        $app_id = $opt['appid'];
        $secret = $opt['secret'];
        $app   = weixin::init($app_id,$secret);  
        $this->config = $app['config'];
        $this->app    = $app['app'];
        $this->api    = $app['api'];
        $this->utils  = $app['utils']; 
    }
    /**
    * 获取不限制的小程序码  
    * @param $path   
    */
    public function getUnlimitedQRCode($opt=[]){
        $page = $opt['page'];
        $config_info = $opt['config_info'];
        $scene = $opt['scene'];
        $env_version = $opt['env_version'];
        $return_url = $opt['return_url']; 
        $width = $opt['width']?:280; 
        $env_version = $env_version?:'release';
        $page = $page?:'pages/index/index';
        if(substr($page,0,1)=='/'){
            $page = substr($page,1);
        }
        $app_id = $this->config['app_id'];
        $url = "/uploads/WeMinProgram/getUnlimitedQRCode.".md5($app_id.$page.$scene.$env_version).'.jpg';
        $file = WWW_PATH.$url;
        if(file_exists($file)){
            if($return_url){
                return $url;
            }
            return success_data(cdn_url().$url);
        }
        if($config_info){
            $this->init($config_info);
        }
        $arr =  [
            "width" => $width,   
            "page" => $page,   
            "check_path"=>false, 
        ];
        if($scene){
            $arr['scene'] = $scene;
        }
        if($env_version){
            $arr['env_version'] = $env_version;
        }  
        $res = $this->api->postJson('/wxa/getwxacodeunlimit',$arr);  
        if($res->isSuccessful()){
            $c = $res->getContent();     
            $dir = get_dir($file);
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }
            file_put_contents($file,$c);
            if($return_url){
                return $url;
            }
            return success_data(cdn_url().$url);
        }else{
            return error_data('生成小程序码失败');    
        }        
    }

    /**
    * 文本内容安全识别
    * @param $config_info  {appid,secret}
    * @param $content   需检测的文本内容，文本字数的上限为2500字，需使用UTF-8编码
    * @param $scene   场景枚举值（1 资料；2 评论；3 论坛；4 社交日志）
    * @param $openid 用户的openid（用户需在近两小时访问过小程序）    
    */
    public function msgSecCheck($opt=[]){
        $config_info = $opt['config_info'];
        $content = $opt['content'];
        $openid = $opt['openid'];
        $scene = $opt['scene']?:1; 
        $this->init($config_info);
        $scene = $scene?:1;
        $dd = [
            "openid"  => $openid,  
            "content" => $content, 
            "version" => 2,
            "scene"   => $scene,
        ]; 
        $res = $this->api->postJson('/wxa/msg_sec_check', $dd);   
        if($res->isSuccessful()){
           $c = json_decode($res->getContent(),true); 
           if($c['errcode'] == 0){
                //有risky、pass、review三种值
                $suggest = $c['result']['suggest'];
                $label = $c['result']['label'];
                $in = [
                    100=>'正常',
                    10001 =>'广告',
                    20001 => '时政',
                    20002 => '色情',
                    20003 =>'辱骂',
                    20006 =>'违法犯罪',
                    20008 =>'欺诈',
                    20012 =>'低俗',
                    20013 =>'版权',
                    21000 =>'其他'
                ];
                $a = $in[$label];
                if($suggest == 'pass'){
                    return success_data([
                        'suggest'=>$suggest,
                        'label'=>$a,
                    ]);
                }else{
                    return error_data([
                        'suggest'=>$suggest,
                        'label'=>$a,
                    ]);
                }
                
           }
        }else{
           return error_data('文本内容安全识别失败'); 
        }

    } 
    /**
    * 条形码识别
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/img/scanQRCode.html
    */
    public function qrcode($url){ 
        $url = download_file($url); 
        $new_url = host().$url; 
        $res = $this->api->postJson('/cv/img/qrcode?img_url='.urlencode($new_url),[
            "type"   => 'photo', 
        ]); 
        $res = json_decode($res->getContent(),true); 
        if($res['errcode'] == 0){
            $err = false;
            $res['type'] = strtolower($res['type']);
        }
        if($err){
            return error_data($res); 
        }else{
            return success_data($res);
        }
    }
    /**
    * 身份证识别 （收费）
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/idCardOCR.html
    */
    public function idcard($url,$ocr_url = 'idcard'){ 
        $url = download_file($url); 
        $new_url = host().$url; 
        $res = $this->api->postJson('/cv/ocr/'.$ocr_url.'?img_url='.urlencode($new_url),[
            "type"   => 'photo', 
        ]); 
        $err = true; 
        $res = json_decode($res->getContent(),true); 
        if($res['errcode'] == 0){
            $err = false;
            $res['type'] = strtolower($res['type']);
        }
        if($res['errcode'] == '101003'){
            $res['msg'] = '需前往 https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415 购买微信OCR识别';
        }
        if($err){
            return error_data($res); 
        }else{
            return success_data($res);
        }
    }
    /**
    * 驾驶证识别 行驶证识别 （收费）
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/driverLicenseOCR.html
    */
    public function driving($url){
        return $this->idcard($url,$ocr_url = 'drivinglicense');
    } 

    /**
    * 营业执照识别 （收费）
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/businessLicenseOCR.html
    */
    public function bizlicense($url){
        return $this->idcard($url,$ocr_url = 'bizlicense');
    } 

    /**
    * 银行卡识别 （收费）
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/bankCardOCR.html
    */
    public function bankcard($url){
        return $this->idcard($url,$ocr_url = 'bankcard');
    } 

    /**
    * 通用印刷体识别（收费）
    * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/ocr/printedTextOCR.html
    */
    public function comm($url){
        return $this->idcard($url,$ocr_url = 'comm');
    } 

    /**
    * 强制刷新access token
    */
    public function refresh_token($opt = []){
        $this->init($opt);
        $this->app->getAccessToken()->refresh();
    }  
    
    /**
    * 自动刷新
    */
    protected function auto_refresh_token($res){
        $c = json_decode($res->getContent(),true); 
        if($c['errcode'] && $c['errcode'] == 40001){
            $this->refresh_token();
        }else{
            return $c;
        }
    }
}