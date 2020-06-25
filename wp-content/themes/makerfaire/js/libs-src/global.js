
jQuery(document).ready(function(){
	// change up the cookie privacy popup styles and language
	if(jQuery("#cliModalClose").length) {
		jQuery("#cliModalClose").insertAfter(jQuery(".cli-tab-section-container")).css({"width": "180px", "position": "relative", "color": "#005E9A", "margin-top": "10px", "margin-left": "-10px"});
		jQuery("#cliModalClose svg").remove();
		jQuery("#cliModalClose .wt-cli-sr-only").text("Save Configuration").css("display", "block");
	}
});