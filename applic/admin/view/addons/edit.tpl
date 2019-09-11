<style>
    .layui-form-pane .layui-form-label{width:150px;}
    .layui-form-pane .layui-input-block{margin-left:150px;}
</style>
<form id="easy-form" class="layui-form layui-form-pane easy-form-main" action="" method="post">
    <blockquote class="layui-elem-quote" style="margin: 10px 0 10px 0;">插件配置</blockquote> 
    {volist name="$addon_config" id="r"}
    <?php $r['name'] = $key; ?>
    {:Form::input($r['type'],$r)}
    {/volist}
    <button lay-submit lay-filter="easy-form" id="submit" style=" display:none" ></button>
</form>
<script>
easy.define(function(){})
</script>