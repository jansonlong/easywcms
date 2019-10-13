<style>.layui-form-item .layui-input-inline{ width:240px;}
    .select-ico{ position:absolute; bottom: 0; right: 0; height: 38px; width: 38px; padding: 0}
    .select-ico i{ margin-right: 0!important}
    .iconlist-box{margin:5px; background: #fff; overflow: hidden; border-top: 1px solid #efefef;border-left: 1px solid #efefef;}
    .iconlist-box li{ width: 80px; height: 80px; float: left; text-align: center; border-right: 1px solid #efefef; border-bottom: 1px solid #efefef; cursor: pointer;}
    .iconlist-box li i{ width: 100%; height: 40px; display: block; font-size: 30px; padding-top: 24px; color:#333}
    .iconlist-box li:hover{ background-color:#efefef}
</style>
<form id="easy-form" class="layui-form layui-form-pane easy-form-main" method="post">
	<div class="layui-form-item">
    <?php 
        if( isset($_GET['parent_id']) && !isset($vo['parent_id']) && !empty($_GET['parent_id']) ){
            $vo['parent_id'] = $_GET['parent_id'];
            $disabled = 'disabled';
        }
        if( isset($_GET['menutype']) && (int)$_GET['menutype'] === 0 && !isset($vo['menutype']) ){
            $vo['menutype'] = $_GET['menutype'];
        }else if( !isset($_GET['menutype']) && !isset($vo['menutype']) ){
            $vo['menutype'] = '-1';
        }
    ?>
        
    {:Form::select('上级','parent_id',$vo.parent_id,['list'=>$list,'keys'=>['id','title'],'verify'=>'required','disablesub'=>$vo.id,'defaultvalue'=>0,'placeholder'=>'作为父级','ignore'=>1,'disabled'=>$disabled])}

    {:Form::select('类型','menutype',$vo.menutype,['list'=>[1=>'菜单',0=>'子操作'],'verify'=>'required','ignore'=>1,'disabled'=>$disabled])}
	</div>

	<div class="layui-form-item">
		{:Form::text('图标','fontico',$vo.fontico,['disabled'=>'disabled','placeholder'=>'请点击右边按钮选择图标'])}
		{:Form::switcher('状态','status',$vo.status,['layval'=>[0,1]])}
	</div>
    
	<div class="layui-form-item">
	{:Form::text('标题','title',$vo.title,['verify'=>'required','placeholder'=>'例如：文章管理'])}
        
    {:Form::select('按钮类型','btnclass',$vo.btnclass,['list'=>['easy-btn-add'=>'添加','easy-btn-edit'=>'编辑','easy-btn-delete'=>'删除','exportFile'=>'表格导出','easy-btn-iframe'=>'弹出窗口'],'ignore'=>1])}
	</div>
    
	{:Form::select('模块','module',$vo.module,['verify'=>'required','list'=>$modulelist,'ignore'=>1])}
	<div style="height: 22px; line-height:18px; color: #8a8a8a">
		规则：不需要写入模块名，只需选择相应的 [模块] 即可。例如：/控制器/方法名　(支持多级控制器)
	</div>
	{:Form::text('规则','name',$vo.name,['verify'=>'required','placeholder'=>'例如：/console/index'])}
	<div style="height: 22px; line-height: 18px; color: #8a8a8a">
		参数：填写例如 userid=1&sex=2
	</div>
	{:Form::text('参数','parameter',$vo.parameter,['verify'=>'parameter','placeholder'=>'例如：userid=1&sex=2'])}
	

	{:Form::text('规则描述','description',$vo.description,['placeholder'=>'请输入规则描述'])}
	{if (isset($vo.id))}<input type="hidden" name="id" value="{$vo.id|default=''}">{/if}
	<button lay-submit lay-filter="easy-form" id="submit" style=" display:none" ></button>
</form>

<div id="iconlist-box" class="iconlist-box" style="display: none;">
    {volist name="iconlist" id="r"}
    <li data-ico="icon-{$r}"><i class="iconfont icon-{$r}"></i></li>
    {/volist}
</div>