<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-操作日志
// +----------------------------------------------------------------------
namespace app\admin\logic\auth;

use app\admin\logic\Backend;

class AuthLogLogic extends Backend {
	
	//定义模型
	public $Model = null;
	
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\common\model\AuthLog;
	}

}