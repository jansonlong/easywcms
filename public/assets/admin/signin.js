//刷新验证码
function resetVerifyCode(){
	var verifyImage = $('#verifyImage');
	verifyImage.attr('src',verifyImage.attr('src') );
	$('#verifyCode').val('').focus();
}

layui.define(function(exports){
	
	"use strict";
		
    var obj = {
        index: function(){
            
            //input焦点
            $('.click').click(function(){
                $(this).find('input').focus()
            })

            //触发登录
            $('#verifyCode').keydown(function(){
                if(event.keyCode==13){
                    $(this).blur();
                    $('.loginSub').click()
                }
            });

            $('.loginSub').click(function(){
                var isnull = false;
                $('.login input').each(function() {
                    if( !$(this).val() ){
                        $(this).focus();
                        $('.msg').html('请输入'+$(this).attr('msg'))
                        isnull = true;
                        return false;
                    }
                });
                if(isnull){ return false; }
                $(this).attr('disabled','disabled').addClass('disabled');
                $('.msg').html('正在处理，请稍后...');
                //提交请求
                $.ajax({
                    url: $(this).attr('post-url') ,
                    type: "POST",
                    timeout:"4000",
                    dataType: "JSON",
                    data: {
                        username : $('#username').val(),
                        password : $('#password').val(),
                        verifyCode:$('#verifyCode').val()
                    },
                    success: function(data){
                        if (data && data.code==1){
                            $('.msg').html('')
                            layer.msg(data.msg||'登录成功', {icon: 1,shade: 0.01,time:2000},function(){
                                window.location.href = data.url;
                            });
                        }else{
                            $('#verifyCode').val('');
                            resetVerifyCode();
                            if(data && data.msg){
                                $('.msg').html(data.msg)
                            }else{
                                $('.msg').html('发生未知错误，请刷新重试')
                            } 
                            $('.loginSub').removeAttr('disabled').removeClass('disabled');
                        }
                    },
                    error:function(e){
                        $('.msg').html('操作失败,请重试')
                        $('.loginSub').removeAttr('disabled').removeClass('disabled');
                    }
                });
            });
        }
    };
	
	//输出接口
	exports('signin',obj);
});


