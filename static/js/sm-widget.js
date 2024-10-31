function subscribe_user(el) {

	var loading = '<p><img src="' + SM_JS_DATA.loading_img + '"></p>';
	var res = null;
	var acknowledge_request = 'is_sm_ajax_request=1';
	var request_params = "";
	var instance_id = el.id.slice(18); // extract sm_subscribe_form
	
	for (var i = 0; i < el.childNodes.length; i++) {
		if (el.childNodes[i].className === "sm_wp_sub_req_resp") {
			res = el.childNodes[i];
			break;
		}
	}
	
	if(!res) return;
	
	if (el.elements['EMAIL'].value) {
		if (!/(.+)@(.+){2,}\.(.+){2,}/.test(el.elements['EMAIL'].value)) {
			res.innerHTML = "<div class='error' >" + SM_JS_DATA.invalid_email + "</div>";
			return true;
		}
	}

	for (var i = 0; i < el.elements.length; i++) {
		
		if(el.elements[i].name) request_params += encodeURIComponent(el.elements[i].name) + "=" + encodeURIComponent(el.elements[i].value)+"&";
	}
	
	request_params += acknowledge_request;
	
	res.innerHTML = loading;

	var xmlhttp;

	if (window.XMLHttpRequest) xmlhttp = new XMLHttpRequest();
	else xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

	xmlhttp.onreadystatechange = function () {
		
		var data = {status: "error", message: SM_JS_DATA.response_not_arrived};

		if (xmlhttp.readyState === 4) {

			if (xmlhttp.status === 200) {

				try {
					data = JSON.parse(xmlhttp.responseText);
				} catch (e) {}

				if (data.status === "success") {
					el.reset();
					if (SM_JS_DATA.redirect) window.location = SM_JS_DATA.redirect;
				}
				
				if(data.respawn_captcha) {
					document.getElementById('form_captcha_img_' + instance_id).src = data.respawn_captcha.img;
					document.getElementById('form_captcha_prefix_' + instance_id).value = data.respawn_captcha.prefix;
					document.getElementById('form_input_captcha_' + instance_id).value = '';
				}

			}
			
			res.innerHTML = "<div class='" + data.status + "' >" + data.message + "</div>";
		}
	};

	xmlhttp.open("POST", "", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(request_params);
}