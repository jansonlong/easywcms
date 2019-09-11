<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-权限验证
// +----------------------------------------------------------------------
namespace app\common\controller;

use think\Controller;
use think\facade\Config;
use app\common\model\AuthRule;
use easywcms\Easy;

class Verification extends Controller {
    
	//容器对象实例
    protected static $instance;
	//用户信息
    protected $userinfo;
	//参数
	protected $params = false;
	
	//初始化
    public function initialize($value=false){
		$this->userinfo = $value ? : Easy::getSession('Admininfo','admin');
    }
	
    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance($value=false){
        if (is_null(static::$instance)){
            static::$instance = new static;
        }
		//用户信息
		static::$instance->userinfo = $value ? : Easy::getSession('Admininfo','admin');
		//返回实例
        return static::$instance;
    }
		
    /**
     * 验证登录
     * @access public
     * @return $this
     */
	public function login()
    {
		//登录验证
		if( ! isset($this->userinfo['sysadm_id']) || empty($this->userinfo['sysadm_id']) ){
			if( $this->request->isAjax() || $this->request->isPost() ){
				Easy::ajaxReturn(['code'=>0,'msg'=>lang('Please login first')]);
			}else{
				$this->redirect('/');
			}
		}
		return $this;
	}
	
	/**
	 * 权限验证
	 * @date 2018-08-25
	 */
	public function auth($value=false)
    {
		if( is_array($value) ){
			$this->params = $value;
		}else{
			$this->params = [
				'module' 		=> $this->request->module(),
				'action' 		=> $this->request->action(),
				'controller' 	=> $this->request->controller()
			];
		}
		extract($this->params);
		//控制器
		$controller = '/'.strtolower( strstr($controller,'.') ? str_replace('.','/',$controller) : $controller );
		//规则名称
		$name = $controller.'/'.$action;
		//验证非超级管理员
		if( $this->userinfo['auth_role_id'] != 1 ){
			//获取规则白名单
			$white = AuthRule::where('ischeck','=',0)->column('module','name');
			//不验证白名单的规则
			if( !isset($white[$controller]) || $white[$controller] != $module ){
				//验证非规则白名单的操作
				if ( ! Easy::checkAuth($module , $name , $this->userinfo) ){
					//返回错误信息
					$message = lang('You have no permission')."：$module$name";
					//Ajax请求返回json格式
					if( $this->request->isAjax() ){
						Easy::ajaxReturn(['code'=>0,'msg'=>$message]);
					//非Ajax请求
					}else{
						$this->error($message,'',false,0);
					}
				}
			}
		}
		//赋值参数到模板
		$this->assign(['action'=>$action,'controller'=>$this->getModule().$controller]);
		//选回当前对象
		return $this;
	}
	
	/**
	 * 获取菜单列表
	 * @date 2018-08-25
	 */
	public function getMenu()
    {
		//超级管理员
		if($this->userinfo['auth_role_id'] == 1){
			$Auth_rule = Easy::getcache('Auth_rule');
		//非超级管理员
		}else{
			$where = [
				['id','in',$this->userinfo['auth_rule']],
				['status','=',1]
			];
			$Auth_rule = model('AuthRule')
				->where($where)
				->cache('Admin_AuthRule_'.$this->userinfo['sysadm_id'],600)
				->select();
		}
		//读取一级菜单
		foreach($Auth_rule as $k=>$v){
			if($v['pid']==0 && $v['status']==1){
				$item = [];
				$mainmenu[] = $v;
				//组装一级菜单的所有二级菜单
				foreach($Auth_rule as $key=>$val){
					if($val['pid'] == $v['id'] && $val['status']==1){
						//生成url
						$val['url'] = url($this->getModule($val['module']).$val['name']);
						$item[$val['id']] = $val;
					}
				}
				$submenu[$v['id']] = $item;
			}
		}
		$this->assign( 'mainmenu', isset($mainmenu) ? $mainmenu : [] );
		$this->assign( 'submenu' , isset($submenu) ? $submenu : [] );
	}
	
	/**
	 * 获取当前菜单的子菜单
	 * @date 2018-08-25
	 */
	public function subMenu()
    {
		if( is_array($this->params) ){
			extract($this->params);
		}else{
			return false;
		}
		//获取菜单描述
		$authRuleMin = Easy::getcache('Auth_rule_min');
		$rule_key = strtolower($module.'/'.str_replace('.','/',$controller).'/'.$action);		
		//菜单类型的数据
		if( isset($authRuleMin[$rule_key]) && $authRuleMin[$rule_key]['menutype'] == 1 ){
			$ThisAuthRule = $authRuleMin[$rule_key];
			$quote['name']			= $rule_key;
			$quote['title'] 		= $ThisAuthRule['title'];
			$quote['description'] 	= $ThisAuthRule['description'];
			//用户拥有的权限
			$user_rule = explode(',',$this->userinfo['auth_rule']);
			//获取当前菜单的子菜单
			foreach($authRuleMin as $k=>$v){
				//非超级管理员,只显示拥有的权限
				if( $this->userinfo['auth_role_id'] != 1 && !in_array($v['id'],$user_rule) ){
					continue;
				}
				if( $v['pid'] == $ThisAuthRule['id'] && $v['status']==1 ){ 
					$submenu[] = $v;
				}
			}
			$this->assign('submenu', isset($submenu) ? $submenu : []);
			$this->assign('quote',$quote);
		}
		return $this;
	}
	
	//
	private function getModule($value='')
    {
		$value = empty($value) ? $this->request->module() : $value ;
		return $value == 'Admin' ? '' : '/'.$value;
	}
	
}
