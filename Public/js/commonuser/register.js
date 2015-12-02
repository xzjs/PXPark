		var interval;
		//用户名验证
		function nameCheck() {
			var patten = /^[a-zA-Z0-9]{3,20}$/;//用户名正则

			if (!patten.test($("#username").val())) {
				$("#username").val("");
				$("#nameTip").html("用户名不合规范");
				$("#nameTip").css("color", "red");
			} else {
				$("#nameTip").html("可用");
				$("#nameTip").css("color", "green");
			}

		}
		//密码验证
		function pwdCheck() {
			var patten = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,22}$/;//密码正则
			if (!patten.test($("#password").val())) {
				$("#password").val("");
				$("#pwdTip").html("密码格式错误");
				$("#pwdTip").css("color", "red");
			} else {
				$("#pwdTip").css("color", "green");
				$("#pwdTip").html("可用");
			}
		}
		//再次输入密码验证
		function pwdEqualsCheck() {
			var pwd = $("#password").val();
			var pwd1 = $("#password1").val();
			if (pwd1 != null && pwd == pwd1 && pwd1 != "") {
				$("#pwdTipEq").html("正确");
				$("#pwdTipEq").css("color", "green");
			} else {
				$("#pwdTipEq").html("请正确填写密码");
				$("#pwdTipEq").css("color", "red");
				$("#password1").val("");
			}
		}
		//手机验证
		function phoneCheck() {
			var patten = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/;
			var phone = $("#phone").val();
			
			if (!patten.test(phone)) {
				$("#codeBtn").attr("disabled","disabled");
			} else {
				$("#codeBtn").removeAttr("disabled");
				
			}

		}
		function getVerifyCode(){
			var count = 60;
			$("#codeBtn").html(count + "秒");
			interval = setInterval(function() {
				count = count - 1;
				$("#codeBtn").html(count + "秒");
				if (count == 0) {
					clearInterval(interval);
					$("#codeBtn").html("发送验证码");

				}

			}, 1000);
		}
		//下一步认证
		function userInfoCheck() {
			clearInterval(interval);
			var username = $("#username").val();
			var password = $("#password").val();
			var password1 = $("#password1").val();
			var phone = $("#phone").val();
			var message = $("#message").val();
			/* if(username==null||password==null||phone==null||username==""||password==""||phone==""||password1!=password||message==null||message==""){
			   alert("仍有未正确填写用户信息！");
			   return;
			}else{ */
			$("#login-box").hide();
			$("#park-box").show();
			//}
		}
		//点击加号图标
		function parkAdd() {
			$('#detailInfoWin').modal();
		}
		//切换车场类型
		function typeChange() {
			var type = $("#parktype").val();
			if (type == "1") {
				$("#acceptDiv").css("display", "none");
			} else {
				$("#acceptDiv").css("display", "block");
			}
		}
		function proviceChange() {
		}
		function cityChange() {
		}
		//点击添加信息框完成按钮
		function addComplate() {
			//先添加信息验证，判断是否可以提交完成
			//将信息相关内容赋值到顶框parkCompany
			var name = $("#parkname").val();//将添加信息框需要显示的信息都定义变量传入顶框
			$("#parkCompany").val(name + "ok......");
			//隐藏无停车场的图标
			$("#isNullDiv").css("display", "none");
			//显示停车场信息框
			$("#parkInfoDiv").css("display", "block");
			$('#detailInfoWin').modal();
//			if($("#parktype").val()=="1"){
//				$("#addImgDiv").css('display','none');
//			}
		}
		//点击修改图标
		function edits() {
			$('#detailInfoWin').modal();
		}
		//点击删除图标
		function deletes() {
			$('#deleteInfoWin').modal();
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
		//点击下一步，验证
		function parkInfoCheck() {
			$("#park-box").css("display", "none");
			$("#zhifu-box").css("display", "block");
		}
		//点击下一步，验证，进入第四步
		function costRuleSet() {
			$("#zhifu-box").css("display", "none");
			$("#rule-box").css("display", "block");
		}
		//收费规则确认
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
		window.location.href="index.html";
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
					});
		    });
			
			$(".fa-remove").each(function(index, element) {
		        $(this).click(function(){
					ruleDelete();
					});
		    });
		});