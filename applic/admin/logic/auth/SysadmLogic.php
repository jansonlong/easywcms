<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.zcphp.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-管理员-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\auth;

use app\admin\logic\Backend;

class SysadmLogic extends Backend {
	
	//定义模型
	public $Model = null;
		
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\common\model\Sysadm;
	}
	
	//设置预载入
	public function setWith($value){
		$this->with = $value;
		return $this;
	}

	/**
	 * 验证表单输入
	 * @param $value 需要验证的数据
	 * @date 2018-08-17
	 */
	public function validate($value=false){
		$value = $value ? : input('post.');
		$rule = [
			'username' 		=> 'require',
			'realname' 		=> 'require',
		];
		$message = [
			'username.require' 		=> '账户 不能为空',
			'realname.require' 		=> '姓名 不能为空',
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) {
			$this->error( $Validate->getError() );
		}
		return $this;
	}
	
	//验证两次输入的密码是否是一至
	function validatePW($param){
		$password = $param['password'];
		$confirmp = $param['confirmp'];
		if( !empty($password) || !empty($confirmp) ){
			if( $password != $confirmp ){
				$this->error( lang('Two password inconsistency') );
			}
		}
		return $this;
	}
	
	
}
