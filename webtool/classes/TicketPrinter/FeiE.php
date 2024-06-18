<?php 
namespace app\webtool\classes\TicketPrinter;
/*
https://admin.feieyun.com/index.php

$arr[0] = array('title'=>'酸菜鱼','price'=>'100.4','num'=>'10');
$arr[1] = array('title'=>'可乐鸡翅+蒜蓉蒸扇贝','price'=>'10.3','num'=>'6');
$arr[2] = array('title'=>'紫苏焖鹅+梅菜肉饼+椒盐虾+北京烤鸭','price'=>'10.0','num'=>'8');
$obj = new feie;
$set = [
	'user'=>'@qq.com',
	'ukey'=>'',
	'sn'=>'111',
	'key'=>'111',
	'title'=>'门店名称',
	'phone'=>'13285801489',
	'desc'=>'少辣',
	'qr'=>'http://baidu.com',	
];
$info = $obj->do_print($arr); 
pr($info);

//名称14 单价6 数量3 金额6-->这里的字节数可按自己需求自由改写，14+6+3+6再加上代码写的3个空格就是32了，58mm打印机一行总占32字节

$time = date('Y-m-d H:i:s',time());
$orderInfo .= '--------------------------------<BR>';
$orderInfo .= '合计：'.number_format($nums, 2).'元<BR>';  
if($this->phone){
	$orderInfo .= '联系电话：'.$this->phone.'<BR>';
}      	
$orderInfo .= '打印时间：'.$time.'<BR>';
if($this->desc){
	$orderInfo .= '备注：'.$this->desc.'<BR><BR>';
}      	
if($this->qr){
	$orderInfo .= '<QR>'.$this->qr.'</QR>';//把解析后的二维码生成的字符串用标签套上即可自动生成二维码
}      	
return $orderInfo;

*/
class FeiE extends Base{
	//手机号
	public $phone;
	//标题
	public $title;
	//备注
	public $desc;
	//条码URL，带http的
	public $qr;
	//设备号
	public $sn; //*必填*：打印机编号，必须要在管理后台里添加打印机或调用API接口添加之后，才能调用API
	public $key;
	public $user = '';//*必填*：飞鹅云后台注册账号
	public $ukey = '';//*必填*: 飞鹅云后台注册账号后生成的UKEY 【备注：这不是填打印机的KEY】

	public $ip   = 'api.feieyun.cn';//接口IP或域名
	public $port = 80;//接口IP端口
	public $path = '/Api/Open/';  
	 
	/**
	* 打印
	* @param $option  arr打印菜品 list[k=>v]
	*/
	public function do_print($set=[],$option = []){
		$this->set($set);
		$arr = $option['arr'];
		if(!$arr){
			return false;
		}
		$info = $this->get_string($arr,14,6,3,6);	
		if($option['list']){
			$info .= '--------------------------------<BR>';
			foreach($option['list'] as $k=>$v){
				$info .= $k.'：'.$v.'<BR>';  
			}
		}
		return  $this->do_print_58mm($set,$this->sn,$info,1);
	}
    /**
     *    
     *    #########################################################################################################
     *
     *    进行订单的多列排版demo，实现商品超出字数的自动换下一行对齐处理，同时保持各列进行对齐
     *
     *    排版原理是统计字符串字节数，补空格换行处理
     *
     *    58mm的机器,一行打印16个汉字,32个字母;80mm的机器,一行打印24个汉字,48个字母
     *
     *    #########################################################################################################
     */
    public function get_string($arr,$A,$B,$C,$D,$string1='')
    { 
      $nums = 0;
      $orderInfo = '<CB>'.$this->title.'</CB><BR>';
      $orderInfo .= '名称           单价  数量 金额<BR>';
      $orderInfo .= '--------------------------------<BR>';
      foreach ($arr as $k5 => $v5) {
        $name   = $v5['title'];
        $price  = $v5['price'];
        $num    = $v5['num'];
        $prices = bcmul($v5['price'],$v5['num'],2);
        $kw3 = '';
        $kw1 = '';
        $kw2 = '';
        $kw4 = '';
        $str = $name;
        $blankNum = $A;//名称控制为14个字节
        $lan = mb_strlen($str,'utf-8');
        $m = 0;
        $j=1;
        $blankNum++;
        $result = array();
        if(strlen($price) < $B){
              $k1 = $B - strlen($price);
              for($q=0;$q<$k1;$q++){
                    $kw1 .= ' ';
              }
              $price = $price.$kw1;
        }
        if(strlen($num) < $C){
              $k2 = $C - strlen($num);
              for($q=0;$q<$k2;$q++){
                    $kw2 .= ' ';
              }
              $num = $num.$kw2;
        }
        if(strlen($prices) < $D){
              $k3 = $D - strlen($prices);
              for($q=0;$q<$k3;$q++){
                    $kw4 .= ' ';
              }
              $prices = $prices.$kw4;
        }
        for ($i=0;$i<$lan;$i++){
          $new = mb_substr($str,$m,$j,'utf-8');
          $j++;
          if(mb_strwidth($new,'utf-8')<$blankNum) {
            if($m+$j>$lan) {
              $m = $m+$j;
              $tail = $new;
              $lenght = iconv("UTF-8", "GBK//IGNORE", $new);
              $k = $A - strlen($lenght);
              for($q=0;$q<$k;$q++){
                $kw3 .= ' ';
              }
              if($m==$j){
                $tail .= $kw3.' '.$price.' '.$num.' '.$prices;
              }else{
                $tail .= $kw3.'<BR>';
              }
              break;
            }else{
              $next_new = mb_substr($str,$m,$j,'utf-8');
              if(mb_strwidth($next_new,'utf-8')<$blankNum) continue;
              else{
                $m = $i+1;
                $result[] = $new;
                $j=1;
              }
            }
          }
        }
        $head = '';
        foreach ($result as $key=>$value) {
          if($key < 1){
            $v_lenght = iconv("UTF-8", "GBK//IGNORE", $value);
            $v_lenght = strlen($v_lenght);
            if($v_lenght == 13) $value = $value." ";
            $head .= $value.' '.$price.' '.$num.' '.$prices;
          }else{
            $head .= $value.'<BR>';
          } 
        }
        $orderInfo .= $head.$tail; 
      }
      return $orderInfo; 
    }


	/**
	* [批量添加打印机接口 Open_printerAddlist]
	* @param  [string] $printerContent [打印机的sn#key]
	* @return [string]                 [接口返回值]
	*/
	public function add($set=[],$printerContent){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_printerAddlist',
		  'printerContent'=>$printerContent
		); 
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  $res = $client->getContent();
		  $res['flag'] = $res['no'][0]?false:true;
		  return $res;
		}
	}


	/**
	* [打印订单接口 Open_do_print_58mm]
	* @param  [string] $sn      [打印机编号sn]
	* @param  [string] $content [打印内容]
	* @param  [string] $times   [打印联数]
	* @return [string]          [接口返回值]
	*/
	protected function do_print_58mm($set,$sn,$content,$times){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_printMsg',
		  'sn'=>$this->sn,
		  'content'=>$content,
		  'times'=>$times//打印次数
		); 
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  //服务器返回的JSON字符串，建议要当做日志记录起来
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [标签机打印订单接口 Open_printLabelMsg]
	* @param  [string] $sn      [打印机编号sn]
	* @param  [string] $content [打印内容]
	* @param  [string] $times   [打印联数]
	* @return [string]          [接口返回值]
	*/
	protected function do_print_label($set=[] , $sn,$content,$times){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_printLabelMsg',
		  'sn'=>$this->sn,
		  'content'=>$content,
		  'times'=>$times//打印次数
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  //服务器返回的JSON字符串，建议要当做日志记录起来
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [批量删除打印机 Open_printerDelList]
	* @param  [string] $snlist [打印机编号，多台打印机请用减号“-”连接起来]
	* @return [string]         [接口返回值]
	*/
	public function del($set=[],$snlist){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_printerDelList',
		  'snlist'=>$snlist
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [修改打印机信息接口 Open_printerEdit]
	* @param  [string] $sn       [打印机编号]
	* @param  [string] $name     [打印机备注名称]
	* @param  [string] $phonenum [打印机流量卡号码,可以不传参,但是不能为空字符串]
	* @return [string]           [接口返回值]
	*/
	public function edit($set=[],$sn,$name,$phonenum=''){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_printerEdit',
		  'sn'=>$this->sn,
		  'name'=>$name, 
		);
		if($phonenum){
			$msgInfo['phonenum'] = $phonenum;
		}
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){
		   
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}


	/**
	* [清空待打印订单接口 Open_delPrinterSqs]
	* @param  [string] $sn [打印机编号]
	* @return [string]     [接口返回值]
	*/
	public function clear($set=[],$sn){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_delPrinterSqs',
		  'sn'=>$this->sn
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){
		   
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [查询订单是否打印成功接口 Open_queryOrderState]
	* @param  [string] $orderid [调用打印机接口成功后,服务器返回的JSON中的编号 例如：123456789_20190919163739_95385649]
	* @return [string]          [接口返回值]
	*/
	public function query($set=[], $orderid){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_queryOrderState',
		  'orderid'=>$orderid
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){
		   
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [查询指定打印机某天的订单统计数接口 Open_queryOrderInfoByDate]
	* @param  [string] $sn   [打印机的编号]
	* @param  [string] $date [查询日期，格式YY-MM-DD，如：2019-09-20]
	* @return [string]       [接口返回值]
	*/
	public function query_date($set=[],$sn,$date){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_queryOrderInfoByDate',
		  'sn'=>$this->sn,
		  'date'=>$date
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [获取某台打印机状态接口 Open_queryPrinterStatus]
	* @param  [string] $sn [打印机编号]
	* @return [string]     [接口返回值]
	*/
	public  function get_status($set=[],$sn){
		$this->set($set);
		$time = time();         //请求时间
		$msgInfo = array(
		  'user'=>$this->user,
		  'stime'=>$time,
		  'sig'=>$this->signature($time),
		  'apiname'=>'Open_queryPrinterStatus',
		  'sn'=>$this->sn
		);
		$client = new FeiEHttpClient($this->ip,$this->port);
		if(!$client->post($this->path,$msgInfo)){ 
		}else{
		  $result = $client->getContent();
		  return $result;
		}
	}

	/**
	* [signature 生成签名]
	* @param  [string] $time [当前UNIX时间戳，10位，精确到秒]
	* @return [string]       [接口返回值]
	*/
	public function signature($time){
		return sha1($this->user.$this->ukey.$time);//公共参数，请求公钥
	} 
    
}


class FeiEHttpClient {
    // Request vars
    var $host;
    var $port;
    var $path;
    var $method;
    var $postdata = '';
    var $cookies = array();
    var $referer;
    var $accept = 'text/xml,application/xml,application/xhtml+xml,text/html,text/plain,image/png,image/jpeg,image/gif,*/*';
    var $accept_encoding = 'gzip';
    var $accept_language = 'en-us';
    var $user_agent = 'Incutio HttpClient v0.9';
    var $timeout = 20;
    var $use_gzip = true;
    var $persist_cookies = true; 
    var $persist_referers = true; 
    var $debug = false;
    var $handle_redirects = true; 
    var $max_redirects = 5;
    var $headers_only = false;    
    var $username;
    var $password;
    var $status;
    var $headers = array();
    var $content = '';
    var $errormsg;
    var $redirect_count = 0;
    var $cookie_host = '';
    function __construct($host, $port=80) {
        $this->host = $host;
        $this->port = $port;
    }
    function get($path, $data = false) {
        $this->path = $path;
        $this->method = 'GET';
        if ($data) {
            $this->path .= '?'.$this->buildQueryString($data);
        }
        return $this->doRequest();
    }
    function post($path, $data) {
        $this->path = $path;
        $this->method = 'POST';
        $this->postdata = $this->buildQueryString($data);
        return $this->doRequest();
    }
    function buildQueryString($data) {
        $querystring = '';
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $val2) {
                        $querystring .= urlencode($key).'='.urlencode($val2).'&';
                    }
                } else {
                    $querystring .= urlencode($key).'='.urlencode($val).'&';
                }
            }
            $querystring = substr($querystring, 0, -1); // Eliminate unnecessary &
        } else {
            $querystring = $data;
        }
        return $querystring;
    }
    function doRequest() {
        if (!$fp = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout)) {
            switch($errno) {
                case -3:
                    $this->errormsg = 'Socket creation failed (-3)';
                case -4:
                    $this->errormsg = 'DNS lookup failure (-4)';
                case -5:
                    $this->errormsg = 'Connection refused or timed out (-5)';
                default:
                    $this->errormsg = 'Connection failed ('.$errno.')';
                $this->errormsg .= ' '.$errstr;
                $this->debug($this->errormsg);
            }
            return false;
        }
        socket_set_timeout($fp, $this->timeout);
        $request = $this->buildRequest();
        $this->debug('Request', $request);
        fwrite($fp, $request);
        $this->headers = array();
        $this->content = '';
        $this->errormsg = '';
        $inHeaders = true;
        $atStart = true;
        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            if ($atStart) {
                $atStart = false;
                if (!preg_match('/HTTP\/(\\d\\.\\d)\\s*(\\d+)\\s*(.*)/', $line, $m)) {
                    $this->errormsg = "Status code line invalid: ".htmlentities($line);
                    $this->debug($this->errormsg);
                    return false;
                }
                $http_version = $m[1]; 
                $this->status = $m[2];
                $status_string = $m[3];
                $this->debug(trim($line));
                continue;
            }
            if ($inHeaders) {
                if (trim($line) == '') {
                    $inHeaders = false;
                    $this->debug('Received Headers', $this->headers);
                    if ($this->headers_only) {
                        break;
                    }
                    continue;
                }
                if (!preg_match('/([^:]+):\\s*(.*)/', $line, $m)) {
                    continue;
                }
                $key = strtolower(trim($m[1]));
                $val = trim($m[2]);
                if (isset($this->headers[$key])) {
                    if (is_array($this->headers[$key])) {
                        $this->headers[$key][] = $val;
                    } else {
                        $this->headers[$key] = array($this->headers[$key], $val);
                    }
                } else {
                    $this->headers[$key] = $val;
                }
                continue;
            }
            $this->content .= $line;
        }
        fclose($fp);
        if (isset($this->headers['content-encoding']) && $this->headers['content-encoding'] == 'gzip') {
            $this->debug('Content is gzip encoded, unzipping it');
            $this->content = substr($this->content, 10);
            $this->content = gzinflate($this->content);
        }
        if ($this->persist_cookies && isset($this->headers['set-cookie']) && $this->host == $this->cookie_host) {
            $cookies = $this->headers['set-cookie'];
            if (!is_array($cookies)) {
                $cookies = array($cookies);
            }
            foreach ($cookies as $cookie) {
                if (preg_match('/([^=]+)=([^;]+);/', $cookie, $m)) {
                    $this->cookies[$m[1]] = $m[2];
                }
            }
            $this->cookie_host = $this->host;
        }
        if ($this->persist_referers) {
            $this->debug('Persisting referer: '.$this->getRequestURL());
            $this->referer = $this->getRequestURL();
        }
        if ($this->handle_redirects) {
            if (++$this->redirect_count >= $this->max_redirects) {
                $this->errormsg = 'Number of redirects exceeded maximum ('.$this->max_redirects.')';
                $this->debug($this->errormsg);
                $this->redirect_count = 0;
                return false;
            }
            $location = isset($this->headers['location']) ? $this->headers['location'] : '';
            $uri = isset($this->headers['uri']) ? $this->headers['uri'] : '';
            if ($location || $uri) {
                $url = parse_url($location.$uri);
                return $this->get($url['path']);
            }
        }
        return true;
    }
    function buildRequest() {
        $headers = array();
        $headers[] = "{$this->method} {$this->path} HTTP/1.0"; 
        $headers[] = "Host: {$this->host}";
        $headers[] = "User-Agent: {$this->user_agent}";
        $headers[] = "Accept: {$this->accept}";
        if ($this->use_gzip) {
            $headers[] = "Accept-encoding: {$this->accept_encoding}";
        }
        $headers[] = "Accept-language: {$this->accept_language}";
        if ($this->referer) {
            $headers[] = "Referer: {$this->referer}";
        }
        if ($this->cookies) {
            $cookie = 'Cookie: ';
            foreach ($this->cookies as $key => $value) {
                $cookie .= "$key=$value; ";
            }
            $headers[] = $cookie;
        }
        if ($this->username && $this->password) {
            $headers[] = 'Authorization: BASIC '.base64_encode($this->username.':'.$this->password);
        }
        if ($this->postdata) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $headers[] = 'Content-Length: '.strlen($this->postdata);
        }
        $request = implode("\r\n", $headers)."\r\n\r\n".$this->postdata;
        return $request;
    }
    function getStatus() {
        return $this->status;
    }
    function getContent() {
        $res = $this->content;
        if($res && is_json($res)){
        	return json_decode($res,true);
        }else{
        	return $res;
        }
    }
    function getHeaders() {
        return $this->headers;
    }
    function getHeader($header) {
        $header = strtolower($header);
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        } else {
            return false;
        }
    }
    function getError() {
        return $this->errormsg;
    }
    function getCookies() {
        return $this->cookies;
    }
    function getRequestURL() {
        $url = 'https://'.$this->host;
        if ($this->port != 80) {
            $url .= ':'.$this->port;
        }            
        $url .= $this->path;
        return $url;
    }
    function setUserAgent($string) {
        $this->user_agent = $string;
    }
    function setAuthorization($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    function setCookies($array) {
        $this->cookies = $array;
    }
    function useGzip($boolean) {
        $this->use_gzip = $boolean;
    }
    function setPersistCookies($boolean) {
        $this->persist_cookies = $boolean;
    }
    function setPersistReferers($boolean) {
        $this->persist_referers = $boolean;
    }
    function setHandleRedirects($boolean) {
        $this->handle_redirects = $boolean;
    }
    function setMaxRedirects($num) {
        $this->max_redirects = $num;
    }
    function setHeadersOnly($boolean) {
        $this->headers_only = $boolean;
    }
    function setDebug($boolean) {
        $this->debug = $boolean;
    }
    function quickGet($url) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        if (isset($bits['query'])) {
            $path .= '?'.$bits['query'];
        }
        $client = new HttpClient($host, $port);
        if (!$client->get($path)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
    function quickPost($url, $data) {
        $bits = parse_url($url);
        $host = $bits['host'];
        $port = isset($bits['port']) ? $bits['port'] : 80;
        $path = isset($bits['path']) ? $bits['path'] : '/';
        $client = new HttpClient($host, $port);
        if (!$client->post($path, $data)) {
            return false;
        } else {
            return $client->getContent();
        }
    }
    function debug($msg, $object = false) {
        if ($this->debug) {
            print '<div style="border: 1px solid red; padding: 0.5em; margin: 0.5em;"><strong>HttpClient Debug:</strong> '.$msg;
            if ($object) {
                ob_start();
                print_r($object);
                $content = htmlentities(ob_get_contents());
                ob_end_clean();
                print '<pre>'.$content.'</pre>';
            }
            print '</div>';
        }
    }   
}