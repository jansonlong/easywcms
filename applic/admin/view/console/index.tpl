<link rel="stylesheet" type="text/css" href="{:config('param.assets')}admin/css/console.css?t={$timestamp}">
<div id="EASY_app">
    <div class="layui-layout easy-console">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav console-header-left">
                <li class="layui-nav-item easywcms-flexible" lay-unselect="">
                    <a easywcms-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="EASY_app_flexible"></i>
                    </a>
                </li>
                {volist name="mainmenu" id="data"}
                <li class="layui-nav-item" lay-unselect="" {if condition="$data.id==1"}class="layui-this"{/if}>
                    <a easywcms-event="showSubMenu" title="{$data.title}" data-id="{$data.id}">
                        <i class="iconfont {$data.fontico}"></i><span>{$data.title}</span>
                    </a>
                </li>
                {/volist}
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="easywcms-layout-right">
                <li class="layui-nav-item layui-hide-xs" lay-unselect="">
                    <a easywcms-event="theme" easy-url="{:url('/admin/console/selectskin')}">
                        <i class="iconfont icon-skin_light"></i><font>皮肤</font>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect="">
                    <a easywcms-event="upcache"> <i class="iconfont icon-punch_light"></i><font>缓存</font></a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect="">
                    <a easywcms-event="fullscreen"><i class="iconfont icon-full screen-full"></i><font>全屏</font></a>
                </li>
                <li class="layui-nav-item" lay-unselect=""> <a href="{:url('/admin/signin/safeexit')}" title="退出登录"> <i class="iconfont icon-exit"></i><font>退出</font> </a> </li>
            </ul>
        </div>
        <!-- 菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" style="background-image: url('{:config('system.logo')}')"></div>
                <div class="easy-user-box">
                    <i class="iconfont icon-my"></i>
                    <p> <span>{$userinfo['realname']}</span>
                        <font>[{$userinfo['auth_title']}]</font>
                    </p>
                </div>
                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="easywcms-system-side-menu"></ul>
                <p class="easy-nav-tit">相关链接</p>
                <ul class="layui-nav layui-nav-tree" >
                    <li class="layui-nav-item" lay-unselect="unselect">
                        <a href="https://www.kancloud.cn/easywcms/v_1_0/content" target="_blank">
                            <i class="iconfont icon-form_light" style="color: red"></i> <cite>官方文档</cite>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 标签 -->
        <div class="easywcms-pagetabs" id="EASY_app_tabs">
            <div class="layui-icon easywcms-tabs-control layui-icon-prev" easywcms-event="leftPage"></div>
            <div class="layui-icon easywcms-tabs-control layui-icon-next" easywcms-event="rightPage"></div>
            <div class="easywcms-tabs-control layui-but-refresh" lay-unselect="" easywcms-event="refresh">
                <a title="刷新"><i class="layui-icon layui-icon-refresh-3"></i></a>
            </div>
            <div class="layui-tab" lay-unauto="" lay-allowclose="true" lay-filter="easywcms-layout-tabs">
                <ul class="layui-tab-title" id="EASY_app_tabsheader"></ul>
            </div>
        </div>
        <!-- 内容 -->
        <div class="layui-body" id="EASY_app_body"><div style=" padding: 10px">正在加载...</div></div>
        <div class="easywcms-body-shade" easywcms-event="shade"></div>
    </div>
</div>
<script id="demo" type="text/html">
{{# layui.each(d, function(index, item){ }}
<li data-name="{{ index }}" class="layui-nav-item" lay-unselect="">
  <a easy-href="{{ item.url }}" lay-tips="{{ item.title }}" easy-type="menu" easy-id="{{ item.id }}" easy-pid="{{ item.parent_id }}">
    <i class="iconfont {{ item.fontico }}"></i>
    <cite>{{ item.title }}</cite>
  </a>
</li>
{{#  }); }}
{{#  if(d.length === 0){ }}
<p class=tis>暂无数据</p>
{{#  } }} 
</script>
<script>
var submenu = {:json_encode($submenu,JSON_UNESCAPED_UNICODE)};
</script>