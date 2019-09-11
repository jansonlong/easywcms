<input type="hidden" id="pid" value="{$_GET['id']}"/>
<div class="easy-list-main">
	<div class="easy-toolbar-box" >
		{include file="/submenu"/}
		<a class="layui-btn easy-btn-add" data-url="{:url('/admin/auth/rule/add')}?parent_id={$_GET[id]}&menutype=0" >
			<i class="iconfont icon-add_light"></i><font>添加资源操作规则</font>
		</a>
		<a class="layui-btn easy-btn-edit" data-url="{:url('/admin/auth/rule/edit')}" data-options='{"width":"800px","height":"580px"}'>
			<i class="iconfont icon-add_light"></i><font>编辑规则</font>
		</a>
	</div>
	<div class="layui-form">
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</div>
</div>