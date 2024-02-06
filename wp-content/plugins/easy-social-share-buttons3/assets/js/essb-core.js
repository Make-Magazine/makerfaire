/**
 * Easy Social Share Buttons for WordPress Core Javascript
 *
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 5.0
 */

/**
 * jQuery function extension package for Easy Social Share Buttons
 */
jQuery(document).ready(function($){
	"use strict";
	
	jQuery.fn.essb_toggle_more = function(){
		return this.each(function(){
			$(this).removeClass('essb_after_more');
			$(this).addClass('essb_before_less');
		});
	};

	jQuery.fn.essb_toggle_less = function(){
		return this.each(function(){
			$(this).addClass('essb_after_more');
			$(this).removeClass('essb_before_less');
		});
	};

	jQuery.fn.extend({
		center: function () {
			return this.each(function() {
				var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
				var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
				jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
			});
		}
	});

});

(function ($) {
    $.fn.countTo = function (options) {
        options = options || {};

        return $(this).each(function () {
            // set options for current element
            var settings = $.extend({}, $.fn.countTo.defaults, {
                from: $(this).data('from'),
                to: $(this).data('to'),
                speed: $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals: $(this).data('decimals')
            }, options);

            // how many times to update the value, and how much to increment the value on each update
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
                increment = (settings.to - settings.from) / loops;

            // references & variables that will change with each update
            var self = this,
                $self = $(this),
                loopCount = 0,
                value = settings.from,
                data = $self.data('countTo') || {};

            $self.data('countTo', data);

            // if an existing interval can be found, clear it first
            if (data.interval) {
                clearInterval(data.interval);
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);

            // initialize the element with the starting value
            render(value);

            function updateTimer() {
                value += increment;
                loopCount++;

                render(value);

                if (typeof (settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value);
                }

                if (loopCount >= loops) {
                    // remove the interval
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;

                    if (typeof (settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value);
                    }
                }
            }

            function render(value) {
                var formattedValue = settings.formatter.call(self, value, settings);
                $self.text(formattedValue);
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0, // the number the element should start at
        to: 0, // the number the element should end at
        speed: 1000, // how long it should take to count between the target numbers
        refreshInterval: 100, // how often the element should be updated
        decimals: 0, // the number of decimal places to show
        formatter: formatter,  // handler for formatting the value before rendering
        onUpdate: null, // callback method for every time the element is updated
        onComplete: null       // callback method for when the element finishes updating
    };

    function formatter(value, settings) {
        return value.toFixed(settings.decimals);
    }



}(jQuery));

( function( $ ) {
	"use strict";
	
	/**
	 * Easy Social Share Buttons for WordPress
	 *
	 * @package EasySocialShareButtons
	 * @since 5.0
	 * @author appscreo
	 */
	var essb = window.essb = {};

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

	var isElementInViewport = function (el) {

	    //special bonus for those using jQuery
	    if (typeof jQuery === "function" && el instanceof jQuery) {
	        el = el[0];
	    }

	    var rect = el.getBoundingClientRect();

	    return (
	        rect.top >= 0 &&
	        rect.left >= 0 &&
	        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
	        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
	    );
	};
	
	var isVisibleSelector = function(selector) {
		var top_of_element = $(selector).offset().top;
	    var bottom_of_element = $(selector).offset().top + $(selector).outerHeight();
	    var bottom_of_screen = $(window).scrollTop() + $(window).innerHeight();
	    var top_of_screen = $(window).scrollTop();

	    if ((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)){
	        return true;
	    } else {
	        return false;
	    }
	};

	essb.add_event = function(eventID, user_function) {
		if (!essb.events) essb.events = {};
		essb.events[eventID] = user_function;
	};

	essb.trigger = function(eventID, options) {
		if (!essb.events) return;
		if (essb.events[eventID]) essb.events[eventID](options);
	};

	essb.window = function (url, service, instance, trackingOnly) {
		var element = $('.essb_'+instance),
			instance_post_id = $(element).attr('data-essb-postid') || '',
			instance_position = $(element).attr('data-essb-position') || '',
			wnd,
			isMobile = $(window).width() <= 1024 ? true : false,
			keyWin = 'essb_share_window' + (isMobile) + '-' + (Date.now()).toString();

		var w = (service == 'twitter') ? '500' : '800',
			h = (service == 'twitter') ? '300' : '500',
			left = (screen.width/2)-(Number(w)/2),
			top = (screen.height/2)-(Number(h)/2);

		if (typeof essbShareWindowURLFilter != 'undefined') 
			url = essbShareWindowURLFilter(service, url, instance_post_id, instance_position);

		if (!trackingOnly)
			wnd = window.open( url, keyWin, "height="+(service == 'twitter' ? '500' : '500')+",width="+(service == 'twitter' ? '500' : '800')+",resizable=1,scrollbars=yes,top="+top+",left="+left );


		if (typeof(essb_settings) != "undefined") {
			if (essb_settings.essb3_stats) {
				if (typeof(essb_handle_stats) != "undefined")
					essb_handle_stats(service, instance_post_id, instance);

			}

			if (essb_settings.essb3_ga)
				essb_ga_tracking(service, url, instance_position);
			
			if (essb_settings.essb3_ga_ntg && typeof(gtag) != 'undefined') {
				gtag('event', 'social share', {
				    'event_category': 'NTG social',
				    'event_label': service,
				    'non_interaction' : false
				});
			}

		}

		if (typeof(essb_settings) != 'undefined') {
			if (typeof(essb_settings.stop_postcount) == 'undefined') essb_self_postcount(service, instance_post_id);
		}

		if (typeof(essb_abtesting_logger) != "undefined")
			essb_abtesting_logger(service, instance_post_id, instance);

		if (typeof(essb_conversion_tracking) != 'undefined')
			essb_conversion_tracking(service, instance_post_id, instance);

		if (!trackingOnly)
			var pollTimer = window.setInterval(function() {
				if (wnd.closed !== false) {
					window.clearInterval(pollTimer);
					essb_smart_onclose_events(service, instance_post_id);

					if (instance_position == 'booster' && typeof(essb_booster_close_from_action) != 'undefined')
						essb_booster_close_from_action();
				}
			}, 200);

	};

	essb.share_window = function(url, custom_position, service) {
		var w = '800', h = '500', left = (screen.width/2)-(Number(w)/2), top = (screen.height/2)-(Number(h)/2);
		wnd = window.open( url, "essb_share_window", "height="+'500'+",width="+'800'+",resizable=1,scrollbars=yes,top="+top+",left="+left );

		if (typeof(essb_settings) != "undefined") {
			if (essb_settings.essb3_stats) {
				if (typeof(essb_log_stats_only) != "undefined")
					essb_log_stats_only(service, essb_settings["post_id"] || '', custom_position);

			}

			if (essb_settings.essb3_ga)
				essb_ga_tracking(service, url, custom_position);
			
			if (essb_settings.essb3_ga_ntg && gtag) {
				gtag('event', 'social share', {
				    'event_category': 'NTG social',
				    'event_label': service,
				    'non_interaction' : false
				});
			}
		}
	};

	essb.fbmessenger = function(app_id, url, saltKey) {
		var isMobile = $(window).width() <= 1024 ? true : false,
			cmd = '';

		if (isMobile) cmd = 'fb-messenger://share/?link=' + url;
		else cmd = 'https://www.facebook.com/dialog/send?app_id='+app_id+'&link='+url+'&redirect_uri=https://facebook.com';
		if (isMobile) {
			window.open(cmd, "_self");
			essb.tracking_only('', 'messenger', saltKey, true);
		}
		else {
			essb.window(cmd, 'messenger', saltKey);
		}

		return false;
	};

	essb.whatsapp = function(url, saltKey) {
		var isMobile = $(window).width() <= 1024 ? true : false,
			cmd = '';

		if (isMobile) cmd = 'whatsapp://send?text=' + url;
		else cmd = 'https://web.whatsapp.com/send?text=' + url;
		if (isMobile) {
			window.open(cmd, "_self");
			essb.tracking_only('', 'whatsapp', saltKey, true);
		}
		else {
			essb.window(cmd, 'whatsapp', saltKey);
		}

		return false;
	};
	
	essb.sms = function(url, saltKey) {
		var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
			cmd = 'sms:' + (iOS ? '&' : '?') + 'body=' + url;
		window.open(cmd, "_self");
		essb.tracking_only('', 'sms', saltKey, true);
		
		return false;
	};

	essb.tracking_only = function(url, service, instance, afterShare) {
		if (url == '')
			url = document.URL;

		essb.window(url, service, instance, true);

		var element = $('.essb_'+instance),
			instance_position = $(element).attr('data-essb-position') || '';

		if (afterShare) {			
			setTimeout(function() {			
				var instance_post_id = $('.essb_'+instance).attr('data-essb-postid') || '';
				essb_smart_onclose_events(service, instance_post_id);
	
				if (instance_position == 'booster' && typeof(essb_booster_close_from_action) != 'undefined')
					essb_booster_close_from_action();
			}, 1500);
		}
	};

	essb.pinterest_picker = function(instance) {
		essb.tracking_only('', 'pinterest', instance);
		var e=document.createElement('script');
		e.setAttribute('type','text/javascript');
		e.setAttribute('charset','UTF-8');
		e.setAttribute('src','//assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);
		document.body.appendChild(e);
	};

	essb.print = function (instance) {
		essb.tracking_only('', 'print', instance);
		window.print();
	};

	essb.setCookie = function(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toGMTString();
	    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
	};

	essb.getCookie = function(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i].trim();
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	};

	essb.loveThis = function (instance) {
		console.log(window.essb_love_you_message_thanks);
		
		if (typeof(essb_love_you_message_loved) == 'undefined') var essb_love_you_message_loved = '';
		if (typeof(essb_love_you_message_thanks) == 'undefined') var essb_love_you_message_thanks = '';
		
		if (typeof (window.essb_love_you_message_thanks) != 'undefined') essb_love_you_message_thanks = window.essb_love_you_message_thanks;
		if (typeof (window.essb_love_you_message_loved) != 'undefined') essb_love_you_message_loved = window.essb_love_you_message_loved;

		if (essb.clickedLoveThis) {
			if (!essb.loveDisableLoved) alert(essb_love_you_message_loved ? essb_love_you_message_loved : 'You already love this today');
			return;
		}
		
		var element = $('.essb_'+instance);
		if (!element.length) return;

		var instance_post_id = $(element).attr("data-essb-postid") || "";

		var cookie_set = essb.getCookie("essb_love_"+instance_post_id);
		if (cookie_set) {
			if (!essb.loveDisableLoved) alert(essb_love_you_message_loved ? essb_love_you_message_loved : 'You already love this today');
			return;
		}

		if (typeof(essb_settings) != "undefined") {
			$.post(essb_settings.ajax_url, {
				'action': 'essb_love_action',
				'post_id': instance_post_id,
				'service': 'love',
				'nonce': essb_settings.essb3_nonce
			}, function (data) { if (data) {
				if (!essb.loveDisableThanks) alert(essb_love_you_message_thanks ? essb_love_you_message_thanks : 'Thank you for loving this');
			}},'json');
		}

		essb.tracking_only('', 'love', instance, true);
	};

	essb.toggle_more = function(unique_id) {
		if (essb['is_morebutton_clicked']) {
			essb.toggle_less(unique_id);
			return;
		}
		$('.essb_'+unique_id+' .essb_after_more').essb_toggle_more();

		var moreButton = $('.essb_'+unique_id).find('.essb_link_more');
		if (typeof(moreButton) != "undefined") {
			moreButton.hide();
			moreButton.addClass('essb_hide_more_sidebar');
		}

		moreButton = $('.essb_'+unique_id).find('.essb_link_more_dots');
		if (typeof(moreButton) != "undefined") {
			moreButton.hide();
			moreButton.addClass('essb_hide_more_sidebar');
		}

		essb['is_morebutton_clicked'] = true;
	};

	essb.toggle_less = function(unique_id) {
		essb['is_morebutton_clicked'] = false;
		$('.essb_'+unique_id+' .essb_before_less').essb_toggle_less();

		var moreButton = $('.essb_'+unique_id).find('.essb_link_more');
		if (typeof(moreButton) != "undefined") {
			moreButton.show();
			moreButton.removeClass('essb_hide_more_sidebar');
		}

		moreButton = $('.essb_'+unique_id).find('.essb_link_more_dots');
		if (typeof(moreButton) != "undefined") {
			moreButton.show();
			moreButton.removeClass('essb_hide_more_sidebar');
		}
	};

	essb.toggle_more_popup = function(unique_id) {
		if (essb['essb_morepopup_opened']) {
			essb.toggle_less_popup(unique_id);
			return;
		}
		if ($(".essb_morepopup_"+unique_id).hasClass("essb_morepopup_inline")) {
			essb.toggle_more_inline(unique_id);
			return;
		}

		var is_from_mobilebutton = false, height_of_mobile_bar = 0, parentDraw = $(".essb_morepopup_"+unique_id);
		if ($(parentDraw).hasClass('essb_morepopup_sharebottom')) {
			is_from_mobilebutton = true;
			height_of_mobile_bar = $(".essb-mobile-sharebottom").outerHeight();
		}

		var win_width = $( window ).width(), win_height = $(window).height(),
			base_width = !is_from_mobilebutton ? 660 : 550,
			height_correction = is_from_mobilebutton ? 10 : 80;

		if (win_width < base_width) base_width = win_width - 30;

		var element_class = ".essb_morepopup_"+unique_id,
			element_class_shadow = ".essb_morepopup_shadow_"+unique_id,
			alignToBottom = $(element_class).hasClass("essb_morepopup_sharebottom") ? true : false;

		if ($(element_class).hasClass("essb_morepopup_modern") && !is_from_mobilebutton) height_correction = 100;

		$(element_class).css( { width: base_width+'px'});

		var element_content_class = ".essb_morepopup_content_"+unique_id;
		var popup_height = $(element_class).outerHeight();
		if (popup_height > (win_height - 30)) {
			var additional_correction = 0;
			if (is_from_mobilebutton) {
				$(element_class).css( { top: '5px'});
				additional_correction += 5;
			}
			$(element_class).css( { height: (win_height - height_of_mobile_bar - height_correction - additional_correction)+'px'});
			$(element_content_class).css( { height: (win_height - height_of_mobile_bar - additional_correction - (height_correction+50))+'px', "overflowY" :"auto"});
		}
		if (is_from_mobilebutton)
			$(element_class_shadow).css( { height: (win_height - (is_from_mobilebutton ? height_of_mobile_bar : 0))+'px'});
		
		if (!alignToBottom)
			$(element_class).center();
		else {
			var left = ($(window).width() - $(element_class).outerWidth()) / 2;
			$(element_class).css( { left: left+"px", position:'fixed', margin:0, bottom: (height_of_mobile_bar + height_correction) + "px" });
		}
		$(element_class).fadeIn(400);
		$(element_class_shadow).fadeIn(200);
		essb['essb_morepopup_opened'] = true;
	};

	essb.toggle_less_popup = function(unique_id) {
		$(".essb_morepopup_"+unique_id).fadeOut(200);
		$(".essb_morepopup_shadow_"+unique_id).fadeOut(200);
		essb['essb_morepopup_opened'] = false;
	};

	essb.toggle_more_inline = function(unique_id) {
		var buttons_element = $(".essb_"+unique_id);
		if (!buttons_element.length) return;
		var element_class = ".essb_morepopup_"+unique_id;

		var appear_y = $(buttons_element).position().top + $(buttons_element).outerHeight(true);
		var appear_x = $(buttons_element).position().left;
		var appear_position = "absolute";

		var appear_at_bottom = false;

		if ($(buttons_element).css("position") === "fixed")
			appear_position = "fixed";

		if ($(buttons_element).hasClass("essb_displayed_bottombar"))
			appear_at_bottom = true;

		if (appear_at_bottom) {
			appear_y = $(buttons_element).position().top - $(element_class).outerHeight(true);
			var pointer_element = $(element_class).find(".modal-pointer");
			if ($(pointer_element).hasClass("modal-pointer-up-left")) {
				$(pointer_element).removeClass("modal-pointer-up-left");
				$(pointer_element).addClass("modal-pointer-down-left");
			}
		}

		var more_button = $(buttons_element).find(".essb_link_more");
		if (!$(more_button).length)
		    more_button = $(buttons_element).find(".essb_link_more_dots");
		if ($(more_button).length)
			appear_x = (appear_position != "fixed") ? $(more_button).position().left - 5 : (appear_x + $(more_button).position().left - 5);

		var share_button = $(buttons_element).find(".essb_link_share");
		if ($(share_button).length)
			appear_x = (appear_position != "fixed") ? $(share_button).position().left - 5 : (appear_x + $(share_button).position().left - 5);



		$(element_class).css( { left: appear_x+"px", position: appear_position, margin:0, top: appear_y + "px" });

		$(element_class).fadeIn(200);
		essb['essb_morepopup_opened'] = true;

	};

	essb.subscribe_popup_close = function(key) {
		$('.essb-subscribe-form-' + key).fadeOut(400);
		$('.essb-subscribe-form-overlay-' + key).fadeOut(400);
	};

	essb.sharebutton = function(key) {
		if ($('.essb-windowcs-'+key).length) {

			/**
			 * @since 8.3 Custom pop-up size
			 */
			var win_width = $( window ).width(), win_height = $(window).height(),
				popup_width = $('.essb-windowcs-'+key).data('width'), popup_height = $('.essb-windowcs-'+key).data('height'),
				customPosition = false;
						
			if (Number(popup_width || 0) > 0 || Number(popup_height || 0) > 0) {
				if (Number(popup_width) > Number(win_width)) popup_width = win_width;
				if (Number(popup_height) > Number(win_height)) popup_height = win_height;
				
				if (Number(popup_width) > 0) $('.essb-windowcs-'+key).css({ 'width': popup_width + 'px'});
				if (Number(popup_height) > 0) $('.essb-windowcs-'+key).css({ 'height': popup_height + 'px'});
				
				$('.essb-windowcs-'+key).center();
				customPosition = true;
			}
			

			$('.essb-windowcs-'+key).fadeIn(200);
			if (!customPosition) $('.essb-windowcs-'+key+' .inner-content').center();
			else {
				$('.essb-windowcs-'+key+' .inner-content').css( {'position': 'absolute', 'left': '50%', 'top': '50%', 'transform': 'translate(-50%,-50%)', 'width': '90%' });
			}
		}
	};

	essb.sharebutton_close = function(key) {
		if ($('.essb-windowcs-'+key).length) {
			$('.essb-windowcs-'+key).fadeOut(200);
		}
	};

	essb.toggle_subscribe = function(key) {
		// subsribe container do not exist
		if (!$('.essb-subscribe-form-' + key).length) return;

		if (!essb['essb_subscribe_opened'])
			essb['essb_subscribe_opened'] = {};

		var asPopup = $('.essb-subscribe-form-' + key).attr("data-popup") || "";

		// it is not popup (in content methods is asPopup == "")
		if (asPopup != '1') {
			if ($('.essb-subscribe-form-' + key).hasClass("essb-subscribe-opened")) {
				$('.essb-subscribe-form-' + key).slideUp('fast');
				$('.essb-subscribe-form-' + key).removeClass("essb-subscribe-opened");
			}
			else {
				$('.essb-subscribe-form-' + key).slideDown('fast');
				$('.essb-subscribe-form-' + key).addClass("essb-subscribe-opened");

				if (!essb['essb_subscribe_opened'][key]) {
					essb['essb_subscribe_opened'][key] = key;
					essb.tracking_only('', 'subscribe', key, true);
				}
			}
		}
		else {
			var win_width = $( window ).width();
			var doc_height = $('document').height();

			var base_width = 600;

			if (win_width < base_width) { base_width = win_width - 40; }


			$('.essb-subscribe-form-' + key).css( { width: base_width+'px'});
			$('.essb-subscribe-form-' + key).center();

			$('.essb-subscribe-form-' + key).fadeIn(400);
			$('.essb-subscribe-form-overlay-' + key).fadeIn(200);

		}

	};

	essb.is_after_comment = function() {
		var addr = window.location.href;

		return addr.indexOf('#comment') > -1 ? true : false;
	};

	essb.flyin_close = function () {
		$(".essb-flyin").fadeOut(200);
	};

	essb.popup_close = function() {

		$(".essb-popup").fadeOut(200);
		$(".essb-popup-shadow").fadeOut(400);
	};
	
	essb.copy_link_direct = function(currentLocation) {
		
		if (!$('#essb_copy_link_field').length) {
			var output = [];
			output.push('<div style="display: none;"><input type="text" id="essb_copy_link_field" style="width: 100%;padding: 5px 10px;font-size: 15px;background: #f5f6f7;border: 1px solid #ccc;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;" /></div>');
			output.push('<div id="essb_copy_link_message" style="background: rgba(0,0,0,0.7); color: #fff; z-index: 1100; position: fixed; padding: 15px 25px; font-size: 13px; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;">');
			output.push(essb_settings.translate_copy_message2 ? essb_settings.translate_copy_message2 : 'Copied to clipboard.');
			output.push('</div>');
			$('body').append(output.join(''));
		}
		
		$('#essb_copy_link_field').val(currentLocation);
		$('#essb_copy_link_field').focus();
		$('#essb_copy_link_field').select();
		
		setTimeout(function() {
			var copyText = document.querySelector("#essb_copy_link_field");
			try {
				copyText.value = currentLocation;
				copyText.focus();
				copyText.select();
				copyText.setSelectionRange(0, 99999); /*For mobile devices*/
				document.execCommand("copy");
				navigator.clipboard.writeText(copyText.value);
				$('#essb_copy_link_message').center();
				$('#essb_copy_link_message').fadeIn(300);
				setTimeout(function() {
					$('#essb_copy_link_message').fadeOut();
				}, 2000);
			}
			catch (e) {
				console.log('Error link copy to clipboard!');
			}
		}, 100);
	};
	
	essb.copy_link = function(instance_id, user_href) {
		var currentLocation = window.location.href, win_width = $( window ).width();
				
		if (instance_id && $('.essb_' + instance_id).length) {
			var instance_url = $('.essb_' + instance_id).data('essb-url') || '';
			if (instance_url != '') currentLocation = instance_url;
		}
		
		if (user_href && user_href != '') currentLocation = user_href;
		
		if (essb_settings && essb_settings.copybutton_direct) {
			essb.copy_link_direct(currentLocation);
			return;
		}
		
		if (!$('.essb-copylink-window').length) {
			var output = [];
			output.push('<div class="essb_morepopup essb-copylink-window" style="z-index: 1301;">');
			output.push('<div class="essb_morepopup_header"> <span>&nbsp;</span> <a href="#" class="essb_morepopup_close"><svg style="width: 24px; height: 24px; padding: 5px;" height="32" viewBox="0 0 32 32" width="32" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M32,25.7c0,0.7-0.3,1.3-0.8,1.8l-3.7,3.7c-0.5,0.5-1.1,0.8-1.9,0.8c-0.7,0-1.3-0.3-1.8-0.8L16,23.3l-7.9,7.9C7.6,31.7,7,32,6.3,32c-0.8,0-1.4-0.3-1.9-0.8l-3.7-3.7C0.3,27.1,0,26.4,0,25.7c0-0.8,0.3-1.3,0.8-1.9L8.7,16L0.8,8C0.3,7.6,0,6.9,0,6.3c0-0.8,0.3-1.3,0.8-1.9l3.7-3.6C4.9,0.2,5.6,0,6.3,0C7,0,7.6,0.2,8.1,0.8L16,8.7l7.9-7.9C24.4,0.2,25,0,25.7,0c0.8,0,1.4,0.2,1.9,0.8l3.7,3.6C31.7,4.9,32,5.5,32,6.3c0,0.7-0.3,1.3-0.8,1.8L23.3,16l7.9,7.9C31.7,24.4,32,25,32,25.7z"/></svg></a> </div>');
			output.push('<div class="essb_morepopup_content">');
			output.push('<div class="essb_copy_internal" style="display: flex; align-items: center;">');
			output.push('<div style="width: calc(100% - 50px); padding: 5px;"><input type="text" id="essb_copy_link_field" style="width: 100%;padding: 5px 10px;font-size: 15px;background: #f5f6f7;border: 1px solid #ccc;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;" /></div>');
			output.push('<div style="width:50px;text-align: center;"><a href="#" class="essb-copy-link" title="'+ (essb_settings.translate_copy_message1 ? essb_settings.translate_copy_message1 : 'Press to copy the link')+'" style="color:#5867dd;background:#fff;padding:10px;text-decoration: none;"><svg style="width: 24px; height: 24px; fill: currentColor;" class="essb-svg-icon" aria-hidden="true" role="img" focusable="false" width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M25.313 28v-18.688h-14.625v18.688h14.625zM25.313 6.688c1.438 0 2.688 1.188 2.688 2.625v18.688c0 1.438-1.25 2.688-2.688 2.688h-14.625c-1.438 0-2.688-1.25-2.688-2.688v-18.688c0-1.438 1.25-2.625 2.688-2.625h14.625zM21.313 1.313v2.688h-16v18.688h-2.625v-18.688c0-1.438 1.188-2.688 2.625-2.688h16z"></path></svg></a></div>');
			output.push('</div>');
			output.push('<div class="essb-copy-message" style="font-size: 13px; font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;"></div>');
			output.push('</div>');
			output.push('</div>');
			
			output.push('<div class="essb_morepopup_shadow essb-copylink-shadow" style="z-index: 1300;"></div>');
			$('body').append(output.join(''));
			
			$('.essb-copylink-window .essb_morepopup_close').on('click', function(e) {
				e.preventDefault();
				
				$('.essb-copylink-window').fadeOut(300);
				$('.essb-copylink-shadow').fadeOut(200);
			});
			
			$('.essb-copylink-window .essb-copy-link').on('click', function(e) {
				e.preventDefault();
				var copyText = document.querySelector("#essb_copy_link_field");
				try {
					copyText.select();
					copyText.setSelectionRange(0, 99999); /*For mobile devices*/
					document.execCommand("copy");
					navigator.clipboard.writeText(copyText.value);
					$('.essb-copylink-window .essb_morepopup_header span').html(essb_settings.translate_copy_message2 ? essb_settings.translate_copy_message2 : 'Copied to clipboard.');
					setTimeout(function() {
						$('.essb-copylink-window .essb_morepopup_header span').html('&nbsp;');
					}, 2000);
				}
				catch (e) {
					$('.essb-copylink-window .essb_morepopup_header span').html(essb_settings.translate_copy_message3 ? essb_settings.translate_copy_message3 : 'Please use Ctrl/Cmd+C to copy the URL.');
					setTimeout(function() {
						$('.essb-copylink-window .essb_morepopup_header span').html('&nbsp;');
					}, 2000);
				}
			});
		}
		
		$('.essb-copy-message').html('');
		$('.essb-copylink-window').css({'width': (win_width > 600 ? 600 : win_width - 50) + 'px'});
		$('.essb-copylink-window').center();
		$('.essb-copylink-window').fadeIn(300);
		$('.essb-copylink-shadow').fadeIn(200);
		
		$('#essb_copy_link_field').val(currentLocation);
		$('#essb_copy_link_field').focus();
		$('#essb_copy_link_field').select();
	};

	/**
	 * Mobile Display Code
	 */

	essb.mobile_sharebar_open = function() {
		var element = $('.essb-mobile-sharebar-window');
		if (!element.length) return;
		
		var sharebar_element = $('.essb-mobile-sharebar');
		if (!sharebar_element.length)
			sharebar_element = $('.essb-mobile-sharepoint');

		if (!sharebar_element.length)
			return;


		if (essb['is_displayed_sharebar']) {
			essb.mobile_sharebar_close();
			return;
		}

		var current_height_of_bar = $(sharebar_element).outerHeight();
		var win_height = $(window).height();
		var win_width = $(window).width();
		win_height -= current_height_of_bar;

		if ($('#wpadminbar').length)
			$("#wpadminbar").hide();


		var element_inner = $('.essb-mobile-sharebar-window-content');
		if (element_inner.length) {
			element_inner.css({
				height : (win_height - 60) + 'px'
			});
		}

		$(element).css({
			width : win_width + 'px',
			height : win_height + 'px'
		});
		$(element).fadeIn(400);
		essb['is_displayed_sharebar'] = true;
	};

	essb.mobile_sharebar_close = function() {
		var element = $('.essb-mobile-sharebar-window');
		if (!element.length)
			return;


		$(element).fadeOut(400);
		essb['is_displayed_sharebar'] = false;
	};

	essb.responsiveEventsCanRun = function(element) {
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

	window.essb = essb;
	
	/**
	 * Incore Specific Functions & Events
	 */
	
	var essb_int_value = function(value) {
		value = parseInt(value);
		
		if (isNaN(value) || !isFinite(value)) value = 0;
		return value;
	}

	var essb_self_postcount = function (service, countID) {
		if (typeof(essb_settings) != "undefined") {
			countID = String(countID);

			$.post(essb_settings.ajax_url, {
				'action': 'essb_self_postcount',
				'post_id': countID,
				'service': service,
				'nonce': essb_settings.essb3_nonce
			}, function (data) { },'json');
		}
	};

	var essb_smart_onclose_events = function (service, postID) {	
		// 7.0 - trigger only on selected networks (when such are present)
		if (essb_settings && essb_settings['aftershare_networks']) {
			var workingNetworks = essb_settings['aftershare_networks'] != '' ? essb_settings['aftershare_networks'].split(',') : [];
			if (workingNetworks.indexOf(service) == -1) return;
		}
		else {
		// 6.0.3 - adding email & mail as also ignoring options
			if (service == "subscribe" || service == "comments" || service == 'email' || service == 'mail') return;
		}
		
		if (service == "subscribe" || service == "comments" || service == 'email' || service == 'mail') return;
		
		if (typeof (essbasc_popup_show) == 'function')
				essbasc_popup_show();

		if (typeof essb_acs_code == 'function')
			essb_acs_code(service, postID);

		if ($('.essb-aftershare-subscribe-form').length) {
			var key = $('.essb-aftershare-subscribe-form').data('salt') || '';
			if (key != '') essb.toggle_subscribe(key);
		}
	};


	var essb_ga_tracking = function(service, url, position) {
		var essb_ga_type = essb_settings.essb3_ga_mode;

		if ( 'ga' in window && window.ga !== undefined && typeof window.ga === 'function' ) {
			if (essb_ga_type == "extended")
				ga('send', 'event', 'social', service + ' ' + position, url);

			else
				ga('send', 'event', 'social', service, url);

		}

		if (essb_ga_type == "layers" && typeof(dataLayer) != "undefined") {
			dataLayer.push({
			  'service': service,
			  'position': position,
			  'url': url,
			  'event': 'social'
			});
		}
	};
	
	var essb_open_mailform = window.essb_open_mailform = function(unique_id) {
		if (essb['essb_mailform_opened']) {
			essb_close_mailform(unique_id);
			return;
		}
		
		var sender_element = $(".essb_"+unique_id);
		if (!sender_element.length) return;
		
		var sender_post_id = $(sender_element).attr("data-essb-postid") || "";
		
		$("#essb_mail_instance").val(unique_id);
		$("#essb_mail_post").val(sender_post_id);
		
		var win_width = $( window ).width(),
			win_height = $(window).height(),
			base_width = 400;
		
		if (win_width < base_width) base_width = win_width - 30;
		
		var height_correction = 20,
			element_class = ".essb_mailform",
			element_class_shadow = ".essb_mailform_shadow";
			
		$(element_class).css( { width: base_width+'px'});
			
		var popup_height = $(element_class).outerHeight();
		if (popup_height > (win_height - 30)) {		
			$(element_class).css( { height: (win_height - height_correction)+'px'});
		}
		
		$("#essb_mailform_from").val("");
		$("#essb_mailform_to").val("");
		$('#essb_mailform_from_name').val('');
		if ($("#essb_mailform_c").length)
			$("#essb_mailform_c").val("");
		
		// Maybe load reCAPTCHA.
		if ( typeof(essb_recaptcha) != 'undefined' && essb_recaptcha && essb_recaptcha.recaptchaSitekey ) {
			grecaptcha.render( 'essb-modal-recaptcha', {
				sitekey:  essb_recaptcha.recaptchaSitekey
			} );
		}
			
		$(element_class).center();
		$(element_class).slideDown(200);
		$(element_class_shadow).fadeIn(200);
		$('#essb_mailform_status_message').html('');
		essb['essb_mailform_opened'] = true;
		essb.tracking_only("", "mail", unique_id);
	};
	
	var essb_close_mailform = window.essb_close_mailform = function() {
		$(".essb_mailform").fadeOut(200);
		$(".essb_mailform_shadow").fadeOut(200);
		$('#essb_mailform_status_message').html('');
		essb['essb_mailform_opened'] = false;
	};
	
	var essb_mailform_send = window.essb_mailform_send = function() {
		var highlight = function(id) {
			$(id).css('background-color', '#ffd8d8');
			setTimeout(function() {
				$(id).css('background-color', '#fff');
			}, 5000);
		};
		
		$('#essb_mailform_status_message').html('');
		$('.essb_mailform_content_buttons').css('visibility', 'visible');
		
		var sender_email = $("#essb_mailform_from").val(),
			sender_name = $('#essb_mailform_from_name').val(),
			recepient_email = $("#essb_mailform_to").val(),
			captcha_validate = $("#essb_mailform_c").length ? true : false,
			errorMessage = $('.essb_mailform').attr('data-error') || '',
			sendingMessage = $('.essb_mailform').attr('data-sending') || '',
			captcha = captcha_validate ? $("#essb_mailform_c").val() : "",
			recaptcha  = $( '#g-recaptcha-response' ).val(),
			sender_aff_id = $('#essb_mail_affiliate_id').length ? $('#essb_mail_affiliate_id').val() : '',
			custom_message = '';
		
		if (sender_name == '' || sender_email == "" || recepient_email == "" || (captcha == "" && captcha_validate)) {
			
			if (sender_name == '') highlight("#essb_mailform_from_name");
			if (sender_email == '') highlight("#essb_mailform_from");
			if (recepient_email == '') highlight("#essb_mailform_to");
			$('#essb_mailform_status_message').html('<span style="color: #d80001;">' + errorMessage + '</span>');			
			return;
		}
		
		var mail_salt = $("#essb_mail_salt").val(),
			instance_post_id = $("#essb_mail_post").val();
		
		if (typeof(essb_settings) != "undefined") {
			// hiding buttons before sending of the message
			$('.essb_mailform_content_buttons').css('visibility', 'hidden');
			$('#essb_mailform_status_message').html(sendingMessage + ' ' + recepient_email);
			
			$.post(essb_settings.ajax_url, {
				"action": "essb_mail_action",
				"post_id": instance_post_id,
				"from": sender_email,
				"from_name": sender_name,
				"to": recepient_email,
				"c": captcha,
				"cu": custom_message,
				"salt": mail_salt,
				'affid': sender_aff_id,
				'recapcha': recaptcha,
				"nonce": essb_settings.essb3_nonce
				}, function (data) { if (data) {
					$('.essb_mailform_content_buttons').css('visibility', 'visible');
					if (data['message']) $('#essb_mailform_status_message').html('<span style="color: #d80001;">' + data['message'] + '</span>');
					if (data["code"] == "1") { 
						essb_close_mailform(); 						
						alert(data['message']);
					}
					if (data['code']) {
						if (data['code'] == '101' && $("#essb_mailform_c").length) highlight("#essb_mailform_c");
						if (data['code'] == '102' && $("#essb_mailform_to").length) highlight("#essb_mailform_to");
						if (data['code'] == '104' && $("#essb_mailform_from").length) highlight("#essb_mailform_from");
					}
			}}, 'json');
		}
	};
	
	/** 
	 * After Share Events functions
	 */
	var essbasc_popup_show = window.essbasc_popup_show = function() {
		if (!$('.essbasc-popup').length) return;
		if (essb.getCookie('essb_aftershare')) return; // cookie already set for visible events
		
		var cookie_len = (typeof(essbasc_cookie_live) != "undefined") ? essbasc_cookie_live : 7;
		if (parseInt(cookie_len) == 0) { cookie_len = 7; }
		
		var win_width = $( window ).width(), base_width = 800, 
			userwidth = $('.essbasc-popup').attr("data-popup-width") || '',
			singleShow = $('.essbasc-popup').attr("data-single") || '';
		
		if (Number(userwidth) && Number(userwidth) > 0) base_width = userwidth;
		if (win_width < base_width) base_width = win_width - 60; 	
		
		$(".essbasc-popup").css( { width: base_width+'px'});
		$(".essbasc-popup").center();
		$(".essbasc-popup").fadeIn(300);		
		$(".essbasc-popup-shadow").fadeIn(100);
		
		if (singleShow == 'true') essb.setCookie('essb_aftershare', "yes", cookie_len);
	};
	
	var essbasc_popup_close = window.essbasc_popup_close = function () {		
		$(".essbasc-popup").fadeOut(200);		
		$(".essbasc-popup-shadow").fadeOut(100);
	};	

	$(document).ready(function(){

		/**
		 * Mobile Share Bar
		 */

		var mobileHideOnScroll = false;
		var mobileHideTriggerPercent = 90;
		var mobileAppearOnScroll = false;
		var mobileAppearOnScrollPercent = 0;
		var mobileAdBarConnected = false;

		var essb_mobile_sharebuttons_onscroll = function() {

			var current_pos = $(window).scrollTop();
			var height = $(document).height() - $(window).height();
			var percentage = current_pos / height * 100;

			var isVisible = true;
			if (mobileAppearOnScroll && !mobileHideOnScroll) {
				if (percentage < mobileAppearOnScrollPercent) isVisible = false;
			}
			if (mobileHideOnScroll && !mobileAppearOnScroll) {
				if (percentage > mobileHideTriggerPercent) isVisible = false;
			}
			if (mobileAppearOnScroll && mobileHideOnScroll) {
				if (percentage > mobileHideTriggerPercent || percentage < mobileAppearOnScrollPercent) isVisible = false;

			}

			if (!isVisible) {
				if (!$('.essb-mobile-sharebottom').hasClass("essb-mobile-break")) {
					$('.essb-mobile-sharebottom').addClass("essb-mobile-break");
					$('.essb-mobile-sharebottom').fadeOut(400);
				}

				if ($('.essb-adholder-bottom').length && mobileAdBarConnected) {
					if (!$('.essb-adholder-bottom').hasClass("essb-mobile-break")) {
						$('.essb-adholder-bottom').addClass("essb-mobile-break");
						$('.essb-adholder-bottom').fadeOut(400);
					}
				}

			} else {
				if ($('.essb-mobile-sharebottom').hasClass("essb-mobile-break")) {
					$('.essb-mobile-sharebottom').removeClass("essb-mobile-break");
					$('.essb-mobile-sharebottom').fadeIn(400);
				}

				if ($('.essb-adholder-bottom').length && mobileAdBarConnected) {
					if ($('.essb-adholder-bottom').hasClass("essb-mobile-break")) {
						$('.essb-adholder-bottom').removeClass("essb-mobile-break");
						$('.essb-adholder-bottom').fadeIn(400);
					}
				}
			}
		};

		if ($('.essb-mobile-sharebottom').length) {

			var hide_on_end = $('.essb-mobile-sharebottom').attr('data-hideend');
			var hide_on_end_user = $('.essb-mobile-sharebottom').attr('data-hideend-percent');
			var appear_on_scroll = $('.essb-mobile-sharebottom').attr('data-show-percent') || '';
			var check_responsive = $('.essb-mobile-sharebottom').attr('data-responsive') || '';

			if (Number(appear_on_scroll)) {
				mobileAppearOnScroll = true;
			    mobileAppearOnScrollPercent = Number(appear_on_scroll);
			}

			if (hide_on_end == 'true') mobileHideOnScroll = true;

			var instance_mobile = false;
			if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
				instance_mobile = true;
			}

			if ($('.essb-adholder-bottom').length) {
				$adbar_connected = $('.essb-adholder-bottom').attr('data-connected') || '';
				if ($adbar_connected == 'true') mobileAdBarConnected = true;
			}

			if (mobileHideOnScroll || mobileAppearOnScroll) {
				if (parseInt(hide_on_end_user) > 0)
					mobileHideTriggerPercent = parseInt(hide_on_end_user);

				if (check_responsive == '' || (check_responsive == 'true' && instance_mobile))
					$(window).on('scroll', debounce(essb_mobile_sharebuttons_onscroll, 1));

			}
		}


		/**
		 * Display Methods: Float on top
		 */
		if ($('.essb_displayed_float').length) {

			var floatingTop = $('.essb_displayed_float').offset().top - parseFloat($('.essb_displayed_float').css('marginTop').replace(/auto/, 0)),
				basicElementWidth = '',
				hide_float_percent = $('.essb_displayed_float').data('float-hide') || '',
				custom_top_postion = $('.essb_displayed_float').data('float-top') || '',
				hide_float_active = false;

			if (hide_float_percent != '' && Number(hide_float_percent) > 0) {
				hide_float_percent = parseInt(hide_float_percent);
				hide_float_active = true;
			}

			var active_custom_top = false;
			if (custom_top_postion != '' && Number(custom_top_postion) > 0) {
				custom_top_postion = parseInt(custom_top_postion);
				active_custom_top = true;
			}

			/**
			 * Hold down scroll event for floating top display method when such is present
			 * inside code
			 */
			function essbFloatingButtons() {
				var y = $(window).scrollTop();

				if (active_custom_top)
					y -= custom_top_postion;

				var height = $(document).height() - $(window).height();
				var percentage = y/height*100;
				// whether that's below the form
				if (y >= floatingTop) {
					// if so, ad the fixed class
					if (basicElementWidth == '') {
						var widthOfContainer = $('.essb_displayed_float').width();
						basicElementWidth = widthOfContainer;
						$('.essb_displayed_float').width(widthOfContainer);
					}

					$('.essb_displayed_float').addClass('essb_fixed');

				} else {
					// otherwise remove it
					$('.essb_displayed_float').removeClass('essb_fixed');
					if (basicElementWidth != '') {
						$('.essb_displayed_float').width(basicElementWidth);
					}
				}

				if (hide_float_active) {
					if (percentage >= hide_float_percent && !$('.essb_displayed_float').hasClass('hidden-float')) {
						$('.essb_displayed_float').addClass('hidden-float');
						$('.essb_displayed_float').fadeOut(100);
						return;
					}
					if (percentage < hide_float_percent && $('.essb_displayed_float').hasClass('hidden-float')) {
						$('.essb_displayed_float').removeClass('hidden-float');
						$('.essb_displayed_float').fadeIn(100);
						return;
					}
				}
			} // end: essbFloatingButtons

			if (essb.responsiveEventsCanRun($('.essb_displayed_float')))
				$(window).on('scroll', debounce(essbFloatingButtons, 1));
		}

		/**
		 * Display Methods: Sidebar
		 */

		// Sidebar animation reveal on load
		if ($('.essb_sidebar_transition').length) {
			$('.essb_sidebar_transition').each(function() {

				if (!essb.responsiveEventsCanRun($(this))) return;
				
				if ($(this).hasClass('essb_sidebar_transition_slide'))
					$(this).toggleClass('essb_sidebar_transition_slide');
	
				if ($(this).hasClass('essb_sidebar_transition_fade'))
					$(this).toggleClass('essb_sidebar_transition_fade');
		

			});
		}
		
		/**
		 * Reposition sidebar at the middle of page
		 */
		if ($('.essb_sidebar_location_middle').length) {
			var essbSidebarRepositionMiddle = function() {
				var heightOfSidebar = $('.essb_sidebar_location_middle').outerHeight(),
					winHeight = $(window).height(), top = 0;
				

				if (heightOfSidebar > winHeight) top = 0;
				else {
					top = Math.round((winHeight - heightOfSidebar) / 2);
				}				
				$('.essb_sidebar_location_middle').css({'top': top + 'px', 'opacity': '1'});
			};
			
			essbSidebarRepositionMiddle();
			$(window).on('resize', debounce(essbSidebarRepositionMiddle, 1));
		}

		// Sidebar close button
		$(".essb_link_sidebar-close a").each(function() {
			$(this).on('click', function(event) {
				event.preventDefault();
				var links_list = $(this).parent().parent().get(0);

				if (!$(links_list).length) return;

				$(links_list).find(".essb_item").each(function(){
					if (!$(this).hasClass("essb_link_sidebar-close"))
						$(this).toggleClass("essb-sidebar-closed-item");
					else
						$(this).toggleClass("essb-sidebar-closed-clicked");
				});

			});
		});

		var essb_sidebar_onscroll = function () {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;


			var element;
			if ($(".essb_displayed_sidebar").length)
				element = $(".essb_displayed_sidebar");

			if ($(".essb_displayed_sidebar_right").length)
				element = $(".essb_displayed_sidebar_right");


			if (!element || typeof(element) == "undefined") return;
			
			var value_disappear = essb_int_value($(element).data('sidebar-disappear-pos') || '');
			var value_appear = essb_int_value($(element).data('sidebar-appear-pos') || '');
			var value_appear_unit = $(element).data('sidebar-appear-unit') || '';
			var value_contenthidden = $(element).data('sidebar-contenthidden') || '';
			if (value_appear_unit == 'px') percentage = current_pos;
			
			var visibleByDisplayCond = true;
			
			if (value_appear > 0 || value_disappear > 0) {
				visibleByDisplayCond = false;
				if (value_appear && percentage >= value_appear) visibleByDisplayCond = true;
				if (value_disappear && percentage <= value_disappear) visibleByDisplayCond = true;
			}
			
			// Hiding share buttons when content is visible
			if (value_contenthidden == 'yes' && ($('.essb_displayed_top').length || $('.essb_displayed_bottom').length)) {
				
				if (($('.essb_displayed_top').length && isVisibleSelector($('.essb_displayed_top'))) ||
						($('.essb_displayed_bottom').length && isVisibleSelector($('.essb_displayed_bottom'))))
					element.fadeOut(100);
				else {
					if (visibleByDisplayCond) element.fadeIn(100);
				}
			}
			
			if (value_appear > 0 && value_disappear == 0) {
				if (percentage >= value_appear && !element.hasClass("active-sidebar")) {
					element.fadeIn(100);
					element.addClass("active-sidebar");
					return;
				}

				if (percentage < value_appear && element.hasClass("active-sidebar")) {
					element.fadeOut(100);
					element.removeClass("active-sidebar");
					return;
				}
			}

			if (value_disappear > 0 && value_appear == 0) {
				if (percentage >= value_disappear && !element.hasClass("hidden-sidebar")) {
					element.fadeOut(100);
					element.addClass("hidden-sidebar");
					return;
				}

				if (percentage < value_disappear && element.hasClass("hidden-sidebar")) {
					element.fadeIn(100);
					element.removeClass("hidden-sidebar");
					return;
				}
			}

			if (value_appear > 0 && value_disappear > 0) {
				if (percentage >= value_appear && percentage < value_disappear && !element.hasClass("active-sidebar")) {
					element.fadeIn(100);
					element.addClass("active-sidebar");
					return;
				}

				if ((percentage < value_appear || percentage >= value_disappear) && element.hasClass("active-sidebar")) {
					element.fadeOut(100);
					element.removeClass("active-sidebar");
					return;
				}
			}
		};

		if (essb.responsiveEventsCanRun($('.essb_displayed_sidebar'))) {
			var essbSidebarContentHidden = $('.essb_displayed_sidebar').data('sidebar-contenthidden') || '',
				essbSidebarAppearPos = $('.essb_displayed_sidebar').data('sidebar-appear-pos') || '',
				essbSidebarDisappearPos = $('.essb_displayed_sidebar').data('sidebar-disappear-pos') || '';
	 		if (essbSidebarAppearPos != '' || essbSidebarDisappearPos != '' || essbSidebarContentHidden == 'yes') {
				if ($( window ).width() > 800) {
					$(window).on('scroll', debounce(essb_sidebar_onscroll, 1));
					essb_sidebar_onscroll();
				}
			}
		}

		/**
		 * Display Method: Post Vertical Float
		 */

		if ($('.essb_displayed_postfloat').length) {
			var top = $('.essb_displayed_postfloat').offset().top - parseFloat($('.essb_displayed_postfloat').css('marginTop').replace(/auto/, 0));
			var postfloat_always_onscreen = ($('.essb_displayed_postfloat').data('postfloat-stay') || '').toString() == 'true' ? true : false;
			var postfloat_fix_bottom = ($('.essb_displayed_postfloat').data('postfloat-fixbottom') || '').toString() == 'true' ? true : false;
			var custom_user_top = $('.essb_displayed_postfloat').data('postfloat-top') || '';
			var postFloatVisibleSelectors = $('.essb_displayed_postfloat').data('postfloat-selectors') || '',
				postFloatViewportCheck = [],
				postFloatPercentAppear = $('.essb_displayed_postfloat').data('postfloat-percent') || '';
			
			if (!Number(postFloatPercentAppear) || Number(postFloatPercentAppear) == 0) {
				postFloatPercentAppear = '';
				$('.essb_displayed_postfloat').attr('data-postfloat-percent', '');
			}
			
			if (postFloatVisibleSelectors != '') {
				postFloatViewportCheck = postFloatVisibleSelectors.split(',');
				for (var i=0;i<postFloatViewportCheck.length;i++) {
					if ($(postFloatViewportCheck[i]).length) $(postFloatViewportCheck[i]).addClass('essb-postfloat-monitor');
				}
			}
			
			setTimeout(function() {
				$('.essb_displayed_postfloat').css({'transition' : 'all 0.3s linear'});
				if (postFloatPercentAppear == '') $('.essb_displayed_postfloat').css({'opacity' : '1'});
			}, 100);
			
			if (custom_user_top != '' && Number(custom_user_top) && !isNaN(custom_user_top)) top -= parseInt(custom_user_top);
			
			function essbPostVerticalFloatScroll() {
				var y = $(this).scrollTop(),
					break_top = 0, 
					postFloatPercentAppear = $('.essb_displayed_postfloat').data('postfloat-percent') || '',
					postFloatAppearMeasure = $('.essb_displayed_postfloat').data('postfloat-percent-m') || '';
				
				if ($('.essb_break_scroll').length) {
					var break_position = $('.essb_break_scroll').position();
					break_top = break_position.top;
					var hasCustomBreak = $('.essb_displayed_postfloat').data('postfloat-bottom') || '';
					
					if (hasCustomBreak && hasCustomBreak != '' && Number(hasCustomBreak) != 0) break_top = Number(break_top) - Number(hasCustomBreak);
				}
				
				if (postFloatPercentAppear != '') {
					var height = $(document).height()-$(window).height(),
						percentage = y/height*100,
						shouldBeVisible = postFloatAppearMeasure == 'px' ? y >= Number(postFloatPercentAppear) : percentage >= Number(postFloatPercentAppear);
					
					if (shouldBeVisible) {
						$('.essb_displayed_postfloat').css({'opacity' : '1'});
						$('.essb_displayed_postfloat').css({'transform' : 'translateY(0)'});
					}
					else {
						$('.essb_displayed_postfloat').css({'opacity' : '0'});
						$('.essb_displayed_postfloat').css({'transform' : 'translateY(50px)'});
					}
				}

				if (y >= top) {
					$('.essb_displayed_postfloat').addClass('essb_postfloat_fixed');

					var element_position = $('.essb_displayed_postfloat').offset();
					var element_height = $('.essb_displayed_postfloat').outerHeight();
					var element_top = parseInt(element_position.top) + parseInt(element_height);

					if (!postfloat_always_onscreen) {
						if (element_top > break_top) {
							if (!$('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
								$('.essb_displayed_postfloat').addClass("essb_postfloat_breakscroll");
							}
						}
						else {
							if ($('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
								$('.essb_displayed_postfloat').removeClass("essb_postfloat_breakscroll");
							}
						}
					}
					else {
						var isOneVisible = false;
						$('.essb-postfloat-monitor').each(function() {
							if (isVisibleSelector($(this)))
								isOneVisible = true;
						});

						if (!isOneVisible) {
							if ($('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
								$('.essb_displayed_postfloat').removeClass("essb_postfloat_breakscroll");
							}
							
							/**
							 * Fix the postfloat at the bottom of content
							 */
							if (postfloat_fix_bottom) {								
								if (element_top > break_top) {
									if (!$('.essb_displayed_postfloat').hasClass('essb_postfloat_absolute')) {
										$('.essb_displayed_postfloat').removeClass('essb_postfloat_fixed');
										$('.essb_displayed_postfloat').attr('data-unfixed', element_top);
										$('.essb_displayed_postfloat').addClass('essb_postfloat_absolute');
										$('.essb_displayed_postfloat').css({ 'position': 'absolute', 'top': ($('.essb_break_scroll').position().top - element_height - 100) + 'px'});
									}
								}
								else {
									if ($('.essb_displayed_postfloat').hasClass('essb_postfloat_absolute')) {
										$('.essb_displayed_postfloat').removeClass('essb_postfloat_absolute');
										$('.essb_displayed_postfloat').removeAttr('data-unfixed');
										$('.essb_displayed_postfloat').css({ 'position': '', 'top': '' });
										$('.essb_displayed_postfloat').addClass('essb_postfloat_fixed');
									}
								}
							}
						}
						else {
							if (!$('.essb_displayed_postfloat').hasClass("essb_postfloat_breakscroll")) {
								$('.essb_displayed_postfloat').addClass("essb_postfloat_breakscroll");
							}
						}
					}
				}
				else
			      // otherwise remove it
			      $('.essb_displayed_postfloat').removeClass('essb_postfloat_fixed');

			}

			if (essb.responsiveEventsCanRun($('.essb_displayed_postfloat')))
				$(window).on('scroll', debounce(essbPostVerticalFloatScroll, 1));
		}


		

		/**
		 * Display Method: Post Vertical Float
		 */
		function essb_postfloat_onscroll() {
			var current_pos = $(window).scrollTop();
			var height = $(document).height()-$(window).height();
			var percentage = current_pos/height*100;

			var element;
			if ($(".essb_displayed_postfloat").length)
				element = $(".essb_displayed_postfloat");

			if (!element || typeof(element) == "undefined") { return; }
			var value_appear = essb_int_value($(element).data('postfloat-percent') || '');

			if (value_appear > 0 ) {
				if (percentage >= value_appear && !element.hasClass("essb_active_postfloat")) {
					element.addClass("essb_active_postfloat");
					return;
				}

				if (percentage < value_appear && element.hasClass("essb_active_postfloat")) {
					element.removeClass("essb_active_postfloat");
					return;
				}
			}
		}

		if (essb.responsiveEventsCanRun($('.essb_displayed_postfloat'))) {
			if ((essb_settings.postfloat_percent || '') != '' && $(".essb_displayed_postfloat").length)
				$(window).on('scroll', debounce(essb_postfloat_onscroll, 1));
		}

		/**
		 * Animated Counters Code
		 */
		$(".essb_counters .essb_animated").each(function() {
			var current_counter = $(this).attr("data-cnt") || "";
			var current_counter_result = $(this).attr("data-cnt-short") || "";

			if ($(this).hasClass("essb_counter_hidden")) return;

			$(this).countTo({
				from: 1,
				to: current_counter,
				speed: 500,
				onComplete: function (value) {
 					$(this).html(current_counter_result);
				}
			});
		});

		/**
		 *  Display Method: Follow Me
		 */

		if ($('.essb-followme').length) {
			if ($('.essb-followme .essb_links').length) $('.essb-followme .essb_links').removeClass('essb_displayed_followme');

			var dataPosition = $('.essb-followme').attr('data-position') || '',
				dataCustomTop = $('.essb-followme').attr('data-top') || '',
				dataBackground = $('.essb-followme').attr('data-background') || '',
				dataFull = $('.essb-followme').attr('data-full') || '',
				dataAvoidLeftMargin = $('.essb-followme').attr('data-avoid-left') || '',
				dataFollowmeHide = $('.essb-followme').attr('data-hide') || '';
						

			if (dataPosition == 'top' && dataCustomTop != '')
				$('.essb-followme').css({'top': dataCustomTop+'px'});
			if (dataBackground != '')
				$('.essb-followme').css({ 'background-color': dataBackground});

			if (dataFull != '1' && dataPosition != 'left') {
				var basicWidth = $('.essb_displayed_followme').width();
				var leftPosition = $('.essb_displayed_followme').position().left;

				if (dataAvoidLeftMargin != 'true')
					$('.essb-followme .essb_links').attr('style', 'width:'+ basicWidth+'px; margin-left:'+leftPosition+'px !important;');
				else
					$('.essb-followme .essb_links').attr('style', 'width:'+ basicWidth+'px;');
			}

			function essb_followme_scroll() {
				var isOneVisible = false,
					dataFollowmeShowAfter = $('.essb-followme').attr('data-showafter') || '';

				if (dataFollowmeShowAfter != '' && !Number(dataFollowmeShowAfter)) dataFollowmeShowAfter = '';
				
				$('.essb_displayed_followme').each(function() {
					if (isElementInViewport($(this)))
						isOneVisible = true;
				});

				var current_pos = $(window).scrollTop();
				var height = $(document).height() - $(window).height();
				var percentage = current_pos / height * 100;
				
				if (Number(dataFollowmeShowAfter) > 0 && Number(dataFollowmeShowAfter) > current_pos) isOneVisible = true;

				if (!isOneVisible) {
					if (!$('.essb-followme').hasClass('active')) $('.essb-followme').addClass('active');
				}
				else {
					if ($('.essb-followme').hasClass('active')) $('.essb-followme').removeClass('active');
				}

				if (dataFollowmeHide != '') {
					if (percentage > 95) {
						if (!$('.essb-followme').hasClass('essb-followme-hiddenend')) {
							$('.essb-followme').addClass('essb-followme-hiddenend');
							$('.essb-followme').slideUp(100);
						}
					}
					else {
						if ($('.essb-followme').hasClass('essb-followme-hiddenend')) {
							$('.essb-followme').removeClass('essb-followme-hiddenend');
							$('.essb-followme').slideDown(100);
						}
					}
				}
			}

			$(window).on('scroll', debounce(essb_followme_scroll, 1));

			// execute one time after load
			essb_followme_scroll();
		}

		if ($('.essb-point').length) {
			var essb_point_triggered = false;
			var essb_point_trigger_mode = "";

			var essb_point_trigger_open_onscroll = function() {
				var current_pos = $(window).scrollTop() + $(window).height() - 200;

				var top = $('.essb_break_scroll').offset().top - parseFloat($('.essb_break_scroll').css('marginTop').replace(/auto/, 0));

				if (essb_point_trigger_mode == 'end') {
					if (current_pos >= top && !essb_point_triggered) {
						if (!$('.essb-point-share-buttons').hasClass('essb-point-share-buttons-active')) {
							$('.essb-point-share-buttons').addClass('essb-point-share-buttons-active');
							if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');
							essb_point_triggered = true;

							if (essb_point_autoclose > 0) {
								setTimeout(function() {
									$('.essb-point-share-buttons').removeClass('essb-point-share-buttons-active');
									if (essb_point_mode != 'simple') $('.essb-point').removeClass('essb-point-open');
								}, essb_point_autoclose * 1000)
							}
						}
					}
				}
				if (essb_point_trigger_mode == 'middle') {
					var percentage = current_pos * 100 / top;
					if (percentage > 49 && !essb_point_triggered) {
						if (!$('.essb-point-share-buttons').hasClass('essb-point-share-buttons-active')) {
							$('.essb-point-share-buttons').addClass('essb-point-share-buttons-active');
							if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');
							essb_point_triggered = true;

							if (essb_point_autoclose > 0) {
								setTimeout(function() {
									$('.essb-point-share-buttons').removeClass('essb-point-share-buttons-active');
									if (essb_point_mode != 'simple') $('.essb-point').removeClass('essb-point-open');
								}, essb_point_autoclose * 1000)
							}
						}
					}
				}
			}

			var essb_point_onscroll = $('.essb-point').attr('data-trigger-scroll') || "";
			var essb_point_mode = $('.essb-point').attr('data-point-type') || "simple";
			var essb_point_autoclose = Number($('.essb-point').attr('data-autoclose') || 0) || 0;

			if (essb.responsiveEventsCanRun($('.essb-point'))) {
				if (essb_point_onscroll == 'end' || essb_point_onscroll == 'middle') {
					essb_point_trigger_mode = essb_point_onscroll;
					$(window).on('scroll', essb_point_trigger_open_onscroll);
				}
			}

			$(".essb-point").on('click', function(){

				$('.essb-point-share-buttons').toggleClass('essb-point-share-buttons-active');

				if (essb_point_mode != 'simple') $('.essb-point').toggleClass('essb-point-open');

				if (essb_point_autoclose > 0) {
					setTimeout(function() {
						$('.essb-point-share-buttons').removeClass('essb-point-share-buttons-active');
						if (essb_point_mode != 'simple') $('.essb-point').removeClass('essb-point-open');
					}, essb_point_autoclose * 1000)
				}
	        });
		}

		/**
		 *  Display Method: Corner Bar
		 */

		if ($('.essb-cornerbar').length) {
			if ($('.essb-cornerbar .essb_links').length) $('.essb-cornerbar .essb_links').removeClass('essb_displayed_cornerbar');

			var dataCornerBarShow = $('.essb-cornerbar').attr('data-show') || '',
				dataCornerBarHide = $('.essb-cornerbar').attr('data-hide') || '';

			function essb_cornerbar_scroll() {
				var current_pos = $(window).scrollTop();
				var height = $(document).height() - $(window).height();
				var percentage = current_pos / height * 100,
					breakPercent = dataCornerBarShow == 'onscroll' ? 5 : 45;

				if (dataCornerBarShow == 'onscroll' || dataCornerBarShow == 'onscroll50') {
					if (percentage > breakPercent) {
						if ($('.essb-cornerbar').hasClass('essb-cornerbar-hidden')) $('.essb-cornerbar').removeClass('essb-cornerbar-hidden');
					}
					else {
						if (!$('.essb-cornerbar').hasClass('essb-cornerbar-hidden')) $('.essb-cornerbar').addClass('essb-cornerbar-hidden');
					}
				}

				if (dataCornerBarShow == 'content') {
					var isOneVisible = false;
					$('.essb_displayed_top').each(function() {
						if (isElementInViewport($(this)))
							isOneVisible = true;
					});
					$('.essb_displayed_bottom').each(function() {
						if (isElementInViewport($(this)))
							isOneVisible = true;
					});

					if (!isOneVisible) {
						if ($('.essb-cornerbar').hasClass('essb-cornerbar-hidden')) $('.essb-cornerbar').removeClass('essb-cornerbar-hidden');
					}
					else {
						if (!$('.essb-cornerbar').hasClass('essb-cornerbar-hidden')) $('.essb-cornerbar').addClass('essb-cornerbar-hidden');
					}
				}

				if (dataCornerBarHide != '') {
					if (percentage > 90) {
						if (!$('.essb-cornerbar').hasClass('essb-cornerbar-hiddenend')) $('.essb-cornerbar').addClass('essb-cornerbar-hiddenend');
					}
					else {
						if ($('.essb-cornerbar').hasClass('essb-cornerbar-hiddenend')) $('.essb-cornerbar').removeClass('essb-cornerbar-hiddenend');
					}
				}
			}

			if (essb.responsiveEventsCanRun($('.essb-cornerbar'))) {
				if (dataCornerBarHide != '' || dataCornerBarShow != '')
					$(window).on('scroll', debounce(essb_cornerbar_scroll, 1));

				if (dataCornerBarShow == 'content') essb_cornerbar_scroll();

			}


		}

	
				
		/** 
		 * Reveal the social followers counter that comes with a transition effect
		 */
		if ($('.essbfc-container-sidebar').length) {
			$(".essbfc-container-sidebar").each(function() {
				if ($(this).hasClass("essbfc-container-sidebar-transition")) {
					$(this).removeClass("essbfc-container-sidebar-transition");
				}
			});
		}
		
	});


} )( jQuery );
