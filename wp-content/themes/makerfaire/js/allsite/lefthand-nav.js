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

	if (jQuery("#menu-toolkit-left-hand-nav").length) {
		jQuery('a[href^="#"]:not(a[href="#"])').click(function(){
			  jQuery('html, body').animate({
					scrollTop: jQuery('[name="' + jQuery.attr(this, 'href').substr(1) + '"]').offset().top
			  }, 500);
			  return false;
		});
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
		var windowHeight = jQuery(window).height() - 140;
		if(jQuery(".toolkit-nav").height() > windowHeight) {
			jQuery(".left-hand-nav").css("position", "relative").css("top", "0px");
		}

		jQuery(window).scroll(function(){
			if (!jQuery('#menu-toolkit-left-hand-nav').isInViewport() && jQuery('#menu-toolkit-left-hand-nav .back-to-top').length == 0) {
				jQuery(".left-nav-back-to-top").show();
			}else{
				jQuery(".left-nav-back-to-top").hide();
			}
		});
	}
});

function leftNavScroll(){
	if( jQuery(".toolkit-nav").height() > jQuery(window).height() && jQuery(".downArrow").length == 0 ) {
		jQuery(".left-hand-nav").append("<a class='downArrow'></a>");
	}else{
		jQuery(".left-hand-nav").remove(".downArrow");
	}
}