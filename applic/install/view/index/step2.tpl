<blockquote class="layui-elem-quote">运行环境检查</blockquote>
<table class="layui-table">
  <colgroup>
    <col width="200">
    <col width="200">
    <col>
  </colgroup>
    </colgroup>
    <thead>
        <tr>
            <th>项目</th>
            <th>所需配置</th>
            <th>当前配置</th>
        </tr>
    </thead>
    <tbody>
    {volist name="env" id="item"}
    <tr>
        <td>{$item[0]}</td>
        <td>{$item[1]}</td>
        <td><i class="layui-icon layui-icon-{$item[4]}">&nbsp;</i>{$item[3]}</td>
    </tr>
    {/volist}
    </tbody>
</table>

<blockquote class="layui-elem-quote">函数依赖性检查</blockquote>
<table class="layui-table">
  <colgroup>
    <col width="200">
    <col >
  </colgroup>
    </colgroup>
    <thead>
        <tr>
            <th>函数名称</th>
            <th>检查结果</th>
        </tr>
    </thead>
    <tbody>
    {volist name="func" id="item"}
    <tr>
        <td>{$item[0]}()</td>
        <td><i class="layui-icon layui-icon-{$item[2]}">&nbsp;</i>{$item[1]}</td>
    </tr>
    {/volist}
    </tbody>
</table>
<script>
easy.define(function(){
    
});
</script>