function addFriend(fr){
	//alert(fr);
	$.ajax({
		type: "POST",
		url: "Data/addFriend",
		async: false,
		data: {user: fr},
		success: success
		});
	function success(res){
		updateStart();
	}
}
function remFriend(fr){
	//alert(fr);
	$.ajax({
		type: "POST",
		url: "Data/remFriend",
		async: false,
		data: {user: fr},
		success: success
		});
	function success(res){
		updateStart();
	}
}

