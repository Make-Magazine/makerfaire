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
	var essb = {};

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


		if (!trackingOnly)
			wnd = window.open( url, keyWin, "height="+(service == 'twitter' ? '500' : '500')+",width="+(service == 'twitter' ? '500' : '800')+",resizable=1,scrollbars=yes,top="+top+",left="+left );


		if (typeof(essb_settings) != "undefined") {
			if (essb_settings.essb3_stats) {
				if (typeof(essb_handle_stats) != "undefined")
					essb_handle_stats(service, instance_post_id, instance);

			}

			if (essb_settings.essb3_ga)
				essb_ga_tracking(service, url, instance_position);

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
			var instance_post_id = $('.essb_'+instance).attr('data-essb-postid') || '';
			essb_smart_onclose_events(service, instance_post_id);

			if (instance_position == 'booster' && typeof(essb_booster_close_from_action) != 'undefined')
				essb_booster_close_from_action();
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

			$('.essb-windowcs-'+key).fadeIn(200);
			$('.essb-windowcs-'+key+' .inner-content').center();
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

	essb.ajax_subscribe = function(key, event) {

		event.preventDefault();

		var formContainer = $('.essb-subscribe-form-' + key + ' #essb-subscribe-from-content-form-mailchimp'),
			positionContainer = $('.essb-subscribe-form-' + key + ' .essb-subscribe-form-content');

		var usedPosition = $(positionContainer).attr('data-position') || '',
			usedDesign = $(positionContainer).attr('data-design') || '';

		if (formContainer.length) {
			// Additional check for require agree to terms check
			if ($(formContainer).find('.essb-subscribe-confirm').length) {
				var state = $(formContainer).find('.essb-subscribe-confirm').is(":checked");
				if (!state) {

					if (essb_settings.subscribe_terms_error)
						alert(essb_settings.subscribe_terms_error);
					else
						alert('You need to confirm that you agree with our terms');
					return;
				}
			}

			if ($(formContainer).find('.essb-subscribe-form-content-name-field').length && essb_settings.subscribe_validate_name) {
				if ($(formContainer).find('.essb-subscribe-form-content-name-field').val() == '') {
					if (essb_settings.subscribe_validate_name_error)
						alert(essb_settings.subscribe_validate_name_error);
					else
						alert('You need to fill name field too');
					return;
				}
			}

			var user_mail = $(formContainer).find('.essb-subscribe-form-content-email-field').val();
			var user_name = $(formContainer).find('.essb-subscribe-form-content-name-field').length ? $(formContainer).find('.essb-subscribe-form-content-name-field').val() : '';
			$(formContainer).find('.submit').prop('disabled', true);
			$(formContainer).hide();
			$('.essb-subscribe-form-' + key).find('.essb-subscribe-loader').show();
			var submitapi_call = formContainer.attr('action') + '&mailchimp_email='+user_mail+'&mailchimp_name='+user_name+'&position='+usedPosition+'&design='+usedDesign+'&title='+encodeURIComponent(document.title);
			
			/**
			 * @since 7.7 Additional check to prevent mixed content 
			 */
			var current_page_url = window.location.href;
			if (current_page_url.indexOf('https://') > -1 && submitapi_call.indexOf('https://') == -1) submitapi_call = submitapi_call.replace('http://', 'https://');
			
			// validate reCaptcha too
			if ($('.essb-subscribe-captcha').length) {
				var recaptcha  = $( '#g-recaptcha-response' ).val();
				submitapi_call += '&validate_recaptcha=true&recaptcha=' + recaptcha;
			}
			
			
			$.post(submitapi_call, { mailchimp_email1: user_mail, mailchimp_name1: user_name},
					function (data) {

						if (data) {

						console.log(data);

						if (data['code'] == '1') {
							$('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-success').show();
							$('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').hide();
							$(formContainer).hide();

							// subscribe conversions tracking
							//usedPosition
							if (typeof(essb_subscribe_tracking) != 'undefined') {
								essb_subscribe_tracking(usedPosition);
							}

							// redirecting users if successful redirect URL is set
							if (data['redirect']) {
								setTimeout(function() {

									if (data['redirect_new']) {
										var win = window.open(data['redirect'], '_blank');
										win.focus();
									}
									else
										window.location.href = data['redirect'];
								}, 200);
							}

							essb.trigger('subscribe_success', {'design': usedDesign, 'position': usedPosition, 'email': user_mail, 'name': user_name});
						}
						else {
							var storedMessage = $('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').attr('data-message') || '';
							if (storedMessage == '') {
								 $('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').attr('data-message', $('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').text());
							}

							if (data['code'] == 90)
								$('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').text(data['message']);
							else
								$('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').text(storedMessage);
							$('.essb-subscribe-form-' + key).find('.essb-subscribe-form-content-error').show();
							$('.essb-subscribe-form-' + key).find('.essb-subscribe-from-content-form').show();
							$(formContainer).find('.submit').prop('disabled', false);
						}
						$('.essb-subscribe-form-' + key).find('.essb-subscribe-loader').hide();
					}},
			'json');
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
	
	essb.copy_link = function(instance_id, user_href) {
		var currentLocation = window.location.href, win_width = $( window ).width();
				
		if (instance_id && $('.essb_' + instance_id).length) {
			var instance_url = $('.essb_' + instance_id).data('essb-url') || '';
			if (instance_url != '') currentLocation = instance_url;
		}
		
		if (user_href && user_href != '') currentLocation = user_href;
		
		if (!$('.essb-copylink-window').length) {
			var output = [];
			output.push('<div class="essb_morepopup essb-copylink-window" style="z-index: 1301;">');
			output.push('<div class="essb_morepopup_header"> <span>&nbsp;</span> <a href="#" class="essb_morepopup_close"><i class="essb_icon_close"></i></a> </div>');
			output.push('<div class="essb_morepopup_content">');
			output.push('<div class="essb_copy_internal" style="display: flex; align-items: center;">');
			output.push('<div style="width: calc(100% - 50px); padding: 5px;"><input type="text" id="essb_copy_link_field" style="width: 100%;padding: 5px 10px;font-size: 15px;background: #f5f6f7;border: 1px solid #ccc;font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;" /></div>');
			output.push('<div style="width:50px;text-align: center;"><a href="#" class="essb-copy-link" title="'+ (essb_settings.translate_copy_message1 ? essb_settings.translate_copy_message1 : 'Press to copy the link')+'" style="color:#5867dd;background:#fff;padding:10px;text-decoration: none;"><i class="essb_icon essb_icon_copy"></i></a></div>');
			output.push('</div>');
			output.push('<div class="essb-copy-message" style="font-size: 13px; font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;"></div>');
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
		essb['essb_mailform_opened'] = true;
		essb.tracking_only("", "mail", unique_id);
	};
	
	var essb_close_mailform = window.essb_close_mailform = function() {
		$(".essb_mailform").fadeOut(200);
		$(".essb_mailform_shadow").fadeOut(200);
		essb['essb_mailform_opened'] = false;
	};
	
	var essb_mailform_send = window.essb_mailform_send = function() {
		var sender_email = $("#essb_mailform_from").val(),
			sender_name = $('#essb_mailform_from_name').val(),
			recepient_email = $("#essb_mailform_to").val(),
			captcha_validate = $("#essb_mailform_c").length ? true : false,
			errorMessage = $('.essb_mailform').attr('data-error') || '',
			captcha = captcha_validate ? $("#essb_mailform_c").val() : "",
			recaptcha  = $( '#g-recaptcha-response' ).val(),
			custom_message = '';
		
		if (sender_email == "" || recepient_email == "" || (captcha == "" && captcha_validate)) {
			alert(errorMessage);
			return;
		}
		
		var mail_salt = $("#essb_mail_salt").val(),
			instance_post_id = $("#essb_mail_post").val();
		
		if (typeof(essb_settings) != "undefined") {
			$.post(essb_settings.ajax_url, {
				"action": "essb_mail_action",
				"post_id": instance_post_id,
				"from": sender_email,
				"from_name": sender_name,
				"to": recepient_email,
				"c": captcha,
				"cu": custom_message,
				"salt": mail_salt,
				'recapcha': recaptcha,
				"nonce": essb_settings.essb3_nonce
				}, function (data) { if (data) {
					alert(data["message"]);
					if (data["code"] == "1") essb_close_mailform();
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
			
			// Hiding share buttons when content is visible
			if (value_contenthidden == 'yes' && ($('.essb_displayed_top').length || $('.essb_displayed_bottom').length)) {
				
				if (($('.essb_displayed_top').length && isVisibleSelector($('.essb_displayed_top'))) ||
						($('.essb_displayed_bottom').length && isVisibleSelector($('.essb_displayed_bottom'))))
					element.fadeOut(100);
				else
					element.fadeIn(100);				
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
		 * Display Method: Post Bar
		 */
		if ($('.essb-postbar').length) {

	        // Define Variables
	        var ttr_start = $(".essb_postbar_start"),
	            ttr_end = $(".essb_postbar_end");
	        if (ttr_start.length) {
	            var docOffset = ttr_start.offset().top,
	        	docEndOffset = ttr_end.offset().top,
	            elmHeight = docEndOffset - docOffset,
	            progressBar = $('.essb-postbar-progress-bar'),
	            winHeight = $(window).height(),
	            docScroll,viewedPortion;


		        $(".essb-postbar-prev-post a").on('mouseenter touchstart', function(){
		            $(this).next('div').css("top","-162px");
		        });
		        $(".essb-postbar-close-prev").on('click', function(){
		        	$(".essb-postbar-prev-post a").next('div').css("top","46px");
		        });
		        $(".essb-postbar-next-post a").on('mouseenter touchstart', function(){
		            $(this).next('div').css("top","-162px");
		        });
		        $(".essb-postbar-close-next").on('click', function(){
		        	$(".essb-postbar-next-post a").next('div').css("top","46px");
		        });

		        $(window).on('load', function(){
		            docOffset = ttr_start.offset().top,
		            docEndOffset = ttr_end.offset().top,
		            elmHeight = docEndOffset - docOffset;
		        });

		        $(window).on('scroll', function() {

					docScroll = $(window).scrollTop(),
		            viewedPortion = winHeight + docScroll - docOffset;

					if(viewedPortion < 0) {
						viewedPortion = 0;
					}
		            if(viewedPortion > elmHeight) {
		            	viewedPortion = elmHeight;
		            }
		            var viewedPercentage = (viewedPortion / elmHeight) * 100;
					progressBar.css({ width: viewedPercentage + '%' });

				});

				$(window).on('resize', function() {
					docOffset = ttr_start.offset().top;
					docEndOffset = ttr_end.offset().top;
					elmHeight = docEndOffset - docOffset;
					winHeight = $(window).height();
					$(window).trigger('scroll');
				});

				$(window).trigger('scroll');
	        }

		};

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

						var cookieSet = essb.getCookie(cookie_name);
						if (cookieSet == "yes") return;
						essb.setCookie(cookie_name, "yes", 7);
					}
					if (popHideOnCloseAll == "1") {
						cookie_name = base_cookie_name + "all";

						var cookieSet = essb.getCookie(cookie_name);
						if (cookieSet == "yes") return;
						essb.setCookie(cookie_name, "yes", 7);
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

			if (essb.responsiveEventsCanRun($('.essb-flyin'))) {
				var element = $('.essb-flyin');
				if (essb.is_after_comment() && element.hasClass("essb-flyin-oncomment")) {
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

						var cookieSet = essb.getCookie(cookie_name);
						if (cookieSet == "yes")  return;
						essb.setCookie(cookie_name, "yes", 7);
					}
					if (popHideOnCloseAll == "1") {
						cookie_name = base_cookie_name + "all";

						var cookieSet = essb.getCookie(cookie_name);
						if (cookieSet == "yes") return;
						essb.setCookie(cookie_name, "yes", 7);
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
						essb.popup_close();
					}, optin_time);

				}

				$(".essb-popup").css( { width: base_width+'px'});
				$(".essb-popup").center();

				$(".essb-popup").fadeIn(400);
				$(".essb-popup-shadow").fadeIn(200);

				popupShown = true;
			};			

			if (essb.responsiveEventsCanRun($('.essb-popup'))) {
				var element = $('.essb-popup');
				if (essb.is_after_comment()) {
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
		
		/**
		 * Display Method: Bottom Bar
		 */

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
			if (essb.responsiveEventsCanRun($('.essb_bottombar'))) {
				var element = $('.essb_bottombar');
				if (($(element).find('.essb_links').data('bottombar-appear') || '') != '' || ($(element).find('.essb_links').data('bottombar-disappear') || '') != '')
					$(window).on('scroll', debounce(essb_bottombar_onscroll, 1));
			}

		//TODO: From here to add responsive events class

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

		if (essb.responsiveEventsCanRun($('.essb_topbar'))) {
			if ($(".essb_topbar").length) {
				var element = $(".essb_topbar");
				if (($(element).find('.essb_links').data('topbar-appear') || '') != '' || ($(element).find('.essb_links').data('topbar-disappear') || '') != '')
					$(window).on('scroll', debounce(essb_topbar_onscroll, 1));
			}
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

				essb.setCookie(boosterCookieKey, "yes", Number(booster_hide));
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
			var cookie_set = essb.getCookie(boosterCookieKey);

			// booster is already triggered
			if (cookie_set) booster_trigger = 'disabled';

			if (essb.responsiveEventsCanRun($('.essb-sharebooster'))) {
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

		/**
		 * Click2Chat
		 */
		if ($('.essb-click2chat').length) {
			$('.essb-click2chat').on('click', function(event) {
				event.preventDefault();

				$('.essb-click2chat-window').toggleClass('active');
			});

			if ($('.essb-click2chat-window .chat-close').length) {
				$('.essb-click2chat-window .chat-close').on('click', function(event) {
					event.preventDefault();

					$('.essb-click2chat-window').toggleClass('active');
				});
			}

			$('.essb-click2chat-window .operator').each(function() {
				$(this).on('click', function(event) {
					event.preventDefault();

					var app = $(this).attr('data-app') || '',
						number = $(this).attr('data-number') || '',
						message = $(this).attr('data-message') || '',
						cmd = '';

					var instance_mobile = false;
					if( (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i).test(navigator.userAgent) ) {
						instance_mobile = true;
					}

					if (app == 'whatsapp') {
						cmd = 'https://api.whatsapp.com/send?phone='+number+'&text=' + message;
					}
					if (app == 'viber') {
						cmd = 'viber://chat?number='+number+'&text=' + message;
					}
					if (app == 'email') {
						cmd = 'mailto:'+number+'&body=' + message;
					}
					if (app == 'phone') {
						cmd = 'tel:'+number;
					}
					
					if (instance_mobile) window.location.href = cmd;
					else {
						window.open(cmd, '_blank');
					}

				});
			});
		}
		
		/**
			* Applying additional Pinterest optimizations for images
			*/
		if (essb_settings.force_pin_description && essb_settings.pin_description) {
			$('img').each(function() {
				if (!$(this).data('pin-description')) $(this).attr('data-pin-description', essb_settings.pin_description);
			});
		}
				
		if (essb_settings.pin_pinid_active && essb_settings.pin_pinid) {
			$('img').each(function() {
				var hasPinID = $(this).data('pin-id') || '';
				if (!hasPinID || hasPinID == '') $(this).attr('data-pin-id', essb_settings.pin_pinid);
			});
		}
		
		if (essb_settings.pin_force_active && essb_settings.pin_force_image) {
			$('img').each(function() {
				$(this).attr('data-pin-media', essb_settings.pin_force_image);
				
				/**
				 * Forcing all custom parameters too
				 */
				if (!$(this).data('pin-description')) {
					var pinDescription  = '';
					if ($(this).attr('title')) pinDescription = $(this).attr('title');
					else if ($(this).attr('alt')) pinDescription = $(this).attr('alt');

					// give always priority of the custom description if set
					if (essbPinImages.force_custompin && !essbPinImages.custompin) essbPinImages.custompin = document.title;
					if (essbPinImages.custompin) pinDescription = essbPinImages.custompin;

					// if title is not genenrated it will use the Document Title
					if (pinDescription == '') pinDescription = document.title;
					
					$(this).attr('data-pin-description', pinDescription);
				}
				
				if (!$(this).data('pin-url')) $(this).attr('data-pin-url', encodeURI(document.URL));
			});
		}
		
		/**
		 * Pinterest Pro Gutenberg images integration
		 */
		
		$('.essb-block-image').each(function() {
			var pinID = $(this).data('essb-pin-id') || '',
				pinDesc = $(this).data('essb-pin-description') || '',
				pinAvoid = $(this).data('essb-pin-nopin') || '';						
			
			if (pinAvoid.toString() == 'true') {
				$(this).find('img').attr('data-pin-nopin', 'true');
				$(this).find('img').addClass('no_pin');
				return;
			}
			
			if (pinID != '') $(this).find('img').attr('data-pin-id', pinID);
			if (pinDesc != '') $(this).find('img').attr('data-pin-description', pinDesc);
		});

		/**
		 * Pinterest responsive thumbnail correction
		 */
		if (essb_settings.force_pin_thumbs) {
			// setting up a map of parsing images on site
			var essbReposiveImagesMap = window.essbReposiveImagesMap = {};

			// getting actual size of a single image
			var essbDetectAndLocateImageSize = window.essbDetectAndLocateImageSize = function(url, element, isResponsive) {
			  if (isResponsive) {
			    essbReposiveImagesMap[element].responsive[url] = {};
			  }
			  $("<img/>", {
			    load: function() {
			      if (essbReposiveImagesMap[element]) {
			        if (!isResponsive) {
			          essbReposiveImagesMap[element].originalSize = { 'w': this.width, 'h': this.height, 'done': true };
			          essbCompileTheDataPinImage(element);
			        } else {
			          essbReposiveImagesMap[element].responsive[url] = { 'w': this.width, 'h': this.height, 'done': true };
			          essbCompileTheDataPinImage(element);
			        }
			      }

			    },
			    src: url
			  });
			};

			var essbCompileTheDataPinImage = window.essbCompileTheDataPinImage = function(element) {
			  var totalImages = 0,
			    processImages = 0,
			    currentMaxW = 0,
			    imageURL = '';

			  for (var rImageURL in essbReposiveImagesMap[element].responsive) {
			    var dataObj = essbReposiveImagesMap[element].responsive[rImageURL] || {};
			    totalImages++;

			    if (!dataObj.done) continue;
			    processImages++;
			    if (currentMaxW == 0 || currentMaxW < dataObj.w) {
			      currentMaxW = dataObj.w;
			      imageURL = rImageURL;
			    }

			  }

			  if (totalImages == processImages && essbReposiveImagesMap[element].original != imageURL) {
			    if (essbReposiveImagesMap[element].originalSize.done) {
			      if (currentMaxW > essbReposiveImagesMap[element].originalSize.w) {
			        $('[data-pinpro-key="' + element + '"]').attr('data-pin-media', imageURL);
			        $('[data-pinpro-key="' + element + '"]').attr('data-media', imageURL);
			        $('[data-pinpro-key="' + element + '"]').attr('data-pin-url', window.location.href);
					$('[data-pinpro-key="' + element + '"]').removeClass('pin-process');
					$('[data-pinpro-key="' + element + '"]').each(essbPinImagesGenerateButtons);
			      }
			    }
			  }
			}

			$('img').each(function() {
			  var responsiveImages = $(this).attr('srcset') || '',
			    uniqueID = Math.random().toString(36).substr(2, 9),
			    element = uniqueID;

			  if (!responsiveImages || responsiveImages == '') return;

			  $(this).attr('data-pinpro-key', uniqueID);
				$(this).addClass('pin-process');
			  var responsiveSet = responsiveImages.split(', '),
			    originalImage = $(this).attr('src') || '',
			    foundReponsiveImage = '',
			    foundReponsiveSize = 0;

			  essbReposiveImagesMap[element] = {
			    source: element,
			    original: originalImage,
			    originalSize: {},
			    responsive: {}
			  };
			  essbDetectAndLocateImageSize(originalImage, element);
			  for (var i = 0; i < responsiveSet.length; i++) {
			    if (!responsiveSet[i]) continue;
			    var imageData = responsiveSet[i].split(' '),
			      imageURL = imageData[0] || '',
			      imageSize = (imageData[1] || '').replace('w', '');

			    if (!imageURL || !Number(imageSize)) continue;

			    essbDetectAndLocateImageSize(imageURL, element, true);

			  }

			});
		} // end forcing generation of responsive images

		/**
		 * Pinterest Images
		 */

		var essbPinImagesGenerateButtons = function() {
			var image = $(this);
			// the option to avoid button over images with links
			if (essbPinImages.nolinks && $(image).parents().filter("a").length) return;

			// avoid buttons on images that has lower size that setup
			if (image.outerWidth() < Number(essbPinImages.min_width || 0) || image.outerHeight() < Number(essbPinImages.min_height || 0)) return;
			// ignore the non Pinable images
			if (image.hasClass('no_pin') || image.hasClass('no-pin') || image.data('pin-nopin') || image.hasClass('pin-generated') || image.hasClass('pin-process') || image.hasClass('zoomImg') || image.hasClass('lazy-hidden')) return;

			var pinSrc = $(image).prop('src') || '',
				pinDescription = '', shareBtnCode = [],
				buttonStyleClasses = '', buttonSizeClasses = '',
				pinID = $(image).data('pin-id') || '';
			
			// additional check for the autoptimize svg placeholder preventing images from load
			// Pinterest also does not accept SVG images
			if (pinSrc.indexOf('data:image/svg+xml') > -1 || pinSrc.indexOf('data:image/gif') > -1) return;

			if (image.data('media')) pinSrc = image.data('media');
			if (image.data('lazy-src')) pinSrc = image.data('lazy-src');
			if (image.data('pin-media')) pinSrc = image.data('pin-media');

			if (image.data("pin-description")) pinDescription = image.data("pin-description");
			else if (image.attr('title')) pinDescription = image.attr('title');
			else if (image.attr('alt')) pinDescription = image.attr('alt');

			// give always priority of the custom description if set
			if (essbPinImages.force_custompin && !essbPinImages.custompin) essbPinImages.custompin = document.title;
			if (essbPinImages.custompin) pinDescription = essbPinImages.custompin;

			// if title is not genenrated it will use the Document Title
			if (pinDescription == '') pinDescription = document.title;

			var shareCmd = 'https://pinterest.com/pin/create/button/?url=' + encodeURI(document.URL) + '&is_video=false' + '&media=' + encodeURI(pinSrc) + '&description=' + encodeURIComponent(pinDescription);
			
			if (essbPinImages.legacy_share_cmd)
				shareCmd = 'https://pinterest.com/pin/create/bookmarklet/?url=' + encodeURI(document.URL) + '&media=' + encodeURI(pinSrc) + '&title=' + encodeURIComponent(pinDescription)+'&description=' + encodeURIComponent(pinDescription) + '&media=' + encodeURI(pinSrc);
			
			if (pinID != '') shareCmd = 'https://www.pinterest.com/pin/'+pinID+'/repin/x/';

			var imgClasses = image.attr('class'),
			    imgStyles = image.attr('style');

			if (essbPinImages ['button_style'] == 'icon_hover') {
				buttonStyleClasses = ' essb_hide_name';
			}
			if (essbPinImages ['button_style'] == 'icon') {
				buttonStyleClasses = ' essb_force_hide_name essb_force_hide';
			}
			if (essbPinImages ['button_style'] == 'button_name') {
				buttonStyleClasses = ' essb_hide_icon';
			}
			if (essbPinImages ['button_style'] == 'vertical') {
				buttonStyleClasses = ' essb_vertical_name';
			}

			if (essbPinImages['button_size']) buttonSizeClasses = ' essb_size_' + essbPinImages['button_size'];
			if (essbPinImages['animation']) buttonSizeClasses += ' ' + essbPinImages['animation'];
			if (essbPinImages['position']) buttonSizeClasses += ' essb_pos_' + essbPinImages['position'];
			
			if (essbPinImages['mobile_position']) buttonSizeClasses += ' essb_mobilepos_' + essbPinImages['mobile_position'];
			if (essbPinImages['visibility'] && essbPinImages['visibility'] == 'always') buttonSizeClasses += ' essb_always_visible';

			image.removeClass().attr('style', '').wrap('<div class="essb-pin" />');
			if (imgClasses != '') image.parent('.essb-pin').addClass(imgClasses);
			if (imgStyles != '') image.parent('.essb-pin').attr('style', imgStyles);

			if (essbPinImages.reposition) {
				var imgWidth = $(image).width();
				if (Number(imgWidth) && !isNaN(imgWidth) && Number(imgWidth) > 0) {
					image.parent('.essb-pin').css({'max-width': imgWidth+'px'});
				}
			}
			
			var uid = (new Date().getTime()).toString(36);

			shareBtnCode.push('<div class="essb_links essb_displayed_pinimage essb_template_'+essbPinImages.template+buttonSizeClasses+' essb_'+uid+'" data-essb-position="pinit" data-essb-postid="'+(essb_settings.post_id || '')+'" data-essb-instance="'+uid+'">');
			shareBtnCode.push('<ul class="essb_links_list'+(buttonStyleClasses != '' ? ' ' + buttonStyleClasses : '')+'">');
			shareBtnCode.push('<li class="essb_item essb_link_pinterest nolightbox">');
			shareBtnCode.push('<a class="nolightbox" rel="noreferrer noopener nofollow" href="'+shareCmd+'" onclick="essb.window(&#39;'+shareCmd+'&#39;,&#39;pinpro&#39;,&#39;'+uid+'&#39;); return false;" target="_blank"><span class="essb_icon essb_icon_pinterest"></span><span class="essb_network_name">'+(essbPinImages['text'] ? essbPinImages['text'] : 'Pin')+'</span></a>');
			shareBtnCode.push('</li>');
			shareBtnCode.push('</ul>');
			shareBtnCode.push('</div>');

			image.after(shareBtnCode.join(''));
			image.addClass('pin-generated'); // adding class to avoid generating again the same information
			//essb.share_window
			//removing the lazyloading class if posted
			if (image.parent('.essb-pin').hasClass('lazyloading')) image.parent('.essb-pin').removeClass('lazyloading');

		}

		if (typeof(essbPinImages) != 'undefined' && $('body').hasClass('tcb-edit-mode')) essbPinImages.active = false;

		if (typeof(essbPinImages) != 'undefined' && essbPinImages.active) {
			// Begin detection of potential images and assign the pinterest generation
			if (!essbPinImages.min_width || !Number(essbPinImages.min_width)) essbPinImages.min_width = 300;
			if (!essbPinImages.min_height || !Number(essbPinImages.min_height)) essbPinImages.min_height = 300;
			
			// Integration with the mobile minimal width and height (if set)
			if ($(window).width() < 720) {
				if (Number(essbPinImages.min_width_mobile)) essbPinImages.min_width = Number(essbPinImages.min_width_mobile);
				if (Number(essbPinImages.min_height_mobile)) essbPinImages.min_height = Number(essbPinImages.min_height_mobile);
			}

			if ($('.essb-pin.tve_image').length) {
				$('.essb-pin.tve_image .essb_links').remove();
				$('.essb-pin img').removeClass('pin-generated');
			}

			// WP Rocket Lazy Videos set no-pin class to those images to prevent holding down
			$('.rll-youtube-player img').each(function() {
				$(this).addClass('no-pin');
			});
			
			// Hide on images option
			if (essbPinImages.hideon) {
				$(essbPinImages.hideon).each(function() {
					$(this).addClass('no-pin');
				});
			}

			window.addEventListener('LazyLoad::Initialized', function (e) {
				$('.rll-youtube-player img').each(function() {
					$(this).addClass('no-pin');
				});
			});

			var essbPinImagesDetect = function() {
				
				// WP Rocket Lazy Videos set no-pin class to those images to prevent holding down
				$('.rll-youtube-player img').each(function() {
					$(this).addClass('no-pin');
				});
				

				if (essbPinImages.selector) {
					$(essbPinImages.selector).each(essbPinImagesGenerateButtons);
				}
				else {
					if (!$('.essb-pinterest-images').length) return;
					$('.essb-pinterest-images').parent().find('img').each(essbPinImagesGenerateButtons);
				}
			}

			if (essbPinImages.lazyload) $(window).on('scroll', debounce(essbPinImagesDetect, 10));

			setTimeout(essbPinImagesDetect, 1);
		}

		if ((typeof(essbPinImages) != 'undefined' && !essbPinImages.active) || typeof(essbPinImages) == 'undefined') {
			if ($('.essb-pin.tve_image').length) {
				$('.essb-pin.tve_image .essb_links').remove();
				$('.essb-pin img').removeClass('pin-generated');
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
		
		/**
		 * Subscribe reCaptcha		
		 */
		if ($('.essb-subscribe-captcha').length) {
			$('.essb-subscribe-captcha').each(function() {
				var id = $(this).attr('id') || '';
				if (id == '') return;
				
				// Maybe load reCAPTCHA.
				if ( typeof(essb_subscribe_recaptcha) != 'undefined' && essb_subscribe_recaptcha && essb_subscribe_recaptcha.recaptchaSitekey ) {
					grecaptcha.render( id, {
						sitekey:  essb_subscribe_recaptcha.recaptchaSitekey
					} );
				}
			});
		}		
	});


} )( jQuery );
