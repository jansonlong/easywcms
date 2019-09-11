<style>
	.personal_box{ overflow: hidden; margin:10px 10px 10px 15px;}
	.personal_box .pleft{ min-width: 279px; width:25%; max-width: 470px; float: left; margin-right: 17px; }
	.personal_box .personal_head{ width: 100%; height: 150px; margin-bottom: 4px; }
	.personal_box .personal_head a{ width: 80px; height: 80px; display: block; margin: 0 auto; padding: 5px; border:4px solid #efefef; border-radius: 50%; overflow: hidden; position: relative; cursor: pointer;}
	.personal_box .personal_head a img{ width: 80px; height: 80px; border-radius: 50%; background-color: #efefef }
	.personal_box .personal_head a i{ width: 80px; height: 80px; border-radius: 50%; position: absolute; text-align: center; line-height: 80px; color:#b1b1b1; font-size: 60px; z-index: 1; background-color: #efefef}
	.personal_box .personal_head a font{ width: 80px; height: 80px; border-radius: 50%; background-color:rgba(0,0,0,0.50); position: absolute; text-align: center; line-height: 80px; color: #fff; font-style: normal; display: none;z-index: 2}
	.personal_box .personal_head a:hover font{ display:block; }
	.personal_box .personal_head p{ text-align: center; padding-top: 6px; }
	.personal_box .pright{ margin-left: 26.5%; }
	.personal_box .layui-form{ margin: 0;}
	.personal_box blockquote{ background: #fff;border-radius: 4px; }
</style>
<div class="personal_box">
	<div class="pleft">
		<blockquote class="layui-elem-quote layui-quote-nm">
			<fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px;">
			  <legend style=" font-size: 14px;">个人信息</legend>
			</fieldset>
			<div class="personal_head">
				<a class="layui-upload easy-upload-image" lay-data="{field:'headimgurl'}">
					<font>点击上传</font><img id="headimgurl_img" src="{$headimgurl?:config('param.assets').'admin/images/nophoto.gif'}" />
				</a>
				<p><strong>{$username}</strong><br>{$realname}</p>
			</div>
			<form id="easy-form" class="layui-form layui-form-pane">
				<div class="layui-form-item">
					<label class="layui-form-label">账号</label>
					<div class="layui-input-block">
						<input type="text" readonly="readonly" disabled class="layui-input" value="{$username}">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">姓名</label>
					<div class="layui-input-block">
						<input type="text" readonly="readonly" disabled class="layui-input" value="{$realname}">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">角色组</label>
					<div class="layui-input-block">
						<input type="text" readonly="readonly" disabled class="layui-input" value="{$auth_title}">
					</div>
				</div>
				<div style="opacity: 0; height: 0">
					<input type="password" name="_password" disabled readonly >
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">重置密码</label>
					<div class="layui-input-block">
						<input type="password" name="password" autocomplete="off" placeholder="若留空则不重置" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">确认密码</label>
					<div class="layui-input-block">
						<input type="password" name="confirmp" autocomplete="off" placeholder="请再次输入密码" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-input-block">
						<input type="hidden" name="headimgurl" id="headimgurl" value="{$headimgurl}" />
						<input type="hidden" name="id" value="{$sysadm_id}" />
						<button class="layui-btn" lay-submit lay-filter="easy-form" style="float: right;">提交保存</button>
					</div>
				</div>
			</form>
		</blockquote>
	</div>
	<blockquote class="layui-elem-quote layui-quote-nm pright" >
		<fieldset class="layui-elem-field layui-field-title" style="margin-top: 10px;">
		  <legend style=" font-size: 14px;">操作日志</legend>
		</fieldset>
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</blockquote>
</div>