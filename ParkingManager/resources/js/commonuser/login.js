function userLogin(){
	var type = $('input:radio[name="loginType"]:checked').val(); 
	if(type=='luce'){//路侧用户登录
		window.location.href="mainLc.html";
	}else{ //普通用户登录
		window.location.href="mainPt.html";
	}
}

function userRegister(){
	$('#registerWin').modal();
	//window.location.href="userRegister.html";
}

function goToRegisterPage(type){
	if(type == '0'){
		window.location.href="userRegisterPt.html";
	}else{
		window.location.href="userRegisterLc.html";
	}
}