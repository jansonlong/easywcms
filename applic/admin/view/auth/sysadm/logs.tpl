<input type="hidden" id="uid" value="{$_GET['uid']}" >
<div class="easy-list-main" style="margin: 0;">
	<div class="easy-toolbar-box" style="display: none;">
		{include file="/submenu"/}
	</div>
	<form class="layui-form">
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</form>
</div>

<script type="text/html" id="logTpl">
<a class="layui-btn layui-btn-xs layui-btn-primary" lay-event="logs" title="查看日志">日志</a>
<a class="layui-btn layui-btn-xs layui-btn-normal"  lay-event="auth" title="设置权限">权限</a>
</script>