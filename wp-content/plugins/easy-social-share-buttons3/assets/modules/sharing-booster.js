/**
 * Booster
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.0
 */

jQuery(document).ready(function($){
	"use strict";
	
	jQuery.fn.extend({
		center: function () {
			return this.each(function() {
				var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
				var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
				jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
			});
		}
	});
	
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
	
	var essb_int_value = function(value) {
		value = parseInt(value);
		
		if (isNaN(value) || !isFinite(value)) value = 0;
		return value;
	};
	
	var essb_setCookie = function(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toGMTString();
	    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
	};
	
	var essb_getCookie = function(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i].trim();
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	};

	

	/**
	 * Display Method: Share Booster
	 */

	if ($('.essb-sharebooster').length) {
		function essb_booster_trigger() {
			if (booster_shown) return;

			$('.essb-sharebooster').center();
			$('.essb-sharebooster').fadeIn(400);
			$('.essb-sharebooster-overlay').fadeIn(200);

			$('body').addClass('essb-sharebooster-preventscroll');

			booster_shown = true;

			if (Number(booster_autoclose))
				setTimeout(essb_booster_close, Number(booster_autoclose) * 1000);

		}

		function essb_booster_close() {
			$('.essb-sharebooster').fadeOut(200);
			$('.essb-sharebooster-overlay').fadeOut(400);

			$('body').removeClass('essb-sharebooster-preventscroll');
		}

		function essb_booster_close_from_action() {
			var boosterCookieKey = booster_donotshow == 'all' ? 'essb_booster_all' : 'essb_booster_' + essb_settings.post_id;

			essb_setCookie(boosterCookieKey, "yes", Number(booster_hide));
			essb_booster_close();
		}

		window.essb_booster_close_from_action = essb_booster_close_from_action;

		function essb_booster_scroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height() - $(window).height();
			var percentage = current_pos / height * 100,
				breakPercent = booster_scroll;

			if (percentage > breakPercent)
				essb_booster_trigger();

		}

		var booster_trigger = $('.essb-sharebooster').attr('data-trigger') || '',
			booster_time = $('.essb-sharebooster').attr('data-trigger-time') || '',
			booster_scroll = $('.essb-sharebooster').attr('data-trigger-scroll') || '',
			booster_hide = $('.essb-sharebooster').attr('data-donotshow') || '',
			booster_donotshow = $('.essb-sharebooster').attr('data-donotshowon') || '',
			booster_autoclose = $('.essb-sharebooster').attr('data-autoclose') || '',
			booster_shown = false;

		if (!Number(booster_hide)) booster_hide = 7;

		var boosterCookieKey = booster_donotshow == 'all' ? 'essb_booster_all' : 'essb_booster_' + essb_settings.post_id;
		var cookie_set = essb_getCookie(boosterCookieKey);

		// booster is already triggered
		if (cookie_set) booster_trigger = 'disabled';

		if (essb_responsiveEventsCanRun($('.essb-sharebooster'))) {
			if (booster_trigger == '')
				essb_booster_trigger();
			if (booster_trigger == 'time')
				setTimeout(essb_booster_trigger, Number(booster_time) * 1000)
			if (booster_trigger == 'scroll')
				$(window).on('scroll', debounce(essb_booster_scroll, 1));
		}

		if ($('.essb-sharebooster-close').length) {
			$('.essb-sharebooster-close').on('click', function(e){
				e.preventDefault();
				essb_booster_close();
			});
		}
	}

	
});