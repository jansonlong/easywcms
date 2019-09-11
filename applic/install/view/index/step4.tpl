<form id="easy-forms" class="layui-form layui-form-pane layui-tab-content easy-form-main">
    
    <blockquote class="layui-elem-quote">填写数据库信息</blockquote>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库类型</label>
        <div class="layui-input-inline">
            <input type="text" name="type" placeholder="请输入" autocomplete="off" class="layui-input" value="mysql" disabled>
        </div>
        <div class="layui-form-mid layui-word-aux">目前仅限mysql</div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库服务器</label>
        <div class="layui-input-inline">
            <input type="text" name="hostname" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['hostname']?:'127.0.0.1'}">
        </div>
        <div class="layui-form-mid layui-word-aux">数据库服务器，数据库服务器IP，一般为127.0.0.1</div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库端口号</label>
        <div class="layui-input-inline">
            <input type="text" name="hostport" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['hostport']?:'3306'}">
        </div>
        <div class="layui-form-mid layui-word-aux">数据库端口号，一般为3306</div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库名称</label>
        <div class="layui-input-inline">
            <input type="text" name="database" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['database']?:''}">
        </div>
        <div class="layui-form-mid layui-word-aux"></div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库帐号</label>
        <div class="layui-input-inline">
            <input type="text" name="username" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['username']?:''}">
        </div>
        <div class="layui-form-mid layui-word-aux"></div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据库密码</label>
        <div class="layui-input-inline">
            <input type="text" name="password" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['password']?:''}">
        </div>
        <div class="layui-form-mid layui-word-aux"></div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">数据表前缀</label>
        <div class="layui-input-inline">
            <input type="text" name="prefix" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['prefix']?:'easy_'}">
        </div>
        <div class="layui-form-mid layui-word-aux"> 数据表前缀，同一个数据库运行多个系统时请修改为不同的前缀</div>
    </div>
    
    <div class="layui-form-item" pane="" style="max-width: 350px;">
        <label class="layui-form-label">数据库字符集</label>
        <div class="layui-input-block">
            <input type="radio" name="charset" value="utf8" title="utf8" {$data['charset']=='utf8'||$data['charset']==''?'checked':''}>
            <input type="radio" name="charset" value="GBK" title="GBK"  {$data['charset']=='GBK'?'checked':''}>
        </div>
    </div>
    
    <blockquote class="layui-elem-quote">管理员账户</blockquote>
    
    <div class="layui-form-item">
        <label class="layui-form-label">账户</label>
        <div class="layui-input-inline">
            <input type="text" name="adminuser" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['adminuser']?:'admin'}">
        </div>
        <div class="layui-form-mid layui-word-aux">用于登录后台管理的账户</div>
    </div>
    
    <div class="layui-form-item">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-inline">
            <input type="text" name="adminpwd" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" value="{$data['adminpwd']?:''}">
        </div>
        <div class="layui-form-mid layui-word-aux">用于登录后台管理的密码</div>
    </div>
    
    <button lay-submit lay-filter="easy-form" id="submit" style="display: none;"></button>
    
</form>
<script>
easy.define(function(){
    
    //重新定义下一步按钮
    var next = $('#anext').attr('href');
    
    $('#anext').removeAttr('href').click(function(){
        $('#submit').click();
    });
    
    layui.form.on('submit(easy-form)', function(data){
        easy.ajax({ 
            url:"{:url('/Install?step=4')}",
            data:data.field
        },function(ret){
            if(ret.code===1){
                window.location.href = next;
            }else{
                layer.alert(ret.msg, {
                    title:'错误',
                    skin: 'layui-layer-lan',
                    closeBtn: 0
                });
            }
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });
});
</script>