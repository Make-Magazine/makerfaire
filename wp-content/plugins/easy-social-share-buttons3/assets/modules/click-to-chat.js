/**
 * Flyin
 * @package EasySocialShareButtons
 * @author appscreo
 * @since 8.0
 */

jQuery(document).ready(function($){
	"use strict";
	
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
	
} );
