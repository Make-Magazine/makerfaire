function em_setup_timepicker(wrap){
	wrap = jQuery(wrap);
	var timepicker_options = {
		step:15
	}
	timepicker_options.timeFormat = EM.show24hours == 1 ? 'G:i':'g:i A';
	jQuery(document).triggerHandler('em_timepicker_options', timepicker_options);
	wrap.find(".em-time-input").em_timepicker(timepicker_options);

	// Keep the duration between the two inputs.
	wrap.find(".em-time-range input.em-time-start").each( function(i, el){
		var time = jQuery(el);
		time.data('oldTime', time.em_timepicker('getSecondsFromMidnight'));
	}).on('change', function() {
		var start = jQuery(this);
		var end = start.nextAll('.em-time-end');
		if (end.val()) { // Only update when second input has a value.
			// Calculate duration.
			var oldTime = start.data('oldTime');
			var duration = (end.em_timepicker('getSecondsFromMidnight') - oldTime) * 1000;
			var time = start.em_timepicker('getSecondsFromMidnight');
			if( end.em_timepicker('getSecondsFromMidnight') >= oldTime ){
				// Calculate and update the time in the second input.
				end.em_timepicker('setTime', new Date(start.em_timepicker('getTime').getTime() + duration));
			}
			start.data('oldTime', time);
		}
	});
	// Validate.
	wrap.find(".event-form-when .em-time-range input.em-time-end").on('change', function() {
		var end = jQuery(this);
		var start = end.prevAll('.em-time-start');
		var wrapper = end.closest('.event-form-when');
		var start_date = wrapper.find('.em-date-end').val();
		var end_date = wrapper.find('.em-date-start').val();
		if( start.val() ){
			if( start.em_timepicker('getTime') > end.em_timepicker('getTime') && ( end_date.length == 0 || start_date == end_date ) ) { end.addClass("error"); }
			else { end.removeClass("error"); }
		}
	});
	wrap.find(".event-form-when .em-date-end").on('change', function(){
		jQuery(this).closest('.event-form-when').find('.em-time-end').trigger('change');
	});
	//Sort out all day checkbox
	wrap.find('.em-time-range input.em-time-all-day').on('change', function(){
		var allday = jQuery(this);
		if( allday.is(':checked') ){
			allday.closest('.em-time-range').find('.em-time-input').each( function(){
				this.style.setProperty('background-color','#ccc', 'important');
				this.readOnly = true;
			});
		}else{
			allday.closest('.em-time-range').find('.em-time-input').each( function(){
				this.style.removeProperty('background-color');
				this.readOnly = false;
			});
		}
	}).trigger('change');
}
