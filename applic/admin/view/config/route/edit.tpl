<form id="easy-form" class="layui-form layui-form-pane easy-form-main" >

	{:Form::text('名称','title',$vo.title,['verify'=>'required','placeholder'=>'请输入路由名称，例如：新闻'])}
    
    {:Form::text('备注','remarks',$vo.remarks)}
    
    {:Form::switcher('状态','status',$vo.status,['layval'=>[0,1]])}
    
	{:Form::select('请求类型','type',$vo.type,['list'=>['ALL'=>'任何请求类型','GET'=>'GET','POST'=>'POST','PUT'=>'PUT','DELETE'=>'DELETE','PATCH'=>'PATCH'],'verify'=>'required','attr'=>'lay-ignore'])}
		
	{:Form::text('路由分组','group',$vo.group,['verify'=>'required','placeholder'=>'请输入路由分组，例如：admin'])}
    
	{:Form::text('路由表达式','expression',$vo.expression,['verify'=>'required','placeholder'=>'请输入路由表达式，例如：new/:id'])}
	
	{:Form::text('路由地址','address',$vo.address,['verify'=>'required','placeholder'=>'请输入路由地址，例如：index/News/read'])}
	
	{:Form::text('路由标识','name',$vo.name,['placeholder'=>'请输入路由标识，例如：new_read'])}
    
    {:Form::fieldlist('路由附加参数','append',$vo.append)}
    
    {:Form::fieldlist('参数变量规则','pattern',$vo.pattern)}
	{if (isset($id))}<input type="hidden" name="id" value="{$id}">{/if}
	
	<button lay-submit lay-filter="easy-form" id="submit" style="display: none;">提交保存</button>
	
</form>
<div style="padding:10px;">多级控制器的路由地址示例：admin/test.user/index</div>
<script>
easy.define(function(){

});
</script>