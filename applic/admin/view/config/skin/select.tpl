<style>
html,body{ margin: 0; padding: 0; background: #f1f4f6; overflow: hidden;}
#elem-quote{display: none}
#skinBox ul{ margin: 0; padding: 10px 5px; display: block;overflow: hidden}
#skinBox ul li{ width:55px; height: 55px; float: left; padding: 7px;text-align: center;list-style-type: none;}
#skinBox ul li a{ width:100%; height: 33px; display: block;    box-shadow: 0 0 3px rgba(0,0,0,0.4);opacity: 0.9; cursor: pointer;}
#skinBox ul li a:hover{ opacity: 1; }
#skinBox ul li a span{ width:100%; height: 7px; display: block;}
#skinBox ul li a i{display:block; float: left;}
#skinBox ul li p{ width:100%; height: 18px; line-height: 20px;  font-size: 12px; color: #404548; margin: 0;}

{volist name="configskin" id="data"}
<?php $skin_data = $data['skin_data'] ?>
.skin_{$data['skin_name']}{
background-image: -webkit-linear-gradient(left, {$skin_data['maincolor']}, {$skin_data['assistcolor']} );
background-image: -o-linear-gradient(left, {$skin_data['maincolor']}, {$skin_data['assistcolor']} );
background-image: linear-gradient(to right, {$skin_data['maincolor']}, {$skin_data['assistcolor']} );		
}
{/volist}
</style>

<aside id="skinBox">
	<ul>
		{volist name="configskin" id="data"}
		<?php $skin_data = $data['skin_data'] ?>
		<li class="setSink" data-id="{$data.id}" data-name="{$data.skin_name}">
			<a>
				<span class="skin_{$data.skin_name}">
					<i style="width:20%;height:7px;"></i>
					<i style="width:80%;height:7px;"></i>
				</span>
				<span>
					<i style="width:20%;height:26px;background:{$skin_data['sidebar']};"></i>
					<i style="width:80%;height:26px;background:#f1f4f6;"></i>
				</span>
			</a>
			<p>{$data.skin_name}</p>
		</li>
		{/volist}
	</ul>
</aside>

<script>
easy.define(function(){
	//自定义皮肤
	$('#skinBox .setSink').click(function(){
		var skin_name = $(this).attr('data-name');
		var href_value = "{:config('param.skinpath')}" + skin_name +'.css?t='+ (new Date()).getTime();
		var parent = window.parent.document;
		$('#skinlink',parent).attr('href',href_value);
		$('#console-iframe-list',parent).find('iframe').contents().find('#skinlink').attr('href',href_value);
		localStorage.setItem('skinname', skin_name);
	});
});
</script>