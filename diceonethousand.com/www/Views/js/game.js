function prepareGame(){
	$("#scr").html(getPage("game"));
	$("#menu").html(getPage("menuGame"));	
	$(".bBreak").click(function(){
		breakGame();
	});
	$("#bMove").click(function(){
		clickDice();
	});	
	$("#bNext").click(function(){
		clickMove();
	});
	$("#bHelp").click(function(){
		help();
	});		
	updateGame();	
	startInterval("gameID", "updateGame()", 5000);
}
function startGame(){
	$.ajax({
		type: "POST",
		url: "Game/startGame",
		async: false,
		data: "",
		success: success
		});
	function success(res){		
		setCookie("statusGame", 9);		
		prepareGame();
	}	
}
function updateGame(){	
	$.ajax({
		type: "POST",
		url: "Game/updateGame",
		async: false,
		data: "",
		success: success
		});
	function success(res){
		//alert(res);
		var r = JSON.parse(res);		
		//move[0] - останні оновленні значення кісток
		//move[1] - чи роблю я хід(bool)	
		//move[2] - статус гри
		//move[3] - кінець ходу			
		if((r.move[0] != getCookie("dice"))&&(r.move[0] != 0)){
			$("#imgDice").attr("src", "Game/getImgDice?"+r.move[0]);
			setCookie("dice", r.move[0]);
		}
		if((r.move[2] == 0) && (getCookie("statusGame") != 0)){
			$("#sortition").fadeIn(3000);
			setTimeout("$('#sortition').fadeOut(3000)", 3000);
			setCookie("statusGame", 0);
		}
		if((r.move[2] == 1) && (getCookie("statusGame") != 1)){
			$("#beginGame").fadeIn(3000);
			setTimeout("$('#beginGame').fadeOut(3000)", 3000);
			setCookie("statusGame", 1);
		}
			
		if(r.move[3] == 1){
			$("#bNext").show();
			$("#bMove").hide();				
		} else if(r.move[1] == 1){
			if(r.move[2] != 3){
				$("#bNext").hide();
				$("#bMove").show();				
			} else {
				$("#bNext").show();
				$("#bMove").show();
			}		
		} else if(r.move[1] == 0){
			$("#bMove").hide();	
			$("#bNext").hide();						
		}
		//players :
		//	[0] - player
		//	[1] - status
		//	[2] - progress
		//	[3] - pts
		//	[4] - sum
		//	[5] - total	
		//	[6] - хто строкив гру
		//  [7] - прочерки		
		$("#playersList").empty();		
		var ingame = 0;//скільки гравців лишилося		
		for(var i=0; i<r.players.length; i++){			
			if(r.players[i][1] == 3)
				ingame++;
			$("#playersList").append(r.tplPlayer);			
			$(".playerGame").html(function(i0, val){
				if(i0 == i){					
					return r.players[i0][0];
				}
			});
			$(".points").html(function(i1, val){
				if(i1 == i)
					return r.players[i1][3];
			});
			$(".sum").html(function(i2, val){
				if(i2 == i)
					return r.players[i2][4];
			});
			$(".total").html(function(i3, val){
				if(i3 == i){
					var t = r.players[i3][5];
					if(((t>=200) && (t<300)) || ((t>=600)&&(t<700)) || ((t>=900)&&(t<1000)))
						$(this).css({	"border":"solid 2px",
										"background-color":"OliveDrab"});
					return r.players[i3][5];
				}
			});			
			$(".lineGame").css("background-color", 
				function(i4, val){
					if(r.players[i4][1] == 4){						
						return "darkgray";
					}
					else if(r.players[i4][2] == 1)
						return "crimson";					
					else return val;
				}
			);					
			$(".addButton").append(function(i5, val){
				if(i5 != i)
					return;				
				if((r.user == r.players[i5][6]) && (r.players[i5][0] != r.players[i5][6]) && (r.players[i5][1] == 3)){					
					return r.buttonRem;
				} else 
					return;
			});	
			$(".lineGame").append(function(i6, val){
				if(i6 != i)
					return;				
				if((r.players[i6][0] != r.user) && (r.players[i6][1] == 3)){					
					return r.buttonChat;
				} else 
					return;
			});	
			checkAnimation(r.players[i][0], r.players[i][4], r.players[i][5]);			
			if(r.players[i][5] == 1000){				
				stopInterval("gameID");
				win(r.players[i][0]);				
			}			
		}
		$("#mainGame .bChat").click(function(){
			chat($(this).parents('.lineGame').find('.playerGame').html());
		});
		$(".dash").each(function(i7, val){
			for(var u=0;u<r.players[i7][7];u++){
				$(val).append("<span style='color: white'>■</span>");
			};
			for(var u=r.players[i7][7];u<3;u++){				
				$(val).append("<span style='color: gray'>□</span>");
			};
		});
		$(".bRemPlGame").click(function(){
			remPlayerGame($(this).parents('.lineGame').find('.playerGame').html());
		});		
		if(ingame <= 1){
			$("#scr").append(getPage("lastPlayer"));
			$(".bBreak").click(function(){
				breakGame();
			});
			stopInterval("gameID");
		}
		$("#bank").html(r.bank);
		$("#bonus").html(r.bonus);
		
		$("#messageArea").empty();			
		for(var i=0;i<r.chat.length;i++){			
			var dateMess = new Date(r.chat[i][3]*1000);
			dateMess = dateMess.toLocaleString();
			var dateHtml = "<span class='date'><span class='dateMess'>"+dateMess+"</span><div class='bHover bRemMess' param="+r.chat[i][3]+"> - </div></span></br>"
			if(r.user == r.chat[i][0])
				$("#messageArea").append("<img id='arrow' src='Views/img/arrowLeft.png'/><span class='toUser'>"+r.chat[i][1]+"</span>"+dateHtml);
			else
				$("#messageArea").append("<img id='arrow' src='Views/img/arrowRight.png'/><span class='fromUser'>"+r.chat[i][0]+"</span>"+dateHtml);
			$("#messageArea").append("<span class='textMessage'> - "+r.chat[i][2]+"</span></br>");
		}		
		$(".bRemMess").click(function(i9, val){
			remMessage($(this).attr("param"));
		});
	}
}
function remMessage(mess){	
	$.post("Chat/remMess", {mess: mess}, function(res){updateGame();});
}
function win(name){	
	$("#win").slideDown();
	$("#win").prepend(name+" ");
	$("#win").bind("click", function(){
		breakGame();
	});	
}
function checkAnimation(name, sum, total){		
	var data = getCookie(name);	
	if(data == undefined){
		var obj = {sum: sum, total: total};
		var jsn = JSON.stringify(obj);
		setCookie(name, jsn);	
		return;
	}
	var pars = JSON.parse(data);
	if(pars.sum != sum){
		var p = 0;
		$(".sum").each(function(i, val){					
			p = $(val).offset();
		});
		anim(sum - pars.sum, p);	
	}	
	if(pars.total != total){
		var p = 0;		
		$(".total").each(function(i, val){
			p = $(val).offset();
		});
		anim(total - pars.total, p);
	}		
	var obj = {sum: sum, total: total};
	var jsn = JSON.stringify(obj);
	setCookie(name, jsn);
	
}
function anim(val,  x){	
	if(x == 0)
		return;	
	$("#value").stop();
	$("#value").css({opacity: 1, top: "0px", left:"0px", fontSize: "350%"});	
	$("#value").offset({top: x.top-30, left: x.left});
	$("#value").html(val);
	$("#value").animate({
		opacity: 0,
		top: "-=70",
		left:"+=30",
		fontSize: "-=10"}, 3000, function(){$("#value").empty();}
	);
}
function remPlayerGame(user){		
	$.ajax({
		type: "POST",
		url: "Game/remPlayerGame",
		async: false,
		data: {user: user},
		success: success
		});	
	function success(res){		
		updateGame();
	}	
}
function breakGame(){
	$.ajax({
		type: "POST",
		url: "Game/breakGame",
		async: false,
		data: "",
		success: success
		});
	function success(res){		
		stopInterval("gameID");
		getLogin();			
	}	
}
function clickDice(){
	$.ajax({
		type: "POST",
		url: "Game/getStage",
		async: false,
		data: {},
		success: success
		});	
	function success(res){
		if(res=="0")		
			sortition();
		else
			gameProcess();
	}	
}
function clickMove(){
	$.ajax({
		type: "POST",
		url: "Game/endMove",
		async: false,
		data: {},
		success: success
		});	
	function success(res){	
		//alert(res);
		move();		
	}	
}
function gameProcess(){
	$.ajax({
		type: "POST",
		url: "Game/gameProcess",
		async: false,
		data: {},
		success: success
		});	
	function success(res){	
		//alert(res);		
		updateGame();		
	}
}

function sortition(){
	$.ajax({
		type: "POST",
		url: "Game/sortition",
		async: false,
		data: {},
		success: success
		});	
	function success(res){		
		// 0 - однакові значення
		// 1 - наступний хід
		// 2 - кінець сортування
		updateGame();		
		switch(res){		
		case '0':
			$("#sameVal").slideDown();
			setTimeout("$('#sameVal').slideUp()", 3000);
			return;
			break;
		case '1':				
			move();
			break;				
		case '2':
			var button = $("#bMove").html();
			$("#bMove").html($("#bNext").html());
			$("#bMove").unbind("click");
			$("#bMove").click(function(){
				endSortition(button);
			});
			break;
		}			
	}	
}
function endSortition(button){
	$.ajax({
		type: "POST",
		url: "Game/endSortition",
		async: false,
		data: {},
		success: success
		});	
	function success(res){
		$("#bMove").html(button);
		$("#bMove").unbind("click");		
		$("#bMove").click(function(){
			clickDice();
		});
		updateGame();
	}
}
function move(){
	$.ajax({
		type: "POST",
		url: "Game/move",
		async: false,
		data: {},
		success: success
		});	
	function success(res){			
		updateGame();
	}	
}