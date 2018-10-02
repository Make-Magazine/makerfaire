// used by old nav... left here in case there are legacy pages with the old header
jQuery(function() {
  jQuery('.desktop-nav .sub-menu').wrapInner('<div class=\'container\'></div>');
});

function sumomeActive() {
	if ( document.querySelector(".sumome-react-wysiwyg-popup-container") != null ) {
		jQuery('body').addClass('sumome-active');
	} else {
		jQuery('body').removeClass('sumome-active');
	}
}

(function($) {
	
   // keep these from happening before any angular or login scripts
	
	
   $(window).bind("load", function() {
		if(mobileCheck() == true) {
			jQuery('body').addClass("mobile");
		}
		// Left hand nav script
		if(jQuery('.left-hand-nav').length > 0){
			jQuery('.left-hand-nav .menu-item-has-children > a').css("pointer-events", "all");
			jQuery('.left-hand-nav .menu-item-has-children > a').click(function(event){
				jQuery(this).parent().toggleClass('expanded-lnav');
				event.preventDefault();
			});
			// expand current page category
			jQuery('.left-hand-nav .current-menu-item').parent().parent().toggleClass('expanded-lnav');
		}
		
		// USERSNAP
		var firstPath = location.pathname.split("/")[1];
		if ( firstPath != "new-york" && firstPath != "bay-area" && firstPath != "") {
			jQuery("#us_report_button").text("Website Help");
			jQuery('body').addClass('usersnap');
		} 
		
      $(".nav-level-1-auth #profile-view .avatar").css("display","block");

   });
})(jQuery);
