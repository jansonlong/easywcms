<form id="easy-form" class="layui-form layui-form-pane easy-form-main" action="" method="post">
	{:Form::text('分组名称','title',$vo['title'])}
	{:Form::text('描述信息','description',$vo['description'])}
	<div style="padding: 10px;  background-color: #f1f4f6; z-index: 9;">
		<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="checkAll">全选</button>
		<button type="button" class="layui-btn layui-btn-sm layui-btn-primary" id="uncheckAll">全不选</button>
	</div>
	<input type="hidden" name="rules[0]" value="1">
	<div id="easy-auth-tree" style=""><br>正在处理权限列表...<br>　</div>
	{if (isset($vo['id']))}<input type="hidden" name="id" value="{$vo['id']}" />{/if}
	<button lay-submit lay-filter="easy-form" id="submit" style="display:none">提交</button>   
</form>
<script>
var authlist = {:json_encode($authlist)};
var rulesdata = [{$vo.rules}];
</script>