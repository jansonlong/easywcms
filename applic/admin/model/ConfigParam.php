<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-配置参数-数据层
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class ConfigParam extends Model{

	
    public function Module(){
        return $this->hasOne('Module', 'id', 'module_id')->cache(true,10);
    }
	
	
}