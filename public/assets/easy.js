/**
  扩展 easy 模块
**/
//全局定义
var $;
var imageAdd;

//在最顶层打开loding
if(window.top.layer && ! easy.closeloading ){
	window.top.layer.msg('正在处理...', {icon: 16,shade: 0.01,time:3000,zIndex:19911207});
}

//页面加载完成后执行
window.onload = function(){
	"use strict";
	var layer = window.top.layer || layer || false;
	if(!layer){  return false; }
	layer.closeAll('dialog');
	layer.closeAll('loading');
};

//预加载的模块
layui.load_modular = ['jquery','element','layer','form'];

//页面表格元素加载table模块
if( document.getElementById("layui-table") ){
	layui.load_modular.push('table');
}

//加载：上传组件
if( document.getElementsByClassName('layui-upload').length>0 ){
	layui.load_modular.push('upload');
}

//加载：下拉多选组件
if( document.getElementsByClassName('layui-multiselect').length>0 ){
	layui.extend({
		multiSelect: '{/}'+easy.config.assets+'layui/res/multiSelect'
	});
	layui.use(['multiSelect'],function() {
		var multiSelect = layui.multiSelect;
	});
}

layui.define(layui.load_modular, function(exports){
	
	"use strict";
	
	$ = layui.$;
	
	/** 
	 * 将要转为URL参数字符串的对象 
	 * return URL参数字符串 
	 */
	function urlEncode (url, data) {
		if(typeof(url) === 'undefined' || url === null || url === '') {return '';}
		if(typeof(data) === 'undefined' || typeof(data) !== 'object') {
//			return '1';
		}else{
			url += (url.indexOf("?") !== -1) ? "" : "?";
		}
		for(var k in data) {
			if( typeof(data[k]) !== 'undefined' || url !== null || url !== '' ){
				url += ((url.indexOf("=") !== -1) ? "&" : "") + k + "=" + encodeURI(data[k]);
			}
		}
		return url;
	}
	
	var obj = {
		
		/*
		* 自动设置窗口大小
		*/
		setIframeSize: function(){
			var width = $(window).width();
			var height = $(window).height();
			if(width<=1000){
				width = '100%';
				height = '100%';
			}else if(width>1300 && width<2000){
				width = '1000px';
				height = (height <= 800) ? '85%' : '80%';
			}else if(width>1600 && height > 800){
				width = '1000px';
				height = '720px';
			}else{
				width = '1000px';
				height = '98%';
			}
			return [width,height];
		},
	
		/*
		* loading 层
		*/
		loading: function(options){
			var that_layer = window.top.layer || layer;
			that_layer.msg((options&&options.loading&&options.loading.text)||'正在处理...', { icon: 16, shade: 0.01, time: (options&&options.time)||0 });
			return that_layer;
		},
		
		/*
		* toast提示层
		* options 选项参数
		* callback 回调方法
		*/
		toast: function(options,callback){
			var windowlayer = window.top.layer || layer;
			var windowlayui = window.top.layui || layui;
			windowlayer.closeAll('dialog');
			windowlayui.toast({
				heading: options.heading || options.icon ,
				icon: options.icon || '',
				text: options.text || 'null',
                hideAfter: options.hideAfter || 5000, //停留时间
				position:'top-right',
				afterHidden: function () {
					if( typeof(callback) === "function" ){ callback(); } 
				}
			});
		},
		
		/*
		* 成功提示
		* content 提示内容
		* callback 回调方法
		*/
		success: function(content,callback){
			this.toast({heading:'Success',icon:'success',hideAfter:3000,text:content||'处理成功'},callback);
			return true;
		},
		
		/*
		* 警告提示
		* content 提示内容
		* callback 回调方法
		*/
		warning: function(content,callback){
			this.toast({heading:'Warning',icon:'warning',text:content||'警告提示'},callback);
			return true;
		},
		
		/*
		* 错误提示
		* content 提示内容
		* callback 回调方法
		*/
		error: function(content,callback){
			this.toast({heading:'Error',icon:'error',text:content||'处理失败，请重试'},callback);
			return false;
		},
		
		/*
		* 刷新列表数据
		*/
		tablereload: function(value){
			if( typeof(layui.table) !== 'undefined' && typeof(layui.table.config) !== 'undefined' ){
				if( layui.table.config.page === false ){
					layui.table.reload('layui-table');
				}else{
					if( value == 1 ){
						//根据原有的条件重置table
						layui.table.reload('layui-table');
					}else{
						//无条件重置table
						layui.table.reload('layui-table',{where:false});
					}
				}
			}else{
				//来源窗口对象
				var parentIframe = false;
				if( self.frameElement ){
					parentIframe = self.frameElement.getAttribute('id') || self.frameElement.getAttribute('name');
					if(parentIframe){
						window.top.document.getElementById(parentIframe).contentWindow.location.reload(true);
					}
				}
			}
			return true;
		},
		
		/** 
		 * 添加数据窗口 最终是调用 edit方法
		 * param 请求参数
		 * callback 自定义回调
		 */ 
		add: function(options,callback){
			if( typeof(options) !== 'object' ){
				return this.error('easy.add() 方法options参数必须为对象类型');
			}
			if( typeof(options.param) !== 'undefined' && typeof(options.param) !== 'object' ){
				return easy.error('easy.add() 方法param参数必须为对象类型或空');
			}
			if( typeof(options.url) === 'undefined'){
				if( typeof(easy.url.add) !== 'undefined' ){
					options.url = easy.url.add;
				}else{
					return easy.error('easy.add() 方法options参数缺少url值');
				}
			}
			if( typeof(options.title) === 'undefined'){
				options.title = '添加';
			}
			this.edit(options,callback);
		},
		
		/** 
		 * 编辑数据窗口 
		 * options 选项参数 
		 * param 请求参数
		 */ 
		edit: function(_options,callback){
			let options = _options;			
			if( typeof(options) !== 'object' && options !== false){
				return easy.error('easy.edit() 方法 options参数必须为对象类型');
			}
			//将对象数据转换成url参数
			var content = urlEncode( options.url || easy.url.edit , options.param );
			//自动计算弹出窗口的尺寸
			if( typeof(options.width) === 'undefined' || typeof(options.height) === 'undefined' ){
				var IframeSize = this.setIframeSize();
				options.width  = options.width || IframeSize[0];
				options.height = options.height || IframeSize[1];
			}
			//统一设置
			if( typeof(easy.open) !== 'undefined' ){
				if( typeof(easy.open.width) !== 'undefined'){
				   if(typeof(easy.open.width) === 'number'){
					 options.width = easy.open.width +'px';
				   }else{
					 options.width = easy.open.width;
				   }
				}
				if( typeof(easy.open.height) !== 'undefined'){
				   if(typeof(easy.open.height) === 'number'){
					 options.height = easy.open.height +'px';
				   }else{
					 options.height = easy.open.height;
				   }
				}
				if( easy.open.title == false ){
				  options.title = 'none'
				}else if( typeof(easy.open.title) !== 'undefined' ){
					if( typeof(options.title) === 'undefined' ){
						options.title = '编辑';
					}
				  options.title = options.title + easy.open.title;
				}
			}
			
			//独立设置
			if( typeof(_options.open) !== 'undefined' ){
				options.width  = _options.open.width;
				options.height = _options.open.height;
			   if(typeof(options.width) === 'number'){
				 options.width = options.width +'px';
			   }
			   if(typeof(options.height) === 'number'){
				 options.height = options.height +'px';
			   }
			}
			//浏览器可视高度
			var w_height = window.top.document.documentElement.clientHeight 
							|| document.documentElement.clientHeight 
							|| document.body.clientHeight;
			
			//当弹窗高度大于浏览器可视高度
			if( parseInt(options.height) > w_height ){
				options.height = '95%';
			}
			//layer对象
			var layer = window.top.layer || layer;
			//来源窗口对象
			var parentIframe = false;
			if( self.frameElement ){
				parentIframe = self.frameElement.getAttribute('id') || self.frameElement.getAttribute('name');
			}
			//窗口标题
			if( options.title === "false" || options.title === false  || options.title === 'none' ){
				options.title = false;
			}else{
				options.title = options.title || '编辑';
			}
			
			//打开新窗口
			layer.open({
				skin: 'easy-layer',
				type: 2,
				anim: options.isOutAnim || -1,
				resize: options.resize || false ,
				offset: options.offset || (easy.open && easy.open.offset) || 'auto' ,
				shade: options.shade === 0 ? false : options.shade || [0.5] ,
				title: options.title,
				content : content || 'null',
				isOutAnim: options.isOutAnim || false,
				area : [options.width,options.height],
				success : function(layero,index){
					//清空参数
					delete options.url;
					delete options.param;
					options = false;
					//打开成功后回调
					if( typeof(callback) === "function" ){ callback(); } 
					//点击后置顶
					layer.setTop(layero);
				},
				btn: options.btn || ['保存','刷新'],
				//保存
				yes: function(index, obj){
					var iframeWin = window.top[obj.find('iframe')[0]['name']];
					iframeWin.SetParameter(parent,parentIframe);
					layer.getChildFrame('#submit', index).click();
				},
				//刷新
				btn2: function(index, obj){
					window.top.document.getElementById(obj.find('iframe')[0]['name']).contentWindow.location.reload(true);
					return false;
				},
				//关闭
				cancel: function(index,obj){  
					if( typeof(option) !== "undefined" && option.cancel == 1){
						layer.confirm('你确定要关闭此窗口吗？ 未保存的数据会丢失哦！', {skin: 'easy-layer',btn: ['确定','取消'],offset: '10px'
						}, function(){
							layer.close( layer.getFrameIndex(obj.find('iframe')[0]['name']) );//关闭当前窗口
							layer.closeAll('dialog'); //关闭信息框
						}, function(){
							return true;
						});
						return false;
					}
				}
			});
		},
				
		/** 
		 * 删除 
		 * options 选项参数 url 必填、 title 提示标题、content 提示内容
		 * options.param 请求参数 
		 * callback 自定义回调
		 */  
		delete: function(options,callback)
        {
			if( typeof(options) !== 'object' ){
				return easy.error('easy.edit() 方法options参数必须为对象类型');
			}
			var layer = window.top.layer || layer;
			var index = layer.confirm(options.content || "确定删除数据吗？", {
				skin: 'easy-layer',
				title: options.title || '操作提示',
				btn: ['确定','取消'],
				shade:0.3,
				offset: 60,
				isOutAnim: false,
			}, function(){
				//关闭确认窗口
				layer.close(index);
				//设置请求url
				if( typeof(options.url) === 'undefined'){
					if( typeof(easy.url.delete) !== 'undefined' ){
						options.url = easy.url.delete;
					}else{
						return easy.error('easy.delete() 方法options参数缺少url值');
					}
				}
				//发送请求
				easy.ajax(options,function(data){
					if(data.code === 1){
						//删除对应的行
						if( typeof(options.obj) !== 'undefined'){
							options.obj.del();
						}else{
							easy.tablereload(1);
						}
						easy.success(data.msg||'');
					}else{
						easy.error(data.msg||'');
					}
					//回调处理
					if( typeof(callback) === "function" ){ callback(data); } 
				});
			});
		},
		
		/** 
		 * 批量操作 
		 * options 选项参数
		 * options.list 请求参数数组 示例：[{id:1,field:1},{id:2,field:2}]
		 * callback 自定义回调
		 */  
		multi: function(options,callback)
        {
			if( typeof(options) !== 'object' ){
				return easy.error('easy.multi() 方法options参数必须为对象类型');
			}
			if( typeof(options.list) !== 'object' ){
				return easy.error('easy.multi() 方法options.list参数必须为对象类型');
			}
			this.ajax({ url: easy.url.multi , param: { list : options.list } },callback);
		},
		
		
		/*提交Form数据
		* options 参数
		* callback 回调方法
		*/
		formsubmit: function(options,callback)
        {
			var layer = window.top.layer || layer;
            var w = window;
			$.ajax({
				type:"POST",dataType:"JSON",
				url:  options.form.action,
				data: options.field,
                beforeSend:function(){
                    if( options.hideloading != true ){
                        layer.msg('正在处理...', {
                            icon: 16,
                            shade: 0.01,
                            time:0,
                            zIndex: layer.zIndex, //重点1
                            success: function(layero){
                                layer.setTop(layero); //重点2
                            }                 
                        });
                    }
                },
				success:function(res){
					layer.closeAll('dialog');//关闭加载框
					if( res.code === 1 || res.code === 200 ){
                        //关闭成功提示
						if( ! res.off_success ){
                           easy.success( res.msg || '' );
                        }
						if( typeof(callback) === "function" ){ 
							callback(res);  
						}else{
							if(typeof(parents) !== "undefined"){
								if( typeof(parentIframes) !== "undefined" && typeof(parents.frames[parentIframes]) !== "undefined" ){
                                     parents.frames[parentIframes].easy.tablereload(1);
								}else{
									parents.easy.tablereload(1);
								}
							}
						}
                        if(!options.closeFrame){
                            layer.close( layer.getFrameIndex(window.name) );//关闭当前窗口
                        }
					}else{
						easy.error( res.msg || '' );
					}
				},
				error:function(){ easy.error(); }
			});
			return false;
		},
		
		/*在最顶层打开窗口 
		*
		* 方式一
		* data.title 	窗口标题 选填
		* data.url 		链接地址 必填
		* data.param 	get请求附带参数 必填
		*
		* 方式二
		* data.title 	窗口标题 选填
		* data.content 	html代码或链接地址 必填
		
		* 说明, 当传入 data.param 时  data.url 为必填值
		
		* options.width 	窗口宽度	为空则自动计算
		* options.height 	窗口高度	为空则自动计算
		* options.offset 	1.窗口显示位置  '100px' 只定义top坐标，水平保持居中
							2.窗口显示位置  ['100px', '50px'] 同时定义top、left坐标
		* options.cancel 	点击关闭是否提示确认关闭 1 or 0
		* options.skin 		窗口皮肤
		* options.shade 	遮罩透明度 0-1.0
		*/
		iframe: function(options,callback)
        {
			if( typeof(options) !== "undefined" ){
				//方式一
				if( typeof(options.param) !== "undefined" ){
					//必填url地址
					if( typeof(options.url) === "undefined" ){
						return this.error('Lack of parameters (url) ');
					}
					//将对象数据转换成url参数
					var content = urlEncode(options.url,options.param);
				//方式二
				}else{
					if( typeof(options.url) !== "undefined" ){
						var content = options.url;
					}else if( typeof(options.content) === "undefined" ){
						return this.error('Lack of parameters (content) ');
					}else{
						var content = options.content;
					}
				}
				delete options.url;
				delete options.content;
			}else{
				return this.error('Lack of parameters (options) ');
			}			
			//计算高度
			if( typeof(options) === "undefined" || (!options.width && !options.height) ){
				var IframeSize = this.setIframeSize();
				options.width  = IframeSize[0];
				options.height = IframeSize[1];
			}
			//窗口位置
			if( options.offset === 'tr'){
				var win_width = $(window).width();
				var left = win_width - parseInt(options.width) - 10;
				options.offset = [60,left];
			}
			//统一设置
			if( typeof(easy.iframeArea) !== 'undefined' ){
				if( typeof(easy.iframeArea) !== 'undefined' ){
				   if(typeof(easy.iframeArea.width) === 'number'){
					 options.width = easy.iframeArea.width +'px';
				   }else{
					 options.width = easy.iframeArea.width;
				   }
				}
				if( typeof(easy.iframeArea.height) !== 'undefined'){
				   if(typeof(easy.iframeArea.height) === 'number'){
					 options.height = easy.iframeArea.height +'px';
				   }else{
					 options.height = easy.iframeArea.height;
				   }
				}
			}
			//打开窗口
			var windowlayer = window.top.layer || layer; 
			windowlayer.open({
				anim: -1, type: 2, isOutAnim: false,
				skin: ( (options && options.skin) || 'easy-layer' ),
				offset: ( (options && options.offset) || 'auto' ),
				shade: ( typeof(options.shade) !== "undefined" ? options.shade : (options && options.shade) || [0.5] ),
				area: [options.width, options.height],
				title: options.title || false,
				content: content || '传入的参数有误',
                success: function(layero, index){
                    //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                    var iframeWin = window.top[layero.find('iframe')[0]['name']];
                    //重新定义窗口样式
                    if ( typeof(iframeWin.setStyle) === "function" ){
                        iframeWin.setStyle(windowlayer,index);
                    }
                }
			});
		},
		
		/*
		* 图片预览框
		* options 选项参数  src
		*/
		imageIframe: function(options)
        {
			var src = options.elem.attr('data-src') || options.elem.attr('src');
			if( ! src ) return false;
			layer.open({
				type: 1,
				title: false,
				anim:-1,
				shade:0.1,
				content: '<img src="'+src+'" style="background-color:#1278f6;margin:10px;"/>'
			});
		},
		
		/*
		* 数据请求
		* options 选项参数  url / data
		* callback 回调方法
		*/
		ajax: function(options,callback){
			if( typeof(options) === 'undefined' ){
				return easy.error('参数不能为空');
			}
			//第一个参数传入非对象，默认为url 
			if( typeof(options) !== 'object' ){
				var _options = {};
				_options.url = options;
				options = _options;
			}
			if(options.hideloading!=true){
				var thelayer = this.loading(options);
			}
			$.ajax({
				url : options.url , 
				data : options.param || options.data || {},
				type : options.type || "POST", 
				dataType : options.dataType || "JSON",
				success : function(ret){
                    if(thelayer){
                        thelayer.closeAll('dialog');
                    }
					//回调处理
					if( typeof(callback) === "function" ){
						callback(ret); 
					//不回调就提示信息
					}else{
						if(ret.code===1){
							easy.success(ret.msg||'');
						}else{
							easy.error(ret.msg||'');
						}
					}
				},
				error: function(ret){
                    if(thelayer){
                        thelayer.closeAll('dialog');
                    }
                    alert('系统请求发生错误，请刷新重试！');
				}
			});
			return true;
		},
		
		/*
		* 数据表格工具栏
		* d 表格数据
		*/
		toolbar: function(d){
			if( !d ) { return '未传入参数'; }
			var str = '<a class="layui-btn" lay-event="edit" title="编辑">编辑</a>';
			if(d.deleting === 0 ){
				str += '<a class="layui-btn layui-btn-disabled" title="禁止删除">删除</a>';
			}else{
				str += '<a class="layui-btn layui-btn-danger" lay-event="delete" title="删除">删除</a>';
			}
			return str;
		},
		
		/*
		* 图片
		* src 图片地址
		*/
		image: function(src){
			return src ? '<img src='+src+' />' : '-';
		},
		
		/*
		* 数据表格状态栏
		* d 表格数据
		*/
		status: function(d){
			return d.status === 1 ? '<span class="layui-badge-dot layui-bg-green"></span>' : '<span class="layui-badge-dot"></span>';
		},
		
		/*
		* 数据表格a标签按钮组
		* e 配置
		* e = [{title:'按钮1',href:'href1'},{title:'按钮1',href:'href2'},......]
		*/
		alabelGroup: function(e)
        {
			var length = e.length;
			var html = '';
			if(length<0) return '-';
			//循环组装按钮
			for (var i=0; i<length; i++){
				var This  = e[i];
				var href  = This.href  || '';
				var target= This.target|| '_blank';
				var event = This.event || '';
				var Class = This.class || 'layui-btn-normal';
				var title = This.title || '按钮';
				//跳转连接
				if( href ){ 
					var href = 'target="'+target+'" href="'+href+'"';
				}
				//自定义事件
				if( event ){ 
					var href = 'lay-event="'+event+'"';
				}
				html += '<a class="layui-btn '+Class+'" '+href+' title="'+title+'">'+title+'</a>';
			}
			return html;
		},
		
		/*
		* 数据表格按钮
		* d 表格数据
		*/
		buttonGroup: function(d)
        {
			return 1;
		},
		
		/*
		* 数据表格状态栏
		* d 表格数据
		*/
		switchstatus: function(d)
        {
			var checked = d.status === 1 ? 'checked' : '';
			return '<input type="checkbox" name="status" value="'+d.id+'" lay-skin="switch" lay-filter="status" '+checked+'>';
		},
		
		/*
		* 数据表格开关按钮
		* id    : 数据表主键
		* filed : 字段名
		* value : filed字段的值
		*/
		switcher: function(id,filed,value)
        {
			var checked = value ? 'checked' : '';
			return '<input type="checkbox" name="'+filed+'" value="'+id+'" lay-skin="switch" '+checked+'>';
		},
		
		/*
		* 工具栏按钮点击事件
		* elem 按钮对象
		*/
		bindToolbarBtnClick: function(elem)
        {
			//替换
			if( elem.indexOf(".") === -1 && elem.indexOf("#") === -1){
				return alert('参数开头必须是 . 或 #');
			}
			if( $(elem).length <= 0 ){ return false; }
			//点击事件
			$(elem).click(function(){
				var This = $(this);
				var title = This.find('font').html();
				if(easy.open && easy.open.title == false){
					title = 'none';
				}
				var options = {title:title};
				//添加url参数
				options.url = This.attr('data-url') || '';
				options.elem = This;
				//替换
				var str = elem.replace('#','');
					str = elem.replace('.','');
				var Function = str.replace('easy-btn-','easy.');
				//调用指定方法
				try {
					if (typeof( eval(Function) ) === "function") {
						eval( Function+'(options)' );
						return true;
					}else{
						return easy.error(Function+'() 未定义');
					}
				} catch (e) {
					return easy.error(Function+'() 未定义');
				}
			});
		},
		
		//再次确认窗口
		artCaveat: function(content,confirm,cancel){
			var layer = window.top.layer || layer;
			layer.confirm(content ? content : "真的确定吗?", {
				skin: 'easy-layer',
				title:'操作提示',
				btn: ['确定','取消'],
				shade:0.3,
				offset: 60,
				isOutAnim: false,
			}, function(){
				if(confirm){
					 confirm();
				}
			}, function(){
				if(cancel){
					cancel();
				}
			});
		},
		
		//时间戳转化成时间格式
		formatDate: function(timestamp,type){
		  //timestamp是整数，否则要parseInt转换,不会出现少个0的情况
			var time = new Date(timestamp*1000);
			var year = time.getFullYear();
			var month = time.getMonth()+1;
			var date = time.getDate();
			var hours = time.getHours();
			var minutes = time.getMinutes();
			var add0 = function(m) {
				return m < 10 ? '0' + m : m;
			};
			if(type==2){
				return add0(month)+'-'+add0(date)+' '+add0(hours)+':'+add0(minutes);
			}else if(type){
				return year+'-'+add0(month)+'-'+add0(date)+' '+add0(hours)+':'+add0(minutes)+':'+add0(minutes);	
			}else{
				return year+'-'+add0(month)+'-'+add0(date);
			} 
		}
		
	};
	
	//输出全局接口
	window.easy = Object.assign( easy , obj );
	
	//输出 layui.easy 接口
	exports('easy', false);
	
	//设置按钮工具栏的 刷新事件
	easy.bindToolbarBtnClick('.easy-btn-tablereload');
	//设置按钮工具栏的 添加内容事件
	easy.bindToolbarBtnClick('.easy-btn-add');
	//设置按钮工具栏的 异步请求事件
	easy.bindToolbarBtnClick('.easy-btn-ajax');
	//打开窗口
	easy.bindToolbarBtnClick('.easy-btn-iframe');
	//打开窗口
	easy.bindToolbarBtnClick('.easy-btn-imageIframe');
	//设置按钮工具栏的 批量删除数据事件
	$('.easy-btn-delete').click(function(){
		var options = {};
		options.url = $(this).attr('data-url');
		//获取选中的数据
		var checkStatus = layui.table.checkStatus('layui-table');
		if(checkStatus.data.length === 0){
			return easy.error('至少选择一条数据');
		}else{
			var j = 0 ,
				ids	 = new Array(),
				data = checkStatus.data;
			for(var item in data) {
				var deleting = data[item]['deleting'];
				if( typeof(deleting) === 'undefined' || deleting === null || deleting === '' || deleting === 1 ){
					ids[j] = data[item]['id'];
					j++;
				}
			}
			if( ids.length == 0 ){
				return easy.warning('选择的数据存在不可删除');
			}
			options.param = {id:ids}
			options.title = '删除提示';
			options.content = '你选中了 '+data.length+' 条数据，有 '+ids.length+' 条可删除！<br>确认删除吗？'
			ids.join();
			easy.delete(options);
		}
	});
	
	//打开多个编辑窗口
	$('.easy-btn-edit').click(function(){
		var options = {};
		options.url = $(this).attr('data-url');
		//获取选中的数据
		var checkStatus = layui.table.checkStatus('layui-table');
		if(checkStatus.data.length === 0){
			return easy.error('至少选择一条数据');
		}else if( checkStatus.data.length <= 5 ){
			var arrs = checkStatus.data;
			var data = [];
			var j = 1;
			var w_width = window.top.document.documentElement.clientWidth || document.documentElement.clientWidth || document.body.clientWidth;
			var w_height = window.top.document.documentElement.clientHeight || document.documentElement.clientHeight || document.body.clientHeight;
			//循环延迟打开窗口
			for(var item in arrs) {
				setTimeout(open(item),700*item)
			}		
			function open(item){
				return function(){
					if(item==0){
						options.shade = 0.5;
					}else{
						options.shade = 0;
					}
					var top = 30 + (item >= 0 ? item : item - 1 ) * 30 ;
					if( top + parseInt(options.height) >= w_height ){
						top = 30 + (item-2*j) * 30 ;
						if(top < 30){
							j--;
							top = 30 + (item-2*j) * 30 ;
						}else{
							j++
						}
					}
					options.offset= [top,200+item*60];
					options.param = {id:arrs[item].id};
					easy.edit(options);
				}
			}
			delete checkStatus.data;
		}else{
			return easy.error('最多只能选择5个进行编辑')
		}
	});
	
    //删除图片
    $('.delete-image').click(function(){
        var field = $(this).attr('del-field');
        var index = layer.confirm("确定删除图片吗？", {
            skin: 'easy-layer',
            title: '操作提示',
            btn: ['确定','取消'],
            shade:0.3,
            offset: 60,
            isOutAnim: false,
        }, function(){
            $('#'+field+'_img').attr('src','/assets/admin/images/nopic.png');
            $('#'+field).val('');
            //关闭确认窗口
            layer.close(index);
        });
    });
    
	//初始化编辑器
	if( $('.easy-editor').length >0 ){	
		//设定编辑器模块别名
		layui.extend({
			editor: '{/}'+easy.config.assets+easy.config.editor+'/editor'
		});
		//layui初始化editor配置
		layui.use('editor');
	}
	
	//监听主Form表单提交事件
	if( document.getElementById("easy-form") ){
		//监听submit提交
		layui.form.on('submit(easy-form)', function(data){
			return easy.formsubmit(data);
		});
	}
	
	//监听表格相关事件
	if( document.getElementById("layui-table") ){
		//监听 编辑/删除 按钮事件
		layui.table.on('tool', function(obj){
			//编辑
			if(obj.event === 'edit'){
				//调用
				easy.edit({ param:{id:obj.data.id} });
			//删除
			}else if(obj.event === 'delete'){
				easy.delete({obj:obj, param:{id:obj.data.id} });
			}
		});
		//监听单元格编辑
		layui.table.on('edit', function(obj){
			var value = obj.value; //得到修改后的值
			var field = obj.field; //得到字段
			var list = { id:obj.data.id };
				list[field] = value ;
			easy.multi({ list : list } );
		});
		//监听状态开关操作
		layui.form.on('switch', function(obj){
			var val  = obj.elem.checked ? 1 : 0;
			var data = { list:{ id: this.value } };
			var name = obj.elem.name;
			if(name){
				//动态写入对象
				data.list[name] = val;
				//执行提交
				easy.multi(data);
			}else{
				alert('开关按钮缺少name值');
			}
		});
	}
	
	//初始化：日期时间
	if($('.layui-date').length > 0){
		layui.use('laydate', function(){
			$('.layui-date').each(function(){
				var that = $(this);
				var elem = that.attr('id');
				var type = that.attr('laydate-type') || 'date';
				if( !elem ) return false;
				layui.laydate.render({
					elem: '#' + elem,
					theme: 'easy',
					position: 'fixed',
					type: type
				});
			})
		})
	}
	
	//初始化：日期范围
	if($('.layui-date-range').length > 0){
		layui.use('laydate', function(){
			$('.layui-date-range').each(function(){
				var that = $(this);
				var elem = that.attr('id');
                var type = that.attr('laydate-type') || 'date';
				if( !elem ) return false;
				layui.laydate.render({
					elem: '#' + elem,
					theme: 'easy',
					range: true,
                    type: type
				});
			})
		})
	}
	
	//列表框组件事件
	if( $('.easy-listinput-add').length > 0 ){
		$('.easy-listinput-add').click(function(){
			var length = $(this).parent().find('.list-box').length;
			var name = $(this).attr('data-name');
			var html ='<div class="list-box"><input name="'+name+'['+length+'][key]" /> <input name="'+name+'['+length+'][value]" /><a class="layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a></div>';
			$(this).before(html);
		});
		$('.easy-form-listinput').on("click",'.list-box a',function(){
        	$(this).parent().html('').hide();
    	});
	}
	
	//多图片组件
	if( $('.easy-upload-images').length > 0 ){
		//普通图片上传
		var imagesAdd = layui.upload.render({
			elem: '.easy-upload-images'
			//,size:1024
			,acceptMime: 'image/*'
			,field:'easy-file-input'
			,exts:'jpg|png|gif|bmp|jpeg'
			,url: easy.url.upload
			,data:{field:'image',type:'image'}
			,multiple:true
			,before: function(obj){
				imagesAdd.config.data.field = this.field;
				layer.msg('正在上传...', {icon: 16,shade: 0.1,time:0});
			}
			,done: function(res){
				layer.closeAll('dialog');//关闭信息框
				//如果上传失败
				if(res.code != 1){
					return easy.error(res.msg||false);
				//上传成功
				}else if(res.code==1){
					var item = this.item;
					var name = item.attr('data-name');
					var html ='<div class="list-box"><img src="'+res.filepath+'"/><div><input name="'+name+'[title][]" value="'+res.title+'" placeholder="图片标题"/> <input name="'+name+'[url][]" value="'+res.filepath+'" placeholder="图片地址"/> <input name="'+name+'[href][]" placeholder="跳转链接"/></div><li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-back"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-right"></i></a></li></div>';
					item.siblings('.list-conten').append(html);
					easy.success('上传成功');
				}
			}
			,error: function(){
				layer.closeAll('dialog'); //关闭信息框
				return easy.error('上传失败，请重试');
			}
		});
		//删除
		$('.easy-form-images').on("click",'.list-box li a.del',function(){
        	$(this).parents('.list-box').remove();
    	});
	}
    
    //普通图片上传
    function easy_upload_render(elem){
		var imageAdd = layui.upload.render({
			elem: elem
			,size:1024
			,acceptMime: 'image/*'
			,exts:'jpg|png|gif|bmp|jpeg'
			,url: easy.url.upload
			,field:'easy-file-input'
			,data:{field:'image',type:'image'}
			,before: function(obj){
				imageAdd.config.data.field = this.field;
				layer.msg('正在上传...', {icon: 16,shade: 0.1,time:0});
			}
			,done: function(res){
				layer.closeAll('dialog');//关闭信息框
				//如果上传失败
				if(res.code != 1){
					return easy.error(res.msg||false);
				//上传成功
				}else if(res.code==1){
					var item = this.item;
					$('#'+this.field).val(res.filepath);
					$('#'+this.field+'_img').attr('src',res.filepath);
					easy.success('上传成功');
				}
			}
			,error: function(){
				layer.closeAll('dialog'); //关闭信息框
				return easy.error('上传失败，请重试');
			}
		});
    }
    
	//单图片上传组件
	if( $('.easy-upload-image').length > 0 ){
        easy_upload_render('.easy-upload-image')
	}
    
	//多图片组件(大小图)
	if( $('.easy-upload-imagebs').length > 0 ){
        var html = function(name,key){
            return '<div class="list-box">'
                    +'<li>'
                    +'    <img id="bimg_'+key+'_img" class="easy-btn-imageIframe"/>'
                    +'    <a class="layui-btn layui-btn-sm easy-upload-image upload_'+key+'" lay-data="{field:\'bimg_'+key+'\'}" title="上传或替换">上传</a>'
                    +'    <input name="'+name+'[bimg][]" class="hidden" id="bimg_'+key+'" />'
                    +'</li>'
                    +'<li>'
                    +'    <img id="simg_'+key+'_img" class="easy-btn-imageIframe"/>'
                    +'    <a class="layui-btn layui-btn-sm easy-upload-image upload_'+key+'" lay-data="{field:\'simg_'+key+'\'}" title="上传或替换">上传</a>'
                    +'    <input name="'+name+'[simg][]" class="hidden" id="simg_'+key+'" />'
                    +'</li>'
                    +'<input name="'+name+'[title][]" />'
					+'<input name="'+name+'[href][]" /><li class="easy-form-imagebs-tool"><a class="del layui-btn layui-btn-sm layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li>'
				+'</div>';
        }
        $('.easy-upload-imagebs').click(function(){
            var name = $(this).attr('data-name');
            var key = Date.parse( new Date());
            var text = html(name,key)
            $(this).siblings('.list-conten').append(text);
            easy_upload_render('.upload_'+key);
        })
		//删除
		$('.easy-form-imagebs').on("click",'.list-box li a.del',function(){
        	$(this).parents('.list-box').remove();
    	});
	}
	
	//单文件上传组件
	if( $('.easy-upload-file').length > 0 ){
		//普通图片上传
		var fileAdd = layui.upload.render({
			elem: '.easy-upload-file'
			,accept:'file'
			,url: easy.url.upload
			,field:'easy-file-input'
			,data:{field:'file',type:'file'}
			,before: function(obj){
				fileAdd.config.data.field = this.field;
				layer.msg('正在上传...', {icon: 16,shade: 0.1,time:0});
			}
			,done: function(res){
				layer.closeAll('dialog');//关闭信息框
				//如果上传失败
				if(res.code != 1){
					return easy.error(res.msg||false);
				//上传成功
				}else if(res.code==1){
					var item = this.item;
					$('#'+this.field).val(res.filepath);
					easy.success('上传成功');
				}
			}
			,error: function(){
				layer.closeAll('dialog'); //关闭信息框
				return easy.error('上传失败，请重试');
			}
		});
	};
	
	//多文件组件
	if( $('.easy-upload-files').length > 0 ){
		//普通图片上传
		var filesAdd = layui.upload.render({
			elem: '.easy-upload-files'
			,accept:'file'
			,field:'easy-file-input'
			,url: easy.url.upload
			,data:{field:'file',type:'file'}
			,multiple:true
			,before: function(obj){
				filesAdd.config.data.field = this.field;
				layer.msg('正在上传...', {icon: 16,shade: 0.1,time:0});
			}
			,done: function(res){
				layer.closeAll('dialog');//关闭信息框
				//如果上传失败
				if(res.code != 1){
					return easy.error(res.msg||false);
				//上传成功
				}else if(res.code==1){
					var item = this.item;
					var name = item.attr('data-name');
					var length = item.parent().find('.list-box').length;
					var html ='<div class="list-box"><input name="'+name+'[title][]" value="'+res.title+'" /> <input name="'+name+'[url][]" value="'+res.filepath+'" /> <input name="'+name+'[ext][]" value="'+res.ext+'" /> <input name="'+name+'[size][]" value="'+res.filesize+'" /><li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li></div>';
					item.before(html);
					easy.success('上传成功');
				}
			}
			,error: function(){
				layer.closeAll('dialog'); //关闭信息框
				return easy.error('上传失败，请重试');
			}
		});
		//删除
		$('.easy-upload-files').on("click",'.list-box li a.del',function(){
        	$(this).parents('.list-box').remove();
    	});
	};
	
    //向上移动
    $('.easy-form-sortbut').on("click",'.list-box li a.up',function(){
       $(this).parents('.list-box').attr('data', 'true');
        $.each($('.easy-form-sortbut .list-conten .list-box'),function(i, t) {
            var fl = $(t).attr('data');
            if (fl == 'true') {
                if ($(t).prev().size() > 0) {
                    $(t).removeAttr('data').prev().before(t);
                }
            }
        })
    });
    
    //向下移动
    $('.easy-form-sortbut').on("click",'.list-box li a.down',function(){
       $(this).parents('.list-box').attr('data', 'true');
        $.each($('.easy-form-sortbut .list-conten .list-box'),function(i, t) {
            var fl = $(t).attr('data');
            if (fl == 'true') {
                if ($(t).next().size() > 0) {
                    $(t).removeAttr('data').next().after(t);
                }
            }
        })
    });
    
    //动态字段列表 添加
    if( $('.easy-fieldlist-add').length > 0 ){
        $('.easy-fieldlist-add').click(function(){
            var name = $(this).attr('data-name');
            var html = '<div class="list-box"><input class="arrkey" name="'+name+'[key][]" /><input class="arrval" name="'+name+'[val][]" /><li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li></div>';
            $(this).siblings('.list-conten').append(html);
        });
        //删除
        $('.easy-form-listinput').on("click",'.list-box li a.del',function(){
            $(this).parents('.list-box').remove();
        });
    }
	//加载：地区选择器
	if( $('.layui-city-picker').length > 0 ){
		layui.link(easy.config.assets+'layui/res/citypicker/city-picker.css');
		//
		var ueditorJS = document.createElement('script');
		ueditorJS.setAttribute('type', 'text/javascript');
		ueditorJS.setAttribute('src', easy.config.assets+'layui/res/citypicker/city-picker.data.js');
		document.body.appendChild(ueditorJS);
		//
		ueditorJS.onload = function(){
			layui.extend({
				citypicker: '{/}'+easy.config.assets+'layui/res/citypicker/city-picker'
			});
			layui.use(['citypicker'],function() {
				var cityPicker = layui.citypicker;
				$('.layui-city-picker').each(function(){
					var that = $(this);
					var elemid = that.attr('id');
					if( ! elemid ){ return false; }
					var values = that.attr('data-value');
					new cityPicker("#"+elemid, {
						provincename	: elemid + '_provinceId',
						cityname		: elemid + '_cityId',
						districtname	: elemid + '_districtId',
						level			: 'districtId',
					}).setValue(values);
				});
			});
		}
	}
	
	//设置可自定义输入的下位框
	if( $('.easy-select-input').length > 0 ){
		$('.easy-select-input').each(function(){
			var select = $(this);
			select.next('.layui-form-select').find('div>input').removeAttr("readonly").bind('blur', function () {
				var value = $(this).val() || '';
				select.html('<option value="'+value+'" selected>'+value+'</option>')
			});
		});
	}
		
});


function SetParameter(parent,parentIframe){
	parents = parent;
	parentIframes = parentIframe;
}

//默认回调刷新页面
function Callback(){
	window.location.reload();
}

//关闭窗口
function layerClose(){
	var windowTopLayer = window.top.layer;
	windowTopLayer.close( windowTopLayer.getFrameIndex(window.name) );//关闭当前窗口
}

//刷新窗口
function windowReload(){
	window.location.reload();
}

//移除元素
function remove_this(obj){
	$('#'+obj).remove();
}