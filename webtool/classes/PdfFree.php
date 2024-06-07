<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
use helper_v3\Pdf;
 
class PdfFree{
    /**
    * doc docs xls ppt转pdf
    */
    public function word_to_pdf($url){
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $md5 = md5($url);
        $name = $md5.'.pdf'; 
        $url_out = '/uploads/free_pdf/word_to_pdf/'.date('Y-m-d').'/'.$md5;
        $dir = WWW_PATH.$url_out;
        create_dir_if_not_exists([$dir]);
        $cmd = "libreoffice --headless --convert-to pdf --outdir $dir  $file";  
        exec($cmd);    
        $old_file = $dir.substr($new_url,strrpos($new_url,'/'));
        $old_file = get_dir($old_file).'/'.get_name($old_file).'.pdf'; 
        $new_url = str_replace(WWW_PATH,'',$old_file); 
        return success_data(['url'=>host().$new_url]);
    }
    /**
    * PDF总页数
    */
    public function get_pages($url,$return_array = true){ 
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;  
        $pages = Pdf::get_pages($file);
        if(!$return_array){
            return $pages;
        }
        return success_data(['pages'=>$pages]);
    }

    protected function init($option = []){
        $option['tempDir'] = PATH.'/runtime/mpdf';
        return Pdf::init($option);
    }

    /**
    * 根据HTML生成pdf
    * https://mpdf.github.io/
    * @param $option 参数
    */
    public function create_html($option = [],$html_code){
        $mpdf = $this->init($option);
        $mpdf->WriteHTML($html_code);
        $url = '/uploads/free_pdf/html/'.date('Y-m-d').'/'.md5($html_code).'.pdf';
        $file = WWW_PATH.$url;
        $dir = get_dir($file);
        create_dir_if_not_exists([$dir]);
        $mpdf->Output($file);
        return success_data(['url'=>host().$url]);
    }
    /**
    * PDF合并，支持pdf、image合并成一个PDF
    */
    public function merger($url){
        if(strpos($url,',')!==false){
            $url = explode(',',$url);
        } 
        if(is_array($url)){
            foreach($url as $f){
                $new_url = download_file($f);
                $file[] = WWW_PATH.$new_url;
            }
            $md5 = md5(json_encode($url));
        }else{
            $new_url = download_file($url);
            $file = WWW_PATH.$new_url;
            $md5 = md5($file);
        } 
        $url = '/uploads/free_pdf/merger/'.date('Y-m-d').'/'.$md5.'.pdf';
        $output = WWW_PATH.$url;
        if(!file_exists($output)){
            Pdf::merger2($file,$output);    
        }        
        return success_data(['url'=>host().$url]);
    } 
    /**
    * 图片转为PDF,支持多图数组
    */
    public function image_to_pdf($url){
        $file = '';
        if(is_array($url)){
            foreach($url as $f){
                $new_url = download_file($f);
                $file[] = WWW_PATH.$new_url;
            }
            $md5 = md5(json_encode($url));
        }else{
            $new_url = download_file($url);
            $file = WWW_PATH.$new_url;
            $md5 = md5($file);
        } 
        $url = '/uploads/free_pdf/image_to_pdf/'.date('Y-m-d').'/'.$md5.'.pdf';
        $output = WWW_PATH.$url;
        if(!file_exists($output)){
            Pdf::image_to_pdf($file,$output);    
        }        
        return success_data(['url'=>host().$url]);
    }
    /*
    * PDF导出图片
    */
    public function pdf_to_image($url){
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $md5 = md5($file);
        $tmp = '/uploads/free_pdf/to_image/'.date('Y-m-d').'/'.$md5; 
        $last = WWW_PATH.$tmp.'/'.$md5.'-1.jpg';
        if(!file_exists($last)){
           Pdf::pdf_to_image($file,WWW_PATH.$tmp);    
        }        
        $list = [];
        $all = glob(WWW_PATH.$tmp.'/*.jpg');
        foreach($all as $name){
            $list[] = host().str_replace(WWW_PATH,'',$name);
        }
        return success_data(['url'=>$list]);
    }

}