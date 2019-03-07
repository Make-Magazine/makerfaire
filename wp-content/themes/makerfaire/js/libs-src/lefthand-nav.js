jQuery(document).ready(function(){
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
	
});