<style>
    .easy-toolbar-box{display:none;}
</style>
<div class="layui-tab" lay-filter="addonstype" style="margin: 8px 15px 0 15px">
	<ul class="layui-tab-title">
		<li data-type='1' class="layui-this" style="margin-left: 10px;">已安装</li>
		{volist name="$typeall" id="r"}
		<li data-type='{$key}'>{$r}</li>
		{/volist}
	</ul>
	<form class="layui-form" style="background-color: #fff; padding: 10px;">
		<div class="easy-toolbar-box" >
			<a title="刷新列表" class="layui-btn easy-btn-tablereload"><i class="layui-icon">&#xe669;</i></a>
		</div>
		<div id="table-list-body">
			<table id="layui-table" lay-filter="layui-table" class="layui-table" style="display: none"></table> 
		</div>
	</form>
</div>
<script type="text/html" id="editTpl">
{{#  if(d.id){ }}
<a class="layui-btn layui-btn-xs" lay-event="edit" title="配置插件">配置</a>
{{#  if(d.deleting){ }}
<a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="uninstall" title="卸载插件">卸载</a>
{{#  } }} 
{{#  }else{ }}
<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="install" title="安装插件">安装</a>
{{#  } }} 
</script>
<script type="text/html" id="editTpl3">
<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="online_install" title="安装插件">下载并安装</a>
</script>
<script>
var store_url = '{$store_url|raw}';
</script>
<div id="login-store-html" style="display: none">
<form name="login-store-form" class="layui-form layui-form-pane" action="" style="padding:20px;">
    <blockquote class="layui-elem-quote" style="margin: 0 0 20px 0;">由于当前插件为收费版，需要您登录EasyWcms官方的Store账号。</blockquote>
    <div class="layui-form-item">
        <label class="layui-form-label" >账号</label>
        <div class="layui-input-block" style="max-width: 650px;">
            <input type="text" class="layui-input"  lay-verType="tips" lay-verify="required" id="name" name="username" placeholder="请输入 Store账号">
        </div>
    </div>	
    <div class="layui-form-item">
        <label class="layui-form-label" >密码</label>
        <div class="layui-input-block" style="max-width: 650px;">
            <input type="text" class="layui-input" lay-verType="tips" lay-verify="required" id="password" name="userpwd" placeholder="请输入 Store密码" onfocus="this.setAttribute('type','password');">
        </div>
    </div>
    <div style=" padding-top: 5px;">还没有Store账号? 点击<a href="//store.zcphp.com/" target="_blank" style="color: #1278f6">去注册</a></div>
    <div><button class="layui-btn" style="float:right;" lay-submit lay-filter="login-store-form" id="submit">登录</button></div>
</form>
</div>
