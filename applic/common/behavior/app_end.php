<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 共公行为扩展
// +----------------------------------------------------------------------
namespace app\common\behavior;

use think\facade\Session;
use think\Request;
use think\Db;

class app_end{

    public function run(Request $request)
    {
		//后台操作日志
		$sysadm_id = Session::get('Admininfo.sysadm_id','admin') ? : 0;
		//
		if( $request->isPost() && $sysadm_id ){
            $param = $request->param();
            if( isset($param['password']) ){
                unset($param['password']);
            }
			//记录post请求日志
			$data = [
				'uid'			=> $sysadm_id,
				'type' 			=> 'post',
				'module' 		=> $request->module(),
				'controller' 	=> $request->controller(),
				'action' 		=> $request->action(),
				'url' 			=> $request->url(),
				'ip' 			=> $request->ip(),
				'param' 		=> json_encode( $param ),
				'create_time' 	=> time()
			];
			//入库保存
			Db::name('auth_log')->insert($data);
			return true;
		}else{
			 return false;
		}
	}
}