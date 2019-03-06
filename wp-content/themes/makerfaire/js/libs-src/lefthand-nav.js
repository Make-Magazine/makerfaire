jQuery(document).ready(function(){
	jQuery('a[href^="#"]:not(a[href="#"])').click(function(){
		  jQuery('html, body').animate({
				scrollTop: jQuery('[name="' + jQuery.attr(this, 'href').substr(1) + '"]').offset().top
		  }, 500);
		  return false;
	});
	jQuery(document).scroll(function() {
		var lnavBottom = jQuery("#menu-toolkit-left-hand-nav").offset().top + jQuery("#menu-toolkit-left-hand-nav").height();
		var footerTop = jQuery("footer").offset().top;
		// console.log( lnavBottom );
		//console.log( footerTop - lnavBottom );
		if( (footerTop - lnavBottom) <= 65  ) {
			jQuery("#menu-toolkit-left-hand-nav").fadeOut("fast");
		} else {
			jQuery("#menu-toolkit-left-hand-nav").fadeIn("fast");
		}
	});

});