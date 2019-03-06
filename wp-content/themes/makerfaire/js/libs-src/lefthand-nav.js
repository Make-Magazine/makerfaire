jQuery(document).ready(function(){
	jQuery('a[href^="#"]:not(a[href="#"])').click(function(){
		  jQuery('html, body').animate({
				scrollTop: jQuery('[name="' + jQuery.attr(this, 'href').substr(1) + '"]').offset().top
		  }, 500);
		  return false;
	});
});