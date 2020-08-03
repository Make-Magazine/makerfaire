jQuery(document).ready(function( $ ){
	"use strict";
	
	var essbShortcodeWindowShow = window.essbShortcodeWindowShow = function(outsideSettings) {
		$('.essb-shortcode-overlay').fadeIn(200);
		$('.essb-shortcode-overlay .essb-shortcode-screen .window-content .navigation a').first().trigger('click');
		essbShortcodeWindowCorrectHeight();
		
		if (outsideSettings) 
			$('.essb-shortcode-screen .generate-options').hide();
		else
			$('.essb-shortcode-screen .generate-options').css({'display' : 'flex'});
	};

	var essbShortcodeWindowCorrectHeight = window.essbShortcodeWindowCorrectHeight = function() {
		if (!$('.essb-shortcode-screen').length) return;
		
		var windowHeight = $(window).height(),
			screenHeight = $('.essb-shortcode-screen').outerHeight(),
			headingHeight = $('.essb-shortcode-screen .heading').outerHeight();
		
		// correcting screen height
		if (screenHeight > (windowHeight - 50)) {
			$('.essb-shortcode-screen').css({'height': (windowHeight - 50) + 'px'});
			screenHeight = windowHeight - 50;
		}
		
		$('.essb-shortcode-screen .window-content .navigation').css({'height': (screenHeight - headingHeight) + 'px'});
		$('.essb-shortcode-screen .window-content .content').css({'height': (screenHeight - headingHeight) + 'px'});
	};
	
	var essbShortcodeGeneratorRemove = window.essbShortcodeGeneratorRemove = function(sender) {
		var key = sender.getAttribute('data-ukey') || '';
		if (key != '') {
			var remotePost = { 'shortcode_key': key };
			essbAdvancedOptions.post('shortcode_remove', remotePost, function(data) {
				$('.essb-shortcode-screen .window-content .shortcode-list').html(data);
			});
		}
	};
	
	var essbShortcodeGeneratorEdit = window.essbShortcodeGeneratorEdit = function(sender) {
		var key = sender.getAttribute('data-ukey') || '';
		if (key != '') {
			var remotePost = { 'key': key };
			essbAdvancedOptions.post('shortcode_get', remotePost, function(data) {
				if (!data || data == '') return;
				
				data = JSON.parse(data || '{}');
				if (data.shortcode) {
					var rootPath = '.essb-shortcode-screen .shortcode-options.active';
					$('.essb-sc-menu-' + data.shortcode).trigger('click');
					$(rootPath + ' .shortcode-store').attr('checked', true);
					$(rootPath + ' .shortcode-name').val(data.name || '');
					$(rootPath).attr('data-ukey', key);
					
					$(rootPath + ' input, '+rootPath+' select, '+rootPath+' textarea').each(function() {
						var param = $(this).data('param') || '';
						
						if (param == '' || !data.settings[param]) return;				
						$(this).val(data.settings[param] || '');
					});
					
					$('.essb-shortcode-screen #generated_shortcode_profiles_all_networks').trigger('change');
				}
			});
		}
	};
	
	var essbShortcodeFocusSelect = window.essbShortcodeFocusSelect = function(sender) {
		var element = sender;
		if (document.body.createTextRange) {
			var range = document.body.createTextRange();
		    range.moveToElementText(element);
		    range.select();
		} else if (window.getSelection) {
			var selection = window.getSelection();        
		    var range = document.createRange();
		    range.selectNodeContents(element);
		    selection.removeAllRanges();
		    selection.addRange(range);
		}
	};
	
	$('.essb-shortcode-screen .window-content .navigation a').on('click', function(e) {
		e.preventDefault();
		
		$('.essb-shortcode-screen .window-content .navigation a').removeClass('active');
		$(this).addClass('active');
		
		var code = $(this).data('code') || '';
		$('.essb-shortcode-screen .shortcode-options').fadeOut(150);
		$('.essb-shortcode-screen .shortcode-options').removeClass('active');
		$('.essb-shortcode-screen .shortcode-' + code).fadeIn(150);
		$('.essb-shortcode-screen .shortcode-' + code).addClass('active');
		$('.essb-shortcode-screen .shortcode-' + code).data('ukey', '');
		$('.essb-shortcode-screen .shortcode-generated').hide();
	});
	
	$('.essb-shortcode-screen .heading .close').on('click', function(e) {
		e.preventDefault();
		
		$('.essb-shortcode-overlay').hide();
	});
	
	$('.essb-shortcode-screen .heading .generate').on('click', function(e) {
		e.preventDefault();
		
		$('.essb-shortcode-screen .shortcode-generated').fadeOut(200);
		$('.essb-shortcode-screen .shortcode-result').html('');
		$('.essb-shortcode-screen .shortcode-embed-result').html('');
		
		var rootPath = '.essb-shortcode-screen .shortcode-options.active',
			shortcode = $(rootPath).data('code') || '', options = {},
			storeOptionExist = $(rootPath + ' #shortcode-store').length,
			code = '';
		
		$(rootPath + ' input, '+rootPath+' select, '+rootPath+' textarea').each(function() {
			var value = $(this).val(), param = $(this).data('param') || '';
			
			if (param == '' || value == '') return;				
			options[param] = value;
		});
		
		if ($(rootPath + ' .essb-sc-networks').length) {
			var networks = [], param = $(rootPath + ' .essb-sc-networks').data('param') || '';
			$(rootPath + ' .essb-sc-networks .sc-network-select').each(function() {
				if ($(this).is(':checked')) networks.push($(this).val());
			});
			
			if (networks.length > 0) options[param] = networks.join(',');
		}
		
		if (storeOptionExist && $(rootPath + ' .shortcode-store').is(':checked')) {
			var storeName = $(rootPath + ' .shortcode-name').val();
			if (storeName == '') {
				swal({ 'title': 'Missing shortcode saving name', 'text': 'To save the generated shortcode for further usage you should fill the name field. The name field is used just as a reference but is required.', 'icon': 'error'});
				return;
			}
			
			// requesting generation of the shortcode
			var remotePost = {
				'name': storeName,
				'options': options,
				'shortcode': shortcode,
				'key': $(rootPath).attr('data-ukey') || ''
			};
			
			essbAdvancedOptions.post('shortcode_save', remotePost, function(data) {
				if (data && data != '') data = JSON.parse(data || '{}');
				if (data && data.key) {
					code = '[' + shortcode + ' ukey="'+data.key+'"]';
					$('.essb-shortcode-screen .shortcode-generated').fadeIn(200);
					$('.essb-shortcode-screen .shortcode-result').html(code);
					$('.essb-shortcode-screen .shortcode-result').attr('contenteditable', 'true');
					$('.essb-shortcode-screen .shortcode-result').focus();
					
					$('.essb-shortcode-screen .shortcode-embed-result').html('&lt;?php echo do_shortcode(\''+code+'\'); ?&gt;');
					
					$(rootPath).attr('data-ukey', data.key);
					
					var element = document.querySelector('.essb-shortcode-screen .shortcode-result');
					if (document.body.createTextRange) {
						var range = document.body.createTextRange();
					    range.moveToElementText(element);
					    range.select();
					} else if (window.getSelection) {
						var selection = window.getSelection();        
					    var range = document.createRange();
					    range.selectNodeContents(element);
					    selection.removeAllRanges();
					    selection.addRange(range);
					}
				}
			});
			
		}
		else {
			code = '[' + shortcode;
			
			let isSharingCode = ['social-share', 'easy-social-share', 'easy-social-share-popup'].indexOf(shortcode);
			
			for (var param in options) {
				
				let value = options[param] || '';
				if (isSharingCode && (param == 'counter' || param == 'counters')) {
					code += ' ' + param + '=' + (value == 'yes' ? '1': '0') + '';
				}
				else {
					code += ' ' + param + '="' + value + '"';
				}
			}
			code += ']';
			
			$('.essb-shortcode-screen .shortcode-generated').fadeIn(200);
			$('.essb-shortcode-screen .shortcode-result').html(code);
			$('.essb-shortcode-screen .shortcode-result').attr('contenteditable', 'true');
			$('.essb-shortcode-screen .shortcode-result').focus();
			
			$('.essb-shortcode-screen .shortcode-embed-result').html('&lt;?php echo do_shortcode(\''+code+'\'); ?&gt;');
			
			
			var element = document.querySelector('.essb-shortcode-screen .shortcode-result');
			if (document.body.createTextRange) {
				var range = document.body.createTextRange();
			    range.moveToElementText(element);
			    range.select();
			} else if (window.getSelection) {
				var selection = window.getSelection();        
			    var range = document.createRange();
			    range.selectNodeContents(element);
			    selection.removeAllRanges();
			    selection.addRange(range);
			}
		}
	});
	
	if ($('.essb-shortcode-screen .generate-options').length) {
		$('.essb-shortcode-screen .generate-options').on('click', function(e) {
			e.preventDefault();
			
			window.open($(this).data('url'));
		});
	};
	
	if ($('.essb-shortcode-screen .shortcode-list').length) {
		$('.essb-shortcode-screen .generated-list').css({'display': 'flex'});
		$('.essb-shortcode-screen .generated-list').on('click', function(e) {
			e.preventDefault();
			
			$('.essb-shortcode-screen .shortcode-generated').fadeOut(200);
			$('.essb-shortcode-screen .shortcode-result').html('');
			
			$('.essb-shortcode-screen .window-content .navigation a').removeClass('active');			
			var code = 'list';
			$('.essb-shortcode-screen .shortcode-options').fadeOut(150);
			$('.essb-shortcode-screen .shortcode-options').removeClass('active');
			$('.essb-shortcode-screen .shortcode-' + code).fadeIn(150);
			$('.essb-shortcode-screen .shortcode-' + code).addClass('active');
			$('.essb-shortcode-screen .shortcode-' + code).data('ukey', '');
			
			essbAdvancedOptions.post('shortcode_list', {}, function(data) {
				$('.essb-shortcode-screen .window-content .shortcode-list').html(data);
			});
		});
	}
	
	if ($('.essb-shortcode-screen #generated_shortcode_profiles_all_networks').length) {
		$('.essb-shortcode-screen #generated_shortcode_profiles_all_networks').on('change', function() {
			if ($(this).val() == 'no') {
				$('.essb-shortcode-screen .shortcode-all-profile-networks').hide();
			}
			else {
				$('.essb-shortcode-screen .shortcode-all-profile-networks').show();
			}
		});
	}
		
	
	$(window).on('resize', essbShortcodeWindowCorrectHeight);
	
	$('.essb-cc-shortcode').on('click', function(e) {
		e.preventDefault();
		essbShortcodeWindowShow();
	});
	
	$('.essb-open-shortcodes').on('click', function(e) {
		e.preventDefault();
		essbShortcodeWindowShow();
	});
	
	$('#wp-admin-bar-essb_top_shortcodegen a').on('click', function(e) {
		e.preventDefault();
		essbShortcodeWindowShow();
	});
	
	$(document).on('click', '.mce-i-essb-sc-generator', function(e){
		e.preventDefault();
		essbShortcodeWindowShow(true);
	});
	
	$(document).on('click', '.essb-mce-button', function(e){
		e.preventDefault();
		essbShortcodeWindowShow(true);
	});
});