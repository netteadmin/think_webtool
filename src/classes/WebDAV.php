<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;

use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client;
/*
https://flysystem.thephpleague.com/docs/usage/filesystem-api/

webdav_url      = 
webdav_name     = 
webdav_password = 

$c = new \app\webtool\classes\WebDav;
$c->write("aa/test.txt",'welcome');   
       
*/
class WebDAV extends Flysystem{
    protected $filesystem;
    protected $adapter; 
    public function __construct(){
        $this->top_dir = get_config("webdav_path"); 
        $this->init_webdav(get_config("webdav_url"),get_config("webdav_name"),get_config("webdav_password"));
    }

} 

