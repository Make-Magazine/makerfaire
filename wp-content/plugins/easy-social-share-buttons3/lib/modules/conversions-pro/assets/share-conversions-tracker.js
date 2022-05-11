jQuery(document).ready(function($){
	"use strict";
	
	var essb_conversion_tracking = window.essb_conversion_tracking = function(service, post_id, instance_id) {
		var element = $('.essb_'+instance_id),
			instance_postion = $(element).attr("data-essb-position") || '',
			obj = {};
		
		obj['network'] = service;
		obj['post_id'] = post_id;
		obj['position'] = instance_postion;
		
		essbShareConversionProLog('sharing_conversion_share', obj);
	}
	
	var essbShareConversionProLog = window.essbShareConversionProLog = function(action, postData) {
		var isMobile = false,
			url = window.location.href.split('?')[0];
	
		if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
			isMobile = true;
		}
			
		postData['essb-ajax'] = action;
		postData['mobile'] = isMobile ? 'true' : 'false';
		if (!postData['post_id']) postData['post_id'] = essb_settings && essb_settings.post_id ? essb_settings.post_id : '';
		
		console.log(postData);
	
		$.post(url, postData, function (data) { 
			if (data) {
				console.log(data);
			}
		},'json');
	}	
	
	/**
	 * Starting logging of the share positions
	 */	
	if ($('.essb_links').length) {
		var positions = {};
		$('.essb_links').each(function() {
			var instancePosition = $(this).attr("data-essb-position") || "";
			
			if (!positions[instancePosition]) positions[instancePosition] = [];
			
		    $(this).find("li").each(function() {
				var classList =  jQuery(this).attr("class").split(/\s+/);
				
				for (var i=0;i<classList.length;i++) {
					if (classList[i].indexOf("essb_link_") == -1) continue;
					var key = classList[i].replace("essb_link_", "");
					
					if (positions[instancePosition].indexOf(key) == -1) positions[instancePosition].push(key);					
				}
  			});
		});
		
		essbShareConversionProLog('sharing_conversion_register', { 'conversion': positions });
	}	
});