<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 插件配置
// +----------------------------------------------------------------------
namespace addons\baiduue;

use think\Addons;

class Baiduue extends Addons{
    
	// 该插件的基础信息
    public $info = [
        'name'          => 'baiduue',	// 插件标识
        'title'         => '百度编辑器',	// 插件名称
        'description'   => 'UEditor是百度的一个javascript编辑器的开源项目，能带给您良好的富文本使用体验',	// 插件简介
        'author'        => '官方',
        'version'       => '0.1',
        'remarks'       => '不要删除哦....'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
		return true;
    }

    /**
     * 实现的钩子方法
     * @return mixed
     */
    public function baiduue($param)
    {
		return true;
    }

}