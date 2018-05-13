function chat(user){	
	$("#chatArea").slideDown();
	$("#bSendMess").unbind("click");
	$("#bCancelMess").unbind("click");
	$("#areaMess").unbind("keyup");
	$("#areaMess").unbind("blur");
	$("#areaMess").focus();	
	$("#areaMess").keyup(function(e){
		if((e.keyCode==13)&&(!e.ctrlKey)){
			e.preventDefault();
			sendMess(user);	
		}
		if((e.keyCode==13)&&(e.ctrlKey)){			
			var m = $("#areaMess").val();
			$("#areaMess").val(m+"\n");
		}
		if(e.keyCode!=13)
			changeMess();
	});
	$("#bSendMess").click(function(){		
		sendMess(user);
	});
	$("#bCancelMess").click(function(){		
		$("#chatArea").slideUp();
		$("#areaMess").val("");	
	});
}
function changeMess(){	
	var txt = $("#areaMess").val();		
	$("#sp").text(txt);	
	var w = $("#sp").width();
	var wa = $("#areaMess").width();	
	for(var i=0;i<txt.length;i++){	
		if(txt.charCodeAt(i) == 10)
			w += wa;
	}	
	$("#areaMess").css("height", Math.floor(w/wa)*30+30);	
}
function sendMess(user){	
	$.ajax({
		type: "POST",
		url: "Chat/sendMess",
		async: false,
		data: {to: user, mess: $("#areaMess").val()},
		success: success
	});
	function success(res){
		$("#chatArea").slideUp();
		$("#areaMess").val("");
		updateStart();
	}
}