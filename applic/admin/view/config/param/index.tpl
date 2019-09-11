<style>
	.easy-form-main { padding:10px 20px!important; margin:0 0 0 0; overflow:inherit!important;border-radius:4px;}
	.easy-form-main .layui-form-pane .layui-form-label{ width:140px;}
	.easy-form-main .layui-input-block{ width:440px; margin-left: 0; float: left;}
	.layui-form-item .layui-input-inline{width:430px!important; min-width: 200px; text-align: left;}
	.layui-form-item .layui-form-titles{ background: none; border:none;}
	.layui-form-item .layui-input-explain{color: #333!important;font-size: 13px; text-align: left; margin-left:20px; }
	.layui-form-item .layui-input-explain span{width:220px; float: left;}
	.layui-form-item .layui-input-explain font{float: left; text-indent: 10px;}
	.layui-form-text .layui-form-label,.layui-form-text .layui-input-inline,.layui-form-item .layui-textarea,.layui-form-text .layui-input-block {width:580px!important;}
	#add_GroupForm .layui-form-text .layui-form-label,#add_GroupForm .layui-form-item .layui-textarea{width:280px!important;}
	#add_GroupForm .layui-input-block {width:auto!important;}
	.layui-form-item .layui-form-checkbox[lay-skin=primary]{ margin: 0;}
	.layui-form-checkbox[lay-skin=primary] span{height: 20px;}
	@media only screen and (max-width: 1150px) {
		.layui-form-mid{ width:100%; padding-bottom: 0px !important; }
		.layui-form-item .layui-input-explain{ margin-left:0;}
	}
    .easy-input-imagebox input.layui-input{ width:265px;}
</style>

<div class="layui-tab" lay-filter="layuitab" style="margin: 8px 15px 15px 15px">
	<ul class="layui-tab-title" style="margin-left:10px;">
		<li lay-id='-1' style="min-width: 30px;" id="addGroupBut"><i class="iconfont">&#xe7e5;</i></li>
	</ul>
	<div class="layui-tab-content easy-form-main" id="ContentListForm">
		{volist name="$config" id="g"}
		<div class="layui-tab-item group_param" data-id="{$g.id}" data-title="{$g.title}">
			{if (isset($g['description'])&&!empty($g['description']))}<blockquote class="layui-elem-quote" style="margin: 10px 0 10px 0;">{$g['description']}</blockquote>{/if}
			<form class="layui-form layui-form-pane" action="{:url($controller.'/edit')}" style="margin-top: 0">
				<div class="layui-form-item" style="margin-bottom: 0;display: none;">
					<label class="layui-form-label layui-form-titles">变量标题</label>
					<div class="layui-form-label layui-form-titles layui-input-inline">变量值</div>
				</div>
				{volist name="$g['data']" id="gdata"}
				<?php
					$explain 			= "{literal}{:config('".$g['name'].".".$gdata['name']."')}{/literal}";
					$gdata['explain'] 	= '<span>'.$explain.'</span><font>'.$gdata['placeholder'].'</font>';
					$gdata['name'] 		= 'config['.$gdata['name'].']';
					$gdata['setup']['option'] = $gdata['option'];
					unset($gdata['option']);
				?>
				{:Form::input($gdata['type'],$gdata)}
				{/volist}
				{if (isset($g['details'])&&$g['details']!='')}
				<fieldset class="layui-elem-field" style="margin-bottom: 20px;">
					<legend>说明</legend>
					<div class="layui-field-box" style="line-height: 24px;">{:htmlspecialchars_decode($g['details'])}</div>
				</fieldset>
				{/if}
				<div class="layui-form-item">
					<div class="layui-input-block">
						<input type="hidden" name="group_id" value="{$g.id}" />
						<button class="layui-btn" lay-submit lay-filter="MainForm">提交保存</button>
					</div>
				</div>
			</form>
		</div>
		{/volist}
		<!--添加参数 Start-->
		<div class="layui-tab-item">
			<form class="layui-form layui-form-pane" action="{:url($controller.'/add')}" style="margin-top: 0;">
				<blockquote class="layui-elem-quote" style="margin: 10px 0 20px 0;">请根据业务需求添加需要的参数。如果需要删除参数，请到数据库中删除。</blockquote>
				{:Form::select('字段类型','config[type]',false,['verify'=>'required','list'=>Form::item(),'explain'=>'系统根据选择的字段类型生成表单'])}
				{:Form::select('归属分组','config[group_id]',false,['verify'=>'required','attr'=>'id=groupid','list'=>$config,'keys'=>['id','title'],'explain'=>'<button type="button" class="layui-btn layui-btn-xs" id="addGroup">添加分组</button>'])}
				{:Form::text('变量标题','config[title]',false,['verify'=>'required','placeholder'=>'请输入变量的中文标题'])}
				{:Form::text('变量名','config[name]',false,['verify'=>'required','placeholder'=>'只能输入字母'])}
				<div class="layui-form" style="height: 40px;">
					<input type="checkbox" name="config[snake]" title="变量名驼峰转下划线，例如输入userName 转换为 user_name" lay-skin="primary">
				</div>
				{:Form::text('变量值','config[value]',false,['placeholder'=>'可为空'])}
				{:Form::text('提示信息','config[placeholder]',false,['placeholder'=>'可为空'])}
				{:Form::text('校验规则','config[verify]',false,['placeholder'=>'多个验证请用 [ | ] 隔开'])}
				<fieldset class="layui-elem-field" style="margin-bottom: 20px;">
					<legend>说明</legend>
					<div class="layui-field-box" style="line-height: 24px;">
						<strong>校验规则：</strong>required（必填项）phone（手机号）email（邮箱）url（网址）number（数字）date（日期）identity（身份证）
						<br>
						<strong>必填项并且只能手机号：</strong>required|phone
						<br>
						<strong>必填项并且只能输入数字：</strong>required|number
					</div>
				</fieldset>
				<div class="layui-form-item">
					<div class="layui-input-block">
						<input type="hidden" name="actionname" value="add" />
						<button class="layui-btn" lay-submit lay-filter="MainForm">提交保存</button>
					</div>
				</div>
			</form>
		</div>
		<!--添加参数 End-->		
	</div>
</div>
<div id="addGroupFormHtml" style="display: none">
<form name="addGroupForm" class="layui-form layui-form-pane" action="" style="padding:20px;">
	{:Form::text('分组名称','name',false,['verify'=>'required','placeholder'=>'请输入例：site'])}
	{:Form::text('分组标题','title',false,['verify'=>'required','placeholder'=>'请输入例：站点配置'])}
	{:Form::text('分组描述','description',false,['verify'=>'required','placeholder'=>'请输入分组描述'])}
	<div>
	<button class="layui-btn" style="float:right;" lay-submit lay-filter="addGroupForm" id="submit">保存分组</button>
	</div>
</form>
</div>