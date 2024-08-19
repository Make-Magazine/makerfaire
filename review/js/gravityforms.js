var ajaxurl = '/wp-admin/admin-ajax.php';

/*
 * Triggers an AJAX update of the entry detail
 */
function updateMgmt(action, entryID) {
    var data = {
        'action': 'mf-update-entry',
        'mfAction': action,
        'entry_id': entryID
    };
    var data = new URLSearchParams(data);

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
    }

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
                }
            } else {
                //after update - set meta field status to failed            
                document.getElementById(updMsgBox).innerHTML = '<span style="color:red"><i class="bi bi-x"></i>Error in Update</span>';
            }
        }
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