<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 安装模块
// +----------------------------------------------------------------------
namespace app\install\logic;

use think\Db;
use think\facade\Env;

class Index  
{
    
	/**
	 * 验证表单输入
	 * @param $value	验证的数据
	 * @date 2019-07-10
	 */
	public function validate($value=false)
    {
		$value = $value ? : input('post.');
		$rule = [
			'hostname' 	 => 'require',
			'hostport' 	 => 'require',
			'database' 	 => 'require',
			'username' 	 => 'require',
			'password' 	 => 'require',
			'prefix' 	 => 'require',
			'adminuser'  => 'require',
			'adminpwd' 	 => 'require'
		];
		$message = [
			'hostname.require' 		=> '数据库服务器 不能为空',
			'hostport.require' 		=> '数据库端口号 不能为空',
			'database.require' 		=> '数据库名称 不能为空',
			'username.require' 		=> '数据库帐号 不能为空',
			'password.require' 		=> '数据库密码 不能为空',
			'prefix.require' 	    => '数据表前缀 不能为空',
			'adminuser.require' 	=> '管理员账户 不能为空',
			'adminpwd.require' 	    => '管理员密码 不能为空'
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) {
			exit(json_encode(['code'=>0,'msg'=>$Validate->getError()]));
		}
		return $this;
	}
    
    //验证mysql信息是否正确
    function check_connect($value=false)
    {
        try{
            //删除config目录下的database.php文件
            $database = \think\facade\Env::get('root_path').'config/database.php';
            @unlink($database);
            //测试链接数据库
            Db::connect($value)->execute('SELECT 1');
            
            session('install_data',$value);
            
            return ['code'=>1,'msg'=>'正确'];
        } catch (\PDOException $e) {
            exit(json_encode(['code'=>0,'msg'=>'数据库链接失败，请检查参数是否填写正确']));
        } 
    }
    
    //sql文件位置
    public function sqlFilePath()
    {
        return str_replace('//','/', \think\facade\Env::get('root_path')).'applic/install/install.sql';
    }
    
    /**
     * 导入SQL
     *
     * @param   string $name 插件名称
     * @return  boolean
     * @date    2019-07-11
     */
    public function importsql()
    {
        $install_data = session('install_data');
        if( !isset($install_data['database']) || !isset($install_data['prefix']) ){
           exit(json_encode(['code'=>0,'msg'=>'配置错误，请点击返回 <a href="javascript:history.back(-1)">[上一步]</a>'])); 
        }else{
            $this->check_connect($install_data);
        }
        $sqlFile = $this->sqlFilePath();
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $templine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*'){
                    continue;
                }
                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    try {
                        $templine = str_ireplace('__PREFIX__', $install_data['prefix'], $templine);
                        $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                        //链接数据库并导入SQL
                        Db::connect($install_data)->execute($templine);
                    } catch (\Exception $e) {
                        exit(json_encode(['code'=>0,'msg'=>'导入数据出错：'.$e->getMessage().'<br>可能是数据库不存在或存在相同的数据表']));
                    }
                    $templine = '';
                }
            }
            return ['code'=>1,'setting'=>1,'msg'=>'数据库导入成功......<br>正在创建配置文件......<br>'];
        }else{
            return ['code'=>0,'msg'=>'数据库文件不存在...'];
        }
        
    }
        
    //执行安装
    public function install($setting)
    {
        $install_data = session('install_data');
        //导入数据库
        if( $setting === 0){
            return $this->importsql();
        //创建数据库配置文件
        }else if( $setting === 1 ){
            //文件路径
            $database = \think\facade\Env::get('root_path');
            //读取文件
            $content = file_get_contents($database.'applic/install/database.txt');
            foreach($install_data as $key=>$val){
                $content = str_ireplace('{'.$key.'}', $val, $content);
            }
            //初始化文件驱动
            $File = new \think\template\driver\File();
            //写入文件
            $File->write($database.'config/database.php', $content);
            //
            return ['code'=>1,'setting'=>2,'msg'=>'创建配置文件成功......<br>正在创建管理员账号......<br>'];
        //创建相关数据
        }else if( $setting === 2 ){
            try {
                //文件路径
                $root_path = \think\facade\Env::get('root_path');
                //创建管理员账号
                (new \app\common\model\Sysadm)->save([
                    'auth_role_id'  => 1,
                    'username'      => $install_data['adminuser'],
                    'realname'      => '超级管理员',
                    'description'   => '拥有系统所有的权限（请谨慎使用）',
                    'deleting'      => 0,
                    'password'      => $install_data['adminpwd']
                ]);
                $login_key = md5(time());
                //创建路由
                (new \app\admin\model\ConfigRoute)->save([
                    'group'     => '/',
                    'name'      => 'admin-login',
                    'type'      => 'ALL',
                    'title'     => '登录地址',
                    'remarks'   => '可以修改［路由表达式］自定义你的登录地址，默认为：login',
                    'expression'=> 'login$',
                    'address'   => 'admin/signin/index',
                    'append'    => '{"key":["login_key"],"val":["'.$login_key.'"]}'
                ]);
                //初始化文件驱动
                $File = new \think\template\driver\File();
                //创建登录配置文件
                $File->write($root_path.'config/login.php',"<?php return ['diylogin'=>1,'key'=>'{$login_key}'];");
                //创建锁文件
                $File->write($root_path.'applic/install/install.lock','lock');
                //
                return ['code'=>1,'setting'=>0,'msg'=>'管理员账户创建成功......<br>安装即将完成......'];
            } catch (\Exception $e) {
                exit(json_encode(['code'=>0,'msg'=>'安装出错：'.$e->getMessage()]));
            }
        }
    }
    
    
    /**
     * 系统环境检测
     * @return array 系统环境数据
     */
    function check_env()
    {
        $items = array(
            'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'ok'),
            'php'     => array('PHP版本', '5.6', '5.6+', PHP_VERSION, 'ok'),
            //'mysql'   => array('MYSQL版本', '5.0', '5.0+', '未知', 'ok'), //PHP5.5不支持mysql版本检测
            'upload'  => array('附件上传', '不限制', '2M+', '未知', 'ok'),
            'gd'      => array('GD库', '2.0', '2.0+', '未知', 'ok'),
            'disk'    => array('磁盘空间', '5M', '不限制', '未知', 'ok'),
        );

        //PHP环境检测
        if($items['php'][3] < $items['php'][1]){
            $items['php'][4] = 'close';
            session('error', true);
        }

        //数据库检测
        //if(function_exists('mysql_get_server_info')){
        //	$items['mysql'][3] = mysql_get_server_info();
        //	if($items['mysql'][3] < $items['mysql'][1]){
        //		$items['mysql'][4] = 'close';
        //		session('error', true);
        //	}
        //}

        //附件上传检测
        if(@ini_get('file_uploads'))
            $items['upload'][3] = ini_get('upload_max_filesize');

        //GD库检测
        $tmp = function_exists('gd_info') ? gd_info() : array();
        if(empty($tmp['GD Version'])){
            $items['gd'][3] = '未安装';
            $items['gd'][4] = 'close';
            session('error', true);
        } else {
            $items['gd'][3] = $tmp['GD Version'];
        }
        unset($tmp);

        //磁盘空间检测
        if(function_exists('disk_free_space')) {
            $items['disk'][3] = floor(disk_free_space(WCMS_PATH) / (1024*1024)).'M';
            //$items['disk'][4] = 'close';
        }

        return $items;
    }
    
    
    /**
     * 目录，文件读写检测
     * @return array 检测数据
     */
    function check_dirfile()
    {
        $items = array(
            array('dir',  '可写', 'ok', 'addons'),
            array('dir',  '可写', 'ok', 'applic/install'),
            array('dir',  '可写', 'ok', 'config'),
            array('dir',  '可写', 'ok', 'runtime'),
            array('dir',  '可写', 'ok', 'vendor/route/'),
            array('dir',  '可写', 'ok', './uploads',true),
        );
        //
        foreach ($items as &$val) {
            //根目录
            if( $val[4] ){
               $path = str_replace(array('/./','/../'),'/',WCMS_PATH ); 
            }else{
               $path = WCMS_PATH; 
            }
            if('dir' == $val[0]){
                try {
                    if(!is_writable($path . $val[3])) {
                        if(is_dir($items[1])) {
                            $val[1] = '可读';
                            $val[2] = 'error';
                            session('error', true);
                        } else {
                            $val[1] = '不存在';
                            $val[2] = 'close';
                            session('error', true);
                        }
                    }
                } catch (\Exception $e) {
                    $val[1] = '不可写';
                    $val[2] = 'close';
                    session('error', true);
                }
            } else {
                try {
                    if(file_exists($path . $val[3])) {
                        if(!is_writable($path . $val[3])) {
                            $val[1] = '不可写';
                            $val[2] = 'close';
                            session('error', true);
                        }
                    } else {
                        if(!is_writable(dirname($path . $val[3]))) {
                            $val[1] = '不存在';
                            $val[2] = 'error';
                            session('close', true);
                        }
                    }
                } catch (\Exception $e) {
                    $val[1] = '不可写';
                    $val[2] = 'close';
                    session('error', true);
                }
            }
        }

        return $items;
    }
    
    /**
     * 函数检测
     * @return array 检测数据
     */
    function check_func()
    {
        $items = array(
            array('mysql_connect',     '支持', 'ok'),
            array('file_get_contents', '支持', 'ok'),
            array('fsockopen',         '支持', 'ok'),
            array('curl_init', '支持', 'ok'), //该函数非必须
        );

        foreach ($items as &$val) {
            if(!function_exists($val[0])){
                $val[1] = '不支持';
                $val[2] = 'close';
                $val[3] = '开启';
                session('error', true);
            }
        }

        return $items;
    }
    
}