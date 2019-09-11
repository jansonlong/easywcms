<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 附件上传-模型
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\model\concern\SoftDelete;
use think\Model;

class Annex extends Model{
	
	//软删除
	use SoftDelete;	
	//自动填充时间戳
	protected $autoWriteTimestamp = true;
		
	//只读字段
	//protected $readonly = ['deleting'];
	
	//设置json类型字段
	//protected $json = ['skin_data'];
	
	//自动完成
	//protected $insert = ['listorder' => 0 , 'status' => 1, 'deleting' => 1];
	
	//数组对象转换数组
//	protected function getSkinDataAttr($value){
//		return get_object_vars($value);
//	}
	
}
