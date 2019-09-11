<style>
	html,body{ overflow: hidden; }
	#headBox{ height: 50px; }
	.skinBox{ width:100%; height: 279px; background-color: #f1f4f6; text-align: center; color: #fff;}
	.maincolors,.assistcolor,.sidebar,.content{ position: relative;}
	.maincolors{ width:25%; height: 50px; float: left; line-height: 50px;}
	.assistcolor{ width:75%; height: 50px; float: left; line-height: 50px;}
	.sidebar{ width:25%; height: 297px; float: left; background-color: {$vo.skin_data.sidebar|default='#041200'}; line-height: 75px;}
	.content{ float:left; padding: 5px; color: #000; }
	.content div{ padding:3px 5px; float: left;}
	/*颜色选择框*/
	.layui-colorpicker-main-input .layui-btn-primary{display:none}
	.layui-colorpicker{padding:0;}
	.layui-colorpicker,.layui-colorpicker-trigger-span{border:none}
</style>
<div class="skinBox">
	<div id="headBox">
		<div class="maincolors">LOGO</div>
		<div class="assistcolor">顶部菜单</div>
	</div>
	<div class="sidebar">左侧菜单栏</div>
	<div class="content">
		<div>主色：<span id="maincolor" ></span></div>
		<div>辅色：<span id="assistcolor" ></span></div>
		<div>左侧：<span id="sidebar" ></span></div>
	</div>
	<form id="easy-form" class="layui-form layui-form-pane" style="display: none">
		<input type="text" name="skin_data[maincolor]" id="i_maincolor" value="{$vo.skin_data.maincolor|default='#0ba360'}"/>
		<input type="text" name="skin_data[assistcolor]" id="i_assistcolor" value="{$vo.skin_data.assistcolor|default='#3cba92'}"/>
		<input type="text" name="skin_data[sidebar]" id="i_sidebar" value="{$vo.skin_data.sidebar|default='#041200'}"/>
		{if isset($vo.id)}
		<input type="text" name="id" value="{$vo.id}"/>
		{/if}
		<button lay-submit lay-filter="easy-form" id="submit"></button> 
	</form>
</div>
<script>
function setHeadBg(){
	var maincolo = document.getElementById('i_maincolor').value;
	var assistcolor = document.getElementById('i_assistcolor').value;
	document.getElementById('headBox').style = +
	"background-image: -webkit-gradient(linear,left top,right top,from("+maincolo+"),to("+assistcolor+"));"+
	"background-image: -webkit-linear-gradient(left,"+maincolo+","+assistcolor+");"+
	"background-image: -moz-linear-gradient(left,"+maincolo+","+assistcolor+");"+
	"background-image: linear-gradient(to right,"+maincolo+","+assistcolor+");"
}
setHeadBg();
easy.define(function(){
	//layui初始化
	layui.use(['colorpicker'], function(){
		
		var colorpicker = layui.colorpicker;

		colorpicker.render({
			elem: '#maincolor'
			,color: $('#i_maincolor').val()
			,size: 'xs'
			,done: function(color){
				$('#i_maincolor').val(color);
			}
			,change: function(color){
				$('#i_maincolor').val(color);
				setHeadBg();
			}
		});

		colorpicker.render({
			elem: '#assistcolor'
			,color: $('#i_assistcolor').val()
			,size: 'xs'
			,done: function(color){
				$('#i_assistcolor').val(color);
			}
			,change: function(color){
				$('#i_assistcolor').val(color);
				setHeadBg();
			}
		});

		colorpicker.render({
			elem: '#sidebar'
			,color: $('#i_sidebar').val()
			,size: 'xs'
			,done: function(color){
				$('#i_sidebar').val(color);
			}
			,change: function(color){
				$('.sidebar').css('background-color',color);
				$('#i_sidebar').val(color);
			}
		});

	});
});
</script>