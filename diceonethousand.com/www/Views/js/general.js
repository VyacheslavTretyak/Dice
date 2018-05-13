function getCookie(name){
	var matches = document.cookie.match(new RegExp(
		"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
	return matches ? decodeURIComponent(matches[1]) : undefined;
}
function setCookie(name, value, options){
  options = options || {};
  var expires = options.expires;
  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }
  value = encodeURIComponent(value);
  var updatedCookie = name + "=" + value;
  for (var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
    }
  }
  document.cookie = updatedCookie;
}
function deleteCookie(name) {
  setCookie(name, "", {
    expires: -1
  })
}
function startInterval(name, func, time){
	while(getCookie(name) != undefined){
		clearInterval(getCookie(name));
		deleteCookie(name);
	}
	var i = setInterval(func, time);
	setCookie(name, i);
	//alert("start : "+name);	
}
function stopInterval(name){	
	if(getCookie(name) != undefined){
		clearInterval(getCookie(name));
		deleteCookie(name);
		//alert("stop : "+name);
	}
}
function getPage(page){	
	var r = "";
	$.ajax({
		type: "POST",
		url: "Main/get",
		async: false,
		data: {page: page},
		success: success
	});
	return r;
	function success(res){		
		r = res;		
	}
}