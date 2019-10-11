var top_location = window.top.location;
var online_install = false;
var buystorewin = false;
var loginstorewin = false;
layui.define(function(exports){
	
	"use strict";
	
	//设置弹窗大小
	easy.open = {width:460,height:550}
		
	//输出接口
	exports('addons',{
        //列表页
        index: function(){
            //弹窗配置
            easy.open = {width:760,height:600};
            
            var options = {
                elem: '#layui-table'
                ,url: easy.url.index
                ,page: {
                  layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'], //自定义分页布局
                  limits: [easy.config.page_limit, 30, 50, 100]
                }
                ,limit: easy.config.page_limit
                ,even: true
            };
            
            //已安装
            function type_1(){
                var param = options;
                param['url'] = easy.url.index;
                param['cols'] = [[
                    {field:'title',	 title: '名称', width:240, unresize:true},
                    {field:'description',title: '描述', unresize:true},
                    {field:'author',	 title: '作者',width:100, align: 'center', unresize:true},
                    {field:'version',	 title: '版本',width:100, align: 'center', unresize:true},
                    {field:'create_time',title: '安装时间',width:150, align: 'center', unresize:true},
                    {field:'edit', 		title: '按钮', width:120, toolbar:'#editTpl',unresize:true}
                ]];
                param['where'] = {type:1};
                //方法级渲染
                layui.table.render(param);
            }
            type_1();
            
            //待安装
            function type_2(){
                var param = options;
                param['url'] = easy.url.index;
                param['cols'] = [[
                    {field:'title',	 title: '名称', width:240, unresize:true},
                    {field:'description',title: '描述', unresize:true},
                    {field:'author',	 title: '作者',width:100, align: 'center', unresize:true},
                    {field:'version',	 title: '版本',width:100, align: 'center', unresize:true},
                    {field:'edit', 		title: '按钮', width:120, toolbar:'#editTpl',unresize:true}
                ]];
                param['where'] = {type:2};
                //方法级渲染
                layui.table.render(param);
            }
            
            //在线安装
            function type_3(){
                var param = options;
                //请求链接
                param['url'] = store_url;
                param['cols'] = [[
                    {field:'title',	 title: '名称', width:240, unresize:true},
                    {field:'description',title: '描述', unresize:true},
                    {field:'price_txt',	    title: '价格(元)',width:100, align: 'center', unresize:true},
                    {field:'author',	 title: '作者',width:100, align: 'center', unresize:true},
                    {field:'version',	 title: '版本',width:100, align: 'center', unresize:true},
                    {field:'edit', 		title: '按钮', width:120, toolbar:'#editTpl3',unresize:true}
                ]];
                param['where'] = {};
                //方法级渲染
                layui.table.render(param);
            }
            
            //Tab切换
            layui.element.on('tab(addonstype)', function(data){
                var type = $(this).attr('data-type');
                eval('type_'+type+'()');
            });

            //自定义操作按钮
            layui.table.on('tool(layui-table)', function(obj){
                //安装
                if(obj.event === 'install'){
                    easy.edit({
                        title : '安装插件 ['+obj.data.title+']',
                        url : easy.url.base + 'install',
                        param : {name:obj.data.name},
                        width:'1000px',
                        height:'85%',
                        btn:['执行安装','刷新']
                    });
                }else if(obj.event === 'uninstall'){
                    easy.delete({
                        url:easy.url.base + 'uninstall',
                        content:'你确定要卸载此插件吗? 卸载会影响现有的业务哦.',
                        param: {name:obj.data.name},
                    },function(res){
                        var layer = window.top.layer;
                        //弹出提示
                        layer.confirm(res.msg||'提示', {
                            skin: 'easy-layer',
                            offset: 60,
                            btn: ['刷新','稍后手动刷新'] //按钮
                        }, function(){
                            top_location.reload()
                        }, function(){});
                    });
                //下载并安装
                }else if(obj.event === 'online_install'){
                    online_install(obj);
                }
            });
            
            //删除
            $('body').on("click",'.my-purchased',function(){
                var _this = $(this);
                var obj  = new Object;
                obj.data = new Object;
                obj.data.title = _this.attr('data-title');
                obj.data.name  = _this.attr('data-name');
                layer.close(buystorewin);
                //
                //验证用户是否有购买记录
                easy.ajax({
                    url:easy.url.index + '?type=online_install',
                    param: { name:obj.data.name},
                    loading:{text:'正在验证，请稍候...'},
                },function(ret){
                    //重新登录
                    if( ret.code==40000 ){
                        easy.error('请重新登录');
                        online_install(obj);
                    //打开购买界面
                    }else if( ret.code == 40003 ){
                        layer.confirm(ret.msg||'提示', {
                            anim: -1,isOutAnim :false,
                            skin: 'easy-layer',
                            offset: 60,
                            btn: ['好的'] //按钮
                        }, function(index){
                            layer.close(index);
                            open_buy(obj.data,ret.content);
                        });
                    }else if( ret.code == 1 ){
                        open_install(obj.data);
                    }else{
                        alert(ret.msg)
                    }
                });
            });
            
            //下载并安装
            online_install = function (obj){
                easy.ajax({
                    url:easy.url.index + '?type=online_install',
                    param: {name:obj.data.name},
                    loading:{text:'正在下载插件...'},
                },function(ret){
                    if( ret.code==1 ){
                        open_install(obj.data);
                    //登录store账号
                    }else if( ret.code==40000 ){
                        //创建窗口
                        loginstorewin = layer.open({
                            id:'login-store',
                            isOutAnim :false,
                            title:'登录 Store', type: 1, offset:80,shade:0.1,
                            skin: 'easy-layer',
                            area: ['480px', '320px'], //宽高
                            content: $('#login-store-html').html()
                        });
                        //监听submit提交
                        layui.form.on('submit(login-store-form)', function(data){
                            layer.msg('正在登录...', {icon: 16,shade: 0.15,time:0});
                            $.ajax({
                                url:easy.url.index + '?type=login',
                                type: "POST", 
                                data: data.field,
                                success:function(ret){
                                    layer.closeAll('dialog');
                                    if(ret.code == 1){
                                        layer.close(loginstorewin);
                                        //验证用户是否有购买记录
                                        easy.ajax({
                                            url:easy.url.index + '?type=online_install',
                                            param: {name:obj.data.name},
                                            loading:{text:'正在处理...'},
                                        },function(ret){
                                            //打开购买界面
                                            if( ret.code == 40003 ){
                                                open_buy(obj.data,ret.content);
                                            }else if( ret.code == 1 ){
                                                open_install(obj.data);
                                            }else{
                                                alert(ret.msg)
                                            }
                                        });
                                    }else{
                                        easy.error(ret.msg||false);
                                    }
                                },
                                error:function(){
                                    layer.close(loginstorewin);
                                },
                                complete:function(){
                                    layer.closeAll('dialog');
                                }
                            });
                            return false;
                        });
                    }else if( ret.code==40003 ){
                             open_buy(obj.data,ret.content);
                    //弹出提示
                    }else{
                       window.top.layer.confirm(ret.msg||'提示', {
                            anim: -1,isOutAnim :false,
                            skin: 'easy-layer',
                            offset: 60,
                            btn: ['好的'] //按钮
                        }, function(index){
                            window.top.layer.close(index);
                        }); 
                    }
                });
            }
            
            //创建安装插件窗口
            function open_install(obj){
                easy.edit({
                    title : '安装插件 ['+obj.title+']',
                    url : easy.url.base + 'install',
                    param : {name:obj.name},
                    width:'1000px',
                    height:'85%',
                    btn:['执行安装','刷新']
                });
            }
            
            //创建窗口
            function open_buy(obj,content){
                buystorewin = layer.open({
                    id:'buy-store',
                    anim: -1,isOutAnim :false,
                    isOutAnim :false,
                    title:'购买插件', type: 1, offset:80,shade:0.1,
                    skin: 'easy-layer',
                    area: ['480px', '350px'], //宽高
                    content: content
                });
                //监听submit提交
                layui.form.on('submit(buy-store-form)', function(data){
                    layer.msg('正在验证...请稍后！', {icon: 16,shade: 0.15,time:0});
                    $.ajax({
                        url:easy.url.index + '?type=check_kami',
                        type: "POST", 
                        data: data.field,
                        success:function(ret){
                            layer.closeAll('dialog');
                            if(ret.code == 1){
                                layer.close(buystorewin);
                                open_install(obj);
                            }else{
                                window.top.layer.confirm(ret.msg||'提示', {
                                    anim: -1,isOutAnim :false,
                                    skin: 'easy-layer',
                                    offset: 60,
                                    btn: ['好的'] //按钮
                                }, function(index){
                                    window.top.layer.close(index);
                                }); 
                            }
                        },
                        error:function(){
                            //layer.close(loginstorewin);
                        },
                        complete:function(){
                            layer.closeAll('dialog');
                        }
                    });
                    return false;
                });
            }
        },
        //
        install: function(){
            //监听submit提交
            layui.form.on('submit(easy-form-install)', function(data){
                return easy.formsubmit(data,function(res){
                    var layer = window.top.layer;
                    //弹出提示
                    layer.confirm(res.msg||'提示', {
                        anim: -1,isOutAnim :false,
                        skin: 'easy-layer',
                        offset: 60,
                        btn: ['刷新','稍后手动刷新'] //按钮
                    }, function(){
                        top_location.reload()
                    }, function(){});
                });
            });
        }
    });
});