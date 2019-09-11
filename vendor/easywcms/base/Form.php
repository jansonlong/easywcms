<?php
// +----------------------------------------------------------------------
// | easywcms v1.0
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://www.easywcms.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: Janson <admin@zcphp.com>
// +----------------------------------------------------------------------
// | 表单生成器
// +----------------------------------------------------------------------
namespace easywcms\base;

use think\View;

class Form{

    /**
	 * 当调用的静态方法不存在或权限不足时，会自动调用__callStatic方法。
     * @return FormBuilder
     */
    public static function __callStatic($action,$param){
		if($action == 'input'){
			$param['setParam'] = false;
		}
        return call_user_func_array([FormBuilder::instance($param),$action],$param);
    }

}

/**
 * 表单生成器
 * @date 2018-09-18
 */
class FormBuilder {
	
	protected static $instance;
	
    /**
     * 构造方法
     * @access public
     */
    public function __construct() {

    }

    /**
     * 获取单例
     * @param array $form
     * @return static
     */
    public static function instance($param = [])
	{
        if (is_null(self::$instance)) {
            self::$instance = new static($param);
        }
		if( ! isset($param['setParam']) ){
			self::$instance->setParam($param);
		}
        return self::$instance;
    }
	
	/**
	 * form元素集合  系统参数 配置
	 */
	function item()
	{
		return [
			'text'		=> '单行文本',
			'textarea'	=> '多行文本',
			'switcher'	=> '开关按钮',
			'select'	=> '下拉列表 (单选)',
			'selects'	=> '下拉列表 (多选)',
			'radio'		=> '单选',
			'editor'	=> '编辑器',
			'dates'		=> '日期',
			'times'		=> '时间',
			'datetime'	=> '日期时间',
			'listinput'	=> '数组框',
		];
	}
	
	/**
	 * 获取字段类型
	 * @return array
	 */
	public static function getFieldType()
	{
		return [
			['disabled' , ':::下拉选项:::', 'disabled'=>1],
			['category' , '　栏目'],
			['tags'		,'　标签'],
			['select'	,'　下拉列表 (单选)'],
			['selects'	,'　下拉列表 (多选)'],
			['disabled'	,':::输入框:::','disabled'=>1],
			['text'		,'　单行文本'],
			['textarea'	,'　多行文本'],
			['editor'	,'　内容编辑器'],
			['disabled'	,':::选项框:::','disabled'=>1],
			['radio'	,'　单选框'],
			['checkbox'	,'　复选框'],
			['disabled'	,':::上传组件:::','disabled'=>1],
			['image'	,'　单张图片-上传'],
			['images'	,'　多张图片-上传'],
			['imagebs'	,'　多张图片-上传(大小图)'],
			['file'		,'　单文件-上传'],
			['files'	,'　多文件-上传'],
			['disabled'	,':::日期时间组件:::','disabled'=>1],
			['datepicker','　日期'],
			['timepicker','　时间'],
			['datetime'	,'　日期与时间'],
			['disabled'	,':::其他:::','disabled'=>1],
			['switcher'	,'　开关按钮'],
			['fieldlist','　动态字段列表'],
			['disabled'	,':::地区选择器:::','disabled'=>1],
			['citypicker','　地区选择器'],
		];
	}

	/**
	 * 获取字段验证
	 * @return array
	 */
	public static function getvalidationRules()
	{
		return [
			'null'		=> '不需验证',
			'required'	=> '必填项',
			'phone'		=> '手机号码',
			'email'		=> '邮箱',
			'url'		=> '网址',
			'date'		=> '日期',
			'number'	=> '有效的数值',
			'identity'	=> '身份证',
		];
	}
	
	/**
	 * 处理表单数据
	 * @param $form 表单数据
	 */
	private function setParam($param)
	{
		if( !is_array($param) ){ $param = []; }
		//合并参数
		//如果调用时只传入一个参数时并且为数组类型,直接赋值给form
		//例如：Form::text( ['title'=>'标题','name'=>'字段名','value'=>'值',...] )
		if(is_array($param[0])){
			$form = $param[0];
		// 传参例如：Form::text( 'title','name','value',[...] )
		}else{
			isset($param[0]) && $form['title'] = $param[0];
			isset($param[1]) && $form['name'] = $param[1];
			isset($param[2]) && $form['value'] = $param[2];
			//默认第4个参数为数组类型的, 如果不是, 则不合并
			if( isset($param[3]) && is_array($param[3]) ){
				$form = array_merge($form,$param[3]);
			}
			!isset($form) && $form = $param;
		}
		
		//表单项目是否开启内联
		$this->itemclass = (isset($form['inline'])&&$form['inline']!=='') ? 'layui-inline' : 'layui-form-item';
		$this->display 	 = $this->itemclass == 'layui-form-item' ? 'block' : 'inline';
		//输入框栏目
		$this->inputclass= (isset($form['inputclass'])&&$form['inputclass']!=='') ? $form['inputclass'] : '';
		//表单名
		$this->name		= (isset($form['name'])&&$form['name']!=='') ? $form['name'] : '';
		//
		$this->label	= (isset($form['label'])&&$form['label']!=='') ? $form['label'] : false;
		//表单值
		$this->value 	= (isset($form['value'])&&$form['value']!=='') ? $form['value'] : '';
		//
		$this->defaultvalue = (isset($form['defaultvalue'])&&$form['defaultvalue']!=='') ? $form['defaultvalue'] : '';
		//数据列表
		$this->list		=  (isset($form['list'])&&$form['list']!=='') ? $form['list'] : '';
		//数据列表 指定的 键=>值
		isset( $form['keys'] ) && $this->keys = $form['keys'];
		//禁止选择子项
		$this->disablesub = isset( $form['disablesub'] ) ? $form['disablesub'] : false ;
		//表单前端验证规则
		$this->verify 	= (isset($form['verify'])&&$form['verify']!=='') ? 'lay-verify="'.$form['verify'].'"' : '';
		//自定义属性值
		$this->attr 	= (isset($form['attr'])&&$form['attr']!=='') ? $form['attr'] : '';
		//定义异常提示层模式
		$this->vertype 	= (isset($form['vertype'])&&$form['vertype']!=='') ? $form['vertype'] : 'msg';
		$this->vertype 	= 'lay-verType="'.$this->vertype.'"';
		//说明
		$this->explain 	= (isset($form['explain'])&&$form['explain']!=='') ? '<div class="layui-form-mid layui-word-aux layui-input-explain">'.$form['explain'].'</div>' : '';
		//占位符
		$this->placeholder = (isset($form['placeholder'])&&$form['placeholder']!=='') ? $form['placeholder'] : '';
		//表单标题
		$this->title 	= (isset($form['title'])&&$form['title']!=='') ? $form['title'] : '文本框';
		//禁止修改并禁止回传到后台
		$this->disabled = (isset($form['disabled'])&&$form['disabled']) ? 'disabled=true' : '';
		//禁止修改 可传值到后台
		$this->readonly = (isset($form['readonly'])&&$form['readonly']) ? 'readonly=true' : '';
		//忽略美化
		$this->ignore 	= isset($form['ignore']) ? 'lay-ignore' : '';
		//数据
		$this->data 	= isset($form['data']) ? $form['data'] : false;
		//style
		$this->style 	= (isset($form['style'])&&$form['style']!=='') ? $form['style'] : '';
		$this->id 		= (isset($form['id'])&&$form['id']!=='') ? $form['id'] : $form['name'];
		//slect选择器的样式
		$this->selectclass = (isset($form['selectclass'])&&$form['selectclass']!=='') ? $form['selectclass'] : '';
		//form 对象
		$this->form = $form; 
		//返回当前对象
		return $this;
	}
		
	/**
	 * 日期时间
	 * @param $form 表单数据
	 */
	function datetime()
	{
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'"  placeholder="'.($this->placeholder?:'请选择日期与时间').'" value="'.$value.'" laydate-type="datetime" >
			</div>
			'.$this->explain.'
		</div>';
	}
    	
	/**
	 * 日期
	 */
    function datepicker()
    {
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'" placeholder="'.($this->placeholder?:'请选择日期').'" value="'.$value.'" laydate-type="date" >
			</div>
			'.$this->explain.'
		</div>';
    }
	
	/**
	 * 时间
	 */
    function timepicker()
    {
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'"  placeholder="'.($this->placeholder?:'请选择时间').'" value="'.$value.'" laydate-type="time" >
			</div>
			'.$this->explain.'
		</div>';
	}
    
	/**
	 * 日期区间
	 */
    function daterange()
    {
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date-range layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'" placeholder="'.($this->placeholder?:'请选择日期').'" value="'.$value.'" laydate-type="date" >
			</div>
			'.$this->explain.'
		</div>';
    }
    
	/**
	 * 时间区间
	 */
    function timerange()
    {
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date-range layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'" placeholder="'.($this->placeholder?:'请选择时间').'" value="'.$value.'" laydate-type="time" >
			</div>
			'.$this->explain.'
		</div>';
    }
    
	/**
	 * 日期时间区间
	 */
    function datetimerange(){
		$id = str_replace(['[',']'],'',$this->name);
		$value = is_int($this->value) ? date('Y-m-d H:i:s',$this->value) : $this->value;
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 440px;">
				<input type="text" class="layui-date-range layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$id.'" name="'.$this->name.'" placeholder="'.($this->placeholder?:'请选择日期时间').'" value="'.$value.'" laydate-type="datetime" >
			</div>
			'.$this->explain.'
		</div>';
    }
	/**
	 * 隐藏文本框
	 */
	function hidden()
	{
		return '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'">';
	}
	
	/**
	 * 单行文本框
	 * @param $form 表单数据
	 */
	function text()
	{
		$type = $this->form['setup']['ispassword'] == 1 || (isset($this->form['ispassword']) && $this->form['ispassword'])  ? 'password' : 'text';
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 650px;">
				<input type="'.$type.'" class="layui-input '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' id="'.$this->name.'" name="'.$this->name.'" placeholder="'.$this->placeholder.'" value="'.$this->value.'" '.$this->disabled.'>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 多行文本框
	 * @param $form 表单数据
	 */
	function textarea()
	{
		return '
		<div class="'.$this->itemclass.' layui-form-text" style="max-width: 759px;">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" >
				<textarea type="text" class="layui-textarea '.$this->inputclass.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' name="'.$this->name.'" placeholder="'.$this->placeholder.'" '.$this->disabled.'>'.$this->value.'</textarea>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 开关按钮
	 * @param $form 表单数据
	 */
	function switcher()
	{
		if(isset($this->form['layval']) && isset($this->form['layval'][1])){
			$val0 = $this->form['layval'][0];
			$val1 = $this->form['layval'][1];
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.' layui-input-pane" style="max-width: 440px;'.$this->style.'">
				<input name="'.$this->name.'" value="'.(isset($val0)?$val0:0).'" type="hidden" >
				<input name="'.$this->name.'" value="'.(isset($val1)?$val1:1).'" type="checkbox" lay-skin="switch" lay-text="'.(isset($this->form['laytext'])&&$this->form['laytext']?$this->form['laytext']:'开|关').'" '.(isset($this->value)&&$this->value?'checked':'').' lay-filter="'.$this->name.'" >
			</div>
			'.$this->explain.'
		</div>';
	}
	
    /**
	* 得到子级数组
	* @param int
	* @return array
	*/
	private function get_tree_child($myid)
	{
		$a = $newarr = array();
		if(is_array($this->lists)){
			foreach($this->lists as $id => $a){
				if($a['parent_id'] == $myid) $newarr[$id] = $a;
			}
		}
		return $newarr ? $newarr : false;
	}
	
    /**
	* 得到所有子级
	* @param int
	* @return array
	*/
	private function set_tree_child($id)
	{
		$child = $this->get_tree_child($id);
		if(is_array($child)){
			foreach($child as $key=>$val){
				$this->child[$val['id']] = 1;
				$this->set_tree_child($val['id']);
			}
		}
		return isset($this->child) ? $this->child : [];
	}
	
	/**
	 * 下拉选择(单选)
	 *
	 *　{:Form::select('标题名','字段名','value',['list'=>数组,'keys'=>['id','name'],'disabled'=>$vo['id']])}
	 *
	 *	相关参数说明：
	 * 　keys　	 ：根据list数组设置 [键 , 值] 如：['id','name']
	 ＊　disabled　：是否自动设置下级选项禁止选择 disablesub
	 ＊　ignore　  ：忽略表单美化渲染
	 */
	function select()
	{
		//list 参数优先于模型字段里的设置
		if( ! $this->list){
			$this->form['data'] = isset($this->form['setup']['option']) ? $this->form['setup']['option'] : false;
			//当值为空时,就设置默认值
			if( $this->value === '' && isset($this->form['setup']['value']) ){
				$this->value = $this->form['setup']['value'];
			}
		}
		$option = '';
		//字段串类型使用回车切割
		if( isset($this->form['data']) && ! is_array($this->form['data']) ){
			$data = explode("\n",$this->form['data']);
			if( is_array($data) ){
				$this->form['data'] = [];
				foreach($data as $k=>$v){
					$temp = explode('|',$v);
					if( empty($temp[0]) || empty($temp[1]) ){ continue; }
					$option.='<option value="'.trim($temp[0]).'" '.($this->value==$temp[0]?'selected':'').'>'.trim($temp[1]).'</option>';
				}
			}
		//list 为级数类型
		}else if( is_array($this->list) ){
			//循环组装
			foreach($this->list as $key=>$val){
				$disabled = '';
				if( is_array($val) && isset($this->keys) && isset($this->keys[1]) ){
					$value = $val[$this->keys[0]];
					if( isset($val['disabled']) && $val['disabled'] == 1){
						$disabled = 'disabled=true';
					}
                    //判断类型
                    if( gettype($value) == 'integer' ){
                        $selected = ( $this->value !== '' && (integer)$this->value == (integer)$value) ? 'selected' : '';
                    }else{
                        $selected = $this->value === $value ? 'selected' : '';
                    }
                    //禁止选择
					if( $this->disablesub && $this->disablesub == $value ){
						$disabled = 'disabled=true';
					}
					//html
					$option .= '<option value="'.trim($value).'" '.$selected.' '.$disabled.'>'.trim($val[$this->keys[1]]).'</option>';
				}else{
                    //当设置为可输入的下拉框时，默认key为val
                    if( $this->selectclass ){
                        $key = $val;
                    }
					//禁止选择
					if( $this->disablesub && $this->disablesub == $key ){
						$disabled = 'disabled=true';
					}
                    //判断类型
                    if( gettype($key) == 'integer' ){
                        $selected = (integer)$this->value === (integer)$key ? 'selected' : '';
                    }else{
                        $selected = $this->value === $key ? 'selected' : '';
                    }
					//html
					$option .= '<option value="'.trim($key).'" '.$selected.' '.$disabled.'>'.trim($val).'</option>';
				}
			}
		}
       
		//html
		return '
		<div class="'.$this->itemclass.' ">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'">
				<select '.$this->ignore.' id="'.$this->id.'" name="'.$this->name.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' lay-filter="'.$this->name.'" '.$this->disabled.' class="'.$this->selectclass.'" >
					<option value="'.$this->defaultvalue.'">'.($this->placeholder?:'请选择').'</option>'.$option.'
				</select>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 下拉列表(多选)
	 * {:Form::selects('标题名','表字段','值',[ 'list'=>'数组' ])}
	 * 例如：
	 * {:Form::selects('爱好','hobby','0,1',[ 'list'=>[0=>'打游戏',1=>'敲代码'] ])}
	 */ 
	function selects()
	{
		$option = '';
		//list 参数优先于模型字段里的设置
		$data = $this->form['list'] ? : $this->form['setup']['option'];
        //
        if( empty($data) ){
            $data = $this->form['data'] ? : [];
            $item = explode(',',$data);
            foreach($item as $k=>$v){
                $data_item[$v] = $v;
            }
            $data = $data_item;
        }
		//字段串类型使用回车切割
		if( ! is_array($data) ){
			$data = explode("\n",$data);
		}
		//当值为空时,就设置默认值
		if( $this->value === '' && isset($this->form['setup']['value']) ){
			$this->value = $this->form['setup']['value'];
		}
		if( isset($this->value) ){
			$value = explode(',',$this->value);
		}
		foreach($data as $k=>$v){
			$temp = explode('|',$v);
			if( ! isset($temp[0]) || ! isset($temp[1]) ){
				$temp[0] = $k;
				$temp[1] = $v;
			}
			$selected = '';
			if( is_array($temp) && is_array($value) && isset($temp[0]) ){
				foreach($value as $val){
					if( !empty($val) && $val == $temp[0] ){
						$selected = 'selected';
						break;
					}
				}
			}
			$option .= '<option value="'.$temp[0].'" '.$selected.'>'.$temp[1].'</option>';
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.' layui-multiselect">
				<input id="'.$this->name.'" name="'.$this->name.'" type="hidden" value="'.$this->value.'"  >
				<select multiple="multiple" lay-filter="'.$this->name.'" field="'.$this->name.'" '.$this->vertype.' '.$this->verify.'>
					<option value="">请选择</option>'.$option.'
				</select>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 栏目选项
	 */
	function category()
	{
		$this->keys = ['id','alias'];
		//判断栏目数据是否定义
		if( ! is_array($this->data['category']) ){
			//查询需要的栏目数据
			$this->list = (new \addons\cms\logic\Category)->data()['data'];
		}else{
			$this->list = $this->data['category'];
		}
		$option = '';
		if( is_array($this->list) ){
			//禁止选择处理
			if( $this->value!=='' && $this->disablesub && isset($this->keys) && isset($this->keys[1]) ){
				$this->lists = $this->list;
				$this->child = $this->set_tree_child($this->disablesub);
			}
			if( $this->value === '' ){
				$this->value = 'null';
			}
			//循环组装
			foreach($this->list as $key=>$val){
				$disabled = '';
				//disabled条件
				if( isset($this->form['disabledwhere']) ){
					$command = preg_replace('/\[(\w*?)\]/', '$val[\'\\1\']', $this->form['disabledwhere']);
					@(eval('$condition=(' . $command . ');'));
					if ( $condition ) {
						$disabled = 'disabled=true';
					}
				}
				if( is_array($val) && isset($this->keys) && isset($this->keys[1]) ){
					$value = $val[$this->keys[0]];
					//开启禁止选择下级
					if( $this->disablesub ){
						//禁止选择下级 和 选中自己
						if( isset( $this->child[$value] ) || $this->disablesub == $value){
							$disabled = 'disabled=true';
						}
					}
					if( isset($val['disabled']) && $val['disabled'] == 1){
						$disabled = 'disabled=true';
					}
					//选中
					$selected = $this->value === $value ? 'selected' : '';
					//html
					$option .= '<option value="'.$value.'" '.$selected.' '.$disabled.'>'.$val[$this->keys[1]].'</options>';
				}else{
					//禁止选择
					if( $this->disablesub && $this->disablesub == $key ){
						$disabled = 'disabled=true';
					}
					//选中
					$selected = $this->value === $key ? 'selected' : '';
					//html
					$option .= '<option value="'.$key.'" '.$selected.' '.$disabled.'>'.$val.'</options>';
				}
			}
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 650px;">
				<select '.$this->ignore.' id="'.$this->name.'" name="'.$this->name.'" '.$this->attr.' '.$this->vertype.' '.$this->verify.' lay-filter="'.$this->name.'" '.$this->disabled.' >
					<option value="'.$this->defaultvalue.'">'.($this->placeholder?:'请选择').'</option>'.$option.'
				</select>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 标签选项
	 */
	function tags()
	{
		//查询标签
		$tag_model = new \addons\cms\model\CmsTags;
		$this->data = $tag_model->field('id,title')->where(['status'=>1])->order('listorder desc,id desc')->select()->toArray();
		$option = '';
		if( $this->value ){
			$this->value = explode(',',$this->value);
		}
		foreach( $this->data as $k=>$v ){
			$selected = '';
			if( is_array($this->value) ){
				foreach( $this->value as $val ){
					if( $val == $v['title'] ){
						$selected = 'selected';
						break;
					}
				}
			}
			$option .= '<option value="'.$v['title'].'" '.$selected.'>'.$v['title'].'</option>';
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'" style="max-width: 650px;">
				<input id="'.$this->name.'" name="'.$this->name.'" type="hidden">
				<select multiple="multiple" id="'.$this->name.'-select" lay-filter="'.$this->name.'" class="layui-multiselect" field="'.$this->name.'" '.$this->vertype.' '.$this->verify.' '.$this->disabled.'>
					<option value="">请选择</option>
					'.$option.'
				</select>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 单选框
	 */
	function radio()
	{
		$input = '';
		//list 参数优先于模型字段里的设置
		$this->list = $this->list ? : ( $this->form['setup']['option'] ? : [] );
        //如果list数组为空，判断data是否有值
        if( empty($this->list) ){
            $data = isset($this->form['data']) && !empty($this->form['data']) ? $this->form['data'] : false;
            if( $data == false) return $this->name . ' 缺少 list 或 data 参数';
            $item = explode(',',$data);
            foreach($item as $k=>$v){
                $data_item[$v] = $v;
            }
            $this->list = $data_item;
        }
		//字段串类型使用回车切割
		if( ! is_array($this->list) ){
			$data = explode("\n",$this->list);
			if( is_array($data) ){
				$this->list = [];
				foreach($data as $k=>$v){
					$temp = explode('|',$v);
					$this->list[$temp[0]] = $temp[1];
				}
			}
		}
		//当值为空时,就设置默认值
		if( $this->value === '' && isset($this->form['setup']['value']) ){
			$this->value = $this->form['setup']['value'];
		}
		$selected = '';
		//循环组装选项 没做好
		if( isset($this->list) && is_array($this->list) ){
			foreach($this->list as $key=>$val){
				$input .= '<input type="radio" name="'.$this->name.'" value="'.$key.'" title="'.$val.'" '.($this->value==$key?"checked":"").'>';
			}
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.' layui-input-pane">
				'.$input.'
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 复选框
	 */
	function checkbox()
	{
		$input = '';
		//list 参数优先于模型字段里的设置
		$this->list = $this->list ? : ( $this->form['setup']['option'] ? : [] );
        //如果list数组为空，判断data是否有值
        if( empty($this->list) ){
            $data = isset($this->form['data']) && !empty($this->form['data']) ? $this->form['data'] : false;
            if( $data == false) return $this->name . ' 缺少 list 或 data 参数';
            $item = explode(',',$data);
            foreach($item as $k=>$v){
                $data_item[$v] = $v;
            }
            $this->list = $data_item;
        }
		//字段串类型使用回车切割
		if( ! is_array($this->list) ){
			$data = explode("\n",$this->list);
			if( is_array($data) ){
				$this->list = [];
				foreach($data as $k=>$v){
					$temp = explode('|',$v);
					$this->list[$temp[0]] = $temp[1];
				}
			}
		}
		//
		if( !empty($this->value) ){
			$this->value = json_decode($this->value,true);
		}
		//当值为空时,就设置默认值
		if( $this->value === '' && isset($this->form['setup']['value']) ){
			$this->value = $temp = explode(',',$this->form['setup']['value']);
		}
		//循环组装选项
		if( isset($this->list) && is_array($this->list) ){
			foreach($this->list as $key=>$val){
				$checked = '';
				if( is_array($this->value) ){
					foreach($this->value as $value){
						if( $value == $key ){
							$checked = 'checked';
							break;
						}
					}
				}
				$input .= '<input type="checkbox" name="'.$this->name.'[]" lay-skin="primary" value="'.$key.'" title="'.$val.'" '.$checked.'>';
			}
		}
		return '
		<div class="'.$this->itemclass.'">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" />
			<div class="layui-input-'.$this->display.' layui-input-pane">
				'.$input.'
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 富文本编辑器
	 * @param $form 表单数据
	 */
	function editor()
	{
		$width = $this->form['setup']['width'] ? : ( $this->form['width'] ? : '100%');
		$height = $this->form['setup']['height'] ? : ( $this->form['height'] ? : '360px');
		$value = htmlspecialchars_decode($this->value);
		return '
		<div class="'.$this->itemclass.' layui-form-text">
			<label class="layui-form-label" >'.$this->title.'</label>
			<div class="layui-input-'.$this->display.'">
				<div id="'.$this->name.'_loading" style="line-height:40px;" onclick="window.location.reload()">正在加载编辑器...　　　(如果无法加载，请刷新)</div>
				<script type="text/plain" class="easy-editor" id="'.$this->name.'" name="'.$this->name.'" style="width:'.$width.';height:'.$height.';">'.$value.'</script>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 列表框组件
	 * @param $form 表单数据
	 */
	function listinput()
	{
		$a_html = '<a class="layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a>';
		$this->value = json_decode($this->value,true);
		if( is_array( $this->value ) ){
			$i=0;
			foreach($this->value as $key=>$val){
				$list .= '<div class="list-box">
					<input name="'.$this->name.'['.$i.'][key]" value="'.$val['key'].'" />
					<input name="'.$this->name.'['.$i.'][value]" value="'.$val['value'].'" />'.$a_html.'
				</div>';
				$i++;
			}
		}
		//设置input的label
		if( $this->label === false ){
			$this->label[0] = '键名';
			$this->label[1] = '键值';
		}
		return '
		<div class="layui-form-item layui-form-text">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" value />
			<div class="easy-form-listinput easy-form-sortbut">
				<div class="label"><span>'.$this->label[0].'</span><span>'.$this->label[1].'</span></div>
				'.$list.'
				<a data-name="'.$this->name.'" class="layui-btn layui-btn-sm easy-listinput-add"><i class="layui-icon">&#xe608;</i> 添加</a>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	/**
	 * 单图片上传组件
	 * @param $form 表单数据
	 */
	function image()
	{
        $id = str_replace(['[',']'],'',$this->name);
		return '
		<div class="layui-form-item">
			<label class="layui-form-label">'.$this->title.'</label>
			<div class="layui-input-block easy-input-imagebox">
				<input type="text" class="layui-input" id="'.$id.'" name="'.$this->name.'" '.$this->vertype.' '.$this->verify.' placeholder="'.($this->placeholder?:"请上传图片").'" value="'.$this->value.'" style="'.($this->style ? : '').'">
				<a class="layui-btn layui-upload easy-upload-image" lay-data="{field:\''.$id.'\'}"><i class="layui-icon">&#xe608;</i> 上传图片</a>
                <img class="img easy-btn-imageIframe" id="'.$id.'_img" src="'.$this->value.'" />
			</div>
            '.$this->explain.'
		</div>';
	}
	
	/**
	 * 多图片上传组件
	 * @param $form 表单数据
	 */
	function images()
	{
		$a_html = '<li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-back"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-right"></i></a></li>';
		if( !is_array( $this->value ) ){
			$this->value = json_decode($this->value,true);
		}
		if( is_array( $this->value ) && count($this->value ) > 0 ){
			$i=0;
			foreach($this->value['title'] as $key=>$val){
				$list .= '<div class="list-box">
                    <img src="'.$this->value['url'][$key].'"/>
                    <div>
					<input placeholder="图片标题" name="'.$this->name.'[title][]" value="'.$val.'" />
					<input placeholder="图片地址" name="'.$this->name.'[url][]" value="'.$this->value['url'][$key].'" />
					<input placeholder="跳转链接" name="'.$this->name.'[href][]" value="'.$this->value['href'][$key].'"/>'.$a_html.'
                    </div>
				</div>';
				$i++;
			}
		}
		return '
		<div class="layui-form-item layui-form-text" style="max-width:759px;">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" value />
			<div class="easy-form-images easy-form-sortbut">
				<div class="list-conten">'.$list.'</div>
				<a data-name="'.$this->name.'" class="layui-btn layui-btn-sm layui-upload easy-upload-images"><i class="layui-icon">&#xe608;</i> 上传图片</a>
			</div>
			'.$this->explain.'
		</div>';
	}
    
	/**
	 * 大小图片上传组件
	 * @param $form 表单数据
	 */
	function imagebs()
	{
		$a_html = '<li class="easy-form-imagebs-tool"><a class="del layui-btn layui-btn-sm layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li>';
		
		if( !is_array( $this->value ) ){
			$this->value = json_decode($this->value,true);
		}
		if( is_array( $this->value ) && count($this->value ) > 0 ){
			$i=0;
			foreach($this->value['title'] as $key=>$val){
				$list .= '<div class="list-box">
                    <li>
                        <img id="bimg_'.$key.'_img" src="'.$this->value['bimg'][$key].'" class="easy-btn-imageIframe"/>
                        <a class="layui-btn layui-btn-sm easy-upload-image" lay-data="{field:\'bimg_'.$key.'\'}" title="上传或替换">上传</a>
                        <input name="'.$this->name.'[bimg][]" value="'.$this->value['bimg'][$key].'" class="hidden" id="bimg_'.$key.'" />
                    </li>
                    <li>
                        <img id="simg_'.$key.'_img" src="'.$this->value['simg'][$key].'" class="easy-btn-imageIframe"/>
                        <a class="layui-btn layui-btn-sm easy-upload-image" lay-data="{field:\'simg_'.$key.'\'}" title="上传或替换">上传</a>
                        <input name="'.$this->name.'[simg][]" value="'.$this->value['simg'][$key].'" class="hidden" id="simg_'.$key.'" />
                    </li>
                    <input name="'.$this->name.'[title][]" value="'.$val.'" />
					<input name="'.$this->name.'[href][]" value="'.$this->value['href'][$key].'" />'.$a_html.'
				</div>';
				$i++;
			}
		}
		//设置input的label
		if( $this->label === false ){
			$this->label[0] = '大图';
			$this->label[1] = '小图';
			$this->label[2] = '名称';
			$this->label[3] = '链接';
		}
		return '
		<div class="layui-form-item layui-form-text" style="max-width:759px;">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" value />
			<div class="easy-form-imagebs easy-form-sortbut">
				<div class="label"><input disabled value='.$this->label[0].' ><input disabled value='.$this->label[1].' ><input disabled value='.$this->label[2].' ><input disabled value='.$this->label[3].' ><li>删除 / 排序</li></div>
				<div class="list-conten">'.$list.'</div>
				<a data-name="'.$this->name.'" class="layui-btn layui-btn-sm easy-upload-imagebs">添加图片</a>
			</div>
			'.$this->explain.'
		</div>';
	}    
	
	/**
	 * 单文件上传组件
	 * @param $form 表单数据
	 */
	function file()
	{
		return '
		<div class="layui-form-item">
			<label class="layui-form-label">'.$this->title.'</label>
			<div class="layui-input-block easy-input-filebox">
				<input type="text" class="layui-input" id="'.$this->name.'" name="'.$this->name.'" '.$this->vertype.' '.$this->verify.' placeholder="'.($this->placeholder?:"请上传文件").'" value="'.$this->value.'" style="'.($this->style ? : '').'" >
				<a class="layui-btn layui-btn-normal layui-upload easy-upload-file" lay-data="{field:\''.$this->name.'\'}"><i class="layui-icon">&#xe608;</i> 上传文件</a>
			</div>
			
		</div>';
	}
	
	/**
	 * 多文件上传组件
	 * @param $form 表单数据
	 */
	function files()
	{
		$a_html = '<li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li>';
		if( !is_array( $this->value ) ){
			$this->value = json_decode($this->value,true);
		}
		if( is_array( $this->value ) && count($this->value ) > 0 ){
			foreach($this->value['title'] as $key=>$val){
				$list .= '<div class="list-box">
					<input name="'.$this->name.'[title][]" value="'.$val.'" />
					<input name="'.$this->name.'[url][]" value="'.$this->value['url'][$key].'" />
					<input name="'.$this->name.'[ext][]" value="'.$this->value['ext'][$key].'" />
					<input name="'.$this->name.'[size][]" value="'.$this->value['size'][$key].'" />'.$a_html.'
				</div>';
				$i++;
			}
		}
		//设置input的label
		if( $this->label === false ){
			$this->label[0] = '文件名称';
			$this->label[1] = '地址';
			$this->label[2] = '类型';
			$this->label[3] = '大小(字节)';
		}
		return '
		<div class="layui-form-item layui-form-text" style="max-width:759px;">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" value />
			<div class="easy-form-listinput easy-form-sortbut">
				<div class="label"><input disabled value='.$this->label[0].' ><input disabled value='.$this->label[1].' ><input disabled value='.$this->label[2].' ><input disabled value='.$this->label[3].' ><li>删除</li></div>
				<div class="list-conten">'.$list.'</div>
				<a data-name="'.$this->name.'" class="layui-btn layui-btn-sm layui-upload easy-upload-files layui-btn-normal"><i class="layui-icon">&#xe608;</i> 上传文件</a>
			</div>
			'.$this->explain.'
		</div>';
	}
	
	
	/**
	 * 省市县区选择器
	 */
	function citypicker()
    {
		return '
		<div class="'.$this->itemclass.'" >
			<label class="layui-form-label" >'.($this->title?:'省市县').'</label>
			<div class="layui-input-inline" style="'.($this->style ? : 'width:480px').'" >
				<input type="text" autocomplete="on" class="layui-input layui-city-picker" id="'.$this->name.'" name="'.$this->name.'" readonly="readonly" data-toggle="city-picker" placeholder="请选择" data-value="'.$this->value.'">
			</div>
			'.$this->explain.'
		</div>';
	}
    
	/**
	 * 动态字段列表 html模板
	 */
    function fieldlist_temp($key,$val)
    {
        $a_html = '<li><a class="del layui-btn layui-btn-danger"><i class="iconfont icon-close_light"></i></a><a class="up layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-top"></i></a><a class="down layui-btn layui-btn-sm layui-btn-primary"><i class="iconfont icon-down"></i></a></li>';
        return '<div class="list-box">
            <input class="arrkey" name="'.$this->name.'[key][]" value="'.$key.'" />
            <input class="arrval" name="'.$this->name.'[val][]" value="'.$val.'" />'.$a_html.'
        </div>';
    }
    
	/**
	 * 动态字段列表
	 */
	function fieldlist()
	{  
        if( ! empty($this->value) ){
            if( isset( $this->value['key'] ) && is_array( $this->value['key'] ) && count($this->value ) > 0 ){

                foreach($this->value['key'] as $key=>$val){
                    $list .= $this->fieldlist_temp($val,$this->value['val'][$key]);
                    $i++;
                }
            }else if( is_array( $this->value ) && count($this->value ) > 0 ){
                foreach($this->value as $key=>$val){
                    $list .= $this->fieldlist_temp($key,$val);
                    $i++;
                }
            }else if( is_string($this->value) ) {
                $value = json_decode($this->value,true);
                foreach($value['key'] as $k=>$v){
                    $list .= $this->fieldlist_temp($v,$value['val'][$k]);
                    $i++; 
                }
            }
        }
		//设置input的label
		if( $this->label === false ){
			$this->label[0] = '键';
			$this->label[1] = '值';
		}
		return '
		<div class="layui-form-item layui-form-text" style="max-width:759px;">
			<label class="layui-form-label" >'.$this->title.'</label>
			<input type="hidden" name="'.$this->name.'" value />
			<div class="easy-form-listinput easy-form-sortbut">
				<div class="label"><input disabled value="键" ><input disabled value='.$this->label[1].' ><li></li></div>
				<div class="list-conten">'.$list.'</div>
				<a data-name="'.$this->name.'" class="layui-btn layui-btn-sm easy-fieldlist-add"><i class="layui-icon">&#xe608;</i> 添加</a>
			</div>
			'.$this->explain.'
		</div>';
	}

	/**
	 * 根据表单类型生成
	 * @param $type 表单类型
	 * @param $param 表单数据
	 */
	function input($type,$param=false)
	{
		if( !isset($type) || empty($type) ) return '<div>未定义表单类型</div>';
		$data = $param['data'];
		$this->setParam($param);
		//根据类型输出表单
		switch($type){
			//数字
			case 'number':
				//强制前端必须输入数字
				if( isset($param['verify']) || ! empty($param['verify']) ){
					$verify = explode('|',$param['verify']);
					if( ! in_array('number',$verify) ){
						$verify[] = 'number';
					}
					$this->verify = 'lay-verify="'.implode('|',$verify).'"';
				}else{
					$this->verify = 'lay-verify="number"';
				}
				return $this->text();
				break;
			default:
				if( isset($data) && isset($param['name']) ){
					if( isset($data[$param['name']]) ){
						$this->value = $data[$param['name']];
					}
				}
				return $this->$type();
				break;
		}
		return '';
	}

}