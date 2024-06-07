<?php 
/*
    授权许可说明请阅读LICENSE.md
    获取授权方式: yiiphp@foxmail.com 
*/ 
namespace app\webtool\classes; 
/**
* 图片放到纸上
*/
class ImgToPaper{  
    /**
     * 默认300dpi
     */
	public $dpi = 300;
	/**
	 * 是否留白,默认留白
	 */
	public $is_bai = true;
	/**
	 * 留白时宽度缺少mm
	 */
	public $bai_w = 2;
	/**
	 * 留白时高度缺少mm
	 */
	public $bai_h = 3;
	public $spect_type;
	/**
	 * DPI
	 */
	protected function get_cf_pdi(){
		return $this->dpi;
	}
	/**
	 * 是否留白 
	 */
	protected function get_cf_is_bai(){
		return $this->is_bai;
	}

	/**
	* 1张图放纸上
	*/
	public function create_1($img1,$type='chusheng',$to_paper="a4"){
		$cache_id = "/uploads/tmp/image_to_paper/".md5($img1.$type.$to_paper).'.png';
		$cache_file = WWW_PATH.$cache_id;
		$cache_dir = get_dir($cache_file);
		if(!is_dir($cache_dir)){mkdir($cache_dir,0777,true);}
		if(file_exists($cache_file)){
			return host().$cache_id;
		}
		$img = applet_image_init();
		$px = $this->get_px($type,true);  
		$img1 = download_file($img1);
        $file_1 = WWW_PATH.$img1;  
		$img = $img->make($file_1)->fit((int)$px[0],(int)$px[1])->greyscale(); 
		$bg = $this->create_blank_paper($to_paper); 
		$bg = $bg->insert($img,'center');
		$bg->save($cache_file,85,'png');
		return host().$cache_id;
	}

	/**
	* 2张图放纸上
	* $p->create_2('/uploads/t3.jpg','/uploads/t2.jpg','id_card')
	*/
	public function create_2($img1,$img2,$type='id_card',$to_paper="a4"){
		$cache_id = "/uploads/tmp/image_to_paper/".md5($img1.$img2.$type.$to_paper).'.png';
		$cache_file = WWW_PATH.$cache_id;
		$cache_dir = get_dir($cache_file);
		if(!is_dir($cache_dir)){mkdir($cache_dir,0777,true);}
		if(file_exists($cache_file)){
			//return host().$cache_id;
		}

	    $img1 = download_file($img1);
        $file_1 = WWW_PATH.$img1;
        $img2 = download_file($img2); 
		$file_2 = WWW_PATH.$img2;  
		$space  = 30; 
		$px = $this->get_px($type,true);  
		$bg_px = $this->get_px($to_paper);  
		//背景图
		$bg = $this->create_blank_paper($to_paper); 
		//计算两张图放在x y坐标点
		$w = $px[0];
		$bg_w = $bg_px[0];
		$h = $px[1];
		$bg_h = $bg_px[1];
		$h2 = $h*2+$this->mm_to_px($space);
		$x0 = ($bg_w-$w)/2;
		$y0 = ($bg_h-$h2)/2; 
		$y1 = $y0+$h+$this->mm_to_px($space);
		$x1 = $x0; 
		$x0 = (int)$x0;
		$y0 = (int)$y0;
		$x1 = (int)$x1;
		$y1 = (int)$y1;
		//图片缩放 
		$file_1 = $this->get_fit_img($file_1,$w,$h);
		$file_2 = $this->get_fit_img($file_2,$w,$h);  
		//把图片放在背景图上
		$bg = $bg->insert($file_1,'top-left',$x0,$y0);
		$bg = $bg->insert($file_2,'top-left',$x1,$y1); 
		$bg->save($cache_file,85,'png');
		return host().$cache_id; 
	}
	/**
	 * 自动旋转90度
	 */
	public function get_fit_img($file,$w,$h,$h_big_w=true,$w_big_h=false){
		$img = applet_image_init();
		$file = $img->make($file);
		$file1_w = $file->getWidth();  
		$file1_h = $file->getHeight();  
		if($file1_h > $file1_w && $h_big_w){
			$file = $file->rotate(90);
		}
		if($file1_h < $file1_w && $w_big_h){
			$file = $file->rotate(90);
		}
		$file = $file->fit((int)$w,(int)$h)->gamma(1)->brightness(20);
		return $file;
	}

	/**
	* 多张图放纸上
	* 1 1x 2 2x
	* $p->create_3('一寸照片','/uploads/t2.jpg','1',8)
	* $p->create_3('二寸照片','/uploads/t2.jpg','2',4)
	*/
	public function create_3($text = '',$img1,$type='1x',$num = 8,$to_paper="c6"){
		$this->spect_type = $type;
		$cache_id = "/uploads/tmp/image_to_paper/".md5($img1.$type.$num.$text.$to_paper).'.png';
		$cache_file = WWW_PATH.$cache_id;
		$cache_dir = get_dir($cache_file);
		if(!is_dir($cache_dir)){mkdir($cache_dir,0777,true);}
		if(file_exists($cache_file)){
			return host().$cache_id;
		}
	    $img1 = download_file($img1);
        $file_1 = WWW_PATH.$img1; 
		$space_x  = 2; 
		$space_y  = 2; 
		$space_line  = 1; 
		$space_x = $this->mm_to_px($space_x);
		$space_y = $this->mm_to_px($space_y);
		$space_line = $this->mm_to_px($space_line);
		$px = $this->get_px($type,true);  
		$bg_px = $this->get_px($to_paper); 
		//每行数量，共2行
		$center = $num/2; 
		//背景图
		$bg = $this->create_blank_paper($to_paper); 
		//计算两张图放在x y坐标点
		$w = $px[0];
		$bg_w = $bg_px[0];
		$h = $px[1];
		$bg_h = $bg_px[1];
		if($num == 8){
		    
		}else if($num == 4){
		    $bg = $bg->rotate(90);
		    $w = $px[0];
    		$bg_w = $bg_px[1];
    		$h = $px[1];
    		$bg_h = $bg_px[0];
		}
		$h2 = $h*2+$space_y;
		$pos = []; 
		$j = 0;
		$top_line = [];
		$bot_line = [];
		$bot_last_line = [];
		for($i=0;$i<$num;$i++){
			$x = ($bg_w-$w*$center-$space_x*$center)/2; 
			$y = ($bg_h-$h2)/2;  
			if(!$first_x){
				$first_x = $x;
			}
			$line_y1 = '';
			$line_x1 = '';
			$line = [];
			if($i>=$center){  
				$x = $first_x+$j*$space_x+$j*$w;		
				$j++; 
				$y = $y+$h+$space_y;
				
				if(!$bot_last_line){
    			    $top = $y+$h+$space_line;
    			    $bot_last_line = [
    			        'x0'=>0,   
    				    'y0'=>(int)$top,
    				    'x1'=>(int)($bg_w),
    				    'y1'=>(int)$top,
    			    ];
    			}
			}else{
				if($i>0){
					$x = $first_x+$i*$space_x+$i*$w;	
					$line_x0 = $x-$space_line;
				}else{
				    $line_x0 = $x-$space_line;
				}	 
				$line_y0 = 0;
				$line_x1 = $line_x0;
				$line_y1 = $bg_h;
				$line = [
				    'x0'=>(int)$line_x0,   
				    'y0'=>(int)$line_y0,
				    'x1'=>(int)$line_x1,
				    'y1'=>(int)$line_y1,
				];
			}	
			if(!$top_line){
			    $top = $y-$space_line;
			    $top_line = [
			        'x0'=>0,   
				    'y0'=>(int)$top,
				    'x1'=>(int)($bg_w),
				    'y1'=>(int)$top,
			    ];
			}
			
			if(!$bot_line){
			    $top = $y+$h+$space_line;
			    $bot_line = [
			        'x0'=>0,   
				    'y0'=>(int)$top,
				    'x1'=>(int)($bg_w),
				    'y1'=>(int)$top,
			    ];
			} 
			
			$x = (int)$x; 
			$y = (int)$y;
			$line_last = [];
			if($i==$num-1){
			    $line_last_x0 =  $x+$space_line+$w;
			    $line_last_y0 = 0;
			    $line_last_x1 = $line_last_x0;
			    $line_last_y1 = $bg_h;
			    $line_last = [
			        'x0'=>(int)$line_last_x0,   
				    'y0'=>(int)$line_last_y0,
				    'x1'=>(int)$line_last_x1,
				    'y1'=>(int)$line_last_y1,    
			    ];
			}
			$pos[] = [
				'x'=>$x,
				'y'=>$y, 
				'line'=>$line,
				'line_last'=>$line_last,
				'top_line'=>$top_line,
				'bot_line'=>$bot_line,
				'bot_last_line'=>$bot_last_line,
			];
		}    
		//pr($pos);exit;
		//图片缩放
		$img = applet_image_init();
		$file_1 = $img->make($file_1)->fit((int)$w,(int)$h);  
		foreach($pos as $v){
			$x  = $v['x'];
			$y  = $v['y'];
			//把图片放在背景图上
			$bg = $bg->insert($file_1,'top-left',$x,$y);
            $line = $v['line'];
            $line_last = $v['line_last'];
            $top_line = $v['top_line'];
            $bot_line = $v['bot_line'];
            $bot_last_line = $v['bot_last_line'];
            
            if($line&&$line['x1']>0){
                $bg->line($line['x0'], $line['y0'], $line['x1'], $line['y1'], function ($draw) {
    			    $draw->color('#000');
    			});
            } 
            if($line_last&&$line_last['x1']>0){ 
                $bg->line($line_last['x0'], $line_last['y0'], $line_last['x1'], $line_last['y1'], function ($draw) {
    			    $draw->color('#000');
    			});
            } 
            if($top_line&&$top_line['y0']>0){ 
                $bg->line($top_line['x0'], $top_line['y0'], $top_line['x1'], $top_line['y1'], function ($draw) {
    			    $draw->color('#000');
    			});
            } 
            
            if($bot_line&&$bot_line['y0']>0){ 
                $bg->line($bot_line['x0'], $bot_line['y0'], $bot_line['x1'], $bot_line['y1'], function ($draw) {
    			    $draw->color('#000');
    			});
            } 
            
            if($bot_last_line&&$bot_last_line['y0']>0){ 
                $bg->line($bot_last_line['x0'], $bot_last_line['y0'], $bot_last_line['x1'], $bot_last_line['y1'], function ($draw) {
    			    $draw->color('#000');
    			});
            } 
            
		} 
		if($text){
		    $bg = $bg->text($text, 20, 100, function($font) { 
    		    $font->file(PATH.'/vendor/thefunpower/helper/font/NotoSansSC-Regular.ttf');
                $font->size(16);
                $font->color('#000000'); 
                
            });
		} 
		$bg->save($cache_file,85,'png');
		return host().$cache_id;
	}
 
	/**
	* 白色背景图
	*/
	protected function create_blank_paper($type = 'a4'){
		$img = applet_image_init();
		$px = $this->get_px($type);
		$img = $img->canvas($px[0],$px[1],'#FFFFFF');
		return $img;
	}
	/**
	* 根据类型取px
	*/
	public function get_px($type = 'a4',$is_image = false){
		$met = "get_".$type;
		if(!method_exists($this,$met)){
			return;
		}
		$arr = $this->$met(); 
		$w = $arr[0];
		$h = $arr[1];
		if($is_image && $this->get_cf_is_bai()){
			$w = $w+$this->bai_w;
			$h = $h+$this->bai_h;
			if(strpos($this->spect_type,2)!==false){
				$w = $w+0.5;
				$h = $h+0.5;
			}
		}
		$w = $this->mm_to_px($w);
		$h = $this->mm_to_px($h); 
		if($w<1){$w = '';}
		if($h<1){$h = '';}
		return [(int)$w,(int)$h];
	}
	/**
	* mm转px
	*/
	public function mm_to_px($size,$dpi = ''){
		$size = str_replace('mm','',$size);
		$dpi = $dpi?:$this->get_cf_pdi();
		$px = $size*$dpi/25.4;
		return $px;
	} 
	/**
	* 6寸照片
	*/
	public function get_c6(){
		return [
			152,102
		];
	}
	/**
	* A4大小
	*/
	public function get_a4(){
		return [
			210,297
		];
	}
	/**
	* 身份证大小
	*/ 
	public function get_id_card(){
		return [85.6 ,54];
	}
	/**
	* 户口本
	*/
	public function get_hukou(){
		return [143,105];
	}

	/**
	* 驾驶证
	*/
	public function get_jiashi(){
		return [95,66];
	}

	/**
	* 行驶证
	*/
	public function get_xinshi(){
		return [95,66];
	}

	/**
	* 出生证
	*/
	public function get_chusheng(){
		return [200,280];
	}

	/**
	* 小一寸
	*/
	public function get_1x(){
		return [22,32];
	}

	/**
	* 一寸
	*/
	public function get_1(){
		return [25,35];
	}

	/**
	* 小二寸
	*/
	public function get_2x(){
		return [35,45];
	}

	/**
	* 二寸
	*/
	public function get_2(){
		return [35,49];
	}
}