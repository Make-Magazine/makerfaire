jQuery.fn.isInViewport = function() {
  if(jQuery(this) != undefined) {
    var elementTop = jQuery(this).offset().top;
    var elementBottom = elementTop + jQuery(this).outerHeight();

    var viewportTop = jQuery(window).scrollTop();
    var viewportBottom = viewportTop + jQuery(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;
  }
};

jQuery(document).ready(function(){
	jQuery('a[href^="#"]:not(a[href="#"])').click(function(){
		  jQuery('html, body').animate({
				scrollTop: jQuery('[name="' + jQuery.attr(this, 'href').substr(1) + '"]').offset().top
		  }, 500);
		  return false;
	});
	
	if (document.getElementById("#menu-toolkit-left-hand-nav")) {
		// highlight the first one on load
		jQuery("#menu-toolkit-left-hand-nav li").first().addClass("active");
		// highlight the left nav based on what area of the page a user is mousing over or clicking on.
		jQuery(".toolkit-section").mouseenter(function(){
			var section = ".toolkit-nav ." +  jQuery(this).attr('id');
			jQuery(".toolkit-nav li").removeClass("active");
			jQuery(section).addClass("active");
		});

		jQuery(".sub-section-header a").click(function(){
			jQuery(".toolkit-nav li").removeClass("active");
			jQuery(this).parent().addClass("active");
		});

		// if the lefthand nav is too tall, just don't have it be sticky
		if(jQuery(".toolkit-nav").height() > jQuery(window).height()) {
			jQuery(".left-hand-nav").css("position", "relative").css("top", "0px");
		}

		jQuery(window).scroll(function(){
			if (!jQuery('#menu-toolkit-left-hand-nav').isInViewport() && jQuery('#menu-toolkit-left-hand-nav .back-to-top').length == 0) {
				jQuery(".left-nav-back-to-top").show();
			}else{
				jQuery(".left-nav-back-to-top").hide();
			}
		});

		/*
		var leftNavPos = 0;
		var leftNavBottom = jQuery(".toolkit-nav").offset().top + jQuery(".toolkit-nav").outerHeight(true);

		jQuery(window).scroll( function(){
			leftNavScroll();
			leftNavBottom = jQuery(".toolkit-nav").offset().top + jQuery(".toolkit-nav").outerHeight(true);
		});

		leftNavScroll();

		if(jQuery(".downArrow").length && ( leftNavBottom > 605 ) ){
			jQuery(".downArrow").on('mousedown', function() {
				console.log(jQuery(".downArrow").offset().top);
				console.log("vs leftnav bottom: " + leftNavBottom);
				leftNavPos+=50;
				jQuery(".toolkit-nav").animate({ bottom: leftNavPos });
				leftNavBottom = jQuery(".toolkit-nav").offset().top + jQuery(".toolkit-nav").outerHeight(true);
			});
		}*/
	}

});

function leftNavScroll(){
	if( jQuery(".toolkit-nav").height() > jQuery(window).height() && jQuery(".downArrow").length == 0 ) {
		jQuery(".left-hand-nav").append("<a class='downArrow'></a>");
	}else{
		jQuery(".left-hand-nav").remove(".downArrow");
	}
}