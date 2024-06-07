<?php
declare (strict_types = 1);

namespace app\webtool\command;
/**
* php think webtool:upload_webdav --ansi
*/
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use app\webtool\classes\WebDAV; 

class UploadWebDav extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('webtool_upload_webdav')
            ->setDescription('上传数据至webdav ');
    }

    protected function execute(Input $input, Output $output)
    { 
        $dir = get_config("webdav_upload_dir");
        $dir_1 = str_replace("\\","/",$dir);
        $top_name = substr($dir_1,strrpos($dir_1,'/')); 
        if(!$dir){
            $output->error("未设置 webdav_upload_dir");exit;
        }
        $webdav = new WebDAV;
        $in_ext = [
            'txt','md','doc','docx','ppt','pptx','xls','xlsx','pdf','php','zip','gz',
            'jpg','jpeg','png','gif','bmp','webp','sql','json','yaml','bson',
        ];
        $ignore = ['vendor'];
        $all = get_deep_dir($dir);
        $list = [];
        foreach($all as $v){
            $ext  = get_ext($v);
            $flag = true;
            foreach($ignore as $ig){
                if(strpos($v,'/'.$ig.'/') !== false){
                    $flag = false;
                }    
            }
            if(!$flag){
                continue;
            }            
            if(in_array($ext,$in_ext) && is_file($v)){
                $name = str_replace($dir,'',$v);
                $list[$v] = substr($name,1);
            } 
        } 
        foreach($list as $v=>$name){
            $name = $top_name.'/'.$name; 
            $cache_id = "webdav:".md5($dir.$name);
            if(cache($cache_id)){
                echo ".";
                continue;
            }
            $output->info("找到文件 ".$name); 
            $read = $webdav->is_file($name); 
            if($read){
                $output->warning(" --- 已存在，忽略 --- ");  
                cache($cache_id,now());   
            } else {
                $output->info("   上传文件 …… "); 
            } 
            if(!$read){
                $content = file_get_contents($v); 
                $webdav->write($name,$content);
            }            
            sleep(1);
        }      
        // 指令输出
        $output->info("操作完成");
    }
}
