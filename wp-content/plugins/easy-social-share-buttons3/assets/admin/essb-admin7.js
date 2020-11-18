jQuery(document).ready(function( $ ){
	"use strict";
	
	var essb_refresh_editors = window.essb_refresh_editors = function() {
		if (loadedEditorControls) {
			for (var key in loadedEditorControls) {
				loadedEditorControls[key].refresh();
			}
 		}
	};
	
	$('.essb-menu-item').on('click', function(e){
		
		if ($(this).hasClass('active')) {
			e.preventDefault();
	
			$('.essb-menu-item').removeClass('active', 500);
			$(this).addClass('active');
			essb_refresh_editors();
		}
	});

	$('.essb-submenu-item').on('click', function(e){
		if ($(this).parent().hasClass('active-submenu')) {
			e.preventDefault();
	
			$('.essb-submenu-item').removeClass('active', 500);
			$(this).addClass('active');
			
			$('#essb_options_form #section').val($(this).data('submenu'));
			var optionsSectionID = 'essb-container-' + $(this).data('submenu');
			$('.essb-data-container').hide();
			$('#' + optionsSectionID).show();
			
			if ($('#' + optionsSectionID).length) {
				essb_fix_width_of_radio_and_checkboxes('#' + optionsSectionID);
				essb_activate_code_editors('#' + optionsSectionID);
				$("#essb-scroll-top").scrollintoview({ duration: "slow", direction: "y"});
			}
			
			if ($('.essb-options-subtitle').length) $('.essb-options-subtitle').text($(this).text());
			
			$('#essb_options_form #subsection').val('');
			$('#essb_options_form #section').val($(this).data('submenu'));
			
			// Performing an additional check about existing internal menus. If so plugin will activate the first tab
			// when such is not present inside it
			if ($('#' + optionsSectionID + ' .essb-inner-menu').length) {
				var presetSubsection = essbcc_strings && essbcc_strings.load_subsection ? essbcc_strings.load_subsection : '';
				essbcc_strings.load_subsection = ''; // clear the loading section to prevent multiple loading instances
				if (presetSubsection && $('.essb-control-content .essb-control-inner .essb-inner-menu li.essb-inner-menu-item-'+presetSubsection).length)
					$('.essb-control-content .essb-control-inner .essb-inner-menu li.essb-inner-menu-item-'+presetSubsection).trigger('click');
				else
					$('.essb-control-content .essb-control-inner #' + optionsSectionID+' .essb-inner-menu li').first().trigger('click');
			}
			
			essb_refresh_editors();
		}
	}); 
	
	$('.essb-control-content .essb-control-inner .essb-inner-menu li').on('click', function(e){
		e.preventDefault();
		
		$(this).closest('.essb-inner-menu').find('li').removeClass('active');
		$(this).addClass('active');
		
		$('.essb-child-section').hide(50);
		var optionsChildID = $(this).data('tab') || '';
		if (optionsChildID != '') $('.essb-child-section-' + optionsChildID).show(100);
		$('#essb_options_form #subsection').val(optionsChildID);
		
		essb_refresh_editors();
	});
	
	$('.essb-control-btn-save').on('click', function(e) {
		e.preventDefault();
		
		if ($('#essb-cc-preloader').length) $('#essb-cc-preloader').fadeIn(100);
		if ($('#essb-btn-update').length) $('#essb-btn-update').trigger('click');
	});

	var activeSection = $('#essb_options_form #section').val() || '',
		activeTab = $('#essb_options_form #tab').val() || '';
	
	if (!$('.essb-cc-' + activeTab + '-' + activeSection).length) activeSection = '';
	
	if (!activeSection) activeSection = $('.essb-primary-navigation .active-submenu .essb-submenu-item').first().data('submenu') || '';
	$('.essb-cc-' + activeTab + '-' + activeSection).trigger('click');
	
	
	if ($('.essb-control-top').length) $('.essb-control-top').scrollToFixed( { marginTop: 32 } );
	
	if (essbcc_strings && !essbcc_strings.deactivate_action_save) {
		if ($('#essb-btn-update').length && $('#essb_options_form').length) {
			var frmSettings = $('#essb_options_form');

			$(frmSettings).submit(function (e) {

				if (typeof(essbWizardIsRunning) == 'undefined') var essbWizardIsRunning = false;
				
				if (essbWizardIsRunning) {
					e.preventDefault();
					return;
				}

				if (typeof(essb_disable_ajax_submit) == "undefined") essb_disable_ajax_submit = false;
				if (!essb_disable_ajax_submit) {
			        e.preventDefault();

			        if ($('.tmce-active').length) {
				        try {
					        console.log('MCE calling save');
				         	tinyMCE.triggerSave();
				        }
				        catch (e) {
				        }
			        }

					// updating codemirror before save
					$('.is-code-editor').each(function(){
						var elementId = $(this).attr('data-editor-key') || '';
						if (typeof(loadedEditorControls[elementId] != 'undefined')) {
							try {
								loadedEditorControls[elementId].save();
							}
							catch (e) {
							}
						}
					});
					
					$.ajax({
			            type: frmSettings.attr('method'),
			            url: frmSettings.attr('action'),
			            data: frmSettings.serialize(),
			            success: function (data) {
			            	$('#essb-cc-preloader').fadeOut(100)
			                swal({
				                title: essbcc_strings.setup_save,
						        icon: 'success',
						        text: essbcc_strings.setup_save_desc,
						        className: "essb-swal",
			                });

			            }
			        });

				}
			});
		}
	}
	
	/**
	 * Events and code appearing only inside the setup options
	 */
	if ($('.essb-settings-wrap').length) {
		/**
		 * Activating mobile setup
		 */
		$('.toggleselect-item-functions_mode_mobileadvanced').on('click', function(e) {
			var deactivate_id = 'mobile_positions',
				custom_deactivate_value = 'true';
		
			if ($('#advancedoptions-preloader').length) $('#advancedoptions-preloader').fadeIn(100);
					
			$('#essb_options_form').append('<input type="hidden" name="essb_options['+deactivate_id+']" id="essb_'+deactivate_id+'" value="'+custom_deactivate_value+'" />');
			essb_disable_ajax_submit = true;
			$('#essb_options_form').submit();
		});
		
		$('#essb_options_functions_mode_mobile').on('change', function(e) {
			if ($(this).val() == 'auto') $('#functions_mode_mobile_auto').show();
			else $('#functions_mode_mobile_auto').hide();
		});
		
		$('#essb_options_subscribe_function').on('change', function(e) {
			$('.essb-subscribe-function-link').hide();
			$('.essb-subscribe-function-form').hide();
			$('.essb-subscribe-function-mailchimp').hide();
			
			var activateSection = $(this).val();
			$('.essb-subscribe-function-' + activateSection).show();
			essb_refresh_editors();
		});
		
		$('.essb-posttype-selection li input').on('change', function(e) {
			$('.essb-position-posttype').hide();
			
			$('.essb-posttype-selection li input').each(function() {
				if ($(this).is(':checked')) {
					var type = $(this).attr('id') || '';
					$('#essb-position-posttype-' + type).show();
				}
			});
		});
		
		$('#essb_options_cct_template').on('change', function(e){
			var r = [], template = $(this).val();
			r.push('<div class="essb-ctt '+(template != '' ? ' essb-ctt-'+template : '')+'">');
			r.push('<span class="essb-ctt-quote">Add an awesome looking click to tweet boxes from the best social sharing plugin for #WordPress</span>');
			r.push('<span class="essb-ctt-button"><span>Click to Tweet</span><i class="essb_icon_twitter"></i></span>');
			r.push('</div>');
			
			if ($('.essb-clicktotweet-preview').length) {
				$('.essb-clicktotweet-preview').css('max-width', '800px');
				$('.essb-clicktotweet-preview').css('margin', '0 auto');
				$('.essb-clicktotweet-preview').html(r.join(''));
			}
		});
		
		$('#essb_options_mail_function').on('change', function(e) {
			if ($(this).val() == 'form') {
				$('#essb-setup-mail-function').show();
			}
			else {
				$('#essb-setup-mail-function').hide();
			}
		});
		
		$('#essb_options_optimize_load').on('change', function() {
			if ($(this).val() == 'post') {
				$('.ao-panel-optimize_load_id').show();
			}
			else {
				$('.ao-panel-optimize_load_id').hide();
			}
		});
		
		$('#essb_field_precompiled_resources').on('change', function() {
			if ($(this).is(':checked')) {
				$('.settings-panel-precompiled_mode').show();
				$('.settings-panel-precompiled_folder').show();
				$('.ao-panel-precompiled_unique').show();
				$('.ao-panel-precompiled_footer').show();
				$('.ao-panel-precompiled_preload_css').show();
			}
			else {
				$('.settings-panel-precompiled_mode').hide();
				$('.settings-panel-precompiled_folder').hide();
				$('.ao-panel-precompiled_unique').hide();
				$('.ao-panel-precompiled_footer').hide();
				$('.ao-panel-precompiled_preload_css').hide();
			}
		});
		
		// Default after load events
		$('#essb_options_subscribe_function').trigger('change');
		$('.essb-posttype-selection li input').trigger('change');
		$('#essb_options_cct_template').trigger('change');
		$('#essb_options_functions_mode_mobile').trigger('change');
		$('#essb_options_mail_function').trigger('change');
		$('#essb_options_optimize_load').trigger('change');
		$('#essb_field_precompiled_resources').trigger('change');
		
		if ($('.essb-select2').length) {
			$('.essb-select2').each(function() {
				var currentValues = $(this).attr('data-values') || '';
				$(this).css('width', '100%');
				$(this).select2();
				$(this).val(currentValues.split(','));
				$(this).trigger('change');
			});
		}
	}
	
	/*
	 * Profiles widget
	 */
	var $container = $( '.widgets-holder-wrap, .editwidget, .wp-core-ui' );
	
	// Open the media library frame when the button or image are clicked.
	$container.on( 'change', '.essb-profiles-widget-trigger-all', function( e ) {
		e.preventDefault();
		if ($(this).is(':checked')) {
			$($container).find('.essb-profiles-widget-all-networks-list').show();
		}
		else {
			$($container).find('.essb-profiles-widget-all-networks-list').hide();
		}
	});
	
	
	/*
	 * Shortcodes
	 */
	
	$('.essb-shortcode-list .shortcode-block').on('click', function(e) {
		e.preventDefault();
		
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$(this).find('.shortcode-state i').removeClass('fa-chevron-down');
			$(this).find('.shortcode-state i').addClass('fa-chevron-right');
		}
		else {
			$(this).addClass('active');
			$(this).find('.shortcode-state i').addClass('fa-chevron-down');
			$(this).find('.shortcode-state i').removeClass('fa-chevron-right');
		}
	});
	
	$('.essb-options-helprow .help-heading .help-action-btn').on('click', function(e) {
		e.preventDefault();
		
		var parent = $(this).closest('.essb-options-helprow');
		if (!$(parent).length) return;
		
		var helpURL = $(parent).data('url') || '';
		if (helpURL != '') {
			if (typeof aoHelpBeaconAvailable != 'undefined' && aoHelpBeaconAvailable()) aoHelpBeaconDispatch(helpURL);
			else window.open(helpURL);
		}
		else{
			if ($(parent).find('.help-details').hasClass('opened')) {
				$(parent).find('.help-details').removeClass('opened');
				$(parent).find('.help-details').fadeOut(200);
			}
			else {
				$(parent).find('.help-details').fadeIn(200);
				$(parent).find('.help-details').addClass('opened');
			}
		}
	});
	
	if ($('#easy-social-share-buttons-for-wordpress-update').length) {
		$('[data-slug="easy-social-share-buttons-for-wordpress"]').addClass('update');
	}
	
	/**
	 * Refresh the state of code editors if not loaded
	 */
	
	var essbAdminRefreshCodeEditors = function() {
		
		var activeMenu = $('.essb-submenu-item.active');
		if (!$(activeMenu).length) return;
		
		var activeSection = $('#essb_options_form #section').val(),
			optionsSectionID = 'essb-container-' + activeSection;
		
		if ($('#' + optionsSectionID).length) {
			essb_activate_code_editors('#' + optionsSectionID);
		}
		
	}
});