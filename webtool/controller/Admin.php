<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\AdminController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc; 
use lib\Time; 

/**
* @Apidoc\Title("接口日志")
*/
class Admin extends AdminController
{  

    public function index()
    { 
        if(!admin_access('webtool.view')){
            throw new \Exception("权限不够", 403); 
        }
        return view('index');
    }

    /**
    * @Apidoc\Title("请求日志") 
    * @Apidoc\Method("POST")  
    * @Apidoc\Query("wq",type="string",require=true,desc="搜索名称")    
    * @Apidoc\Query("date",type="string",require=true,desc="时间Y-m-d")    
    */
    public function get_pager()
    { 
        $wq = $this->input['wq'];
        $date = $this->input['time'];
        if(!admin_access('webtool.view')){
            throw new \Exception("权限不够", 403); 
        }
        $where = ['ORDER'=>['id'=>'DESC']];
        if($wq){
            $where['title[~]'] = $wq;
        }
        if($date){
            $a = $date.' 00:00:00';
            $b = $date.' 23:59:59'; 
            $where['created_at[<>]'] = [$a,$b];
        } 
        $all = db_pager("webtool_log",$where);
        return json($all);
    }

    /**
    * @Apidoc\Title("接口调用统计") 
    * @Apidoc\Method("POST")     
    */
    public function get_count()
    { 
        if(!admin_access('webtool.view')){
            throw new \Exception("权限不够", 403); 
        }
        $arr = [
            'today',
            'yesterday',
            'week',
            'lastweek',  
            'month',
            'lastmonth',
            'year',
            'lastyear'   
        ];
        $list = [];
        foreach($arr as $k){
            $time = Time::get($k,true);
            $a = $time[0];
            $b = $time[1];
            $list[$k] = db_get_count("webtool_log",[
                ['created_at','>',$a],
                ['created_at','<=',$b],
                //'flag'=>'ok',
            ]); 
        }  
        return json_success(['data'=>$list]);
    }
}