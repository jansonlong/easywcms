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

namespace think\addons;

use think\App;
use think\facade\Env;
use think\facade\Config;
use think\facade\View;
use think\facade\Hook;
use think\Loader;
use think\Request;
use think\Container;

/**
 * 插件基类控制器
 * Class Controller
 * @package think\addons
 */
class Controller extends \think\Controller
{
    // 当前插件操作
    protected $addon = null;
    protected $controller = null;
    protected $action = null;
    // 当前template
    protected $template;
    // 模板配置信息
    protected $config = [
        'type' => 'Think',
        'view_path' => '',
        'view_suffix' => 'html',
        'strip_space' => true,
        'view_depr' => DIRECTORY_SEPARATOR,
        'tpl_begin' => '{',
        'tpl_end' => '}',
        'taglib_begin' => '{',
        'taglib_end' => '}',
    ];
	
	//中间件
	protected $middleware = [
		'app\admin\middleware\Auth'
	];

    /**
     * 架构函数
     * @param App $app App对象
     * @access public
     */
    public function __construct(App $app = null)    
    {		
        // 生成request对象
        $this->request = Container::get('request');		
        $this->app     = Container::get('app');
		
        // 初始化配置信息
        $this->config = $this->app['config']->get('template.') ?: $this->config;
		
        // 处理路由参数
        $route = [
            $this->request->param('addon'),
            $this->request->param('control'),
            $this->request->param('action'),
        ];
		
        // 是否自动转换控制器和操作名
        $convert = Config::get('app.url_convert');
		
		//控制器分组名
		$this->request->param('group') && $this->group = $this->request->param('group');
		
        //格式化路由的插件位置
        $this->action 		= $convert ? strtolower(array_pop($route)) : array_pop($route);
        $this->controller 	= $convert ? strtolower(array_pop($route)) : array_pop($route);
        $this->addon 		= $convert ? strtolower(array_pop($route)) : array_pop($route);
		
        // 生成view_path
        $view_path = Env::get('addons_path') . $this->addon . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
		
        // 重置配置
		View::config('view_path',$view_path);
		
		//多级控制器处理
		if(isset($this->group)){
			$this->controller = $this->group . '.' . $this->controller;
		}
		//设置参数
		$this->request->setModule('addons/'.$this->addon);
		$this->request->setController($this->controller);
		$this->request->setAction($this->action);
    }

}
