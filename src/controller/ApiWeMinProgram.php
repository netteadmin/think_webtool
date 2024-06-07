<?php
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/
namespace app\webtool\controller;

use app\ApiController; 
use think\facade\Db; 
use hg\apidoc\annotation as Apidoc;  
use app\webtool\classes\WeMinProgram as Api;

/**
* @Apidoc\Title("API 小程序相关")
*/
class ApiWeMinProgram extends ApiController
{ 
     
    public $api;
    public $guest = false;
    public function init(){
        parent::init();
        $this->api = new Api;
    }
    /**
    * @Apidoc\Title("获取不限制的小程序码") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/WeMinProgram <br> 该接口用于获取小程序码，适用于需要的码数量极多的业务场景。通过该接口生成的小程序码，永久有效，数量暂无限制 <br>https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/qrcode-link/qr-code/getUnlimitedQRCode.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("appid",type="string",require=false,desc="小程序appid")  
    * @Apidoc\Query("secret",type="string",require=false,desc="小程序secret")  
    * @Apidoc\Query("page",type="string",require=true,desc="如 pages/index/index")  
    * @Apidoc\Query("scene",type="string",require=true,desc="scene")  
    * @Apidoc\Query("env_version",type="string",require=true,desc="正式版为release 体验版为trial开发版为develop  默认是正式版")  
    */
    public function getUnlimitedQRCode(){
        $input = $this->input;
        $page = $input['page'];
        $appid = $input['appid'];
        $secret = $input['secret'];
        $scene = $input['scene'];
        $env_version = $input['env_version']?:'';
        return json($this->api->getUnlimitedQRCode([
            'appid'=>$appid,
            'secret'=>$secret,
            'page'=>$page,
            'scene'=>$scene,
            'env_version'=>$env_version,
        ]));
    }

    /**
    * @Apidoc\Title("文本内容安全识别") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/WeMinProgram<br>https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/sec-center/sec-check/msgSecCheck.html")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("appid",type="string",require=false,desc="小程序appid")  
    * @Apidoc\Query("secret",type="string",require=false,desc="小程序secret")  
    * @Apidoc\Query("scene",type="string",require=true,desc="需检测的文本内容，文本字数的上限为2500字，需使用UTF-8编码")  
    * @Apidoc\Query("content",type="string",require=true,desc="场景枚举值1 资料；2 评论；3 论坛；4 社交日志")   

    * @Apidoc\Query("openid",type="string",require=true,desc="用户的openid用户需在近两小时访问过小程序")   
    * @Apidoc\Returned("suggest",type="string",require=true,desc="建议 有risky、pass、review三种值")   
    * @Apidoc\Returned("label",type="string",require=true,desc="命中标签")   
    */
    public function msgSecCheck(){
        $input = $this->input;
        $openid = $input['openid'];
        $content = $input['content'];  
        $scene = $input['scene'];  

        $appid = $input['appid'];
        $secret = $input['secret'];
        return json($this->api->msgSecCheck([
            'appid'=>$appid,
            'secret'=>$secret,
            'openid'=>$openid,
            'scene'=>$scene,
            'content'=>$content,
        ]));
    }

    
    /**
    * @Apidoc\Title("条码识别") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/img-ocr/img/scanQRCode.html")
    * @Apidoc\Method("POST")     
    * @Apidoc\Query("url",type="string",require=true,desc="")   
    */
    public function qrcode(){
        $input = $this->input;
        $appid = $input['appid'];
        $secret = $input['secret'];
        $url = $input['url'];
        $d = $this->api->qrcode($url);
        return json($d);
    }

    /**
    * @Apidoc\Title("身份证识别 （收费）") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>在微信服务市场购买一个之后，对应的小程序OCR都可使用<br>购买地址：https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")  
    * @Apidoc\Returned("type",type="string",desc="front为正面 back为背面")    
    * @Apidoc\Returned("name",type="string",desc="姓名")    
    * @Apidoc\Returned("id",type="string",desc="身份证号")    
    * @Apidoc\Returned("birth",type="string",desc="出生日期")    
    * @Apidoc\Returned("gender",type="string",desc="性别")    
    * @Apidoc\Returned("nationality",type="string",desc="民族")    
    */
    public function idcard(){
        $input = $this->input; 
        $url = $input['url'];
        $d = $this->api->idcard($url);
        return json($d);
    }

    /**
    * @Apidoc\Title("驾驶证、行驶证识别 （收费）") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>购买地址：https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")  
    */
    public function driving(){
        $input = $this->input; 
        $url = $input['url'];
        $d = $this->api->driving($url);
        return json($d);
    }

    /**
    * @Apidoc\Title("营业执照识别 （收费）") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>购买地址：https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")  
    */
    public function bizlicense(){
        $input = $this->input; 
        $secret = $input['secret'];
        $url = $input['url'];
        $d = $this->api->bizlicense($url);
        return json($d);
    }
    /**
    * @Apidoc\Title("银行卡识别 （收费）") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>购买地址：https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415")
    * @Apidoc\Method("POST")   
    * @Apidoc\Query("url",type="string",require=true,desc="")  
    */
    public function bankcard(){
        $input = $this->input; 
        $url = $input['url'];
        $d = $this->api->bankcard($url);
        return json($d);
    }
    /**
    * @Apidoc\Title("通用印刷体识别 （收费）") 
    * @Apidoc\Desc("RPC请求：/rpc/webtool/OcrTx <br>购买地址：https://fuwu.weixin.qq.com/service/detail/000ce4cec24ca026d37900ed551415")
    * @Apidoc\Method("POST")    
    * @Apidoc\Query("url",type="string",require=true,desc="")  
    */
    public function comm(){
        $input = $this->input; 
        $url = $input['url'];
        $d = $this->api->comm($url);
        return json($d);
    }

    /**
    * @Apidoc\Title("刷新token") 
    * @Apidoc\Method("POST")  
    */
    public function refresh_token(){
        $this->api->refresh_token();
        return json_success();
    }

}