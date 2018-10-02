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




		
      // fix nav to top on scrolldown, stay fixed for transition from mobile to desktop
      var e = jQuery(".universal-nav");
      var hamburger = jQuery(".nav-hamburger");
      var y_pos = jQuery(".nav-level-2").offset().top;
      var nextItemUnderNav = jQuery("#main");

      if (jQuery(window).width() < 578) {
              jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
      }
      jQuery(window).on('resize', function(){
          if (jQuery(window).width() < 767) {
              y_pos = 0;
              nextItemUnderNav.css("margin-top", "55px");
          }else{
              y_pos = 75;
              nextItemUnderNav.css("margin-top", "0px");
          }
          if (jQuery(window).width() < 578) {
              jQuery(".auth-target").append(jQuery(".nav-level-1-auth"));
          }else{
              jQuery("nav.container").append(jQuery(".nav-level-1-auth"));
          }
      });
      jQuery(document).scroll(function() {
          var scrollTop = jQuery(this).scrollTop();
          if(scrollTop > y_pos && jQuery(window).width() > 748){
				  jQuery('body').addClass('scrolled');
              e.addClass("main-nav-scrolled"); 
              hamburger.addClass("ham-menu-animate");
              nextItemUnderNav.css("margin-top", "55px");
          }else if(scrollTop <= y_pos){
				  jQuery('body').removeClass('scrolled');
              e.removeClass("main-nav-scrolled"); 
              hamburger.removeClass("ham-menu-animate");
              if (jQuery(window).width() > 767) {
                nextItemUnderNav.css("margin-top", "0px");
              }
          }
          sumomeActive();
      });

   });
})(jQuery);
