/**
 * Core plugin script for showing and manipulating notifications on scree
 * @package SocialProofNotifications
 */
(function () {

	"use strict";
	
	function essbspn_read_cookie(key) {
		var pairs = document.cookie.split("; ");
		for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
			if (pair[0] === key) return pair[1] || "";
		}
		return null;
	}
	
	function essbspn_write_cookie(key, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		} else var expires = "";
		document.cookie = key+"="+value+expires+"; path=/";
	}
	
	
	jQuery(document).ready(function( $ ){
		if (!$('.essbspn-holder').length) return; // no notifications, what else to do
		var count = $('.essbspn-holder').data('count') || '',
			delayStart = $('.essbspn-holder').data('start') || '',
			delayStay = $('.essbspn-holder').data('stay') || '',
			delayBetween = $('.essbspn-holder').data('between') || '',
			messageLoop = $('.essbspn-holder').data('loop') || '',
			index = 1,
			periodHolder = null;
		
		if (!Number(count) || isNaN(count)) count = 1;
		if (!Number(delayStart) || isNaN(delayStart)) delayStart = 10;
		if (!Number(delayStay) || isNaN(delayStay)) delayStay = 5;
		if (!Number(delayBetween) || isNaN(delayBetween)) delayBetween = 5;
		
		function essbSPNAnimateBox() {
			var element = $('.essbspn-holder .essbspn-index-'+index);
			
			/**
			 * User configure the option to loop message from the beginning as soon as they
			 * reach the end of list
			 */
			if (messageLoop == 'yes' && !$(element).length) {
				index = 1;
				element = $('.essbspn-holder .essbspn-index-'+index);
			}
			
			if (!$(element).length) return;
			
			$(element).addClass('visible');
			$(element).fadeIn(400, function() {
				index++;
				$(element).delay(delayStay * 1000).animate({ opacity: 0, bottom: -150 },300, function() {
					$(element).removeClass('visible');
					setTimeout(function() {
						essbSPNAnimateBox();
					}, delayBetween * 1000);
					
				});
				
			});
		}
	
		setTimeout(function() {
			index = 1;
			essbSPNAnimateBox();
		}, delayStart * 1000);
		
		/**
		 * Subscribe Form Popup
		 */
		
		var essbspn_subscribe_form = window.essbspn_subscribe_form = function() {
	
			var base_element = '.essbspn-booster';
			var base_overlay_element = '.essbspn-booster-overlay';
	
			if (!$(base_element).length) return;
			
			jQuery.fn.extend({
		        center: function () {
		            return this.each(function() {
		                var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
		                var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
		                jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
		            });
		        }
		    });
	
			var win_width = jQuery( window ).width();
			var doc_height = jQuery('document').height();
	
			var base_width = 700;
			if (win_width < base_width) { base_width = win_width - 60; }
	
			$(base_element).css( { width: base_width+'px'});
			$(base_element).center();
	
			$(base_element).fadeIn(400);
			$(base_overlay_element).fadeIn(200);
	
			$(base_element).addClass('active-subscribe-notification');
			$(base_overlay_element).addClass('active-subscribe-notification-overlay');
		}
	
		var essbspn_subscribe_form_close = window.essbspn_subscribe_form_close = function() {
	
	
			$(".active-subscribe-notification").fadeOut(200);
			$('.active-subscribe-notification').removeClass('active-subscribe-notification');
	
			$(".active-subscribe-notification-overlay").fadeOut(400);
			$('.active-subscribe-notification-overlay').removeClass('active-subscribe-notification-overlay');
		}
	
	});

})();