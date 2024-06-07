<div id="tabs-webtool" class=" input_100" > 

	<el-row>
	  <el-col :span="12" style="padding-right: 10px;">
	  	<label>阿里云市场AppCode</label>
	    <input type="text" v-model="form.aliyun_market_AppCode" placeholder="" /> 

	    <h3>textin.com</h3>
	  	<div class="config_card">
	  		<label>App Id</label>
		    <input type="text" v-model="form.textin_app_id" placeholder="" />  
	 		<label>Secret</label>
	 		<input type="text" v-model="form.textin_secret_code" placeholder="" />   
	  	</div>
	  	<p></p>

	  	<h3>FACE++</h3>
	  	<div class="config_card">
	  		<label>Api Key</label>
		    <input type="text" v-model="form.faceplug_api_key" placeholder="" />  
	 		<label>API Secret</label>
	 		<input type="text" v-model="form.faceplug_api_secret" placeholder="" />  
	 		 
	  	</div>
	  	<p></p> 


	    <h3>制作证件照</h3>
	    <div class="config_card">
		    <label>制作并检测证件照KEY</label>
		    <input type="text" v-model="form.printer_ai_key" placeholder="" /> 
		    <p class="small text-right">接口申请地址: http://dev.id-photo-verify.com/userCenter.html,创建应用时应用类型选择 制作并检测证件照</p>
		    <label>证件换装</label>
		    <input type="text" v-model="form.printer_ai_change_dress" placeholder="" /> 
		    <p class="small text-right">接口申请地址: http://dev.id-photo-verify.com/userCenter.html,创建应用时应用类型选择 制作并裁剪换正装</p>
 		</div>
 		<h3>链科云打印</h3>
	  	<div class="config_card">
	  		<label>链科云打印</label>
		    <input type="text" v-model="form.printer_lianke_key" placeholder="" /> 
	 		<p class="small text-right">https://open.liankenet.com/#/user/list</p>
	 		<label>回调AppSecret</label>
	 		<input type="text" v-model="form.printer_lianke_secret" placeholder="" /> 
	 		<p class="small text-right">回调地址 <?= host()?>/webtool/printerLiankeCall/index</p> 
	  	</div>
	  	<p></p>
	  	<h3>百度内容审核</h3>
	  	<div class="config_card">
	  		<label>AppID</label>
		    <input type="text" v-model="form.baidu_yun_app_id" placeholder="" />  
	 		<label>API Key</label>
	 		<input type="text" v-model="form.baidu_yun_app_key" placeholder="" />  
	 		<label>Secret Key</label>
	 		<input type="text" v-model="form.baidu_yun_app_secret" placeholder="" /> 
	 		<p class="small text-right">
	 			<a href="https://console.bce.baidu.com/ai/#/ai/antiporn/app/list" target="_blank">内容审核</a> 
	 		</p> 
	  	</div>
	  	<p></p>
	    <p>
		    <button class="button-xsmall pure-button pure-button-primary" @click="save()">保 存</button>   
		</p>
	  </el-col> 
	  <el-col :span="12" style="padding-right: 10px;">
	  	<label>webtool服务地址</label>
	    <input type="text" v-model="form.RPC_WEBTOOL_URL" placeholder="" /> 

	    <h3>京东云 对象存储</h3>
	  	<div class="config_card">
	  		<label>KEY_ID</label>
		    <input type="text" v-model="form.JD_ACCESS_KEY_ID" placeholder="" />  
	 		<label>ACCESS_SECRET</label>
	 		<input type="text" v-model="form.JD_ACCESS_SECRET" placeholder="" />
	 		<label>REGION</label>
	 		<input type="text" v-model="form.JD_REGION" placeholder="" /> 
	 		<label>BUCKET</label>
	 		<input type="text" v-model="form.JD_BUCKET" placeholder="" /> 
	 		<label>ENDPOINT</label>
	 		<input type="text" v-model="form.JD_ENDPOINT" placeholder="" /> 
	 		<label>DOMAIN</label>
	 		<input type="text" v-model="form.JD_DOMAIN" placeholder="" />   
	 		 
	  	</div>
	  	<p></p>

	  	<h3>AMAZON_S3</h3>
	  	<div class="config_card">
	  		<label>KEY_ID</label>
		    <input type="text" v-model="form.AMAZON_S3_KEY" placeholder="" />  
	 		<label>ACCESS_SECRET</label>
	 		<input type="text" v-model="form.AMAZON_S3_SECRET" placeholder="" />
	 		<label>REGION</label>
	 		<input type="text" v-model="form.AMAZON_S3_REGION" placeholder="" /> 
	 		<label>BUCKET</label>
	 		<input type="text" v-model="form.AMAZON_S3_BUCKET" placeholder="" /> 
	 		<label>ENDPOINT</label>
	 		<input type="text" v-model="form.AMAZON_S3_ENDPOINT" placeholder="" /> 
	 		<label>DOMAIN</label>
	 		<input type="text" v-model="form.AMAZON_S3_DOMAIN" placeholder="" />   
	 		 
	  	</div>
	  	<p></p>

	  	<h3>AMAZON_S3</h3>
	  	<div class="config_card">
	  		<label>KEY_ID</label>
		    <input type="text" v-model="form.AMAZON_S3_KEY" placeholder="" />  
	 		<label>ACCESS_SECRET</label>
	 		<input type="text" v-model="form.AMAZON_S3_SECRET" placeholder="" />
	 		<label>REGION</label>
	 		<input type="text" v-model="form.AMAZON_S3_REGION" placeholder="" /> 
	 		<label>BUCKET</label>
	 		<input type="text" v-model="form.AMAZON_S3_BUCKET" placeholder="" /> 
	 		<label>ENDPOINT</label>
	 		<input type="text" v-model="form.AMAZON_S3_ENDPOINT" placeholder="" /> 
	 		<label>DOMAIN</label>
	 		<input type="text" v-model="form.AMAZON_S3_DOMAIN" placeholder="" />   
	 		 
	  	</div>
	  	<p></p>


	  	<!--<h3>Azure blob </h3>
	  	<div class="config_card">
	  		<label>ISSUER</label>
		    <input type="text" v-model="form.AZURE_ISSUER" placeholder="" />  
	 		<label>SECRET</label>
	 		<input type="text" v-model="form.AZURE_SECRET" placeholder="" />
	 		<label>CONTAINER</label>
	 		<input type="text" v-model="form.AZURE_CONTAINER" placeholder="" /> 
	 		<label>BUCKET</label>
	 		<input type="text" v-model="form.AZURE_BUCKET" placeholder="" /> 
	 		<label>ENDPOINT</label>
	 		<input type="text" v-model="form.AZURE_ENDPOINT" placeholder="" /> 
	 		<label>DOMAIN</label>
	 		<input type="text" v-model="form.AZURE_DOMAIN" placeholder="" />   
	  	</div>
	  	<p></p>-->



	   </el-col>
	</el-row> 
	
</div>

<!--随机数：<?=md5(time().mt_rand(0,9999999))?>-->