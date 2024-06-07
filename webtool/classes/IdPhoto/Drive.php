<?php 
/**
 * 制作证件照
 * http://dev.id-photo-verify.com/doc.html 
 */
namespace app\webtool\classes\IdPhoto;  
use GuzzleHttp\Client;  
class Drive{
	public static $url;
	public static $app_key;
	public static $local_file;
	public static $option; 
	/**
	 * 制作证件照
	 * http://dev.id-photo-verify.com/doc.html
	    调用此接口不会增加已调用次数 
	    带水印图片存储时间为 1 天，无水印图片存储时间为 7 天 
	    通过名字获取带水印图片不会增加已调用次数 
       （通过获取图片的api获取无水印图片或无水印已排版图片会增加 1 次调用次数）
	 */
	public static function make($arr = []){
		$local_file = $arr['file'];
		unset($arr['file']);
		//spec_id 已有的规格ID,见页面上方的‘照片规格列表’ 
		//is_fair 是否美颜，默认为美颜 
		//fair_level 美颜等级，分为1,2,3,4,5等级，支持字典形式传输详见下方“参数说明3”，只在is_fair为1时有效 
		$arr['spec_id'] = $arr['spec_id']?:1;
		$arr['is_fair'] = $arr['is_fair']?:1;
		$arr['fair_level'] = $arr['fair_level']?:3; 
		self::$option = $arr; 
		self::$url = 'http://apicall.id-photo-verify.com/api/cut_check_pic';
		self::$app_key = get_config("printer_ai_key");  
		self::$local_file = $local_file; 
		$res = self::do();
		if($res['code'] == 200) {
			$top = $res['result']['img_wm_url_list'][0];
			$img = $res['result']['print_wm_url_list'][0];
			if(!$img){
				$img = $top;
			}
			$relative_path = '/uploads/tmp/';
			$top_dir = WWW_PATH.$relative_path;

			if(!is_dir($top_dir)){
				mkdir($top_dir,0777,true);
			}
			$name = md5($top);
			$f1 = $top_dir.$name.'.jpg';
			$f1_file = $relative_path.$name.'.jpg';
			file_put_contents($f1,file_get_contents($top));   
			$file_name = $res['result']['file_name'][0];  
			$new_list = [
				'top' => $top,
				'img' => $img, 
				'file_name' => $file_name,  
				'app_key' => self::$app_key,
			];
			return $new_list;
		}else{
			trace("制作失败".$res['error'],'error');
		}
		return ['error'=>$res['error']]; 
	}
	/*
	$res_2 = self::get($file_name,self::$app_key);
	* @param $app_key 1为换背景色、美颜，2为换正装
	*/
	public static function get_no_water($file_name,$app_key = null,$option = [])
	{
		if($app_key == 1){
			$app_key = get_config("printer_ai_key");
		} else if($app_key == 2){
			$app_key = get_config("printer_ai_change_dress"); 
		} 
		self::$url = 'http://apicall.id-photo-verify.com/api/take_cut_pic_v2';
		self::$app_key = $app_key;  
		self::$local_file = $local_file; 
		self::$option = ['file_name'=>$file_name];  
		$res = self::do();  
		$list = [];
		$relative_path = '/uploads/tmp/'; 
		$top_dir = WWW_PATH.$relative_path;
		if(!is_dir($top_dir)){
			mkdir($top_dir,0777,true);
		}
		if($res['data']['file_name']){
			$top = $res['data']['file_name'];
			$name = md5($top);
			$f1 = $top_dir.$name.'.jpg';
			$f1_file = $relative_path.$name.'.jpg';
			file_put_contents($f1,file_get_contents($top));
			$list['top'] = host().$relative_path.$name.'.jpg';
		}
		if($res['data']['file_name_list']){
			$img = $res['data']['file_name_list'];
			$name = md5($img);
			$f2 = $top_dir.$name.'.jpg';
			file_put_contents($f2,file_get_contents($img)); 
	        if(!$list['img']){
	        	$list['img'] = host().$relative_path.$name.'.jpg'; 
	        } 
		}
		if($list){
			return $list;	
		}else{
			return $res;
		}		
	}

	/**
	 * 剪裁换正装,返回有水印的照片
	 */
	public static function ai($arr = []){
		$local_file = $arr['file'];
		unset($arr['file']); 
		$arr['origin_pic_name'] = $local_file;
		//fair_level 美颜级别（默认为0，代表不美颜，级别0-1，美颜程度依次增强）
		//spec_id 已有的规格ID,见页面上方的‘照片规格列表’ 
		self::$url = 'http://apicall.id-photo-verify.com/api/cut_change_clothes';
		self::$app_key = get_config("printer_ai_change_dress");  
		self::$local_file = $local_file; 
		unset($arr['origin_pic_name']);
		self::$option = $arr;  
		$res = self::do();   
		if($res['code'] == 200) {
			$top = $res['big_pic_wm_url'];
			$img = $res['print_wm_pic_url'][0];
			if(!$img){
				$img = $top;
			}	 
			$file_name1 = $res['final_pic_name'][0]; 
			$file_name2 = $res['print_pic_name'][0]; 
			if($top){$top = download_file($top,true);}
			if($img){$img = download_file($img,true);}
			$new_list = [
				'top' => $top,
				'img' => $img, 
				'file_name'=>$file_name2, 
				'topfile_name'=>$file_name1,  
				'app_key' => 2,
			];
			return $new_list;
		} 
		return ['error'=>$res['error']]; 
	}
	/**
	 * 返回 正装图片数组
	 */
	public static function clothes(){
		$dir = WWW_PATH.'/img/clothes/*'; 
		$all = glob($dir);
		foreach($all as $v){
			$name = substr($v,strrpos($v,'/')+1);
			$new_arr = [];
			$new_list = glob(WWW_PATH.'/img/clothes/'.$name.'/*');
			foreach($new_list as $vv){
				$name2 = substr($vv,strrpos($vv,'/')+1);
				$new_arr[get_name($vv)] = host().'/img/clothes/'.$name.'/'.$name2;
			}
			$list[$name] = $new_arr;
		}
		return $list;
	}
	

	public static function do(){
		$client = new Client;
		self::$option['app_key'] = self::$app_key;
		if(self::$local_file){
			self::$option['file'] = base64_encode(file_get_contents(self::$local_file));
		}
		self::$option['ppi'] = 300;
		$res = $client->post(self::$url,
                [
                    'json' => self::$option,
                ]
            );
       $res = json_decode($res->getBody()->getContents(),true);
       return $res;
	} 
} 