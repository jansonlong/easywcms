<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 菜单规则-共用逻辑
// +----------------------------------------------------------------------
namespace app\common\logic;

use easywcms\Easy;
use think\facade\Env;
use app\common\model\AuthRule;

class Menu 
{
    
    /**
     * 创建菜单
     * @param array $menu
     */
    public static function create($menu,$module,$parent_id=-1)
    {
        if( !is_array($menu) || empty($module) ){
            return false;
        }
        foreach($menu as $leaf) {
            if($parent_id>=0){
                $leaf['parent_id'] = $parent_id;
            }
            $children = isset( $leaf['children'] ) ? $leaf['children'] : false;
            //删除不需要的数据
            unset($leaf['children']);
            //模块
            $leaf['module'] = 'addons/'.$module;
            //入库
            $rule = AuthRule::create($leaf);
            //写入子菜单
            if( $children ){
                self::create($children,$module,$rule->id); 
            }
        } 
    }
    
    /**
     * 按分组删除菜单
     * @param string $name
     */
    public static function delete($name)
    {
        $module = 'addons/'.$name;
        AuthRule::destroy(['module'=>$module],true);
        //更新菜单缓存
        Easy::savecache('auth_rule');
    }
    
}