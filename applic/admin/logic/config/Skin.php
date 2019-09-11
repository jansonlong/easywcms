<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.zcphp.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-皮肤管理-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\config;

use app\admin\logic\Backend;

class Skin extends Backend {
	
	//定义模型
	public $Model = null;
		
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\admin\model\ConfigSkin;
	}
			
}
