<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-路由管理-数据层
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\model\concern\SoftDelete;
use think\Model;

class ConfigRoute extends Model{
	
	//软删除
	use SoftDelete;
	
    // 设置json类型字段
    protected $json = ['append','pattern'];
    
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
    
}