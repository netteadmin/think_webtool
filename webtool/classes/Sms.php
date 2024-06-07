<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;  
use helper_v3\Sms as SmsHelper;  
class Sms{
    protected $sms;
    public $msg;
    /**
    * 短信信息
    */
    public function get_info()  { 
         $d = SmsHelper::less();
         $d['total'] = bcadd($d['sms_less'],$d['sms_used']);
         return $d;
    }
    /**
    * 发送短信
    */
    public function send($phone,$message,$sign)  {
         if(!$phone || !$message || !$sign){
            return  ['code'=>250,'msg'=>'参数异常','type'=>'error'];
         }
         $nid = webtool_log('sms','发送短信',['phone'=>$phone,'message'=>$message,'sign'=>$sign]);
         $res = SmsHelper::send($phone, $message, $sign);
         if($res['code'] == 0){
            update_webtool_log($nid,['api_data'=>'发送成功'],'ok');
         }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
         }
         return $res;
    }
    /**
    * 阿里云短信
    */
    public function send_ali($phone,$template,$data = []){
        $sms = $this->init();
        $nid = webtool_log('sms','阿里云发送短信',['phone'=>$phone,'template'=>$template,'data'=>$data]);
        try {
           $res  = $sms->send($phone, [ 
                'template' => $template,
                'data'     => $data,
            ], ['aliyun']); 
            update_webtool_log($nid,['api_data'=>'发送成功'],'ok'); 
            return $res; 
        } catch (\Exception $e) {
            $msg = $e->getExceptions()['aliyun']->getMessage();
            $this->msg = $msg;
            update_webtool_log($nid,['api_data'=>$msg],'error'); 
            return false;
        }   
    }
    /**
    * 腾讯云短信
    */
    public function send_qcloud($phone,$template,$data = []){
        $sms = $this->init();
        $nid = webtool_log('sms','腾讯云发送短信',['phone'=>$phone,'template'=>$template,'data'=>$data]);
        try {
           $res  = $sms->send($phone, [ 
                'template' => $template,
                'data'     => $data,
            ], ['qcloud']); 
            update_webtool_log($nid,['api_data'=>'发送成功'],'ok'); 
            return $res; 
        } catch (\Exception $e) {
            $msg = $e->getExceptions()['qcloud']->getMessage();
            $this->msg = $msg;
            update_webtool_log($nid,['api_data'=>$msg],'error'); 
            return false;
        }   
    }

    protected function init(){
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0, 
            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class, 
                // 默认可用的发送网关
                'gateways' => [
                   'aliyun','qcloud'
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ], 
                'aliyun' => [
                    'access_key_id' => get_config('aliyun_accessid'),
                    'access_key_secret' => get_config('aliyun_accesskey'),
                    'sign_name' => get_config('aliyun_sms_name'),
                ], 
                'qcloud' => [
                    'sdk_app_id' => get_config('qcloud_sms_app_id'), // 短信应用的 SDK APP ID
                    'secret_id'  =>  get_config('COS_SECRET_ID'), // SECRET ID
                    'secret_key' => get_config('COS_SECRET_KEY'), // SECRET KEY
                    'sign_name'  => get_config('qcloud_sms_sign_name'), // 短信签名
                ]
            ],
        ];
        $this->sms = new \Overtrue\EasySms\EasySms($config);
        return $this->sms;
    }
}