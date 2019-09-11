<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 系统参数-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\config;

use app\admin\controller\Admin;
use app\admin\model\ConfigSite;
use app\admin\model\ConfigParam;

class Param extends Admin {
	
	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\config\Param;
    }
	
	//参数配置
	private function get_config_data(){
		try{
			//参数分组
			$group = db('config_group')->select();
			foreach($group as $k=>$v){
				$v['data'] = ConfigParam::where([['group_id','=',$v['id']]])->select()->toArray();
				$config[] = $v;
			}
			$this->assign('config',$config);
		} catch (\Exception $e) {
			exit($e->getMessage());
		}
	}
	
	//列表
    function index(){
		$this->get_config_data();
		return $this->fetch();
	}
	
	/**
	 * 添加参数分组
	 * @date 2018-09-14
	 */
	function addGroup()
    {
		return $this->logic->validateGroup($this->request->param())->doAddGroup();
	} 
	
	/**
	 * 添加参数字段
	 * @date 2018-09-13
	 */
	function add()
    {
		return $this->logic->validate($this->request->param('config'))->doAdd();
	}
	
	/**
	 * 保存参数
	 * @date 2018-09-13
	 */
	function edit(){
		return $this->logic->setParam($this->request->param())->doEdit();
	}


}
