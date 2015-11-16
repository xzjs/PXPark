/*validator extend 2015-5-14 xwj*/
//extend message//
jQuery.extend(jQuery.validator.messages, { 
	required: "必须输入字段", 
	remote: "请修正该字段", 
	email: "请输入正确格式的电子邮件", 
	url: "请输入合法的网址", 
	date: "请输入合法的日期", 
	dateISO: "请输入合法的日期 (ISO).", 
	number: "请输入合法的数字", 
	digits: "只能输入整数", 
	creditcard: "请输入合法的信用卡号", 
	equalTo: "两次输入的密码不一致", 
	accept: "请输入拥有合法后缀名的字符串", 
	maxlength: jQuery.validator.format("请输入一个长度最多是 {0} 的字符串"), 
	minlength: jQuery.validator.format("请输入一个长度最少是 {0} 的字符串"), 
	rangelength: jQuery.validator.format("请输入一个长度介于 {0} 和 {1} 之间的字符串"), 
	range: jQuery.validator.format("请输入一个介于 {0} 和 {1} 之间的值"), 
	max: jQuery.validator.format("请输入一个最大为 {0} 的值"), 
	min: jQuery.validator.format("请输入一个最小为 {0} 的值") 
}); 

//extend default setting//
jQuery.validator.setDefaults({
	focusInvalid: false,
	highlight: function( element, errorClass, validClass ) {
		var parent = $(element).parent();
		if(!parent.hasClass("has-error")) {
			parent.addClass("has-error");
		}
	},

	success: function(label) {
		$(label).parent().removeClass('has-error');
		label.remove();
	},
	
	errorPlacement: function(error, element) {
		$(element).parent().append(error);
	},
	
	submitHandler: function(form) {
	    form.submit();
	}
});

//extend validate method//
//字母 数字  汉字  () （） _
jQuery.validator.addMethod("legalChar", function(value, element, params) {
	var regExp = /^[\w()（）°℃.\-\u4e00-\u9fa5]*$/;
	return regExp.test(value); 
}, "请输入合法字符");

jQuery.validator.addMethod("greaterThan", function(value, element, params) {
	if($(params).val()=='' || parseInt($(params).val())<parseInt(value)){
		return true;
	}else {
		return false;
	}
}, "后面的值必须大于前面的值");

jQuery.validator.addMethod("lessThan", function(value, element, params) {
	if($(params).val()=='' || parseInt($(params).val())>parseInt(value)){
		return true;
	}else {
		return false;
	}
}, "前面的值必须小于后面的值");


