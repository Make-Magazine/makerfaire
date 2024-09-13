var ajaxurl = '/wp-admin/admin-ajax.php';

/*
 * Triggers an AJAX update of the entry detail
 */
function updateMgmt(action, entryID, delID=0) {
    var data = {
        'action': 'mf-update-entry',
        'mfAction': action,
        'entry_id': entryID
    };
    var data = new URLSearchParams(data);
    var error = false;
    var errorMsg = '';

    var processing_icon = '<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>';
    //add additional data for each action
    if (action == 'add_note_sidebar') {
        var note_text = document.getElementById("new_note_" + entryID).value;
        var toEmail = document.getElementById("toEmail" + entryID).value;
        var updMsgBox = 'add_noteMSG_' + entryID;

        if (note_text == '') {
            document.getElementById(updMsgBox).textContent = "Please enter a message";
            return;
        }

        //set text        
        data.append('new_note_sidebar', note_text);

        //email note to
        var gentry_email_notes_to_sidebar = [];
        if (toEmail !== '') {
            gentry_email_notes_to_sidebar.push(toEmail);
        }

        data.append('gentry_email_notes_to_sidebar', gentry_email_notes_to_sidebar);
    } else if (action === 'update_admin') {
        //find all checked flags
        var flags = document.getElementsByName("entry_flags_" + entryID + '[]');

        if (flags.length != 0) {
            for (var i = 0; i < flags.length; i++) {
                if (flags[i].checked) {
                    // push all checked entry types
                    data.append('entry_info_flags_change[]', flags[i].value);
                }
            }
        }

        //Preliminary Location        
        var prelim_loc = document.getElementsByName("entry_prelim_loc_" + entryID + '[]');

        if (prelim_loc.length != 0) {
            for (var i = 0; i < prelim_loc.length; i++) {
                if (prelim_loc[i].checked) {
                    // push all checked
                    data.append('entry_info_location_change[]', prelim_loc[i].value);
                }
            }

            //location comment
            data.append('entry_location_comment', document.getElementById("location_comment_" + entryID).value);
        }

        //Entry Type
        var entry_type = document.getElementsByName("admin_exhibit_type_" + entryID + '[]');

        if (entry_type.length != 0) {
            for (var i = 0; i < entry_type.length; i++) {
                if (entry_type[i].checked) {
                    // push all checked entry types
                    data.append('entry_exhibit_type[]', entry_type[i].value);
                }
            }
        }

        //Fee Management
        var fee_mgmt = document.getElementsByName("info_fee_mgmt_" + entryID + '[]');
        if (fee_mgmt.length != 0) {
            for (var i = 0; i < fee_mgmt.length; i++) {
                if (fee_mgmt[i].checked) {
                    // push all selected fee mgmnt
                    data.append('entry_info_fee_mgmt[]', fee_mgmt[i].value);
                }
            }
        }

        //Message Box
        var updMsgBox = 'updAdminMSG' + entryID;
    } else if (action === 'update_entry_status') {
        //Entry Status
        var updateStatus = document.getElementById("entryStatus_" + entryID);
        if (typeof (updateStatus) != 'undefined' && updateStatus != null) {
            data.append('entry_info_status_change', updateStatus.value);
        }

        //Message Box
        var updMsgBox = 'updStatusMSG' + entryID;
    } else if (action == 'update_entry_schedule') {                
        var schedule = document.getElementById("schedule" + entryID);
        
        var selDate = schedule.querySelector('[name="sched_date"]').value;                        
        var startTm = schedule.querySelector('[name="start_time"]').value;                        
        var endTm   = schedule.querySelector('[name="end_time"]').value;                        
        var subarea = schedule.querySelector('[name="sched_subarea"]').value;                        
        var booth   = schedule.querySelector('[name="sched_booth"]').value;                        
        var type    = schedule.querySelector('[name="sched_type"]').value;  

        if(subarea == ''){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>You must select an Area/Subarea for the Schedule.</span><br/>';
        }                      
        if(type == ''){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>You must select a Type for the Schedule.</span><br/>';
        }        
        if(selDate == ''){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>You must select a date.</span><br/>';
        }             
        if(startTm == ''){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>You must select a Start Time.</span><br/>';
        }
        if(endTm == ''){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>You must select an End Time.</span><br/>';
        }       
        if(endTm <= startTm ){
            error = true;
            errorMsg += '<span style="color:red"><i class="bi bi-x"></i>End Time must be after Start Time.</span><br/>';
        }              
        
        //buid output
        data.append('sched_type', type);
        data.append('entry_location_subarea_change', subarea);
        data.append('update_entry_location_code', booth);
        data.append('datetimepickerstart', selDate+' '+startTm);
        data.append('datetimepickerend', selDate+' '+endTm);
        /**
         
         */        
        //Message Box
        var updMsgBox = 'addScheduleMSG' + entryID;            
    } else if (action == 'delete_entry_schedule') {                
        //Message Box
        var updMsgBox = 'delScheduleMSG' + entryID;                
        data.append('delete_schedule_id[]', delID);        
    }
    if (!error) {
        //set message box to the processing icon
        document.getElementById(updMsgBox).innerHTML = processing_icon;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", ajaxurl);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(new URLSearchParams(data));

        xhr.onreadystatechange = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                try {
                    response = JSON.parse(xhr.response);
                } catch (e) {
                    document.getElementById(updMsgBox).innerHTML = '<span style="color:red"><i class="bi bi-x"></i>Error in Update</span>';

                    return false;
                }

                //if we have a result, let's check it here
                if (response.result === 'updated') {
                    //after update - find all displays of status and update them
                    document.getElementById(updMsgBox).innerHTML = '<span style="color:green"><i class="bi bi-check2"></i>Updated</span>';
                    //if user updated the entry status, we need to update the display field as well
                    if (action == 'update_entry_status') {
                        //find all checked flags
                        var displayStatus = document.getElementsByClassName("status_" + entryID);
                        for (var i = 0; i < displayStatus.length; i++) {
                            displayStatus[i].innerHTML = updateStatus;
                        }
                    }else if (action == 'update_entry_schedule') {           
                        var table = document.getElementById("schedTable-"+ entryID);     
                        
                        //add a new row
                        var row = table.insertRow(-1);
                        
                        //add cells
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        var cell3 = row.insertCell(2);
                        var cell4 = row.insertCell(3);
                        var cell5 = row.insertCell(4);

                        // Add data to the new cells:
                        sel_location = schedule.querySelector('[name="sched_subarea"]'); 
                        location_text = sel_location.options[sel_location.selectedIndex].text;
                        if(booth !='') location_text += '('+booth+')';
                        cell1.innerHTML = location_text; //area/subarea (location)                        
  
                        cell2.innerHTML = 'Type: '+type.charAt(0).toUpperCase() + type.slice(1); //Type: type

                        //schedule date
                        const date = new Date(selDate.replace("-", "/")); // creating new date is more accurate when using / rather than - for dividers https://stackoverflow.com/questions/7556591/is-the-javascript-date-object-always-one-day-off                       
                        cell3.innerHTML = new Intl.DateTimeFormat('default', {dateStyle: 'short'}).format(date); 

                        //schedule time
                        const startTime = new Date(selDate+' '+startTm);                                                
                        frmtStartTime = new Intl.DateTimeFormat('default', {timeStyle: 'short'}).format(startTime);
                        const endTime = new Date(selDate+' '+endTm);                                                
                        frmtEndTime = new Intl.DateTimeFormat('default', {timeStyle: 'short'}).format(endTime);                        
                        cell4.innerHTML = frmtStartTime + ' - '+frmtEndTime; //start time - end time

                        cell5.innerHTML = '<span class="faux-btn" onclick="updateMgmt(\'delete_entry_schedule\', '+entryID+', '+response.insert_row+')" style="color:red">X</span>'; //remove button
                                                                                                        
                    }else if (action == 'delete_entry_schedule') {
                        document.getElementById(updMsgBox).innerHTML = '<span style="color:green"><i class="bi bi-check2"></i>Deleted</span>';
                        //remove the deleted row
                        document.getElementById("sched-"+delID).remove();
                    }
                } else {
                    //after update - set meta field status to failed            
                    document.getElementById(updMsgBox).innerHTML = '<span style="color:red"><i class="bi bi-x"></i>Error in Update</span>';
                }
            }
        }
    }else{
        document.getElementById(updMsgBox).innerHTML = errorMsg;
    }

}

function setLightBox(className, image_id) {
    var lightbox = new FsLightbox();
    var images = document.getElementsByClassName(className);
    var imgArr = [];
    var captionsArr = [];
    for (var i = 0; i < images.length; i++) {
        if (images[i].tagName == 'IMG') {
            imgArr.push(images[i].src);
            captionsArr.push(images[i].alt);
        } else if (images[i].tagName == 'A') {
            imgArr.push(images[i].href);
            captionsArr.push('video');
        }

    }
    lightbox.props.sources = imgArr;
    lightbox.props.captions = captionsArr;

    lightbox.open(image_id);
}

//this is a copy of the function from the gravityforms plugin
// original function can be found at makerfaire/wp-content/plugins/gravityforms/entry_detail.php
function ResendNotifications(entry_id, form_id) {
    var notification = document.getElementById('gform_notifications_' + entry_id).value;

    var selectedNotifications = new Array();
    var selectedNotifications = [notification];

    var sendTo = document.getElementById('notification_override_email_' + entry_id).value;
    var nonce = document.getElementById('gfnonce_' + entry_id).value;

    if (selectedNotifications.length <= 0) {
        alert('You must select at least one type of notification to resend.');
        return;
    }

    document.getElementById('please_wait_container').style.display = 'block';
    var data = {
        'action': "gf_resend_notifications",
        'gf_resend_notifications': nonce,
        'notifications': JSON.stringify(selectedNotifications),
        'sendTo': sendTo,
        'leadIds': entry_id,
        'formId': form_id
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", ajaxurl);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(new URLSearchParams(data));

    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            try {
                //response = JSON.parse(xhr.response);
                if (xhr.response) {
                    document.getElementById('please_wait_container').innerHTML = '<span style="color:red"><i class="bi bi-x"></i>Error in Update' + xhr.response + '</span>';
                }
                document.getElementById('please_wait_container').innerHTML = '<span style="color:green"><i class="bi bi-check2"></i>Notification Sent</span>';
            } catch (e) {
                document.getElementById('please_wait_container').innerHTML = '<span style="color:red"><i class="bi bi-x"></i>Error in Update</span>';
            }

        };
    }
}