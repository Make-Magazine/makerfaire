jQuery(document).ready(function($){
	"use strict";
	
	$(".essb-wizard-menu").find(".essb-menu-item").each(function() {
		
		$(this).on('click', function(e) {
			$(".essb-wizard-menu").find(".essb-menu-item").each(function(){
				if ($(this).hasClass('active')) {
					$(this).removeClass('active');
					$(this).addClass('passed');
				}
			});

			if (!$(this).prev().length) 
				$('#prevbutton').hide();
			else
				$('#prevbutton').show();

			if (!$(this).next().length) 
				$('#nextbutton').hide();
			else
				$('#nextbutton').show();
			
			
			$(this).addClass('active');

			var lookupHolder = $(this).attr('data-menu') || '';

			if (lookupHolder != '') {
				$(".essb-options-container").find(".essb-data-container").each(function(){
					if ($(this).hasClass('active')) {
						$(this).removeClass('active');
						$(this).fadeOut('fast');
					}
				});

				if ($('#essb-container-'+lookupHolder).length) {
					$('#essb-container-'+lookupHolder).addClass('active');
					$('#essb-container-'+lookupHolder).fadeIn('fast');
				}

			}
		});
	});

	$('#nextbutton').on('click', function(e) {
		var triggerOn;
		$(".essb-wizard-menu").find(".essb-menu-item").each(function(){
			if ($(this).hasClass('active')) {
				if ($(this).next().length) {
					triggerOn = $(this).next();					
				}
			}
		});

		if ($(triggerOn).length)
			$(triggerOn).trigger('click');
	});

	$('#prevbutton').on('click', function(e) {
		var triggerOn;
		$(".essb-wizard-menu").find(".essb-menu-item").each(function(){
			if ($(this).hasClass('active')) {
				if ($(this).prev().length) {
					triggerOn = $(this).prev();					
				}
			}
		});

		if ($(triggerOn).length)
			$(triggerOn).trigger('click');
	});
	
	
	$(".essb-wizard-menu").find(".essb-menu-item").first().trigger('click');

	$('#essb_options_functions_mode_mobile').on('change', function(e) {
		var selectedMode = $(this).val();

		if (selectedMode == 'auto' || selectedMode == 'deactivate') {
			$('#essb-wizard-mobile-auto').show();
			$('#essb-wizard-mobile-manual').hide();
		}
		else {
			$('#essb-wizard-mobile-auto').hide();
			$('#essb-wizard-mobile-manual').show();
		}
	});

	$('#essb_options_functions_mode').on('change', function(e) {
		var selectedMode = $(this).val();

		if (selectedMode == 'light') {
			$('#essb-wizard-subscribe-auto').show();
			$('#essb-wizard-subscribe-manual').hide();
		}
		else {
			$('#essb-wizard-subscribe-auto').hide();
			$('#essb-wizard-subscribe-manual').show();
		}

		if (selectedMode == 'light' || selectedMode == 'medium' || selectedMode == 'advanced') {
			$('#essb-wizard-follow-auto').show();
			$('#essb-wizard-follow-manual').hide();
		}
		else {
			$('#essb-wizard-follow-auto').hide();
			$('#essb-wizard-follow-manual').show();
		}
	});

	$('#essb_options_functions_mode_mobile').trigger('change');
	$('#essb_options_functions_mode').trigger('change');

	$('.essb-wizard-buttons').scrollToFixed( {marginTop: 30 });
});

var wizardTab = true;
