layui.define(function(exports){
	
	"use strict";
	
	easy.open = {title:false, width:540,height:400,offset:120};

	//输出接口
	exports('config_skin',{
        index: function(){
            layui.table.render({
                elem: "#layui-table"
                ,url: easy.url.index
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'],
                  limits: [easy.config.page_limit, 30, 50, 100]
                }
                ,limit: easy.config.page_limit
                ,even: true
                ,cols: [[
                    {field:'id', 			title: '编号', width:62,align: 'center',unresize:true}
                    ,{field:'skin_name', 	title: '名称', width:120,align: 'left',unresize:true}
                    ,{field:'skin_data', 	title: '配色方案',templet:'#skindataTpl',unresize:false}
                    ,{field:'create_time',	title: '创建时间', width:150,align: 'center', templet:'#createtimeTpl',unresize:true}
                    ,{field:'set', 			title: '设置', width:90, align: 'center',toolbar:'#setTpl',unresize:true}
                    ,{field:'toolbar', 		title: '按钮', width:120,templet:function(d){ return easy.toolbar(d); },unresize:true}
                ]]
            });

            //监听按钮
            layui.table.on('tool', function(obj){
                //修改
                if(obj.event === 'edit'){
                    easy.edit({ param:{id:obj.data.id} });
                //删除
                }else if(obj.event === 'delete'){
                    easy.delete({obj:obj, content:'确认删除吗?', param:{id:obj.data.id} });
                //使用
                }else if(obj.event === 'use'){
                    if(obj.data.status==0){
                        return artError('关闭的状态下不可使用');
                    }
                    var href_value = easy.config.skinpath + obj.data.skin_name +'.css?t='+ (new Date()).getTime();
                    $('#skinlink', window.parent.document).attr('href',href_value);
                    $('#console-iframe-list', window.parent.document).find('iframe').contents().find('#skinlink').attr('href',href_value);
                    localStorage.setItem('skinname',obj.data.skin_name);
                }
            });
        }
    });
});