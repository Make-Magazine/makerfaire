jQuery(document).ready(function(){
	// change up the cookie privacy popup styles and language
	if(jQuery("#cliModalClose").length) {
		jQuery("#cliModalClose").css({"width": "180px", "top": "20px", "color": "#005E9A"});
		jQuery("#cliModalClose svg").remove();
		jQuery("#cliModalClose .wt-cli-sr-only").text("Save Configuration");
	}
});