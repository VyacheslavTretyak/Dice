function reg(res){	
	stopInterval("beginID");
	var name = $("#mylogin").html();		
	$("#scr").empty();
	$("#scr").html(getPage("reg"));
	$("#iLogin").val(name);
	$(".mesReg").hide();
	$("#bBack").show();
	$("#bExit").hide();
	$("#bLogin").hide();
	$("#bReg").click(function(){
		registration();
	});
	$("#iCaptcha").focus(function(){
		getCaptcha();
	});	
	$("#iLogin").blur(function(){
		checkRegLogin();
	});
	$("#iEmail").blur(function(){
		checkEmail();
	});
	$("#iRegPass").blur(function(){
		checkRegPass();
	});
	$("#iConfirm").blur(function(){
		checkConfirmPass();
	});
	$("#imgCaptcha").attr("src", "Reg/getCaptcha?"+Math.random());
	getCaptcha();
}	
function getCaptcha(){		
	$("#imgCaptcha").attr("src", "Reg/getCaptcha?"+Math.random());	
}
function checkCaptcha(){	
	var ret = false;	
	$.ajax({
		type: "POST",
		url: "Reg/checkCaptcha",
		async: false,
		data: {val: $("#iCaptcha").val()},
		success: success
	});	
	function success(res){
		if(res==0){
			$("#eCaptcha").show();
			ret = false;	
		} else {
			$("#eCaptcha").hide();			
			ret = true;
		}
	}
	return ret;
}
function checkRegLogin(){
	var ret = false;
	var val = $("#iLogin").val();
	$.ajax({
		type: "POST",
		url: "Reg/checkRegLogin",
		async: false,
		data: {val: val},
		success: success
		});	
	function success(res){		
		if(res==0){
			$("#eRLogin").show();
			ret = false;
		} else {			
			$("#eRLogin").hide();			
			ret = true;
		}			
	}
	return ret;	
}
function checkRegPass(){
	var ret = false;
	var val = $("#iRegPass").val();
    if(val.length<6){		
		$("#eRegPass").show();
		ret = false;			
	} else {			
		$("#eRegPass").hide();			
		ret = true;
	}	
	return ret;
}
function checkConfirmPass(){	
	var ret = false;
	var val = $("#iConfirm").val();
    if(val !== $("#iRegPass").val()){		
		$("#eConfPass").show();
		ret = false;			
	} else {			
		$("#eConfPass").hide();			
		ret = true;
	}
	return ret;	
}
function checkEmail(){	
	var ret = false;	
	var reg = /[a-z0-9_-]+(\.[a-z0-9_-]+)*@([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})/i; 
	var val = $("#iEmail").val();
    if(!(reg.test(val))){			
		$("#eEmail").show();
		ret = false;			
	} else {			
		$("#eEmail").hide();			
		ret = true;
	}
	return ret;	
}
function registration(){	
	var l = checkRegLogin();
	var e = checkEmail();
	var p = checkRegPass();
	var c = checkConfirmPass();	
	var h = checkCaptcha();		
	if(l && e && p && c && h){		
		$.ajax({
			type: "POST",
			url: "Reg/reg",
			async: false,
			data: {login: $("#iLogin").val(),
					email: $("#iEmail").val(),
					pass: $("#iRegPass").val()},
			success: function(res){			
				$("#scr").empty();
				$("#scr").html(res);			
			}
		});			
	}	
}