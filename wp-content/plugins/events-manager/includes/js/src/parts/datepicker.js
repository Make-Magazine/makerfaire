function em_setup_datepicker(wrap){
	wrap = jQuery(wrap);

	//apply datepickers - jQuery UI (backcompat)
	let dateDivs = wrap.find('.em-date-single, .em-date-range');
	if( dateDivs.length > 0 ){
		//default picker vals
		var datepicker_vals = {
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			firstDay : EM.firstDay,
			yearRange:'c-100:c+15',
			beforeShow : function( el, inst ){
				em_setup_jquery_ui_wrapper();
				inst.dpDiv.appendTo('#em-jquery-ui');
			}
		};
		if( EM.dateFormat ) datepicker_vals.dateFormat = EM.dateFormat;
		if( EM.yearRange ) datepicker_vals.yearRange = EM.yearRange;
		jQuery(document).triggerHandler('em_datepicker', datepicker_vals);
		//apply datepickers to elements
		dateDivs.find('input.em-date-input-loc').each(function(i,dateInput){
			//init the datepicker
			var dateInput = jQuery(dateInput);
			var dateValue = dateInput.nextAll('input.em-date-input').first();
			var dateValue_value = dateValue.val();
			dateInput.datepicker(datepicker_vals);
			dateInput.datepicker('option', 'altField', dateValue);
			//now set the value
			if( dateValue_value ){
				var this_date_formatted = jQuery.datepicker.formatDate( EM.dateFormat, jQuery.datepicker.parseDate('yy-mm-dd', dateValue_value) );
				dateInput.val(this_date_formatted);
				dateValue.val(dateValue_value);
			}
			//add logic for texts
			dateInput.on('change', function(){
				if( jQuery(this).val() == '' ){
					jQuery(this).nextAll('.em-date-input').first().val('');
				}
			});
		});
		//deal with date ranges
		dateDivs.filter('.em-date-range').find('input.em-date-input-loc[type="text"]').each(function(i,dateInput){
			//finally, apply start/end logic to this field
			dateInput = jQuery(dateInput);
			if( dateInput.hasClass('em-date-start') ){
				dateInput.datepicker('option','onSelect', function( selectedDate ) {
					//get corresponding end date input, we expect ranges to be contained in .em-date-range with a start/end input element
					var startDate = jQuery(this);
					var endDate = startDate.parents('.em-date-range').find('.em-date-end').first();
					var startValue = startDate.nextAll('input.em-date-input').first().val();
					var endValue = endDate.nextAll('input.em-date-input').first().val();
					startDate.trigger('em_datepicker_change');
					if( startValue > endValue && endValue != '' ){
						endDate.datepicker( "setDate" , selectedDate );
						endDate.trigger('change').trigger('em_datepicker_change');
					}
					endDate.datepicker( "option", 'minDate', selectedDate );
				});
			}else if( dateInput.hasClass('em-date-end') ){
				var startInput = dateInput.parents('.em-date-range').find('.em-date-start').first();
				if( startInput.val() != '' ){
					dateInput.datepicker('option', 'minDate', startInput.val());
				}
			}
		});
	}

	// datpicker - new format
	let datePickerDivs = wrap.find('.em-datepicker, .em-datepicker-range');
	if( datePickerDivs.length > 0 ){
		// wrappers and locale
		let datepicker_wrapper = jQuery('#em-flatpickr');
		if( datepicker_wrapper.length === 0 ){
			datepicker_wrapper = jQuery('<div class="em pixelbones em-flatpickr" id="em-flatpickr"></div>').appendTo('body');
		}
		// locale
		if( 'locale' in EM.datepicker ){
			flatpickr.localize(flatpickr.l10ns[EM.datepicker.locale]);
			flatpickr.l10ns.default.firstDayOfWeek = EM.firstDay;
		}
		//default picker vals
		let datepicker_options = {
			appendTo : datepicker_wrapper[0],
			dateFormat: "Y-m-d",
			disableMoble : "true",
			allowInput : true,
			onChange : [function( selectedDates, dateStr, instance ){
				let wrapper = jQuery(instance.input).closest('.em-datepicker');
				let data_wrapper = wrapper.find('.em-datepicker-data');
				let inputs = data_wrapper.find('input');
				let dateFormat = function(d) {
					let month = '' + (d.getMonth() + 1),
						day = '' + d.getDate(),
						year = d.getFullYear();
					if (month.length < 2) month = '0' + month;
					if (day.length < 2) day = '0' + day;
					return [year, month, day].join('-');
				}
				if( selectedDates.length === 0 ){
					inputs.attr('value', '');
				}else{
					if( instance.config.mode === 'range' && selectedDates[1] !== undefined ) {
						// deal with end date
						inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
						inputs[1].setAttribute('value', dateFormat(selectedDates[1]));
					}else if( instance.config.mode === 'single' && wrapper.hasClass('em-datepicker-until') ){
						if( instance.input.classList.contains('em-date-input-start') ){
							inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
							// set min-date of other datepicker
							let fp;
							if( wrapper.attr('data-until-id') ){
								let fp_input = jQuery('#' + wrapper.attr('data-until-id') + ' .em-date-input-end');
								fp = fp_input[0]._flatpickr;
							}else {
								fp = wrapper.find('.em-date-input-end')[0]._flatpickr;
							}
							if( fp.selectedDates[0] !== undefined && fp.selectedDates[0] < selectedDates[0] ){
								fp.setDate(selectedDates[0], false);
								inputs[1].setAttribute('value', dateFormat(fp.selectedDates[0]));
							}
							fp.set('minDate', selectedDates[0]);
						}else{
							inputs[1].setAttribute('value', dateFormat(selectedDates[0]));
						}
					}else{
						inputs[0].setAttribute('value', dateFormat(selectedDates[0]));
					}
				}
				inputs.trigger('change');
				let current_date = data_wrapper.attr('date-value');
				data_wrapper.attr('data-value', dateStr);
				if( current_date === dateStr ) data_wrapper.trigger('change');
			}],
			onClose : function( selectedDates, dateStr, instance ){
				// deal with single date choice and clicking out
				if( instance.config.mode === 'range' && selectedDates[1] !== undefined ){
					if(selectedDates.length === 1){
						instance.setDate([selectedDates[0],selectedDates[0]], true); // wouldn't have been triggered with a single date selection
					}
				}
			},
			locale : {},
		};
		if( EM.datepicker.format !== datepicker_options.dateFormat ){
			datepicker_options.altFormat = EM.datepicker.format;
			datepicker_options.altInput = true;
		}
		jQuery(document).triggerHandler('em_datepicker_options', datepicker_options);
		//apply datepickers to elements
		datePickerDivs.each( function(i,datePickerDiv) {
			// hide fallback fields, show range or single
			datePickerDiv = jQuery(datePickerDiv);
			datePickerDiv.find('.em-datepicker-data').addClass('hidden');
			let isRange = datePickerDiv.hasClass('em-datepicker-range');
			let altOptions = {};
			if( datePickerDiv.attr('data-datepicker') ){
				altOptions = JSON.parse(datePickerDiv.attr('data-datepicker'));
				if( typeof altOptions !== 'object' ){
					altOptions = {};
				}
			}
			let options = Object.assign({}, datepicker_options, altOptions); // clone, mainly shallow concern for 'mode'
			options.mode = isRange ? 'range' : 'single';
			if( isRange && 'onClose' in options ){
				options.onClose = [function( selectedDates, dateStr, instance ){
					if(selectedDates.length === 1){ // deal with single date choice and clicking out
						instance.setDate([selectedDates[0],selectedDates[0]], true);
					}
				}];
			}
			if( datePickerDiv.attr('data-separator') ) options.locale.rangeSeparator = datePickerDiv.attr('data-separator');
			if( datePickerDiv.attr('data-format') ) options.altFormat = datePickerDiv.attr('data-format');
			let FPs = datePickerDiv.find('.em-date-input');
			FPs.attr('type', 'text').flatpickr(options);
		});
		// add values to elements, done once all datepickers instantiated so we don't get errors with date range els in separate divs
		datePickerDivs.each( function(i,datePickerDiv) {
			datePickerDiv = jQuery(datePickerDiv);
			let FPs = datePickerDiv.find('.em-date-input');
			let inputs = datePickerDiv.find('.em-datepicker-data input');
			inputs.attr('type', 'hidden'); // hide so not tabbable
			if( datePickerDiv.hasClass('em-datepicker-until') ){
				let start_fp, end_fp;
				if( datePickerDiv.attr('data-until-id') ){
					end_fp = jQuery('#' + datePickerDiv.attr('data-until-id') + ' .em-date-input-end')[0]._flatpickr;
				}else{
					end_fp = FPs.filter('.em-date-input-end')[0]._flatpickr;
					if( inputs[1] && inputs[1].value ) {
						end_fp.setDate(inputs[1].value, false, 'Y-m-d');
					}
				}
				if( inputs[0] && inputs[0].value ){
					start_fp = FPs.filter('.em-date-input-start')[0]._flatpickr;
					start_fp.setDate(inputs[0].value, false, 'Y-m-d');
					end_fp.set('minDate', inputs[0].value);
				}
			}else{
				let dates = [];
				inputs.each( function( i, input ){
					if( input.value ){
						dates.push(input.value);
					}
				});
				FPs[0]._flatpickr.setDate(dates, false, 'Y-m-d');
			}
		});
		// fire trigger
		jQuery(document).triggerHandler('em_flatpickr_loaded', [wrap]);
	}
}