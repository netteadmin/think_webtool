<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
class Ocr{
    /**
    * 查看支持的列表
    */
    public function get_info(){
        return [
            '印刷文字识别_营业执照识别'=>'https://market.aliyun.com/products/57124001/cmapi013592.html',
            '医疗器械经营许可证'=>'https://market.aliyun.com/products/57124001/cmapi00042850.html',
            '第二类医疗器械经营备案凭证'=>'https://market.aliyun.com/products/57124001/cmapi00042851.html',
            '医疗器械生产许可证'=>'https://market.aliyun.com/products/57124001/cmapi00042848.html',
            '医疗机构执业许可证'=>'同 医疗器械经营许可证',
            '全电发票_PDF格式电子发票(有点贵)'=>'https://market.aliyun.com/products/57124001/cmapi031135.html',
            '发票抬头模糊查询 '=>'https://market.aliyun.com/products/56928005/cmapi00054686.html',
            '智能试卷擦除'=>'https://market.aliyun.com/products/57124001/cmapi00062251.html', 
            '表格识别'=>'https://market.aliyun.com/products/57124001/cmapi024968.html', 
        ];
    }
 
    /*
    * 发票抬头模糊查询 
    */ 
    public function get_invoce_title($title){ 
        $req = 'http://fp.81api.com/fuzzyQueryInvoiceTitle/'.$title."/?isRaiseErrorCode=0"; 
        $body = [
           
        ];    
        $nid = webtool_log('ocr','发票抬头模糊查询',['get'=>$title]);
        $res = curl_aliyun($req,$body,'GET');    
        $list = $res['data']['list'];
        $d = [];
        if($res['status'] == 1 && $list){
            foreach($list as $v){
                $d[] = $v['companyname'];
            }
            update_webtool_log($nid,['api_data'=>$list],'ok');
            return ['data'=>$d,'code'=>0,'type'=>'success']; 
        }
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    } 

    /*
    * 开具发票信息查询 
    */ 
    public function get_invoce_company_info($company_name){
        $ori_url = $company_name; 
        $req = 'http://fp.81api.com/getInvoiceTitleInfo/'.$company_name.'/?isRaiseErrorCode=0'; 
        $body = [
           
        ];     
        $nid = webtool_log('ocr','开具发票信息查询',['get'=>$company_name]);
        $res = curl_aliyun($req,$body,'GET');    
        $list = $res['data']; 
        if($res['status'] == 1 && $list){ 
            update_webtool_log($nid,['api_data'=>$list],'ok');
            return ['data'=>$list,'code'=>0,'type'=>'success']; 
        }
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    } 


    /*
    * 全电发票_PDF格式电子发票 
    */ 
    public function get_invoce_pdf($url){
        $ori_url = $url; 
        $req = 'https://vatinvoice.market.alicloudapi.com/ai_market/ocr/digital_invoice/qd/pdf/v1';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'URL'=>  host().$new_url,  
        ];   
        $nid = webtool_log('ocr','全电发票_PDF格式电子发票',['img'=>$ori_url]);
        $res = curl_aliyun($req,$body,'GET');   
        if($res['电子发票（全电票）识别状态']){
            $data  = $res['电子发票（全电票）实体信息']; 
            update_webtool_log($nid,['api_data'=>$data],'ok');
            return ['data'=>$data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    } 

    

    /*
    * 医疗器械生产许可证 
    */ 
    public function get_medical_device_plicense($url){
        $ori_url = $url; 
        $req = 'https://medicalshc.market.alicloudapi.com/ocrservice/medicalDevicePlicense';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'img'=> base64_encode(file_get_contents($file)),  
        ];   
        $nid = webtool_log('ocr','医疗器械生产许可证',['img'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['data']){
            $data  = $res['data']; 
            update_webtool_log($nid,['api_data'=>$data],'ok');
            return ['data'=>$data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    } 
    /*
    * 医疗机构执业许可证 同 医疗器械经营许可证
    */
    public function get_medical_institution($url){
        $ori_url = $url; 
        $req = 'https://medicaljy.market.alicloudapi.com/ocrservice/medical_institution_plicense';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'image'=> base64_encode(file_get_contents($file)),  
        ];   
        $nid = webtool_log('ocr','医疗机构执业许可证',['img'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['data']){
            $data  = $res['data']; 
            update_webtool_log($nid,['api_data'=>$data],'ok');
            return ['data'=>$data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    }  

    /** 
    * 第二类医疗器械经营备案凭证
    */
    public function get_medical_cert_2($url){
        $ori_url = $url;
        $req = 'https://medical2nd.market.alicloudapi.com/ocrservice/medicalDeviceRecord';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'img'=> base64_encode(file_get_contents($file)),
        ];  
        $nid = webtool_log('ocr','第二类医疗器械经营备案凭证',['image'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['data']){
            $data  = $res['data']; 
            update_webtool_log($nid,['api_data'=>$data],'ok');
            return ['data'=>$data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250]; 
    } 

    /** 
    * 医疗器械经营许可证
    */
    public function get_medical_device_business_license($url){
        $ori_url = $url; 
        $req = 'https://medicaljy.market.alicloudapi.com/ocrservice/medicalDeviceBlicense';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'img'=> base64_encode(file_get_contents($file)),  
        ];   
        $nid = webtool_log('ocr','医疗器械经营许可证',['img'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['data']){
            $data  = $res['data'];
            $data['备案凭证号'] = str_replace("凭证号：","",$data['备案编号']);
            update_webtool_log($nid,['api_data'=>$data],'ok');
            return ['data'=>$data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$res],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    } 
    /** 
    * 印刷文字识别_营业执照识别
    */
    public function get_business_license($url){
        $ori_url = $url;
        $req = 'https://bizlicense.market.alicloudapi.com/rest/160601/ocr/ocr_business_license.json';
        $new_url = download_file($url);
        $file = WWW_PATH.$new_url;
        $body = [
            'image'=> base64_encode(file_get_contents($file)),
        ];  
        $nid = webtool_log('ocr','营业执照',['image'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['success'] ==  1){
            $new_data = [];
            $new_data['类型']        = $res['type'];
            $new_data['名称']        = $res['name'];
            $new_data['法定代表人']   = $res['person']; 
            $new_data['住所']        = $res['address'];
            $new_data['经营范围']     = $res['business'];
            $new_data['注册资本']     = $res['capital'];
            $new_data['成立日期']     = $res['establish_date'];
            $new_data['统一社会信用代码']   = $res['reg_num'];
            $new_data['营业期限']          = $res['valid_period'];  
            update_webtool_log($nid,['api_data'=>$new_data],'ok');
            return ['data'=>$new_data,'code'=>0,'type'=>'success']; 
        }  
        trace($res['msg'],'error');
        update_webtool_log($nid,['api_data'=>$new_data],'error');
        return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
    }
    /**
    * 表格图片识别
    */
    public function get_table($url){
        $ori_url = $url;
        $req = 'https://form.market.alicloudapi.com/api/predict/ocr_table_parse';
        $new_url = download_file($url); 
        $file = WWW_PATH.$new_url;
        $body = [ 
                'image'=> base64_encode(file_get_contents($file)),
                'configure'=>[ 
                    'format'=>'json'
                ], 
        ]; 
        $nid = webtool_log('ocr','表格',['image'=>$ori_url]);
        $res = curl_aliyun($req,json_encode($body),'POST');  
        if($res['success'] == 1){
            $all = $res['tables']; 
            $body['configure']['format'] = 'html';
            $html = curl_aliyun($req,json_encode($body),'POST'); 
            $table = $html['tables'];
            $d = [];  
            foreach($all as $vv){
             foreach($vv as $v){
                if(is_array($v)){
                    foreach($v as $v1){  
                        if(is_array($v1) && $v1['text']){
                            $d[] = implode(" ",$v1['text']);    
                        }                        
                    }
                }
                
              }
            } 
            update_webtool_log($nid,['api_data'=>$d],'ok');
            return ['data'=>['array'=>$d,'table'=>$table],'code'=>0,'type'=>'success']; 
        }else{
            update_webtool_log($nid,['api_data'=>$res],'error');
            return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
        } 
    }  
    /**
    * 智能试卷擦除(推荐)
    * https://www.textin.com/market/detail/text_auto_removal
    */
    public function get_shijuan_textin($url,$options = []){
         $obj = new SerTextin;
         return $obj->text_auto_removal($url,$options);
    }

    /**
    * 智能试卷擦除 (效果一般)
    * https://market.aliyun.com/products/57124001/cmapi00062251.html
    */
    public function get_shijuan($url){
        $ori_url = $url;
        $nid = webtool_log('ocr','智能试卷擦除',['image'=>$ori_url]);
        $req = 'https://sjccup.market.alicloudapi.com/sjccup';
        $new_url = download_file($url); 
        $file = WWW_PATH.$new_url;
        $content = base64_encode(file_get_contents($file));        
        $body = [ 
           'media_id'=> $content, 
           'keep_distortion'=>false,
           'keep_ori'=>false,
        ];  
        $res = curl_aliyun($req,json_encode($body),'POST');  
        $mediaId = base64_decode($res['data']['data']['mediaId']);
        if($mediaId){  
            $new_url = '/uploads/tmp/'.date("Y-m-d").'/';
            $new_dir = WWW_PATH.$new_url;
            if(!is_dir($new_dir)){
                mkdir($new_dir,0777,true);
            }
            $name = 'get_shijuan_'.md5($url);
            $new_file = $new_dir.$name.'.png';
            file_put_contents($new_file,$mediaId);
            $new_url = host().$new_url.$name.'.png'; 
            update_webtool_log($nid,['api_data'=>$new_url],'ok');
            return ['data'=>['url'=>$new_url],'code'=>0,'type'=>'success']; 
        }else{
            update_webtool_log($nid,['api_data'=>''],'error');
            return ['msg'=>$res['msg'],'code'=>250,'type'=>'error']; 
        }  
    }

    /**
    * 图像切边增强
    * https://www.textin.com/market/detail/crop_enhance_image
    */
    public function crop_enhance_image($url,$options = []){
         $obj = new SerTextin;
         return $obj->crop_enhance_image($url,$options);
    }
  
        


}