<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Home模块 首页
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Controller;

class Index extends Controller
{
	
    public function index()
    {
		if(is_file(WCMS_PATH . 'applic/install/install.lock')){
            $this->view->engine->layout(false);
            return $this->fetch();
		}else{
            $this->redirect("/Install");
        }
    }
    
}