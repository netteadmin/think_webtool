<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
use helper_v3\Pdf;
/**
* 文档转PDF  
*/
class DocToPdf{
    public static $title = '文档转PDF';
    public static $url = 'https://market.aliyun.com/products/56928005/cmapi00044564.html';
    public static function covert($url,$type = ''){
        $req = 'https://ali.duhuitech.com/v1/convert';
        $type = get_ext($url);
        $body = [
            'url'=>urlencode($url),
            'type'=>$type,
            //如果是图片文件，是否识别图中文字并且在PDF中可选可搜索文字，默认0否，1是
            'imageocr'=>1,
            // 如果是图片文件，是否将斜的文字矫正，默认0否，1是
            'imagedeskew'=>1,
            //如果是Excel文件，是否横屏，默认0否（竖屏），1是
            //'excelislandscape'=>1,
            //如果是Excel文件，不显示网格线，默认0显示，1不显示
            'excelnotshowgridlines'=>1,
        ]; 
        $nid = webtool_log('DocToPdf','文档转PDF',$body);
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
        $name = '/uploads/tmp/'.$token.'.pdf';
        $file = WWW_PATH.$name; 
        $url = 'https://api.duhuitech.com/q?token='.$token;
        $res = get_remote_file($url,true); 
        $pdfurl = $res['result']['pdfurl'];
        $out = [];
        if($pdfurl) {  
            $out['url']  = download_remote_file($pdfurl);
            $file = WWW_PATH.get_url_remove_http($out['url']); 
            $out['pages'] = Pdf::get_pages($file,false);
        }else{
            return $res['result'];
        }
        return $out;
    } 

} 
