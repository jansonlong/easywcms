<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 权限管理-中间件
// +----------------------------------------------------------------------
namespace app\admin\middleware;

use app\common\model\AuthRule;
use easywcms\Easy;

class Auth{
	
    public function handle($request, \Closure $next){
		try{
			//获取当前控制器
			$controller = $request->controller();
			//获取当前操作方法
			$action = $request->action();

			//设置用户组权限-前置行为
			if( $request->isPost() && $action == 'rules' ){
				//角色组rules为空时，设置rules字段为１
				empty($request->param('rules')) && $request->rules = '1';
			}

			//删除角色组-前置行为
			if( $request->isPost() && $controller == 'Auth.role' && $action == 'delete' ){
				$role_id = $request->param('id/d');
				//禁止删除超级管理角色组
				$role_id == 1 && Easy::ajaxReturn(['code'=>0,'msg'=>lang('Prohibit deleting')]);
				//验证删除当前组是否存在用户
				$ret = (new \app\admin\logic\auth\SysadmLogic)->setField('id')->setWhere([['auth_role_id','=',$role_id]])->doFind();
				isset($ret['id']) && Easy::ajaxReturn(['code'=>0,'msg'=>lang('Role group exists administrator can not delete')]);
			}
			
			//禁止add 或 edit 添加相同的规则
			if( $request->isPost() ){
				$post = $request->post();
				$find = (new AuthRule)->where(['module'=>$post['module'],'name'=>$post['name']])->find();
				if( $find['id'] && ( ( $action == 'edit' && $post['id'] != $find['id'] ) || $action == 'add') ){
					Easy::ajaxReturn(['code'=>0,'msg'=>lang('There are the same rules')]);
				}
			}

			//以上代码是前置行为 ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
			$response = $next($request);
			//以下代码是后置行为 ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

			//添加菜单规则-后置行为
			//当添加类型为菜单, 自动创建 add edit delete multi 操作方法
			if( $request->isPost() && $controller == 'Auth.rule' && $action == 'add' ){
				$ret = $response->getData();
				if( $ret['code']==1 && $ret['data'] ){
					$data = AuthRule::get($ret['data']);
					//添加类型为菜单时, 自动创建多个资源操方法
					if($data['menutype']==1){
						//为规则类型为菜单添加多个子操作
						$name = strstr($data['name'], '/index') ? str_replace( '/index' , '' , $data['name'] ).'/' : $data['name'].'_';
						$save = [
							[
								'parent_id'	=>$ret['data'],
								'menutype'	=>0,
								'module'	=>$data['module'],
								'name'		=>$name.'add',
								'title'		=>'添加',
								'status'	=>1,
								'btnclass'	=>'easy-btn-add',
								'fontico'	=>'icon-add_light'
							],
							[
								'parent_id'	=>$ret['data'],
								'menutype'	=>0,
								'module'	=>$data['module'],
								'name'		=>$name.'edit',
								'title'		=>'编辑',
								'btnclass'	=>'easy-btn-edit',
								'fontico'	=>'icon-edit_light'
							],
							[
								'parent_id'	=>$ret['data'],
								'menutype'	=>0,
								'module'	=>$data['module'],
								'name'		=>$name.'delete',
								'title'		=>'删除',
								'btnclass'	=>'easy-btn-batchdel',
								'fontico'	=>'icon-delete_light'
							],
							[
								'parent_id'	=>$ret['data'],
								'menutype'	=>0,
								'module'	=>$data['module'],
								'name'		=>$name.'multi',
								'title'		=>'批量操作'
							],
						];
						(new AuthRule)->saveAll($save);
					}
					//更新缓存
					Easy::savecache('Auth_rule');
				}
				return $response;
			}
			//保存菜单规则成功后，更新缓存
			if( $request->isPost() && $controller == 'Auth.rule' ){
				$ret = $response->getData();
				if( $ret['code'] == 1 ){
					Easy::savecache('Auth_rule');
				}
				return $response;
			}
			//
			return $response;
		} catch (\Exception $e) {
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
    }
}