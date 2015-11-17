/*给table添加行*/
function addRow(compId) {
	$("html,body").animate({
		scrollTop: $(compId).offset().top + $(compId).height() - 300
	}, 200);

	var tbody = $(compId + ' tbody:first');
	var index = tbody.data('index');
	if(index) {
		index = parseInt(index) + 1;
	} else {
		var rowId = tbody.find('tr:last').attr('rowId');
		index = parseInt(rowId) + 1;
	}
	tbody.data('index', index);
	var firstRow = tbody.find('tr:first');
	var tr = firstRow[0];
	var newTr = $(tr).clone();
	newTr.attr('rowId', index);
	tbody.append(newTr.show());
	refreshIndex(tbody);
	tbody.find('tr:last .id_name input:last').focus();
}
/* 刷新table中的序号 */
function refreshIndex(table) {
	table.find('.index').each(function(index, item) {
		$(item).html(index);
	});
}
/* 删除表格中的行 */
function deleteRow(me, args) {
	showConfirm('确定要删除吗？', null, function(event) {
		if(event.data.modalResult == modalResult.mrCancel) return;
		var tbody = $(me).parents('tbody');
		$(me).parents('tr:first').remove();
		refreshIndex(tbody);
	}, args);
}
function hideFirstRow4Clone() {
	$('tbody').find('tr:first').hide();
}
function resisterEnterEvent4Search(tableId) {
	$(".search-panel input").off("keyup").on("keyup", function(event) {
		if(event.keyCode == 13) search(tableId);
	})
}
/**
 * 点击检索按钮时的处理函数
 * 
 * @param tableId
 */
function search(param) {
	var tableId = param;
	if(_.isObject(param) && _.isObject(param.data)) {
		tableId = param.data.tableId;
	}
	var searchParams = getSearchParams();
	searchParams['offset'] = 0;
	searchParams['sortType'] = 1;
	$(tableId).data('searchParams', searchParams);
	$(tableId).bootstrapTable('refresh');
}

/**
 * bootstrap-table取得检索条件的回调函数，其中this指向的是bootstrap table对象中的options属性
 * 
 * @param params
 * @param args
 * @returns
 */
function queryParam(params, args) {
	var sort = params.sort;
	var searchParams = $('#' + this.tableId).data('searchParams');
	_.extend(params,searchParams);
	if(searchParams) {
		delete searchParams.offset;// 每次检索时需要从第一页开始显示
		delete searchParams.sortType;
	}
	
	return params;
}
/**
 * 清空检索条件，重新初始化表格
 * 
 * @param tableId
 */
function resetTable(tableId) {
	resetSearch();
	$(tableId).data('selectedRows', {});
	$(tableId).data('searchParams', {
		offset: 0
	});
	$(tableId).bootstrapTable('showFirstPage');
}
/**
 * 刷新table，切换到第一页
 * @param tableId
 */
function refreshTable(tableId, url, offset) {
	var param = {};
	if(offset) param.query.offset = offset;
	if(url) param.url = url;
	$(tableId).bootstrapTable('refresh', param);
	$(tableId).data('selectedRows', {});
}
/**
 * 换页时能够保持住其他页选中的checkbox
 * 
 * @param tableId:
 *            bootstrap table Id
 * @param keyName:
 *            行主键名称，一般是id
 */
function cacheCheckedRows(tableId, keyName) {
	if(!keyName) keyName = 'id';
	$(tableId).data('selectedRows', {});

	$(tableId).on('check.bs.table', function(e, row) {
		$(this).data('selectedRows')[row[keyName]] = row;
	});
	$(tableId).on('uncheck.bs.table', function(e, row) {
		delete $(this).data('selectedRows')[row[keyName]];
	});
	$(tableId).on('check-all.bs.table', function(e, rows) {
		for( var i in rows ) {
			$(this).data('selectedRows')[rows[i][keyName]] = rows[i];
		}
	});
	$(tableId).on('uncheck-all.bs.table', function(e, rows) {
		for( var i in rows ) {
			delete $(this).data('selectedRows')[rows[i][keyName]];
		}
	});
	$(tableId).on('post-body.bs.table', function() {
		$(tableId).bootstrapTable("checkBy", {
			field: keyName,
			values: getSelectKeys(tableId)
		});
	});
}
function getSelectedRows(tableId) {
	var data = [];
	var selectedRows = $(tableId).data('selectedRows');
	for( var key in selectedRows ) {
		data.push(selectedRows[key]);
	}
	return data;
}
function getSelectKeys(tableId) {
	var keys = [];
	var selectedRows = $(tableId).data('selectedRows');
	for( var key in selectedRows ) {
		keys.push(key);
	}
	return keys;
}
function hasRowChecked(tableId, count) {
	if(!count) count = 1;
	
	var selections = $(tableId).data('selectedRows');
	if(_.size(selections) < count) {
		var msg = formatMsg(g_msg.NO_ROW_CHECKED, count);
		showMsg(msg);
		return false;
	}
	return true;
}
