jQuery(document).ready(function( $ ){
	"use strict";
	
	function essbGetParameterByName(name, url = window.location.href) {
	    name = name.replace(/[\[\]]/g, '\\$&');
	    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}
	
	/**
	 * Experimental - vertical scrolling for the screens with too many items inside
	 */
	/*if ($(window).width() > 1024 && $('.essb-inner-navigation').length) {
		var barHeight = ($('#wpadminbar').length) ? $('#wpadminbar').height() : 0;
		
		$('.essb-inner-navigation').css('max-height', $(window).height() - barHeight);
		$('.essb-inner-navigation').addClass('essb-inner-scrolly');
		
		$(window).on('resize', function() {
			var barHeight = ($('#wpadminbar').length) ? $('#wpadminbar').height() : 0;
			
			$('.essb-inner-navigation').css('max-height', $(window).height() - barHeight);
			
			if ($(window).width() > 1024)
				$('.essb-inner-navigation').addClass('essb-inner-scrolly');
			else
				$('.essb-inner-navigation').removeClass('essb-inner-scrolly');
		});
	}*/
	
	/**
	 * Tooltips
	 */
	$('.essb-vertical-blocks-nav .nav-block').hover(function(e){ // Hover event
		var titleText = $(this).attr('title'),
			desc = $(this).attr('data-description') || '',
			tooltip = titleText,
			screenPos = $(this).offset();
		
		if (desc != '') tooltip += '<span class="desc">' + desc + '</span>';
		
		$(this).data('tiptext', titleText).removeAttr('title');
		$('<p class="tooltip"></p>').html(tooltip).appendTo('body').css('top', (screenPos.top - 5) + 'px').css('left', (screenPos.left + 45) + 'px').fadeIn('200');
	}, function(){ // Hover off event
		$(this).attr('title', $(this).data('tiptext'));
		$('.tooltip').remove();
	}).mousemove(function(e){ // Mouse move event
		//$('.tooltip').css('top', (e.pageY - 20) + 'px').css('left', (e.pageX + 20) + 'px');
	});
	
	$('.essb-vertical-blocks-nav .nav-block').on('click', function(e) {
		e.preventDefault();
		
		$('.essb-vertical-blocks-nav .nav-block.active').removeClass('active');
		$(this).addClass('active');
		
		var menuBlockID = $(this).data('block');
		
		$('.essb-primary-navigation').removeClass('active');
		$('#block-' + menuBlockID).addClass('active');
	});
	
	$('.essb-inner-navigation .essb-inner-menu li').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$(this).closest('.essb-inner-menu').find('li').removeClass('active');
		$(this).addClass('active');
		
		$('.essb-child-section').hide(50);
		var optionsChildID = $(this).data('tab') || '';
		if (optionsChildID != '') $('.essb-child-section-' + optionsChildID).show(100);
		$('#essb_options_form #subsection').val(optionsChildID);
		
		essb_refresh_editors();
	});
	
	$('.essb-submenu-item').on('click', function(e){
		if ($(this).find('.essb-inner-menu') && $(this).find('.essb-inner-menu li.active').length) {
			$(this).find('.essb-inner-menu li.active').trigger('click');
		}

		if ($(this).parent().hasClass('active-submenu')) {
			if ($(this).find('.essb-inner-menu').length && !$(this).find('.essb-inner-menu li.active').length) {
				$(this).find('.essb-inner-menu li').removeClass('active');
				$(this).find('.essb-inner-menu li').first().trigger('click');
			}
			
			if ($('.essb-options-subtitle').length && $(this).data('title')) $('.essb-options-subtitle').text($(this).data('title'));
		}		
	});
	
	$('.essb-usefull-hint-positions .essb-enable-positions').on('click', function(e) {
		e.preventDefault();
		
		var userWidth = '',
			settings = 'features',
			title = 'Manage Plugin Features';
		
		essbAdvancedOptions.settings = settings;
		essbAdvancedOptions.requireReload = true;
		essbAdvancedOptions.withoutSave = false;
		
		essbAdvancedOptions.correctWidthAndPosition(userWidth);
		
		if (essbAdvancedOptions.withoutSave) {
			$('#essb-advancedoptions .advancedoptions-save').hide();
		}
		else{
			$('#essb-advancedoptions .advancedoptions-save').show();
		}

		$('#essb-advanced-options-form').html('');
		$('.advancedoptions-modal').fadeIn();
		$('#essb-advancedoptions').fadeIn();
		$('#advancedOptions-title').text(title);
		essbAdvancedOptions.read('get', { 'settings': settings, 'loadingOptions': {}  }, function(content) {			
			essbAdvancedOptions.load(content);
			
			$('.features-container .navigation [data-tab="display"]').trigger('click');
		});
		//essbAdvancedOptions.show('features', true, 'Manage Plugin Features', false, {});
	});
	
	/**
	 * Post loading data
	 */
	var activeSection = $('#essb_options_form #section').val() || '',
	activeTab = $('#essb_options_form #tab').val() || '';

	if (!$('.essb-cc-' + activeTab + '-' + activeSection).length) activeSection = '';

	if (!activeSection) activeSection = $('.essb-primary-navigation .active-submenu .essb-submenu-item').first().data('submenu') || '';
	
	if ($('.essb-cc-' + activeTab + '-' + activeSection + ' .essb-inner-menu')) {
		var presetSubsection = essbcc_strings && essbcc_strings.load_subsection ? essbcc_strings.load_subsection : '';
		essbcc_strings.load_subsection = ''; // clear the loading section to prevent multiple loading instances
		if (presetSubsection && $('.essb-cc-' + activeTab + '-' + activeSection + ' .essb-inner-menu li.essb-inner-menu-item-'+presetSubsection).length) {
			$('.essb-cc-' + activeTab + '-' + activeSection + ' .essb-inner-menu li.essb-inner-menu-item-'+presetSubsection).trigger('click');
		}
		else {
			$('.essb-cc-' + activeTab + '-' + activeSection + ' .essb-inner-menu li').first().trigger('click');
		}
	}
	
	/**
	 * Display conditions (relations)
	 */

	if (essbFieldConditions) {
		for (var section in essbFieldConditions) {
			for (var field in essbFieldConditions[section]) {
				var type = essbFieldConditions[section][field].type || '',
					connected = essbFieldConditions[section][field].fields || [];
								
				if (type == 'switch' && $('#essb-container-' + section + ' #essb_field_' + field).length) {
					$('#essb-container-' + section + ' #essb_field_' + field).attr('data-condition', field);
					$('#essb-container-' + section + ' #essb_field_' + field).attr('data-section', section);
					$('#essb-container-' + section + ' #essb_field_' + field).on('change', function(e) {
						
						var conditionField = $(this).attr('data-condition') || '',
							sectionField = $(this).attr('data-section') || '';					
						
						if (conditionField == '' || !essbFieldConditions || !essbFieldConditions[sectionField] || !essbFieldConditions[sectionField][conditionField]) return;
						
						var connectedToCondition = essbFieldConditions[sectionField][conditionField].fields || [];
						for (var i = 0; i < connectedToCondition.length; i++) {
							var connectedID = connectedToCondition[i] || '';
							if (!$('#essb-container-' + sectionField + ' .settings-panel-' + connectedID).length) continue;
							
							$('#essb-container-' + sectionField + ' .settings-panel-' + connectedID).css('display', $(this).is(':checked') ? 'inline-flex': 'none');
						}
					});
					
					$('#essb-container-' + section + ' #essb_field_' + field).trigger('change');
				}
			}
		}
	}
	
	/**
	 * Clear pre-compiled cache
	 */
	
	if (essbGetParameterByName('rebuild-resource') == 'true') {
		$.toast({
		    heading: 'Static CSS and Javascript files cache is cleared',
		    showHideTransition: 'fade',
		    icon: 'success',
		    position: 'bottom-right',
		    hideAfter: 5000
		});
	}
});