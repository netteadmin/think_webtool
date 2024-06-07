<?php global $vue;admin_header();?>
 
<div> 
  <h1>接口请求日志</h1>
  <div class="pure-form ">
    <input v-model="where.wq" @keyup.enter="get_lists_search" @input="get_lists_search" placeholder="请输入名称回车搜索">
    <input type="date" v-model="where.time" @change="get_lists_search">
    <button class=" pure-button" @click="reset_page()">重置</button>
  </div>
  <table class="pure-table pure-table-bordered" style="width:100%;">
      <thead>
          <tr>
              <th style="width:50px;">序号</th>
              <th>名称</th>  
              <th style="width:100px;"> 参数</th>  
              <th style="width:100px;">返回</th>   
              <th style="width:180px;">请求时间</th>    
              <th style="width:180px;" >完成时间</th>    
          </tr>
      </thead>
      <tbody>
          <template v-for="v in lists"> 
          <tr>
              <td>{{v.index}}</td>
              <td>{{v.title}}</td> 
              <td>
                <a href="javascript:void(0);" @click="view_json(v.par)">查看参数</a>
              </td> 
              <td>
                <a href="javascript:void(0);" @click="view_json(v.api_return)">查看返回数据</a>
              </td> 
 
              <td>{{v.created_at}}</td>    
              <td>
                <span v-if="v.flag == 'error'" style="color:red;">{{v.updated_at}}</span>
                <span v-else>{{v.updated_at}}</span>
                
              </td>    
          </tr> 
          </template>
      </tbody>
  </table>

  <div class="bottom">
      <el-pagination  @size-change="size_change" @current-change="current_change"
        background  page-size="20" :current-page="where.page"
        layout="total,prev, pager, next"
        :total="total">
      </el-pagination>
  </div>
<div id="dialog" title="查看数据" style="display: none;" >
  <div style="width:100%;height: 80vh;">
    <pre id="json-renderer"  ></pre>
  </div>
</div>
</div>

<?php 
$vue->created(['get_list()']); 
$vue->data('lists', "[]");
$vue->data('total', "");
$vue->method("get_list()"," 
$.post('/webtool/admin/get_pager',this.where,function(res){
    app.lists = res.data;   
    app.total = res.total;
},'json');
");
$vue->method("size_change(e)", "
    console.log('size_change');
    console.log(e);
");
$vue->method("current_change(e)", "
    console.log('current_change');
    this.where.page = e;
    this.get_list();
    console.log(e);
");
$vue->method("get_lists_search()", "
    this.where.page = 1;
    this.get_list();
");
$vue->method("reset_page()", "
    this.where = {page:1};
    this.get_list();
");
$vue->method("view_json(data)", "
    if(data){
      $('#json-renderer').jsonViewer(data, {collapsed: false, withQuotes: false, withLinks: true});  
      $('#dialog').dialog({minWidth:800});
    }    
");

 



add_action("vue",function(&$vue_code){
    $vue_code.='$(function(){ 
         

    })';
});
?>

 
<?php admin_footer();?>