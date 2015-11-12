var g_unsave_flag = false;
var g_sep_chars = /[,,.,，,。,、,;,；, ,　]/;
/* 绑定并初始化日期框 */
function registerTimePicker(id, args) {
	var config = {
		format: 'yyyy/MM/dd',
		language: 'zh-CN',
		weekStart: 1,
		todayHighlight: 1,
		forceParse: 0
	}
	if(args) {
		_.extend(config, args);
	}
	$(id).datetimepicker(config);
}
/* 为带有图标的input注册获取光标和清空内容的事件 */
function registerIconInputEvent() {
	$('.label-input-icon  .delete-icon').click(function() {
		$(this).prev().val('');
	});
	$('.label-input-icon').click(function() {
		$(this).children('input').focus();
	});
}
function getContextPath() {
	var pathName = document.location.pathname;
	var index = pathName.substr(1).indexOf("/");
	var result = pathName.substr(0, index + 1);
	return result;
}
/* 初始化画面中的select */
function initDropdownCodes(selector, codeGrpId, emptyFirst) {
	var url = getContextPath() + "/codegroup/" + codeGrpId + "/codes";
	ajaxGet(url, null, function(data) {
		if(emptyFirst) $(selector).append("<option value='0'></option>");
		for( var i in data ) {
			var option = "<option value='" + data[i].id + "'>" + data[i].name + "</option>";
			$(selector).append(option);
		}
	});
}
/* 使用常量数组初始化画面中的select */
function initLocalDropdownCodes(selector, data, emptyFirst, selectedValue) {
	if(emptyFirst) {
		$(selector).append("<option value='0'></option>");
	}
	for( var i in data ) {
		var selectedOption = '';
		if(selectedValue) {
			if(selectedValue == data[i][0]) {
				selectedOption = 'selected="selected"';
			}
		}
		var option = "<option " + selectedOption + " value='" + data[i][0] + "'>" + data[i][1] + "</option>";
		$(selector).append(option);
	}
}
/* 初始化页面中的checkbox */
function initCheckBoxCodes(selector, codeGrpId, name, selectedValues) {
	var url = getContextPath() + "/codegroup/" + codeGrpId + "/codes";
	ajaxGet(url, null, function(data) {
		var values = selectedValues ? selectedValues.split(",") : [];
		for( var i in data ) {
			var checked = '';
			if(_.indexOf(values, data[i].id) >= 0) checked = 'checked';
			var label = $("<label class='checkbox-inline label-wrap-checkbox'></label>");
			label.append("<input type='checkbox' name='" + name + "' value='" 
					+ data[i].id + "' " + checked + ">" + data[i].name);
			$(selector).append(label);
		}
	});
}
/* 使用常量数组初始化页面中的checkbox */
function initLocalCheckBoxCodes(selector, data, name, selectedValues) {
	for( var i in data ) {
		var checked = '';
		if(selectedValues && selectedValues.indexOf(data[i][0]) != -1) {
			checked = 'checked="checked"';
		}
		var label = $("<label class='checkbox-inline label-wrap-checkbox'></label>");
		label.append("<input type='checkbox' " + checked + " name='" + name + "' value='" + data[i][0] + "'>"
				+ data[i][1]);
		$(selector).append(label);
	}
}
// -------------表格中静态Code的格式化函数-------------//
function getCodeText(codeData, codeValue) {
	for( var i in codeData ) {
		if(codeData[i][0] == codeValue) {
			return codeData[i][1];
		}
	}
	return "";
}
// 对一览画面审核状态字段进行格式化
function statusFmt(value, row, index, param) {
	if(param){
		var url = param + row.id;
		return '<a href="#" onclick="showHistoryModal(\''+url+'\');">' + getCodeText(CODES_STATUS, value) + '</a>';
	}else{
		return getCodeText(CODES_STATUS, value);
	}
}
function textStatusFmt(value, row) {
	return getCodeText(CODES_STATUS, value);
}
// 对一览画面类型字段进行格式化
function medTypeFmt(value, row) {
	return getCodeText(CODES_MED_TYPE, value);
}
// 对一览画面来源字段进行格式化
function srcTypeFmt(value, row) {
	return getCodeText(CODES_SRC_TYPE, value);
}
// 对一览画面审核状态字段进行格式化
function signPropTypeFmt(value, row) {
	return getCodeText(CODES_SIGN_PROP_TYPE, value);
}
// 对一览画面审核状态字段进行格式化
function linkBodyFlagFmt(value, row) {
	return getCodeText(CODES_LINK_BODY_FLAG, value);
}
// 对一览画面审核状态字段进行格式化
function sourceFmt(srcType, provenance, row) {
	var text = getCodeText(CODES_SRC_TYPE, srcType);
	if(provenance) text += "(" + provenance + ")";
	return text;
}
/**
 * @author xuruizhen
 * 
 * @requires jQuery jQuery默认有serialize()和serializeArray(),将form表单元素序列化成字符串和数组
 *           将form表单元素的值序列化成对象
 * 
 * @returns object
 */
function serializeObject(form) {
	var o = {};
	$.each(form.serializeArray(), function(index) {
		if(o[this['name']]) {
			o[this['name']] = o[this['name']] + "," + this['value'];
		} else {
			o[this['name']] = this['value'];
		}
	});
	return o;
};
/**
 * 清除表单form中的元素
 * 
 * @param formId
 */
function resetForm(formId) {// TODO:能否和resetSearch合并
	$(':input', formId).not(':button, :submit, :reset, :hidden').val('').removeAttr('checked').removeAttr('selected');
	$('textarea', formId).val('');
}
/** *****************************检索相关****************************** */
/* 清空检索条件 */
function resetSearch() {
	$(".search-panel input").not(':button, :submit, :reset').val("").removeAttr('checked').removeAttr('selected');
}
function getSearchParams() {
	var params = {};
	$('.search-panel input[type="text"]').each(function(i) {
		params[this.name] = this.value.trim();
	});
	$('.search-panel input[type="radio"]:checked').each(function(i) {
		params[this.name] = this.value;
	});
	$('.search-panel input[type="checkbox"]:checked').each(function(i) {
		var value = params[this.name];
		if(!value) params[this.name] = this.value;
		else params[this.name] = value + "," + this.value;
	});
	return params;
}



/**
 * 通过post方式提交form数据
 * 
 * @param url
 * @param param
 *            数据对象或者formid
 * @param successHandler
 * @param errorHandler
 */
function ajaxPost(url, param, successHandler, errorHandler) {
	_ajax(url, 'POST', param, successHandler, errorHandler);
}
/**
 * 通过put方式提交form数据
 * 
 * @param url
 * @param param
 *            数据对象或者formid
 * @param successHandler
 * @param errorHandler
 */
function ajaxPut(url, param, successHandler, errorHandler) {
	_ajax(url, 'PUT', param, successHandler, errorHandler);
}
/**
 * 通过delete方式提交form数据
 * 
 * @param url
 * @param param
 *            数据对象或者formid
 * @param successHandler
 * @param errorHandler
 */
function ajaxDelete(url, param, successHandler, errorHandler) {
	_ajax(url, 'DELETE', param, successHandler, errorHandler);
}
/**
 * 通过get方式提交form数据
 * 
 * @param url
 * @param param
 *            数据对象或者formid
 * @param successHandler
 * @param errorHandler
 */
function ajaxGet(url, param, successHandler, errorHandler) {
	_ajax(url, 'GET', param, successHandler, errorHandler);
}
/**
 * 通过ajax方式提交form数据
 * 
 * @param url
 * @param method
 * @param param
 *            数据对象或者formid
 * @param successHandler
 * @param errorHandler
 */
function _ajax(url, method, param, successHandler, errorHandler) {
	var formSubmit = _.isString(param) && param.indexOf('#') == 0;
	if(!successHandler) {
		successHandler = function(msg) {// TODO:make default handler
			showMsg(msg.MSG_CODE);
			if(formSubmit && method == 'POST') resetForm(param);
		}
	}
	if(!errorHandler) {
		errorHandler = function(msg) {// TODO:make default handler
			showMsg('操作失败！');
		}
	}
	var reqData = param || {};
	if(formSubmit) {
		reqData = serializeObject($(param));// param=form id
	}
	_.extend(reqData, {
		'_method': method
	});
	$.ajax({
		data: reqData,
		url: url,
		type: method == 'GET' ? 'GET' : 'POST',
		success: successHandler,
		error: errorHandler
	});
}
function ajaxWithJsonStr(url, method, param, successHandler, errorHandler) {
	if(!successHandler) {
		successHandler = function(result) {
			showMsg(result.MSG_CODE);
		}
	}
	if(!errorHandler) {
		errorHandler = function(result) {
			showMsg("操作失败");
		}
	}
	$.ajax({
		type: method,
		url: url,
		dataType: "json",
		contentType: "application/json",
		data: JSON.stringify(param),
		success: successHandler,
		error: errorHandler
	});
}
function checkForm(formSelector) {
	return $(formSelector).validate().form();
}
function getHintInfo(map) {
	var hint = "";
	for( var key in map ) {
		hint += key + ": " + map[key] + "\r\n";
	}
	return hint;
}
function registerTypeAhead(selector, url, submitName, agrs) {
	$(selector).attr("autocomplete", "off");
	if(submitName) {
		$(selector).before("<input type='hidden' name='" + submitName + "'/>");
	}
	var options = {
		instantTrigger: true,
		source: function(query, process) {
			var hidden = this.$element.prev();
			if(hidden && hidden.val()) hidden.val("");
			ajaxGet(url, {
				"query": query
			}, function(data) {
				process(data);
			});
		},
		matcher: function(item) {
			return true;
		},
		listItemHandler: function(item) {
	        if(!item.srcType) return item[this.textName];
	        else return item[this.textName] + " - " + sourceFmt(item.srcType, item.provenance);
		}
	};
	_.extend(options, agrs);
	$(selector).typeahead(options);
}
function listItemHandler4Alias(item) {
	if(!item.alias) return item.text;
	else return item.alias + "(" + item.text + ")";
}
/**
 * 为typeahead提供静态数据源
 * 
 * @param data
 *            数据全集，格式为[{key1:value1,key2:value2},{key1:value1,key2:value2}]
 * @param textName
 *            过滤结果中显示文本的key的名字
 * @returns {Function}
 */
function getTreeSource(data, textName) {
	if(!textName) textName = 'text';
	return function(query, process) {
		var items = _.filter(data, function(item) {
			var isParent = _.find(data, function(ele) {
				return ele.parent_id == item.id;
			});
			return !isParent && item[textName].indexOf(query) == 0;
		});
		process(items);
	}
}
function getListSource(data) {
	return function(query, process) {
		var items = data;
		if(query != ''){
			items = _.filter(data, function(item) {
				return item['text'].indexOf(query) == 0;						
			});	
		}
		process(items);
	}
}
/**
 * js中没有java中的replaceAll()函数,为了达到与java的replaceAll()一样的效果,对js中String的方法进行扩充
 * 
 * @param s1：带替换字符串
 * @param s2：替换后的字符串
 * @returns
 */
String.prototype.replaceAll = function(s1, s2) {
	return this.replace(new RegExp(s1, "gm"), s2);
}
function formatMsg(msg) {
	var params = $.makeArray(arguments).slice(1);
	$.each(params, function(i, param) {
		msg = msg.replace(new RegExp("\\{" + i + "\\}", "g"), param);
	});
	return msg;
}

/**
 * 判断是否是火狐浏览器
 * @returns {Boolean}
 */
function isFirefoxBrowser(){
	if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){  
        return true;  
   } else{
	   return false;
   }
}

/**
 * codes.js中根据id、name获取数组
 * 
 * @param codeArray
 * @param id_name
 * @returns
 */
function getCodeByID_Name(codeArray, id_name) {
	for( var i in codeArray ) {
		if(_.indexOf(codeArray[i], id_name) >= 0) {
			return codeArray[i];
		}
	}
	return null;
}
/**
 * 汉字转拼音
 */
function getPinYin(me, pinyin, initials) {
	var value = $.trim($(me).val());
	if(value != '') {
		var url = getContextPath() + '/pinyin/';
		ajaxGet(url, {
			'character': value
		}, function(obj) {
			$(pinyin).val(obj.pinyin);
			$(initials).val(obj.initials);
		});
	}
}
/**
 * 选中select元素中值等于value的option
 * 
 * @param selector
 *            一般为select元素Id
 * @param value
 *            待选中值
 */
function selectOption(selector, value) {
	$(selector + " option[value='" + value + "']").prop("selected", "selected");
}
/**
 * 按照标准错误式样为指定控件显示message
 * @param selector
 * @param msg
 */
function showError(selector, msg) {
	var parent = $(selector).parent();
	if(!parent.hasClass("has-error")) {
		parent.addClass("has-error");
		parent.append("<span class='error-msg'>" + msg + "\</span>");
	}
}
function hideError(selector) {
	var parent = $(selector).parent();
	if(parent.hasClass("has-error")) {
		parent.removeClass('has-error');
		parent.children().remove(".error-msg");
	}
}
function clearError(selector) {
	var error = selector ? selector + " .has-error" : ".has-error";
	var errMsg = selector ? selector + " .error-msg" : ".error-msg";
	$(error).removeClass("has-error");
	$(errMsg).remove();
}
/**
 * 添加同义词
 * 
 * @param element
 */
function addAlias(element) {
	var aliasInput = $(element).prev();
	hideError(aliasInput);
	var value = aliasInput.val();
	if(value == "") {
		showError(aliasInput, "同义词不能为空");
		return;
	}
	if(!value.match(/^[\w()（）,，°℃、\- \u4e00-\u9fa5]+$/)) {
		showError(aliasInput, "同义词中有非法字符");
		return;
	}
	var container = $(element).parent().next();
	var vals = _.uniq(value.split(/[,，、]/));
	for( var i in vals ) {
		var alias = vals[i];
		if(!$.trim(alias)) continue;
		if(isAliasExisted(container, alias)) {
			showError(aliasInput, "同义词已存在");
			return;
		}
		container.append(getAliasItem(vals[i]));
	}
	aliasInput.val("");
}
/**
 * 判断同义词是否已经在客户端添加了
 * 
 * @param container
 *            同义词元素的容器
 * @param value
 *            需要检查的值
 * @returns {Boolean}
 */
function isAliasExisted(container, value) {
	var isExist = false;
	container.find("input").each(function(i) {
		if($(this).val() == value) {
			isExist = true;
		}
	});
	return isExist;
}
function getAliasItem(value, showIcon) {
	if(showIcon === undefined) showIcon = true;
	var html = "<div class='icon-input' style='margin:5px'>";
	html += "<input type='text' name='alias' class='form-control' readonly value='" + value + "'/>";
	if(showIcon) html += "<i class='icon fa fa-remove fa-lg' onclick='$(this).parent().remove()'></i>";
	html += "</div>";
	return html;
}

/**
 * 通过服务器端返回的message code是否已i开头来判断后台是否执行成功。
 * @param msgCode
 * @returns {Boolean}
 */
function isSucceed(msgCode) {
	if(msgCode && msgCode.indexOf("i") == 0) return true;
	return false;
}
function showProgress(text) {
	text = text || "Loading...";
	$("body").append("<div class='mask-layer'><table><tr><td><i class='fa fa-spinner fa-pulse fa-3x'></i><br><br>"+text+"</td></tr></table><div>");
}
function hideProgress() {
	$(".mask-layer").remove();
}

//放大缩小图片
function previewImage(obj, fromServer, formId){
	var newSrc = $(obj).attr("src");
	if(fromServer) newSrc = newSrc.replace("/100_", "/");
	var img = $("<img id='imgPreview' style='position:fixed;z-index:10001;width:400px;height:300px' src='" + newSrc + "'>");
	$(formId).append("<div class='mask-layer' onclick='hidePreview()'></div>");
	$(formId).append(img);
	var top = ($("body").height() - 400) / 2;
	var left = ($("body").width() - 300) / 2;
	img.css("top", top).css("left", left);
}

function hidePreview() {
	$(".mask-layer").remove();
	$("#imgPreview").remove();
}

