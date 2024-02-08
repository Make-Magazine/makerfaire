/* Provide a class for Safari, the new IE */
if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Mac') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
	// console.log('Safari on Mac detected, applying class...');
	jQuery('html').addClass('safari-mac'); // provide a class for the safari-mac specific css to filter with
}


jQuery(document).ready(function(){
	jQuery('.show-more-snippet').each(function() {
		if(jQuery(this).height() >= 70 ) {
			jQuery(this).after('<a href="#" class="show-more">More...</a>');
			jQuery('.show-more').click(function(event) {
				event.preventDefault();
				if(jQuery(this).prev().css('height') != '70px'){
					jQuery(this).prev().css('max-height', '70px');
					jQuery(this).prev().animate({height: '70px'}, 200);
					jQuery(this).text('More...');
				}else{
					jQuery(this).prev().css({'height':'100%', 'max-height':'none'});
					var xx = jQuery(this).prev().height();
					jQuery(this).prev().css({height:'70px'});
					jQuery(this).prev().stop().animate({height: xx - 80}, 400);
					jQuery(this).text('Less...');
				}
			});
		}
	});
});