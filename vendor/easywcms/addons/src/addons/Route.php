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

use ReflectionMethod;
use think\facade\Response;
use think\facade\Request;
use think\facade\Config;
use think\facade\Hook;
use think\exception\HttpException;

/**
 * 插件执行默认控制器
 * Class addonsController
 * @package think\addons
 */
class Route extends Controller
{
		
    /**
     * 插件执行
     */
    public function execute(){
        try {
			// 获取类的命名空间
			$class = get_addon_class($this->addon, 'controller', $this->controller);
			
			//当控制器不存在时
			if( class_exists($class) == false ){
				$namespace = "\\addons\\" . $this->addon . "\\controller\\Error";
				//检测是否有空控制器
				if( class_exists( $namespace ) ){
					$class = $namespace;
				}
			}
			$instance = $this->app->controller($class,
					Config::get('url_controller_layer'),
					Config::get('controller_suffix'),
					Config::get('empty_controller'));
        } catch (ClassNotFoundException $e) {
            throw new HttpException(404, 'controller not exists:' . $e->getClass());
        }
		
        $this->app['middleware']->controller(function (Request $request, $next) use ($instance) {

            // 获取当前操作名
            $action = $this->action . Config::get('action_suffix');
			//
            if (is_callable([$instance, $action])) {
                // 执行操作方法
                $call = [$instance, $action];
                // 严格获取当前操作方法名
                $reflect    = new ReflectionMethod($instance, $action);
                $methodName = $reflect->getName();
                $suffix     = Config::get('action_suffix');
                $actionName = $suffix ? substr($methodName, 0, -strlen($suffix)) : $methodName;
                $this->request->setAction($actionName);
                // 自动获取请求变量
                $vars = Config::get('url_param_type')? $this->request->route() : $this->request->param();
			// 空操作	
            } elseif (is_callable([$instance, '_empty'])) {
                $call    = [$instance, '_empty'];
                $vars    = [$this->action];
                $reflect = new ReflectionMethod($instance, '_empty');
            } else {
                // 操作不存在
                throw new HttpException(404, 'method not exists:' . get_class($instance) . '->' . $action . '()');
            }

            $this->app['hook']->listen('action_begin', $call);

            $data = $this->app->invokeReflectMethod($instance, $reflect, $vars);
			
            return $this->autoResponse($data);
        });
		return $this->app['middleware']->dispatch($this->request, 'controller');
    }
	
	//输出类型
    protected function autoResponse($data)
    {
        if ($data instanceof Response) {
            $response = $data;
        //显示模板
        } elseif ( is_object($data) ) {
            $template = $data->getData();
            $response = Response::create($template, 'view');
        //返回JOSN
        } else {
            return Response::create($data, 'json', 200);
        }
        return $response;
    }
	
}
