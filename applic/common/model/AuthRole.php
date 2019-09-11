<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 角色组-数据层
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class AuthRole extends Model{
	
	//软删除
	use SoftDelete;	
	
    //自动过滤掉不存在的字段
    protected $field = true;
	//自动填充时间戳
	protected $autoWriteTimestamp = true;
	//只读字段
	protected $readonly = ['deleting'];
	//自动完成
	protected $insert 	= ['status' => 1, 'deleting' => 1];
	//自动完成rules
    protected function setRulesAttr($value){
		if( is_array($value) ){
			sort($value);
		}else{
			$value = json_decode($value,true);
		}
		sort($value);
		$value = implode(',',$value);
		return $value;
    }
	
}