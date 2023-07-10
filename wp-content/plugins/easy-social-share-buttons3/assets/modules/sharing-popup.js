/**
 * Popup
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
	
	var essb_popup_close = function() {
		$(".essb-popup").fadeOut(200);
		$(".essb-popup-shadow").fadeOut(400);
	};
	
	var essb_is_after_comment = function() {
		var addr = window.location.href;

		return addr.indexOf('#comment') > -1 ? true : false;
	};

	/**
	 * Display Method: Pop up
	 */

	if ($('.essb-popup').length) {

		var popupTriggeredOnScroll = false;
		var popupTriggerPercent = -1;
		var popupTriggerEnd = false;
		var popupTriggerExit = false;
		var popupShown = false;


		var essb_popup_exit = function(event) {
			if (popupTriggerExit) return;

			var e = event || window.event;

			var from = e.relatedTarget || e.toElement;

			// Reliable, works on mouse exiting window and user switching active program
			if(!from || from.nodeName === "HTML") {
				popupTriggerExit = true;
				essb_popup_show();
			}
		};

		var essb_popup_onscroll = function() {
			if (popupTriggeredOnScroll) return;

			var current_pos = $(window).scrollTop();
			var height = $(document).height() - $(window).height();
			var percentage = current_pos/height*100;

			if (!popupTriggerEnd) {
				if (percentage > popupTriggerPercent && popupTriggerPercent > 0) {
					popupTriggeredOnScroll = true;
					essb_popup_show();
				}
			}
			else {
				var element = $('.essb_break_scroll');
				if (!element.length) {
					var userTriggerPercent = 90;
					if (percentage > userTriggerPercent && userTriggerPercent > 0) {
						popupTriggeredOnScroll = true;
						essb_popup_show();
					}
				}
				else {
					var top = $('.essb_break_scroll').offset().top - parseFloat($('.essb_break_scroll').css('marginTop').replace(/auto/, 0));

					if (current_pos >= top) {
						popupTriggeredOnScroll = true;
						essb_popup_show();
					}
				}
			}
		};
		
		var essb_manual_popup_show = window.essb_manual_popup_show = function() {
			popupShown = false;			
			essb_popup_show();
		}
		
		var essb_popup_show = window.essb_popup_show = function() {

			if (popupShown) return;

			var element = $('.essb-popup');
			if (!element.length) return;

			var popWidth = $(element).attr("data-width") || "";
			var popHideOnClose = $(element).attr("data-close-hide") || "";
			var popHideOnCloseAll = $(element).attr("data-close-hide-all") || "";
			var popPostId = $(element).attr("data-postid") || "";

			var popAutoCloseAfter = $(element).attr("data-close-after") || "";

			if (popHideOnClose == "1" || popHideOnCloseAll == "1") {
				var cookie_name = "";
				var base_cookie_name = "essb_popup_";
				if (popHideOnClose == "1") {
					cookie_name = base_cookie_name + popPostId;

					var cookieSet = essb_getCookie(cookie_name);
					if (cookieSet == "yes")  return;
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

			var base_width = 800;
			var userwidth = popWidth;
			if (Number(userwidth) && Number(userwidth) > 0) {
				base_width = userwidth;
			}

			if (win_width < base_width) { base_width = win_width - 60; }

			// automatically close
			if (Number(popAutoCloseAfter) && Number(popAutoCloseAfter) > 0) {

				optin_time = Number(popAutoCloseAfter) * 1000;
				setTimeout(function(){
					essb_popup_close();
				}, optin_time);

			}

			$(".essb-popup").css( { width: base_width+'px'});
			$(".essb-popup").center();

			$(".essb-popup").fadeIn(400);
			$(".essb-popup-shadow").fadeIn(200);

			popupShown = true;
		};			

		if (essb_responsiveEventsCanRun($('.essb-popup'))) {
			var element = $('.essb-popup');
			if (essb_is_after_comment()) {
				if (element.hasClass("essb-popup-oncomment")) {
					essb_popup_show();
					return;
				}
			}
			
			var popOnPercent = $(element).attr("data-load-percent") || "";
			var popAfter = $(element).attr("data-load-time") || "";
			var popOnEnd = $(element).attr("data-load-end") || "";
			var popManual = $(element).attr("data-load-manual") || "";
			var popExit = $(element).attr("data-exit-intent") || "";

			if (popManual == '1') {
				popOnPercent = '';
				popAfter = '-1';
				popOnEnd = '';
				popExit = '';
			}

			if (popOnPercent != '' || popOnEnd == "1") {
				popupTriggerPercent = Number(popOnPercent);
				popupTriggeredOnScroll = false;
				popupTriggerEnd = (popOnEnd == "1") ? true : false;
				$(window).on('scroll', essb_popup_onscroll);
			}

			if (popExit == '1') {
				function addEvent(obj, evt, fn) {
					  if (obj.addEventListener) {
					    obj.addEventListener(evt, fn, false);
					  } else if (obj.attachEvent) {
					    obj.attachEvent("on" + evt, fn);
					  }
					}

					// Exit intent trigger
					addEvent(document, 'mouseout', function(evt) {
						evt = evt ? evt : window.event;

						// If this is an autocomplete element.
						if(evt.target.tagName.toLowerCase() == "input")
							return;

						// Get the current viewport width.
						var vpWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

						// If the current mouse X position is within 50px of the right edge
						// of the viewport, return.
						if(evt.clientX >= (vpWidth - 50))
							return;

						// If the current mouse Y position is not within 50px of the top
						// edge of the viewport, return.
						// 7.7.3 - replace 50 -> 0
						if(evt.clientY >= 0)
							return;

					  if (evt.toElement === null && evt.relatedTarget === null) {
						  essb_popup_exit();
					  }
					});
			}

			if (popAfter && typeof(popAfter) != "undefined" && popAfter != '-1') {
				if (popAfter != '' && Number(popAfter)) {
					setTimeout(function() {
						essb_popup_show();
					}, (Number(popAfter) * 1000));
				}
				else {
					essb_popup_show();
				}

			}
			else {
				if (popOnPercent == '' && popOnEnd != '1' && popExit != '1' && popAfter != '-1') {
					essb_popup_show();
				}

			}
		}

	}

	
});