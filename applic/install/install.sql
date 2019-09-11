DROP TABLE IF EXISTS `__PREFIX__addons`;
CREATE TABLE IF NOT EXISTS `__PREFIX__addons` (
  `id` int(11) NOT NULL,
  `classify` tinyint(2) NOT NULL DEFAULT '0' COMMENT '分类',
  `name` varchar(20) DEFAULT NULL COMMENT '名称',
  `title` varchar(30) DEFAULT NULL COMMENT '标题',
  `description` varchar(150) DEFAULT NULL COMMENT '介绍',
  `author` char(20) DEFAULT NULL COMMENT '作者',
  `version` varchar(20) DEFAULT NULL COMMENT '版本',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `deleting` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0为不可删除',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `__PREFIX__addons` (`id`, `classify`, `name`, `title`, `description`, `author`, `version`, `status`, `deleting`, `create_time`, `update_time`) VALUES
(1, 0, 'baiduue', '百度编辑器', 'UEditor是百度的一个javascript编辑器的开源项目，能带给您良好的富文本使用体验', '官方', '1.0', 1, 0, 1534585999, 1564190558);

DROP TABLE IF EXISTS `__PREFIX__annex`;
CREATE TABLE IF NOT EXISTS `__PREFIX__annex` (
  `id` int(11) NOT NULL,
  `type` tinyint(1) DEFAULT '1' COMMENT '1本地2阿里云',
  `title` varchar(100) DEFAULT NULL COMMENT '文件原名称',
  `filename` varchar(100) DEFAULT NULL COMMENT '文件上传后的名称',
  `filepath` text COMMENT '文件绝对路径',
  `filesize` varchar(20) DEFAULT NULL COMMENT '文件大小(字节)',
  `ext` varchar(12) NOT NULL,
  `host` varchar(100) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `__PREFIX__auth_log`;
CREATE TABLE IF NOT EXISTS `__PREFIX__auth_log` (
  `id` mediumint(10) unsigned NOT NULL,
  `uid` smallint(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `type` char(10) DEFAULT NULL COMMENT '请求类型',
  `module` char(20) DEFAULT NULL COMMENT '所属模块',
  `controller` char(20) DEFAULT NULL COMMENT '控制器',
  `action` char(20) DEFAULT NULL COMMENT '方法',
  `url` varchar(200) DEFAULT NULL COMMENT '请求链接',
  `ip` char(20) DEFAULT NULL COMMENT 'ip地址',
  `param` text COMMENT '请求参数',
  `create_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='操作日志';

DROP TABLE IF EXISTS `__PREFIX__auth_role`;
CREATE TABLE IF NOT EXISTS `__PREFIX__auth_role` (
  `id` mediumint(8) unsigned NOT NULL,
  `title` char(20) DEFAULT NULL COMMENT '角色组名称',
  `description` varchar(80) DEFAULT NULL COMMENT '描述信息',
  `module` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '所属模块',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '组类型',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `deleting` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可删除',
  `rules` text COMMENT '拥有的权限',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='角色组数据表';

INSERT INTO `__PREFIX__auth_role` (`id`, `title`, `description`, `module`, `type`, `status`, `deleting`, `rules`, `create_time`, `update_time`, `delete_time`) VALUES
(1, '超级管理', '拥有系统的所有权限（请谨慎分配）', 'admin', 1, 1, 0, '1,100', 1534585999, 1557977269, NULL),
(2, '普通管理', '拥有系统的部分权限', 'admin', 1, 1, 0, '1,100', 1534585999, 1564907144, NULL),
(3, '体验组', '体验系统的账户, 禁止对数据的修改', 'admin', 1, 1, 1, '1,100', 1534585999, 1564907067, NULL);

DROP TABLE IF EXISTS `__PREFIX__auth_rule`;
CREATE TABLE IF NOT EXISTS `__PREFIX__auth_rule` (
  `id` mediumint(8) unsigned NOT NULL,
  `parent_id` smallint(8) NOT NULL DEFAULT '0' COMMENT '父id',
  `menutype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1主菜单0子菜单',
  `title` char(20) DEFAULT NULL COMMENT '规则名称',
  `module` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '模块',
  `name` char(80) DEFAULT NULL COMMENT '规则',
  `fontico` varchar(50) DEFAULT NULL COMMENT '图标',
  `btnclass` varchar(50) DEFAULT NULL COMMENT '按钮样式',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
  `deleting` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可删除',
  `ischeck` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否验证',
  `condition` varchar(300) DEFAULT NULL COMMENT '规则附加条件',
  `parameter` varchar(50) DEFAULT NULL COMMENT '参数',
  `description` varchar(100) DEFAULT NULL COMMENT '规则描述',
  `listorder` int(3) NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '类型'
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限规则表';

INSERT INTO `__PREFIX__auth_rule` (`id`, `parent_id`, `menutype`, `title`, `module`, `name`, `fontico`, `btnclass`, `status`, `deleting`, `ischeck`, `condition`, `parameter`, `description`, `listorder`, `create_time`, `update_time`, `delete_time`, `type`) VALUES
(1, 0, 1, '常规管理', 'admin', '/console', 'icon-home_light', '', 1, 0, 0, NULL, '', '', 1, 1538058964, 1557977589, NULL, 1),
(3, 0, 1, '系统管理', 'admin', '/config', 'icon-settings_light', '', 1, 0, 1, NULL, NULL, NULL, 3, 1538058964, 1537164315, NULL, 1),
(4, 0, 1, '权限管理', 'admin', '/auth', 'icon-quanxianguanli', '', 1, 0, 1, NULL, NULL, '', 4, 1538058964, 1539134955, NULL, 1),
(5, 0, 1, '应用管理', 'admin', '/apply', 'icon-yingyong', '', 1, 0, 1, NULL, NULL, '', 5, 1538058964, 1542379876, NULL, 1),
(6, 0, 1, '插件管理', 'admin', '/addons', 'icon-filter', '', 1, 0, 1, NULL, '', '', 5, 1538058964, 1557996366, NULL, 1),
(100, 1, 1, '首页', 'admin', '/console/welcome', 'icon-home_light', '', 1, 0, 0, NULL, '', '展示系统统计数据及系统信息。', 1, 1536033583, 1559013233, NULL, 1),
(300, 3, 1, '系统参数', 'admin', '/config/param/index', 'icon-settings_light', '', 1, 0, 1, NULL, NULL, '可以在此增改系统的变量和分组，也可以自定义变量和分组，如果需要删除请从数据库中删除', 1, 1536038712, 1541467804, NULL, 1),
(301, 300, 0, '添加参数', 'admin', '/config/param/add', '', '', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1545185401, NULL, 1),
(302, 300, 0, '编辑参数', 'admin', '/config/param/edit', '', '', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1545185402, NULL, 1),
(303, 300, 0, '添加分组', 'admin', '/config/param/addgroup', '', '', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1545185405, NULL, 1),
(310, 3, 1, '个人信息', 'admin', '/auth/sysadm/personal', 'icon-friend_settings_light', '', 1, 0, 1, NULL, NULL, '管理个人的信息，操作日志', 2, 1536038712, 1541467806, NULL, 1),
(320, 3, 1, '皮肤管理', 'admin', '/config/skin/index', 'icon-skin_light', '', 1, 0, 1, NULL, NULL, '后台界面的皮肤设置，可自定义你喜欢的皮肤', 3, 1536039417, 1541467807, NULL, 1),
(321, 320, 0, '添加皮肤', 'admin', '/config/skin/add', 'icon-add_light', 'easy-btn-add', 1, 0, 1, NULL, NULL, '', 0, 1536039417, 1543052113, NULL, 1),
(322, 320, 0, '编辑', 'admin', '/config/skin/edit', 'icon-edit_light', 'easy-btn-edit', 0, 0, 1, NULL, NULL, '', 0, 1536039417, 1543050063, NULL, 1),
(323, 320, 0, '删除', 'admin', '/config/skin/delete', 'icon-delete_light', 'easy-btn-batchdel', 0, 0, 1, NULL, NULL, '', 0, 1536039417, 1540543860, NULL, 1),
(324, 320, 0, '更新样式缓存', 'admin', '/config/skin/updatecss', 'icon-refresh_light', 'easy-btn-ajax', 1, 0, 1, NULL, NULL, '', 0, 1536039417, 1541486643, NULL, 1),
(330, 3, 1, '路由管理', 'admin', '/config/route/index', 'icon-luyou', '', 1, 0, 1, NULL, NULL, '把URL的请求优雅的对应到你想要执行的操作方法。', 4, 1536038712, 1563874278, NULL, 1),
(331, 330, 0, '添加', 'admin', '/config/route/add', 'icon-add_light', 'easy-btn-add', 1, 0, 1, NULL, NULL, '', 0, 1536038712, 1541149442, NULL, 1),
(332, 330, 0, '编辑', 'admin', '/config/route/edit', '', 'easy-btn-edit', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1541483236, NULL, 1),
(333, 330, 0, '删除', 'admin', '/config/route/delete', '', '', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1541483243, NULL, 1),
(334, 330, 0, '批量', 'admin', '/config/route/multi', '', '', 0, 0, 1, NULL, NULL, '', 0, 1536038712, 1541483246, NULL, 1),
(335, 330, 0, '更新缓存', 'admin', '/config/route/upcache', 'icon-refresh_light', 'easy-btn-ajax', 1, 0, 1, NULL, NULL, '', 0, 1536038712, 1542642285, NULL, 1),
(400, 4, 1, '管理员', 'admin', '/auth/sysadm/index', 'icon-friend_settings_light', '', 1, 0, 1, NULL, NULL, '可设置多个管理员，菜单是根据管理员所拥有的权限进行生成的。', 1, 1536039764, 1541468720, NULL, 1),
(401, 400, 0, '添加', 'admin', '/auth/sysadm/add', 'icon-add_light', 'easy-btn-add', 1, 0, 1, NULL, NULL, '', 1, 1536039764, 1541468369, NULL, 1),
(402, 400, 0, '编辑', 'admin', '/auth/sysadm/edit', NULL, '', 0, 0, 1, NULL, NULL, NULL, 2, 1536039764, 1544232962, NULL, 1),
(403, 400, 0, '删除', 'admin', '/auth/sysadm/delete', NULL, '', 0, 0, 1, NULL, NULL, '', 3, 1536039764, 1541468106, NULL, 1),
(404, 400, 0, '批量', 'admin', '/auth/sysadm/multi', NULL, '', 0, 0, 1, NULL, NULL, NULL, 4, 1536039764, 1541468108, NULL, 1),
(410, 4, 1, '角色组', 'admin', '/auth/role/index', 'icon-friend_light', '', 1, 0, 1, NULL, NULL, '一个管理组可含多个功能权限，请根据需求进行分配权限。', 2, 1536040018, 1541467816, NULL, 1),
(411, 410, 0, '添加', 'admin', '/auth/role/add', 'icon-add_light', 'easy-btn-add', 1, 0, 1, NULL, NULL, '', 0, 1536040018, 1540809227, NULL, 1),
(412, 410, 0, '编辑', 'admin', '/auth/role/edit', NULL, '', 0, 0, 1, NULL, NULL, '', 0, 1536040018, 1536133290, NULL, 1),
(413, 410, 0, '删除', 'admin', '/auth/role/delete', NULL, 'layui-btn-danger', 0, 0, 1, NULL, NULL, '', 0, 1536040018, 1536133302, NULL, 1),
(414, 410, 0, '批量', 'admin', '/auth/role/multi', NULL, '', 0, 0, 1, NULL, NULL, '', 0, 1536040018, 1536133309, NULL, 1),
(420, 4, 1, '菜单规则', 'admin', '/auth/rule/index', 'icon-subtitle_unblock_light', '', 1, 0, 1, NULL, NULL, '后台菜单规则是对应一个控制器的方法，左侧的菜单栏也是根据规则生成。', 3, 1536040067, 1541467820, NULL, 1),
(421, 420, 0, '添加', 'admin', '/auth/rule/add', 'icon-add_light', 'easy-btn-add', 1, 0, 1, NULL, NULL, '', 0, 1536040067, 1540712363, NULL, 1),
(422, 420, 0, '编辑', 'admin', '/auth/rule/edit', 'icon-edit_light', 'easy-btn-edit', 1, 0, 1, NULL, NULL, '', 0, 1536040067, 1540712366, NULL, 1),
(423, 420, 0, '删除', 'admin', '/auth/rule/delete', 'icon-delete_light', 'easy-btn-delete', 0, 0, 1, NULL, NULL, '', 0, 1536040067, 1543062225, NULL, 1),
(424, 420, 0, '批量', 'admin', '/auth/rule/multi', NULL, '', 0, 0, 1, NULL, NULL, NULL, 0, 1536040067, 1536040067, NULL, 1),
(425, 420, 0, '更新缓存', 'admin', '/console/upcache?modelname=auth_rule', 'icon-refresh_light', 'easy-btn-ajax', 1, 0, 1, NULL, NULL, '', 0, 1536043648, 1543062403, NULL, 1),
(430, 4, 1, '资源管理器', 'admin', '/auth/rule/restful', 'icon-round_list_light', '', 1, 0, 1, NULL, NULL, '统一管理常用的操作，例如：add()、edit()、delete()、multi()', 4, 1536040067, 1541467824, NULL, 1),
(600, 6, 1, '插件管理', 'admin', '/addons/index', 'icon-filter', '', 1, 0, 1, NULL, '', '可在线安装及卸载、同时支持添加本地插件。', 5, 1538058964, 1556587421, NULL, 1),
(601, 600, 0, '编辑', 'admin', '/addons/edit', '', '', 1, 1, 1, NULL, '', '', 0, 1560395592, 1560395592, NULL, 1),
(602, 600, 0, '安装', 'admin', '/addons/install', '', '', 1, 1, 1, NULL, '', '', 0, 1560395592, 1560395592, NULL, 1),
(603, 600, 0, '卸载', 'admin', '/addons/uninstall', '', '', 1, 1, 1, NULL, '', '', 0, 1560395592, 1560395592, NULL, 1);

DROP TABLE IF EXISTS `__PREFIX__config_group`;
CREATE TABLE IF NOT EXISTS `__PREFIX__config_group` (
  `id` mediumint(8) unsigned NOT NULL,
  `name` char(20) NOT NULL COMMENT '名称',
  `title` char(20) DEFAULT NULL COMMENT '标题',
  `description` text COMMENT '描述',
  `details` text
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统配置类型表';

INSERT INTO `__PREFIX__config_group` (`id`, `name`, `title`, `description`, `details`) VALUES
(1, 'system', '后台配置', '配置当前后台的信息。', NULL),
(2, 'param', '参数配置', '系统常用的参数，请勿随意修改或删除。', NULL),
(3, 'cache', '缓存配置', 'ThinkPHP5.1的缓存，内置支持的缓存类型包括file、memcache、wincache、sqlite、redis和xcache。', NULL),
(4, 'captcha', '验证码配置', '模版内添加验证码的显示代码 <div>{:captcha_img()}</div>，更具体的使用请查看ThinkPHP5.1官方开发手册', NULL),
(5, 'upload', '上传配置', '上传文件的相关参数配置，可限制文件的大小、格式、MIME类型等。', NULL),
(6, 'aliyunoss', '阿里云OSS', '开启此功能后，文件将会上传到阿里云OSS服务器，前提是您已申请阿里云OSS服务。', NULL),
(10, 'domain', '域名部署', '将子域名到部署到指定的模块。如果配置错误，请手动删除 /Config/domain.php 文件', '&lt;p&gt;&lt;span style=&quot;font-size:12px&quot;&gt;如果需自定义子域名部署到模块或控制器，请添加一个参数变量:&lt;br/&gt;&lt;strong&gt;字段类型：&lt;/strong&gt;[字符]&lt;br/&gt;&lt;strong&gt;归属分组：&lt;/strong&gt;[域名部署]&lt;br/&gt;&lt;strong&gt;变量标题：&lt;/strong&gt;[如：后台]&lt;br/&gt;&lt;strong&gt;　变量名：&lt;/strong&gt;[如：admin]&lt;br/&gt;&lt;strong&gt;　变量值：&lt;/strong&gt;[这个就填你的子域名前缀，如：admin]&lt;/span&gt;&lt;/p&gt;');

DROP TABLE IF EXISTS `__PREFIX__config_param`;
CREATE TABLE IF NOT EXISTS `__PREFIX__config_param` (
  `id` mediumint(8) unsigned NOT NULL,
  `group_id` smallint(6) NOT NULL DEFAULT '1' COMMENT '分组ID',
  `type` char(20) DEFAULT NULL COMMENT '字段类型',
  `name` char(20) NOT NULL COMMENT '变量名',
  `title` char(30) DEFAULT NULL COMMENT '变量标题',
  `placeholder` varchar(50) DEFAULT NULL COMMENT '提示占位符号',
  `value` varchar(255) DEFAULT NULL COMMENT '变量值',
  `verify` varchar(50) DEFAULT NULL COMMENT '校验规则',
  `option` text COMMENT '数据配置'
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统配置参数';

INSERT INTO `__PREFIX__config_param` (`id`, `group_id`, `type`, `name`, `title`, `placeholder`, `value`, `verify`, `option`) VALUES
(1, 1, 'text', 'title', '后台名称', '请填写后台名称', 'EasyWcms 管理系统', 'required', ''),
(3, 1, 'text', 'beian', '备案号', '请填写站点备案号', '粤ICP备13006272号', NULL, ''),
(4, 1, 'text', 'copyright', '版权信息', '请填写版权信息', 'EasyWcms 版权所有', NULL, NULL),
(5, 1, 'text', 'icon', 'ICON', 'ico格式，像素：16px * 16px', '/assets/favicon.ico', NULL, NULL),
(6, 1, 'image', 'logo', '控制台LOGO', 'png格式，像素：150px * 50px', '/assets/logo.png', NULL, NULL),
(7, 1, 'image', 'login_logo', '登录界面LOGO', 'png格式，像素：260px * 60px', '/assets/login.png', NULL, NULL),
(8, 1, 'text', 'login_text', '登录界面文字', '', '让开发更简单 更快捷', NULL, NULL),
(16, 2, 'text', 'skinpath', '后台皮肤路径', '仅限存放在与index.php同级的目录内', '/assets/skin/', 'required', NULL),
(17, 2, 'text', 'assets', '静态资源路径', '可将静态资源(css,js,img)部署到cdn', '/assets/', 'required', NULL),
(18, 2, 'text', 'html_path1', 'Html静态文件', 'Html静态文件的存放目录', '/html/', 'required', NULL),
(20, 2, 'number', 'page_limit', '列表分页数', '列表每页显示的条数 例：15', '15', 'required|number', NULL),
(22, 3, 'text', 'type', '驱动方式', '缓存类型或者缓存驱动类名　默认为file', 'file', 'required', NULL),
(23, 0, 'text', 'path', '缓存保存目录', '缓存目录', '../runtime/cache', '', NULL),
(24, 4, 'text', 'codeSet', '验证码字符集合', '验证码出现的字符', '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY', 'required', NULL),
(25, 4, 'number', 'fontSize', '验证码字体大小', '验证码字体大小 单位:像素(px)', '16', 'required|number', NULL),
(26, 4, 'switcher', 'useCurve', '画混淆曲线', '是否开启画混淆曲线', '0', 'required', NULL),
(27, 4, 'switcher', 'useNoise', '添加杂点', '是否开启添加杂点', '0', 'required', NULL),
(28, 4, 'number', 'imageH', '验证码图片高度', '验证码图片高度，根据所需进行设置高度', '40', 'required|number', NULL),
(29, 4, 'number', 'imageW', '验证码图片宽度', '验证码图片宽度，根据所需进行设置宽度', '150', 'required|number', NULL),
(30, 4, 'number', 'length', '验证码位数', '验证码位数，根据所需设置验证码位数', '4', 'required|number', NULL),
(31, 4, 'number', 'expire', '过期时间(s)', '验证码过期时间为秒', '600', 'required|number', NULL),
(32, 3, 'text', 'prefix', '缓存前缀', '默认为空', '', '', NULL),
(33, 3, 'number', 'expire', '缓存有效期', '缓存有效期（秒） 0表示永久缓存', '0', 'required|number', NULL),
(35, 10, 'text', 'admin', 'admin模块', '请输入域名，例如：http://admin.example.com/', '', '', NULL),
(36, 2, 'select', 'editor', '默认编辑器', '编辑器输入框', 'ueditor', 'required', 'ueditor|百度编辑器'),
(37, 5, 'text', 'url', '访问地址', '例如：http://admin.example.com/uploads/', '/uploads/', 'required', NULL),
(39, 5, 'number', 'size', '文件大小', '单个文件的上传文件的最大字节', '1048576', 'required|number', NULL),
(40, 5, 'text', 'ext', '文件后缀', '多个请用英文逗号分割', 'jpg,png,gif,bmp,jpeg,zip', 'required', NULL),
(42, 6, 'switcher', 'enable', '启用OSS', '开启后，文件将会统一上传到阿里云OSS', '0', '', NULL),
(43, 6, 'text', 'keyid', 'AccessKey ID', '请前往阿里云控制台获取', '', 'required', NULL),
(44, 6, 'text', 'keysecret', 'AccessKey Secret', '请前往阿里云控制台获取', '', 'required', NULL),
(45, 6, 'text', 'endpoint', 'Endpoint', '由阿里云提供的地域节点', '', 'required', NULL),
(46, 6, 'text', 'bucket', 'Bucket', '自定义的Bucket名称', '', 'required', NULL),
(47, 6, 'text', 'host', '自定义域名', '例如：http://admin.example.com/', '', 'required|url', NULL),
(48, 6, 'text', 'directory', '存放目录', '在OSS的根目录下创建一个主目录', '', 'required', NULL),
(49, 5, 'switcher', 'water', '开启图片水印', '', '1', '', NULL),
(50, 5, 'number', 'minwidth', '水印添加条件 宽', '上传小于此尺寸的图片将不会添加水印', '300', 'required|number', NULL),
(51, 5, 'number', 'minheight', '水印添加条件 高', '上传小于此尺寸的图片将不会添加水印', '300', 'required|number', NULL),
(52, 5, 'text', 'source', '水印图片', '水印存放路径', './assets/logo.png', 'required', NULL),
(53, 5, 'number', 'alpha', '水印透明度', ' 请设置为0-100之间的数字，0代表完全透明，100代表不透明', '90', 'required|number', NULL),
(54, 5, 'number', 'quality', 'JPEG 水印质量', '水印质量请设置为0-100之间的数字,决定 jpg 格式图片的质量', '80', 'required|number', NULL),
(55, 5, 'select', 'locate', '水印位置', '', '5', 'required', '0|随机位置\n1|顶部居左\n2|顶部居中\n3|顶部居右\n4|中部居左\n5|中部居中\n6|中部居右\n7|底部居左\n8|底部居中\n9|底部居右');

DROP TABLE IF EXISTS `__PREFIX__config_route`;
CREATE TABLE IF NOT EXISTS `__PREFIX__config_route` (
  `id` mediumint(8) unsigned NOT NULL,
  `group` varchar(30) DEFAULT NULL COMMENT '路由分组名',
  `name` varchar(50) DEFAULT NULL COMMENT '路由标识',
  `type` char(20) DEFAULT NULL COMMENT '请求类型',
  `title` varchar(20) DEFAULT NULL COMMENT '标题',
  `expression` varchar(50) DEFAULT NULL COMMENT '路由表达式',
  `address` varchar(150) DEFAULT NULL COMMENT '路由地址',
  `append` text COMMENT '路由参数',
  `pattern` text COMMENT '参数变量规则',
  `remarks` varchar(50) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `deleting` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='路由配置';

DROP TABLE IF EXISTS `__PREFIX__config_skin`;
CREATE TABLE IF NOT EXISTS `__PREFIX__config_skin` (
  `id` mediumint(8) unsigned NOT NULL,
  `skin_name` varchar(20) DEFAULT NULL,
  `skin_data` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `listorder` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `deleting` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可删除'
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='皮肤配置表';

INSERT INTO `__PREFIX__config_skin` (`id`, `skin_name`, `skin_data`, `create_time`, `update_time`, `delete_time`, `listorder`, `deleting`) VALUES
(1, 'Green', '{"maincolor":"#0ba360","assistcolor":"#3cba92","sidebar":"#021c11"}', 1532540649, 1561528807, NULL, 0, 0),
(2, 'Darkdeep', '{"maincolor":"#3cba92","assistcolor":"#9f9f11","sidebar":"#001b17"}', 1535534952, 1542478864, NULL, 0, 0),
(3, 'Navyblue', '{"maincolor":"#60a37c","assistcolor":"#105cc1","sidebar":"#101b14"}', 1535206905, 1540654601, NULL, 0, 0),
(4, 'Blue', '{"maincolor":"#1278f6","assistcolor":"#00b4aa","sidebar":"#02101a"}', 1532540649, 1539875319, NULL, 0, 0),
(5, 'Purple', '{"maincolor":"#1fa5de","assistcolor":"#9d2fc5","sidebar":"#07161c"}', 1532540649, 1542478833, NULL, 0, 0),
(6, 'Black', '{"maincolor":"#29323c","assistcolor":"#395473","sidebar":"#12171c"}', 1532540649, 1539791143, NULL, 0, 0),
(7, 'Red', '{"maincolor":"#c3272b","assistcolor":"#ee3528","sidebar":"#140406"}', 1532540649, 1540906363, NULL, 0, 0),
(8, 'Purples', '{"maincolor":"#68117a","assistcolor":"#ef7168","sidebar":"#0d000d"}', 1535096604, 1540906316, NULL, 0, 0),
(9, 'Orange', '{"maincolor":"#de4313","assistcolor":"#fcae38","sidebar":"#1a0802"}', 1532540649, 1540906289, NULL, 0, 0),
(10, 'Latte', '{"maincolor":"#917046","assistcolor":"#aa8452","sidebar":"#0e0b07"}', 1535014002, 1545184699, NULL, 0, 0),
(11, 'Diy11', '{"maincolor":"#126541","assistcolor":"#104a37","sidebar":"#04150e"}', 1543497797, 1548838089, NULL, 0, 1),
(12, 'Diy12', '{"maincolor":"#0ba360","assistcolor":"#3cba92","sidebar":"#041200"}', 1557978078, 1557978078, NULL, 0, 1);

DROP TABLE IF EXISTS `__PREFIX__sysadm`;
CREATE TABLE IF NOT EXISTS `__PREFIX__sysadm` (
  `id` int(11) unsigned NOT NULL,
  `auth_role_id` char(20) NOT NULL DEFAULT '0',
  `username` varchar(11) DEFAULT NULL COMMENT '账号',
  `usersign` varchar(10) DEFAULT NULL COMMENT '签名',
  `realname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `description` varchar(100) DEFAULT NULL COMMENT '说明',
  `headimgurl` varchar(100) DEFAULT NULL COMMENT '头像',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(50) DEFAULT NULL,
  `login_count` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  `lastlogintime` int(11) unsigned NOT NULL DEFAULT '0',
  `lastip` char(15) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleting` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可删除',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `rules` text,
  `disable_rules` text COMMENT '禁用指定的权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统管理员';


ALTER TABLE `__PREFIX__addons`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `__PREFIX__annex`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `__PREFIX__auth_log`
  ADD PRIMARY KEY (`id`), ADD KEY `uid` (`uid`);

ALTER TABLE `__PREFIX__auth_role`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `__PREFIX__auth_rule`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `__PREFIX__config_group`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `__PREFIX__config_param`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `__PREFIX__config_route`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `__PREFIX__config_skin`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `skin_name` (`skin_name`);

ALTER TABLE `__PREFIX__sysadm`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);


ALTER TABLE `__PREFIX__addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
ALTER TABLE `__PREFIX__annex`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `__PREFIX__auth_log`
  MODIFY `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `__PREFIX__auth_role`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
ALTER TABLE `__PREFIX__auth_rule`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1000;
ALTER TABLE `__PREFIX__config_group`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
ALTER TABLE `__PREFIX__config_param`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=56;
ALTER TABLE `__PREFIX__config_route`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `__PREFIX__config_skin`
  MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=13;
ALTER TABLE `__PREFIX__sysadm`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;