function confirm_campaign() {

	var campaign_name = document.getElementById("sm_wp_feed_campaign_name").value;
	var campaign_subject = document.getElementById("sm_wp_feed_campaign_subject").value;
	
	var list_resource = document.getElementById("sm_wp_feed_contactlist_name");
	var contactlist_name = list_resource.options[list_resource.selectedIndex].text;
	
	var sender_address = document.getElementById("sm_wp_feed_sender_address").value;

	if (confirm(sprintf(SM_JS_DATA.startcampaign_text, campaign_name, campaign_subject, contactlist_name, sender_address)) === true) return true;
	else return false;
}
 
function resize_iframe(el) {

	var newheight = el.contentWindow.document.body.scrollHeight;
	var newwidth = el.contentWindow.document.body.scrollWidth;

	el.height = (newheight) + "px";
	el.width = (newwidth) + "px";
}

function sprintf() {
	
	var data = arguments;
	
	var text = data[0];
	var c = 0;
	
	return text.replace(/%s/g, function (match) {
		c++;
		return typeof data[c] !== "undefined" ? data[c] : match;
	});
}

function smInitCredentialsBlur() {

	smToggleCredentialsVisibility('sm_api_username', 'hide');
	smToggleCredentialsVisibility('sm_api_password', 'hide');

	document.getElementById('sm_api_username').onfocus = function () {
		smToggleCredentialsVisibility('sm_api_username', 'show');
	};
	document.getElementById('sm_api_password').onfocus = function () {
		smToggleCredentialsVisibility('sm_api_password', 'show');
	};
}

function smToggleCredentialsVisibility(id, state) {

	if (id !== 'sm_api_username' && id !== 'sm_api_password') {
		return;
	}

	var style = '';
	var text = '';

	var button = document.getElementById('button_' + id);

	if (!state) {
		state = button.getAttribute('smState');
	}

	if (state === 'hide') {
		style = 'color:transparent;text-shadow:0 0 5px rgba(0,0,0,0.5)';
		text = 'show';
	}
	else if (state === 'show') {
		style = '';
		text = 'hide';
	}

	button.setAttribute('smState', text);
	button.innerHTML = text;
	document.getElementById(id).setAttribute('style', style);
}