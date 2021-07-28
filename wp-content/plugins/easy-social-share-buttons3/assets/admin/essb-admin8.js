jQuery(document).ready(function( $ ){
	"use strict";

	/**
	 * Tooltips
	 */
	$('.essb-vertical-blocks-nav .nav-block').hover(function(e){ // Hover event
		var titleText = $(this).attr('title'),
			screenPos = $(this).offset();
		$(this).data('tiptext', titleText).removeAttr('title');
		$('<p class="tooltip"></p>').text(titleText).appendTo('body').css('top', (screenPos.top - 5) + 'px').css('left', (screenPos.left + 45) + 'px').fadeIn('200');
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
		if ($(this).parent().hasClass('active-submenu')) {
			if ($(this).find('.essb-inner-menu').length && !$(this).find('.essb-inner-menu li.active').length) {
				$(this).find('.essb-inner-menu li').removeClass('active');
				$(this).find('.essb-inner-menu li').first().trigger('click');
			}
			
			if ($('.essb-options-subtitle').length && $(this).data('title')) $('.essb-options-subtitle').text($(this).data('title'));
		}
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
});