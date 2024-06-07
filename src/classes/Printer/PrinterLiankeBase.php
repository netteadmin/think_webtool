<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\Printer;  

/**
 * 链科打印机硬件接口
 * 官网 https://cloud.wisiyilink.com/ 
 * 手册 https://documenter.getpostman.com/view/1758872/SWE83H6u?version=latest
 */ 
class PrinterLiankeBase
{ 
  // 声明属性
  public $api_key = '';
  public $server = "https://cloud.wisiyilink.com/";
  public $timeout = 10;
  public $debug = false;
  public $device_id = '';
  public $device_key = '';

  public function __construct($device_id, $device_key,  $timeout = 10)
  {
    $this->api_key = get_config("printer_lianke_key");
    if (!$this->api_key) {
      trace('LianKe api key未配置', 'error');
    }
    $this->timeout = $timeout;
    $this->device_id = $device_id;
    $this->device_key = $device_key; 
  }

  private function requests($method, $endpoint, $fields = array(), $content_type = 'application/json')
  { 
     if ($method == 'POST' && $content_type == 'application/json') {
        $fields = json_encode($fields);
      }
      $curl = curl_init();
      $headers = array(
        "ApiKey: " . $this->api_key
      );
      if ($method == "POST") {
        array_push($headers, 'Content-Type: ' . $content_type);
      }
      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->server . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 1,
        CURLOPT_TIMEOUT => $this->timeout,
        CURLOPT_FOLLOWLOCATION => true,
        CURLINFO_HEADER_OUT => $this->debug,
        CURLOPT_VERBOSE => $this->debug,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_HTTPHEADER => $headers,
      ));
      $response = curl_exec($curl);
      $info = curl_getinfo($curl);  
      $data = json_decode($response,true);
      if ($data['code'] != 200) {
        return [];
      }
      return $data;
  }
  /*
  * 设备信息
  */
  public function get_info()
  {

    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
    );
    $nid = webtool_log('PrinterLianke','链科打印-设备信息-'.$this->device_id,$data);
    $res = $this->requests("GET", 'api/device/device_info?' . http_build_query($data)); 
    update_webtool_log($nid,['res'=>$res]);
    return $res;
  }
  /**
   * 获取打印机参数
   */
  public function get_par(){
    $res1 = $this->get_info();
    $res = $this->get_printer_name();
    $res1['data']['io_model'] = $res['io_model'];
    $res1['data']['api_data_1'] = $res['api_data_1'];
    return $res1;
  }
  public function get_printer_name(){
    $all = $this->get_printer_list();
    $row1 = $all['data']['row'][0];
    $printer_name = $row1['printer_name'];
    $drivce_name = $row1['drivce_name'];
    $res = [];
    $res['io_model'] =  $printer_name;
    $res['api_data_1'] =  $drivce_name;
    return $res;
  }
  /*
  * 刷新设备信息，包括打印机信息
  */
  public function refresh()
  {
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
    );
    $response = $this->requests("GET", 'api/device/refresh_device_info?' . http_build_query($data)); 
    return $response;
  }
  /*
  * 打印机列表
  */
  public function get_printer_list()
  {
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
    );
    $nid = webtool_log('PrinterLianke','链科打印-打印机列表-'.$this->device_id,$data);
    $res = $this->requests("GET", 'api/external_api/printer_list?' . http_build_query($data));
    update_webtool_log($nid,['res'=>$res]);
    return $res;
  }
  /**
   * 获取打印机参数
   * printerModel 打印机型号，对应printer_list中driver_name
   */
  public function get_printer_par($printer_model)
  {
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
      "printerModel" => $printer_model,
    );
    $nid = webtool_log('PrinterLianke','链科打印-打印机参数-'.$this->device_id,$data);
    $d = $this->requests("GET", 'api/print/printer_params?' . http_build_query($data));
    $api_data = $d; 
    $d = $d['data']['Capabilities'];
    $list = []; 
    foreach($d as $k=>$v){
        $k = strtolower($k);
        $list[$k] = $v;
    }
    $list['media_type'] = $list['mediatypes'];
    unset($list['mediatypes']); 
    update_webtool_log($nid,['list'=>$list,'api_data'=>$api_data]);
    return $list;
  }

  /*public function get_pages($printer_model, $file)
  {
    $file = str_replace(PATH, '', $file);
    if (substr($file, 0, 1) == '/') {
      $file = substr($file, 1);
    }
    $file = host() . $file;

    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
      "printerModel" => $printer_model,
      "devicePort" => 1,
      "dmPaperSize" => 9,
      "dmOrientation" => 1,
      "jobFile" => $file
    );

    $res = $this->requests("POST", 'api/print/file_pages', $data, "application/x-www-form-urlencoded");
    return $res['data']['pages'] ?? 1;
  }*/
  /**
  * 统一参数
    dmPaperSize：0(必填) 打印纸张尺寸 9：A4  11：A5，可取值：获取打印机参数：Capabilities -> Papers
    dmOrientation： 打印纸张方向1：竖向 2：横向
    dmCopies: 1：打印一份 最大不能超过打印机参数：Capabilities -> Copies
    dmDefaultSource: 纸张来源 可取值：获取打印机参数：Capabilities -> Bins
    dmColor: 打印颜色1：黑白 2：彩色 默认值：获取打印机参数：DevMode -> Color
    dmDuplex: 双面打印1：关闭 2：长边 3：短边
    dmMediaType:256 打印纸张类型 可取值：获取打印机参数：Capabilities -> MediaTypes
    dmPaperLength: 300 自定义高，dmPaperSize等于0时生效，单位0.1mm
    dmPaperWidth:200 自定义宽，dmPaperSize等于0时生效，单位0.1mm
    dmPrintQuality:打印质量，可选值-1，-2，-3，-4；-1质量最低，-4质量最高
    isPreview:预览，默认0，isPreview=1任务结果返回预览图片
    jpPageRange:文档页数范围例如：1,2,3,4,5-10（为空则全部打印）特殊值： -1：打印奇数页 -2：打印偶数页
    jpAutoScale:4(必填) 自动缩放4 : 自适应(推荐)0 : 原图打印(由于dpi原因，可能会过小)1: 宽度优先(超出时裁剪高度)2: 高度优先(超出时裁剪宽度)'3 : 拉伸全图xx% : 自定义, 纸张的百分比
    jpAutoAlign: z5
      自动对齐
                  <空>：左上（同z1, 默认）
                  z1: 左上
                  z4: 左中
                  z7: 左下
                  z2: 中上
                  z5: 中
                  z8: 中下
                  z3: 右上
                  z6: 右中
                  z9: 右下
    callbackUrl：https://xxx.com/xxxxx 打印结果回调 

  */
  protected function review_config($option){ 
    $paper_arr = [
      'a4'=>[
        //纸张类型 media_type
        'dmMediaType'=>1, 
        //纸张来源 bins
        'dmDefaultSource'=>276,
        //a4  papers
        'dmPaperSize'=>9, 
      ],
      // 6寸照片
      'photo_c6'=>[
        'dmMediaType'=>325,
        'dmDefaultSource'=>276,
        'dmPaperSize'=>123,  
      ],
      'photo_ai'=>[
        'dmMediaType'=>325,
        'dmDefaultSource'=>276, 
        'dmPaperSize'=>123,  
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
    $config = $option['config'];
    $color = $config['color']==1?'1':'2';
    $single = $config['single'];
    $copies = $config['copies']?:1;
    $start = $config['start'];
    $end = $config['end'];
    //1：竖向 2：横向
    $direction = $config['direction']?:1; 
    $type = $config['type'];
    if($config){ 
      $option = [];
      $copies = (string)$config['copies'];
      $page_range = $start."-".$end;
      if($start == $end){
        $page_range = $start;
      } 
      $option = $paper_arr[$type];  
      $option['dmCopies']  = $copies; 
      $option['dmOrientation']  = $direction; 
      $option['dmCopies']  = $copies; 
      $option['dmDuplex']  = $single; 
      $option['dmPrintQuality']  = -4; 
      //文档页数范围例如：1,2,3,4,5-10（为空则全部打印）特殊值： -1：打印奇数页 -2：打印偶数页
      if(strpos($type,'photo')!==false){
        $option['jpPageRange'] = ''; 
      }else{
        $option['jpPageRange'] = (string)$page_range;
      }
      //4(必填) 自动缩放4 : 自适应(推荐)0 : 原图打印(由于dpi原因，可能会过小)1: 宽度优先(超出时裁剪高度)2: 高度优先(超出时裁剪宽度)'3 : 拉伸全图xx% : 自定义, 纸张的百分比
      $option['jpAutoScale'] = 4;
      $option['jpAutoAlign'] = 'z5';
      //$option['callbackUrl'] = '';
      return $option;
    }else{
      return $option;
    }
  }
  /*
  * 发起打印任务  
  */
  public function add_job($printer_model, $file, $opt = array())
  { 
    $file_url = $opt['url'];
    $order_id = $opt['order_id'];
    $order_detail_id = $opt['order_detail_id'];
    $opt = $this->review_config($opt);
    $data = array(
      "deviceId"     => $this->device_id,
      "deviceKey"    => $this->device_key,
      "devicePort"   => 1,
      "printerModel" => $printer_model,
      "jobFile"      => $file
    );   
    $data = array_merge($data, $opt); 
    $nid = webtool_log('PrinterLianke','链科执行打印'.$this->device_id,$data); 
    $res = $this->requests("POST", 'api/print/job', $data, "multipart/form-data");
    $job_id =  $res['data']['task_id'];
    if($job_id){
         update_webtool_log($nid,['job_id'=>$job_id,'api_data'=>$res]);
         $res = db_get("webtool_printer_task",['drive'=>'lianke','job_id'=>$job_id],1);
         if(!$res){
            db_insert("webtool_printer_task",[
              'drive'=>'lianke',
              'order_id'=>$order_id,
              'order_detail_id'=>$order_detail_id,
              'printer_id' => $printer_model,
              'job_id'=>$job_id,
              'file_name'=>$file,
              'created_at'=>now(),
              'url' => $file,
              'status'=>'progress',
            ]);
         }
    }     
    return $job_id;
  }

  /*
  * 获取任务状态
  */
  public function get_job($task_id,$device_port = 1)
  {
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
      "devicePort" => $device_port,
      "task_id" => $task_id,
    );
    $nid = webtool_log('PrinterLianke','链科获取打印任务状态-'.$this->device_id,$data);
    $res = $this->requests("GET", 'api/print/job?' . http_build_query($data));
    $d = $res['data'];
    $api_data = $d;
    $arr = [
        'READY' => '排队中',
        'PARSING' => '解析中',
        'SENDING' => '发送中',
        'SUCCESS' => '成功',
        'FAILURE' => '失败',
        'SET_REVOKE' =>'标记为撤回',
        'REVOKED' => '撤回成功',
    ];
    $d['msg']  = $arr[$d['task_state']];
    $d['time'] = date("Y-m-d H:i:s",strtotime($d['task_done_time']));
    update_webtool_log($nid,['data'=>$d,'api_data'=>$api_data]);
    if($d['task_result']['code'] == 200){
      $d['code'] = 0; 
      return success_data(['info'=>['job_state'=>'finished']]);
    }else{
      $d['code'] = 250;
      $d['msg']  = $d['task_result']['msg']?:$d['msg'];
      return error_data(['msg'=>$data['msg'],'info'=>$d]);
    } 
  }
  /*
  * 取消任务
  */
  public function cancel_job($device_port, $task_id)
  { 
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
      "devicePort" => $device_port,
      "task_id" => $task_id,
    );
    $nid = webtool_log('PrinterLianke','链科打印-取消打印任务-'.$this->device_id,$data);
    $res = $this->requests("DELETE", 'api/print/job?' . http_build_query($data)); 
    update_webtool_log($nid,['api_data'=>$res]);
    return $res;
  }

  /*
  * 实时获取打印机状态
  此接口需要硬件升级到最新的版本
  
  api/device/printer_status
请求
  Array
(
    [deviceId] => lc01cy39959900
    [deviceKey] => X5wrpsNooBTLc92M
    [printerModel] => EPSON WF-C5290 Series
    [usbPort] => 1
)
返回
Array
(
    [code] => 500
    [msg] => 设备无响应
)

headOpened 盖子已开启
paperJam 卡纸
outOfPaper 缺纸
outOfRibbon 缺碳带 (只适用于带碳带功能的标签机型)
outOfInk 低墨量/碳粉 (只用于部分激光喷墨机型)
pause 打印机暂停
printing 打印中
msg 信息

  */
  public function get_status($printerModel)
  {
    $this->async_refresh_device_info();
    $data = array(
      "deviceId"     => $this->device_id,
      "deviceKey"    => $this->device_key,
      "printerModel" => $printerModel,
      "usbPort"      => 1,
    );
    $res = $this->requests("GET", 'api/device/printer_status?' . http_build_query($data));
    return $res;
  }
  /**
   * 内部使用
   */
  public function async_refresh_device_info()
  {
    $data = array(
      "deviceId" => $this->device_id,
      "deviceKey" => $this->device_key,
      "usbPort" => 1,
    );
    $res = $this->requests("GET", 'api/device/async_refresh_device_info?' . http_build_query($data));
    return $res;
  }
} 