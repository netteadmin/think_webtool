<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
 
/**
* 人脸  
*/
class ImageFace{  
    /**
    * 取人脸坐标点
    */
    public function get_face_detection($url){
        $new_url = download_file($url);
        $file_1 = WWW_PATH.$new_url;
        if(!file_exists($file_1)){
            return;
        }
        $py = __DIR__.'/ImageFace/face_detection.php';
        $cmd = "/usr/bin/python3  $py  $file_1 2>&1";
        exec($cmd,$o); 
        pr($o);
        foreach($o as $v){
            if(is_json($v)){
                $d = json_decode($v,true);
                return $d;
            }
         } 
    }

    /**
    * 计算肩膀位置
    */
    public function get_shoulder($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3){ 
        // 计算人脸中心点
        $x_c = ($x0 + $x1 + $x2 + $x3) / 4;
        $y_c = ($y0 + $y1 + $y2 + $y3) / 4;        
        // 计算肩膀位置
        $x_s = $x_c;
        $y_s = $y3 + 2 * ($y3 - $y_c); 
        return array($x_s, $y_s); 
    }
}