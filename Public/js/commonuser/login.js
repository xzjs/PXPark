function userLogin(){
	var t = $('input:radio[name="loginType"]:checked').val(); 
	var name=document.getElementById("name").value;
	var pswd=document.getElementById("password").value;
	var type;
	if (t=='luce')type=3;
	else type=2;
	//alert(type);
	$.ajax({
	    url:"../Index/login",
	    type:"post",
	    data:{type,name,pswd},
	    success:function(data){
	    if(data>0)
	      {
	    	if(t=='luce'){//路侧用户登录
	    		window.location.href="/PXPark/index.php/Home/Common/index.html";
	    	}else{ //普通用户登录
	    		window.location.href="/PXPark/index.php/Home/Common/index.html";
	    	}
	       }
	    if(data==-1)alert("用户名未注册");
	    if(data==-2)alert("密码错误");
	           
	    }
	    });
	/*if(type=='luce'){//路侧用户登录
		window.location.href="/PXPark/index.php/Home/Common/index.html";
	}else{ //普通用户登录
		window.location.href="mainPt.html";
	}*/
}

function userRegister(){
	$('#registerWin').modal();
	//window.location.href="userRegister.html";
}

function goToRegisterPage(type){
	if(type == '0'){
		window.location.href="/PXPark/index.php/Home/Commonuser/RegisterPt.html";
	}else{
		window.location.href="/PXPark/index.php/Home/Commonuser/RegisterLc.html";
	}
}