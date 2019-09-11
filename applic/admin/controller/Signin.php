<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 后台登录-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\model\AuthRole;
use app\common\model\AuthRule;
use app\common\model\Sysadm;
use think\Controller;
use easywcms\Easy;

class Signin extends Controller
{
	    
	//登录界面
    function index()
    {
		if(!is_file(WCMS_PATH . 'applic/install/install.lock')){
            $this->redirect("/Install");
        }
		//清空session
		session(null);
        //清空权限缓存
        Easy::rmCache('Auth_rule');
        //自定义路径登录
        if( config('login.diylogin') == 1){
            $key = config('login.key');
            $login_key = $this->request->param('login_key');
            if( $login_key !== $key ){
                $this->view->engine->layout(false);
                return $this->fetch('err');
            }
        }
        return $this->fetch();
    }
	
	//安全退出跳转
	function safeexit(){
		//清空session
		session(null);
        //清空权限缓存
        Easy::rmCache('Auth_rule');
        //跳转到登录
		$this->redirect('/');
	}
	
	//提交登录
	function submit(){		
		//判断验证码
		$captcha = new \think\captcha\Captcha(config('captcha.'));
		if ( $captcha->check( $this->request->param('verifyCode') ) == false ){
			$this->error('验证码不正确');
		}
		//用户名or密码
		$username = input('request.username');
		$password = input('request.password');
		if ( empty($username) || empty($password) ){
            $this->error('用户名或密码为空');
		}
		//查询用户表
		$condition['username'] = array('eq',$username);
		//查询系统用户表
		$info = (new Sysadm)->where($condition)->find();
		//验证登录信息
		if($info['auth_role_id']==0){
            $this->error('无权限登录');
		}else if(!$info['username']){
            $this->error('账号不存在');
		}else if(!$info['status']){
            $this->error('帐号已被禁用');
		}else{
			$password = Easy::sysmd5($password,$info['usersign']);
			if( $info['password'] == $password ){
				return $this->SaveLogin($info);
			}else{
                $this->error('密码不正确，请重试');
			}
		}	
	}
	
	//保存登录信息
	private function SaveLogin($info){
		//获取当前用户所有在角色组信息
		$auth_roles_data = AuthRole::field('title,rules')->find($info['auth_role_id']);
		//查询不需要验证的规则的 id
		$auth_rules_list = AuthRule::where('ischeck','=',0)->column('id');
		$auth_rules_list = implode(',',(array)$auth_rules_list);
		//清除权限缓存
		Easy::rmCache('Admin_Auth_Role_'.$info['id']);
		//用户组权限
		$rules_data = explode(',',$auth_roles_data['rules']);
		//合并用户权限和用户组权限
		if(!empty($info['rules'])){
			$user_rules = explode(',',$info['rules']);
			$rules_data = array_merge($rules_data,$user_rules);
		}
		//禁用指定权限
		if(!empty($info['disable_rules'])){
			$rules_data = array_merge( array_diff( $rules_data, explode(',', $info['disable_rules']) ) );
		}
		//权限转换成字符串1,2,3,4,5,.....
		$rules_data = implode(',',$rules_data);
		//保存SESSION
		$Admininfo = [
			'sysadm_id'		=> $info['id'],				//用户ID
			'auth_role_id'	=> $info['auth_role_id'],	//用户组ID
			'username'		=> $info['username'],		//用户账号
			'realname'		=> $info['realname'],		//用户姓名
			'headimgurl'	=> $info['headimgurl'],		//用户头像
			'login_count'	=> $info['login_count'],	//用户登录次数
			'auth_title'	=> $auth_roles_data['title'],		//用户组名称
			'auth_rule'		=> $auth_rules_list.','.$rules_data,//用户组所得权限
			'timestamp'		=> time()
		];
		//赋值admin作用域
		Easy::setSession('Admininfo',$Admininfo,'admin');
		//更新用户最后登录的信息
		$Sysadm= Sysadm::field('login_count,lastlogintime')->find($info['id']);
		$Sysadm->login_count = $Sysadm->login_count + 1;
		$Sysadm->lastlogintime = time();
		$Sysadm->save();
		//清空权限缓存
		Easy::rmCache('admin-'.$Admininfo['sysadm_id']);
		Easy::rmCache('Admin-AuthRule'.$Admininfo['sysadm_id']);
		//返回数据
        $this->success('登录成功，正在进入系统...',url('/admin/console'));
	}
}
