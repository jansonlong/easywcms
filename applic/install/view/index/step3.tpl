<blockquote class="layui-elem-quote">目录、文件权限检查</blockquote>
<table class="layui-table">
  <colgroup>
    <col width="200">
    <col width="200">
    <col>
  </colgroup>
    </colgroup>
    <thead>
        <tr>
            <th>目录/文件</th>
            <th>所需状态</th>
            <th>当前状态</th>
        </tr>
    </thead>
    <tbody>
    {volist name="dirfile" id="item"}
        <tr>
            <td>{$item[3]}</td>
            <td>可写</td>
            <td><i class="layui-icon layui-icon-{$item[2]}">&nbsp;</i>{$item[1]}</td>   
        </tr>
    {/volist}
    </tbody>
</table>
<script>
easy.define(function(){
    
});
</script>