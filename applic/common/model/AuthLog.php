<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-操作日志-模型
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;

class AuthLog extends Model{
		
	//自动填充时间戳
	protected $autoWriteTimestamp = true;
	//隐藏敏感字段
	//protected $hidden = ['param'];
	
}
