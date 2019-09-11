<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 路由管理-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\config;

use app\admin\controller\Admin;

class Route extends Admin {

	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\config\RouteLogic;
        //更新缓存
       if(  $this->request->isPost() ){
            cache('ConfigRoute',null); 
        }
    }
    
    //更新缓存
    public function upcache(){
        cache('ConfigRoute',null);
        return ['code'=>1,'msg'=>'更新成功'];
    }
			
}