<div class="easy-list-main">
	<div class="easy-toolbar-box" >
		{include file="/submenu"/}
	</div>
	<form class="layui-form">
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</form>
</div>

<script type="text/html" id="setTpl">
<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="use" title="使用皮肤">使用</a>
</script>
<script type="text/html" id="skindataTpl">
<span class="layui-badge" style="background-color:{{ d.skin_data.maincolor }}">主色</span>
<span class="layui-badge" style="background-color:{{ d.skin_data.assistcolor }}">辅色</span>
<span class="layui-badge" style="background-color:{{ d.skin_data.sidebar }}">左侧菜单栏</span>
</script>