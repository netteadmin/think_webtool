<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController;  
use hg\apidoc\annotation as Apidoc;    
/**
* @Apidoc\Title("API 位置距离")
*/
class ApiDistance extends ApiController
{   
    public $guest = true; 

    /**
    * @Apidoc\Title("位置距离")  
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("lat",type="string",require=true,desc="")    
    * @Apidoc\Query("lng",type="string",require=true,desc="")    
    * @Apidoc\Query("lat1",type="string",require=true,desc="")    
    * @Apidoc\Query("lng1",type="string",require=true,desc="")    
    * @Apidoc\Query("unit",type="string",require=true,desc="1米 2公里 默认返回公里")    
    */
    public function index(){
        $lat = $this->input['lat'];
        $lng = $this->input['lng'];
        $lat1 = $this->input['lat1'];
        $lng1 = $this->input['lng1'];
        $unit = $this->input['unit']??2;
        $d = get_distance($lng,$lat,$lng1,$lat1,$unit);
        return json_success([
            'data'=>$d,
            'version'=>'v20240705'
        ]);
    }

}