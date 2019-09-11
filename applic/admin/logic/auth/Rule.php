<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-菜单规则-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\auth;

use app\admin\logic\Backend;
use easywcms\base\Tree;

class Rule extends Backend {
			
	//定义排序方式
	public $orderRaw = "listorder!=0 desc,listorder,id asc";
		
	//定义模型
	public $Model = null;
	
	//查询字段
	public $Field = '*';
	
	//查询条件
	public $Where = [];
    	
	//初始化逻辑层
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\common\model\AuthRule;
	}
	
	/**
	 * 验证表单输入
	 * @param $value	验证的数据
	 * @date 2018-08-16
	 */
	public function validate($value=false){
		$value = $value ? : input('post.');
		$rule = [
			'parent_id' => 'require',
			'menutype' 	=> 'require',
			'title' 	=> 'require',
			'name' 		=> 'require'
		];
		$message = [
			'parent_id.require' => '请选择 上级菜单',
			'menutype.require' 	=> '请选择 菜单类型',
			'title.require' 	=> '请填写 菜单名称',
			'name.require' 		=> '请填写 菜单规则',
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if (!$Validate->check($value)) {
			exit( json_encode(['code'=>0,'msg'=>$Validate->getError()]) );
		}
		return $this;
	}
	
	/**
	 * 获取数据 JSON 格式
	 * @date 2018-10-18
	 */
	public function getPage(){
		$arr  = $this->Model->orderRaw($this->orderRaw)->where($this->Where)->select()->toArray();
		$tree = new Tree($arr);
		$data = $tree->get_tree(0,"\$spacer\$title",'alias',' ');
		return ['code'=>1,'data'=>$data];
	}
	
	/**
	 * 获取数据 数组格式
	 * @date 2018-10-18
	 */
	public function data($title = "\$spacer::: \$title"){
		$arr  = $this->Model->field($this->Field)->orderRaw($this->orderRaw)->where($this->Where)->select()->toArray();
		$tree = new Tree($arr);
		$tree->icon = array('│','├','└');
		$data = $tree->get_data(0,$title,'title',' ');
		return ['code'=>1,'data'=>$data];
	}
	
	/**
	 * 获取指定菜单规则的子操作
	 * @return json or array
	 * @date 2018-08-05
	 */
	public function getReleson($parent_id){
		$where = [['menutype','=',0]];
		!empty($parent_id) && $where[1] = ['parent_id','=',$parent_id];
			
		$data =$this->Model->orderRaw($this->orderRaw)->where($where)->select()->toArray();
		return json(['code'=>1,'data'=>$data]);
	}
	
}
