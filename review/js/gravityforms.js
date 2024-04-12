/*
 * Triggers an AJAX update of the entry detail
 */
function updateMgmt(action, entryID) {
    var ajaxurl = '/wp-admin/admin-ajax.php';
	//set the processing icon
	jQuery("span." + action + 'Msg').html('<i class="fas fa-spinner fa-spin"></i>');

	var entry_id = jQuery("input[name=entry_info_entry_id]").val();
	var data = {
		'action': 'mf-update-entry',
		'mfAction': action,
		'entry_id': entry_id
	};

	var processing_icon = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';
	//add additional data for each action
	if (action == 'add_note_sidebar') {        
        var note_text  = document.getElementById("new_note_"+entryID).value; 
        var toEmail    = document.getElementById("toEmail"+entryID).value; 
        var updMsgBox  = 'add_noteMSG_'+entryID;
        if(toEmail==''){
            document.getElementById(updMsgBox).textContent="Please enter a to Email";    
            return;
        }else if(note_text==''){
            document.getElementById(updMsgBox).textContent="Please enter a message";    
            return;
        }else {
            document.getElementById(updMsgBox).innerHTML=processing_icon;
        }		

        //set text
        data.new_note_sidebar = note_text;
		//email note to
		var gentry_email_notes_to_sidebar = [];		
		if (toEmail !== '') {
			gentry_email_notes_to_sidebar.push(toEmail);
		}
		data.gentry_email_notes_to_sidebar = gentry_email_notes_to_sidebar;
    } else if (action === 'update_flags') {        
        var updMsgBox  = 'updFlagsMSG'+entryID;
        document.getElementById(updMsgBox).innerHTML=processing_icon;

        //find all checked flags
        let checkboxes = document.getElementsByName("entry_flags_"+entryID+'[]');        
        var entry_info_flags_change = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // push all checked locations to array
			    entry_info_flags_change.push(checkboxes[i].value);    
            }
        }        
        data.entry_info_flags_change = entry_info_flags_change;
    } else if (action == 'update_prelim_loc') {        
        var updMsgBox  = 'updPrelimLocMSG'+entryID;
        document.getElementById(updMsgBox).innerHTML=processing_icon;

        //find all checked flags
        let checkboxes = document.getElementsByName("entry_prelim_loc_"+entryID+'[]');        
        var entry_prelim_loc_change = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // push all checked locations to array
			    entry_prelim_loc_change.push(checkboxes[i].value);    
            }
        }        
        data.entry_info_location_change = entry_prelim_loc_change;    

		//location comment
		data.entry_location_comment = document.getElementById("location_comment_"+entryID).value;     
	} else if (action == 'update_exhibit_type') {
		var updMsgBox  = 'updExhibitTypeMsg'+entryID;
		alert('you are here '+updMsgBox);
        document.getElementById(updMsgBox).innerHTML=processing_icon;
						
		//find all checked flags
        let checkboxes = document.getElementsByName("admin_exhibit_type_"+entryID+'[]');        
        var entry_exhibit_type = [];
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // push all checked locations to array
			    entry_exhibit_type.push(checkboxes[i].value);    
            }
        }        
		data.entry_exhibit_type = entry_exhibit_type;	
	}

	jQuery.post(ajaxurl, data, function(r) {
		if (r.result === 'updated') {
			//after update - set meta field status to success
			jQuery('#'+updMsgBox).html('<i style="color:green" class="fas fa-check"></i>Updated');
		} else {
			//after update - set meta field status to failed
			jQuery('#'+updMsgBox).html('<i style="color:red" class="fas fa-times"></i>Error in Update');
		}		
	});
}