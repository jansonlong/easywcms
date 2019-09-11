<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 管理员-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\auth;

use app\admin\controller\Admin;

class Sysadm extends Admin {
	
	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\auth\SysadmLogic;		
    }
	
	//前置操作
    protected $beforeActionList = [
		//表示 only 里的这些方法使用前置方法 beforeIndex() 。
        'beforeIndex' => [ 'only' => ['index'] ],
        'beforeEdit'  => [ 'only' => ['add','edit'] ]
    ];
	
	/**
	 * 列表前置操作
	 * @access public
	 * @date 2018-08-17
	 */
	public function beforeIndex()
	{
		//设置预载入
		$this->logic->setWith('AuthRole');
	}
		
	/**
	 * 添加与编辑的前置操作
	 * @access public
	 * @date 2018-08-31
	 */
	public function beforeEdit()
	{
		if( $this->request->isGet() ){
			$model = new \app\common\model\AuthRole;
			$AuthRole = $model
			->field('id,title')
			->where([['status','=',1],['module','=',$this->module]])
			->select()
			->toArray();
			$this->assign('AuthRole',$AuthRole);
		//检查账户是否有相同
		}else{
			$id = $this->request->param('id/d');
			$username = $this->request->param('username');
			$model = new \app\common\model\Sysadm;
			$checkid  = $model->where('username','=',$username)->value('id');
			$action	  = $this->request->action();
			$return	  = ( $checkid && ( $action == 'add'||( $action == 'edit' && $checkid != $id ) ) ) ? true : false;
			$return && $this->error( $username.lang('UserName already exists') );
		}
	}
	
	/**
	 * 个人信息
	 * @access public
	 * @date 2018-09-10
	 */
	public function personal()
	{
		//读取个人操作日志
		if(!$this->request->isPost() && $this->request->isAjax() ){
			$this->logic = new \app\admin\logic\auth\AuthLogLogic;
			$where = [['type','=','post'],['uid','=',$this->userinfo['sysadm_id']]];
			return $this->logic->setWhere($where)->setOrder('id desc')->setHidden(['param'])->getPage();
		//保存个人信息
		}else if( $this->request->isPost() && $this->request->param('id') == $this->userinfo['sysadm_id'] ){
			$save = $this->logic
				 ->validatePW( $this->request->param() )
				 ->setAllowField(['headimgurl','password'])
				 ->doEdit();
			//保存成功更新session
			if($save!==false){
				$headimgurl = $this->request->param('headimgurl');
				if( !empty($headimgurl) ){
					$this->userinfo['headimgurl'] = $headimgurl;
					session('Admininfo',$this->userinfo,'admin');
				}
                $this->success();
			}
		//显示个人信息
		}else{
			return $this->assign($this->userinfo)->fetch();
		}
	}
	
	/**
	 * 个人日志
	 * @access public
	 * @date 2019-02-22
	 */
	public function logs()
	{
		$uid = $this->request->param('uid');
		if( $this->request->isAjax() && $uid){
			$this->logic = new \app\admin\logic\auth\AuthLogLogic;
			$where = [['type','=','post'],['uid','=',$uid]];
			return $this->logic->setWhere($where)->setOrder('id desc')->getPage();
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 个人权限
	 * @access public
	 * @date 2019-02-22
	 */
	public function setauth()
	{
		$uid = $this->request->param('uid');
		if( $uid ){
			//获取用户信息
			$user = $this->logic->setField('id,auth_role_id,rules,disable_rules')->setWhere([['id','=',$uid]])->doFind();
			$this->assign('vo',$user);
			//获取当前角色组已有的权限
			$Role = new \app\admin\logic\auth\Role;
			$vo = $Role->setField('id,title,description,rules')->setWhere([['id','=',$user['auth_role_id']]])->doFind();
			//
			$rules_data = explode(',',$vo['rules']);
			//整合权限
			if(!empty($user['rules'])){
				$user_rules = explode(',',$user['rules']);
				$rules_data = array_merge($rules_data,$user_rules);
			}
			//剔除禁用的权限
			if(!empty($user['disable_rules'])){
				$rules_data = array_merge( array_diff( $rules_data, explode(',', $user['disable_rules']) ) );
			}
			$rulesdata = implode(',',$rules_data);
			$this->assign('rulesdata',$rulesdata);
		}else{
			exit('参数错误');
		}
		if( $this->request->isGet() ){
			//获取系统所有权限列表
			$Rule = new \app\admin\logic\auth\Rule;
			$Rule->Field = 'id,parent_id,title,menutype';
			$Rule->orderRaw = 'listorder!=0 desc,listorder,menutype asc,id asc';
			$ret = $Rule->data("\$title");
			$this->assign('authlist',$ret['data']);
			//
			return $this->fetch();
		//提交
		}else if( $this->request->isPost() ){
			//提交的新权限
			$post_rules = $this->request->post('rules');
			//用户组权限
			$user_rules = explode(',',$vo['rules']);
			//设置独立权限：获取提交权限的ID在用户组权限不存在的ID(差)
			if($post_rules != 1){
				$rules = array_diff($post_rules, $user_rules);
				$rules = implode(',', $rules);
			}else{
				$rules = '';
			}
			//禁用指定的权限
			if($post_rules != 1){
				$disable = array_diff($user_rules, $post_rules);
				$disable = implode(',', $disable);
			}else{
				$disable = '';
			}
			//保存
			$model = new \app\common\model\Sysadm;
			$model->save(['rules'=>$rules,'disable_rules'=>$disable],['id'=>$uid]);
			$this->success();
		}
	}
	
}