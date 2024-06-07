<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use lib\Map as Service;
/**
* textin平台
* https://www.textin.com/
*/
class SerTextin{  
	public $key1='';
	public $key2='';
	public function __construct(){
		$this->key1 = get_config("textin_app_id");
		$this->key2 = get_config("textin_secret_code"); 
	}
    /**
    * 图像切边增强
    * https://www.textin.com/market/detail/crop_enhance_image
    */
    public function crop_enhance_image($url,$options = []){
        $ori_url = $url;
        $nid = webtool_log('textin','图像切边增强',['image'=>$ori_url]); 
        $new_url = download_file($url); 
        $file = WWW_PATH.$new_url; 
        $content = file_get_contents($file); 
        $url =  'https://api.textin.com/ai/service/v1/crop_enhance_image';
        $opt = [];
        $opt['enhance_mode'] = isset($options['enhance_mode'])?$options['enhance_mode']:-1;
        $opt['only_position'] = 0;
        $opt['crop_image'] = 1;
        $opt['correct_direction']=1;
        $opt['crop_scene'] = isset($options['crop_scene'])?$options['crop_scene']:1;
        $url = $url."?".http_build_query($opt);  
        $res = $this->get_res($url,$content); 
        $code = $res['code']; 
        if($code != 200){
            return error_data(['msg'=>$this->code_message($code)]);
        }  
        $image = base64_decode($res['result']['image_list'][0]['image']);
        if($image){
            $new_url = $this->new_url('crop_enhance_image',$ori_url,$image);
            update_webtool_log($nid,['api_data'=>$new_url],'ok');
            return success_data(['url'=>$new_url]); 
        }else{
            update_webtool_log($nid,['api_data'=>''],'error');
            return error_data(['res'=>$res]); 
        }
    } 
	/**
	* 身份证识别
	* https://www.textin.com/document/id_card
	*/
	public function id_card($url,$options = []){
		$ori_url = $url;
        $nid = webtool_log('ocr','身份证识别',['image'=>$ori_url]); 
        $new_url = download_file($url); 
        $file = WWW_PATH.$new_url; 
        $content = file_get_contents($file); 
        $url =  'https://api.textin.com/robot/v1.0/api/id_card';
		$res = $this->get_res($url,$content); 
        $code = $res['code'];
        if($code != 200){
        	return error_data(['msg'=>$this->code_message($code)]);
        }  
        $image = base64_decode($res['result']['image']);
        if($image){
            $new_url = $this->new_url('text_auto_removal',$ori_url,$image);
        	update_webtool_log($nid,['api_data'=>$new_url],'ok');
            return success_data(['url'=>$new_url]); 
        }else{
        	update_webtool_log($nid,['api_data'=>$res],'error');
            return error_data(['res'=>$res]); 
        }
	}
	/**
	* 智能试卷擦除(推荐)
    * https://www.textin.com/market/detail/text_auto_removal
	*/
	public function text_auto_removal($url,$options = []){
		$ori_url = $url;
        $nid = webtool_log('ocr','智能试卷擦除',['image'=>$ori_url]);
        $req = 'https://sjccup.market.alicloudapi.com/sjccup';
        $new_url = download_file($url); 
        $file = WWW_PATH.$new_url; 
        $content = file_get_contents($file); 
        $url = 'https://api.textin.com/ai/service/v1/handwritten_erase'; 
        $crop = g("crop")?:0;
        $crop = $options['crop']?:$crop;
        $dewarp = g("dewarp")?:0;
        $dewarp = $options['dewarp']?:$dewarp;
        $binarization = g("binarization")?:0;
        $binarization = $options['binarization']?:$binarization;
        $doc_direction = g("doc_direction")?:0; 
        $doc_direction = $options['doc_direction']?:$doc_direction;
        $crop_position = g("crop_position")?:0; 
        $crop_position = $options['crop_position']?:$crop_position;
        $mask_position = g("mask_position")?:0; 
        $mask_position = $options['mask_position']?:$mask_position; 
        $par = [
             'crop'=>$crop,     
             'crop_position'=>$crop_position,    
             'doc_direction'=>$doc_direction,  
             'dewarp'=>$dewarp,
             'binarization'=>$binarization,  
             'mask_position'=>$mask_position,  
             'output_image_format'=>'png',   
        ];
        $url = $url."?".http_build_query($par); 
        $res = $this->get_res($url,$content); 
        $code = $res['code'];
        if($code != 200){
        	return error_data(['msg'=>$this->code_message($code)]);
        }
        $image = base64_decode($res['result']['image']);
        if($image){ 
            $new_url = $this->new_url('text_auto_removal',$ori_url,$image);
            update_webtool_log($nid,['api_data'=>$new_url],'ok');
            return success_data(['url'=>$new_url]); 
        }else{
            update_webtool_log($nid,['api_data'=>''],'error');
            return error_data(['res'=>$res]); 
        } 
	}

    protected function new_url($k='',$ori_url,$image){
        $new_url = '/uploads/tmp/'.date("Y-m-d").'/';
        $new_dir = WWW_PATH.$new_url;
        if(!is_dir($new_dir)){
            mkdir($new_dir,0777,true);
        }
        $name = $k.md5($ori_url);
        $new_file = $new_dir.$name.'.png';
        file_put_contents($new_file,$image);
        return host().$new_url.$name.'.png'; 
    }

	protected function code_message($code){
		$arr = [
			40101=>'x-ti-app-id 或 x-ti-secret-code 为空',
			40102=>'x-ti-app-id 或 x-ti-secret-code 无效，验证失败',
			40103=>'客户端IP不在白名单',
			40003=>'余额不足，请充值后再使用',
			40004=>'参数错误，请查看技术文档，检查传参',
			40007=>'机器人不存在或未发布',
			40008=>'机器人未开通，请至市场开通后重试',
			40301=>'图片类型不支持',
			40302=>'上传文件大小不符，文件大小不超过 10M',
			40303=>'文件类型不支持',
			40304=>'图片尺寸不符，图像宽高须介于 20 和 10000（像素）之间',
			40305=>'识别文件未上传',
			30203=>'基础服务故障，请稍后重试',
			500=>'服务器内部错误',
		];
		return $arr[$code]?:'操作TextIn失败';
	}

	protected function get_res($url,$content){
		$headers = [ 
             'x-ti-app-id'     => $this->key1,
             'x-ti-secret-code'=> $this->key2,   
        ];   
        $client = guzzle_http(['headers'=>$headers]);
        $res    = $client->request('POST', $url,[
            'body' => $content 
        ]);
        $res = (string)$res->getBody();  
        return json_decode($res,true);
	} 
}

