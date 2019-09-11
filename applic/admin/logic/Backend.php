<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-公共逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic;

use think\facade\Request;

class Backend {
	
    //容器对象实例
    protected static $instance;
	
	//定义模型
	public $Model 	= false;
	//查询字段
	public $Field 	= '*';
	//查询条件
	public $Where 	= '';
	public $WhereOr = '';
	//排序方式
	public $Order 	= '';
	//隐藏字段
	public $Hidden 	= [];
    //分组
	public $Group 	= '';
	//表主键 默认id
	public $PK 		= 'id';
	//预载入
	public $with 	= false;
	//根据关联数据查询
	public $hasWhere= false;
	//关联统计
	public $withCount= false;
	//过滤post数组中的非数据表字段数据
	//post数组中只有name和email字段会写入
	//['name','email']
	public $allowField = true;
	
	public $ToArray = false;
		
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){

	}
	
    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance(){
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }
	
	//过滤post数组中的非数据表字段数据
	public function setAllowField($value){
		$this->allowField = $value;
		return $this;
	}
	
	//设置查询字段
	public function setField($value){
		$this->Field = $value;
		return $this;
	}
	
	//设置查询条件
	public function setWhere($value){
		$this->Where = $value;
		return $this;
	}
	
	//
	public function setWhereOr($value){
		$this->WhereOr = $value;
		return $this;
	}
	
	//设置排序条件
	public function setOrder($value){
		$this->Order = $value;
		return $this;
	}
	
	//设置关联查询
	public function setWith($value){
		$this->with = $value;
		return $this;
	}
	
	//设置关联统计
	public function setWithCount($value){
		$this->withCount = $value;
		return $this;
	}
	
	//隐藏敏感字段
	public function setHidden($value=[]){
		$this->Hidden = $value;
		return $this;
	}
    
	//分组排序
	public function setGroup($value=[]){
		$this->Group = $value;
		return $this;
	}
	
	//对象转数组
	public function setToArray($value=true){
		$this->ToArray = $value;
		return $this;
	}
	
	//获取表主键
	private function getModelPk(){
		$this->PK = $this->Model->getPk();
		return $this;
	}
	
	//返回json数据
	public function ajaxReturn($value)
    {
		header('Content-Type:application/json');
		exit( json_encode($value) );
	}
    
	//返回成功的json数据
	public function success($msg='',$url='',$data='',$wait=3)
    {
        $msg == '' && $msg = lang('Operation completed');
		header('Content-Type:application/json');
		exit( json_encode(['code'=>1,'msg'=>$msg,'url'=>$url,'data'=>$data,'wait'=>$wait]) );
	}
    
	//返回错误的json数据
	public function error($msg='',$url='',$data='',$wait=3)
    {
		header('Content-Type:application/json');
		exit( json_encode(['code'=>0,'msg'=>$msg,'url'=>$url,'data'=>$data,'wait'=>$wait]) );
	}
	
	/**
	 * 定义空的验证器
	 * @param $value 
	 * @date 2018-08-17
	 */
	public function validate($value=false){
		return $this;
	}
		
	/**
	 * 获取表的字段缓存
	 * @date 2018-08-23
	 */
	public function getFieldsCache(){
		//获取表字段, 并缓存
		$fields = cache($this->Model.'_fields');
		//缓存不存在, 则创建
		if( empty($fields) ){
			$fields = $this->Model->getTableFields();
			cache($this->Model.'_fields',$fields);
		}
		return $fields;
	}
	
	/**
	 * 按分页读取
	 * @date 2018-08-20
	 */
	public function getPage()
	{
		//关联统计
		$count = $this->Model;
		if($this->hasWhere!=false){
			$count = $count->hasWhere($this->hasWhere[0],$this->hasWhere[1]);
			if($this->with!=false){
				$count->with($this->with);
			}
		}
		//查询为空的字段
		$count = $count->where($this->Where)->group($this->Group)->count();
		//分页查询数据
		if($count > 0){
			//页码 默认第1页
			$page = (Request::param('page/d',1))-1;
			//每页显示条数 默认page_list_limit
			$listRows = Request::param('limit/d' , config('page_list_limit') );
			//根据关联数据查询
			if($this->hasWhere!=false){
				$data = $this->Model->hasWhere($this->hasWhere[0],$this->hasWhere[1]);
				if($this->with!=false){
					$data->with($this->with);
				}
			}else{
				//设置关联预载入
				$data = $this->Model->field($this->Field)->with($this->with);
			}
			//关联统计
			if($this->withCount!=false){
				$data = $data->withCount($this->withCount);
			}
			//按分页查询
			$data = $data->where($this->Where)
						->whereOr($this->WhereOr)
						->order($this->Order)
                        ->group($this->Group)
						->limit($page*$listRows.','.$listRows)
						->select()
						->hidden($this->Hidden)
						->toArray();
			//显示最后一条SQL语句
			//$getLastSql = $this->Model->fetchSql();
			//返回数据
			return ['code'=>1,'count'=>$count,'data'=>$data];
		}
		return ['code'=>1,'msg'=>lang('None'),'count'=>0];	
	}
	
	/**
	 * 普通查询
	 * @date 2019-02-23
	 */
	public function doSelect($field='*',$toArray = false)
	{
		$data = $this->Model->field($field)->where($this->Where);
		if( $this->Order ){
			$data = $data->orderRaw($this->Order);
		}else if($this->with){
			$data = $data->with($this->with);
		}
        if($toArray){
            return $data->select()->toArray();
        }else{
            return $data->select();
        }
	}
	
	/**
	 * 统计
	 * @date 2019-02-23
	 */
	public function doCount($field='id')
	{
		return $this->Model->where($this->Where)->count($field);
	}
	
	/**
	 * 以条件获取单个数据
	 * @param char $model 模型
	 * @param int $id 主键
	 * @date 2018-08-05
	 */
	public function doFind()
	{
		try{
			if( is_array($this->Where) ){
				$ret = $this->Model->field($this->Field)->where($this->Where)->find();
			}
			return ( isset($ret) && $ret ) ? $ret : ['code'=>0,'msg'=>lang('Invalid parameters')];
		} catch (\Exception $e) {
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}
	
	/**
	 * 以主键获取单个数据
	 * @param int $id 主键
	 * @date 2018-08-05
	 */
	public function doGet()
	{
		try{
			$id = Request::param('id/d');
			$id && $ret = $this->Model->field($this->Field)->get($id);
			return ( isset($ret) && $ret ) ? $ret->toArray() : ['code'=>0,'msg'=>lang('Invalid parameters')];
		} catch (\Exception $e) {
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}
	
	/**
	 * 添加
	 * @date 2018-08-17
	 */
	public function doAdd()
	{
		$post = Request::except('id');
		if( !$this->Model ) return ['code'=>0,'msg'=>lang('Invalid parameters')];
		try {
			//开启事务
			$this->Model->startTrans();
			//获取表主键
			$this->getModelPk();
			$pk = $this->PK;
			//循环将array的类型数据转成json
			foreach($post as $key=>$val){
				if( is_array($val) ){
					$post[$key]= json_encode($val);
				}
			}
			$save = $this->Model->create($post);
			if($save->$pk){
				//提交事务
				$this->Model->commit();
				return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$save->$pk];
			}
		} catch (\Exception $e) {
			//回滚事务
			$this->Model->rollback();
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}

	/**
	 * 编辑
	 * @date 2018-08-10
	 */
	public function doEdit()
	{
		$id   = Request::param('id/d');
		$post = Request::post();
		if( empty($id) || !$this->Model ) return ['code'=>0,'msg'=>lang('Lack of parameters').' id'];
		try {
			//开启事务
			$this->Model->startTrans();
			//获取表主键
			$this->getModelPk();
			//循环将array的类型数据转成json
			foreach($post as $key=>$val){
				if( is_array($val) ){
					$post[$key] = json_encode($val);
				}
			}
			if($this->Model->allowField($this->allowField)->save($post,[$this->PK=>$id]) !== false){
				//提交事务
				$this->Model->commit();
				return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$id];
			}
		} catch (\Exception $e) {
			//回滚事务
			$this->Model->rollback();
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}

	/**
	 * 删除 / 批量删除
	 * @date 2018-08-10
	 */
	public function doDelete()
	{
		$id = Request::param('id');
		if( empty($id) || ! $this->Model ) return ['code'=>0,'msg'=>lang('Invalid parameters')];
		try {
			//开启事务
			$this->Model->startTrans();
			//模型对象
			$Model = $this->Model;
			//循环删除
			if( is_array($id) ){
				foreach($id as $v){
					$Model::destroy($v);
				}
			//删除单个
			}else{
				$Model::destroy($id);
			}
			$this->Model->commit();
			return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$id];
		} catch (\Exception $e) {
			//回滚事务
			$this->Model->rollback();
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}
	
	/**
	 * 批量设置
	 * @param array $post['list']  
	 * 	 参数示例1 一维数组
	 * 	 $post['list'] = {id:1,field:1,field2:1,...}
	 * 	 参数示例2 二维数组
	 * 	 $post['list'] = [
	 * 		{id:1,field:1,field2:1,...},
	 * 		{id:2,field:2,field2:2,...},
	 * 		{id:3,field:3,field3:3,...},
	 * 		...
	 * 	 ]
	 * 注意：list 数组中必须包含数据表主键 默认为id
	 * @date 2018-11-06
	 */
	public function doMulti()
	{
		$_list = Request::post('list');
		$list = array();
		//一维数组
		if(count($_list) == count($_list,1)){
			 $list[0] = $_list;
		//二维数组
		}else{
			foreach($_list as $v){
				$list[] = $v;
			}
		}
		try {
			//开启事务
			$this->Model->startTrans();
			//判断数组中是否存在主键
			if( ! isset($list[0]['id']) ){
				//获取表主键
				$this->getModelPk();
				if( ! isset($list[0][$this->PK]) ){
					return ['code'=>0,'msg'=>lang('Lack of primary keys')];
				}
			}
			//批量更新
			if( $this->Model->saveAll($list) !== false) {
				//提交事务
				$this->Model->commit();
				return ['code'=>1,'msg'=>lang('Operation completed')];
			}else{
				return ['code'=>0,'msg'=>lang('An unexpected error occurred')];
			}
		} catch (\Exception $e) {
			//回滚事务
			$this->Model->rollback();
			return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
		}
	}
	
}
