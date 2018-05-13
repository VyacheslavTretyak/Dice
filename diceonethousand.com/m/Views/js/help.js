function help(){	
	$("#scr").append(getPage("help"));
	$("#help").click(function(){
		$("#help").remove();
	});	
}