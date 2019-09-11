{__NOLAYOUT__}<!doctype html>
<html>
<head>
	<meta name="renderer" content="webkit">
    <meta  name="viewport"  content="width=device-width,  initial-scale=1.0,  minimum-scale=1.0,  maximum-scale=1.0,  user-scalable=no">
	<meta charset="UTF-8">
	<title>{:config('system.title')}</title>
	<script>
	//防止在框架内打开
	if (top.location != self.location) top.location = self.location;
	//配置信息
	var easy = {:json_encode($easy)};	
	</script>
	<link rel="shortcut icon" type="image/x-icon" href="{:config('system.icon')}"/>
	<link rel="stylesheet" href="{:config('param.assets')}admin/css/login.css">
</head>
<body>
<div id="Main">
	<div class="pageMain">
		<div class="pageCon">
            <div class="info">
                <img class="login-logo" src="{:config('system.login_logo')}"/>
                <p>{:config('system.login_text')}</p>
                <a href="https://www.kancloud.cn/easywcms/v_1_0/content" target="_blank">查看文档</a>
            </div>
			<div class="pageConBox">
				<div class="login">
					<div class="loginTit"><span>管理员登录</span></div>
					<div class="input-box">
						<span><i class="iconfont icon-quanxianguanli"></i></span>
                        <input id="username" name="username" type="text" autocomplete="off" msg="账户" placeholder="请输入用户名">
					</div>
                    <div class="input-box">
						<span><i class="iconfont icon-quanxiandaili13"></i></span>
                        <input id="password" name="password" type="password" autocomplete="off" msg="密码" placeholder="请输入密码">
					</div>
                    <div class="input-box">
						<span><i class="iconfont icon-tupian1"></i></span>
                        <input id="verifyCode" name="verifyCode" types="text" autocomplete="off" msg="验证码" placeholder="请输入验证码">
                        <img src="{:captcha_src()}" onClick="resetVerifyCode()" class="checkcode" align="absmiddle"  title="点击获取验证码" id="verifyImage"/>
					</div>
                    <div class="input-box loginSub" post-url="{:url('signin/submit')}" >登录</div>
                    <div class="msg"></div>
				</div>
			</div>
		</div>
		<div class="Copyright">
            {:config('system.copyright')}　
            <a href="http://www.miitbeian.gov.cn/">{:config('system.beian')}</a>
        </div>
	</div>
</div>
<script type="text/javascript" src="{:config('param.assets')}layui/layui.js?t={:time()}"></script>
</body>
</html>