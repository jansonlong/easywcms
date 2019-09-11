<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | easywcms addons
// +----------------------------------------------------------------------

use think\App;
use think\Loader;
use think\facade\Hook;
use think\facade\Config;
use think\facade\Cache;
use think\facade\Route;

//插件目录
$appPath = (new App())->getAppPath();
$addons_path = dirname($appPath) . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR;
Env::set('addons_path', $addons_path);

//路由定义
Route::group('/<module?>/addons/<addon>/', function(){
	//插件错误
	Route::any( '<error>$' , '\\think\\addons\\Route@execute' );
	//一级控制器
	Route::any( '<control>/<action>$' , '\\think\\addons\\Route@execute' );
	//二级控制器定义路由
	Route::any( '<group>/<control>/<action>$' , '\\think\\addons\\Route@execute' );
});

// 如果插件目录不存在则创建
if (!is_dir($addons_path)) {
    @mkdir($addons_path, 0777, true);
}

// 注册类的根命名空间
Loader::addNamespace('addons', $addons_path);

// 闭包自动识别插件目录配置
Hook::add('app_init', function () {
    // 获取开关
    $autoload = (bool)Config::get('addons.autoload', false);
    // 非正是返回
    if (!$autoload) {
		Config::set('addons', Config::get('addons.', []) );
        return false;
    }
    // 当debug时不缓存配置
    $config = Config::get('app_debug') ? [] : (array)cache('addons'); 
    if (empty($config)) {
        // 读取插件目录及钩子列表
        $base = get_class_methods("\\think\\Addons");
        // 读取插件目录中的php文件
        foreach (glob(Env::get('addons_path') . '*/*.php') as $addons_file) {
            // 格式化路径信息
            $info = pathinfo($addons_file);
            // 获取插件目录名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) == strtolower($name)) {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\addons\\" . $name . "\\" . $info['filename']);
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                // 循环将钩子方法写入配置中
                foreach ($hooks as $hook) {
                    if (!isset($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = [];
                    }
                    // 兼容手动配置项
                    if (is_string($config['hooks'][$hook])) {
                        $config['hooks'][$hook] = explode(',', $config['hooks'][$hook]);
                    }
                    if (!in_array($name, $config['hooks'][$hook])) {
                        $config['hooks'][$hook][] = $name;
                    }
                }
            }
        }
    }
    Config::set('addons', $config);
});

// 闭包初始化行为
Hook::add('action_begin', function () {
    // 获取系统配置
    $data = Config::get('app_debug') ? [] : Cache::get('hooks', []);
    $config = Config::get('addons');
    $addons = isset($config['hooks']) ? $config['hooks'] : [];
    if (empty($data)) {
        // 初始化钩子
        foreach ($addons as $key => $values) {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else {
                $values = (array)$values;
            }
            $addons[$key] = array_filter(array_map('get_addon_class', $values));
            Hook::add($key, $addons[$key]);
        }
        Cache::get('hooks', $addons);
    } else {
        Hook::import($data, false);
    }
});

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    Hook::listen($hook, $params);
}

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function get_addonConfig($hook, $params = [])
{
    return Hook::listen($hook, $params);
}

/**
 * 获取所有插件名称
 */
function get_addons_list(){
	foreach (glob(Env::get('addons_path') . '*/*.php') as $addons_file) {
		// 格式化路径信息
		$info = pathinfo($addons_file);
		if( $info['filename'] == 'config' ){
			continue;
		}
		// 获取插件目录名
		$name = pathinfo($info['dirname'], PATHINFO_FILENAME);
		$list[] = 'addons/'. $name;
	}
	return $list;
}

/**
 * 获取所有插件信息 不获取已安装的
 */
function get_addons_info(){
    $addons_path = Env::get('addons_path');
    $config_list = glob($addons_path . '*/config.php');
    //
	foreach ($config_list as $addons_file) {
		// 格式化路径信息
		$info = pathinfo($addons_file);
		// 获取插件目录名
		$name = pathinfo($info['dirname'], PATHINFO_FILENAME);
		if( file_exists($addons_path.$name.'/install.lock') ){
			continue;
		}
        //实例化插件获取插件信息
		$addons = "\\addons\\{$name}\\".ucfirst($name);
		$addons = new $addons;
		$list[] = $addons->info;
	}
	return $list;
}

/**
 * 获取插件类的类名
 * @param $name 插件名
 * @param string $type 返回命名空间类型
 * @param string $class 当前类名
 * @return string
 */
function get_addon_class($name, $type = 'hook', $class = null)
{
    $name = Loader::parseName($name);
    // 处理多级控制器情况
    if (!is_null($class) && strpos($class, '.')) {
        $class = explode('.', $class);
        foreach ($class as $key => $cls) {
            $class[$key] = Loader::parseName($cls, 1,$key);
        }
        $class = implode('\\', $class);
    } else {
        $class = Loader::parseName(is_null($class) ? $name : $class, 1);
    }
    switch ($type) {
		//一级控制器
        case 'controller':
            $namespace = "\\addons\\" . $name . "\\controller\\" . $class;
            break;
        default:
            $namespace = "\\addons\\" . $name . "\\" . $class;
    }

    return $namespace;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 * @param string $name 获取指定key值的value
 * @return array
 */
function get_addon_config($name,$key=false)
{
	$config_file = Env::get('addons_path').$name.'/config.php';
	if (is_file($config_file)) {
		$temp_arr = include $config_file;
        if($key){
            return $temp_arr[$key]['value'];
        }
		return $temp_arr;
	}else{
		return '';
	}
}

/**
 * 插件显示内容里生成访问插件的url
 * @param $url
 * @param array $param
 * @return bool|string
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 */
function addon_url($url, $param = [], $suffix = true, $domain = false)
{
    $url = parse_url($url);
    $case = Config::get('url_convert');
    $addons = $case ? Loader::parseName($url['scheme']) : $url['scheme'];
    $controller = $case ? Loader::parseName($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    // 生成插件链接新规则
    $actions = "{$addons}/{$controller}/{$action}";

    return url("/addons/{$actions}", $param, $suffix, $domain);
}