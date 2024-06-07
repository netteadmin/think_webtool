<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;
/*
https://github.com/nadermx/backgroundremover 
pip3 install backgroundremover  
*/
/**
* 图片移除背景  
*/
class ImageBackground{  
	/**
     * 图片换底色
     */
    public function add_bg($url,$color = 'blue',$size = ''){
        $new_url = download_file($url);
        $file_1 = WWW_PATH.$new_url;
        if(!file_exists($file_1)){
            return;
        }
        if($size && in_array($size,['1','1x','2','2x'])){
        	$res = $this->remove_bg($url);
        	$path = $res['path'];
        	if($res['status'] != 'ok'){
        		return [
        			'status'=>'wait',
        			'tag'=>'size'
        		];
        	}
        	$file_1 = WWW_PATH.$path;
        	$top1 = host().$path;
        }
        $color_t = str_replace("#","",$color);
        $output_url = $new_url.'_addbg_'.$color_t.'.png';
        $file_2 = WWW_PATH.$output_url;  
        $cmd = "convert $file_1 -background '".$color."'  -flatten $file_2";
        add_webtool_cmd_job($cmd);
        $st = 'wait';
        if(file_exists($file_2)){
            $st = 'ok';
        } 
        if($st == 'ok'){
        	$top2 = host().$output_url;
        	if($size && in_array($size,['1','1x','2','2x'])){ 
    			$p = new ImgToPaper;
	        	$num = 8;
	        	if(strpos($size,2)!==false){
	        		$num = 4;
	        	} 
	        	$url = $p->create_3($title='',$output_url,$size,$num); 
	        	return [
		        	'url'=>$url, 
		        	'image_remove_bg'=>$top1, 
		        	'image_add_bg'=>$top2,  
		        	'status'=>$st, 
		        	'size'=>$size, 
		        	'tag'=>'size'
		        ];  
        	}else{
        		return [
        			'url'=>host().$output_url,
        			'status'=>"error",
        			'tag'=>'size'
        		];
        	}
        	
        }	
        return [
        	'url'=>host().$output_url,
        	'ori_url'=>host().$output_url,
        	'status'=>$st,
        	'path'=>$output_url,
        	'tag'=>'base'
        ];
    }

	
    /**
    * 图片移除背景
    */
    public  function remove_bg($url){ 
    	$new_url = download_file($url);
    	$file_1 = WWW_PATH.$new_url;
    	if(!file_exists($file_1)){
    		return;
    	}
    	$output_url = $new_url.'_remove_bg_.png';
    	$file_2 = WWW_PATH.$output_url;
    	$backgroundremover = $this->get_backgroundremover();
    	$cmd = "$backgroundremover -i  '".$file_1."' -a -ae 30 -ab 20  -o '".$file_2."'";
    	add_webtool_cmd_job($cmd);
    	$st = 'wait';
    	if(file_exists($file_2)){
    		$st = 'ok';
    	}
    	return ['url'=>host().$output_url,'status'=>$st,'path'=>$output_url];
	}
	/**
	* 提取头像
	*/
	public function get_avatar($url){
		$new_url = download_file($url);
    	$file_1 = WWW_PATH.$new_url;
    	if(!file_exists($file_1)){
    		return;
    	}
    	$output_url = $new_url."_get_avatar_.png";
    	$file_2 = WWW_PATH.$output_url;
    	$backgroundremover = $this->get_backgroundremover();
		$cmd = "$backgroundremover -i '".$file_1."' -m u2net_human_seg -o '".$file_2."'";
		add_webtool_cmd_job($cmd);
		$st = 'wait';
    	if(file_exists($file_2)){
    		$st = 'ok';
    	}
    	return ['url'=>host().$output_url,'status'=>$st,'path'=>$output_url];
	}

	protected function get_backgroundremover(){
		return get_config('backgroundremover')?:"/opt/rh/rh-python38/root/usr/local/bin/backgroundremover";
	}

}

