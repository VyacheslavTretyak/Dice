function loadLang(){
	$.ajax({
		type: "POST",
		url: "Main/LoadLang",
		async: false,
		data: "",
		success: success
		});	
	function success(res){					
		var r = JSON.parse(res);		
		var a = r[1];		
		for(var i=0; i<a.length; i++){			
			if(r[0] == a[i])
				$("#sel").append("<option selected="+a[i]+">"+a[i]+"</option>");
			else
				$("#sel").append("<option>"+a[i]+"</option>");
		}		
	}
}
function changeLang(r){	
	setCookie("lang", r, {expires: 365*24*3600});		
	window.location.reload();	
}