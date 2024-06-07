<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes;   
use app\applet\classes\weixin;
/**
 *  微信公众号 小程序 共用部分
 */ 
class WeProgram
{ 
    /**
    * 小程序中调用公众号接口取发票信息
    * $arr [{card_id:'',encrypt_code:''}]
    * https://developers.weixin.qq.com/doc/offiaccount/WeChat_Invoice/E_Invoice/Reimburser_API_List.html
    */
    public function get_invoice_infos($config_info=[],$arr){
        $init = weixin::init_mp(get_config('APP_ID'),get_config('APP_SECRET'));
        return $this->get_invoice_data($init,$arr);
    }
    /**
     * 公众号中调用发票信息
     */
    public function mp_get_invoice_infos($config_info=[],$arr){
        $init = weixin::init_mp();
        return $this->get_invoice_data($init,$arr);
    }
    
    protected function get_invoice_data($init,$arr){
        $api = $init['api']; 
        $list = [];
        if(!is_array($arr)){
            $arr = json_decode($arr,true);
        }
        if(!is_array($arr)){
            return error_data(['msg'=>'非法参数']);
        }
        $item_list = [];
        foreach($arr as $v){
           $card_id = $v['card_id'];
           $encrypt_code = $v['encrypt_code'];
           if($card_id && $encrypt_code){
            $item_list[] = [
                'card_id'      => $card_id,
                'encrypt_code' => $encrypt_code,
            ];
           } 
        }  
        if(!$item_list){
            return error_data(['msg'=>'非法参数']);
        } 
        $res = $api->postJson('/card/invoice/reimburse/getinvoicebatch',['item_list'=>$item_list]);   
        $res = json_decode($res->getContent(),true);  
        if($res['errcode'] == 0){
            $item_list = $res['item_list'];
            $new_list = [];
            foreach($item_list as $v){
                $pdf_url = $v['user_info']['pdf_url'];
                $new_pdf_url = '/uploads/wx_invoice/'.md5($pdf_url).'.pdf';
                $pdf_file = WWW_PATH.$new_pdf_url;
                $dir = get_dir($pdf_file);
                create_dir_if_not_exists($dir);
                $title = $v['payee'];
                $content = file_get_contents($pdf_url); 
                file_put_contents($pdf_file,$content);
                $new_list[] = [
                    'title'=>$title,
                    'url'=>$new_pdf_url,    
                ];
            }
            return success_data($new_list); 
        }else{
            return error_data(['msg'=>'操作异常']);
        } 
    }
}