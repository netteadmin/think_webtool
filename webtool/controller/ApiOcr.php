<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\Ocr as Api;

/**
* @Apidoc\Title("API OCR（收费）")
*/
class ApiOcr extends ApiController
{  
    public $api;
    public $guest = false;
    public $url;
    public function init(){
        parent::init();
        $this->api = new Api;
        $this->url = $this->input['url'];
    }
    /**
    * @Apidoc\Title("查看支持的列表") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr")
    * @Apidoc\Method("POST")     
    */
    public function get_info(){
        return json_success(['data'=>$this->api->get_info()]);
    }
    /**
    * @Apidoc\Title("发票抬头模糊查询(效果一般)") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr <br>购买地址: https://market.aliyun.com/products/56928005/cmapi00054686.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="公司名称")    
    */
    public function get_invoce_title(){ 
        return $this->do('get_invoce_title'); 
    }
    /**
    * @Apidoc\Title("开具发票信息查询(效果一般)") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/56928005/cmapi00054686.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="公司名称")   
    * @Apidoc\Returned("taxpayerName",type="string",require=true,desc="公司名称")
    * @Apidoc\Returned("taxpayerNo",type="string",require=true,desc="税号")   
    * @Apidoc\Returned("taxpayerTelephone",type="string",require=true,desc="电话")       
    * @Apidoc\Returned("taxpayerAddress",type="string",require=true,desc="地址")        
    * @Apidoc\Returned("taxpayerBankName",type="string",require=true,desc="银行名称")   
    * @Apidoc\Returned("taxpayerBankAccount",type="string",require=true,desc="银行帐号")      
    */
    public function get_invoce_company_info(){ 
        return $this->do('get_invoce_company_info'); 
    }
    
    /**
    * @Apidoc\Title("全电发票_PDF格式电子发票") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi031135.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("发票类型",type="string",require=true,desc="")     
    * @Apidoc\Returned("发票号码",type="string",require=true,desc="")     
    * @Apidoc\Returned("开票日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("购买方信息{名称,统一社会用代码纳税人识别号}",type="string",require=true,desc="")     
    * @Apidoc\Returned("销售方信息{名称,统一社会用代码纳税人识别号}",type="string",require=true,desc="")     
    * @Apidoc\Returned("项目实体信息[{项目名称,规格型号,单位,数量,单价,金额,税率征收率,税额}]",type="string",require=true,desc="")     
    * @Apidoc\Returned("合计（金额）",type="string",require=true,desc="")     
    * @Apidoc\Returned("合计（税额）",type="string",require=true,desc="")     
    * @Apidoc\Returned("价税合计（大写）",type="string",require=true,desc="")     
    * @Apidoc\Returned("价税合计（小写）",type="string",require=true,desc="")          
    * @Apidoc\Returned("开票人",type="string",require=true,desc="")          
    */
    public function get_invoce_pdf(){ 
        return $this->do('get_invoce_pdf'); 
    }

    
    /**
    * @Apidoc\Title("医疗器械生产许可证") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi00042848.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("企业名称",type="string",require=true,desc="")     
    * @Apidoc\Returned("企业负责人",type="string",require=true,desc="")     
    * @Apidoc\Returned("住所",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证部门",type="string",require=true,desc="")     
    * @Apidoc\Returned("有效期限",type="string",require=true,desc="")     
    * @Apidoc\Returned("法定代表人",type="string",require=true,desc="")     
    * @Apidoc\Returned("注册地址",type="string",require=true,desc="")     
    * @Apidoc\Returned("生产地址",type="string",require=true,desc="")     
    * @Apidoc\Returned("生产范围",type="string",require=true,desc="")          
    * @Apidoc\Returned("许可证编号",type="string",require=true,desc="")          
    */
    public function get_medical_device_plicense(){ 
        return $this->do('get_medical_device_plicense'); 
    } 
    /**
    * @Apidoc\Title("第二类医疗器械经营备案凭证") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi00042851.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("备案编号",type="string",require=true,desc="")     
    * @Apidoc\Returned("企业名称",type="string",require=true,desc="")     
    * @Apidoc\Returned("住所",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营场所",type="string",require=true,desc="")     
    * @Apidoc\Returned("库房地址",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营方式",type="string",require=true,desc="")     
    * @Apidoc\Returned("法定代表人",type="string",require=true,desc="")     
    * @Apidoc\Returned("企业负责人",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营范围",type="string",require=true,desc="")     
    * @Apidoc\Returned("备案日期",type="string",require=true,desc="")          
    */
    public function get_medical_cert_2(){ 
        return $this->do('get_medical_cert_2'); 
    }

    

    /**
    * @Apidoc\Title("医疗器械经营许可证") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi00042850.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("企业名称",type="string",require=true,desc="")     
    * @Apidoc\Returned("企业负责人",type="string",require=true,desc="")     
    * @Apidoc\Returned("住所",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证部门",type="string",require=true,desc="")     
    * @Apidoc\Returned("库房/仓库地址",type="string",require=true,desc="")     
    * @Apidoc\Returned("有效期限/许可期限",type="string",require=true,desc="")     
    * @Apidoc\Returned("法定代表人",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营场所",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营方式",type="string",require=true,desc="")     
    * @Apidoc\Returned("经营范围",type="string",require=true,desc="")     
    * @Apidoc\Returned("许可证编号",type="string",require=true,desc="")     
    * @Apidoc\Returned("证照标题",type="string",require=true,desc="医疗器械经营许可证")      
    */
    public function get_medical_device_business_license(){ 
        return $this->do('get_medical_device_business_license'); 
    }
    /**
    * @Apidoc\Title("医疗机构执业许可证") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:同 医疗器械经营许可证 https://market.aliyun.com/products/57124001/cmapi00042850.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("主要负责人",type="string",require=true,desc="")     
    * @Apidoc\Returned("医疗机构名称",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("发证机关",type="string",require=true,desc="")     
    * @Apidoc\Returned("地址",type="string",require=true,desc="")     
    * @Apidoc\Returned("有效终止日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("有效起始日期",type="string",require=true,desc="")     
    * @Apidoc\Returned("法定代表人",type="string",require=true,desc="")     
    * @Apidoc\Returned("登记号",type="string",require=true,desc="")     
    * @Apidoc\Returned("诊疗科目",type="string",require=true,desc="")    
    */
    public function get_medical_institution(){ 
        return $this->do('get_medical_institution'); 
    }
    /**
    * @Apidoc\Title("印刷文字识别_营业执照识别") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi013592.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")     
    * @Apidoc\Returned("类型",type="string",require=true,desc="法人商事主体【有限责任公司(自然人投资或控股)】")   
    * @Apidoc\Returned("名称",type="string",require=true,desc="")   
    * @Apidoc\Returned("法定代表人",type="string",require=true,desc="")   
    * @Apidoc\Returned("住所",type="string",require=true,desc="")   
    * @Apidoc\Returned("经营范围",type="string",require=true,desc="")   
    * @Apidoc\Returned("注册资本",type="string",require=true,desc="")   
    * @Apidoc\Returned("成立日期",type="string",require=true,desc="")   
    * @Apidoc\Returned("统一社会信用代码",type="string",require=true,desc="")   
    * @Apidoc\Returned("营业期限",type="string",require=true,desc="")   
    */
    public function get_business_license(){ 
        return $this->do('get_business_license'); 
    }
    /**
    * @Apidoc\Title("表格图片识别") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi024968.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")  
    * @Apidoc\Returned("array",type="string",require=true,desc="数组")      
    * @Apidoc\Returned("table",type="string",require=true,desc="html table代码")      
    */
    public function get_table(){ 
        return $this->do('get_table');  
    }
    /**
    * @Apidoc\Title("智能试卷擦除-textin-推荐") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://www.textin.com/market/detail/text_auto_removal")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")  
    * @Apidoc\Query("crop",type="string",require=true,desc="0 关闭切边操作，默认为0 1 执行切边操作")  
    * @Apidoc\Query("crop_position",type="string",require=true,desc="支持客户端传入原图对应切边坐标进行切边; 默认为自动切边坐标点； 调用时需执行切边操作(crop=1); 格式 x1,y1,x2,y2,x3,y3,x4,y4 (x1, y1) 左上角坐标 (x2, y2) 右上角坐标 (x3, y3) 右下角坐标 (x4, y4) 左下角坐标")  
    * @Apidoc\Query("doc_direction",type="string",require=true,desc="支持客户端传入旋转角度和自动判断方向并旋转，默认不旋转 0 关闭方向转正 1 顺时针旋转90度 2 顺时针旋转180度 3 顺时针旋转270度 4 自动方向转正")  
    * @Apidoc\Query("mask_position",type="string",require=true,desc="支持客户端传入原图对应擦除坐标点进行擦除; 默认整图区域; 格式 x1,y1,x2,y2,x3,y3,x4,y4 同crop_position")  
    * @Apidoc\Query("dewarp",type="string",require=true,desc="0 不执行弯曲矫正 1 执行切边操作")  

    * @Apidoc\Query("binarization",type="string",require=true,desc="0 不执行黑白锐化滤镜 1 执行黑白锐化滤镜， 默认为1")   

    * @Apidoc\Returned("url",type="string",require=true,desc="图片地址")          
    */
    public function get_shijuan_textin(){ 
        return $this->do('get_shijuan_textin');  
    } 
    /**
    * @Apidoc\Title("智能试卷擦除 aliyun market 效果一般") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://market.aliyun.com/products/57124001/cmapi00062251.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")  
    * @Apidoc\Returned("url",type="string",require=true,desc="图片地址")              
    */
    public function get_shijuan(){ 
        return $this->do('get_shijuan');  
    }

    /**
    * @Apidoc\Title("图像切边增强 textin") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/Ocr<br>购买地址:https://www.textin.com/market/detail/crop_enhance_image")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="url")               
    */ 
    public function crop_enhance_image(){
        return $this->do('crop_enhance_image');  
    }
    /**
    * 内部调用
    */
    protected function do($method){ 
        $url = $this->url;
        if(!$url){
            return json_error([]);
        }
        $input = $this->input?:[];
        $cache_id = "ocr:".md5($url.json_encode($input));
        $data = cache($cache_id);
        if($data){
            $data['cached'] = true;
            //return $data;
        }
        $d = $this->api->$method($url); 
        if($d['code'] == 0){
            cache($cache_id,$d,86400);
        }
        $data['cached'] = false;
        return json($d); 
    }
}