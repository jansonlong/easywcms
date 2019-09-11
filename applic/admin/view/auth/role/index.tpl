<div class="easy-list-main">
	<div class="easy-toolbar-box" >
		{include file="/submenu"/}
	</div>
	<form class="layui-form">
		<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table>
	</form>
</div>

<div id="addRoleFormHtml" style="display: none">
	<form id="addRoleForm" name="addRoleForm" class="layui-form layui-form-pane" action="" style="padding:20px;">
	  <div class="layui-form-item">
		<label class="layui-form-label">分组名称</label>
		<div class="layui-input-block" id="roletitle"></div>
	  </div>
	  <div class="layui-form-item">
		<label class="layui-form-label">描述信息</label>
		<div class="layui-input-block" id="roledescription"></div>
	  </div>
	  <div>
		<button class="layui-btn" style="float:right;" lay-submit lay-filter="addRoleForm" id="submit">保存分组</button>   
	  </div>
	</form>
</div>