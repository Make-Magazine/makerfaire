/**
 * Subscribe forms
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.0
 */
( function( $ ) {
	"use strict";
	
	function essb_ajax_subscribe(key, event) {

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
					event.preventDefault();
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
			 * @since 8.6 custom fields support for Mailchimp
			 */
			var elCustomFields = document.querySelectorAll('.essb-subscribe-form-' + key + ' #essb-subscribe-from-content-form-mailchimp .essb-subscribe-custom'),
				isCustomError = false;
			for (var i = 0; i < elCustomFields.length; i++) {
				if (elCustomFields[i].value == '' && elCustomFields[i].classList.contains('essb-subscribe-required')) {
					isCustomError = true;
					var place = elCustomFields[i].getAttribute('placeholder') || '';
					alert('You need to fill ' + place);
					break;
				}
				
				var param = elCustomFields[i].getAttribute('data-field') || '';
				if (param != '') submitapi_call += '&mailchimp_' + param + '=' + elCustomFields[i].value;
			}
			
			if (isCustomError) return;
			
			/**
			 * @since 7.7 Additional check to prevent mixed content 
			 */
			var current_page_url = window.location.href;
			if (current_page_url.indexOf('https://') > -1 && submitapi_call.indexOf('https://') == -1) submitapi_call = submitapi_call.replace('http://', 'https://');
			
			// validate reCaptcha too
			if ($('.essb-subscribe-captcha').length) {
				var recaptcha  = $( '#g-recaptcha-response' ).val();
				
				if ($('input[name="cf-turnstile-response"]').length) recaptcha  = $( 'input[name="cf-turnstile-response"]' ).val();
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
							
							// integration with Conversions Pro
							if (typeof (essbSubscribeProLog) != 'undefined') {								
								essbSubscribeProLog('subscribe_conversion_success', usedPosition, usedDesign);
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
							
							if (window.pendingUnlockOnSubscribe) essb_optin_locker_unlock();

							essb.trigger('subscribe_success', {'design': usedDesign, 'position': usedPosition, 'email': user_mail, 'name': user_name});
						}
						else {
							// integration with Conversions Pro
							if (typeof (essbSubscribeProLog) != 'undefined') {								
								essbSubscribeProLog('subscribe_conversion_fail', usedPosition, usedDesign);
							}
							
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

	window.essb_ajax_subscribe = essb_ajax_subscribe;
	
	
	
	$(document).ready(function(){
		/**
		 * Booster Pop-up forms
		 */
		var booster_optin_triggered = false,
			booster_optin_percent = 0,
			booster_optin_time = 0;
	
		var essb_manualform_show = window.essb_manualform_show = function() {
			essb_optin_booster_show('manual');
			booster_optin_triggered = false;
		}
	
		var essb_optin_booster_show = function(event) {
	
			if (booster_optin_triggered) return;
	
			var base_element = '.essb-optinbooster-'+event;
			var base_overlay_element = '.essb-optinbooster-overlay-'+event;
	
			if (!$(base_element).length) return;
	
			var singleDisplay = $(base_element).attr('data-single') || '',
				singleDisplayDelay = $(base_element).attr('data-single-days') || '';
	
			if (singleDisplay == '1') {
				var cookie_name = "essbOptinBooster";
				var cookieSet = essbGetCookie(cookie_name);
				if (cookieSet == "yes") { return; }
	
				singleDisplayDelay = (singleDisplayDelay == '' || !Number(singleDisplayDelay)) ? 14 : Number(singleDisplayDelay);
	
				essbSetCookie(cookie_name, "yes", singleDisplayDelay);
			}
	
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
	
			$(base_element).addClass('active-booster');
			$(base_overlay_element).addClass('active-booster-overlay');
	
			booster_optin_triggered = true;
			
			// integration with Conversions Pro
			if (typeof (essbSubscribeProLog) != 'undefined') {
				var position = $(base_element + ' .essb-subscribe-form-content').data('position') || '',
					design = $(base_element + ' .essb-subscribe-form-content').data('design') || '';
				essbSubscribeProLog('subscribe_conversion_loaded', position, design);
			}
		}
	
		var essb_optinbooster_close = function() {
	
	
			$(".active-booster").fadeOut(200);
			$('.active-booster').removeClass('active-booster');
	
			$(".active-booster-overlay").fadeOut(400);
			$('.active-booster-overlay').removeClass('active-booster-overlay');
		}
	
		if ($('.essb-optinbooster-exit')) {
	
			jQuery(document).on('mouseleave', function(e) {
	
					if(e.clientY < 0) { // Check if the cursor went above the top of the browser window
						essb_optin_booster_show('exit');
					}
				});
		}
	
		var essb_booster_scroll = function() {
			if (booster_optin_triggered) { return; }
	
			var current_pos = jQuery(window).scrollTop();
			var height = jQuery(document).height()-jQuery(window).height();
			var percentage = current_pos/height*100;
	
			if (percentage > booster_optin_percent && booster_optin_percent > 0) {
				essb_optin_booster_show('scroll');
			}
		}
	
		if ($('.essb-optinbooster-scroll')) {
			booster_optin_percent = $('.essb-optinbooster-scroll').attr("data-scroll") || "";
			booster_optin_percent = parseFloat(booster_optin_percent);
			$(window).on('scroll', essb_booster_scroll);
		}
	
		if ($('.essb-optinbooster-time')) {
			booster_optin_time = $('.essb-optinbooster-time').attr("data-delay") || "";
			booster_optin_time = parseFloat(booster_optin_time);
			booster_optin_time = booster_optin_time * 1000;
			setTimeout(function(){ essb_optin_booster_show('time'); }, booster_optin_time);
		}
		
		if ($('.essb-optinbooster-manual').length) {
			var selector = $('.essb-optinbooster-manual').data('manual-selector') || '';
			if (selector != '' && $(selector).length) $(selector).on('click', function(e) {
				e.preventDefault();
				essb_manualform_show();
			});
		}
	
	
		$('.essb-optinbooster-overlay').each(function() {
	
			$(this).on('click', function(e) {
				e.preventDefault();
	
				essb_optinbooster_close();
			});
		});
	
		$('.essb-optinbooster-close').each(function() {
	
			$(this).on('click', function(e) {
				e.preventDefault();
	
				essb_optinbooster_close();
			});
		});
	
		function essbSetCookie(cname, cvalue, exdays) {
		    var d = new Date();
		    d.setTime(d.getTime() + (exdays*24*60*60*1000));
		    var expires = "expires="+d.toGMTString();
		    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
		}
	
		function essbGetCookie(cname) {
		    var name = cname + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0; i<ca.length; i++) {
		        var c = ca[i].trim();
		        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
		    }
		    return "";
		}

		/**
		 * Flyout subscribe forms
		 */
		var optin_triggered = false,
			optin_percent = 0,
			optin_time = 0;
	
		var essb_optin_flyout_show = function(event) {
			
			if (optin_triggered) return;
			
			var base_element = '.essb-optinflyout-'+event;
			var base_overlay_element = '.essb-optinflyout-overlay-'+event;
			
			if (!$(base_element).length) return;
			
			var singleDisplay = $(base_element).attr('data-single') || '';
			if (singleDisplay == '1') {
				var cookie_name = "essbOptinFlyout";
				var cookieSet = essbGetCookie(cookie_name);
				if (cookieSet == "yes") { return; }
				essbSetCookie(cookie_name, "yes", 14);
			}
			
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
			
			var base_width = 500;
			if (win_width < base_width) { base_width = win_width - 60; }
			
			$(base_element).css( { width: base_width+'px'});
			
			$(base_element).slideDown(400);
			
			$(base_element).addClass('active-flyout');
			
			optin_triggered = true;
			// integration with Conversions Pro
			if (typeof (essbSubscribeProLog) != 'undefined') {
				var position = $(base_element + ' .essb-subscribe-form-content').data('position') || '',
					design = $(base_element + ' .essb-subscribe-form-content').data('design') || '';
				essbSubscribeProLog('subscribe_conversion_loaded', position, design);
			}
		}
		
		var essb_optinflyout_close = function() {
			
	
			$(".active-flyout").fadeOut(200);
			$('.active-flyout').removeClass('active-flyout');
		}
	
		var essb_booster_exit = function() {
			var e = window.event;
			
			if (!e) return;
			
			var from = e.relatedTarget || e.toElement;
	
			// Reliable, works on mouse exiting window and user switching active program
			if(!from || from.nodeName === "HTML") {
				essb_optin_flyout_show('exit');
			}
		}
		
		if ($('.essb-optinflyout-exit'))
			$(document).mouseout(essb_booster_exit);
		
		var essb_flyout_scroll = function() {
			if (optin_triggered) { return; }
			
			var current_pos = jQuery(window).scrollTop();
			var height = jQuery(document).height()-jQuery(window).height();
			var percentage = current_pos/height*100;	
			
			if (percentage > optin_percent && optin_percent > 0) {
				essb_optin_flyout_show('scroll');
			}
		}
		
		if ($('.essb-optinflyout-scroll')) {
			optin_percent = $('.essb-optinflyout-scroll').attr("data-scroll") || "";
			optin_percent = parseFloat(optin_percent);
			$(window).on('scroll', essb_flyout_scroll);
		}
		
		if ($('.essb-optinflyout-time')) {
			optin_time = $('.essb-optinflyout-time').attr("data-delay") || "";
			optin_time = parseFloat(optin_time);
			optin_time = optin_time * 1000;
			setTimeout(function(){ essb_optin_flyout_show('time'); }, optin_time);
		}
		
		
		$('.essb-optinflyout-overlay').each(function() {
			
			$(this).on('click', function(e) {
				e.preventDefault();
				
				essb_optinflyout_close();
			});
		});
		
		$('.essb-optinflyout-close').each(function() {
			
			$(this).on('click', function(e) {
				e.preventDefault();
				
				essb_optinflyout_close();
			});
		});
		
		/**
		 * Locker form
		 */
		var locker_optin_triggered = false,
			locker_optin_percent = 0,
			locker_optin_time = 0,
			locker_cookie_name = 'essb_optin_locker_unlocked',
			pendingUnlockOnSubscribe = window.pendingUnlockOnSubscribe = false;
		
		var essb_optin_locker_unlock = window.essb_optin_locker_unlock = function() {
			essb_optinlocker_close();
			
			var unlockLen = $('.essb-optinlocker').data('unlock') || '';			
			if (!Number(unlockLen)) unlockLen = 90;
			essbSetCookie(locker_cookie_name, "yes", unlockLen);
		};
		
		var essb_optin_locker_show = function(event) {
	
			if (locker_optin_triggered) return;
	
			var base_element = '.essb-optinlocker-'+event;
			var base_overlay_element = '.essb-optinlocker-overlay-'+event;
	
			if (!$(base_element).length) return;
	
			// check if it is unlocked
			if (essbGetCookie(locker_cookie_name) == 'yes') return;
				
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
	
			$(base_element).addClass('active-locker');
			$(base_overlay_element).addClass('active-locker-overlay');
			$('body').addClass('removeScroll');
	
			locker_optin_triggered = true;
			window.pendingUnlockOnSubscribe = true;
			
			// integration with Conversions Pro
			if (typeof (essbSubscribeProLog) != 'undefined') {
				var position = $(base_element + ' .essb-subscribe-form-content').data('position') || '',
					design = $(base_element + ' .essb-subscribe-form-content').data('design') || '';
				essbSubscribeProLog('subscribe_conversion_loaded', position, design);
			}
		}
	
		var essb_optinlocker_close = function() {
			$(".active-locker").fadeOut(200);
			$('.active-locker').removeClass('active-locker');
	
			$(".active-locker-overlay").fadeOut(400);
			$('.active-locker-overlay').removeClass('active-locker-overlay');
			$('body').removeClass('removeScroll');
		}
	
	
		var essb_optinlocker_scroll = function() {
			if (locker_optin_triggered) { return; }	
			var current_pos = jQuery(window).scrollTop();
			var height = jQuery(document).height()-jQuery(window).height();
			var percentage = current_pos/height*100;
	
			if (percentage > locker_optin_percent && locker_optin_percent > 0) {
				essb_optin_locker_show('scroll');
			}
		}
	
		if ($('.essb-optinlocker-scroll')) {
			locker_optin_percent = $('.essb-optinlocker-scroll').attr("data-scroll") || "";
			locker_optin_percent = parseFloat(locker_optin_percent);
			$(window).on('scroll', essb_optinlocker_scroll);
		}
	
		if ($('.essb-optinlocker-time')) {
			locker_optin_time = $('.essb-optinlocker-time').attr("data-delay") || "";
			locker_optin_time = parseFloat(locker_optin_time);
			locker_optin_time = booster_optin_time * 1000;
			setTimeout(function(){ essb_optin_locker_show('time'); }, locker_optin_time);
		}
			
		$('.essb-optinlocker-close').each(function() {
			$(this).on('click', function(e) {
				e.preventDefault();
				essb_optinlocker_close();
			});
		});
		
		/**
		 * Subscribe reCaptcha		
		 */
		if ($('.essb-subscribe-captcha').length) {
			$('.essb-subscribe-captcha').each(function() {
				var id = $(this).attr('id') || '';
				if (id == '') return;				
				
				// Maybe load reCAPTCHA.
				if ( typeof(essb_subscribe_recaptcha) != 'undefined' && essb_subscribe_recaptcha && essb_subscribe_recaptcha.recaptchaSitekey ) {
					setTimeout(function() {
						grecaptcha.render(essb_subscribe_recaptcha.turnstile ? '#' + id : id, {
							sitekey:  essb_subscribe_recaptcha.recaptchaSitekey
						} );
					}, 500);
				}
			});
		}				
	});
} )( jQuery );
