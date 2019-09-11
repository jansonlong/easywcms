layui.define("form", function(exports) {
	var MOD_NAME = "multiSelect",
		o = layui.jquery,
		form = layui.form,
		selected_vals = [],
		selected_data = [],
		multiSelect = function() {};

	multiSelect.prototype.init = function() {
		
		var ts = this;
		o('select[multiple]').each(function(idx, item) {
			var field = o(this).attr('field');
			var t = o(this),
				selds = [];
			t.find('option:selected').each(function() {
				selds.push(o(this).val());
			})
            var obj = t.next().addClass('multi').find('.layui-select-title');
            //添加验证
            if( t.attr('lay-verify') ){
                obj.find('input').attr({'lay-verify':t.attr('lay-verify'),'lay-vertype':'tips'});
            }
			obj.click(function() {
				selected_data[idx] && o('#'+field).val(selected_data[idx].join(','))
				selected_vals[idx] && o(this).find('input').val(selected_vals[idx].join(','));
			}).next().find('dd').each(function() {
				var dt = o(this),
					checked = (dt.hasClass('layui-this') || o.inArray(dt.attr('lay-value'), selds) > -1) ? 'checked' : '',
					title = dt.text(),
					data = dt.attr('lay-value'),
					disabled = dt.attr('lay-value') === '' ? 'disabled' : '';
				dt.html('<input type="checkbox" lay-skin="primary" data="' + data + '" title="' + title + '" ' + checked + ' ' + disabled + '>');
				ts.selected(idx, t, dt,field);
			}).click(function(e) {
				var dt = o(this);
				// 点击下拉框每一行触发选中和反选
				if(e.target.localName == 'dd' && dt.attr('lay-value') !== '') {
					var status = dt.find('.layui-form-checkbox').toggleClass('layui-form-checked').hasClass('layui-form-checked');
					dt.find('input').prop('checked', status);
				}
				// 禁止下拉框收回
				dt.parents('.layui-form-select').addClass('layui-form-selected');
				ts.selected(idx, t, dt,field);
			});
			
		})
		form.render('checkbox');
	}

	multiSelect.prototype.selected = function(idx, t, dt,field) {
		// 选中值存入数组
		selected_vals[idx] = [];
		selected_data[idx] = [];
		// 先清除真实下拉款选中的值，在下面循环中重新赋值选中
		t.find('option').prop('selected', false);
		dt.parents('dl').find('[type=checkbox]:checked').each(function() {
			var val = o(this).parent().attr('lay-value');
			t.find('option[value=' + val + ']').prop('selected', true);
			selected_data[idx].push(o(this).attr('data'));
			selected_vals[idx].push(o(this).attr('title'));
		})
		// 显示已选信息
		selected_data[idx] && o('#'+field).val(selected_data[idx].join(','))
		dt.parents('dl').prev().find('input').val(selected_vals[idx].join(','));		
	}

	multiSelect.prototype.render = function(type, filter) {
		form.render(type, filter);
		this.init();
	}

	var i = new multiSelect();
	i.init();

	exports(MOD_NAME, i);
});