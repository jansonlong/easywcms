<style>
    .layui-form-pane .layui-form-label{width:150px;}
    .layui-form-pane .layui-input-block{margin-left:150px;}
</style>
<form id="easy-form" class="layui-form layui-form-pane easy-form-main" action="" method="post">
<!--
    <blockquote class="layui-elem-quote" style="margin: 10px 0 10px 0;">
        <strong>插件配置信息</strong><br>
        标识：{$addon_info.name}<br>
        名称：{$addon_info.title}<br>
        描述：{$addon_info.description}<br>
        作者：{$addon_info.author}<br>
        版本：{$addon_info.version}<br>
        备注：{$addon_info.remarks}
    </blockquote> 
-->
    {volist name="$addon_config" id="r"}
    <?php $r['name'] = $key; ?>
    {:Form::input($r['type'],$r)}
    {/volist}
    <button lay-submit lay-filter="easy-form-install" id="submit" style=" display:none" ></button>
</form>