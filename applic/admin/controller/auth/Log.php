<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 日志管理-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\auth;

use app\admin\controller\Admin;
use think\Request;
use think\Db;

class Log extends Admin {
	
	//记录日志
	public static function record($request){
		//记录post提交的日志
		if( $request->isPost() ){
			self::postLog($request);
		}
	}
	
}
