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
namespace app\install\controller;

use think\Controller;
use easywcms\Easy;

class Index extends Controller
{
    
    public function initialize()
    {
        $this->step = $this->request->get('step/d',1);
		if( $this->step != 6 && is_file(WCMS_PATH . 'applic/install/install.lock') ){
            $this->error('已经成功安装了EasyWcms，请不要重复安装!', url('/'));
		}
        $this->logic = new \app\install\logic\Index;
        $this->assign(['step'=>$this->step ]);
    }
	
    
    public function index()
    {
        if(!empty($this->step)){
            $action = 'step'.$this->step;
            return $this->$action();
        }
		
    }
    
    //安装协议
    public function step1()
    {
        $sqlFile = $this->logic->sqlFilePath();
        if ( !is_file($sqlFile) ) {
            exception('数据库安装文件不存在：'.$sqlFile, 10006);
        }
        return $this->fetch('step1');
    }
    
	//环境检测
	public function step2()
    {
		session('error', false);
		//环境检测
		$env = $this->logic->check_env();
		//函数检测
		$func = $this->logic->check_func();
        //        
		$this->assign([
            'env'       => $env,
            'func'      => $func,
            'upper'     => true,
            'next'      => session('error') ? false : true,
        ]);
		return $this->fetch('step2');
	}
    
	//文件权限
	public function step3()
    {
		session('error', false);
		//目录文件读写检测
		$dirfile = $this->logic->check_dirfile();
        //
		$this->assign([
            'dirfile'   => $dirfile,
            'upper'     => true,
            'next'      => session('error') ? false : true
        ]);
		return $this->fetch('step3');
	}
    
    //账号设置
	public function step4()
    {
        //提交
        if( $this->request->isPost() ){
            //清除admin作用域
            session(null,'admin');
            //验证参数是否正确,并存到session
            $post = $this->request->post();
            return $this->logic->validate($post)->check_connect($post);
        }else{
            $install_data = session('install_data');
            $this->assign([
                'data'     => $install_data,
                'upper'     => true,
                'next'      => session('error') ? false : true,
            ]);
            return $this->fetch('step4'); 
        }
	}
    
    //正在安装
    public function step5()
    {
        if( $this->request->isPost() ){
            $setting = $this->request->get('setting/d');
            return $this->logic->install($setting);
        }else{
            cache('ConfigRoute',0);
            return $this->fetch('step5'); 
        }
    }
    
    //安装完成
    public function step6()
    {
        cache('install_in',null);
        return $this->fetch('step6'); 
    }
    
}