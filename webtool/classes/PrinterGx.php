<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;  
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client; 
class PrinterGx{  
	protected $serial_number;  
	public $user_id = 'SHXL1';   
	/**
	* 初始化
	* 具体查看API接口参数
	* @return object
	*/
	public function init($serial_number){ 
		$this->serial_number = $serial_number; 
		return $this;
	}

	/**
	* 获取端口信息-在线掉线
	* {device_id:""}
	*/
	public function get_info($opt = []){   
		$serial_number = $opt['device_id']; 
		if(!$serial_number){
			return error_data(['msg'=>'GX 缺少序列号'.$serial_number]);
		}
		$res = $this->run([
			'cmd'=>'cloudprint/printer/connectid', 
		    'par'=>['serial_number' => $serial_number],
		]);
		$nid = webtool_log('PrinterGX','佳能-获取端口信息-'.$serial_number,['api_data_1'=>$res]);
		$connect_id = $res['connect_id'];
		if($connect_id){
		  $par = [
		    'connect_id' => $connect_id,
		    'user_id'=>$this->user_id,
		    'user_country'=>'China',
		    'user_city'=>'ShangHai',
		    'user_sex'=>1,
		  ]; 
		  $res = $this->run(['cmd'=>'cloudprint/printer/registration','par'=>$par]); 
		  $printer_id = $res['printer_id'];
		  if($printer_id){ 
		  	$res = $this->get_status(['printer_id'=>$printer_id],true);
		  	update_webtool_log($nid,['api_data'=>$res]);
		  	$info = [];
		  	$info['status'] = 'offline';
		  	if($res['device_connection_status'] == 'online'){
		  		$info['status'] = 'online';
		  	}
		  	$info['printer_model'] = $res['printer_model'];
		  	$info['printer_id'] = $printer_id;
		  	return success_data(['info'=>$info]);
		  }
		}
		return error_data(['msg'=>'GX 注册失败,序列号'.$serial_number,'res'=>$res]);
	}

	/**
	* 获取状态
	*/
	public function get_status($opt = [],$show_full = false)
	{ 
		 $printer_id = $opt['printer_id'];
		 $par = [
			  'printer_id' => $printer_id,
			  'user_id' => $this->user_id,
		 ];  
		 $res = $this->run(['cmd'=>'cloudprint/printer/status','par'=>$par]);
		 $res['printer_capability'] = json_decode($res['printer_capability'],true);
		 if($show_full){
		 	return $res;
		 } 
		 return $res['device_connection_status']; 
	}
 
	/**
	* 获取某个打印机参数
	*/
	public function get_printer_par($opt = [],$printer_id){ 
		$res = $this->get_status(['printer_id'=>$printer_id],true);
		return success_data($res);
	}
	/**
	* 统一参数
	*/
	protected function review_config($option){  
		$paper_arr = [
			'a4'=>[
				//纸张类型
				'MediaTypeClass' =>'mediatype_01',
				//a4 
				'OutputMediaSize'=>'mediasize_02', 
			],
			/*'a5'=>[
				//纸张类型
				'MediaTypeClass' =>'mediatype_01',
				//a5
				'OutputMediaSize'=>'mediasize_26', 
			], */
			// 6寸照片
			'photo_c6'=>[
				'MediaTypeClass' =>'mediatype_07',
				'OutputMediaSize'=>'mediasize_01',  
			],
			 
		]; 
		//任务分发过来的
		$printer = $option['printer'];
        $drive = $printer['drive'];
        $printer_id = $printer['api_io_id'];
        $api_io_model = $printer['api_io_model']; 
        $key1 = $printer['key1'];
        $key2 = $printer['key2'];
        $url = $option['url']; 
		$_config = $option['config'];
        $color = $_config['color']==1?'Grayscale':'Color';
        $single = $_config['single']==1?'OneSided':'TwoSidedLongEdge';
        $copies = $_config['copies']?:1;
        $start = $_config['start'];
        $end = $_config['end'];
        $direction = $_config['direction']; 
        $type = $_config['type'];
        if($_config){ 
        	$option = [];
			$copies = (string)$_config['copies'];
			/*
			可组合使用以下 3 种格式(逗号分隔)
			[1]: 1,3,5
			[2]: 10-15
			[3]: 20-
			注：组合使用时格式[3]必须放在最后
			*/
			$page_range = $start."-".$end;
			if($start == $end){
				$page_range = $start;
			} 
			$setting = $paper_arr[$type]; 
			if(!$setting){
				if(strpos($type,'photo') !== false){
					$setting = $paper_arr['photo_c6'];
				}else{
					$setting = $paper_arr['a4'];
				}
			} 
			$setting['LayoutType'] = 'Normal';
			$setting['OutputColor']=$color;
			$setting['Duplex']=$single; 
			$option['setting'] = $setting;
			$option['copies']  = $copies; 
			$option['page_range'] = (string)$page_range?:"1"; 
			if(strpos($type,'photo_') !== false){
				unset($option['page_range']);
			} 
			return $option;
        }else{
        	return $option;
        }
	}
	/**
	* 提交打印任务
	*/
	public function add_job($opt = [],$printer_id, $file_name, $optional_array){  
		$file_url = $opt['url'];
		$order_id = $opt['order_id'];
		$order_detail_id = $opt['order_detail_id']; 
		$optional_array = $this->review_config($optional_array);
		$par = [
		      'printer_id' => $printer_id,
		      'user_id'   => $this->user_id,
		      'file_name' => $file_name, 
		      'job_setting'=>$optional_array['setting'],
		      //打印份数设置
		      'print_copies_number'=>$optional_array['copies']?:1, 
		      'page_range'=>$optional_array['page_range'],
		      'callback_url'=>host().'/webtool/ApiPrinterGxCall/index',
	     ];   
	     $nid = webtool_log('printer','GX执行打印'.$printer_id,$par,false); 
	     insert_trace($par,"printer_gx"); 
		 $res = $this->run(['cmd'=>'cloudprint/printjobsubmit','par'=>$par]);  
	     $upload_url = $res['upload_url'];
	     $flag = 'ok';
	     if(!$upload_url){
	     	$flag = 'error';
	     }
	     update_webtool_log($nid,['upload_url'=>$upload_url,'api_data'=>$res],$flag);
	     $job_id = $res['job_id'];
	     if($job_id){
	     	 trace("gx download_file start:".now(),'info');
	     	 $new_url = download_file($file_url);
	     	 trace("gx download_file finish:".now(),'info');
	     	 trace("gx download_file url:".$new_url,'info');
       		 $file = WWW_PATH.$new_url; 
       		 if(!file_exists($file)){
       		 	trace("gx download_file error:".now(),'info');
       		 }
	     	 $res = db_get("webtool_printer_task",['drive'=>'gx','job_id'=>$job_id],1);
		     if(!$res){
		     	db_insert("webtool_printer_task",[
		     		'drive'=>'gx',
		     		'order_id'=>$order_id,
		     		'order_detail_id'=>$order_detail_id,
		     		'printer_id' => $printer_id,
		     		'job_id'=>$job_id,
		     		'file_name'=>$file_name,
		     		'created_at'=>now(),
		     		'url' => $new_url,
		     		'status'=>'progress',
		     	]);
		     } 
			$headers = [];  
			$body    = file_get_contents($file);   
			$client  = new Client();
			$request = new Request('PUT', $upload_url, $headers, $body);
			$response = $client->send($request, ['timeout' => 600]);
			$code = $response->getStatusCode();
			$body = (string)$response->getBody(); 
			trace("gx upload file ".$code);
			trace("gx upload getBody ".$body);
	     }else{
	     	return false;
	     }
	      
	}

	/**
	* 获取打印任务状态
	*/
	public function get_job($opt = [],$printer_id,$task_id){   
		$par['printer_id']   = (string)$printer_id; 
        $par['print_job_id'] = (string)$task_id;
        $res = $this->run(['cmd'=>'cloudprint/printer/printjobstatus','par'=>$par]);  
        return success_data(['info'=>$res,'par'=>$par]);
	}

	/**
	* 取消打印任务
	*/
	public function cancel_job($opt = [],$printer_id,$task_id){  
		$par['printer_id'] = $printer_id; 
        $par['job_ids'] = $task_id;
        $res = $this->run(['cmd'=>'cloudprint/printjob/cancel','par'=>$par]);  
        return success_data($res);
	}
	/**
	* 类型 
	*/
	public function get_media_papers(){
		return [
		  'mediasize_01'=> '10x15cm(4"x6")',
		  'mediasize_02'=> 'A4',
		  'mediasize_03'=> 'L(89x127mm)',
		  'mediasize_04'=> '2L(127x178mm)',
		  'mediasize_05'=> '卡片(90×55mm)',
		  'mediasize_06'=> '20x25cm(8"x10")',
		  'mediasize_15'=> '多用途托盘',
		  'mediasize_19'=> '正方形 13x13cm',
		  'mediasize_20'=> '正方形 9x9cm',
		  'mediasize_23'=> '正方形 10x10cm',
		  'mediasize_26'=> 'A5',
		  'mediasize_27'=> 'B5',
		  'mediasize_28'=> '13x18cm(5"x7")',
		  'mediasize_35'=> 'A3',
		  'mediasize_36'=> 'A6',
		  'mediasize_40'=> '28x43cm(11"x17")',
		];
	}
	/**
	* 纸张类型
	*/
	public function get_media_types()
	{
		 return [
		    'mediatype_01' =>'普通纸',
		    'mediatype_04' =>'高级光面照片纸',
		    'mediatype_06' =>'优质专业照片纸',
		    'mediatype_07' =>'亚高光泽照片纸',
		    'mediatype_18' =>'专业绒面照片纸',
		    'mediatype_23' =>'可打印光盘',
		    'mediatype_33' =>'标签纸',
		 ];
	}


	/**
	* 调用python3
	*/
	protected function run($opt = [])
	{ 
		 $cmd = $opt['cmd']?:'cloudprint/printer/connectid';
		 $par = $opt['par']; 
		 $par = json_encode($par);
		 $python_cli = get_config("python")?:'/usr/bin/python3';
		 $cmd = $python_cli." ".__DIR__."/Printer/PrinterGxPython.php  -url $cmd -d '".$par."' 2>&1";
		 exec($cmd,$o);
		 trace("gx cmd:".$cmd,'info');
		 trace($o,'info');   
		 foreach($o as $v){
		 	if(is_json($v)){
		 		$d = json_decode($v,true);
		 		return $d;
		 	}
		 } 
	}
}