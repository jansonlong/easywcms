<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-角色组-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\auth;

use app\admin\logic\Backend;

class Role extends Backend {
	
	//定义模型
	public $Model = null;
	
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\common\model\AuthRole;
	}

	/**
	 * 验证表单输入
	 * @param $value	验证的数据
	 * @date 2018-08-17
	 */
	public function validate($value=false){
		$value = $value ? : input('post.');
		$rule = [
			'title' 		=> 'require',
			'description' 	=> 'require'
		];
		$message = [
			'title.require' 		=> '分组名称 不能为空',
			'description.require' 	=> '描述信息 不能为空'
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) {
			$this->error( $Validate->getError() );
		}
		return $this;
	}
	

}