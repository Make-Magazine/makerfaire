/**
 * Top Bar, Bottom Bar 
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.0
 */

jQuery(document).ready(function($){
	"use strict";
	
	/**
	 * Display Method: Bottom Bar
	 */
	
	if (!debounce) {
		var debounce = function( func, wait ) {
			var timeout, args, context, timestamp;
			return function() {
				context = this;
				args = [].slice.call( arguments, 0 );
				timestamp = new Date();
				var later = function() {
					var last = ( new Date() ) - timestamp;
					if ( last < wait ) {
						timeout = setTimeout( later, wait - last );
					} else {
						timeout = null;
						func.apply( context, args );
					}
				};
				if ( ! timeout ) {
					timeout = setTimeout( later, wait );
				}
			};
		};
	}
	
	var essb_int_value = function(value) {
		value = parseInt(value);
		
		if (isNaN(value) || !isFinite(value)) value = 0;
		return value;
	}
	
	var essb_responsiveEventsCanRun = function(element) {
		var hideOnMobile = $(element).hasClass('essb_mobile_hidden'),
			hideOnDesktop = $(element).hasClass('essb_desktop_hidden'),
			hideOnTablet = $(element).hasClass('essb_tablet_hidden'),
			windowWidth = $(window).width(),
			canRun = true;
	
		if (windowWidth <= 768 && hideOnMobile) canRun = false;
		if (windowWidth > 768 && windowWidth <= 1100 && hideOnTablet) canRun = false;
		if (windowWidth > 1100 && hideOnDesktop) canRun = false;
		
		if (!$(element).length) canRun = false;
	
		return canRun;
	};

	function essb_bottombar_onscroll() {
		var current_pos = $(window).scrollTop();
		var height = $(document).height()-$(window).height();
		var percentage = current_pos/height*100;

	
		var element;
		if ($(".essb_bottombar").length)
			element = $(".essb_bottombar");


		if (!element || typeof(element) == "undefined") return;

		var value_appear = essb_int_value($(element).find('.essb_links').data('bottombar-appear') || '');
		var value_disappear = essb_int_value($(element).find('.essb_links').data('bottombar-disappear') || '');

		if (value_appear > 0 ) {
			if (percentage >= value_appear && !element.hasClass("essb_active_bottombar")) {
				element.addClass("essb_active_bottombar");
				return;
			}

			if (percentage < value_appear && element.hasClass("essb_active_bottombar")) {
				element.removeClass("essb_active_bottombar");
				return;
			}
		}
		if (value_disappear > 0) {
			if (percentage >= value_disappear && !element.hasClass("hidden-float")) {
				element.addClass("hidden-float");
				element.css( {"opacity": "0"});
				return;
			}
			if (percentage < value_disappear && element.hasClass("hidden-float")) {
				element.removeClass("hidden-float");
				element.css( {"opacity": "1"});
				return;
			}
		}
	}

	if ($(".essb_bottombar").length)
		if (essb_responsiveEventsCanRun($('.essb_bottombar'))) {
			var element = $('.essb_bottombar');
			if (($(element).find('.essb_links').data('bottombar-appear') || '') != '' || ($(element).find('.essb_links').data('bottombar-disappear') || '') != '')
				$(window).on('scroll', debounce(essb_bottombar_onscroll, 1));
		}

	/**
	 * Display Method: Top Bar
	 */

	function essb_topbar_onscroll() {
		var current_pos = $(window).scrollTop();
		var height = $(document).height()-$(window).height();
		var percentage = current_pos/height*100;

		var element;
		if ($(".essb_topbar").length)
			element = $(".essb_topbar");


		if (!element || typeof(element) == "undefined") return;

		var value_appear = essb_int_value($(element).find('.essb_links').data('topbar-appear') || '');
		var value_disappear = essb_int_value($(element).find('.essb_links').data('topbar-disappear') || '');

		if (value_appear > 0 ) {
			if (percentage >= value_appear && !element.hasClass("essb_active_topbar")) {
				element.addClass("essb_active_topbar");
				return;
			}

			if (percentage < value_appear && element.hasClass("essb_active_topbar")) {
				element.removeClass("essb_active_topbar");
				return;
			}
		}
		if (value_disappear > 0) {
			if (percentage >= value_disappear && !element.hasClass("hidden-float")) {
				element.addClass("hidden-float");
				element.css( {"opacity": "0"});
				return;
			}
			if (percentage < value_disappear && element.hasClass("hidden-float")) {
				element.removeClass("hidden-float");
				element.css( {"opacity": "1"});
				return;
			}
		}
	}

	if (essb_responsiveEventsCanRun($('.essb_topbar'))) {
		if ($(".essb_topbar").length) {
			var element = $(".essb_topbar");
			if (($(element).find('.essb_links').data('topbar-appear') || '') != '' || ($(element).find('.essb_links').data('topbar-disappear') || '') != '')
				$(window).on('scroll', debounce(essb_topbar_onscroll, 1));
		}
	}

});