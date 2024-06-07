<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
use app\webtool\classes\IdPhoto\Drive;
class IdPhoto{  
    /**
    * 取规格
    */
    public  function get_spec(){ 
      $key = "id_photo_spec";
      $d = cache($key);
      if(!$d){ 
          $file = __DIR__.'/IdPhoto/spec.php';
          $d = include $file;
          cache($key,$d);
      }  
      return $d;
    }
 
    /**
    * 制作证件照
    * [
    *   'file'=>'本地文件'
    * ]
    */
    public  function make($arr){ 
        $file = $arr['file'];
        $new_url = download_file($file);
        $file = WWW_PATH.$new_url;
        $arr['file'] = $file;

        $nid = webtool_log('IdPhoto','证件照片-制作证件照（有水印）',$arr); 
        $res = Drive::make($arr);
        if($res['top']){
            $res['top'] = download_file($res['top'],true);
        }
        if($res['img']){
            $res['img'] = download_file($res['img'],true);
        }
        update_webtool_log($nid,['res'=>$res]);
        return $res;
    }
    /**
    * 制作证件照(去水印)
    */
    public  function get_make_no_water($file_name){
      $nid = webtool_log('IdPhoto','证件照片-制作证件照（去水印）',['file_name'=>$file_name]);
      $res = $this->get_no_water($file_name,1);
      update_webtool_log($nid,['res'=>$res]);
      return $res;
    }

    /**
    * 制作正装
    */
    public  function make_ai($arr){ 
        $file = $arr['file'];
        $new_url = download_file($file);
        $file = WWW_PATH.$new_url;
        $arr['file'] = $file;
        $nid = webtool_log('IdPhoto','证件照片-制作正装（有水印）',$arr);
        $res = Drive::ai($arr);
        update_webtool_log($nid,['res'=>$res]);
        return $res;
    }
    /**
    * 制作正装(去水印)
    */
    public  function get_ai_no_water($file_name){
      $nid = webtool_log('IdPhoto','证件照片-制作正装（去水印）',['file_name'=>$file_name]);
      $res = $this->get_no_water($file_name,2);
      update_webtool_log($nid,['res'=>$res]);
      return $res;
    }
    /**
    * 正装图片数组
    */
    public  function get_ai_clothes(){
        webtool_log('IdPhoto','证件照片-正装图片数组',[],true);  
        return Drive::clothes($arr); 
    } 
    /**
    * 取无水印照
    */
    public  function get_no_water($file_name,$app_key){  
        return Drive::get_no_water($file_name,$app_key);
    }


}


 