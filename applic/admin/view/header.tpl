<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>{:config('system.title')}</title>
<meta name="renderer" content="webkit"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<link rel="shortcut icon" type="image/x-icon" href="{:config('system.icon')}"/>
<link rel="stylesheet" type="text/css" href="{:config('param.assets')}layui/css/layui.css">
<link rel="stylesheet" type="text/css" href="{:config('param.assets')}admin/css/style.css?t={$easy['config']['version']}">
<link rel="stylesheet" type="text/css" id="skinlink">
<script>
var easy = {:json_encode($easy)};
easy.define=function(callback){callback?easy.render=function(){callback()}:false};
</script>
</head>