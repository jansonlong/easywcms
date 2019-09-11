<style>
.easy-admin-backlog .easy-admin-backlog-body { display: block; background-color: rgb(248, 248, 248); color: rgb(153, 153, 153); padding: 10px 15px; border-radius: 2px; transition: all 0.3s; }
    .easy-admin-backlog cite{ font-size:16px; color: #333; font-weight: bold; line-height: 30px;}
    .layui-elem-quote{ margin-bottom: 0;}
    .layui-elem-quote span{ font-weight: bold; }
    .layuiadmin-big-font{ font-weight: bold;}
</style>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm12 layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <blockquote class="layui-elem-quote">您好， <span>{$userinfo['realname']}</span>！ 欢迎回到EasyWcms控制台，您已登录本系统 {$userinfo['login_count']+1} 次。</blockquote>
                </div>
            </div>
        </div>
        <div class="layui-col-sm12 layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <blockquote class="layui-elem-quote  layui-quote-nm"><strong>EasyWcms</strong> 是一款简单而高效的WEB应用内容管理系统，她将会是您轻松建站的首选利器。<br>简易的功能扩展、强大的插件模块、助您高效二次开发实现需要的功能、帮助企业及开发者降低二次开发的成本。</blockquote>
                </div>
            </div>
        </div>
        
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">管理员 </div>
                <div class="layui-card-body  ">
                    <p class="layuiadmin-big-font"><span class="layui-badge layui-bg-green layuiadmin-badge">{$sysadm_conut} 人</span></p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">菜单数</div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font"><span class="layui-badge layui-bg-blue layuiadmin-badge">{$menu_conut} 个</span></p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">皮肤数</div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font"><span class="layui-badge layui-bg-red layuiadmin-badge">{$skin_conut} 个</span></p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">插件数</div>
                <div class="layui-card-body ">
                    <p class="layuiadmin-big-font"><span class="layui-badge layui-bg-cyan layuiadmin-badge">{$addons_conut} 个</span></p>
                </div>
            </div>
        </div>
        
        <div class="layui-col-sm6 layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-header">系统信息</div>
                <div class="layui-card-body ">
                    <table class="layui-table">
                        <tbody>
                            <tr>
                                <th width="200">系统版本</th>
                                <td>{:config('app.easywcms_version')}</td>
                            </tr>
                            <tr>
                                <th>服务器地址</th>
                                <td>{$easy.config.domain}</td>
                            </tr>
                            <tr>
                                <th>当前用户IP</th>
                                <td>{$Think.server.remote_addr}</td>
                            </tr>
                            <tr>
                                <th>运行环境</th>
                                <td>{$Think.server.server_software}</td>
                            </tr>
                            <tr>
                                <th>PHP版本</th>
                                <td>{$Think.PHP_VERSION}</td>
                            </tr>
                            <tr>
                                <th>MYSQL版本</th>
                                <td>{:$MYSQL_VERSION}</td>
                            </tr>
                            <tr>
                                <th>ThinkPHP</th>
                                <td>{$Think.version}</td>
                            </tr>
                            <tr>
                                <th>调试模式</th>
                                <td>{$Think.config.app_debug ? '是':'否'}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="layui-col-sm6 layui-col-md6">
            <div class="layui-card">
                <div class="layui-card-header">开发者手册 </div>
                <div class="layui-card-body  ">
                    <table class="layui-table">
                        <tbody>
                            <tr>
                                <th width="200">EasyWcms</th>
                                <td><a href="https://www.kancloud.cn/easywcms/v_1_0/content" target="_blank">访问</a></td>
                            </tr>
                            <tr>
                                <th width="200">cms 插件</th>
                                <td><a href="https://www.kancloud.cn/easywcms/addon-cms/content" target="_blank">访问</a></td>
                            </tr>
                            <tr>
                                <th width="200">Thinkphp 5.1</th>
                                <td><a href="https://www.kancloud.cn/manual/thinkphp5_1/content" target="_blank">访问</a></td>
                            </tr>
                            <tr>
                                <th width="200">Layui v2.4.3</th>
                                <td><a href="https://www.layui.com/doc/" target="_blank">访问</a></td>
                            </tr>
                            <tr>
                                <th width="200">&nbsp;</th>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="layui-card-header">开发团队</div>
                <div class="layui-card-body ">
                    <table class="layui-table">
                        <tbody>
                            <tr>
                                <td>Janson</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="layui-col-sm12 layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">特别感谢</div>
                <div class="layui-card-body ">
                    ThinkPHP、Layui、Echarts、Jquery
                </div>
            </div>
        </div>
        
    </div>
</div>
