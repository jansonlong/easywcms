<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.zcphp.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Admin模块-系统管理-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic\config;

use app\admin\logic\Backend;
use think\facade\Request;
use think\Validate;

class Param extends Backend {
	
	//定义模型
	public $Model = null;
	//请求参数
	public $param = null;
		
    /**
     * 构造方法
     * @access public
     */
	public function __construct(){
		parent::__construct();
		$this->Model = new \app\admin\model\ConfigParam;
	}
	
	//设置请求参数
	public function setParam($value){
		$this->param = $value;
		return $this;
	}
	
	/**
	 * 验证表单输入
	 * @param $value 需要验证的数据
	 * @date 2018-08-17
	 */
	public function validate($value=false){
		$this->param = $value;
		$rule = [
			'title' 	=> 'require',
			'name' 		=> 'require|alpha',
		];
		$message = [
			'title.require' 	=> '变量标题 不能为空',
			'name.require' 		=> '变量名 不能为空',
			'name.alpha' 		=> '变量名 必须为字母',
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) 
        {
			$this->error( $Validate->getError() );
		}
		return $this;
	}
	
	/**
	 * 验证表单输入
	 * @param $value 需要验证的数据
	 * @date 2018-08-17
	 */
	public function validateGroup($value=false){
		$this->param = $value;
		$rule = [
			'name' 		=> 'require|alpha',
			'title' 	=> 'require'
		];
		$message = [
			'name.require' 		=> '名称 不能为空',
			'name.alpha' 		=> '名称 必须为字母',
			'title.require' 	=> '标题 不能为空'
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) {
			$this->error( $Validate->getError() );
		}
		return $this;
	}
	
	/**
	 * 添加参数字段
	 * @date 2018-09-13
	 */
	public function doAdd()
    {
		$config  = $this->param;
		$group_id= $config['group_id'];
		//将变量名进行驼峰转下划线
		if(isset($config['snake'])){
			$config['name'] = (new \think\helper\Str)->snake($config['name']);
			unset($config['snake']);
		}
		//验证是否已存在相同的变量名  
		$id = $this->Model->where([['group_id','=',$group_id],['name','=',$config['name']]])->value('id');
		if($id){
            $this->error( $config['name'].' '.lang('Already existed') );
		}
		try {
			//开启事务
			$this->Model->startTrans();
			//在config_param表添加一行数据
			$create = $this->Model->create($config);
			if( $create ){
				//创建配置文件
				$createConfigfile = $this->createConfigfile($group_id);
				//若发生错误回滚事务
				if($createConfigfile['code']==0){
					$this->Model->rollback();
                    $this->error( $createConfigfile['msg'] );
				}
				//提交事务
				$this->Model->commit();
                return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$create->id];
			}
		} catch (\Exception $e) {
			//回滚事务
			$this->Model->rollback();
            $this->error( lang('An unexpected error occurred').'<br>'.$e->getMessage() );
		}
	}
	
	/**
	 * 保存参数
	 * @date 2018-09-13
	 */
	function doEdit()
    {
		$config  = $this->param['config'];
		$group_id= $this->param['group_id'];
        //开启事务
        $this->Model->startTrans();
		try{
			//查询配置分组信息
			$config_group = db('config_group')->find($group_id);
			if($config_group){
				//域名部署 相关数据验证
				if($config_group['name']=='domain'){
					//处理admin模块的域名部署
					if( $config['admin'] != config('domain.admin') ){
						$ret = $this->check_config_admin($config['admin']);
						$url = $ret['data'];
						//清空菜单缓存
						cache('Auth_rule',null);
						cache('Auth_rule_min',null);
					}
				}
				//将参数配置循环写入config_param表
				foreach($config as $k=>$v){
					$this->Model->where([ ['group_id','=',$config_group['id']] , ['name','=',$k] ])->setField('value',$v);
				}
				//提交事务
				$this->Model->commit();
				//创建配置文件
				$createConfigfile = $this->createConfigfile($config_group['id']);
				//若发生错误回滚事务
				if($createConfigfile['code']==0){
					$this->Model->rollback();
                    $this->error( $createConfigfile['msg'] );
				}
                return ['code'=>1,'msg'=>lang('Operation completed'),'url'=>isset($ret)?$ret:''];
			}
		} catch (\Exception $e) {
            $this->Model->rollback();
            $this->error( lang('An unexpected error occurred').'<br>'.$e->getMessage() );
		}
	}
	
	/**
	 * 添加参数分组
	 * @date 2018-09-14
	 */
	function doAddGroup()
    {
		$param = $this->param;
		$name  = $param['name'];
		$title = $param['title'];
		if(empty($name)){
			return ['code'=>0,'msg'=>lang('Lack of parameters').' name'];
		}
		$Db = new \think\Db;
		try{
			//查询配置分组是否已存在
			$id = $Db::name('config_group')->where([['name','=',$name]])->value('id');
			if($id){
                $this->error( $name.' '.lang('Already existed') );
			//添加一条分组
			}else{
				$insert = ['name'=>$name,'title'=>$title];
				$insert['id'] = $Db::name('config_group')->strict(false)->insertGetId($insert);
                return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$insert];
			}
		} catch (\Exception $e) {
            $this->error( lang('An unexpected error occurred').'<br>'.$e->getMessage() );
		}
	} 
	
	/**
	 * 创建配置文件
	 * @param $data
	 */
	private function createConfigfile($group_id)
    {
		$Db = new \think\Db;
		$group = $Db::name('config_group')->field('name,title')->where([['id','=',$group_id]])->find();
		$filename = $group['name'];
		$datas = $this->Model->where([['group_id','=',$group_id]])->select()->toArray();
		foreach($datas as $k=>$v){
			if( $v['type']=='number' || $v['type']=='boolean' ){
				$config[$v['name']] = (int)$v['value'];
			}else{
				$config[$v['name']] = $v['value'];
			}
		}
		try {
			if(!$filename || !is_array($config)) return ['code'=>0,'msg'=>lang('Lack of parameters').': filename'];
			if(!is_array($config)) return ['code'=>1];
			$filename = WCMS_PATH . 'config/'.$filename.'.php';
			//删除原文件
			@unlink($filename);
			$filedata = var_export($config,true);
			//写入文件
			file_put_contents($filename,"<?php \n //{$group['title']} \n return ".$filedata.';');
		} catch (\Exception $e) {
			return ['code'=>0,'msg'=>lang('Please check directory permissions').'<br>'.$e->getMessage()];
		}
		return ['code'=>1];
	}
	
	/**
	 * 验证admin模块域名部署
	 * @date 2018-10-12
	 */
	private function check_config_admin($value)
    {
		$field = 'config[admin]';
		if($value){
			//验证器
			$validate = Validate::make([
				$field => 'url'
			]);
			$data = [
				$field => $value
			];
			if (!$validate->check($data)) {
                $this->error( $validate->getError() );
			}
			//再次验证
			$url = parse_url( trim($value) );
			if( !empty($url['host']) ){
				$url['scheme'] = $url['scheme'] ? : 'http';
				$data = url('/console','','',$url['scheme'].'://'.$url['host']);
			}else{
                $this->error( str_replace(':attribute',$field.' ',lang(':attribute not a valid url')) );
			}
		}else{
			$data = '/admin/console';
		}
		return ['code'=>1,'data'=>$data];
	}
	
	
}
