<form id="easy-form" class="layui-form layui-form-pane easy-form-main">
	<div class="layui-form-item">
		{:Form::text('姓名','realname',$vo['realname'],['verify'=>'required','placeholder'=>'请输入姓名'])}
		{:Form::select('角色组','auth_role_id',(int)$vo['auth_role_id'],['verify'=>'required','list'=>$AuthRole,'keys'=>['id','title']])}
	</div>
	<div class="layui-form-item">
		{:Form::text('账户','username',$vo['username'],['verify'=>'required','placeholder'=>'请输入登录账户'])}
		{:Form::switcher('状态','status',$vo['status'])}
	</div>
	<div class="layui-form-item">
		{:Form::text('重置密码','password',$vo['password'],['placeholder'=>'留空则不重置'])}
	</div>
	{:Form::textarea(['title'=>'描述','name'=>'description','value'=>(isset($vo['description'])?$vo['description']:''),'display'=>'block','placeholder'=>'请输入描述'])}

	{:Form::hidden(['name'=>'id','value'=>(isset($vo['id'])?$vo['id']:'')])}
	<button lay-submit lay-filter="easy-form" id="submit" style="display: none;"></button> 
</form>