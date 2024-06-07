<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\Map as Api;

/**
* @Apidoc\Title("API 地图相关")
*/
class ApiMap extends ApiController
{  
    public $api;
    public $guest = false;
    public function init(){
        parent::init();
        $this->api = new Api;
    }
    /**
    * @Apidoc\Title("获取经纬度") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Map")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("address",type="string",require=true,desc="完整地址") 
    * @Apidoc\Returned("lat",type="string",require=true,desc="纬度")  
    * @Apidoc\Returned("lng",type="string",require=true,desc="经度")  
    * @Apidoc\Returned("province",type="string",require=true,desc="省")  
    * @Apidoc\Returned("city",type="string",require=true,desc="市")  
    * @Apidoc\Returned("district",type="string",require=true,desc="区")  
    */
    public function get_tx_address_lat_lng(){
        $address = $this->input['address'];
        return json($this->api->get_tx_address_lat_lng($address));
    }

    /**
    * @Apidoc\Title("把地址加入搜索点") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Map")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("key",type="string",require=true,desc="redis key") 
    * @Apidoc\Query("title",type="string",require=true,desc="地址简写") 
    * @Apidoc\Query("address",type="string",require=true,desc="完整地址")   
    */
    public function add_address_to_geo(){
        $key = $this->input['key'];
        $title = $this->input['title'];
        $address = $this->input['address']; 
        return json($this->api->add_address_to_geo($key,$title,$address));
    } 

    /**
    * @Apidoc\Title("把地址从搜索点删除") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Map")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("key",type="string",require=true,desc="redis key")  
    * @Apidoc\Query("address",type="string",require=true,desc="完整地址")   
    */
    public function delete_address_to_geo(){
        $key = $this->input['key'];
        $address = $this->input['address']; 
        return json($this->api->delete_address_to_geo($key,$address));
    }  
    /**
    * @Apidoc\Title("搜索附近指定距离信息") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Map")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("key",type="string",require=true,desc="redis key") 
    * @Apidoc\Query("lng",type="string",require=true,desc="经度") 
    * @Apidoc\Query("lat",type="string",require=true,desc="纬度")  
    * @Apidoc\Query("juli",type="string",require=true,desc="距离km") 
    */
    public function get_nearby(){
        $key = $this->input['key'];
        $lng = $this->input['lng'];
        $lat = $this->input['lat'];
        $juli = $this->input['juli']?:2;
        return json($this->api->get_nearby($key,$lng,$lat,$juli));
    } 
    /**
    * @Apidoc\Title("剔除所有地址信息") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Map")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("key",type="string",require=true,desc="redis key") 
    */
    public function delete_geo(){
        $key = $this->input['key']; 
        return json($this->api->delete_geo($key));
    }

    
}