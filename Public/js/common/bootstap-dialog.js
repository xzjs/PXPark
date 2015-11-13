/*
 * 基于bootstrap、jquery,对alert confrim prompt三种对话框进行扩展;
 *   1.用法: showMsg(message, title, callback,fnProcOnHide)
 *          showConfirm(message, title, callback,fnProcOnHide)
 *   2.参数含义     message：消息体；   title：消息标题；callback：回调函数；fnProcOnHide：窗体隐藏时的回调处理
 *  
 *  (25 March 2015)
 *  
*/
var modalResult = {
	mrOk: 'OK',
	mrCancel: 'Cancel'
};
var msgType = {
	info: 'msg-info',
	warn: 'msg-warning',
	error: 'msg-error'
};

jQuery.extend({
	dialog : {
		alert: function(message, title, callback, args) {
			if(!title) title = '提示消息';
			$.dialog._show(title, message, 'alert', callback, args);
		},
		
		confirm: function(message, title, callback, args) {
			if(!title) title = '确认消息';
			$.dialog._show(title, message, 'confirm', callback, args);
		},
		
		modal: function(id, args ,preShowHandler) {
			$.dialog._preShowModal(args);
			if(preShowHandler){
				preShowHandler();
			}
			$(id).modal();
			$(id).on('hidden.bs.modal',function() {
				$.dialog._postHideModal(args,id);
			}).on('shown.bs.modal', function () {
				$(id + " input[type=text]:visible:enabled:first").focus();
			});
			
			$(id + " input[type=text]").keyup({modalId: id}, function(e) {
				//console.info(e.keyCode +"=="+ $(e.target).attr("typeahead") +"=="+ new Date().getTime());
				if(!$(e.target).attr("typeahead") && e.keyCode == 13) {
					$(e.data.modalId + " .btn-primary").trigger("click");
				}
				//$(e.target).removeAttr("typeahead");
			});
		},
		
		_show: function(title, msg, type, callback , args) {
			if(!msg) msg='';
			
			var dlgHtml = '<div class="modal" id="_modalDialog">'
				+'<div class="modal-dialog">'
				+'<div class="modal-content">'
				+'<div class="modal-header">'
				+'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">'
				+'&times;</button>'
				+'<h4 class="modal-title">'+title+'</h4></div>'
				+'<div id="dialogBody" class="confrimMsg modal-body">'
				+'<div>'+msg+'</div>'
				+'</div>'
				+'<div id="dialogFooter" class="modal-footer">'
				+'<button id="_cancelBtn" type="button" class="btn  btn-default"  data-dismiss="modal" >取消</button>'
				+' </div> </div> </div> </div>';
			$("BODY").append(dlgHtml);
			
			$("#_cancelBtn").off("click");
			if(callback) {
				$("#_cancelBtn").on("click", {modalResult: modalResult.mrCancel}, callback);
			}
			
			if(type == 'confirm'){
				var _confrimBtn = '<button id="_confrimBtn" type="button" class="btn  btn-primary" data-dismiss="modal">确认</button>';
				$("#dialogFooter").prepend(_confrimBtn);
				
				$("#_confrimBtn").off("click");
				if(callback) {
					$("#_confrimBtn").on("click", {modalResult: modalResult.mrOk}, callback);
				}
			}
			
			$('#_modalDialog').on('hidden.bs.modal',function(){
				$.dialog._postHideModal(args);
			});			
			$.dialog._preShowModal(args);
			
			$('#_modalDialog').modal('show');
		},
		
		/*窗口弹出前如果浏览器有滚动条，滚动条会自动隐藏会使某些元素右移，记录窗口弹出前浏览器的宽度，窗口弹出后，把此宽度赋给可能移动元素*/
		_preShowModal:function(args) {
			$('.navigator a').bind("click", function() {return false;});
			
			var w1 = $(window).width();
			for(var selector in args){
				if(selector == '.sep-line'){
					$(selector).css('left', w1*args[selector] +'px');
				}else{
					$(selector).css('max-width', w1*args[selector] +'px');
				}
			}
		},
		
		/*窗口关闭后重置指定元素的min-width属性*/
		_postHideModal:function(args,winId){
			$('.navigator a').unbind("click");
			if(winId){
				
			}else{
				$('#_modalDialog').remove();
			}
			for(var selector in args){
				if(selector == '.sep-line'){
					$(selector).css('left', '23%');
				}else{
					$(selector).css('max-width','none');
				}
			}
		}
			
    }
});

/*弹窗口后浏览器滚动条隐藏导致的右移元素*/
var movedElements = {'.navbar-default':1,
	'.breadcrumbs-fixed':1,
	'#footer':1,
	'.raw-content':0.42,
	'.sep-line':0.23
};

showMsg = function(message, type) {
	message = message.toString();
	if(!type) {
		if(message.indexOf("i") == 0) type = msgType.info;
		else if(message.indexOf("e") == 0) type = msgType.error;
		else if(message.indexOf("w") == 0) type = msgType.warn;
		else type = msgType.warn;
	}
	
	if(_.isString(g_msg[message])) message = g_msg[message];
	$("#msg-box").removeClass().addClass(type).html(message)
		.stop(true).slideDown("fast").delay(1200).slideUp("normal");
};

showAlert = function(message, title, callback, args) {
	if(_.isString(g_msg[message])) message = g_msg[message];
	if(args) _.extend(movedElements, args);
	$.dialog.alert(message, title, callback, movedElements);
};

showConfirm = function(message, title, callback, args) {
	if(_.isString(g_msg[message])) message = g_msg[message];
	
	if(args) _.extend(movedElements, args);
	$.dialog.confirm(message, title, callback, movedElements);
};

showModal = function(id, args, preShowHandler) {
	if(args) _.extend(movedElements, args);
	$.dialog.modal(id, movedElements, preShowHandler);
};

