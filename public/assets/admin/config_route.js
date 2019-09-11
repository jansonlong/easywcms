layui.define(function(exports){
	
	"use strict";
	
	//设置弹窗大小
	easy.open = {width:460,height:550}
		
	//输出接口
	exports('config_route',{
        //列表页
        index: function(){
            
            //方法级渲染
            layui.table.render({
                elem: '#layui-table'
                ,url: easy.url.index
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'], //自定义分页布局
                  limits: [easy.config.page_limit, 20, 50, 100]
                }
                ,limit: easy.config.page_limit
                ,cols: [[
                    //{width:49,checkbox: true}
                    //,{field:'id', 			title: '编号', width:62,align: 'center',unresize:true}
                    {field:'title', 		title: '名称', width:180,unresize:true}
                    ,{field:'type',			title: '类型', width:80,align: 'center',unresize:true}
                    ,{field:'group',			title: '分组', width:80,align: 'center',unresize:true}
                    ,{field:'expression', 	title: '路由表达式',width:200, align: 'left',unresize:true}
                    ,{field:'address', 		title: '路由地址',width:200, unresize:false}
                    ,{field:'remarks', 		title: '备注',unresize:false}
                    //,{field:'status',  		title: '状态',width:90, align: 'center', templet:function(d){return easy.switchstatus(d)},unresize:true}
                    ,{field:'edit',         title: '按钮',width:120, align: 'center',templet:function(d){ return easy.toolbar(d)},unresize:true}
                ]]
                ,id: 'layui-table'
                ,even: true
            });
        }
    });
});