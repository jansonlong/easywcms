layui.define(function(exports){
	
	"use strict";
	
	easy.open = {width:800,height:'80%'};
	
	var obj = {
        index: function(){
            layui.table.render({
                elem: '#layui-table'
                ,url: easy.url.index
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'],
                  limits:[easy.config.page_limit, 30, 50, 100]
                }
                ,limit:easy.config.page_limit
                ,even: true
                ,cols: [[
                    {field:'id', 			title: '编号', width:62,align: 'center',unresize:true}
                    ,{field:'title', 		title: '角色组名称', width:130,align: 'left',unresize:true}
                    ,{field:'description', 	title: '描述',unresize:false}
                    ,{field:'create_time',	title: '创建时间',width:150, align: 'center',unresize:true}
                    ,{field:'toolbar', 	title: '按钮', width:120,templet:function(d){ return easy.toolbar(d); },unresize:true}
                ]]
            });

            //监听按钮
            layui.table.on('tool', function(obj){
                var data = obj.data;
                //编辑
                if(obj.event === 'edit'){
                    easy.edit({
                        title	: '设置 ['+data.title+'] 的权限',
                        param	: {id:data.id}
                    });
                //删除
                }else if(obj.event === 'delete'){
                    easy.delete({ obj:obj, param:{id:obj.data.id} });
                }
            });

            //添加/修改角色组
            function addrole(data){
                var dataid = (data && data.id) ? data.id : 0;
                var $title = dataid ? '修改角色组' : '添加角色组';
                var action = dataid ? 'edit' : 'add';
                if(!dataid){
                    data = {title:'',description:''};
                }
                $('#roletitle').html('<input type="text" name="title" autocomplete="off" placeholder="请输入角色组名称" class="layui-input" value="'+data.title+'">');
                $('#roledescription').html('<input type="text" name="description" autocomplete="off" placeholder="请输入角色组的描述" class="layui-input" value="'+data.description+'">');
                //创建窗口
                var index = layer.open({
                    title: $title, 
                    type: 1, 
                    offset:50,
                    shade:0.1,
                    skin: 'easy-layer',
                    area: ['420px', '224px'], //宽高
                    content: $('#addRoleFormHtml').html(),
                });
                //监听submit提交
                layui.form.on('submit(addRoleForm)', function(data){
                    if(dataid) data.field.id = dataid;
                    easy.ajax({url:easy.url.base+action, param:data.field},function(ret){
                        if(ret.code == 1){
                            layer.close(index);
                            layui.table.reload('layui-table');
                            easy.success(ret.msg||false);
                        }else{
                            easy.error(ret.msg||false);
                        }
                    });
                    return false;
                });
                return true;
            };
        },
        //编辑
        edit: function(){
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
        //添加
        add: function(){
            obj.edit();
        }
    }

	//输出接口
	exports('auth_role',obj);
});

