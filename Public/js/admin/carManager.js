$(function() {
//	now_che();
	$('.sel').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/carManager.html';
		
	});
	$('#statisticInfo').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/statisticInfo.html';
		
	});
	
	$('#userInfo').on('click', function() {
		window.location.href='/PXPark/index.php/Home/Super/userInfo.html';
		
	});
	// 客户端分页，纯属演示效果
	function pagerFilter(data) {
		if($.isArray(data)) {
			data = {
				total: data.length,
				rows: data
			}
		}
		var target = this;
		var dg = $(target);
		var state = dg.data('datagrid');
		var opts = dg.datagrid('options');
		if(!state.allRows) {
			state.allRows = (data.rows);
		}
		if(!opts.remoteSort && opts.sortName) {
			var names = opts.sortName.split(',');
			var orders = opts.sortOrder.split(',');
			state.allRows.sort(function(r1, r2) {
				var r = 0;
				for( var i = 0; i < names.length; i++) {
					var sn = names[i];
					var so = orders[i];
					var col = $(target).datagrid('getColumnOption', sn);
					var sortFunc = col.sorter || function(a, b) {
						return a == b ? 0 : (a > b ? 1 : -1);
					};
					r = sortFunc(r1[sn], r2[sn]) * (so == 'asc' ? 1 : -1);
					if(r != 0) {
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
		clientPaging: function(jq) {
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
					onSelectPage: function(pageNum, pageSize) {
						opts.pageNumber = pageNum;
						opts.pageSize = pageSize;
						pager.pagination('refresh', {
							pageNumber: pageNum,
							pageSize: pageSize
						});
						dg.datagrid('loadData', state.allRows);
					}
				});
				$(this).datagrid('loadData', state.data);
				if(opts.url) {
					$(this).datagrid('reload');
				}
			});
		},
		loadData: function(jq, data) {
			jq.each(function() {
				$(this).data('datagrid').allRows = null;
			});
			return loadDataMethod.call($.fn.datagrid.methods, jq, data);
		},
		deleteRow: function(jq, index) {
			return jq.each(function() {
				var row = $(this).datagrid('getRows')[index];
				deleteRowMethod.call($.fn.datagrid.methods, $(this), index);
				var state = $(this).data('datagrid');
				if(state.options.loadFilter == pagerFilter) {
					for( var i = 0; i < state.allRows.length; i++) {
						if(state.allRows[i] == row) {
							state.allRows.splice(i, 1);
							break;
						}
					}
					$(this).datagrid('loadData', state.allRows);
				}
			});
		},
		getAllRows: function(jq) {
			return jq.data('datagrid').allRows;
		}
	})
	function getData() {
		var rows = [];
		for( var i = 1; i <= 30; i++) {
			var amount = Math.floor(Math.random() * 1000);
			var price = Math.floor(Math.random() * 1000);
			rows.push({
				'cheh': '鲁B12345',
				'cartype': '小汽车',
				'starttime': '2015-11-1 12:00:01',
				'endtime': '2015-11-1 15:00:01',
				'parkNo': '1',
				'staytime': '3',
				'memeberLevel': 'VIP',
				'cost': '100.00'
			});
		}
		return rows;
	}
	$('#personGrid').datagrid({
		url: '',
		method: 'GET',
		striped: true,
		fitColumns: true,
		rownumbers: true,
		singleSelect: true,
		pagination: true,
		nowrap: false,
		pageSize: 10,
		pageList: [10, 20, 50, 100, 150, 200],
		columns: [[{
			field: 'cheh',
			title: '车牌号码',
			width: 180,
			align: 'center'
		}, {
			field: 'starttime',
			title: '驶入时间',
			width: 150,
			align: 'center'
		}, {
			field: 'endtime',
			title: '驶出时间',
			width: 100,
			align: 'center'
		}, {
			field: 'parkNo',
			title: '所停车位号',
			width: 100,
			align: 'center'
		}, {
			field: 'staytime',
			title: '停车时长',
			width: 100,
			align: 'center'
		}, {
			field: 'memberLevel',
			title: '会员等级（折扣）',
			width: 100,
			align: 'center'
		}, {
			field: 'cost',
			title: '共消费',
			width: 100,
			align: 'center'
		}]]
	});
	$('#realTimeParkingGrid').datagrid({
		title: '停车场实时在停车辆：场内在停车总共888辆',
		url: '',
		method: 'GET',
		striped: true,
		fitColumns: true,
		rownumbers: true,
		singleSelect: true,
		pagination: true,
		nowrap: false,
		pageSize: 10,
		pageList: [10, 20, 50, 100, 150, 200],
		columns: [[{
			field: 'cheh',
			title: '车牌号码',
			width: 180,
			align: 'center'
		}, {
			field: 'cartype',
			title: '车辆类型',
			width: 150,
			align: 'center'
		}, {
			field: 'starttime',
			title: '驶入时间',
			width: 150,
			align: 'center'
		}, {
			field: 'parkNo',
			title: '所停车位号',
			width: 100,
			align: 'center'
		}, {
			field: 'staytime',
			title: '已停车时长',
			width: 100,
			align: 'center'
		}, {
			field: 'memberLevel',
			title: '会员等级（折扣）',
			width: 100,
			align: 'center'
		}]]
	});
	$('#personGrid').datagrid({
		data: getData()
	}).datagrid('clientPaging');
	// 分页工具栏上添加导出excel
	var pager = $('#personGrid').datagrid('getPager'); // 得到datagrid的pager对象
	pager.pagination({
		buttons: [{
			iconCls: 'icon-excel',
			handler: function() {
				alert('导出excel');
			}
		}]
	});
	$('#realTimeParkingGrid').datagrid({
		data: getData()
	}).datagrid('clientPaging');
	// 分页工具栏上添加导出excel
	var pager = $('#realTimeParkingGrid').datagrid('getPager'); // 得到datagrid的pager对象
	pager.pagination({
		buttons: [{
			iconCls: 'icon-excel',
			handler: function() {
				alert('导出excel');
			}
		}]
	});
});
function now_che(){alert("dd");}
function check(){
	//alert("ff");

var start_time;
var end_time;
if(document.getElementById("begintime").value=="")
	{var st=new Date();
     st.setDate(st.getDate()-10);
     var sm=st.getMonth()+1;
     start_time=st.getFullYear()+"-"+sm+"-"+st.getDate();
	}

else
	{
	start_time=document.getElementById("begintime").value;
	}
//alert(start_time);
if(document.getElementById("endtime").value=="")
    {var  et=new Date();
    var m=et.getMonth()+1;
    end_time=et.getFullYear()+"-"+m+"-"+et.getDate();
   }
  else
   {
	  end_time=document.getElementById("endtime").value;
   }
var type = $('select  option:selected').val();
$.ajax({
    url:"../Super/car_manager",
    type:"post",
    data:{start_time,end_time,type},
    success:function(data){
 
    var d=eval("(" + data+ ")");
          //  alert("fsd"+d['time'])  ;
    }
    });
    }
