<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 常用方法
// +----------------------------------------------------------------------
namespace easywcms;

use think\facade\Session;
use think\facade\Config;
use think\facade\Cache;
use think\Db;

class Easy{
	
	/**
	 * 获取配置信息
	 */
	public static function getConfig($key){
		if( empty($key) ){
			return false;
		}else{
			return Config::get($key);
		}
	}
	
	/**
	 * 设置Session
	 */
	public static function setSession($key,$val='',$field=false){
		if( empty($key) ){
			return false;
		}else if( $field ){
			return Session::set($key,$val,$field);
		}else{
			return Session::set($key,$val);
		}
	}
	
	/**
	 * 获取Session
	 */
	public static function getSession($key,$field=false){
		if( empty($key) ){
			return false;
		}else if( $field ){
			return Session::get($key,$field);
		}else{
			return Session::get($key);
		}
	}
	
	/**
	 * 读取指定缓存
	 * @param $name 缓存名称
	 * @param $key 缓存键
	 * @param $val 指定缓存键值 若不传.则返回整个键的所有值
	 */
	public static function getCache($name,$key=false,$val=false){
		$data = Cache::get($name);
		if(!$data){
			self::savecache($name);
			return Cache::get($name);
		}else{
			if($key===false&&$val===false){
				return $data;
			}else{
				if(!isset($data[$key])) return $data;
				if($val){
					return $data[$key][$val];
				}else{
					return $data[$key];
				}
			}

		}
	}
	
	/**
	 * 设置Cache
	 */
	public static function setCache($key,$value,$time=false){	
		if( empty($key) ){
			return false;
		}else if( $key && $time ){
			return Cache::set($key,$value,$time);
		}else{
			return Cache::set($key,$value);
		}
	}
	
	/**
	 * 删除Cache
	 */
	public static function rmCache($key){	
		Cache::rm($key);
	}
		
	/**
	 * 返回json数据
	 * @access private
	 * @return json
	 */
	public static function ajaxReturn($value){
		header('Content-Type:application/json');
		exit( json_encode($value) );
	}

	//自定义md5
	public static function sysmd5($str,$key=false,$type='sha1'){
		$key = $key ? $key : 'f16e092096364f577199e97cc5ffd';
		return hash($type,$str.$key);
	}

	public static function getCatlist(){
		$cat = (new \app\admin\model\CategoryModel)->field('cat_id,parent_id,cat_name')->where(array('status'=>1))->select();
		$cat = (new \app\admin\logic\CategoryLogic)->getTree($cat);
		foreach ($cat as $k => $v) {
			$cat[$k]['cat_name'] = $v['str'] . $v['cat_name'];
		}
		return $cat;
	}
    
	/**
	 * 字段中文
	 * @return array
	 */
    public function ruleFieldReplace($key)
    {
        $arr = [
            'null'=>'',
            'required'=>'require',
            'phone'=>'mobile'
        ];
        if( !empty($key) && isset($arr[$key]) ){
            return $arr[$key];
        }
        return $key;
    }
    
	/**
	 * 字段中文
	 * @return array
	 */
	public static function getFieldTest($value=false)
    {
		$arr = [
			'module'	=> '模块名称',
			'model'		=> '模型名称',
			'type'		=> '类型',
			'catid'		=> '栏目ID',
			'title'		=> '标题',
			'keywords'	=> '关键词',
			'description'=> '描述',
			'pid'		=> '上级ID',
			'url'		=> 'url地址',
			'thumb'		=> '小图',
			'image'		=> '大图',
			'sex'		=> '性别',
			'status'	=> '状态',
			'username'	=> '用户名称',
			'realname'	=> '真实姓名',
			'auth_group_id'	=> '用户组ID',
		];
		return $value ? $arr[$value] : $arr;
	}

	/* 
	* 循环检测并创建文件夹 
	* @param $dir 文件夹路径 
	* @param $mode 权限 
	*/ 
	public static function mk_dir($dir, $mode = 0755){
		if (is_dir($dir) || @mkdir($dir,$mode)) return true; 
		if (!self::mk_dir(dirname($dir),$mode)) return false; 
		return @mkdir($dir,$mode); 
	}

	/* 
	* 删除非空目录的解决方案 
	* @param $dirName 文件夹路径 
	*/ 
	public static function removeDir($dirName){ 
		if(! is_dir($dirName)){ 
			return false; 
		} 
		$handle = @opendir($dirName); 
		while(($file = @readdir($handle)) !== false){ 
			if($file != '.' && $file != '..'){ 
				$dir = $dirName . '/' . $file; 
				is_dir($dir) ? self::removeDir($dir) : @unlink($dir); 
			} 
		} 
		closedir($handle);
		return rmdir($dirName) ; 
	}
	
	/**
	 * 获取数据库所有表名
	 * @param $model 模型
	 * @param $order 排序方式
	 * @param $where 查询条件
	 */
	public static function getAllTables(){
		return db()->query('SHOW TABLE STATUS');
	}

	/**
	 * 生成缓存
	 * @param $model 模型
	 * @param $order 排序方式
	 * @param $where 查询条件
	 */
	public static function savecache($model,$order=false,$where=[['status','=',1]]) {
		$model = strtolower($model);
		try {
			if( empty($model) ) return false;
			$data = array();
			//缓存数据库所有表信息
			if($model=='DB_TABLES' || $model=='DB_TABLES_FIELDS'){
				$list = Db::query('SHOW TABLE STATUS');
				foreach( $list as $key => $val ) {
					$data[$val['Name']] = $val;
					$fields[$val['Name']] = Db::table($val['Name'])->getTableFields();
				}
				Cache::set('DB_TABLES',$data);
				Cache::set('DB_TABLES_FIELDS',$fields);
				unset($data);
				unset($fields);
				return true;
			}
			//用户组
			if( $model=='auth_role' ){
				$AuthRole = new \app\common\model\AuthRole;
				$dbPk = $AuthRole->getPk();
				$order= $order ? $order : 'listorder desc,'.$dbPk;
				$list = $AuthRole->select();
				foreach( $list as $key => $val ) {
					$data[$val[$dbPk]] = $val;
				}
				Cache::set('Auth_role',$data);
			//权限规则
			}else if($model=='auth_rule' || $model=='auth_rule_min'){
				$AuthRule = new \app\common\model\AuthRule;
				$dbPk = $AuthRule->getPk();
				$mindata = array();
				$field = 'id,parent_id,menutype,module,btnclass,fontico,name,parameter,title,status,description,listorder';
				$list = $AuthRule->field($field)->orderRaw("listorder!=0 desc,listorder,id asc")->select()->toArray();
				foreach( $list as $key => $val ) {
					$data[$val[$dbPk]] = $val;
					$mindata[$val['module'].$val['name']] = $val;
				}
				Cache::set('Auth_rule',$data);
				Cache::set('Auth_rule_min',$mindata);
				unset($mindata);
			}
			unset($list);
			unset($data);
		} catch (Exception $e) {
			self::ajaxReturn( ['code'=>0,'msg'=>lang('Error in caching data')] );
		}
	}

	/**
	 * 字段类型
	 * @param $name
	 */
	public static function setFieldType($type,$length=false){
		if($type){
			$type = strtolower($type);
		}else{
			ajaxReturn(['code'=>0,'msg'=>lang('Lack of parameters').': type']);
		}
		$array = [
			'string'  => ['type'=>'VARCHAR',	'length'=>$length?$length:50],
			'text' 	  => ['type'=>'TEXT',		'length'=>''],
			'editor'  => ['type'=>'TEXT',		'length'=>''],
			'number'  => ['type'=>'INT' ,		'length'=>$length?$length:10],
			'date'    => ['type'=>'DATE',		'length'=>''],
			'time'    => ['type'=>'TIME',		'length'=>''],
			'select'  => ['type'=>'TEXT',		'length'=>''],
			'selects' => ['type'=>'TEXT',		'length'=>''],
			'image'   => ['type'=>'VARCHAR',	'length'=>$length?$length:100],
			'images'  => ['type'=>'TEXT',		'length'=>''],
			'file'    => ['type'=>'VARCHAR',	'length'=>$length?$length:100],
			'files'   => ['type'=>'TEXT',		'length'=>''],
			'radio'   => ['type'=>'TEXT',		'length'=>''],
			'checkbox'=> ['type'=>'TEXT',		'length'=>''],
			'datetime'=> ['type'=>'DATETIME',	'length'=>'']
		];
		return $array[$type];
	}

	/**
	 * 创建数据表字段
	 * @param $data
	 */
	public static function createField($data){
		try{
			$table   = config('database.prefix') . $data['table'];
			$length  = $data['length'] ? "(" .$data['length']. ")" : '';
			$default = $data['default'] ? $data['default'] : 'NULL';
			$comment = $data['comment'] ? $data['comment'] : '';
			Db::execute("ALTER TABLE `" .$table. "` ADD `" .$data['name']. "` " .$data['type'].$length. " DEFAULT '" .$default. "' COMMENT '" .$comment. "';");
		} catch (Exception $e) {
			exit(json_encode(['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()]));
		}	
	}
	
	/**
		 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
		 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
		 +----------------------------------------------------------
	 * @return string
		 +----------------------------------------------------------
	 */
	public static function rand_string($len = 6, $type = 0, $addChars = '') {
		$str = '';
		switch ($type) {
			case 0 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz23456789' . $addChars;
				break;
			case 1 :
				$chars = str_repeat ( '0123456789', 3 );
				break;
			case 2 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
				break;
			case 3 :
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz' . $addChars;
				break;
		}
		if ($len > 10) { //位数过长重复字符串一定次数
			$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
		}
		if ($type != 4) {
			$chars = str_shuffle ( $chars );
			$str = substr ( $chars, 0, $len );
		} else {
			// 中文随机字
			for($i = 0; $i < $len; $i ++) {
				$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
			}
		}
		return $str;
	}


}