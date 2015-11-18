$(function() {
	$('#statisticInfo').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/statisticInfo.html';
		
	});
	$('#carManager').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/carManager.html';
		
	});
	$('#userInfo').on('click', function() {
		window.location.href='/PXPark/index.php/Home/Super/userInfo.html';
		
	});
});
function edits(){
	$('#parkingInfoWin').modal();
}


function deletes() {
	$("#deleteInfoWin").modal();
	$("#tishi").html("确定要删除" + $("#parkCompany").val() + "?");
}

function yesBtn() {
	$("#deleteInfoDiv").css("display", "none");
	$("#parkInfoDiv").css("display", "none");
	$("#parkCompany").val("");
	$("#isNullDiv").css("display", "block");
}
function noBtn() {
	$("#deleteInfoDiv").css("display", "none");
}
function ruleConfirm(flag) {
	if (flag == 1) {
		//是其他规则不可选定
		$("#confirmDiv2").css("display","none");
		$("#confirmDiv3").css("display","none");
		$("#confirmDiv4").css("display","none");
		//后台操作
	}else{
	    //后台操作
	}
}
//收费规则添加
function ruleAdd(flag) {
	if (flag == 4) {
		$('#addRuleInfoWin').modal();
		$("#addRuleDiv").css("display", "block");
		$("#ruleTimeDiv").css("display", "block");
	} else {
		$('#addRuleInfoWin').modal();
		$("#addRuleDiv").css("display", "block");
		$("#ruleTimeDiv").css("display", "none");
	}
}
function ruleAddComplete(){
//关闭div
$("#addRuleDiv").css("display", "none");
}
//收费规则编辑
function ruleEdit(flag) {
	if (flag == 4) {
		$('#addRuleInfoWin').modal();
		$("#addRuleDiv").css("display", "block");
		$("#ruleTimeDiv").css("display", "block");
	} else {
		$('#addRuleInfoWin').modal();
		$("#addRuleDiv").css("display", "block");
		$("#ruleTimeDiv").css("display", "none");
	}
	//后台操作，结束后跳转到管理，显示刚才注册的所有信息
}
//收费规则删除
function ruleDelete(flag) {
    //后台操作
	
		//后台操作，更新select
	$('#deleteInfoWin').modal();
	$("#tishi").html("确定要删除此规则吗?");	//后台操作，更新select
	
}
//添加小车收费规则 0白天 1夜晚
function addSmallCar(flag) {
    if(flag==0){
    $("#daySmallDiv").append($("#daySmall").clone().css("display",'block'));
    //alert($("#addRuleDiv").height());
    $("#addRuleDiv").css("height",$("#addRuleDiv").height()+30);
    }else{
    $("#nightSmallDiv").append($("#nightSmall").clone().css("display",'block'));
    //alert($("#addRuleDiv").height());
    $("#addRuleDiv").css("height",$("#addRuleDiv").height()+30);
    }
	
}
//删除小车
function removeCar(flag) {
    $(flag).parent().parent().remove();
	
}
//添加大车收费规则
function addLargeCar(flag) {
	if(flag==0){
    $("#dayLargeDiv").append($("#dayLarge").clone().css("display",'block'));
    $("#addRuleDiv").css("height",$("#addRuleDiv").height()+30);
    }else{
     $("#nightLargeDiv").append($("#nightLarge").clone().css("display",'block'));
    $("#addRuleDiv").css("height",$("#addRuleDiv").height()+30);
    }
}
function registerComplete(){
//后台操作，结束后跳转到管理，显示刚才注册的所有信息
alert("注册结束！");
window.location.href="login.html";
}
$(function() {
	var Accordion = function(el, multiple) {
		this.el = el || {};
		this.multiple = multiple || false;

		// Variables privadas
		var links = this.el.find('.link');
		// Evento
		links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown);
	};

	Accordion.prototype.dropdown = function(e) {
		var $el = e.data.el;
			$this = $(this),
			$next = $this.next();

			$next.slideToggle();
			$this.parent().toggleClass('open');

			if (!e.data.multiple) {
				$el.find('.submenu').not($next).slideUp().parent().removeClass('open');
			};

		
	};	

	var accordion = new Accordion($('#accordion'), false);
	var accordion1 = new Accordion($('#accordion1'), false);
	var accordion2 = new Accordion($('#accordion2'), false);
	var accordion3 = new Accordion($('#accordion3'), false);
	$(".fa-check").each(function(index, element) {
        $(this).click(function(){
			$(".fa-check").css("color","#000");
			$(this).css("color","green");
			$(this).parent().parent().slideUp();
			});
    });
	$(".fa-remove").each(function(index, element) {
        $(this).click(function(){
			ruleDelete();
			});
    });
});