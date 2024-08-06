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

        //Entry Status
        var updateStatus = document.getElementById("entryStatus_" + entryID);
        if (typeof (updateStatus) != 'undefined' && updateStatus != null) {
            data.append('entry_info_status_change', updateStatus.value);
        }

        //Message Box
        var updMsgBox = 'updAdminMSG' + entryID;
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

/*  RMT code    */
//define default layout for each new row
var resourceArray = [{ 'id': 'reslock', 'class': 'lock', 'display': '' },
    { 'id': 'resitem', 'class': 'noSend', 'display': "dropdown" },
    { 'id': 'restype', 'class': 'editable dropdown', 'display': 'dropdown' },
    { 'id': 'resqty', 'class': 'editable numeric', 'display': 'numeric' },
    { 'id': 'rescomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'resuser', class: '', 'display': '' },
    { 'id': 'resdateupdate', class: '', 'display': '' }
    ];
    
    var attributeArray = [{ 'id': 'attcategory', 'class': '', 'display': "dropdown" },
    { 'id': 'attvalue', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'attcomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'attuser', class: '', 'display': '' },
    { 'id': 'attdateupdate', class: '', 'display': '' }
    ];
    var attentionArray = [{ 'id': 'attnvalue', 'class': '', 'display': "dropdown" },
    { 'id': 'attncomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'attnuser', class: '', 'display': '' },
    { 'id': 'attndateupdate', class: '', 'display': '' }
    ];

//function to add a new row to the RMT table for resource, attribute or attention
function addRow(addTo,entryID) {    
    var tableRow = '';
	if (addTo == 'resource') {
		//add resource
		type = 'res';
		dataArray = resourceArray;
	} else if (addTo == 'attribute') {
		//add attribute
		type = 'att';
		dataArray = attributeArray;
	} else if (addTo == 'attention') {
		//add attribute
		type = 'attn';
		dataArray = attentionArray;
	}

    var tableRow = '<tr id="' + type + 'RowNew">';

	//build table columns
	for (i = 0; i < dataArray.length; i++) {
		tableRow += '<td  class="' + dataArray[i]['class'] + '" id="' + dataArray[i]['id'] + '">';
		if (dataArray[i]['display'] == 'dropdown') {
			tableRow += buildDropDown(dataArray[i]['id']);
		} else if (dataArray[i]['display'] == 'numeric') {
			tableRow += '<input  size="4"  class="thVal" type="number" />';
		} else if (dataArray[i]['display'] == 'text') {
			tableRow += '<input  size="4"  class="thVal" type="text" />';
		} else if (dataArray[i]['display'] == 'textarea') {
			tableRow += '<textarea  class="thVal" cols="20" rows="4"></textarea>';
		} else {
			tableRow += dataArray[i]['display'];
		}
		tableRow += '</td>';
	}

	//add action row
	tableRow += '<td id="actions" class="noSend delete">' +
		'<span onclick="insertRowDB(\'' + type + '\')">' +
		'<i class="fas fa-check"></i>' +
		'</span>' +
		'<span onclick="jQuery(\'#' + type + 'RowNew\').remove();">' +  //Rio - convert me
		'<i class="fas fa-ban"></i>' +
		'</span>' +
		'</td>';
	tableRow += '</tr>';

    var tbody = document.querySelector("#rmt" + entryID+" #"+type+"Table tbody");
    if (tbody.children().length == 0) {
		tbody.html(tableRow);
	} else {
		jQuery('#' + type + 'Table > tbody > tr:first').before(tableRow); //Rio - convert me
	}
}

//RMT delete assigned rmt values
function resAttDelete(currentEle, entryID) {
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
    if (r == true) {
        //delete the row
        const element = document.getElementById(currentEle);
        element.remove();

        var fieldData = breakDownEle(currentEle);
        var rowID = currentEle.replace("Row", "");
        var rowID = rowID.replace("attn", "");
        var rowID = rowID.replace("att", "");
        var rowID = rowID.replace("res", "");

        //send delete
        var data = {
            'action': 'delete-entry-resAtt',
            'ID': rowID,
            'entry_id': entryID,
            'table': fieldData['table']
        };

        var xhr = new XMLHttpRequest();
        xhr.open("POST", ajaxurl);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(new URLSearchParams(data));

        xhr.onreadystatechange = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                try {
                    //response = JSON.parse(xhr.response);
                } catch (e) {

                }
            }
        }
    }
}

//lock/unlock a RMT resource
function resAttLock(currentEle, lock) {
    var lockBit = 0;
    if (lock == 0) {
        lockBit = 1;
    }

    var newLock = '<i class="bi bi-unlock-fill"></i>';
    if (lock == 0) {
        newLock = '<i class="bi bi-lock-fill"></i>';
    }

    var lockHtml = '<span class="lockIcon" onclick="resAttLock(\'' + currentEle + '\',' + lockBit + ')">' + newLock + '</span>';

    document.getElementById(currentEle + '_lock').innerHTML = lockHtml;

    var fieldData = breakDownEle(currentEle);
    var rowID = currentEle.replace("Row", "");
    var rowID = rowID.replace("attn", "");
    var rowID = rowID.replace("att", "");
    var rowID = rowID.replace("res", "");
    //send delete
    var data = {
        'action': 'update-lock-resAtt',
        'ID': rowID,
        'lock': lock,
        'table': fieldData['table']
    };

    var xhr = new XMLHttpRequest();
    xhr.open("POST", ajaxurl);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(new URLSearchParams(data));

    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            try {
                //response = JSON.parse(xhr.response);
            } catch (e) {

            }
        }
    }
}

/* input - fieldID
 *      format - typeFieldName_dataID
 * outuput - fieldData
 *      format - array(db table name, section type (res or att),field name, data id )
 */
function breakDownEle(currentEle) {
    var fieldData = [];
    if (currentEle.indexOf("res") != -1) { //resource table
        fieldData['table'] = 'wp_rmt_entry_resources';
        var type = 'res';
    } else if (currentEle.indexOf("attn") != -1) { //resource table
        fieldData['table'] = 'wp_rmt_entry_attn';
        var type = 'attn';
    } else if (currentEle.indexOf("att") != -1) { //attribute table
        fieldData['table'] = 'wp_rmt_entry_attributes';
        var type = 'att';
    }
    fieldData['type'] = type;
    //remove the type from the field
    currentEle = currentEle.replace(type, "");
    //get field name (data prior to the _)
    fieldData['fieldName'] = currentEle.substr(0, currentEle.indexOf('_'));
    //get data ID  (data after the _)
    fieldData['ID'] = currentEle.substr(currentEle.indexOf("_") + 1);
    return fieldData;
}

//RIO - conver this
jQuery('.editable').click(function(e) {
    var that = jQuery(this);
    if (that.find('input').length > 0 || that.find('textarea').length > 0 || that.find('select').length > 0) {
        return;
    }
    var i = 0;
    var id = jQuery(this).attr('id');
    e.stopPropagation();      //<-------stop the bubbling of the event here
    var value = jQuery('#' + id).html();

    updateVal('#' + id, value);
});

//create input or textarea elements where admin can edit values
function updateVal(currentEle, value) {
    if (jQuery(currentEle).hasClass('textAreaEdit')) {
        var cols = Math.round(jQuery(currentEle).width() / 10); //determine how many columns wide the textarea should be
        jQuery(currentEle).html('<textarea class="thVal" cols="' + cols + '" rows="4">' + value + '</textarea>');
    } else if (jQuery(currentEle).hasClass('dropdown')) {
        //build dropdown
        var fieldData = breakDownEle(currentEle.replace("#", ""));

        if (fieldData['fieldName'] == 'type') {
            var type_id = jQuery('#restype_' + fieldData['ID']).attr('data-typeID');
            var item_id = jQuery('#resitem_' + fieldData['ID']).attr('data-itemID');
            setType(item_id, type_id, fieldData['ID']);
        }
    } else if (jQuery(currentEle).hasClass('numeric')) {
        jQuery(currentEle).html('<input class="thVal" type="number" value="' + value + '" />');
    } else {
        jQuery(currentEle).html('<input class="thVal" maxlength="4" type="text" size="4" value="' + value + '" />');
    }

    jQuery(".thVal").focus();
    jQuery(".thVal").focusout(function() {
        //update value in db
        updateDB(jQuery(".thVal").val().trim(), currentEle);
        if (jQuery(currentEle).hasClass('dropdown')) {
            jQuery(currentEle).html(jQuery(".thVal").find("option:selected").text());
        } else {
            jQuery(currentEle).html(jQuery(".thVal").val().trim());
        }
    });
}