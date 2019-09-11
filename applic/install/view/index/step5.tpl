<blockquote class="layui-elem-quote">安装进行中......</blockquote>
<div id="setting" style="line-height: 30px;"></div>
<script>
easy.define(function(){
    var set_ting = 0;
    var intervalId = false;
    $('#setting').append('正在导入数据库，请稍后......<br>');
    var i = 1;
    setTimeout(function(){
        Ajax(0,true);
        intervalId = setInterval(function(){
            if(set_ting==0){
                $('#setting').append('安装进行中，请勿刷新或关闭当前窗口......<br>');
            }else{
                clearInterval(intervalId);
            }
        },2500)
    },1000);
    //
    function Ajax(_setting){
        easy.ajax({
            hideloading:true,
            url:"{:url('/Install?step=5')}&setting="+_setting,
            data: {}
        },function(ret){
            if( ret.code==1 ){
                $('#setting').append(ret.msg||'');
                if(ret.setting){
                    set_ting = ret.setting
                    setTimeout(function(){ Ajax(ret.setting) },1000);
                }else if(ret.setting==0){
                    window.location.href = "{:url('/Install?step=6')}"
                }
            }else{
                clearInterval(intervalId);
                $('#setting').append(ret.msg||'');
            }
        });
    }
    
});
</script>