<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 控制台
// +----------------------------------------------------------------------
namespace app\admin\controller;

use easywcms\Easy;
use think\facade\Cache;
use think\Db;

class Console extends Admin {
		
	//控制台首页
	public function index()
    {        
		//获取个人拥有权限的菜单
		$this->auth->getMenu();
		$this->assign(['userinfo'=>$this->userinfo,'timestamp'=>time()]);
		return $this->fetch();
	}
	
	//选择皮肤
	public function selectskin()
    {
		$listsdata = \app\admin\model\ConfigSkin::limit(0,12)->select()->toArray();
		return $this->assign('configskin',$listsdata)->fetch('config/skin/select');
	}
	
	//欢迎页
	public function welcome()
    {
        //统计管理员数量
        $sysadm_conut = \app\common\model\Sysadm::count('id');
        //统计菜单数
        $menu_conut = \app\common\model\AuthRule::count('id');
        //统计皮肤数
        $skin_conut = \app\admin\model\ConfigSkin::count('id');
        //统计插件数
        $addons_conut = \app\admin\model\Addons::count('id');
        //查询mysql 版本
        $MYSQL_VERSION = Db::query('SELECT VERSION()')[0]['VERSION()'];
        //
        $this->assign([
            'sysadm_conut'  => $sysadm_conut,
            'menu_conut'    => $menu_conut,
            'skin_conut'    => $skin_conut,
            'addons_conut'  => $addons_conut,
            'userinfo'      => $this->userinfo,
            'MYSQL_VERSION' => $MYSQL_VERSION
        ]);
		return $this->fetch();
	}
	
	//更新缓存
	public function upcache(){
		@set_time_limit(1500);
		$modelname = $this->request->param('modelname');
		//更新指定的缓存
		if($modelname){
			Easy::savecache($modelname);
		//批量更新
		}else{
			$models = array('Auth_group','Auth_rule');
			foreach ($models as $r) {
				Easy::savecache($r);
			}
		}
		return ['code'=>1,'msg'=>'缓存更新成功'];
	}

}
