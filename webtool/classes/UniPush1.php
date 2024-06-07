<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
/**
* unipush 1 推送  
* https://dev.dcloud.net.cn/pages/app/push/info
*/
class UniPush1{
	/**
	* 发消息
	* @param $click_type  1、intent：打开应用内特定页面url：打开网页地址。2、payload：自定义消息内容启动应用。3、payload_custom：自定义消息内容不启动应用。4、startapp：打开应用首页。5、none：纯通知，无后续动作
	*/
	public function push($cid,$title,$body,$click_type = 'none'){ 
		//创建API，APPID等配置参考 环境要求 进行获取
		if(!class_exists('GTClient')){return;}
	    $api = new \GTClient(
	    	"https://restapi.getui.com",
	    	get_config('unipush_AppKey'),
	    	get_config('unipush_AppID'),
	    	get_config('unipush_MasterSecret')
	    );
	    //设置推送参数
	    $push = new \GTPushRequest();
	    $uni = 'upush'.order_num();
	    $push->setRequestId($uni);
	    $message = new \GTPushMessage();
	    $notify = new \GTNotification();
	    $notify->setTitle($title);
	    $notify->setBody($body);
	    //点击通知后续动作，目前支持以下后续动作:
	    //1、intent：打开应用内特定页面url：打开网页地址。2、payload：自定义消息内容启动应用。3、payload_custom：自定义消息内容不启动应用。4、startapp：打开应用首页。5、none：纯通知，无后续动作
	    $notify->setClickType("none");
	    $message->setNotification($notify);
	    $push->setPushMessage($message);
	    $push->setCid($cid);
	    //处理返回结果
	    $res = $api->pushApi()->pushToSingleByCid($push);
	    return $res;
	}

}


