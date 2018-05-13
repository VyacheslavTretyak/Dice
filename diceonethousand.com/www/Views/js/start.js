$(document).ready(function(){
	var version = navigator.appVersion;
	if(version.indexOf("Version") != -1){
		alert("Your browser not supported!");
		return;
	}	
	getLogin();		
});
function getLogin(){	
	$("#scr").html(getPage("field"));
	$("#menu").html(getPage("menu"));	
	loadLang();	
	setCookie("lang", $("#sel").val(), {expires: 365*24*3600});
	$("#sel").change(function(){		
		changeLang($("#sel").val());
	});
	$(".message").hide();
	$("#bCC").click(function(){
		clearAllCookie();
	});	
	$("#bHelp").click(function(){
		help();
	});		
	$.ajax({
		type: "POST",
		url: "Main/getUser",
		async: false,
		data: "",
		success: success
	});
	function success(res){			
		if(res == ""){
			$("#bBack").hide();
			$("#bExit").hide();						
			$("#scr").append(getPage("block"));
			$("#iName").focus(function(){				
				focusName();
			});
			$("#bBlock").click(function(){
				enterName($("#iName").val());				
			});
			$("#bBlockLogin").click(function(){
				login();				
			});
			$("#eLoginShort").hide();
			$("#eLoginOccup").hide();			
		} else {		
			start(res);
		}	
	}
}
function clearAllCookie(){
	deleteCookie("user");
	deleteCookie("lang");
	deleteCookie("login");
	alert("delete cookie 'user'");
	window.location.reload();	
}
function start(user){	
	$.ajax({
		type: "POST",
		url: "Data/getLogin",
		async: false,
		data: "",
		success: success
		});
	function success(res){
		//alert(res);
		if(res == "error connection mysql!")			
			errorScr("001");
		var r = JSON.parse(res);
		if(r[0] != "0"){			
			$("#bLogin").hide();			
			$("#bBack").hide();			
			$("#bExit").show();
			$("#bExit").click(function(){
				exit();
			});			
		} else {			 
			$("#bLogin").show();
			$("#bLogin").click(function(){
				login();
			});
			$("#bExit").hide();
			$("#bBack").hide();
		}		 
		$("#mylogin").html(user);
		if(r[1] == -1)
			$("#moneySymbol").html("");
		else 
			$("#myCoins").html(r[1]);
		$("#iSearch").keyup(function(){
			updateStart();
		});
		startInterval("beginID", "updateStart()", 5000);		
		updateStart();		
	}	
}
function enterName(name){	
	if($("#iName").val().length<3){
		$("#eLoginShort").show();
		return;
	}	
	$(".errLogin").hide();
	$.ajax({
		type: "POST",
		url: "Data/checkLogin",
		asyncasync: false,
		data: {val: name},
		success: success
	});	
	function success(res){		
		if(res==0){
			$("#eLoginOccup").show();
			$("#iName").css({focus: 'true'});
			//$("#iName").value("");
		} else {			
			setCookie("user", res, {expires: 365*24*3600});
			setCookie("login", 0, {expires: 365*24*3600});			
			$("#block").remove();	
			start(res);
		}
	}
	
}
function focusName(){	
	$(".errLogin").hide();	
}