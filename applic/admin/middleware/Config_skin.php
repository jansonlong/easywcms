<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 皮肤管理-中间件
// +----------------------------------------------------------------------
namespace app\admin\middleware;

use think\facade\View;
use easywcms\Easy;
use app\admin\model\ConfigSkin;

class Config_skin{
	
    public function handle($request, \Closure $next){
		//获取当前操作方法
		$action = $request->action();
		
		//删除操作-前置行为
		if($action=='delete'){
			$request->Where = [['deleting','=',1]];
		}

        //以上代码是前置行为======================================
        $response =  $next($request);
        //以下代码是后置行为======================================

		//添加操作-后置行为
		if( $action=='add' && $request->isPost() ){
			$ret = $response->getData();
			if( $ret['code'] == 1 && isset($ret['data']) ){
				//重置名称 && 创建css文件
				$update = ConfigSkin::update(['id'=>$ret['data'],'skin_name'=>'Diy'.$ret['data']])
				&&
				$make = $this->makeCss($ret['data']);
				if( ! $update || $make['code'] == 0 ){
					//从数据库删除添加的数据
					ConfigSkin::destroy($ret['data']);
					exit(json_encode($make));
				}
			}
			return $response;
		}
		
		//编辑操作-后置行为
		if( $action=='edit' && $request->isPost() ){
			$ret = $response->getData();
			if( $ret['code'] == 1 && isset($ret['data']) ){
				//删除缓存
				Easy::rmCache('Statics_skin');
				//创建css文件
				$make = $this->makeCss($ret['data']);
				//返回错误信息
				if( $make['code'] == 0 ){
					exit(json_encode($make));
				}
			}
			return $response;
		}
		
		//删除操作-后置行为
		if( $action=='delete' && $request->isPost() ){
			//查询已删除的数据并删除文件
			$ret = $response->getData();
			if( $ret['code']==1 && isset($ret['data']) ){
				//更新缓存
				$this->cacheSkin();
				$list = ConfigSkin::field('id,skin_name')->whereNotNull('delete_time')->select($ret['data']);
				//对数据集进行遍历操作删除文件
				foreach($list as $key=>$skin){
					$filename = '.' . Easy::getConfig('param.skinpath') . $skin->skin_name.'.css';
					file_exists($filename) && @unlink($filename);
				}
			}
			return $response;
		}
		
		//更新样式-后置行为
		if( $action === 'updatecss'){
			$list = ConfigSkin::field('id,skin_name,skin_data')->select()->toArray();
			foreach($list as $val){
				//创建css文件
				$make = $this->makeCss($val);
				//返回错误信息
				if( $make['code'] == 0 ){
					exit(json_encode($make));
				}
			}
			//返回response对象
			return $response;
		}
		
        //返回response对象
		return $response;
    }
	
	//生成缓存
	private function cacheSkin(){
		Easy::setCache('config_skin',null);
		ConfigSkin::cache('config_skin')->whereNull('delete_time')->select();
	}
	
	//创建css文件
	private function makeCss($data){
		if( !is_array($data) ){
			$data = ConfigSkin::field('id,skin_name,skin_data')->find($data)->toArray();
		}
		if( $data['id'] && $data['skin_name'] ){
			$filepath = '.' . Easy::getConfig('param.skinpath');
			$filename = $filepath . $data['skin_name'] . '.css';
			try {
				//创建目录
				Easy::mk_dir($filepath);
				//删除原文件
				file_exists($filename) && @unlink($filename);
				//读取模板
				$filedata = file_get_contents(APP_PATH.'admin/view/config/skin/skin.css');
				$filedata = View::display( $filedata , $data['skin_data'] );
				//写入CSS文件
				if( file_put_contents($filename,$filedata) ){
					return ['code'=>1,'msg'=>lang('Operation completed'),'data'=>$data];
				}
			} catch (\Exception $e) {
				return ['code'=>0,'msg'=>lang('Please check directory permissions').'<br>'.$e->getMessage()];
			}
		}
		return ['code'=>0,'msg'=>lang('Lack of parameters')];
	}
	
	
}