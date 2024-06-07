<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use app\webtool\classes\Printer\PrinterLiankeBase as Printer;
 
class PrinterLianke{  
	protected $printer; 
	public    $device_id; 
	public    $device_key;  
	protected function _init($arr = []){
		if(!$this->device_id){
			$device_id = $arr['device_id'];
			$device_key = $arr['device_key'];
			$debug = $arr['debug'];
			if(!$device_id || !$device_key){
				//throw new \Exception("请求异常", 403);				
			}
			$this->printer = new Printer($device_id,$device_key);
		}
	}
	/**
	* 获取状态
	*/
	public function get_status($opt = []){
		$res = $this->get_info($opt); 
		if($res['data']['info']['online'] == 1){
			return 'online';
		}else{
			return 'offline';
		}
	}
	/**
	* 获取端口信息-在线掉线
	*/
	public function get_info($opt = []){   
		$this->_init($opt); 
		return $this->printer->get_info();
	}

	/**
	* 获取端口下打印机列表
	*/
	public function get_printer_list($opt = []){ 
		$this->_init($opt);
		return $this->printer->get_printer_list();
	}

	/**
	* 获取某个打印机参数
	*/
	public function get_printer_par($opt = [],$printer_model){ 
		$this->_init($opt);
		return $this->printer->get_printer_par($printer_model);
	}

	/**
	* 提交打印任务
	*/
	public function add_job($opt = [],$printerModel, $file, $optional_array){  
		$this->_init($opt);
		return $this->printer->add_job($printerModel, $file, $optional_array);
	}

	/**
	* 获取打印任务状态
	*/
	public function get_job($opt = [],$task_id){ 
		$this->_init($opt);
		return $this->printer->get_job($task_id);
	}

	/**
	* 取消打印任务
	*/
	public function cancel_job($opt = [],$device_port,$task_id){ 
		$this->_init($opt);
		return $this->printer->cancel_job($device_port,$task_id);
	}
}