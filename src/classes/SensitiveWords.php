<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use DfaFilter\SensitiveHelper;
/**
 * 过滤敏感词
 */
class SensitiveWords{  
    protected $handle;
    public function __construct(){
        $wordFilePath = __DIR__.'/SensitiveWords/words.txt';   
        $this->handle = SensitiveHelper::init()->setTreeByFile($wordFilePath);
    }
    /**
     *  是否合法
     */
    public function is_legal($content){
        return $this->handle->islegal($content);
    }
    /**
     *  过滤敏感词
     */
    public function get($content,$replace = '*'){
        $flag = false;
        if($replace == '*'){
            $flag = true;
        }
        $replace = $replace?:"*";
        return $this->handle->replace($content, $replace, $flag);
    }
    /** 
    * 获取文字中的敏感词
    */
    public function get_bad_word($content){
        return $this->handle->getBadWord($content);
    } 

}