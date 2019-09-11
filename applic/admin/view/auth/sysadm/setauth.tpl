<form id="easy-form" class="layui-form layui-form-pane easy-form-main" action="" method="post">
	<div style="padding: 10px;  background-color: #f1f4f6; z-index: 9;">
		<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="checkAll">全选</button>
		<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="uncheckAll">全不选</button>
	</div>
	<input type="hidden" name="rules" value="1">
	<div id="easy-auth-tree" style=""><br>正在处理权限列表...<br>　</div>
	{if (isset($vo['id']))}<input type="hidden" name="id" value="{$vo['id']}" />{/if}
	<button lay-submit lay-filter="easy-form" id="submit" style="display:none">提交</button>   
</form>
<script>
//权限功能
var authlist = {:json_encode($authlist)};
//用户组权限
var rulesdata = [{$rulesdata}];
//用户独立权限
var user_rules = [{$vo.rules}];
</script>