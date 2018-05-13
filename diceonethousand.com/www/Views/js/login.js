function login(){
	stopInterval("beginID");	
	var name = $("#mylogin").html();
	$("#scr").empty();
	$("#scr").html(getPage("login"));
	$("#iLogin").val(name);
	$(".message").hide();
	$("#bBack").show();
	$("#bBack").click(function(){		
		getLogin();
	});	
	$("#bEnter").click(function(){		
		enter();
	});	
	$("#bReg").click(function(){		
		reg();
	});	
	$("#iPass").blur(function(){		
		checkPass();
	});	
	$("#iLogin").blur(function(){		
		checkLogin();
	});	
	$("#bExit").hide();
	$("#bLogin").hide();	
}
function checkLogin(){	
	var ret = false;
	$.ajax({
		type: "POST",
		url: "Reg/checkLogin",
		async: false,
		data: {val: $("#iLogin").val()},
		success: success
		});	
	function success(res){		
		if(res==0){			
			$("#eLogin").show("drop", { direction: "down" }, 600);
			ret = false;
		} else {
			$("#eLogin").hide("drop", { direction: "down" }, 600);
			ret = true;
		}
	}
	return ret;
}
function checkPass(){	
	var ret = false;
	$.ajax({
		type: "POST",
		url: "Reg/checkPass",
		async: false,
		data: {login: $("#iLogin").val(), pass: $("#iPass").val()},
		success: success
		});	
	function success(res){			
		if(res == "0"){			
			$("#ePass").show("drop", { direction: "down" }, 600);
			ret = false;
		} else {
			$("#ePass").hide("drop", { direction: "down" }, 600);
			ret = true;
		}
	}
	return ret;
}
function checkActivate(){
	var ret = false;
	$.ajax({
		type: "POST",
		url: "Reg/checkActivate",
		async: false,
		data: {login: $("#iLogin").val()},
		success: success
		});	
	function success(res){	
		if(res == "0"){
			$("#eActive").show("drop", { direction: "down" }, 600);
			ret = false;
		} else {
			$("#eActive").hide("drop", { direction: "down" }, 600);
			ret = true;
		}
	}
	return ret;	
}
function enter(){	
	var l = checkLogin();
	var p = checkPass();
	var e = checkActivate();
	if(l && p && e){
		setCookie("user", $("#iLogin").val(), {expires: 3600*24*365});
		setCookie("login", 1, {expires: 3600*24*365});		
		getLogin();		
	}
}
function exit(){
	setCookie("login", 0, {expires: 3600*24*365});	
	getLogin();		
}