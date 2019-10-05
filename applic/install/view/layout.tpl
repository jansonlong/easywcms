<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>EasyWcms 安装程序</title>
<link rel="shortcut icon" type="image/x-icon" href="{:config('system.icon')}"/>
<link rel="stylesheet" type="text/css" href="{:config('param.assets')}layui/css/layui.css">
</head>
<style>
    body{ position: relative;background-color: #32cbff;font-family: Microsoft YaHei;}
    a{ text-decoration:none; color: #333; cursor: pointer;}
    .logo{ background:url('/assets/login.png') no-repeat center; height: 100px; margin: 10px 0 ; }
    .step_box{ background:#fff;width:1000px; margin: 0 auto; border-radius: 10px;}
    .number{ height:40px; display: flex; line-height: 50px; text-align: center;}
    .number span{ height:40px; flex: 1; color: #8a8a8a; }
    .text{ height:40px; display: flex; line-height: 30px;  text-align: center;}
    .text span{ height:40px; flex: 1; font-size: 16px; color: #8a8a8a;}
    .speed{ height:15px; padding: 10px 0;display: flex; text-align: center;}
    .speed span{ width:15px; flex: 1; text-align: center; position: relative;}
    .speed span i{ width:15px; height: 15px; display: block; background: #b9b9b9; margin: 0 auto; border-radius: 15px;}
    .speed span b{ width: 100%;  height: 4px; display: block; background: #b9b9b9; position: absolute; top: 5px; left: 0;}
    span.focus{ color: #32cbff; font-weight: bold;}
    span.focus i,span.focus b{ background: #32cbff}
    .content{ width:950px; padding: 25px; background: #fff; border-radius: 10px; min-height: 400px; margin: 20px auto;}
    .footer{ padding: 10px; margin: 0 auto; overflow: hidden;text-align: center;}
    .footer a{ min-width:120px; height: 40px; padding: 0 10px; line-height: 40px; margin: 0 10px; background: #fff; border-radius: 5px; display: none;}
    .footer a.block{  display: inline-block;}
    .footer label{display:none;color: #fff;}
    .footer label.block{  display: block;}
    .layui-form-label{ width:150px!important;}
    .layui-input-block{ margin-left:150px!important;}
    .layui-elem-quote{ border-left: 5px solid #32cbff!important;}
    .layui-icon-ok{ color:rgba(0,140,29,1.00)!important; font-weight: bold;}
    .layui-icon-close{ color: red!important; font-weight: bold;}
    #atongyi{ background:#cacaca}
    #atongyi[href]{ background:#fff}
</style>
<script>
var easy = {:json_encode($easy)};
easy.define=function(callback){callback?easy.render=function(){callback()}:false};
</script>
<body>
    <div class="logo"></div>
    <div class="step_box">
        <div class="number">
            <span class="{if ($step>0)}focus{/if}">1</span>
            <span class="{if ($step>1)}focus{/if}">2</span>
            <span class="{if ($step>2)}focus{/if}">3</span>
            <span class="{if ($step>3)}focus{/if}">4</span>
            <span class="{if ($step>4)}focus{/if}">5</span>
            <span class="{if ($step>5)}focus{/if}">6</span>
        </div>
        <div class="speed">
            <span class="{if ($step>0)}focus{/if}"><b></b><i></i></span>
            <span class="{if ($step>1)}focus{/if}"><b></b><i></i></span>
            <span class="{if ($step>2)}focus{/if}"><b></b><i></i></span>
            <span class="{if ($step>3)}focus{/if}"><b></b><i></i></span>
            <span class="{if ($step>4)}focus{/if}"><b></b><i></i></span>
            <span class="{if ($step>5)}focus{/if}"><b></b><i></i></span>
        </div>
        <div class="text">
            <span class="{if ($step>0)}focus{/if}">安装协议</span>
            <span class="{if ($step>1)}focus{/if}">环境检测</span>
            <span class="{if ($step>2)}focus{/if}">文件权限</span>
            <span class="{if ($step>3)}focus{/if}">账号设置</span>
            <span class="{if ($step>4)}focus{/if}">正在安装</span>
            <span class="{if ($step>5)}focus{/if}">安装完成</span>
        </div>
    </div>
    <div class="content">
    {__CONTENT__}
    </div>
    <div class="footer">
        <label><input type="checkbox" id="tongyi" >　我已详细阅读安装协议并同意安装协议<br><br></label>
        <a id="atongyi" class="{if ($step==1)}block{/if}">开始安装</a>
        <a id="aupper" href="{:url('/Install?step='.($step-1))}" class="{if ($upper)}block{/if}">上一步</a>
        {if ($next)}
        <a id="anext" href="{:url('/Install?step='.($step+1))}" class="block">下一步</a>
        {else}{if ($step>2&&$step!=6)}
        <a id="anext" href="javascript:window.location.reload();" class="block">刷新</a>
        {/if}{/if}
    </div>
</body>
<script type="text/javascript" src="{:config('param.assets')}layui/layui.js?t=1565594821"></script>
</html>
