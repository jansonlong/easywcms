layui.define(['element'],function(exports){
	
	"use strict";
    
    var obj = {
        index: function(){
            //设置选项卡
            $('#ContentListForm .group_param').each(function(){
                var that = $(this);
                if( that.attr('data-id') && that.attr('data-title') ){
                    $('#addGroupBut').before('<li lay-id='+that.attr('data-id')+'>'+that.attr('data-title')+'</li>')
                }
            });

            //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
            var layid = location.hash.replace(/^#layuitab=/, '') || 1;
            layui.element.tabChange('layuitab', layid);

            //监听Tab切换，以改变地址hash值
            layui.element.on('tab(layuitab)', function(){
                location.hash = 'layuitab='+ this.getAttribute('lay-id');
            });

            //监听submit提交
            layui.form.on('submit(MainForm)', function(data){
                return easy.formsubmit(data,function(res){
                    if( res.url ) {
                        window.top.layer.msg('系统正在更新缓存，请稍候...', {icon: 16,shade: 0.1,time:3000},function(){
                            window.top.location.href = res.url;
                        });				
                    }
                    if(data.field['config[group_id]']) {
                        location.hash = 'layuitab='+ data.field['config[group_id]'];
                        window.location.reload();
                    }
                });
            });
            //添加分组
            $('#addGroup').click(function(){
                //创建窗口
                var addgroupwin = layer.open({
                    id:'add_GroupForm',
                    title:'添加分组', type: 1, offset:50,shade:0.1,
                    skin: 'easy-layer',
                    area: ['480px', '280px'], //宽高
                    content: $('#addGroupFormHtml').html(),
                });
                //监听submit提交
                layui.form.on('submit(addGroupForm)', function(data){
                    layer.msg('正在处理...', {icon: 16,shade: 0.01});
                    $.ajax({
                        url : easy.url.base + 'addGroup',
                        type: "POST", 
                        data: data.field,
                        success:function(ret){
                            layer.closeAll('dialog');
                            if(ret.code == 1){
                                layer.close(addgroupwin);
                                easy.success(ret.msg||false);
                                $('select[name="config[group_id]"]').append('<option value="'+ret.data.id+'" selected >'+ret.data.title+'</option>');
                                //刷新select选择框渲染
                                layui.form.render('select');
                            }else{
                                easy.error(ret.msg||false);
                            }
                        },
                        error:function(){ easy.error(); }
                    });
                    return false;
                });
            });
        }
    };
	//输出接口
	exports('config_param',obj);
});