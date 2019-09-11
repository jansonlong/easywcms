<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-管理员-数据层
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;
use easywcms\Easy;
use think\model\concern\SoftDelete;

class Sysadm extends Model{
	
	//软删除
	use SoftDelete;
    //自动过滤掉不存在的字段
    protected $field = true;
	//隐藏敏感的字段
	protected $hidden = ['password','usersign'];
	//只读字段
	protected $readonly = ['usersign','deleting'];
	//自动填充时间戳
	protected $autoWriteTimestamp = true;
	//自动完成
	protected $insert 	= ['status' => 1, 'deleting' => 1,'usersign'];
	protected $update  	= ['lastip'];
	//签名
	protected $usersign = '';
	
	//查询时.转换时间格式
    protected function getLastlogintimeAttr($value){
        return $value ? date('Y-m-d H:i:s',$value) : '-';
    }
	
	//自动完成IP填写
    protected function setLastipAttr(){
        return request()->ip();
    }
	
	//清除账户前后空格
    protected function setUsernameAttr($value){
        return trim($value);
    }
	
	//添加时自动完成签名的输入
	protected function setUsersignAttr($value,$data){
		return substr(Easy::sysmd5($data['username']),1,10);
	}
	
	//自动完成密码
    protected function setPasswordAttr($value,$data){
		//修改管理员
		if(isset($data['id'])){
			$ret = Sysadm::field('usersign,password')->find($data['id']);
			//当输入密码,则更新密码
			return empty($value) ? $ret['password'] : Easy::sysmd5(trim($value),$ret['usersign']);
		//添加管理员
		}else{
			//获取签名
			$usersign = $this->setUsersignAttr(false,$data);
			return $ret['password'] = Easy::sysmd5($value?trim($value):'888888',$usersign);
		}
    }
	
	//关联模型
    public function AuthRole(){
        return $this->hasOne('AuthRole','id','auth_role_id')->field('id,title');
    }
	
}
