<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 插件管理-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller;

set_time_limit(0);

use think\facade\Config;
use easywcms\Easy;
use easywcms\base\Http;
use easywcms\base\Request;
use OSS\OssClient;
use think\Image;
use think\facade\Env;

class Addons extends Admin {
	
	//初始化
    public function initialize(){
		parent::initialize();
        $this->logic = new \app\admin\logic\Addons;
        $this->store_url = 'https://store.zcphp.com/store/addons';
    }
    
    //执行下载
    private function download($info)
    {
        //文件名
        $save_dir = Env::get('runtime_path') . 'addons';
        $filename = $info['name'].'.zip';
        //删除同名文件
        @unlink($save_dir.DIRECTORY_SEPARATOR.$filename);
        //执行下载
        $res = $this->logic->download($info['package_url'], $save_dir, $filename,1);
        if( $res['code'] ){
            //判断文件大小
            $filesize = filesize($save_dir.DIRECTORY_SEPARATOR.$filename);
            if( $filesize == 0 ){
                @unlink($save_dir.DIRECTORY_SEPARATOR.$filename);
                $this->error('下载失败，可能插件已下架',$info['package_url']);
            }
            //解压
            $unzip = $this->logic->unzip($info['name']);
            if( $unzip['code'] ){
                //移除临时文件
                @unlink($save_dir.DIRECTORY_SEPARATOR.$filename);
                //拷贝pulibc下的所有文件
                $sourcePublicDir = $this->logic->getSourcePublicDir($info['name']);
                $destRootDir = $this->logic->getDestRootDir($info['name']);
                if (is_dir($sourcePublicDir)) {
                    copydirs($sourcePublicDir, $destRootDir);
                }
                //拷贝插件下的assets所有文件到跟目录/assets/addons-{$name}/
                $sourceAssetsDir = $this->logic->getSourceAssetsDir($info['name']);
                if (is_dir($sourceAssetsDir)) {
                    copydirs($sourceAssetsDir, $destRootDir . "assets/addons-{$info['name']}/");
                }
                //检查插件是否正确

                //显示安装界面
                $this->success('下载成功');
                //return ['code'=>1,'msg'=>'下载成功'];

            }else{
                return $unzip;
            }
        }else{
            return $unzip;
        } 
    }
	
	/**
	 * 列表查询
	 */
	public function index()
    {
		if( $this->request->isAjax() && $this->logic ){
            
            switch ( $this->request->param('type') )
            {
            //待安装
            case 2:
//                //获取安装包列表
//                $addons_path = \think\facade\Env::get('addons_path');
//                $package_list = glob($addons_path . 'package/*.zip');
//                foreach ($package_list as $file) {
//                    //格式化路径信息
//                    $info = pathinfo($file);
//                    $filename = ucfirst($info['filename']);
//                    //如果插件已存在,就不显示了.
//                    if( is_file(\think\facade\Env::get('root_path')."addons/{$info['filename']}/{$filename}.php") ){
//                       continue; 
//                    }
//                    $file_list[] = $info['filename'];
//                }
//                print_r($file_list);
                    //插件名称列表
                    $addons_list = get_addons_info();
                    return ['code'=>1,'count'=>count($addons_list),'data'=>$addons_list];
            break; 
            //在线安装
            case 'online_install':
                    $addon_name = $this->request->post('name');
                    //检查插件是否存在
                    $class = get_addon_class($addon_name);
                    if( class_exists($class) ){
                        $this->error('插件已下载，不需要重复下载哦！');
                    }
                    //获取store账号密码
                    $host = $this->request->host();
                    $store_info = cache('store_info_'.$host);
                    //商店会员信息
                    $post_data['username'] = $store_info['username'];
                    $post_data['userpwd']  = $store_info['userpwd'];
                    //插件标识
                    $post_data['addon_name'] = $addon_name;
                    //请求官方的api获取插件信息
                    $info = Http::post($this->store_url.'/getInfo?_ajax=1',$post_data);
                    $info = json_decode($info['data'],true);
                    //返回有下载地址
                    if( $info['code'] == 1 && !empty($info['filename']) && !empty($info['package_url']) ){
                        return $this->download($info);
                    //强制重新登录
                    }else if( $info['code'] == 40000 ){
                        cache('store_info_'.$host,NULL);
                        return $info;
                    }else{
                        return $info;
                    }
            break;
            //登录store
            case 'login': 
                    //登录地址
                    $login_url = $this->store_url.'/login?_ajax=1 ';
                    //获取账号密码
                    $username = $this->request->post('username');
                    $userpwd  = $this->request->post('userpwd');
                    $userInfo = ['username'=>$username,'userpwd'=>$userpwd];
                    $ret = Http::post($login_url,$userInfo);
                    $ret = json_decode($ret['data'],true);
                    if( $ret['code']==1 ){
                        //缓存登录信息
                        $host = $this->request->host();
                        cache('store_info_'.$host,$userInfo);
                        //
                        $this->success($ret['msg']);
                    }else{
                        return $ret;
                    }
            break;
            //验证卡密
            case 'check_kami':
                    //验证卡密地址
                    $post_url = $this->store_url.'/check_kami?_ajax=1 ';
                    //获取账号密码
                    $host = $this->request->host();
                    $store_info = cache('store_info_'.$host);
                    //商店会员信息
                    $post_data['username'] = $store_info['username'];
                    $post_data['userpwd']  = $store_info['userpwd'];
                    $post_data['addon_name'] = $this->request->post('addon_name');
                    $post_data['kami'] = $this->request->post('kami');
                    $ret = Http::post($post_url,$post_data);
                    $ret = json_decode($ret['data'],true);
                    if( $ret['code']==1 ){
                        return $this->download($ret);
                    }else{
                        return $ret;
                    }
            break; 
            //默认查询已安装
            default:
                    return $this->logic->getPage();  
            }
		}else{
            
            $store_url = $this->store_url.'?_ajax=1&referer='.$this->request->host();
            
            $this->assign(['typeall'=>[2=>'待安装',3=>'在线安装'],'store_url'=>$store_url]);
			return $this->fetch();
		}
	}
    
    //安装插件
    public function install()
    {
        //插件标识
        $addon_name = trim($this->request->param('name'));
        if( empty($addon_name) ){
            $this->error(lang('Invalid parameters') . ': name','','',0);
        }
        //判断是否已安装相同的插件
        $check = $this->logic->setField('id')->setWhere(['name'=>$addon_name])->doFind();
        //插件已存在
        if($check->id){
            $this->error(lang('Addon already exists'),'','',0);
        }
        //获取插件配置
        $addon_config = get_addon_config($addon_name);
        //执行安装
		if( $this->request->isPost() && $this->logic ){
            //保存配置文件
			$set = $this->logic->setConfig($addon_name,$addon_config);
            if( $set['code'] ){
                $this->logic->addon_config = $set['addon_config'];
                //执行插件的安装方法
                return $this->logic->doInstall($addon_name);
            }else{
                return $set;
            }
		}else{ 
            $assign = ['addon_config'=>$addon_config];
            $installtpl = \think\facade\Env::get('addons_path') . $addon_name . DIRECTORY_SEPARATOR . 'view/install.tpl';
            if( $this->view->exists($installtpl) ){
                $assign['addon_info'] = $this->logic->getInfo($addon_name);
            }else{
                $installtpl = 'install';
            }
            return $this->assign($assign)->fetch($installtpl);
		}
    }
    
	/**
	 * 卸载
	 * @date 2019-07-10
	 */
    public function uninstall()
    {
        //插件标识
        $addon_name = $this->request->param('name');
        if( empty($addon_name) ){
           $this->error(lang('Invalid parameters') . ': name');
        }
        //判断是否已安装相同的插件
        $check = $this->logic->setField('id,deleting')->setWhere(['name'=>$addon_name])->doFind();
        //插件不存在
        if(!$check->id){
            $this->error(lang('Addon not exists'));
        }
        //禁止卸载的插件
        if($check->deleting !== 1){
             $this->error(lang('Addon prohibits unloading'));
        }
        //执行删除
		if( $this->request->isPost() && $this->logic ){
            return $this->logic->doUninstall($addon_name);
        }
    }
    
	/**
	 * 编辑操作
	 */
	public function edit()
    {
        $id = $this->request->param('id/d','');
        if( empty($id) ){
           return lang('Invalid parameters') . ': id';
        }
        //查询数据库
        $addon_info = $this->logic->setField('id,name')->doGet($id);
        $addon_name = $addon_info['name'];
        if( empty($addon_name) ){
           return lang('Invalid parameters') . ': addon_name';
        }
        //获取插件数据库配置
        $addon_config = get_addon_config($addon_name);
		if( $this->request->isPost() && $this->logic ){
			return $this->logic->setConfig($addon_name,$addon_config);
		}else{
			return $this->assign('addon_config',$addon_config)->fetch();
		}
	}

	
}
