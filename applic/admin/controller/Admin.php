<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 主控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\Verification;
use app\admin\controller\auth\Log;
use think\Controller;
use think\Request;
use easywcms\Easy;
use easywcms\Auth;

class Admin extends Controller{
	
	//请求对象
	public $request = false;
	
	//模块
	protected $module = false;
	
	//控制器
	protected $controller = false;
	
	//操作方法
	protected $action = false;
	
	//管理员信息
	protected $userinfo = [];
	
	//权限验证实例
	public $verification = false;
	
	//逻辑层类
	public $logic = false;
	
	//定义模型
	public $Model 	= false;
	
	//关联预载入
	public $with 	= false;

	/**
	 * 初始化
	 * @date 2018-08-10
	 */
    public function initialize(){
		//管理员信息
		$this->userinfo = Easy::getSession('Admininfo','admin');
		//权限验证实例->登录验证->权限验证->获取子菜单
		$this->auth 	= Auth::getInstance()->login()->auth()->subMenu();
		//模块
		$this->module 	= $this->request->module();
		//操作方法
		$this->action 	= $this->request->action();
		//控制器
		$this->controller = $this->request->controller();		
    }
	
	//获取表主键
	private function getModelPk(){
		$this->PK = $this->Model->getPk();
		return $this;
	}
	
	/**
	 * 按分页读取
	 * @return json
	 * @date 2018-08-20
	 */
	public function getPage(){
		//获取表主键
		$this->getModelPk();
		//统计行数
		$count = $this->Model->where($this->Where)->count($this->PK);
		//分页查询数据
		if($count > 0){
			//页码 默认第1页
			$page = ($this->request->param('page/d',1))-1;
			//每页显示条数
			$page_limit = $this->request->param('limit/d' , Easy::getConfig('param.page_limit') );
			//设置关联预载入
			$data = $this->Model->with($this->with);
			//按分页查询
			$data = $data->field($this->Field)->where($this->Where)->order($this->Order)->limit($page*$page_limit.','.$page_limit)->select()->toArray();
			//返回数据
			return ['code'=>1,'count'=>$count,'data'=>$data];
		}
		return ['code'=>0,'msg'=>lang('None'),'count'=>0];	
	}
		
	/**
	 * 共用列表查询操作
	 * @date 2018-08-17
	 */
	public function index(){
		if( $this->request->isAjax() && $this->logic ){
			return $this->logic->getPage();
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 共用添加操作
	 * @date 2018-08-17
	 */
    public function add(){
		if( $this->request->isPost() && $this->logic ){
			return $this->logic->validate()->doAdd();
		}else{
            if( $this->view->exists('add') ){
                return $this->fetch();
            }else{
                return $this->fetch('edit');
            }
		}
    }

	/**
	 * 共用编辑操作
	 * @date 2018-08-17
	 */
	public function edit(){
		if( $this->request->isPost() && $this->logic ){
			return $this->logic->validate()->doEdit();
		}else{
			return $this->assign('vo',$this->logic->doGet())->fetch('edit');
		}
	}
	
	/**
	 * 共用删除操作
	 * @date 2018-08-18
	 */
	public function delete(){
		if( $this->request->isPost() && $this->logic ){
			return $this->logic->doDelete();
		}else{
			return ['code'=>0,'msg'=>lang('Invalid parameters')];
		}
	}

	/**
	 * 批量更新
	 * @date 2018-11-06
	 */
    function multi(){
		if( $this->request->isPost() && $this->logic ){
			return $this->logic->doMulti();
		}else{
			return ['code'=>0,'msg'=>lang('Invalid parameters')];
		}
    }
		
}
