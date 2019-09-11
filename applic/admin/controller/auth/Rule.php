<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 菜单规则-控制器
// +----------------------------------------------------------------------
namespace app\admin\controller\auth;

use app\admin\controller\Admin;
use think\facade\Env;
use app\common\model\AuthRule;

class Rule extends Admin {
	
	//初始化
    public function initialize(){
		parent::initialize();
		$this->logic = new \app\admin\logic\auth\Rule;
		$this->logic->setWhere([['menutype','<>',0]]);
    }
	
	//定义前置操作
    protected $beforeActionList = [
        '_beforeAction' => ['only' => ['add','edit'] ],
    ];
	
	//指定方法调用中间件
	protected $middleware = [
		'app\admin\middleware\Auth'
	];
	
	//前置操作
	public function _beforeAction()
    {
		if( $this->request->isGet() ){
			
			$list = $this->logic->data();
			//模块列表
			$module['admin'] = 'admin';
			//插件名称列表
			$addons_list = get_addons_list();
			if( isset($addons_list) && is_array($addons_list) ){
				foreach($addons_list as $k=>$v){
					$key = strtolower($v);
					$module[$key] = $v;
				}
			}
            //获取图标
            $str = file_get_contents('http://at.alicdn.com/t/font_758109_0yg5uplxtao.css');
            preg_match_all('/(?<=.icon-)[^:]+/',$str,$iconlist);
            unset($str);
            //
			$this->assign(['iconlist'=>$iconlist[0],'list'=>$list['data'],'modulelist'=>$module]);
		}
	}
	
	//资源管理器
	public function restful(){
		if( $this->request->isAjax() ){
			$pid = $this->request->param('pid/d');
			return $this->logic->getReleson($pid);
		}else{
			return $this->fetch();
		}
	}

    
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // 将数据按照所属关系封装 见图2  
    function arr2tree($tree, $rootId = 0) {  
        $return = array();  
        foreach($tree as $leaf) {  
            if($leaf['parent_id'] == $rootId) {  
                foreach($tree as $subleaf) {
                    if($subleaf['parent_id'] == $leaf['id']) {  
                        $children_temp = $this->arr2tree($tree, $leaf['id']);
                        foreach($children_temp as $val) {
                            unset($val['id']);
                            unset($val['parent_id']);
                            $children[] = $val;
                        }
                        $leaf['children'] = $children;
                        $children = [];
                        break;  
                    }  
                }  
                $return[] = $leaf;  
            }  
        }  
        return $return;  
    }  
    
    
//    //导出菜单
//    function exportMenu()
//    {
//        //    
//        $module = 'addons/cms';
//        //
//        $field = 'id, parent_id, menutype, title, module, name, fontico, btnclass, status, deleting, ischeck, condition, parameter, description, listorder, type';
//        $this->logic->setWhere(['module'=>'addons/cms']);
//        $this->logic->setOrder('listorder!=0 desc,listorder,id asc');
//        $channels = $list = $this->logic->doSelect($field,true);
//        $tree = $this->arr2tree($channels);
//        unset($tree[0]['id']);
//        $content = json_encode($tree);
//        //获取本地主题目录
//        $file_path = str_replace('//','/', \think\facade\Env::get('root_path').$module).'/menu.config';
//        //初始化文件驱动
//        $File = new \think\template\driver\File();
//        $File->write($file_path, $content);
//    }
    
    

    


}