<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes\TicketPrinter; 
/**
* 由于小票机打印在多家接口上并不统一且排版困难。
* 一般小票机接口都有对应的字符表示具体的意义，如<BR>表示换行。
* 此类主要作用是统一转换成对应的小票机接口所需要的字符，统一发起调用
* 
$s = new \app\webtool\classes\TicketPrinter\Base;    
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
$str = $s->parse($data);
echo $str;

每一个打印接口需要实现
protected function do_print_label($set=[] , $sn,$content,$times){
}
protected function do_print_58mm($set,$sn,$content,$times){
}
*
* http://help.feieyun.com/document.php
* https://v4.cecdn.yun300.cn/100001_2103235147/%E8%8A%AF%E7%83%A8%E4%BA%91%E5%BC%80%E6%94%BEAPI%E5%BC%80%E5%8F%91%E6%8E%A5%E5%85%A5%E6%96%87%E6%A1%A3v1.9.1.pdf
*/
class Base {
	//设备的2个key
	public $sn;
	public $key;
	//帐号的2个key
	public $user = '';
	public $ukey = '';
	/**
	* 把格式转成打印机支持的标签 
	*/
	public $parse_trans = [
		/////////////////////////////////////////////////////////
		// 58mm 小票机
		/////////////////////////////////////////////////////////
		// 飞鹅 http://help.feieyun.com/document.php
		//数字字母混合条形码,最多支持14位数字大写字母混合
		'code_abc'=>"<BC128_A>#</BC128_A>",
		//最多支持22位纯数字
		'code_int'=>"<BC128_C>#</BC128_C>",
		//字体加粗
		'bo'=>"<BOLD>#</BOLD>", 
		//右对齐
		'r'=>"<RIGHT>#</RIGHT>",
		//二维码（单个订单，最多只能打印一个二维码）
		'qr'=>"<QR>#</QR>",
		//字体变宽一倍
		'w'=>"<W>#</W>",
		//字体变高一倍
		'l'=>"<L>#</L>",
		//居中
		'c'=>"<C>#</C>", 
		//放大一倍
		'b'=>"<B>#</B>", 
		//居中放大
		'cb'=>"<CB>#</CB>",
		//切刀指令(主动切纸,仅限切刀打印机使用才有效果) 
		'cut'=>"<CUT>",
		//换行符
		'br'=>"#<BR>",
		'line'=>"#",
		/////////////////////////////////////////////////////////
		// 标签机 
		/////////////////////////////////////////////////////////
		// 飞鹅
		// 30,20
		't_size'=>"<SIZE>#</SIZE>",
		//1：正向出纸，0：反向出纸， 
		't_d'=>"<DIRECTION>#</DIRECTION>",
		//打印图片 
		't_img'=>'<IMG x="10" y="100">',
		//打印二维码 
		't_qr'=>'<QR x="10"  y="100"  e="L"  w="5">#</QR>',
		// 打印文本，其中属性x为水平方向起始点坐标（默认为0），属性y为垂直方向起始点坐标（默认为0）
		't_text'=>'<TEXT x="10" y="100" font="12" w="2" h="2" r="0">#</TEXT>', 
		//打印code128一维码 
		't_code128'=>'<BC128 x="10" y="100" h="80" s="1" r="0" n="1" w="1">#</BC128>',
		//打印LOGO指令(前提是预先在机器内置LOGO图片)，其中属性x为水平方向起始点坐标（默认为0） 
		't_logo'=>'<LOGO x="10" y="100"></LOGO>',
	];
	//画横线
	public $line_num = 32;
	public $line = '-';
	//58mm的机器,一行打印16个汉字,32个字母;80mm的机器,一行打印24个汉字,48个字母
	public $space_num_row = [
		'58mm'=>32,
		'80mm'=>48,
	];
	//当前小票机默认58mm
	public $cur_mm = "58mm";
	/**
	* 一行最多字符
	*/
	public function get_row_max_abc(){
		return $this->space_num_row[$this->cur_mm];
	}

	/**
	* 把字段转成打印标签
	*/
	public function parse($all){
		$str = '';
		foreach($all as $v){
			if($v['top']){
				$str .= $this->_parse_list($v);
			}else{ 
				$str .= $this->_parse($v);
			}
		}
		return $str;
	}
	/**
	* 解析列表
	* top list
	*/
	protected function _parse_list($all){
		$top  = $all['top'];
		$list = $all['list'];
		//p1是第一行标题行，主要计算出第列最大字符数
		$p1 = [];
		$total = 0;
		$max = $this->get_row_max_abc();
		$top_tag = "br";
		if($top['tag']){
			$top_tag = $top['tag'];
			unset($top['tag']);
		} 
		foreach($top as $k=>$v){
			$arr = explode("|",$v);
			$val = $arr[0];
			$space_num = $arr[1];
			$len = mb_strwidth($val,'utf-8');
			if($space_num == '*'){
				$add = 0;
			}else{
				$add = $space_num;
			}
			$total = $total+$len+$add;
			$p1[$k] = [
				'k'=>$k,
				'v'=>$val,
				'append_len'=>$space_num,
				'val_len'=>$len
			];  
		} 
		/**
		* row_1生成的效果 
		* 名称                 单价  数量 
		*/
		$row_1 = '';
		$list_k_l = [];
		//第一行 key=>总长度
		$top_key_len = [];
		foreach($p1 as $k=>&$v){
			$append_len = $v['append_len'];
			if($append_len == '*'){
				$append_len = $max-$total;
			}
			$space = '';
			for($i=0;$i<$append_len;$i++){
				$space .= " ";
			}
			$row_1 .= $v['v'].$space; 
			$list_k_l[$k] = $append_len;
			$top_key_len[$k] = $append_len+$v['val_len'];
		}  
		$printer_line = [
			[
				'title'=> $row_1,
				'tag'  => $top_tag
			]
		];
		
		//计算列表
		$com = [];
		foreach($list as $v){
			$row = []; 
			foreach($v as $kk=>$vv){ 
				$max = $list_k_l[$kk];
				if($max){
					$row[] = [
						'k'=>$kk,
						'v'=>$vv,
						'len'=>mb_strwidth($vv,'utf-8'),
						'max'=>$max,
					]; 
				} 
			}
			$com[] = $row;
		} 
		foreach($com as $k=>$v){ 
			$vv  = $v[0]; 
			$val = $vv['v']; 
			$len = $vv['len'];
			$max = $vv['max'];
			$i = $j = 0;
			if($len > $max){ 
				$co = ceil(bcdiv($len,$max,2)); 
				$y = 0;
				for($n=0;$n<$co;$n++){
					$new_k = '';
					$max_row = ($n+1)*$max; 
					$j = $n*$max;
					if($max_row > $max){
						$max_row = $max;
					}
					$new_k .= gbk_substr($val,$j,$max); 
					$space = $max;
					if($new_k){
						$v[0]['v'] = $new_k; 
						if($n == 0){
							$new_p[] = $v;
						}else{
							$new_p[] = [
								0=>[ 'v'=> $new_k ]
							];
						}
						
					}
					$y++;
				} 
			}else{ 
				$new_p[] = $v;
			}
		}  
		$all = [];
		foreach($new_p as $v){
			$list = [];
			$title = '';
			foreach($v as $vv){
				$k1   = $vv['k'];
				$v1   = $vv['v'];
				$max  = $top_key_len[$k1];
				$len  = mb_strwidth($v1,'utf-8');
				$less = $max - $len;
				$sp   = '';
				if($less > 0){ 
					for($i = 0;$i < $less; $i++){
						$sp .= ' ';
					}
				}
				$title .= $v1.$sp;
			}
			$printer_line[] = [
				'title'=>$title,
				'tag'=>"br",
			]; 
		}     
		$str = '';
		foreach($printer_line as $v){
			$str .= $this->_parse($v); 
		}
		return $str;
	}  
	/**
	* 画线
	*/
	public function make_line(){
		$str = '';
		for($i=0;$i<$this->line_num;$i++){
			$str.=$this->line;
		}
		return $str;
	}
	/**
	* 把字段转成打印标签
	*/
	protected function _parse($v){ 
		$str = ""; 
		$title = $v['title'];
		$tag   = $v['tag']; 
		$attr  = $v['attr']; 
		if(strpos($tag,'|') === false){
			$tag = $tag.'|';
		}
		$arr = explode('|',$tag);  
		foreach($arr as $t){
			if(!$t){
				continue;
			}
			$find = $this->parse_trans[$t];
			if($find){ 
				if(strpos($t,'t_') !== false){
					$title = $this->_parse_tag($title,$find,$attr);
					if(strpos($title,'<') !== false){
						continue;
					}
				}else if($t == 'line'){
					$title = $this->make_line();
				} 
				$title = str_replace("#",$title,$find); 
			}
		}
		$str .= $title; 
		return $str;
	}
	//用于解析标签机
	protected function _parse_tag($title,$str,$attr = []){  
	   $append = '';
	   if(strpos($str,'#') !== false){
	   		$append = $title;
	   }
	   $t1  = trim(substr($str,1,strpos($str," ")));  
       $xml = simplexml_load_string($str);
       $arr = (array) $xml->attributes();
       if(!$arr){return $title;}
       foreach($arr as $v){
            $find = $v;
       }
       if($attr){
       		foreach($attr as $k=>$v){
		      if($find[$k]){
		            $find[$k] = $v;
		      }
	       }
       }
       $t = ucfirst($t1);
       $str = "<".$t." ";
       $in  = '';
       foreach($find as $k=>$v){
       		$in .= $k.'="'.$v.'" ';
       }
       $str = $str.$in.">".$append."</".$t.">"; 
       $single = ['</LOGO>'];
       foreach($single as $v){
	       	if(strpos($str,$v) !== false){
	       		$str = str_replace($v,"",$str);
	       	}
       }
       return $str;
	}
	//配置参数
	public function set($arr){
		foreach($arr as $k=>$v){
			$this->$k = $v;
		}
	}
}