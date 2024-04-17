/*
 * Triggers an AJAX update of the entry detail
 */
function updateMgmt(action, entryID) {
    var ajaxurl = '/wp-admin/admin-ajax.php';

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

        if (toEmail == '') {
            document.getElementById(updMsgBox).textContent = "Please enter a to Email";
            return;
        } else if (note_text == '') {
            document.getElementById(updMsgBox).textContent = "Please enter a message";
            return;
        }

        //set text        
        data.append('new_note_sidebar',note_text);
        
        //email note to
        var gentry_email_notes_to_sidebar = [];
        if (toEmail !== '') {
            gentry_email_notes_to_sidebar.push(toEmail);
        }
        
        data.append('gentry_email_notes_to_sidebar',gentry_email_notes_to_sidebar);
    } else if (action === 'update_flags') {
        var updMsgBox = 'updFlagsMSG' + entryID;

        //find all checked flags
        var checkboxes = document.getElementsByName("entry_flags_" + entryID + '[]');
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // push all checked entry types
                data.append('entry_info_flags_change[]',checkboxes[i].value);
            }
        }
        
    } else if (action == 'update_prelim_loc') {
        var updMsgBox = 'updPrelimLocMSG' + entryID;

        //find all checked
        var checkboxes = document.getElementsByName("entry_prelim_loc_" + entryID + '[]');        
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {                
                 // push all checked
                 data.append('entry_info_location_change[]',checkboxes[i].value);
            }
        }       

        //location comment
        data.append('entry_location_comment',document.getElementById("location_comment_" + entryID).value);
       
    } else if (action == 'update_exhibit_type') {
        var updMsgBox = 'updExhibitTypeMsg' + entryID;

        //find all checked
        var checkboxes = document.getElementsByName("admin_exhibit_type_" + entryID + '[]');
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                // push all checked entry types
                data.append('entry_exhibit_type[]',checkboxes[i].value);
            }
        }        
    } else if (action == 'update_fee_mgmt') {
        var updMsgBox = 'updFeeMgmtMsg' + entryID;

        //find all checked
        var checkboxes = document.getElementsByName("info_fee_mgmt_" + entryID + '[]');        
        
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {                
                // push all selected fee mgmnt
                data.append('entry_info_fee_mgmt[]',checkboxes[i].value);
            }
        }
    } else if (action == 'update_entry_status') {
        //data.entry_info_status_change = document.getElementById("entryStatus_" + entryID).value;
        var updMsgBox = 'updStatusMsg' + entryID;
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
                    var displayStatus = document.getElementsByClassName("status_"+entryID);                    
                    for (var i = 0; i < displayStatus.length; i++) {
                        displayStatus[i].innerHTML = data.entry_info_status_change;                       
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