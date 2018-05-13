function updateStart(){		
	var preg = $("#iSearch").val();	
	$.ajax({
		type: "POST",
		url: "Data/updateStart",
		async: false,
		data: {preg: preg},
		success: success
		});
	function success(res){		
		var r = JSON.parse(res);
		//alert(r.users);
		$("#listPlayers, #listFriends").empty();		
		for(var i=0;i<r.users.length;i++){			
			$("#listPlayers").append(r.tplPlayer);
			$("#listPlayers .player").append(function(i1, val){
				 if(i == i1)
					return r.users[i][0]; 
			});		
			$("#listPlayers .wins").html(function(i2, val){
				if(i == i2)
					if(r.users[i][2] != undefined)
						return r.users[i][2];
			}); 
			if((r.users[i][1] == 1) || (r.users[i][1] == 3)){
				$("#listPlayers .player").css("color", function(i3, val){
					if(i == i3)
						return "red";
				});
			}
			if(r.login == 1)
				$("#listPlayers .online").append(function(i4, val){
					if(i == i4)
						return r.buttonAdd;
				});
			$("#listPlayers .online").append(function(i5, val){
				if(i == i5)
					return r.buttonChat;
			});
		}
		if(r.friends.length == 0)
			$("#titFriends").hide();
		else 
			$("#titFriends").show();
		for(var i=0;i<r.friends.length;i++){			
			$("#listFriends").append(r.tplPlayer);
			$("#listFriends .player").append(function(i1, val){
				if(i == i1)
					return r.friends[i][0];
			});
			$("#listFriends .wins").html(function(i2, val){
				if(i == i2)
					if(r.friends[i][2] != undefined)
						return r.friends[i][2];
			}); 
			if((r.friends[i][1] == 1) || (r.friends[i][1] == 3)){
				$("#listFriends .player").css("color", function(i6, val){
					if(i == i6)
						return "red";
				});
			}
			if(r.login == 1)
				$("#listFriends .online").append(function(i7, val){
						if(i == i7)
							return r.buttonRem;
				});
			$("#listFriends .online").append(function(i8, val){
				if(i == i8)
					return r.buttonChat;
			});
		}
		$("#listPlayers .player").each(function(i9, el){
			$(el).click(function(){				
				sendInvite({user: r.users[i9][0], 
					coord: {top: $(el).offset().top, left: $(el).offset().left}});
			});			
		});
		$("#listFriends .player").each(function(i10, el){
			$(el).click(function(){
				sendInvite({user: r.friends[i10][0], 
					coord: {top: $(el).offset().top, left: $(el).offset().left}});
			});
		});
		$("#listPlayers .bChat").each(function(i11, el){
			$(el).click(function(){
				chat(r.users[i11][0]);
			});
		});
		$("#listFriends .bChat").each(function(i12, el){
			$(el).click(function(){
				chat(r.friends[i12][0]);
			});
		});
		if(r.login == 1){
			$("#listPlayers .bAddFriend").each(function(i13, el){
				$(el).click(function(){			
					addFriend(r.users[i13][0]);
				});
			});
			$("#listFriends .bRemPlGame").each(function(i14, el){
				$(el).click(function(){					
					remFriend(r.friends[i14][0]);
				});
			});
		}		
		
		if(r.myStatus == 3){
			stopInterval("beginID");
			prepareGame();			
		}		
		if(r.whoInvite.length != 0){
			$("#invites").empty();
			$("#invites").show();			
			for(var i=0;i<r.whoInvite.length;i++){
				$("#invites").append(r.tplInvite);				
				$(".nameInvite").prepend(function(i15, val){
					if(i == i15)		
						return r.whoInvite[i][0];
				});
				$(".betInvite").append(function(i151, val){
					if(i == i151)
						return r.whoInvite[i][1];
				});	
			}
			$(".bAccept").each(function(i16, el){				
				$(el).click(function(){					
					acceptInvite(r.whoInvite[i16][0]);
				});
			});	
			$(".bCancel").each(function(i17, el){
				$(el).click(function(){
					cancelInvite(r.whoInvite[i17][0]);
				});
			});
		} else {
			$("#invites").hide();	
		}
		if((r.answer.length != 0) && (r.myStatus == 1)){
			$("#start").show();
			$("#startBox").empty();
			$("#helpStart").hide();
			for(var i=0;i<r.answer.length;i++){
				$("#startBox").append(r.tplListInvited);
				var pic = "default";
				switch(r.answer[i][1]){
				case "0":
					pic = "question";
					break;
				case "1":
					pic = "accept";
					break;
				case "2":					
					pic = "cancel";
					break;
				}				
				$(".imgAnswer").attr("src", function(i18, val){
					if(i == i18)						
						return "Views/img/"+pic+".png";				
				});
				$(".playerInvited").html(function(i19, val){
					if(i == i19)
						return r.answer[i][0];
				});					
				if((r.iAmMaster == 1) && (r.answer[i][0] != r.user)){
					$(".answer").append(function(i20, val){
						if(i == i20)		
							return r.buttonRem;	
					});			
				}
			}
			$(".answer .bRemPlGame").each(function(i21, el){
				$(el).click(function(){
					remInvite(r.answer[i21+1][0]);
				});
			});
		} else {
			$("#start").hide();
			$("#helpStart").show();			
		}
		var count = 0;
		for(var i=0;i<r.answer.length;i++){
			if(r.answer[i][1] == "1")
				count++;
		}
		if((count>1) && (r.iAmMaster == 1)){
			$("#bStart").show();		
		} else {
			$("#bStart").hide();
		}		
			
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
			remMessageStart($(this).attr("param"));
		});
	}
}
function remMessageStart(mess){	
	$.post("Chat/remMess", {mess: mess}, function(res){updateStart();});
}
function createInvite(obj, bet){	
	$.ajax({
		type: "POST",
		url: "Begin/sendInvite",
		async: false,
		data: {user: obj.user, bet: bet},
		success: success
	});
	function success(res){		
		switch(res){
		case "1":
			$("#c1").show();
			$("#c1").offset(obj.coord);
			setTimeout('$("#c1").hide()', 2000);
			break;
		case "2":
			$("#c2").show();
			$("#c2").offset(obj.coord);
			setTimeout('$("#c2").hide()', 2000);
			break;
		case "3":
			$("#c3").show();
			$("#c3").offset(obj.coord);
			setTimeout('$("#c3").hide()', 2000);
			break;
		case "4":			
			$("#c4").show();
			$("#c4").offset(obj.coord);
			setTimeout('$("#c4").hide()', 2000);
			break;		
		case "5":			
			$("#c5").show();
			$("#c5").offset(obj.coord);
			setTimeout('$("#c5").hide()', 2000);
			break;	
		case "6":			
			$("#c6").show();
			$("#c6").offset(obj.coord);
			setTimeout('$("#c6").hide()', 2000);
			break;	
		}		
	}	
}
function sendInvite(obj){	
	$("#divListPlayers").animate({left: -1000}, 1000);
	$("#bSlideUsers").show();
	$("#bArrowLeft").hide();	
	$.ajax({
		type: "POST",
		url: "Begin/checkGame",
		async: false,
		data: {},
		success: success
	});	
	function success(res){	
			if(res == -1){
			$("#field").append(getPage("bet"));
			$("#bBet").click(function(){
				var myCoins = $("#myCoins").text();				
				var regexp = /\d+/;				
				var v = $("#iBet").val();								
				if(!isNaN(v) && (v != "")){
					if((parseInt(v) > myCoins) || (parseInt(v) < 0)){
						$("#iBet").val(0);						
					} else {
						bet = $("#iBet").val();											
						$("#bet").remove();
						createInvite(obj, bet);
					}
				} else {
					$("#iBet").val(10);					
				}
			});	
			$("#bCancelBet").click(function(){
				$("#bet").remove();
			});				
		} else {
			createInvite(obj, res);
		}	
	}
}
function cancelInvite(user){	
	$.ajax({
		type: "POST",
		url: "Begin/cancelInvite",
		async: false,
		data: {user: user},
		success: success
		});
	function success(res){
		updateStart();	
	}	
}
function acceptInvite(user){	
	$.ajax({
		type: "POST",
		url: "Begin/acceptInvite",
		async: false,
		data: {user: user},
		success: success
	});
	function success(res){		
		updateStart();
	}	
}
function remInvite(user){
	$.ajax({
		type: "POST",
		url: "Begin/remInvite",
		async: false,
		data: {user: user},
		success: success
		});
	function success(res){		
		updateStart();	
	}
}