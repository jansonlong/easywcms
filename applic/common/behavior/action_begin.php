<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 共公行为扩展 - 操作开始
// +----------------------------------------------------------------------
namespace app\common\behavior;

use think\facade\Request;
use think\facade\Session;
use think\facade\Config;
use think\facade\Route;
use think\facade\View;

class action_begin{

    public function run(Request $request){
		
		//限制账户名为test修改数据
		if( $request::isPost() ){
			$session = Session::get('Admininfo','admin');
			if( isset($session['username']) && $session['username'] == 'test'){
				header('Content-Type:application/json');
				exit( json_encode(['code'=>0,'msg'=>'体验账户，不能修改数据哦！']) );
				die;
			}
		}
		
		//定义表单生成器类名
		class_alias('easywcms\\base\\Form', 'Form');
		
		//请求路径
		$path = '/'.$request::module().'/'.str_replace( '.' , '/' , $request::controller(true) );
		//参数配置
		$config = array_merge([
				'domain'	=> Request::domain(),
				'site'		=> Config::get('system.'),
				'module' 	=> $request::module(),
				'action' 	=> $request::action(),
				'controller'=> $request::controller(true),
				'ip' 		=> $request::ip(),
				'version'	=> Config::get('app.app_debug') ? time() : Config::get('app.easywcms_version'),
		],Config::get('param.'));
		//赋值全局模板变量
		View::share('easy',[
			'config'=>$config,
			'url'=> [
				'upload'=> url('/admin/annex/upload'),
				'base'	=> url($path.'/'),
				'index'	=> url($path.'/index'),
				'add'	=> url($path.'/add'),
				'edit'	=> url($path.'/edit'),
				'delete'=> url($path.'/delete'),
				'multi'	=> url($path.'/multi'),
			],
		]);
	}
	
}