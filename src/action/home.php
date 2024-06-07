<div class="mb10">	
	 <h1>接口概况</h1> 
	 <el-row :gutter="12">
	  <el-col :span="4">
	    <el-card shadow="always" style="background: #67C23A;color: #fff;font-weight: bold;font-size: 18px;">
	      <div style="">今日</div> 
	      次数：{{weltool.today}} 
	    </el-card>
	  </el-col>
	  <el-col :span="4">
	    <el-card shadow="hover"  style="background: #E6A23C;color: #fff;font-weight: bold;font-size: 18px;"> 
	      <div style="">昨日</div> 
	      次数：{{weltool.yesterday}} 
	    </el-card>
	  </el-col>
	  <el-col :span="4">
	    <el-card shadow="never"  style="background: #F56C6C;color: #fff;font-weight: bold;font-size: 18px;">
 	      <div style="">本周</div> 
	      次数：{{weltool.week}} 
	    </el-card>
	  </el-col>
	  <el-col :span="4">
	    <el-card shadow="never"  style="background: #7d6aff;color: #fff;font-weight: bold;font-size: 18px;">  
	      <div style="">上周</div> 
	      次数：{{weltool.lastweek}} 
	    </el-card>
	  </el-col>
	  <el-col :span="4">
	    <el-card shadow="never"  style="background: #ffa66a;color: #fff;font-weight: bold;font-size: 18px;"> 
	      <div style="">本月</div> 
	      次数：{{weltool.month}} 
	    </el-card>
	  </el-col>
	  <el-col :span="4">
	    <el-card shadow="never"  style="background: #69b5ff;color: #FFF;font-weight: bold;font-size: 18px;"> 
	      <div style="">上月</div> 
	      次数：{{weltool.lastmonth}} 
	    </el-card>
	  </el-col>
	</el-row> 
</el-row>

<?php 
global $vue; 

$vue->data("weltool","{}");
$vue->method("get_webtoll_list()","	
	$.post('/webtool/admin/get_count',{},function(res){
		app.weltool = res.data;
	},'json');
");
$vue->method("get_webtoll()","
	_this.get_webtoll_list();
	setInterval(()=>{
		_this.get_webtoll_list();
	},60000);
");
?>  