/**
 * 将树控件绑定到指定的input element上
 * @param bindEleId 待绑定元素id，不需要带#
 * @param url 返回的结果中默认按照id、name和parent_id为key取相应初始化数据
 * @param setting
 */
function registerPopTree(bindEleId, url, setting) {
	var settings = {
		async: {
			enable: true,
			dataType: "json",
			type: "get",
			url: url
		},
		view: {
			dblClickExpand: false,
			selectedMulti: false
		},
		data: {
			simpleData: {
				idKey: "id",
				pIdKey: "parent_id",
				enable: true
			}
		},
		callback: {
			beforeClick: beforeClickHandler,
			onClick: clickHandler,
			onAsyncSuccess: asynSuccessHandler
		},
		onlyChooseLeaf: true
	};
	if(setting) {
		_.extend(settings, setting);
	}
	
	var treeId = "tree_" + bindEleId;
	$("body").append("<div class='pop-tree'><ul id='" + treeId + "' class='ztree'/></div>");
	$.fn.zTree.init($('#' + treeId), settings);	
	$("#" + treeId).data("chooseLeafFlag", settings.onlyChooseLeaf);
	$("#" + bindEleId).next().off("mousedown").on("mousedown", {treeId: treeId}, showPopTree);
	$(".pop-tree").on("mousedown", function(event) {
		event.stopPropagation();
	});
}

function showPopTree(event) {
	var treeId = event.data.treeId;
	prepare4Pop(treeId);
	
	var popTree = $("#" + treeId).parent();
	if(!popTree.is(":visible")) {
		var bindEle = $("#" + treeId.substr(5));
		popTree.css({
			left : bindEle.offset().left + "px",
			top : bindEle.offset().top + bindEle.outerHeight() + "px",
			width: bindEle.outerWidth() + "px"
		}).show();
		$("body").off("mousedown").on("mousedown", function() {
			$(".pop-tree").hide();
			$("body").off("mousedown");
		});
	} else {
		popTree.hide();
	}
	event.stopPropagation();
}

/**
 * 异步获取数据成功的回调函数
 * @param event
 * @param treeId
 * @param treeNode
 * @param data
 */
function asynSuccessHandler(event, treeId, treeNode, data) {
	var bindId = treeId.substr(5);
	registerTypeAhead('#' + bindId, null, null, {
		source: getTreeSource(data, 'name'),
		keyName: 'id',
		textName: 'name'
	});

	var hiddenVal = $('#' + bindId).prev().val();
	if($.trim(hiddenVal) != '') {
		var obj = _.find(data, function(p) {
			return p.id == hiddenVal;
		});
		if(obj != undefined) {
			$('#' + bindId).val(obj.name);
		}
	}
}

function beforeClickHandler(treeId, treeNode) {
	var onlyChooseLeaf = $("#" + treeId).data("chooseLeafFlag");
	if(!onlyChooseLeaf) return true;
	
	var check = (treeNode && !treeNode.isParent);
	if(!check) showMsg("只能选择叶子节点");
	return check;
}

function clickHandler(e, treeId, treeNode) {
	var bindId = treeId.substr(5);
	$('#' + bindId).val(treeNode.name);
	$('#' + bindId).prev().val(treeNode.id);
	$("#" + treeId).parent().hide();
}

function prepare4Pop(treeId) {
	var tree = $.fn.zTree.getZTreeObj(treeId);
	tree.expandAll(false);
	
	var bindId = treeId.substr(5);
	var id = $('#' + bindId).prev().val();
	var treeNode = tree.getNodeByParam("id", id);
	if(treeNode) {
		var ancestorNode = null;
		var parentNode = treeNode.getParentNode();
		while(parentNode) {
			ancestorNode = parentNode;
			parentNode = parentNode.getParentNode();
		}
		tree.expandNode(ancestorNode, true, true, true);
		tree.selectNode(treeNode);
	}
}