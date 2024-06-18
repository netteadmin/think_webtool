<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
use app\webtool\classes\TicketPrinter\FeiE;
/** 
* 小票机打印
*/
class TicketPrinterFeiE extends FeiE{  
    /**
    * 打印
    * option['times'] = 1
    * option['list']  = [ ['title'=>'','tag'=>'br']]
    */
    public function print_58($set=[],$option = []){ 
        $res = $this->get_option($option);
        $content = $res[0];
        if(!$content){
            return;
        }
        $times = $res[1];
        return $this->do_print_58mm($set,$this->sn,$content,$times); 
    }
    /**
    * 标签打印
    */
    public function print_label($set=[],$option = []){ 
        $res = $this->get_option($option);
        $content = $res[0];
        if(!$content){
            return;
        }
        $times = $res[1];
        return $this->do_print_label($set,$this->sn,$content,$times); 
    }

    protected function get_option($option){
        /*
        $option = [
            'list'=>[
                [
                    "title"=>'标题',
                    'tag'=>'cb|br', 
                ]
            ],
        ];
        */
        if(!$option['list']){
            return false;
        }
        $content = $this->parse($option['list']);
        $times = $option['times']?:1;
        return [$content,$times];
    }
}
/*
$s = new \app\webtool\classes\TicketPrinter\TicketPrinterFeiE;    
$data = [
  [
    "title"=>'标题',
    'tag'=>'cb|br', 
  ],
  [
    "title"=>'123465',
    'tag'=>'code_int|br', 
  ],
  [
    'tag'=>'line|br'
  ],
  [
     'top'=>[
        'title'=>'名称|*',
        'price'=>'单价|2',
        'num'=>'数量|1', 
     ],
     'list'=>[
        [
            'title'=>'酸菜鱼',
            'price'=>'100.4',
            'num'=>'10',
        ],
        [
            'title'=>'可乐鸡翅+蒜蓉蒸扇贝',
            'price'=>'10.3',
            'num'=>'6',
        ],
        [
            'title'=>'紫苏焖鹅+梅菜肉饼+椒盐虾+北京烤鸭',
            'price'=>'10.0',
            'num'=>'8',
        ],
     ]
  ], 
];
$s->print_58([
    'list'=>$s,
    'times'=>1,
]);
*/