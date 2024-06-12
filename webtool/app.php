<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
function get_webtool_wx(){
    $wx = new \app\webtool\classes\WeMinProgram();
    $wx->init([
        'appid'=>get_config('APP_ID'),
        'secret'=>get_config('APP_SECRET'),
    ]);
    return $wx;
}

function add_webtool_cmd_job($cmd){
    if(!$cmd){
        return;
    }
    if(!db_get("webtool_cmd_job",['cmd'=>$cmd],1)){
        db_insert("webtool_cmd_job",[
            'cmd'=>$cmd,
            'status'=>'wait',
            'created_at'=>now(),
        ]);
    } 
}

function create_oss_remote_url($url){
    $name = '/uploads/'.date('Y-m').'/'.md5($url);
    $ext = get_ext_by_url($url);
    if($ext){
        return $name.'.'.$ext;
    }else{
        return $name;
    }        
}
function add_oss_info($type,$file,$remote_url){
    if(strpos($file,WWW_PATH)!==false){
        $file = str_replace(WWW_PATH,'',$file);
    }
    $res = db_get("webtool_oss",[
        'type'=>$type,
        'file'=>$file,
    ],1);
    if(!$res){
        db_insert('webtool_oss',[
            'type'=>$type,
            'file'=>$file,
            'url'=>$remote_url,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);
    }else{
        db_update('webtool_oss',[
            'type'=>$type,
            'file'=>$file,
            'url'=>$remote_url,
            'updated_at'=>now()
        ],['id'=>$res['id']]);
    }
}


add_admin_access('接口.webtool',[ 
    '查看.view',  
    '编辑.edit',
    '管理.admin',  
    'url'=>'webtool/admin'
],60);

function webtool_log($type,$title,$par,$is_update = false){
    global $g_user_id; 
    $title = str_replace(WWW_PATH,'',$title);
    $title = str_replace(host(),'',$title);
    $d = [
        'user_id'=>$g_user_id,
        'title'=>$title,
        'type'=>$type, 
        'par'=>$par,
        'flag'=>'ok',
        'created_at'=>now(),
    ];
    if($is_update){
        $d['updated_at'] = now();
    }
    return db_insert('webtool_log',$d);
}

function update_webtool_log($id,$data = [],$flag = 'ok'){ 
    db_update('webtool_log',[
        'api_return'=>$data,
        'flag'=>$flag,
        'updated_at'=> now(),
    ],['id'=>$id]);
}
   
add_action("console",function(&$console){
    if(!is_cli()){return;} 
    $console['webtool:rm'] = "app\webtool\command\CleanFile";
    $console['webtool:ssl'] = "app\webtool\command\Ssl";
    $console['webtool:ssl_to_bt'] = "app\webtool\command\SslSyncToBt";
    $console['webtool:printer'] = "app\webtool\command\Printer";
    $console['webtool:cmd'] = "app\webtool\command\Commands";
    $console['webtool:webdav'] = "app\webtool\command\DownloadWebDav";
    $console['webtool:upload_webdav'] = "app\webtool\command\UploadWebDav";
    $console['webtool:printer_close'] = "app\webtool\command\PrinterClose";

});  



add_action("admin.index",function(){
    if(!is_admin_login()){
        return;
    }
    global $config;
    if($config['show_webtool']){
        include __DIR__.'/action/home.php';
    } 
});
add_action("admin.index.created",function(&$created){
    if(!is_admin_login()){
        return;
    } 
    $created[] = 'get_webtoll()';
}); 

include __DIR__.'/printer.php';