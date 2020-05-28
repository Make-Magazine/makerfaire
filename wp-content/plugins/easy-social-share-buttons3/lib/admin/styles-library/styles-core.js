jQuery(document).ready(function($){
	"use strict";
	
	jQuery.fn.extend({
        centerWithAdminBar: function () {
            return this.each(function() {
                var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
                var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;

                if (jQuery('#wpadminbar').length)
                	top = top + (jQuery('#wpadminbar').height() / 2);

                jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    });
	
	if (typeof(essb_styles_ajaxurl) == 'undefied') return;

	var essbStylesManager = window.essbStylesManager = {
		ajax_url: essb_styles_ajaxurl || '',
		debug_mode: false,
		styles: null,
		location: '',
		selected_key: '',
		locationReload: false
	};

	essbStylesManager.post = function(action, options, callback) {
		if (!options) options = {};
		options['action'] = 'essb_style_library';
		options['cmd'] = action;
		options['essb_styleoptions_token'] = $('#essb_styleoptions_token').length ? $('#essb_styleoptions_token').val() : '';

		if ($('#styles-preloader').length) $('#styles-preloader').fadeIn(100);

		$.ajax({
            type: "POST",
            url: essbStylesManager.ajax_url,
            data: options,
            success: function (data) {
            	if ($('#styles-preloader').length) $('#styles-preloader').fadeOut(100);
            	if (essbStylesManager.debug_mode) console.log(data);

	            if (callback) callback(data);
            }
    	});
	};

	essbStylesManager.read = function(action, options, callback) {
		if (!options) options = {};
		options['action'] = 'essb_style_library';
		options['cmd'] = action;
		options['essb_styleoptions_token'] = $('#essb_styleoptions_token').length ? $('#essb_styleoptions_token').val() : '';

		if ($('#styles-preloader').length) $('#styles-preloader').fadeIn(100);

		$.ajax({
            type: "GET",
            url: essbStylesManager.ajax_url,
            data: options,
            success: function (data) {
            	if ($('#styles-preloader').length) $('#styles-preloader').fadeOut(100);
            	if (essbStylesManager.debug_mode) console.log(data);

	            if (callback) callback(data);
            }
    	});
	};

	essbStylesManager.correctWidthAndPosition = function() {
		var baseWidth = 1200, wWidth = $(window).width(),
			wHeight = $(window).height(),
			winHeight = (wHeight - 100);

		if (wWidth < baseWidth) baseWidth = wWidth - 100;
		$('#essb-styleselect').css({'width': baseWidth + 'px', 'height': winHeight + 'px'});
		$('#essb-styleselect').centerWithAdminBar();

		if ($('#essb-styleselect').find('.essb-helper-popup-content').length) {
			var contentHolder = $('#essb-styleselect').find('.essb-helper-popup-content'),
				contentHolderHeight = $(contentHolder).actual('height'),
				contentOffsetCorrection = 90;

			$('#essb-styleselect').find('.essb-helper-popup-content').css({height: (winHeight - contentOffsetCorrection)+'px'});
			$('#essb-styleselect').find('.essb-helper-popup-content').css({overflowY: 'auto'});

		}
	};

	essbStylesManager.startConvert = function(forLocation) {
		essbStylesManager.correctWidthAndPosition();

		$('#styles-manage').hide();
		$('#styles-manage-new').show();
		$('#style-location-choose').hide();
		$('#style-preview-real').hide();
		$('#styles-list-screen').hide();

		$('.styles-modal').fadeIn();
		$('#essb-styleselect').fadeIn();

		if (!forLocation) forLocation = '';

		essbStylesManager.location = forLocation;
	};

	essbStylesManager.show = function(forLocation) {

		essbStylesManager.correctWidthAndPosition();

		$('#styles-manage').hide();
		$('#styles-manage-new').hide();
		$('#style-location-choose').hide();
		$('#style-preview-real').hide();
		$('#styles-list-screen').show();

		$('.styles-modal').fadeIn();
		$('#essb-styleselect').fadeIn();

		if (!forLocation) forLocation = '';

		essbStylesManager.location = forLocation;
		essbStylesManager.locationReload = (forLocation != '') ? true : false;

		essbStylesManager.read('get', {}, essbStylesManager.load);
	};

	essbStylesManager.close = function() {
		$('.styles-modal').fadeOut();
		$('#essb-styleselect').fadeOut(200);
	};

	essbStylesManager.load = function(json) {
		if (essbStylesManager.debug_mode) console.log(json);
		essbStylesManager.styles = JSON.parse(json || '{}');

		if (essbStylesManager.debug_mode) console.log(essbStylesManager.styles);

		essbStylesManager.updateCategories();
		essbStylesManager.updateStyles();
	};

	essbStylesManager.updateStyles = function(filterCat) {
		var output = [], styleObj;

		if (!filterCat) filterCat = '';

		for (var key in essbStylesManager.styles.styles) {
			styleObj = essbStylesManager.styles.styles[key] || {};

			if (!styleObj.options) continue;

			var styleCats = (styleObj.category || '').split(',');

			if (filterCat != '' && styleCats.indexOf(filterCat) == -1) continue;

			output.push('<div class="plugin-style" data-style-key="' + key + '">');
			output.push('<div class="header">');
			output.push('<div class="title">' + (styleObj['name'] || ''));
			output.push('<div class="icons">');

			if (styleObj.options.template) output.push('<span class="ti-palette" title="Configured Template"></span>');
			if (styleObj.options.networks) output.push('<span class="ti-sharethis" title="Configured Social Networks"></span>');
			if (styleObj.options.button_style) output.push('<span class="ti-layout-slider" title="Configured Button Style"></span>');
			if (styleObj.options.css_animations) output.push('<span class="ti-wand" title="Configured Animations"></span>');
			if (styleObj.options.show_counter) output.push('<span class="ti-stats-up" title="Showing Share Counters"></span>');

			output.push('</div>'); // icons
			output.push('</div>'); // title

			if (styleObj.location) {
				var locations = styleObj.location.split(',');
				output.push('<div class="locations">Recommended for usage at:');
				for (var i=0;i<locations.length;i++) {
					output.push('<span class="location">'+(essb_styles_positions_source[locations[i]] ? essb_styles_positions_source[locations[i]] : locations[i]) + '</span>');
				}
				output.push('</div>');
			}
			if (styleObj.desc) output.push('<div class="desc">' + styleObj.desc + '</div>');
			if (typeof(styleObj.generalOptions) != 'undefined') output.push('<div class="has-code">This style also contains additional options that will be added.</div>');

			output.push('</div>'); // header

			output.push('<div class="buttons">');
			output.push('<div class="preview-button apply-button" data-key="' + key + '"><i class="ti-download"></i><span>Apply This Style</span></div>');
			output.push('<div class="preview-button view-button" data-key="' + key + '"><i class="ti-eye"></i><span>Preview</span></div>');

			if ((styleObj.user || '') == 'true') output.push('<div class="preview-button remove remove-button" data-key="' + key + '"><i class="ti-trash"></i><span>Delete</span></div>');
			output.push('<div class="preview-button export-save-button" data-key="' + key + '"><i class="ti-save"></i><span>Export</span></div>');

			output.push('</div>'); // buttons

			output.push('</div>'); // plugin-style
		}

		$('#style-grid-container').html(output.join(''));

		// updating grid events after render

		$('#style-grid-container .remove-button').on('click', function(e) {
			e.preventDefault();

			var key = $(this).data('key') || '';

			swal({
				  title: "Are you sure you want to remove this style?",
				  text: "Once deleted, you will not be able to recover this style!",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
					  var remotePost = { 'style_id': key };
					  if (essbStylesManager.debug_mode) console.log(remotePost);

					  essbStylesManager.post('remove_style', remotePost, function(data) {
							essbStylesManager.updateAfterSave('');

							if (essbStylesManager.styles.styles[key]) delete essbStylesManager.styles.styles[key];

							essbStylesManager.updateCategories();
							essbStylesManager.updateStyles();

							$.toast({
							    heading: 'Style Removed',
							    text: 'The user style is removed from the library',
							    showHideTransition: 'fade',
							    icon: 'success',
							    position: 'bottom-right',
							    hideAfter: 5000
							});
						});
				  }
				});

		});

		$('#style-grid-container .apply-button').on('click', function(e) {
			e.preventDefault();

			var key = $(this).data('key') || '';

			essbStylesManager.selected_key = key;

			/**
			 * Controling will we apply the styles directly or will
			 * it show a selector for choosing location where we
			 * wish to apply (multiple locations are possible to be used
			 */
			if (essbStylesManager.location == '') {
				$('#styles-list-screen').hide(100);
				$('#style-location-choose').fadeIn(200);
			}
			else {
				/**
				 * Direct apply the style on the selected location
				 */
				essbStylesManager.applyToLocation(essbStylesManager.location, essbStylesManager.selected_key);
			}
		});

		$('#style-grid-container .export-save-button').on('click', function(e) {
			e.preventDefault();

			$('#styles-list-screen').hide(100);
			$('#style-export').fadeIn(200);

			var key = $(this).data('key') || '';

			if (essbStylesManager.styles.styles[key]) {
				$('#export-style-content').val(JSON.stringify(essbStylesManager.styles.styles[key]).replace(/\\\"/g, '\"').replace(/\\\"/g, '\"'));
			}
			else {
				$('#export-style-content').val('Style not found!');
			}

		});

		$('#style-grid-container .view-button').on('click', function(e) {
			e.preventDefault();

			var key = $(this).data('key') || '';
			essbStylesManager.selected_key = key;

			$('#styles-list-screen').hide(100);
			$('#style-preview-real').fadeIn(200);

			if (essbStylesManager.styles.styles[key]) {

				var opts = essbStylesManager.styles.styles[key];

				var networks = opts.options['networks'] || '';

				if (networks != '') {
					var list = networks.split(','),
						networksSource = [];

					for (var i=0;i<list.length;i++) {
						var netKey = list[i],
							value = opts.options[netKey + '_name'] || netKey || '';

						networksSource.push({ 'key': netKey, 'name': value });
					}

					if (networksSource.length > 0) {
						essb_managepreview_global_preview.networks = networksSource;
					}
					else {
						essb_managepreview_global_preview.networks = [ {'key': 'facebook', 'name': 'Facebook'}, {'key': 'twitter', 'name': 'Twitter'}, {'key': 'pinterest', 'name': 'Pinterest'}, {'key': 'linkedin', 'name': 'LinkedIn'}];
					}
				}

				$('#style-preview-title').text(opts['name'] || '');
				$('#style-preview-real-content input').each(function() {
					var type = $(this).attr('type') || '',
						id = $(this).attr('id') || '';

					if (type != 'checkbox') $(this).val('');
					else $(this).prop('checked', false);

					var basicID = id.replace('essb_options_managepreview_', '').replace('essb_field_managepreview_', ''),
						value = opts.options[basicID] || '';

					if (type == 'checkbox') $(this).prop('checked', value == 'true');
					else
						$(this).val(value);					

					$(this).trigger('change');
				});

				setTimeout(function() {
					$('#essb_field_managepreview_template').trigger('change');
				}, 1);
			}
		});
	};

	essbStylesManager.getCategoryCount = function(filterCat) {
		var r = 0, styleObj;

		for (var key in essbStylesManager.styles.styles) {
			styleObj = essbStylesManager.styles.styles[key] || {};

			if (!styleObj.options) continue;

			var styleCats = (styleObj.category || '').split(',');

			if (filterCat != '' && styleCats.indexOf(filterCat) == -1) continue;
			r++;
		}

		return r;
	};

	essbStylesManager.getAllCount = function() {
		var r = 0, styleObj;

		for (var key in essbStylesManager.styles.styles) {
			styleObj = essbStylesManager.styles.styles[key] || {};

			if (!styleObj.options) continue;
			r++;
		}

		return r;
	};

	essbStylesManager.updateCategories = function() {
		var output = [];
		output.push('<ul>');

		output.push('<li data-category="" class="active">Show All<span class="count">'+essbStylesManager.getAllCount()+'</span></li>');
		if (essbStylesManager.styles.categories) {
			for (var i=0;i<essbStylesManager.styles.categories.length;i++) {

				var cnt = essbStylesManager.getCategoryCount(essbStylesManager.styles.categories[i]);
				if (cnt == 0) continue;
				output.push('<li data-category="' + essbStylesManager.styles.categories[i]+'">' + essbStylesManager.styles.categories[i]+'<span class="count">'+cnt+'</span></li>');
			}
		}

		if (essbStylesManager.styles.userCategories) {
			for (var i=0;i<essbStylesManager.styles.userCategories.length;i++) {
				var cnt = essbStylesManager.getCategoryCount(essbStylesManager.styles.categories[i]);
				if (cnt == 0) continue;
				output.push('<li data-category="' + essbStylesManager.styles.userCategories[i]+'">' + essbStylesManager.styles.userCategories[i]+'<span class="count">'+cnt+'</span></li>');
			}
		}

		output.push('</ul>');

		if ($('#essb-styleselect .styles-cats .list').length) $('#essb-styleselect .styles-cats .list').html(output.join(''));

		$('#essb-styleselect .styles-cats .list li').on('click', function(e) {
			e.preventDefault();

			$('#essb-styleselect .styles-cats .list li').removeClass('active');
			$(this).addClass('active');

			var activeCat = $(this).data('category') || '';

			essbStylesManager.updateStyles(activeCat);
		});
	};

	essbStylesManager.updateAfterSave = function(data) {
		if (data != '') {
			data = JSON.parse(data) || {};
			for (var key in data) {
				essbStylesManager.styles.styles[key] = data[key];
			}
		};

		essbStylesManager.updateCategories();
		essbStylesManager.updateStyles();
	}

	essbStylesManager.convert = function() {
		var options = {}, styleName = $('#managestyle-new-name').val(), styleCat = $('#managestyle-new-cat').val(),
			styleLocation = $('#managestyle-new-recommend').val(),
			styleTags = $('#managestyle-new-tags').val(),
			styleDesc = $('#managestyle-new-desc').val();

		if (styleName == '') {
			$.toast({
			    heading: 'Missing Style Name',
			    text: 'Please fill style name before pressing again the save button',
			    showHideTransition: 'fade',
			    icon: 'error',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
			return;
		}

		var styleUniqueID = Math.random().toString(16).replace('0.', ''),
		remotePost = {
			'style_id': styleUniqueID,
			'style_name': styleName,
			'style_category': styleCat,
			'style_user': 'true',
			'style_location': styleLocation,
			'style_tags': styleTags,
			'style_desc': styleDesc,
			'original_location': essbStylesManager.location
		};

		if (essbStylesManager.debug_mode) console.log(remotePost);
		essbStylesManager.post('convert_style', remotePost, function(data) {

			// if saved return back to list of styles
			essbStylesManager.close();

			$.toast({
			    heading: styleName + ' Saved!',
			    text: 'Your new style is saved in the library',
			    showHideTransition: 'fade',
			    icon: 'success',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
		});
	}

	essbStylesManager.import = function() {
		var content = $('#import-style-content').val(),
			importOptions = JSON.parse(content || '{}') || {};

			if (!importOptions.options) {
				$.toast({
						heading: 'Wrong Style Code',
						text: 'The code you are trying to import is not valid or it is not in the proper format',
						showHideTransition: 'fade',
						icon: 'error',
						position: 'bottom-right',
						hideAfter: 5000
				});
				return;
			}

			// no matter style type we will force after import to set it as user style inside the user category and the import category
			importOptions['user'] = 'true';
			importOptions['category'] = 'User Styles';

			var styleUniqueID = Math.random().toString(16).replace('0.', ''),
				remotePost = {
					'style_id': styleUniqueID,
					'style_options': importOptions,
					'style_user': 'true',
				};

			essbStylesManager.post('import_style', remotePost, function(data) {
				essbStylesManager.updateAfterSave(data);

				// if saved return back to list of styles
				$('#style-import').hide(100);
				$('#styles-list-screen').fadeIn(200);

				essbStylesManager.updateCategories();
				essbStylesManager.updateStyles();

				$.toast({
				    heading: 'The new style is imported',
				    showHideTransition: 'fade',
				    icon: 'success',
				    position: 'bottom-right',
				    hideAfter: 5000
				});
			});
	}

	essbStylesManager.save = function() {

		var options = {}, styleName = $('#managestyle-name').val(), styleCat = $('#managestyle-cat').val(),
			styleLocation = $('#managestyle-recommend').val(),
			styleTags = $('#managestyle-tags').val(),
			styleDesc = $('#managestyle-desc').val();

		$('#styles-manage input').each(function() {
			var elementId = $(this).id || '',
				elementName = $(this).attr('name') || '',
				elementValue = $(this).val(),
				elementType = $(this).attr('type') || '';

			if (elementType == 'checkbox' || elementType == 'radio')
				elementValue = $(this).is(":checked") ? 'true': 'false';

			if (elementName == '') return;
			if (elementName.indexOf('essb_options') > -1) return;

			elementName = elementName.replace('manage_style', '').replace('[', '').replace(']', '').replace('managestyle_', '');
			options[elementName] = elementValue;
		});

		$('#styles-manage select').each(function() {
			var elementId = $(this).id || '',
				elementName = $(this).attr('name') || '',
				elementValue = $(this).val();

			if (elementName == '') return;
			if (elementName.indexOf('essb_options') > -1) return;

			elementName = elementName.replace('manage_style', '').replace('[', '').replace(']', '').replace('managestyle_', '');
			options[elementName] = elementValue;
		});

		if (styleName == '') {
			$.toast({
			    heading: 'Missing Style Name',
			    text: 'Please fill style name before pressing again the save button',
			    showHideTransition: 'fade',
			    icon: 'error',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
			return;
		}

		/**
		 * Generating list of selected social networks from the list
		 */
		var networkList = '', networkNames = {};
		$('#styles-manage .essb-component-networkselect-managestyle li').each(function() {
			var network = $(this).data('network') || '';

			if (network == '' || network == 'add') return;

			networkList += (networkList != '' ? ',' : '') + network;

			var currentName = $(this).find('.essb-single-network-name input').val();
			if (currentName != '') networkNames[network+'_name'] = currentName;
		});

		if (networkList != '') options['networks'] = networkList;
		for (var key in networkNames) {
			options[key] = networkNames[key] || '';
		}

		var styleUniqueID = Math.random().toString(16).replace('0.', ''),
			remotePost = {
				'style_id': styleUniqueID,
				'style_name': styleName,
				'style_category': styleCat,
				'style_options': options,
				'style_user': 'true',
				'style_location': styleLocation,
				'style_tags': styleTags,
				'style_desc': styleDesc
			};

		if (essbStylesManager.debug_mode) console.log(remotePost);
		essbStylesManager.post('save_style', remotePost, function(data) {
			essbStylesManager.updateAfterSave(data);

			// if saved return back to list of styles
			$('#styles-manage').hide(100);
			$('#styles-list-screen').fadeIn(200);

			essbStylesManager.updateCategories();
			essbStylesManager.updateStyles();

			$.toast({
			    heading: styleName + ' Saved!',
			    text: 'Your new style is saved in the library',
			    showHideTransition: 'fade',
			    icon: 'success',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
		});
	}

	essbStylesManager.applyToLocation = function(location, styleId) {

		var remotePost = { 'style_position' : location, 'style_id': styleId };

		//console.log(remotePost);

		essbStylesManager.post('apply', remotePost, function(data) {
			$.toast({
			    heading: (essb_styles_positions_source[location] ? essb_styles_positions_source[location] : location) + ' Applied!',
			    text: 'The selected settings are active now on the location',
			    showHideTransition: 'fade',
			    icon: 'success',
			    position: 'bottom-right',
			    hideAfter: 5000
			});

			if (essbStylesManager.locationReload) {
				setTimeout(function(){
					if (!essb_styles_reloadurl) return;
					var reload = essb_styles_reloadurl,
						section = $('#section').val(),
						subsection = $('#subsection').val();

					window.location.href = reload + (section != '' ? '&section='+section : '') + (subsection != '' ? '&subsection='+subsection : '');
				}, 2000);
			}
		});
	}

	essbStylesManager.apply = function() {
		var locations = [];

		$('#essb-styleselect .essb-multi-position-select .essb-single.active').each(function() {
			var position = $(this).data('value') || '';

			if (position == '') return;

			position = position.replace('content_', '');

			if (position == 'both') {
				if (locations.indexOf('top') == -1) locations.push('top');
				if (locations.indexOf('bottom') == -1) locations.push('bottom');
			}
			else
				locations.push(position);

		});

		if (locations.length > 0) {
			if ($('#styles-preloader').length) $('#styles-preloader').fadeIn(100);

			for (var i = 0; i < locations.length; i++) {
				essbStylesManager.applyToLocation(locations[i], essbStylesManager.selected_key);
			}

			if ($('#styles-preloader').length) $('#styles-preloader').fadeOut(100);

			$('.manage-back').click();
		}
		else {
			$.toast({
			    heading: 'Missing Selection',
			    text: 'You need to select at least one location where style can be applied',
			    showHideTransition: 'fade',
			    icon: 'error',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
		}
	}

	//-- actions assigned to components

	$('#essb-styleselect .styleselect-close').on('click', function(e) {
		e.preventDefault();
		essbStylesManager.close();
	});

	if ($('.essb-tab-readymade').length) {
		$('.essb-tab-readymade').on('click', function(e) {
			e.preventDefault();
			essbStylesManager.show();
		});
	}

	if ($('.essb-cc-readymade').length) {
		$('.essb-cc-readymade').on('click', function(e) {
			e.preventDefault();
			essbStylesManager.show();
		});
	}	
	
	if ($('.essb-style-apply').length) {
		$('.essb-style-apply').on('click', function(e) {
			e.preventDefault();

			var location = $(this).data('position') || '';
			essbStylesManager.show(location);
		});
	}

	if ($('.essb-style-save').length) {
		$('.essb-style-save').on('click', function(e) {
			e.preventDefault();

			var location = $(this).data('position') || '';
			essbStylesManager.startConvert(location);
			$('#managestyle-new-cat').val('User Styles');
		});
	}

	// attaching create new style button
	if ($('.create-new').length) {
		$('.create-new').on('click', function(e) {
			e.preventDefault();

			$('#styles-list-screen').hide(100);
			$('#styles-manage').fadeIn(200);

			$('#managestyle-cat').val('User Styles');
			$('#managestyle-action').val('new');
		});
	}

	$('.import-new').on('click', function(e){
		e.preventDefault();
		$('#styles-list-screen').hide(100);
		$('#style-import').fadeIn(200);
		$('#import-style-content').val('');
	});

	if ($('.manage-back').length) {
		$('.manage-back').on('click', function(e){
			e.preventDefault();

			$('#styles-manage').hide(100);
			$('#styles-manage-new').hide(100);
			$('#style-location-choose').hide(100);
			$('#style-preview-real').hide(100);
			$('#style-export').hide(100);
			$('#style-import').hide(100);
			$('#styles-list-screen').fadeIn(200);
		});
	}

	if ($('.manage-save').length) {
		$('.manage-save').on('click', function(e) {
			essbStylesManager.save();
		});
	}

	if ($('.manage-import-style').length) {
		$('.manage-import-style').on('click', function(e) {
			e.preventDefault();
			essbStylesManager.import();
		});
	}

	if ($('.manage-new-save').length) {
		$('.manage-new-save').on('click', function(e){
			e.preventDefault();
			essbStylesManager.convert();
		});
	}

	if ($('.manage-apply').length) {
		$('.manage-apply').on('click', function(e) {
			essbStylesManager.apply();
		});
	}

	if ($('.manage-apply-select').length) {
		$('.manage-apply-select').on('click', function(e) {
			e.preventDefault();

			/**
			 * Controling will we apply the styles directly or will
			 * it show a selector for choosing location where we
			 * wish to apply (multiple locations are possible to be used
			 */
			if (essbStylesManager.location == '') {
				$('#styles-list-screen').hide(100);
				$('#style-location-choose').fadeIn(200);
			}
			else {
				/**
				 * Direct apply the style on the selected location
				 */
				essbStylesManager.applyToLocation(essbStylesManager.location, essbStylesManager.selected_key);
			}
		})
	}

	if ($('#essb_options_change-orientation').length) {
		$('#essb_options_change-orientation').on('change', function() {
			var value = $(this).val();

			if (value != '') $('.essb-style-livepreview').addClass('vertical');
			else $('.essb-style-livepreview').removeClass('vertical');
		});
	}

	if ($('.manage-new-close').length) {
		$('.manage-new-close').on('click', function(e){
			e.preventDefault();
			essbStylesManager.close();
		});
	}
});
