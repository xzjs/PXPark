$(function() {
	a();
	// 客户端分页，纯属演示效果
	   
})
var result;
function  a(){
	
	var type = $('select  option:selected').val();
	//alert(type);
	 $.ajax({
		 url:"../Super/tradManager",
	        type:"post",
	        ContentType:"application/json",
	        data:{type},
	        async :false,
	        success:function(data){
	      //   alert(data);
	           var d=eval("(" + data+ ")");
	       result= d;
	       //alert (result);
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
		/*var rows = [];
		for (var i = 1; i <= 30; i++) {
			var amount = Math.floor(Math.random() * 1000);
			var price = Math.floor(Math.random() * 1000);
			rows.push({
				'carNo' : '鲁B3409',
				'name':'徐峥',
				'telphone' : '18733452834',
				'parking' : '五龙街道地下停车场',
				'position' : '5-03',
				'regTelphone' : '18733452834',
				'inTime' : '2015-10-02 12:12:12',
				'outTime' : '2015-10-02 12:16:12',
				'sumConsume' : '3000元'
			});
		}
		return rows;*/
        return park_record;
	}


	$('#tradeGrid')
			.datagrid(
					{
						url : '',
						method : 'GET',
						striped : true,
						fitColumns : true,
						rownumbers : true,
						fit : true,
						singleSelect : true,
						pagination : true,
						nowrap : false,
						pageSize : 20,
						pageList : [ 10, 20, 50, 100, 150, 200 ],
						toolbar:'#tb',
						columns : [ [
								{
									field : 'carNo',
									title : '车牌号',
									width : 100,
									align : 'center'
								},
								{
									field : 'name',
									title : '付费用户名',
									width : 150,
									align : 'center'
								},
								{
									field : 'telphone',
									title : '付费用户手机号',
									width : 100,
									align : 'center'
								},
								{
									field : 'parking',
									title : '所停停车场',
									width : 180,
									align : 'center'
								},
								{
									field : 'position',
									title : '所停车位',
									width : 100,
									align : 'center'
								},
								{
									field : 'regTelphone',
									title : '停车场注册手机号',
									width : 100,
									align : 'center'
								},
								{
									field : 'inTime',
									title : '入场时间',
									width : 100,
									align : 'center'
								},
								{
									field : 'outTime',
									title : '出场时间',
									width : 100,
									align : 'center'
								},
								{
									field : 'sumConsume',
									title : '共消费',
									width : 100,
									align : 'center'
								},
								{
									field : 'managerStatus',
									title : '管理状态',
									width : 80,
									align : 'center',
									formatter : function(value, row, index) {
										return '<select style="color: #000;height:15px;"><option>正常</option><option>返款</option><option>暂停交易</option></select>';
									}
								} ] ]
					});
	
	$('#tradeGrid').datagrid({
		data : result
	}).datagrid('clientPaging');
	
	var pager = $('#tradeGrid').datagrid('getPager');    // 得到datagrid的pager对象  
	pager.pagination({   
	    buttons:[{    
	        iconCls:'icon-excel',    
	        handler:function(){    
	            alert('导出excel');    
	        }    
	    }]
	}); 
	        }
});}