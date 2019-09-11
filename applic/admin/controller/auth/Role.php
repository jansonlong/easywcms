<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 角色组-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\auth;

use app\admin\controller\Admin;

class Role extends Admin {
	
	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\auth\Role;
    }
	
	//定义前置操作
    protected $beforeActionList = [
        '_beforeAction' => ['only' => ['add','edit'] ],
    ];
		
	//指定方法调用中间件
	protected $middleware = [
		'\app\admin\middleware\Auth' => ['only' => ['delete','rules'] ],
	];
	
	//前置操作
	public function _beforeAction(){
		$Rule = new \app\admin\logic\auth\Rule;
		$Rule->Field = 'id,parent_id,title';
        $Rule->orderRaw = 'listorder!=0 desc,listorder,menutype asc,id asc';
		$ret = $Rule->data("\$title");
		$this->assign('authlist',$ret['data']);
	}

	//设置角色组权限
	public function edit(){
		if( $this->request->isPost() ){
			return $this->logic->doEdit();
		}else{
			//获取当前角色组已有的权限
			$vo = $this->logic->setField('id,title,description,rules')->doGet();
			if(isset($vo['code']) && $vo['code']==0){
				return json($vo);
			}
			$this->assign('vo',$vo);
			return $this->fetch();
		}
	}

}
