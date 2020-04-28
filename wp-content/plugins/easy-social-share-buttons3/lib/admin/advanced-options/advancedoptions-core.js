jQuery(document).ready(function($){

	var aoRemoveCustomPosition = window.aoRemoveCustomPosition = function(position) {
		var remotePost = { 'position': position };

		swal({
			title: "Are you sure?",
			text: "If you are using this position anywhere inside content and delete it the design of share buttons will not show again.",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			}).then((willDelete) => {
			  if (willDelete) {
				  essbAdvancedOptions.post('remove_position', remotePost, function(data) {

					  	if (data) {
					  		ao_user_positions = JSON.parse(data);
					  		ao_user_positions_draw();
					  	}

						$.toast({
							    heading: 'The position is removed. The menu entries will disappear when you reload settings page.',
							    //text: 'If you are using a cache plugin, service or CDN do not forget to clear them.',
							    showHideTransition: 'fade',
							    icon: 'success',
							    position: 'bottom-right',
							    hideAfter: 5000
							});
						});
			  }
			});

	}
	
	var essbAdvancedOptions = window.essbAdvancedOptions = {
		ajax_url: essb_advancedopts_ajaxurl,
		debug_mode: true,
		requireReload: false,
		settings: '',
		withoutSave: false
	}

	essbAdvancedOptions.post = function(action, options, callback) {
		if (!options) options = {};
		options['action'] = 'essb_advanced_options';
		options['cmd'] = action;
		// sending the nonce token via settings
		options['essb_advancedoptions_token'] = $('#essb_advancedoptions_token').length ? $('#essb_advancedoptions_token').val() : '';


		if ($('#advancedoptions-preloader').length) $('#advancedoptions-preloader').fadeIn(100);

		$.ajax({
            type: "POST",
            url: essbAdvancedOptions.ajax_url,
            data: options,
            success: function (data) {
            	if ($('#advancedoptions-preloader').length) $('#advancedoptions-preloader').fadeOut(100);
            	if (essbAdvancedOptions.debug_mode) console.log(data);

	            if (callback) callback(data);
            }
    	});
	}

	essbAdvancedOptions.read = function(action, options, callback) {
		if (!options) options = {};
		options['action'] = 'essb_advanced_options';
		options['cmd'] = action;
		// sending the nonce token via settings
		options['essb_advancedoptions_token'] = $('#essb_advancedoptions_token').length ? $('#essb_advancedoptions_token').val() : '';


		console.log(options);
		if ($('#advancedoptions-preloader').length) $('#advancedoptions-preloader').fadeIn(100);

		$.ajax({
            type: "GET",
            url: essbAdvancedOptions.ajax_url,
            data: options,
            success: function (data) {
            	if ($('#advancedoptions-preloader').length) $('#advancedoptions-preloader').fadeOut(100);
            	//if (essbAdvancedOptions.debug_mode) console.log(data);

	            if (callback) callback(data);
            }
    	});
	}

	essbAdvancedOptions.correctWidthAndPosition = function() {
		var baseWidth = 1200, wWidth = $(window).width(),
			wHeight = $(window).height(),
			winHeight = (wHeight - 100);

		if (wWidth < baseWidth) baseWidth = wWidth - 100;
		$('#essb-advancedoptions').css({'width': baseWidth + 'px', 'height': winHeight + 'px'});
		$('#essb-advancedoptions').centerWithAdminBar();

		if ($('#essb-advancedoptions').find('.essb-helper-popup-content').length) {
			var contentHolder = $('#essb-advancedoptions').find('.essb-helper-popup-content'),
				contentHolderHeight = $(contentHolder).actual('height'),
				contentOffsetCorrection = 90;

			$('#essb-advancedoptions').find('.essb-helper-popup-content').css({height: (winHeight - contentOffsetCorrection)+'px'});
			$('#essb-advancedoptions').find('.essb-helper-popup-content').css({overflowY: 'auto'});

		}
	}

	/**
	 *
	 */
	essbAdvancedOptions.show = function(settings, reload, title, hideSave, loadingOptions) {
		console.log('advanced options call show');
		essbAdvancedOptions.correctWidthAndPosition();

		if (!settings) settings = '';
		console.log('advanced options call show 1');
		essbAdvancedOptions.settings = settings;
		essbAdvancedOptions.requireReload = reload;
		essbAdvancedOptions.withoutSave = hideSave;

		if (!title) title = 'Additional Options';
		console.log('advanced options call show 2');
		if (essbAdvancedOptions.withoutSave) {
			$('#essb-advancedoptions .advancedoptions-save').hide();
		}
		else{
			$('#essb-advancedoptions .advancedoptions-save').show();
		}

		console.log('advanced options call show 3');
		$('#essb-advanced-options-form').html('');
		$('.advancedoptions-modal').fadeIn();
		$('#essb-advancedoptions').fadeIn();

		$('#advancedOptions-title').text(title);
		console.log('advanced options call show 4');
		if (reload)
			$.toast({
			    heading: 'Saving of the options will reload the screen. If you have unsaved changes they will be lost.',
			    text: '',
			    showHideTransition: 'fade',
			    icon: 'info',
			    position: 'bottom-right',
			    hideAfter: 5000
			});

		console.log('advanced options call show 5');
		essbAdvancedOptions.read('get', { 'settings': settings, 'loadingOptions': loadingOptions  }, essbAdvancedOptions.load);
	}

	essbAdvancedOptions.close = function() {
		$('.advancedoptions-modal').fadeOut();
		$('#essb-advancedoptions').fadeOut(200);
	}

	essbAdvancedOptions.load = function(content) {
		if (!content) content = '';

		$('#essb-advanced-options-form').html(content);
		essbAdvancedOptions.assignAfterloadEvents();
	}

	essbAdvancedOptions.assignAfterloadEvents = function() {
		$('#essb-advancedoptions .essb-component-toggleselect .toggleselect-item').each(function() {
			$(this).click(function(e) {
				e.preventDefault();
				$(this).parent().find('.toggleselect-item').each(function(){
					if ($(this).hasClass('active'))
						$(this).removeClass('active');
				});

				$(this).addClass('active');

				var selectedValue = $(this).attr('data-value') || '';
				$(this).parent().find('.toggleselect-holder').val(selectedValue);

				$(this).parent().find('.toggleselect-holder').trigger('change');
			});
	 	});

		$('.input-colorselector').each(function() {
			$(this).wpColorPicker();
		});


		$('#essb-advancedoptions').find('.essb-portlet-switch').find('.onoffswitch-checkbox').each(function(){
			console.log('locating ...')
			$(this).click(function(e) {
				console.log('click detected');

				var state_checkbox = $(this);//.find('input');
				if (!state_checkbox.length) return;

				var state = $(state_checkbox).is(':checked');


				var parent_heading = $(this).parent().parent().parent();

				// closed
				if (state) {
					$(parent_heading).removeClass('essb-portlet-heading-closed');
					var content = $(parent_heading).parent().find('.essb-portlet-content');
					if (content.length > 1) content = content[0];
					$(content).slideDown("fast", function() {
						$(content).removeClass('essb-portlet-content-closed');
					});

					$('.CodeMirror').each(function(i, el){
					    el.CodeMirror.refresh();
					});

					$(parent_heading).parent().find('.essb_image_radio').each(function() {
						var image = $(this).find('.checkbox-image img');
						if (image) {
							var width = image.width();
							width += 10;

							$(this).parent().find('.essb_radio_label').width(width);
						}
					});

					$(parent_heading).parent().find('.essb_image_checkbox').each(function() {
						var image = $(this).find('.checkbox-image img');
						if (image) {
							var width = image.width();
							width += 10;

							$(this).parent().find('.essb_checkbox_label').width(width);
						}
					});
				}
				else {
					$(parent_heading).addClass('essb-portlet-heading-closed');
					var content = $(parent_heading).parent().find('.essb-portlet-content');
					if (content.length > 1) content = content[0];
					$(content).slideUp("fast", function() {
						$(content).addClass('essb-portlet-content-closed');
					});

				}
			});
		});

	}

	essbAdvancedOptions.removeFormDesign = function(design) {
		var remotePost = { 'design': design};

		essbAdvancedOptions.post('remove_form_design', remotePost, function(data) {
			$.toast({
			    heading: 'User design is removed! The settings screen will reload to update the values.',
			    //text: 'If you are using a cache plugin, service or CDN do not forget to clear them.',
			    showHideTransition: 'fade',
			    icon: 'success',
			    position: 'bottom-right',
			    hideAfter: 5000
			});

			setTimeout(function(){
				if (!essb_advancedopts_reloadurl) return;
				var reload = essb_advancedopts_reloadurl,
					section = $('#section').val(),
					subsection = $('#subsection').val();

				window.location.href = reload + (section != '' ? '&section='+section : '') + (subsection != '' ? '&subsection='+subsection : '');
			}, 2000);
		});
	}

	essbAdvancedOptions.save = function() {
		var optGroup = $('#essb-advanced-group').val(), options = {},
			paths = ['#essb-advanced-options-form input', '#essb-advanced-options-form select', '#essb-advanced-options-form textarea'];

		for (var i = 0; i < paths.length; i++) {
			$(paths[i]).each(function(){
				var elementId = $(this).id || '',
					elementName = $(this).attr('name') || '',
					elementValue = $(this).val(),
					elementType = $(this).attr('type') || '';

				if (elementType == 'checkbox' || elementType == 'radio')
					elementValue = $(this).is(":checked") ? 'true': 'false';

				if (elementName == '' || elementName == 'essb-advanced-group') return;

				elementName = elementName.replace('essb_options', '').replace('[', '').replace(']', '').replace('managestyle_', '');
				options[elementName] = elementValue;
			});
		}

		console.log(options);
		console.log('group = ' + optGroup);

		var remotePost = {
			'group': optGroup,
			'advanced_options': options
		};

		essbAdvancedOptions.post('save', remotePost, function(data) {
			$.toast({
			    heading: 'Options are saved!' + (essbAdvancedOptions.requireReload ? ' The settings screen will reload to activate the new setup' : ''),
			    //text: 'If you are using a cache plugin, service or CDN do not forget to clear them.',
			    showHideTransition: 'fade',
			    icon: 'success',
			    position: 'bottom-right',
			    hideAfter: 5000
			});
			essbAdvancedOptions.close();

			if (essbAdvancedOptions.requireReload) {
				setTimeout(function(){
					if (!essb_advancedopts_reloadurl) return;
					var reload = essb_advancedopts_reloadurl,
						section = $('#section').val(),
						subsection = $('#subsection').val();

					window.location.href = reload + (section != '' ? '&section='+section : '') + (subsection != '' ? '&subsection='+subsection : '');
				}, 2000);
			}
		});
	}
	//-- actions assigned to components

	$('#essb-advancedoptions .advancedoptions-close').click(function(e) {
		e.preventDefault();
		essbAdvancedOptions.close();
	});

	$('#essb-advancedoptions .advancedoptions-save').click(function(e) {
		e.preventDefault();
		essbAdvancedOptions.save();
	});

	if ($('.essb-head-modesbtn').length) {
		$('.essb-head-modesbtn').click(function(e) {
			e.preventDefault();
			essbAdvancedOptions.show('mode', true, 'Select Plugin Mode', false, {});
		});
	}

	if ($('.essb-head-featuresbtn').length) {
		$('.essb-head-featuresbtn').click(function(e) {
			e.preventDefault();
			essbAdvancedOptions.show('features', true, 'Manage Plugin Features', false, {});
		});
	}

	$('.ao-option-callback').click(function(e) {
		e.preventDefault();

		var action = $(this).data('option') || '',
			title = $(this).data('window-title') || '';

		if (action != '') essbAdvancedOptions.show(action, true, title, false, {});
	});

	$('.ao-form-userdesign').click(function(e){
		e.preventDefault();

		var design = $(this).data('design') || '',
			title = $(this).data('title') || '';

		design = design.replace('design-', '');

		essbAdvancedOptions.show('manage_subscribe_forms', true, title, false, { 'design': design });

	});

	$('.ao-form-removeuserdesign').click(function(e) {
		e.preventDefault();

		var design = $(this).data('design') || '',
			title = $(this).data('title') || '';

		design = design.replace('design-', '');

		swal({ title: "Are you sure?",
			  text: "Once deleted, you will not be able to recover this design!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			}).then((willDelete) => {
			  if (willDelete) {
				essbAdvancedOptions.removeFormDesign(design);
			  }
			});

	});

	$('.ao-options-btn').click(function(e) {
		e.preventDefault();

		var action = $(this).data('option') || '',
			reload = $(this).data('reload') || '',
			title = $(this).data('title') || '';
		if (action != '') essbAdvancedOptions.show(action, (reload == 'yes' ? true : false), title, false, {});
	});

	$('.ao-remove-display-position').click(function(e) {
		e.preventDefault();

		var position = $(this).data('position') || '';
		if (!position || position == '') return;

		aoRemoveCustomPosition(position);
	});

	$('.ao-new-display-position').click(function(e) {
		e.preventDefault();
		swal("Enter position name:", { content: "input", buttons: {
		    cancel: true,
		    confirm: true,
		  }}).then((value) => {
			  if (!value) value = '';
			  
			  if (value == '') return;
			var remotePost = {
					'position_name': value
				};

			essbAdvancedOptions.post('create_position', remotePost, function(data) {

			  	if (data) {
			  		ao_user_positions = JSON.parse(data);
			  		ao_user_positions_draw();
			  	}


				$.toast({
					    heading: 'The new position is added. The new menu entries for this position will appear when you reload the settings page.',
					    //text: 'If you are using a cache plugin, service or CDN do not forget to clear them.',
					    showHideTransition: 'fade',
					    icon: 'success',
					    position: 'bottom-right',
					    hideAfter: 5000
					});
				});

		});
	});

	$('.essb-reset-settings').click(function(e) {
		e.preventDefault();

		var cmd = $(this).data('clear') || '',
			title = $(this).data('title') || '';

		var remotePost = { 'function': cmd };

		swal({
			title: "Are you sure you want to reset: "+ title +"?",
			text: "The reset will remove data or restore default data of plugin. This option cannot be undone.",
			icon: "warning",
			buttons: true,
			dangerMode: true,
			}).then((willDelete) => {
			  if (willDelete) {
				  essbAdvancedOptions.post('reset_command', remotePost, function(data) {
						$.toast({
							    heading: 'Reset of ' + title+ ' is completed!',
							    showHideTransition: 'fade',
							    icon: 'success',
							    position: 'bottom-right',
							    hideAfter: 5000
							});
						});
			  }
			});
	});
});
