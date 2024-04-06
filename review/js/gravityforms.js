/*
 * Triggers an AJAX update of the entry detail
 */
function updateMgmt(action) {
    var ajaxurl = '/wp-admin/admin-ajax.php';
	//set the processing icon
	jQuery("span." + action + 'Msg').html('<i class="fas fa-spinner fa-spin"></i>');

	var entry_id = jQuery("input[name=entry_info_entry_id]").val();
	var data = {
		'action': 'mf-update-entry',
		'mfAction': action,
		'entry_id': entry_id
	};

	var processing_icon = '<i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Loading...</span>';
	//add additional data for each action
	if (action == 'update_entry_status') {
		data.entry_info_status_change = jQuery("select[name=entry_info_status_change]").val();
	} else if (action == 'update_entry_management') {
		//set processing icon on the screen
		jQuery(".upd_mgmt_msg").html(processing_icon);
		//preliminary location
		var entry_info_location_change = [];
		jQuery("[name='entry_info_location_change[]']:checked").each(function() {
			// push all checked locations to array
			entry_info_location_change.push(jQuery(this).val());
		});
		data.entry_info_location_change = entry_info_location_change;

		//flags
		var entry_info_flags_change = [];
		jQuery("[name='entry_info_flags_change[]']:checked").each(function() {
			// push all checked locations to array
			entry_info_flags_change.push(jQuery(this).val());
		});
		data.entry_info_flags_change = entry_info_flags_change;

		//location comment
		data.entry_location_comment = jQuery("textarea[name=entry_location_comment]").val();
	} else if (action == 'change_form_id') {
		data.entry_form_change = jQuery("select[name=entry_form_change]").val();
	} else if (action == 'duplicate_entry_id') {
		data.entry_form_copy = jQuery("select[name=entry_form_copy]").val();
	} else if (action == 'send_conf_letter' || action == 'update_entry_schedule') {
		//send confirmation lette updates the schedule as well
		data.datetimepickerstart = jQuery("input[name=datetimepickerstart]").val();
		data.datetimepickerend = jQuery("input[name=datetimepickerend]").val();
		data.entry_location_subarea_change = jQuery("select[name=entry_location_subarea_change]").val();
		data.update_entry_location_code = jQuery("input[name=update_entry_location_code]").val();
		data.sched_type = jQuery("#typeSel").val();

	} else if (action == 'delete_entry_schedule') {
		//schedule id's to delete
		var delete_schedule_id = [];
		jQuery("[name='delete_schedule_id[]']:checked").each(function() {
			// push all checked locations to array
			delete_schedule_id.push(jQuery(this).val());
		});
		data.delete_schedule_id = delete_schedule_id;

		//location ID's to delete
		var delete_location_id = [];
		jQuery("[name='delete_location_id[]']:checked").each(function() {
			// push all checked locations to array
			delete_location_id.push(jQuery(this).val());
		});
		data.delete_location_id = delete_location_id;
	} else if (action == 'update_ticket_code') {
		data.entry_ticket_code = jQuery("input[name=entry_ticket_code]").val();
	} else if (action == 'delete_note_sidebar') {
		//note id's to delete
		var note = [];
		jQuery("[name='note[]']:checked").each(function() {
			var noteID = jQuery(this).val();
			// push all checked locations to array
			note.push(noteID);
			//remove the row with this note in the ui
			jQuery('tr.note' + noteID).remove();
		});
		data.note = note;
	} else if (action == 'add_note_sidebar') {
		data.new_note_sidebar = jQuery("[name=new_note_sidebar ]").val();

		//email note to
		var gentry_email_notes_to_sidebar = [];
		jQuery("[name='gentry_email_notes_to_sidebar[]']:checked").each(function() {
			// push all checked locations to array
			gentry_email_notes_to_sidebar.push(jQuery(this).val());
		});
		if (jQuery("[name=otherEmail]").val() !== '') {
			gentry_email_notes_to_sidebar.push(jQuery("[name=otherEmail]").val());
		}
		data.gentry_email_notes_to_sidebar = gentry_email_notes_to_sidebar;
	} else if (action == 'update_fee_mgmt') {
		//set processing icon on the screen
		jQuery(".update_fee_mgmt_msg").html(processing_icon);
		//pfee mgmt
		var entry_info_fee_mgmt = [];
		jQuery("[name='entry_info_fee_mgmt[]']:checked").each(function() {
			// push all checked locations to array
			entry_info_fee_mgmt.push(jQuery(this).val());
		});
		data.entry_info_fee_mgmt = entry_info_fee_mgmt;
	} else if (action == 'update_exhibit_type') {
		//set processing icon on the screen
		jQuery(".update_exhibit_typeMsg").html(processing_icon);
		//exhibit type
		var entry_exhibit_type = [];
		jQuery("[name='entry_exhibit_type[]']:checked").each(function() {
			// push all checked locations to array
			entry_exhibit_type.push(jQuery(this).val());
		});
		data.entry_exhibit_type = entry_exhibit_type;
	} else if (action == 'update_final_weekend') {
		//set processing icon on the screen
		jQuery(".update_final_weekend_typeMsg").html(processing_icon);
		//final weekend
		var entry_final_weekend = [];
		jQuery("[name='entry_final_weekend[]']:checked").each(function() {
			// push all checked locations to array
			entry_final_weekend.push(jQuery(this).val());
		});
		data.entry_final_weekend = entry_final_weekend;
	}

	jQuery.post(ajaxurl, data, function(r) {
		if (r.result === 'updated') {
			//after update - set meta field status to success
			jQuery("span." + action + 'Msg').html('<i style="color:green" class="fas fa-check"></i>');
		} else {
			//after update - set meta field status to failed
			jQuery("span." + action + 'Msg').html('<i style="color:red" class="fas fa-times"></i>');
		}
		if (r.rebuild !== '' && r.rebuildHTML !== '') {
			jQuery("." + r.rebuild).replaceWith(r.rebuildHTML);
			//reset the date time picker fields
			jQuery('#datetimepicker').datetimepicker({ value: '2015/04/15 05:03', step: 30 });
			jQuery('#datetimepickerstart').datetimepicker({
				formatTime: 'g:i a',
				formatDate: 'd.m.Y',
				defaultTime: '10:00 am', 
				step: 30,
			});
			jQuery('#datetimepickerend').datetimepicker({
				formatTime: 'g:i a',
				formatDate: 'd.m.Y',
				defaultTime: '10:00 am', 
				step: 30,
			});
		}

		if (action === 'duplicate_entry_id' && r.entryID !== '') {
			jQuery("span.duplicate_entry_idMsg").after(r.entryID);
		}

		if (action === 'change_form_id' && r.entryID !== '') {
			jQuery("span.change_form_idMsg").after(r.entryID);
		}

	});
}