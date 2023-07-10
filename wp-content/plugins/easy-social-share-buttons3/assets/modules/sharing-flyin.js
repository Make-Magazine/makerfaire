/**
 * Flyin
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

	var essb_is_after_comment = function() {
		var addr = window.location.href;

		return addr.indexOf('#comment') > -1 ? true : false;
	};	

	/**
	 * Display Method: Fly In
	 */
	if ($('.essb-flyin').length) {

		var flyinDisplayed = false;

		var essb_flyin_onscroll = function() {
			if (flyinTriggeredOnScroll) return;

			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;

			if (!flyinTriggerEnd) {
				if (percentage > flyinTriggerPercent && flyinTriggerPercent > 0) {
					flyinTriggeredOnScroll = true;
					essb_flyin_show();
				}
			}
			else {
				var element = $('.essb_break_scroll');
				if (!element.length) { return; }
				var top = $('.essb_break_scroll').offset().top - parseFloat($('.essb_break_scroll').css('marginTop').replace(/auto/, 0));

				if (current_pos >= top) {
					flyinTriggeredOnScroll = true;
					essb_flyin_show();
				}
			}
		}
		
		var essb_flyin_manual_show = window.essb_flyin_manual_show = function() {
			if (!$('.essb-flyin').length) return;
			
			var element = $('.essb-flyin'),
				popWidth = $(element).attr('data-width') || '',
				winWidth = $( window ).width(),
				baseWidth = 400;
			
			if (Number(popWidth) && Number(popWidth) > 0) baseWidth = Number(popWidth);
			if (winWidth < baseWidth) baseWidth = winWidth - 60;
			
			$(".essb-flyin").css( { width: baseWidth+'px'});
			$(".essb-flyin").fadeIn(400);
		}

		var essb_flyin_show = window.essb_flyin_show = function() {
			if (flyinDisplayed) return;

			var element = $('.essb-flyin');
			if (!element.length) return;

			var popWidth = $(element).attr("data-width") || "";
			var popHideOnClose = $(element).attr("data-close-hide") || "";
			var popHideOnCloseAll = $(element).attr("data-close-hide-all") || "";
			var popPostId = $(element).attr("data-postid") || "";

			var popAutoCloseAfter = $(element).attr("data-close-after") || "";

			if (popHideOnClose == "1" || popHideOnCloseAll == "1") {
				var cookie_name = "";
				var base_cookie_name = "essb_flyin_";
				if (popHideOnClose == "1") {
					cookie_name = base_cookie_name + popPostId;

					var cookieSet = essb_getCookie(cookie_name);
					if (cookieSet == "yes") return;
					essb_setCookie(cookie_name, "yes", 7);
				}
				if (popHideOnCloseAll == "1") {
					cookie_name = base_cookie_name + "all";

					var cookieSet = essb_getCookie(cookie_name);
					if (cookieSet == "yes") return;
					essb_setCookie(cookie_name, "yes", 7);
				}
			}

			var win_width = $( window ).width();
			var doc_height = $('document').height();

			var base_width = 400;
			var userwidth = popWidth;
			if (Number(userwidth) && Number(userwidth) > 0)
				base_width = userwidth;


			if (win_width < base_width) base_width = win_width - 60;

			// automatically close
			if (Number(popAutoCloseAfter) && Number(popAutoCloseAfter) > 0) {

				var optin_time = parseFloat(popAutoCloseAfter);
				optin_time = optin_time * 1000;
				setTimeout(function(){
					$(".essb-flyin").fadeOut(200);
				}, optin_time);
			}

			$(".essb-flyin").css( { width: base_width+'px'});
			$(".essb-flyin").fadeIn(400);

			flyinDisplayed = true;
		}

		var flyinTriggeredOnScroll = false;
		var flyinTriggerPercent = -1;
		var flyinTriggerEnd = false;

		if (essb_responsiveEventsCanRun($('.essb-flyin'))) {
			var element = $('.essb-flyin');
			if (essb_is_after_comment() && element.hasClass("essb-flyin-oncomment")) {
				essb_flyin_show();
				return;
			}

			var popOnPercent = $(element).attr("data-load-percent") || "";
			var popAfter = $(element).attr("data-load-time") || "";
			var popOnEnd = $(element).attr("data-load-end") || "";
			var popManual = $(element).attr("data-load-manual") || "";

			if (popManual == '1') return;

			if (popOnPercent != '' || popOnEnd == "1") {
				flyinTriggerPercent = Number(popOnPercent);
				flyinTriggeredOnScroll = false;
				flyinTriggerEnd = (popOnEnd == "1") ? true : false;

				$(window).on('scroll', debounce(essb_flyin_onscroll, 1));
			}

			if (popAfter && typeof(popAfter) != "undefined") {
				if (popAfter != '' && Number(popAfter)) {
					setTimeout(function() {
						essb_flyin_show();
					}, (Number(popAfter) * 1000));
				}
				else
					essb_flyin_show();
			}
			else {

				if (popOnPercent == '' && popOnEnd != '1')
					essb_flyin_show();
			}

		}
	}
	
});