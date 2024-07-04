<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use lib\Map as Service;

class Map{  
    //使用天地图 tiantitu 或 腾讯地图 tx
    public static $use = 'tiantitu';
    /**
    * 获取坐标点,使用天地图
    */
    public function get_lat($address){
        return \helper_v3\Map::get_lat($address);
    }
    /**
    * 获取坐标点
    */
    public function get_tx_address_lat_lng($address){
        $res = Service::tx($address);
        if($res['lng']){
            $address_components = $res['data']['result']['address_components'];
            $list = [
                'lat'=>$res['lat'],
                'lng'=>$res['lng'],
                'province'=>$address_components['province'],
                'city'=>$address_components['city'],
                'district'=>$address_components['district'],
            ];
            return success_data($list);
        }
        return error_data('请求失败'); 
    }
    /**
    * 把地址加入搜索点
    * @param $key rediskey
    * @param $title 地址简写
    * @param $address 完整地址
    */
    public function add_address_to_geo($key,$title,$address){
        if(!$key){
            return error_data('请求失败');
        }
        if(self::$use == 'tx'){
            $d = $this->get_tx_address_lat_lng($address)['data'];
        }else if(self::$use == 'tiantitu'){
            $d = $this->get_lat($address);
        }  
        $lat = $d['lat'];
        $lng = $d['lng'];

        if(!$lat || !$lng ){
            return error_data('获取坐标点失败,地址：'.$address);
        } 
        try {
            predis_add_geo($key,[
                [ 
                    'lng'=>$lng,
                    'lat'=>$lat,
                    'title'=>$title
                ]
            ]);  
        } catch (\Exception $e) {
            return error_data($e->getMessage());
        }  
        $res = db_get("webtool_map_geo",[
            'title'=>$title,
            'key'=>$key,
        ],1);
        if(!$res){
            db_insert("webtool_map_geo",[
                'key'=>$key,
                'title'=>$title,
                'lng'=>$lng,
                'lat'=>$lat, 
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }else{
            db_update("webtool_map_geo",[ 
                'lng'=>$lng,
                'lat'=>$lat,  
                'updated_at'=>now(),
            ],['id'=>$res['id']]);
        }
        return success_data($d,'操作成功');
    }

    /**
    * 删除搜索点 
    */
    public function delete_address_to_geo($key,$address){
        if(!is_array($address)){
            $address = [$address];
        }
        return predis_delete_geo($key,$address); 
    } 

    /**
    * 搜索附近指定距离信息 
    */
    public function get_nearby($key,$lng,$lat,$juli = 2){
        if(!$key || !$lat || !$lng){
            return error_data('参数异常');
        } 
        return predis_get_pager($key,$lng,$lat,$juli); 
    }


    /**
    * 剔除所有的地址 
    */
    public function delete_geo($key){
        if(!$key){
            return error_data('参数异常');
        }
        $all = db_get("webtool_map_geo",['key'=>$key]);
        $in = [];
        foreach($all as $v){
            $in[] = $v['title'];
        } 
        db_del("webtool_map_geo",['key'=>$key]);
        try {
            predis_delete_geo($key,$in);    
        } catch (\Exception $e) {
            return error_data($e->getMessage());
        } 
        return success_data('','删除成功'); 
    } 

}
