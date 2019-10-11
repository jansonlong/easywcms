<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 共公行为扩展
// +----------------------------------------------------------------------

namespace app\common\behavior;

use app\admin\model\ConfigRoute;
use think\facade\Config;
use think\facade\Route;
use think\facade\Lang;
use think\facade\Env;
use think\facade\Cache;

class app_init{
	
	/**
	 * 行为入口
	 * @date 2018-09-29
	 */
    public function run(){
		
		//报告运行时错误
		error_reporting(E_ERROR | E_WARNING | E_PARSE);

		//加载共用语言包
		Lang::load( APP_PATH.'common/lang/zh-cn.php' );
		
		//子域名到部署到指定的模块
		$domain = Config::pull('domain');
		foreach($domain as $k=>$v){
			$url = parse_url( trim($v) );
			if( isset($url['scheme']) && isset($url['host']) ){
				Route::domain($url['host'],$k);
			}else{
				Route::domain($v,$k);
			}
		}
        
        //生成路由配置文件
        if( Cache::get('ConfigRoute') != 1 ){
            //判断是否已安装
            if( !is_file(Env::get('root_path').'config/database.php') ){
                if( Cache::get('install_in') != 1 ){
                    Cache::set('install_in',1);
                    header("Location: ".url('/install'));
                    exit;
                }else{
                    return true;
                }
            }
            $route = ConfigRoute::where('status=1')->select()->toArray();
            if(count($route)<=0){
                Cache::set('ConfigRoute',0);
                return true;
            }
            $temp = [];
            foreach ($route as $k=>$v){
                $temp[$v['group']][]=$v;
            }
            foreach($temp as $key=>$val){
                $rule_code = '';
                foreach($val as $k=>$v){
                    $expression = htmlspecialchars_decode($v['expression']);
                    $address    = htmlspecialchars_decode($v['address']);
                    //请求类型
                    if( $v['type'] == 'ALL' ){
                        $v['type'] = '*';
                    }
                    //路由标识
                    if( isset($v['name']) && !empty($v['name']) ){
                        $name = "->name('{$v['name']}')";
                    }else{
                        $name = '';
                    }
                    //路由附加参数
                    if( isset($v['append']) && !empty($v['append']) ){
                        $append_value = $v['append'];
                        $append = [];
                        foreach($append_value['key'] as $ak=>$av){
                            $append[$av] = $append_value['val'][$ak];
                        }
                        $append = var_export($append,true);
                        $append = "->append({$append})";
                    }else{
                        $append = '';
                    }
                    //参数变量规则
                    if( isset($v['pattern']) && !empty($v['pattern']) ){
                        $pattern_value = $v['pattern'];
                        $pattern = [];
                        foreach($pattern_value['key'] as $ak=>$av){
                            $pattern[$av] = $pattern_value['val'][$ak];
                        }
                        $pattern = var_export($pattern,true);
                        $pattern = "->pattern({$pattern})";
                    }else{
                        $pattern = '';
                    }
                    $rule_code.= "\nRoute::rule('{$expression}','{$address}','{$v['type']}'){$name}{$append}{$pattern};";
                }
                $code.= "\n\nRoute::group('{$key}', function () {" . $rule_code . "\n});\n";
            }
            //路由文件地址
            $path =  Env::get('root_path') . 'vendor/route/route.php' ;
            //初始化文件驱动
            $File = new \think\template\driver\File();
            $File->write($path, '<?php '.$code);
            Cache::set('ConfigRoute',1);
        }
		
	}
	
}