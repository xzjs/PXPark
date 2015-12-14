$(function() {
	// 客户端分页，纯属演示效果
	function pagerFilter(data) {
		if ($.isArray(data)) {
			data = {
				total : data.length,
				rows : data
			}
		}
		var target = this;
		var dg = $(target);
		var state = dg.data('datagrid');
		var opts = dg.datagrid('options');
		if (!state.allRows) {
			state.allRows = (data.rows);
		}
		if (!opts.remoteSort && opts.sortName) {
			var names = opts.sortName.split(',');
			var orders = opts.sortOrder.split(',');
			state.allRows.sort(function(r1, r2) {
				var r = 0;
				for (var i = 0; i < names.length; i++) {
					var sn = names[i];
					var so = orders[i];
					var col = $(target).datagrid('getColumnOption', sn);
					var sortFunc = col.sorter || function(a, b) {
						return a == b ? 0 : (a > b ? 1 : -1);
					};
					r = sortFunc(r1[sn], r2[sn]) * (so == 'asc' ? 1 : -1);
					if (r != 0) {
						return r;
					}
				}
				return r;
			});
		}
		var start = (opts.pageNumber - 1) * parseInt(opts.pageSize);
		var end = start + parseInt(opts.pageSize);
		data.rows = state.allRows.slice(start, end);
		return data;
	}

	var loadDataMethod = $.fn.datagrid.methods.loadData;
	var deleteRowMethod = $.fn.datagrid.methods.deleteRow;
	$.extend($.fn.datagrid.methods, {
		clientPaging : function(jq) {
			return jq.each(function() {
				var dg = $(this);
				var state = dg.data('datagrid');
				var opts = state.options;
				opts.loadFilter = pagerFilter;
				var onBeforeLoad = opts.onBeforeLoad;
				opts.onBeforeLoad = function(param) {
					state.allRows = null;
					return onBeforeLoad.call(this, param);
				}
				var pager = dg.datagrid('getPager');
				pager.pagination({
					onSelectPage : function(pageNum, pageSize) {
						opts.pageNumber = pageNum;
						opts.pageSize = pageSize;
						pager.pagination('refresh', {
							pageNumber : pageNum,
							pageSize : pageSize
						});
						dg.datagrid('loadData', state.allRows);
					}
				});
				$(this).datagrid('loadData', state.data);
				if (opts.url) {
					$(this).datagrid('reload');
				}
			});
		},
		loadData : function(jq, data) {
			jq.each(function() {
				$(this).data('datagrid').allRows = null;
			});
			return loadDataMethod.call($.fn.datagrid.methods, jq, data);
		},
		deleteRow : function(jq, index) {
			return jq.each(function() {
				var row = $(this).datagrid('getRows')[index];
				deleteRowMethod.call($.fn.datagrid.methods, $(this), index);
				var state = $(this).data('datagrid');
				if (state.options.loadFilter == pagerFilter) {
					for (var i = 0; i < state.allRows.length; i++) {
						if (state.allRows[i] == row) {
							state.allRows.splice(i, 1);
							break;
						}
					}
					$(this).datagrid('loadData', state.allRows);
				}
			});
		},
		getAllRows : function(jq) {
			return jq.data('datagrid').allRows;
		}
	})

	function getData() {
		var rows = [];
		for (var i = 1; i <= 30; i++) {
			var amount = Math.floor(Math.random() * 1000);
			var price = Math.floor(Math.random() * 1000);
			rows.push({
				'nick' : 'nick',
				'telphone' : '18733452834',
				'name' : '徐峥',
				'cardId' : '22958912884993',
				'memeberLevel' : 'VIP',
				'creditLevel' : '一级',
				'points' : '23000',
				'parkingNum' : '23'
			});
			rows.push({
				'nick' : 'tom',
				'telphone' : '1576363457889',
				'name' : '黄渤',
				'cardId' : '22958912884998',
				'memeberLevel' : 'VIP',
				'creditLevel' : '一级',
				'points' : '23000',
				'parkingNum' : '238'
			});
			rows.push({
				'nick' : 'jack',
				'telphone' : '1576363857889',
				'name' : '赵飞',
				'cardId' : '23958912884998',
				'memeberLevel' : 'VIP',
				'creditLevel' : '一级',
				'points' : '23000',
				'parkingNum' : '248'
			});
		}
		return rows;
	}


	$('#parkingGrid')
			.datagrid(
					{
						title : '个人招租停车场',
						url : '',
						method : 'GET',
						striped : true,
						fitColumns : true,
						rownumbers : true,
						rownumbers : true,
						fit : true,
						singleSelect : true,
						pagination : true,
						nowrap : false,
						pageSize : 10,
						pageList : [ 10, 20, 50, 100, 150, 200 ],
						columns : [ [
								{
									field : 'nick',
									title : '用户昵称',
									width : 180,
									align : 'center'
								},
								{
									field : 'telphone',
									title : '用户注册手机号',
									width : 150,
									align : 'center'
								},
								{
									field : 'name',
									title : '用户真实姓名',
									width : 100,
									align : 'center'
								},
								{
									field : 'cardId',
									title : '用户身份证号',
									width : 100,
									align : 'center'
								},
								{
									field : 'memberLevel',
									title : '会员等级',
									width : 100,
									align : 'center'
								},
								{
									field : 'creditLevel',
									title : '信誉等级',
									width : 100,
									align : 'center'
								},
								{
									field : 'parkingNum',
									title : '对外招租车位个数',
									width : 100,
									align : 'center'
								},
								{
									field : 'parkingInfo',
									title : '对外招租车位信息',
									width : 100,
									align : 'center',
									formatter : function(value, row, index) {
										return '<a class="table_row_btn" href="javascript:showParkingDetail();">详情</a>';
									}
								},
								{
									field : 'verifyStatus',
									title : '审核状态',
									width : 80,
									align : 'center',
									formatter : function(value, row, index) {
										return '<select style="color: #000;height:15px;"><option>通过</option><option>未通过</option></select>';
									}
								},
								{
									field : 'history',
									title : '招租历史',
									width : 100,
									align : 'center',
									formatter : function(value, row, index) {
										return '<a class="table_row_btn" href="javascript:showHistoryDetail();">详情</a>';
									}
								},
								{
									field : 'managerStatus',
									title : '管理状态',
									width : 80,
									align : 'center',
									formatter : function(value, row, index) {
										return '<select style="color: #000;height:15px;"><option>正常</option><option>暂时封号</option><option>永久封号</option></select>';
									}
								} ] ]
					});
	
	
					
	$('#parkingGrid').datagrid({
		data : getData()
	}).datagrid('clientPaging');
	
	//分页工具栏上添加导出excel
	var pager = $('#parkingGrid').datagrid('getPager');    // 得到datagrid的pager对象  
	pager.pagination({   
	    buttons:[{    
	        iconCls:'icon-excel',    
	        handler:function(){    
	            alert('导出excel');    
	        }    
	    }]
	});    
})

function showParkingDetail(){
	$('#parkingDetailInfoWin').modal();
}

function showHistoryDetail(){
	$('#historyDetailInfoWin').modal();
}