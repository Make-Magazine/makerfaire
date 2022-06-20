
(function($) {

   // keep these from happening before any angular or login scripts

   jQuery(window).bind("load", function() {

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

      jQuery(".nav-level-1-auth #profile-view .avatar").css("display","block");


   });
})(jQuery);
