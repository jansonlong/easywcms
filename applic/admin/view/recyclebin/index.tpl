<div class="easy-list-main">
	<form class="layui-form">
		<div class="easy-toolbar-box" >
			<a title="刷新列表" class="layui-btn easy-btn-tablereload"><i class="layui-icon">&#xe669;</i></a>
			<a title="恢复" data-type="recover" class="multi layui-btn layui-btn-sm"><i class="layui-icon">&#xe663;</i>恢复数据</a>
			<a title="删除" data-type="harddel" class="multi layui-btn layui-btn-danger layui-btn-sm"><i class="layui-icon">&#xe640;</i>彻底删除</a>
			<div id="Screenbutton" style="float: left; margin:0 10px; border: 1px solid #d0d2d0; border-radius: 2px;">
				<span style="float: left; height: 28px; line-height: 28px; width:70px; text-align: center; background-color:#eeeeee; ">数据表名</span>
				<div style="float: left;border-left: 1px solid #d0d2d0;">
					<label>
						<select id="module_name" style="height: 28px; border: none; padding: 0 4px;" lay-ignore>
							{volist name="tableinfo" id="r"}
							<option value="{$r.Name}">{$r.Name} - {$r.Comment}</option>
							{/volist}
						</select>
					</label>
				</div>
			</div>
			<a title="更新缓存" class="layui-btn easy-btn-ajax" data-url="{:url('/admin/console/upcache')}?modelname=DB_TABLES">更新数据表缓存</a>
		</div>
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</form>
</div>

<script type="text/html" id="editTpl">
<a lay-event="recover" class="layui-btn layui-btn-xs" title="恢复此数据">恢复</a>
<a lay-event="harddel" class="layui-btn layui-btn-xs layui-btn-danger" title="删除此数据">删除</a>
</script>
<script>
var tableinfo = {:json_encode($tableinfo)};
var fieldTest = {:json_encode($fieldTest)};
easy.define(function(){
	//获取第一张表的数据
	tableRender($('#module_name').val());

	//根据选中的模型进行查询
	$('#module_name').change(function(){
		//获取选中的模型
		var module_name = $('#module_name').val();
		tableRender(module_name);
	});

	//监听按钮
	layui.table.on('tool(layui-table)', function(obj){
		var module_name = $('#module_name').val();
		multi(obj.event,1,{id:obj.data.id,moueld:module_name});
	});
	// 监听按钮 END

	//批量操作
	$('.multi').click(function(){
		var checkStatus = layui.table.checkStatus('layui-table');
		var length = checkStatus.data.length;
		if(length == 0){
			return artError('至少选择一条数据');
		}else{
			var type 	= $(this).attr('data-type'),
				moueld 	= $('#module_name').val(),
				idall 	= new Array(),
				datas 	= checkStatus.data;
			for(var item in datas) {
				idall[item] = datas[item]['id'];
			}
			idallstr = idall.join();
			//请求执行
			multi(type,length,{id:idallstr,moueld:moueld});
		}
	});

	function tableRender(module_name){
		var table_info = tableinfo[module_name];
		var cols = [{width:49,checkbox: true,fixed:'left'}];
		//主键
		var Pk = table_info['Pk']
		cols.push({field:Pk,title:'编号',width:62,align:'center',unresize:true,fixed:'left'})
		//循环5个其他字段
		var Fields = table_info['Fields'];
		for(var i=0; i<=8;i++){
			if( Fields[i] != Pk && Fields[i] != 'delete_time' ){
				cols.push({field:Fields[i], title: fieldTest[Fields[i]] ? fieldTest[Fields[i]] : Fields[i] , unresize:true});
			}
		}
		//固定字段
		cols.push({field:'otherfield', title: '...', width:40,unresize:true})
		cols.push({field:'delete_time',	title: '删除时间', width:150, templet:function(d){return formatDate(d.delete_time,1);},unresize:true})
		cols.push({field:'editbutton', 	title: '操作', width:120, toolbar:'#editTpl',unresize:true})
		//方法级渲染
		layui.table.render({
			elem: '#layui-table'
			,url: "{:url($controller.'/index')}?moueld=" + module_name
			,page: {
			  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'],
			  limits:[{:config('param.page_limit')}, 30, 50, 100]
			}
			,limit:{:config('param.page_limit')}
			,even: true
			,cols: [cols]
		});
	}

	//请求执行
	function multi(type,length,data){
		if(!type){
			return artError('参数不正确');
		//执行恢复
		}else if(type=='recover'){
			var param = { url: "{:url($controller.'/recover')}", msg: '确认恢复选中的 [ '+length+' ] 条数据吗?', data:data };
		//执行删除
		}else if(type=='harddel'){
			var param = { url: "{:url($controller.'/harddel')}", msg: '确认彻底删除选中的 [ '+length+' ] 条数据吗?<br>删除后不可恢复!', data:data };
		}
		easy.artCaveat(param.msg,function(){
			easy.ajax({ url:param.url, param:param.data },function(data){
				if(data.code == 1){
					layui.table.reload('layui-table',{page:{curr:1}});
					easy.success(data.msg||'');
				}else{
					easy.error(data.msg||'');
				}
			});
		});
	}
});
</script>