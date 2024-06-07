<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;   
use Overtrue\Pinyin\Pinyin as P;
/**
* 汉字转拼音
*/
class Pinyin{   
	/**
	* 转拼音
	*/
	public function abc($content,$is_string = true){ 
		$arr = P::sentence($content,'none')->toArray();
		if($is_string){ 
			return implode('',$arr);
		}else{
			return $arr;
		}
	}

	/**
	* 转拼音-连接
	*/
	public function link($content,$tag = '-'){
		$tag = $tag?:'-';
		return P::permalink($content, $tag); 
	} 

	/**
	* 首字母
	*/
	public function first($content,$is_string = true){
		$arr = P::nameAbbr($content)->toArray();
		if($is_string){ 
			return implode('',$arr);
		}else{
			return $arr;
		}
	}

}