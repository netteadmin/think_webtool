<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
/**
* 花生小店
* 97866.com 
*/
class EcHuashen{  

	public $token_url = 'https://{wxappid}.97866.com/api/token/grant.json?appid={appid}&secret={secret}';
	public $order_url = 'https://{wxappid}.97866.com/api/mag.admin.order.list.json?access_token={access_token}';
	public $wxappid;
	public $appid;
	public $secret;
	public $access_token;
	public $err;
	/**
	* 内部，不用调用
	* @param wxappid
	* @param appid
	* @param secret
	*/
	protected function init($arr = []){ 
		$wxappid = $arr['wxappid'];
		$appid = $arr['appid'];
		$secret = $arr['secret'];
		$this->wxappid = $wxappid;
		$this->appid = $appid;
		$this->secret = $secret;
	}

	/**
	* 取token
	* @param wxappid
	* @param appid
	* @param secret
	*/
	protected function get_token($arr = []){
		$this->init($arr);
		$url = $this->token_url;  
		$url = str_replace("{wxappid}",$this->wxappid,$url);
		$url = str_replace("{appid}",$this->appid,$url);
		$url = str_replace("{secret}",$this->secret,$url);
		$cache_id = "ec:huashen:".$this->appid.$this->secret; 
		$d = cache($cache_id);
		if($d){
			$this->access_token = $d;
			return $d;
		} 
		$client = guzzle_http();
		$res    = $client->request('GET', $url);
		$res  = (string)$res->getBody();  
		$res = json_decode($res,true); 
		$access_token = $res['access_token'];
		$expires_in = $res['expires_in']; 
		if($res['errcode']==0 && $access_token && $expires_in){
			cache($cache_id,$access_token,$expires_in);
			$this->access_token = $access_token;
			return $access_token;
		}else{
			$this->err = $res['errmsg'];
		}
	}
	 
	/**
	* 订单列表
	* @param wxappid
	* @param appid
	* @param secret
	*/
	public function get_orders($arr = []){
		$url = $this->order_url;
		$this->get_token($arr);  
		if($this->err){
			return $this->err;
		}
		$url = str_replace("{wxappid}",$this->wxappid,$url); 
		$url = str_replace("{access_token}",$this->access_token,$url); 
		$client = guzzle_http();
		$res    = $client->request('GET', $url);
		$res  = (string)$res->getBody();  
		$res = json_decode($res,true); 
		if($res['errcode'] == 0){
			$orders =  $res['orders']?:[]; 
			return [
				'data'=>[ 
					'orders'=>$orders,
					'start_time'=>date('Y-m-d H:i:s',$res['start_time']),
					'end_time'=>date('Y-m-d H:i:s',$res['end_time']),
				]
			];
		}
		return '';
	}
}