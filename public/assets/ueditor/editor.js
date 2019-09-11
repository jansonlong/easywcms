layui.define(function(exports){
		
	var obj = {
		
		load: function(v){
			//v = '?v='+ new Date().getTime();;
			var body = document.body;
			var ueditorJS = document.createElement('script');
			ueditorJS.setAttribute('type', 'text/javascript');
			ueditorJS.setAttribute('src', easy.config.assets+easy.config.editor+'/ueditor.config.js');
			body.appendChild(ueditorJS);
			//
			ueditorJS.onload = function(){
				ueditorJS = document.createElement('script');
				ueditorJS.setAttribute('type', 'text/javascript');
				ueditorJS.setAttribute('src', easy.config.assets+easy.config.editor+'/ueditor.all.min.js');
				body.appendChild(ueditorJS);
				//
				ueditorJS.onload = function(){
					obj.use();
					delete(ueditorJS);
				}
			}
		},
		
		//初始化
		use: function(){
			//轮询判断是否加载完成
			if( typeof(UE) === 'object' && typeof(eval('UE.getEditor')) === "function" ){
				//循环多个编辑器
				$('.easy-editor').each(function(){
					var this_id = $(this).attr('id');
					//隐藏提示
					$('#'+this_id+'_loading').remove();
					UE.getEditor(this_id);
				});
			//定时
			}else{
				setTimeout(function(){ obj.use() },500);
			}
		}
		
	}
	
    obj.load();
	
	//输出接口
	exports('editor',false);
	
});