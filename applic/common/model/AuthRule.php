<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.zcphp.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 菜单规则-数据层
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class AuthRule extends Model{
	
	//软删除
	use SoftDelete;	
	
	//自动填充时间戳
	protected $autoWriteTimestamp = true;
			
//	//附加字段
//	protected $append = ['url'];
//	
//	//设置url
//    protected function getUrlAttr($value,$data){
//		if( strtolower($data['module']) == 'admin'){
//			$module = '/admin';
//		}else{
//			$module = '/admin/'.$data['module'];
//		}
//        return url($module.$data['name']);
//    }
		
	//自动完成点击事件
    protected function setOnclickAttr($value,$data){
		if( ! empty($value) ){
			return str_replace('&quot;',"'",$data['onclick']);
		}
    }
			
	//模块名
    protected function getModuleAttr($value){
        return (isset($value)) ? strtolower($value) :'';
    }
	
	//模块名
    protected function setModuleAttr($value){
        return (isset($value)) ? strtolower($value) :'';
    }
	
	//将规则转换成全小写
    protected function getNameAttr($value){
        return (isset($value)) ? strtolower($value) :'';
    }
	
	//将规则转换成全小写
    protected function setNameAttr($value){
        return (isset($value)) ? strtolower($value) :'';
    }
	
	//入库设置 选项参数
//    protected function setOptionsAttr($value){
//		print_r($value);
//		if( !isset($value) || empty($value) ){
//			 return '{}';
//		}
//		//
//		foreach($value as $key=>$val){
//			if( !empty($val['key']) ){
//				$item[$val['key']] = $val['value'];
//			}
//		}
//		return json_encode($item);
//    }
	
	//入库设置 请求参数
//    protected function setParamsAttr($value){
//		if( !isset($value) || empty($value) ){
//			 return '{}';
//		}
//		//
//		foreach($value as $key=>$val){
//			if( !empty($val['key']) ){
//				$item[$val['key']] = $val['value'];
//			}
//		}
//		return json_encode($item);
//    }	
	
}