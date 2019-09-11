<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | Explain：树形结构
// +----------------------------------------------------------------------
namespace easywcms\base;

class Tree{
	
	/**
	* 生成树型结构所需要的2维数组
	* @var array
	*/
	public $arr = array();
	public $retarr = array();

	/**
	* 生成树型结构所需修饰符号，可以换成图片
	* @var array
	*/
	public $icon = array('│','├─','└─');
	public $nbsp = '　';

	/**
	* @access private
	*/
	public $ret = '';

	/**
	* 构造函数，初始化类
	* @param array 2维数组，例如：
	* array(
	*      1 => array('id'=>'1','parent_id'=>0,'name'=>'一级栏目一'),
	*      2 => array('id'=>'2','parent_id'=>0,'name'=>'一级栏目二'),
	*      3 => array('id'=>'3','parent_id'=>1,'name'=>'二级栏目一'),
	*      4 => array('id'=>'4','parent_id'=>1,'name'=>'二级栏目二'),
	*      5 => array('id'=>'5','parent_id'=>2,'name'=>'二级栏目三'),
	*      6 => array('id'=>'6','parent_id'=>3,'name'=>'三级栏目一'),
	*      7 => array('id'=>'7','parent_id'=>3,'name'=>'三级栏目二')
	*      )
	*/
	public function __construct($arr=array()){
       $this->arr = $arr;
	   $this->ret = '';
	   return is_array($arr);
	}
	
    /**
	* 得到子级数组
	* @param int
	* @return array
	*/
	public function get_child($myid){
		$a = $newarr = array();
		if(is_array($this->arr)){
			foreach($this->arr as $id => $a){
				if($a['parent_id'] == $myid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	
    /**
	* 得到树型结构
	* @param int ID，表示获得这个ID下的所有子级
	* @return string
	*/
	public function get_tree($myid, $str, $newkey = 'title', $adds = '', $str_group = ''){
		$number=1;
		$child = $this->get_child($myid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $key=>$val){
				$j = $k= '';
				if($number==$total){
					$j.= $this->icon[2];
				}else{
					$j.= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				extract($val);
				$val['parent_id'] == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");
				$this->arr[$key][$newkey] = $nstr;
				$this->ret_arr[] = $this->arr[$key];
				$this->get_tree($id, $str, $newkey, $adds.$k.$this->nbsp,$str_group);
				$number++;
			}
		}
		return $this->ret_arr;
	}
	
    /**
	* 得到树型结构
	* @param int ID，表示获得这个ID下的所有子级
	* @return string
	*/
	public function get_data($myid, $str, $newkey = 'title', $adds = '', $str_group = ''){
		$number=1;
		$child = $this->get_child($myid);
		if(is_array($child)){
		    $total = count($child);
			foreach($child as $key=>$val){
				$j = $k= '';
				if($number==$total){
					$j.= $this->icon[2];
				}else{
					$j.= $this->icon[1];
					$k = $adds ? $this->icon[0] : '';
				}
				$spacer = $adds ? $adds.$j : '';
				extract($val);
				$val['parent_id'] == 0 && $str_group ? eval("\$nstr = \"$str_group\";") : eval("\$nstr = \"$str\";");
				$this->arr[$key][$newkey] = $nstr;
				$this->ret_arr[] = $this->arr[$key];
				$this->get_tree($id, $str, $newkey, $adds.$k.$this->nbsp,$str_group);
				$number++;
			}
		}
		return $this->ret_arr;
	}
	
	
}