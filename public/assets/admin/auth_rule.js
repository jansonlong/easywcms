layui.define(function(exports){
	
	"use strict";
	
	easy.open = {width:800,height:544};
    
    var obj = {
        index: function(){
            layui.table.render({
                elem: '#layui-table',
                url: easy.url.index,
                even: true,
                cols: [[
                    {type: 'checkbox'}
                    ,{field:'id', 		title: '编号', width:70, align: 'center', unresize:true}
                    ,{field:'listorder',title: '排序', width:70, align: 'center', edit: 'text'}
                    ,{field:'status',	title: '显示', width:70, align: 'center', templet:function(d){ return easy.switchstatus(d); }}
                    ,{field:'fontico', 	title: '图标', width:70, align: 'center', templet:function(d){ return '<i class="iconfont '+d.fontico+'"></i>'}}
                    ,{field:'alias', 	title: '标题', width:220}
                    ,{field:'name', 	title: '规则', width:220,edit: 'text',}
                    ,{field:'description', 	title: '说明', minWidth:200,edit: 'text', unresize:false}
                    ,{field:'restful', 	title: '资源', width:75, align: 'center',templet:'#restfulTpl'}
                    ,{field:'toolbar', 	title: '操作', width:120,templet:function(d){ return easy.toolbar(d); }}
                ]],
                 height: 'full-130'
            });

            //设置不需要分页
            layui.table.set({page:false});

            //监听按钮
            layui.table.on('tool(layui-table)', function(obj){
                if(obj.event === 'restful'){
                    easy.iframe({
                        title : '['+obj.data.title+'] - 资源管理',
                        url : easy.url.base + 'restful',
                        param : {id:obj.data.id},
                        width:'1000px',
                        height:'85%'
                    });
                }
            });
        },
        //
        restful: function(){
            var pid = $('#pid').val();
            easy.open = {width:800,height:538};
            //初始化
            layui.table.render({
                elem: '#layui-table'
                ,url: window.location.href
                ,where: { pid:pid }
                ,even: true
                ,cols: [[
                    {type: 'checkbox'}
                    ,{field:'id', 		title: '编号', width:70, align: 'center', unresize:true}
                    ,{field:'parent_id',title: 'PID', width:70, align: 'center', unresize:true}
                    ,{field:'listorder',title: '排序', width:70, align: 'center', edit: 'text',unresize:true}
                    ,{field:'fontico', 	title: '图标', width:70, align: 'center', templet:function(d){return '<i class="iconfont '+d.fontico+'"></i>'},unresize:true}
                    ,{field:'title', 	title: '标题', width:150,unresize:true,edit: 'text'}
                    ,{field:'name', 	title: '规则', unresize:true}
                    ,{field:'status',	title: '显示', width:75, align: 'center', templet:function(d){ return easy.switchstatus(d); },unresize:true}
                    ,{field:'toolbar', 	title: '操作', width:120,templet:function(d){ return easy.toolbar(d); }}
                ]]
            });
            //设置不需要分页
            layui.table.set({page:false})
        },
        //编辑
        edit: function(){
            var fontico = $('#fontico').val() || 'icon-roundcheck';
            //
            $('#fontico').parent()
                .css('position','relative')
                .prepend('<a class="layui-btn select-ico" title="选择图标" id="select-ico"><i id="show-iconfont" class="iconfont '+fontico+'"></i></a>');
            //选择图标
            $("#easy-form").on('click','.select-ico', function(){
                var html = $('#iconlist-box').html();
                //页面层
                layer.open({
                    title:'选择图标',
                    type: 1,
                    skin: 'easy-layer',
                    shade: 0.1,
                    area: ['514px', '300px'], //宽高
                    content: '<div class="iconlist-box">'+html+'</div>',
                    success: function(layero, index){
                        $(".iconlist-box").on('click','li', function(){
                            var text = $(this).attr('data-ico');
                            $('#fontico').val(text);
                            $('#show-iconfont').attr('class','').addClass('iconfont  '+text);
                            layer.close(index);
                        })
                    }
                });
            });
        }
    };
		
	//输出接口
	exports('auth_rule',obj);
});