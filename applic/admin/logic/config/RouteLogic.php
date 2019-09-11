<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.zcphp.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-路由管理-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\config;

use app\admin\logic\Backend;
use think\facade\Request;
use think\Validate;

class RouteLogic extends Backend {
	
	//定义模型
	public $Model = null;
	//查询条件
	public $Where = [];
		
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\admin\model\ConfigRoute;
	}
	
	/**
	 * 验证表单输入
	 * @param $value	验证的数据
	 * @date 2018-08-16
	 */
	public function validate($value=false){
		$value = $value ? : Request::param();
		$rule = [
			'title' 	=> 'require',
			'expression'=> 'require',
			'address'	=> 'require',
			'type'		=> 'require',
		];
		$message = [
			'title.require' 	=> '路由名称 不能为空',
			'address.require' 	=> '路由地址 不能为空',
			'type.require' 		=> '请求类型 不能为空',
			'expression.require'=> '路由表达式 不能为空',
		];
		$Validate = Validate::make($rule,$message);
		if ( ! $Validate->check($value) ) {
			$this->error( $Validate->getError() );
		}
		return $this;
	}
		
}
