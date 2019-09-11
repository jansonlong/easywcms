layui.define(function(exports){
	
	"use strict";
	
	easy.open = {width:680,height:443};

    var obj = {
        //列表页
        index: function(){
            //方法级渲染
            layui.table.render({
                elem: '#layui-table'
                ,url: easy.url.index
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'], //自定义分页布局
                  limits:[easy.config.page_limit, 30, 50, 100]
                }
                ,limit:easy.config.page_limit
                ,even: true
                ,cols: [[
                     {field:'id', 			title: '编号', width:62,align: 'center',unresize:true}
                    ,{field:'username', 	title: '账号', width:100,unresize:true}
                    ,{field:'realname', 	title: '姓名', width:100,unresize:true}
                    ,{field:'group_name',	title: '角色组', width:100,templet:function(d){return d.auth_role?d.auth_role.title:'未设置'},unresize:true}
                    ,{field:'description', 	title: '描述',unresize:false,minWidth:200}
                    ,{field:'login_count',	title: '登录次数', width:95,align: 'center',unresize:true}
                    ,{field:'lastlogintime',title: '最后登录时间', width:150,align: 'center',unresize:true}
                    ,{field:'status',		title: '状态',width:70, align: 'center',templet:function(d){ return easy.status(d); },unresize:true}
                    ,{field:'logbut', 	title: '日志/权限', width:120, toolbar:'#logTpl',unresize:true}
                    ,{field:'toolbar', 	title: '按钮', width:120,templet:function(d){ return easy.toolbar(d); },unresize:true}
                ]]
            });
            //监听按钮
            layui.table.on('tool(layui-table)', function(obj){
                var data = obj.data;
                switch(obj.event)
                {
                    case 'logs'://日志
                        easy.iframe({
                            title : '查看 ['+data.realname+'] 的日志',
                            url	  : easy.url.base + 'logs',
                            param : {uid:data.id}
                        });
                    break;
                    case 'auth'://权限
                        easy.edit({
                            title : '设置 ['+data.realname+'] 的权限',
                            url	  : easy.url.base + 'setauth',
                            param : {uid:data.id},
                            open  : {width:720,height:640}
                        });
                    break;
                }
            });
        },
        //个人信息
        personal: function(){
            //方法级渲染
            layui.table.render({
                elem: '#layui-table'
                ,url: easy.url.base + easy.config.action
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'], //自定义分页布局
                  limits:[12, 20, 50, 100]
                }
                ,limit:12
                ,even: true
                ,cols: [[
                     {field:'id', 			title: '编号', width:100,align: 'center',unresize:true}
                    ,{field:'type', 		title: '请求类型', width:100,align: 'center',unresize:true}
                    ,{field:'url', 			title: '链接', unresize:true}
                    ,{field:'ip',			title: 'IP', width:130,unresize:true}
                    ,{field:'create_time',	title: '时间', width:150,align: 'center',unresize:true}
                ]]
            });
        },
        //设置权限
        setauth: function(){
            //设定模块别名
            layui.extend({
                authtree: '{/}'+easy.config.assets+'extends/authtree'
            });
            //layui初始化
            layui.use('authtree', function(){

                var trees = layui.authtree.listConvert(authlist, {
                    primaryKey: 'id'
                    ,startPid: 0
                    ,parentKey: 'parent_id'
                    ,nameKey: 'title'
                    ,valueKey: 'id'
                    ,checkedKey: rulesdata
                });

                layui.authtree.render('#easy-auth-tree', trees, {
                    inputname: 'rules[]', 
                    layfilter: 'lay-check-auth', 
                    autowidth: false,
                    openall:true
                });

            });
            //全选
            $('#checkAll').click(function(){
                layui.use(['layer', 'authtree'], function(){
                    var layer = layui.layer;
                    var authtree = layui.authtree;
                    authtree.checkAll('#easy-auth-tree');
                });
            });
            //全不选
            $('#uncheckAll').click(function(){
                layui.use(['layer', 'authtree'], function(){
                    var layer = layui.layer;
                    var authtree = layui.authtree;
                    authtree.uncheckAll('#easy-auth-tree');
                });
            });
        },
        //日志
        logs: function(){
            var uid = $('#uid').val();
            //方法级渲染
            layui.table.render({
                elem: '#layui-table'
                ,url: easy.url.base + 'logs'
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'], //自定义分页布局
                  limits:[easy.config.page_limit, 30, 50, 100]
                }
                ,limit:easy.config.page_limit
                ,where:{ uid: uid }
                ,even: true
                ,cols: [[
                     {field:'id', 			title: '编号', width:100,align: 'center',unresize:true}
                    ,{field:'type', 		title: '请求类型', width:100,align: 'center',unresize:true}
                    ,{field:'url', 			title: '链接', unresize:true}
                    ,{field:'param', 		title: '参数', unresize:true}
                    ,{field:'ip',			title: 'IP', width:130,unresize:true}
                    ,{field:'create_time',	title: '时间', width:150,align: 'center',unresize:true}
                ]]
            });
        }
    }

	//输出接口
	exports('auth_sysadm',obj);
});