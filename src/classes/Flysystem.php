<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;
/*
https://flysystem.thephpleague.com/docs/usage/filesystem-api/
*/
class Flysystem{
    protected $filesystem;
    protected $adapter;  
    public $top_dir = '';
    public $name = ''; 
    public $is_webdav = false;
    /**
    * 初始化 webdav
    */
    public function init_webdav($url,$user,$pwd){ 
        $cc = [
            'baseUri'  => $url,
            'userName' => $user,
            'password' => $pwd
        ]; 
        $client  = new Client($cc);
        $adapter = new WebDAVAdapter($client);
        $filesystem = new Filesystem($adapter);
        $this->adapter = $adapter;
        $this->filesystem = $filesystem;  
        $this->is_webdav  = true;
    }
    /**
    * 写文件
    */
    public function write($file,$content,$option = []){
        if($this->is_webdav){
            $dir = get_dir($file);
            $this->create_dir_if($dir);
        }
        $option = $option??['visibility'=>'private','directory_visibility'=>'private'];        
        try {
            $this->filesystem->write($file, $content, $option);
            return true;
        } catch (FilesystemException | UnableToWriteFile $e) {
            trace($e->getMessage(),'error');
            if(is_cli() && $this->is_webdav){
                echo "写文件失败，可能原因无权限或接口限流，返回  ".$e->getMessage()."\n";exit;
            }
            return false;
        }
    }
    /**
    * 坚果云有/dav目录，有时需要删除/dav
    * https://www.jianguoyun.com/
    */
    public function remove_top_dir($dir){
        if($this->top_dir){
            return substr($dir,strlen($this->top_dir));
        }else{
            return $dir;
        }
    }
    /**
    * 远程文件是否存在
    */
    public function is_file($path){
        try {
            return  $this->filesystem->fileExists($path);
        } catch (FilesystemException | UnableToCheckExistence $exception) {
            return false;
        }
    }
    /**
    * 创建目录
    */
    public function create_dir_if($path,$config = [])
    {
       if(!$this->is_dir($path)){
          $this->create_dir($path,$config);
       }
    }
    /**
    * 目录是否存在
    */
    public function is_dir($path){
        try {
            return $this->filesystem->directoryExists($path);
        } catch (FilesystemException | UnableToCheckExistence $e) {
            if(is_cli() && $this->is_webdav){
                echo "判断目录是否存在失败，可能原因无权限或接口限流，返回 ".$e->getMessage()."\n";exit;
            }
            return false;
        } 
    }
    /**
    * 创建目录
    */
    public function create_dir($path,$config = [])
    {
        try {
            return $this->filesystem->createDirectory($path, $config);
        } catch (FilesystemException | UnableToCreateDirectory $e) {
            if(is_cli() && $this->is_webdav){
                echo "创建目录失败，可能原因无权限或接口限流，返回 ".$e->getMessage()."\n";exit;
            }
            return false;
        }
    }
    /**
    * 读取文件
    */
    public function read($path = '/我的坚果云/【01】坚果云入门基础知识.pdf'){ 
        try {
           $content = $this->filesystem->read($path);   
           $base_name = '';
           if($this->name){
                $base_name = $this->name.'/';
           }
           $local_file = PATH.'/data/clould/'.$base_name.$path;
           $dir = get_dir($local_file);
           create_dir_if_not_exists([$dir]);
           file_put_contents($local_file,$content);
           return true;
        } catch (FilesystemException | UnableToReadFile $e) {
           trace($e->getMessage(),'error');
           return false;
        } 
    }
    /**
    * 下载所有的文件 
    * @param $big_data  bool为true时，下载多文件时需要
    */
    public function read_all($big_data = false){
        if($big_data){
            $this->list_dir('/',true,function($v){
                if(is_cli()){
                    echo ".";
                    $this->read($v);
                    echo "_";
                }
            });
        }else{
            $files = $this->list_dir();
            foreach($files as $v){
                $this->read($v);
            }
            return true;
        }
        
    } 
    /**
    * 列出所有文件
    */
    public function list_dir($root_path = '/',$is_top = true,$call = ''){
        static $fs; 
        if($is_top){
            $fs   = []; 
        } 
        $lists = $this->filesystem->listContents($root_path)
            ->sortByPath()
            ->toArray(); 
        foreach($lists as $v){
            $path = $v->path();
            $path = $this->remove_top_dir($path);
            if($v->isFile()){
                if(!$call){
                    $fs[] = $path;     
                }                
                if($call){ 
                    $call($path);
                }
            }else if($v->isDir()){   
                $this->list_dir($path,false,$call);
            }
        }
        if(!$call){
            return $fs;
        }
    }
} 

