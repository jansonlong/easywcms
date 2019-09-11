<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 皮肤管理-控制层
// +----------------------------------------------------------------------
namespace app\admin\controller\config;

use app\admin\controller\Admin;

class Skin extends Admin {
		
	//指定方法调用中间件
	protected $middleware = [
		'\app\admin\middleware\Config_skin' => ['only' => ['add','edit','delete','updatecss'] ],
	];
		
	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\config\Skin;
    }
	
	//批量 更新 css 文件
	public function updatecss()
    {
		$this->success();
	}
    
}