jQuery(document).ready(function($){
	"use strict";
	
	//subscribe_conversion_loaded
	
	var essbSubscribeProLog = window.essbSubscribeProLog = function(action, position, design) {
		var isMobile = false,
			url = window.location.href.split('?')[0],
			postData = {};
		
		if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
			isMobile = true;
		}
				
		postData['essb-ajax'] = action;
		postData['position'] = position;
		postData['design'] = design;
		postData['mobile'] = isMobile ? 'true' : 'false';
		postData['post_id'] = essb_settings && essb_settings.post_id ? essb_settings.post_id : '';
		
		console.log(postData);

		$.post(url, postData, function (data) { 
			if (data) {
				console.log(data);
			}
		},'json');
	};
	
	/**
	 * Starting logging of the content positions
	 */
	$(".essb-subscribe-form-content").each(function(){
		var position = $(this).attr("data-position") || '',
			design = $(this).attr('data-design') || '',
			corePosition = position.split('-')[0];
		
		// ignore booster, flyout or locker until they fire
		if (['booster', 'flyout', 'locker'].indexOf(corePosition) > -1) return;
		essbSubscribeProLog('subscribe_conversion_loaded', position, design);
	});
});