<a class="layui-btn easy-btn-tablereload" title="刷新列表"><i class="layui-icon">&#xe669;</i></a>
{if (isset($submenu))}
{volist name="submenu" id="sm"}
<a class="layui-btn {$sm.btnclass|default='layui-btn-primary layui-btn-'.$sm.id}" data-url='{$sm.url}' >
{if ($sm['fontico'])}<i class="iconfont {$sm.fontico}"></i>{/if}<font>{$sm.title|default=''}</font>
</a>
{/volist}
{/if}