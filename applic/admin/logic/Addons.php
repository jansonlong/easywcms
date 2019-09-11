<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 插件管理-逻辑层
// +----------------------------------------------------------------------
namespace app\admin\logic;

use think\Db;
use easywcms\Easy;
use think\facade\Env;
use think\facade\Request;
use app\admin\logic\Backend;
use easywcms\base\Http;
use ZipArchive;

class Addons extends Backend {
	
	//定义模型
	public $Model = null;
	public $addon_name = '';
	public $addon_config = [];
    /**
     * 构造方法
     * @access public
     */
	public function __construct()
    {
		parent::__construct();
		$this->Model = new \app\admin\model\Addons;
	}

	/**
	 * 验证表单输入
	 * @param $value	验证的数据
	 * @date 2019-07-10
	 */
	public function validate($value=false)
    {
		$value = $value ? : input('post.');
		$rule = [
			'title' 		=> 'require',
			'description' 	=> 'require'
		];
		$message = [
			'title.require' 		=> '标题名称 不能为空',
			'description.require' 	=> '描述信息 不能为空'
		];
		$Validate = new \think\Validate;
		$Validate = $Validate::make($rule,$message);
		if ( !$Validate->check($value) ) {
			$this->error($Validate->getError());
		}
		return $this;
	}
    
    //获取插件信息
    public function getInfo($addon_name)
    {
        //插件信息
        $class = get_addon_class($addon_name);
        if (class_exists($class)) {
            $addon = new $class();
            return $addon->getInfo();
        }
        return [];
    }
    
	/**
	 * 配置config.php文件
	 * @date 2019-07-10
	 */
	public function setConfig($addon_name,$addon_config)
	{
		$post = Request::post();
        if( !is_array($addon_config) ){
            return ['code'=>0,'msg'=>'参数不正确'];
        }
        foreach($addon_config as $k=>$v){
            if( $addon_config[$k]['type'] == 'fieldlist' && isset($post[$k]['key']) ){
                $addon_config[$k]['value'] = [];
                $arr_value = [];
                if( isset($post[$k]['key']) ){
                    foreach($post[$k]['key'] as $k2=>$v2){
                        !empty($v2) && $arr_value[$v2] = $post[$k]['val'][$k2];
                    }
                    $addon_config[$k]['value'] = $arr_value;
                }
            }else{
                $addon_config[$k]['value'] = $post[$k];
            }
        }
        //数组转换成字符
        $filedata = var_export($addon_config,true);
        //初始化文件驱动
        $File = new \think\template\driver\File();
        //文件路径
        $Path = Env::get('root_path')."addons/{$addon_name}/config.php";
        //删除原文件
		@unlink($Path);
        //保存文件
        $File->write($Path, "<?php \n return ".$filedata.';');
        return ['code'=>1,'msg'=>'保存成功','addon_config'=>$addon_config];
	}
    
	/**
	 * 安装
	 * @date 2019-07-10
	 */
    public function doInstall($addon_name)
    {
        $this->addon_name = $addon_name;
        try {
            // 执行安装脚本
            $class = get_addon_class($addon_name);
            if (class_exists($class)) {
                $addon = new $class();
                $addon->addon_config = $this->addon_config;
                //判断插件的前置操作是否存在
                if( method_exists($addon,'before_install') ){
                    $addon_before = $addon->before_install();
                    if( $addon_before !== true ){
                        return $addon_before;
                    }
                }
                //导入SQL
                if( ! $this->importsql() ){
                   return ['code'=>0,'msg'=>lang('Import SQL failed')];
                }
                //执行插件的安装方法
                $install = $addon->install();
                //安装成功后,EasyWcms系统需要更新的信息
                if( $install === true ){
                    $addon_info = $addon->getInfo();
                    //记录安装信息
                    $this->Model->save([
                        'name'          => $addon_info['name'],
                        'title'         => $addon_info['title'],
                        'description'   => $addon_info['description'],
                        'author'        => $addon_info['author'],
                        'version'       => $addon_info['version'],
                    ]);
                    //更新菜单缓存
                    Easy::savecache('auth_rule');
                    //创建锁文件防止多次安装
                    $File = new \think\template\driver\File();
                    $lock_path = $this->getLockPath();
                    $File->write($lock_path, 'lock');
                //安装失败
                }else{
                   $this->deleteMenu();
                }
                return ['code'=>1,'msg'=>'安装成功，是否刷新后台?'];
            }
            return ['code'=>0,'msg'=>lang('Operation failed')];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
        }
    }
	
	/**
	 * 卸载
	 * @date 2019-07-10
	 */
    public function doUninstall($addon_name)
    {
        $this->addon_name = $addon_name;
        try {
            //执行插件的卸载方法
            $class = get_addon_class($addon_name);
            if (class_exists($class)) {
                $addon = new $class();
                $addon->addon_config = $this->addon_config;
                $uninstall = $addon->uninstall();
                //卸载成功
                if( $uninstall === true ){
                    //删除当前插件的菜单
                    $this->deleteMenu();
                    //禁锁文件
                    $lock_path = $this->getLockPath();
                    @unlink($lock_path);
                    //从安装记录表中删除
                    $this->Model->where(['name'=>$addon_name])->delete();
                    //
                    return ['code'=>1,'msg'=>'卸载成功，请刷新后台'];
                }
            }
            return ['code'=>0,'msg'=>lang('Operation failed')];
        } catch (\Exception $e) {
            return ['code'=>0,'msg'=>lang('An unexpected error occurred').'<br>'.$e->getMessage()];
        }
    }
    
	/**
	 * 锁文件路径
	 * @date 2019-07-10
	 */
    private function getLockPath()
    {
         return str_replace('//','/', Env::get('root_path').'addons/'.$this->addon_name).'/install.lock';
    }
    
    /**
     * 导入SQL
     *
     * @return  boolean
     * @date    2019-07-11
     */
    private function importsql()
    {
        $sqlFile = str_replace('//','/', \think\facade\Env::get('root_path').'addons/'.$this->addon_name).'/install.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $templine = '';
            $DB = false;
            $prefix = config('database.prefix');
            //如果插件有设置独立数据库
            if( isset($this->addon_config['database']) ){
                $database = $this->addon_config['database'];
                if( isset($database['value']) && !empty($database['value']) ){
                    $prefix = $database['value']['prefix'];
                    $DB = Db::connect($database['value']);
                }
            }
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*'){
                    continue;
                }
                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    $templine = str_ireplace('__PREFIX__', $prefix, $templine);
                    $templine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $templine);
                    //导入SQL
                    try{
                        if($DB){
                            $DB->execute($templine);
                        }else{
                            Db::execute($templine);
                        }
                    } catch (\Exception $e) {
                        exit(json_encode(['code'=>0,'msg'=>'导入数据出错：'.$e->getMessage()]));
                    }
                    $templine = '';
                }
            }
        }
        return true;
    }
    
    /**
     * 解压插件
     *
     * @param   string $name 插件名称
     * @return  string
     * @date    2019-08-16
     */
    public function unzip($name)
    {
        $file = Env::get('runtime_path') . 'addons/'. $name . '.zip';
        $dir  = Env::get('addons_path') . $name . '/';
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            if ($zip->open($file) !== TRUE) {
                return ['code'=>0,'msg'=>'Unable to open the zip file'];
            }
            if (!$zip->extractTo($dir)) {
                $zip->close();
                return ['code'=>0,'msg'=>'Unable to extract the file'];
            }
            $zip->close();
            return ['code'=>1];
        }
        return ['code'=>0,'msg'=>'无法执行解压操作，请确保ZipArchive安装正确'];
    }
    
	/**
	 * 清空当前插件的菜单
	 * @date 2019-07-10
	 */
    private function deleteMenu()
    {
        \app\common\logic\Menu::delete($this->addon_name);
    }
    
    /**
     * 远程下载插件
     *
     * @param   string $url 下载地址
     * @param   string $name 插件名称
     * @param   array $extend 扩展参数
     * @return  string
     * @date    2019-08-16
     */
    function download($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        //echo $content;
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'w');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'code'      => 1,
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'file_size' => $size
        );
    }
    
    
    /**
     * 获取插件源资源路径
     * @param   string $name 插件名称
     * @return  string
     */
    public function getSourceAssetsDir($name)
    {
        return Env::get('addons_path') . $name . '/assets/';
    }
    
    /**
     * 获取插件public路径
     * @param   string $name 插件名称
     * @return  string
     */
    public function getSourcePublicDir($name)
    {
        return Env::get('addons_path') . $name . '/public/';
    }

    /**
     * 获取插件目标资源路径
     * @param   string $name 插件名称
     * @return  string
     */
    public function getDestRootDir($name)
    {
        $publicDir = './';
        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        return $publicDir;
    }


}