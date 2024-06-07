<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
/**
* 文档转图片 
*/
class PdfToImage extends DocToPdf{
    public static $title = '文档转图片';
    public static $url = 'https://market.aliyun.com/products/56928005/cmapi00045684.html';
    
    public static function covert($url,$type = ''){
        $req = 'https://all2img.ali.duhuitech.com/v1/convert';
        $type = $type?:get_ext($url);
        $body = [
            'url'=>$url,
            'type'=>$type,
            'outtype'=>1
        ]; 
        $nid = webtool_log('DocToPdf','文档转图片-'.$url,$body);
        $res = curl_aliyun($req,$body,'GET');  
        if($res['code'] != 0){
            trace($res['msg'],'error');
            update_webtool_log($nid,['api_data'=>$res],'error');
            return $res; 
        }
        $token = $res['result']['token'];
        update_webtool_log($nid,['token'=>$token,'api_data'=>$res]);
        return $token;
    }

    public static function get($token){ 
        $url = 'https://api.duhuitech.com/q?token='.$token;
        $res = get_remote_file($url,true);
        $urls = $res['result']['imageurls'];
        $out = []; 
        if($urls) { 
            foreach($urls as $v){
                $out[] = download_remote_file($v);
            }
        }
        return ['url'=>$out];
    }
}