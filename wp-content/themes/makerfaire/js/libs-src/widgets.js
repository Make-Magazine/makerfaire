// fit text to fit inside the read more section of the featured items rollover boxes
function fitTextToBox(){
	jQuery(".grid-item").each(function() {
		var availableHeight = jQuery(this).innerHeight() - 30;
		 if(jQuery(this).find(".read-more-link").length > 0){
			 availableHeight = availableHeight - jQuery(this).find(".read-more-link").innerHeight() - 30;
		 }

		 jQuery(jQuery(this).find(".desc-body")).css("mask-image", "-webkit-linear-gradient(top, rgba(0,0,0,1) 80%, rgba(0,0,0,0) 100%)");

		 if( 561 > jQuery(window).width() ) {
		   jQuery(jQuery(this).find(".desc-body")).css("mask-image", "none");
			jQuery(jQuery(this).find(".desc-body")).css("height", "auto");
		 } else {
			jQuery(jQuery(this).find(".desc-body")).css("height", availableHeight);
		 }
	 });
}
