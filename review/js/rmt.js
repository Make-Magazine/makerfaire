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
var resourceArray = [
    { 'id': 'reslock', 'class': 'lock', 'display': '' },
    { 'id': 'resitem', 'class': 'noSend firstdrop', 'display': "dropdown" },
    { 'id': 'restype', 'class': 'editable dropdown', 'display': 'dropdown' },
    { 'id': 'resqty', 'class': 'editable numeric', 'display': 'numeric' },
    { 'id': 'rescomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'resuser', class: '', 'display': '' },
    { 'id': 'resdateupdate', class: '', 'display': '' }
];
var attributeArray = [
    { 'id': 'attcategory', 'class': 'firstdrop', 'display': "dropdown" },
    { 'id': 'attvalue', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'attcomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
    { 'id': 'attuser', class: '', 'display': '' },
    { 'id': 'attdateupdate', class: '', 'display': '' }
];
var attentionArray = [
    { 'id': 'attnvalue', 'class': 'firstdrop', 'display': "dropdown" },
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
                    '<span onclick="document.querySelector(\'#' + type + 'RowNew\').remove();">' +
                        '<i class="fas fa-ban"></i>' +
                    '</span>' +
		        '</td>';
	tableRow += '</tr>';

    var tbody = document.querySelector("#rmt" + entryID+" #"+type+"Table tbody");

    if (!tbody.childNodes.length) {
		tbody.innerHTML = tableRow;
	} else {
        var nodes = document.querySelectorAll('#' + type + 'Table > tbody > tr');
		nodes[0].insertAdjacentHTML('beforebegin', tableRow); 
	}
}

function setType(itemID, typeID, id) { //build type drop down based on item drop down
    // types is set in review-vue.js
	if (types[itemID]) {
		var options = '<option value = "">Select Type</option>';
		for (i in types[itemID]) {
			var type = types[itemID][i];
			selected = '';
			if (i == typeID) selected = 'selected';
			options += '<option value = "' + i + '" ' + selected + '>' + type + '</option>';
		}

		var typeSel = '<select class="thVal">' + options + '</select>';
		if (id == '') {
			document.querySelector('#resRowNew #restype').innerHTML = typeSel;
		} else {
			document.querySelector('#resRow' + id + ' #restype_' + id).innerHTML = typeSel;
		}
	}
}

//item drop down for entry resources
function buildDropDown(type) {
	var itemSel = '';
	if (type == 'resitem') {
		var itemSel = '<select onchange="setType(this.value,\'\',\'\')" class="thVal"><option>Select Item</option>';
        // items object is set in review-vue.js
        Object.entries(items).forEach(([key, val]) => {
            itemSel += '<option value="' + key + '">' + val + '</option>';
		});
		itemSel += '</select>'; 
	} else if (type == 'attcategory') {
		var itemSel = '<select class="thVal"><option>Select Item</option>';
		Object.entries(attributes).forEach(([key, val]) => {
			itemSel += '<option value="' + val.key + '">' + val.value + '</option>';
		});
		itemSel += '</select>';
	} else if (type == 'attnvalue') {
		var itemSel = '<select class="thVal"><option>Select Item</option>';
		Object.entries(attention).forEach(([key, val]) => {
			itemSel += '<option value="' + val.key + '">' + val.value + '</option>';
		});
		itemSel += '</select>';
	}

	return itemSel;
}

//RMT delete assigned rmt values
function resAttDelete(currentEle, entryID) {
    var r = confirm("Are you sure want to delete this row (this cannot be undone)!!!");
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
                    response = JSON.parse(xhr.response);
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
    document.querySelector(currentEle + ' .lock').innerHTML = lockHtml;

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

// wait for the .editable element to exist before adding the click function
waitForElm('.editable').then((elm) => {
    var editableElms = document.querySelectorAll(".editable")
    Array.from(editableElms).forEach(elm => {
       elm.addEventListener('click', function(event){
            if (elm.querySelectorAll('input').length > 0 || elm.querySelectorAll('textarea').length > 0 || elm.querySelectorAll('select').length > 0) {
                return;
            }
            var id = elm.getAttribute('id');
            event.stopPropagation();      //<-------stop the bubbling of the event here
            var value = document.querySelector('#' + id).innerHTML;

            updateVal('#' + id, value);
        });
    });
});

//create input or textarea elements where admin can edit values
function updateVal(currentEle, value) {
    if (document.querySelector(currentEle).classList.contains('textAreaEdit')) {
        var cols = Math.round(document.querySelector(currentEle).offsetWidth / 10); //determine how many columns wide the textarea should be
        document.querySelector(currentEle).innerHTML = '<textarea class="thVal" cols="' + cols + '" rows="4">' + value + '</textarea>';
    } else if (document.querySelector(currentEle).classList.contains('dropdown')) {
        //build dropdown
        var fieldData = breakDownEle(currentEle.replace("#", ""));

        if (fieldData['fieldName'] == 'type') {
            var type_id = document.querySelector('#restype_' + fieldData['ID']).getAttribute('data-typeID');
            var item_id = document.querySelector('#resitem_' + fieldData['ID']).getAttribute('data-itemID');
            setType(item_id, type_id, fieldData['ID']);
        }
    } else if (document.querySelector(currentEle).classList.contains('numeric')) {
        document.querySelector(currentEle).innerHTML = '<input class="thVal" type="number" value="' + value + '" />';
    } else {
        document.querySelector(currentEle).innerHTML = '<input class="thVal" maxlength="4" type="text" size="4" value="' + value + '" />';
    }

    document.querySelector(".thVal").focus();
    document.querySelector(".thVal").addEventListener("focusout", (event) => {
        //update value in db
        updateDB(document.querySelector(".thVal").value.trim(), currentEle);
        if (document.querySelector(currentEle).classList.contains('dropdown')) {
            var that = document.querySelector(".thVal");
            document.querySelector(currentEle).innerHTML = that.options[that.selectedIndex].innerHTML;
        } else {
            document.querySelector(currentEle).innerHTML = document.querySelector(".thVal").value.trim();
        }
    });
}

function insertRowDB(type) {

	var allSelected = true;
	var insertArr = {};
	insertArr['entry_id'] = document.querySelector('[name="entry_info_entry_id"]').value;
	if( type == "res" && (!document.querySelector("#resitem .thVal").value || !document.querySelector("#restype .thVal").value || !document.querySelector("#resqty .thVal").value) ) {
        allSelected = false;
    }
	//update DB table with AJAX
    Array.from(document.querySelectorAll('#' + type + 'RowNew td')).forEach(function(el, index){
        // this conditional is to determine which columns are edited and need to be changed after approval
        if(el.classList.contains("editable") || el.classList.contains("firstdrop") ) {
            value = el.querySelector(".thVal").value;
            // set fieldNames and fieldValues to a comma separated string of data
            if (!el.classList.contains('noSend')) {
                //get field name from column id
                fName = el.getAttribute('id');
                ////remove the type from the field name (i.e.res or att)
                fName = fName.replace(type, "");
                if (fName == 'type' && type == 'res') fName = 'resource_id';
                if (fName == 'category' && type == 'att') fName = 'attribute_id';
                if (fName == 'value' && type == 'attn') fName = 'attn_id';
                insertArr[fName] = value;
            }
            that = el.querySelector(".thVal");
            
            if(allSelected == true) {
                //display the dropdown value not the id
                if (that.matches('select')) {
                    el.innerHTML = that.options[that.selectedIndex].innerHTML;
                } else {
                    //set textarea and input to html
                    el.innerHTML = value;
                }
            }
        }
	});

	if(allSelected == false) {
		alert("You must fill out Item, Type and Qty fields to submit")
	} else {

		if (type == 'res') {
			var table = 'wp_rmt_entry_resources';
			var dataArray = resourceArray;
		} else if (type == 'att') {
			var table = 'wp_rmt_entry_attributes';
			var dataArray = attributeArray;
		} else if (type == 'attn') {
			var table = 'wp_rmt_entry_attn';
			var dataArray = attentionArray;
		}
		var data = {
			'action': 'update-entry-resAtt',			
			'ID': 0,
			'table': table
		};
        // add the insertArr values to our data object to send to ajax
        for (const [key, value] of Object.entries(insertArr)) {
            data["insertArr[" + key + "]"] = value;
        }
        var xhr = new XMLHttpRequest();
        xhr.open("POST", ajaxurl);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(new URLSearchParams(data));

        xhr.onreadystatechange = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                try {
                    response = JSON.parse(xhr.response);
                    //set actions column
                    document.querySelector('#' + type + 'RowNew #actions').innerHTML = '<span onclick="resAttDelete(\'#' + type + 'Row' + response.ID + '\')"><i class="fa fa-circle-minus fa-lg"></i></span></td>';

                    //set item to locked
                    document.querySelector('#' + type + 'RowNew .lock').innerHTML = '<span class="lockIcon" onclick="resAttLock(\'#' + type + 'Row' + response.ID + '\,0)">' + '<i class="fas fa-lock fa-lg"></i>' + '</span>';
                    //update fields with returned row id
                    for (i = 0; i < dataArray.length; i++) {
                        document.querySelector('#' + type + 'RowNew #' + dataArray[i]['id']).setAttribute('id', dataArray[i]['id'] + '_' + response.ID);
                    }

                    //after adding row set row id to the correct value
                    document.querySelector('#' + type + 'RowNew').setAttribute('id', type + 'Row' + response.ID);

                    //update the date/time and user info
                    document.querySelector('#' + type + 'user_' + response.ID).innerHTML = response.user;
                    document.querySelector('#' + type + 'dateupdate_' + response.ID).innerHTML = response.dateupdate;
                } catch (e) {

                }
            }
        }

	}

}
function updateDB(newVal, currentEle) {	
	//remove #
	currentEle = currentEle.replace("#", "");
	var fieldData = breakDownEle(currentEle);
	if (fieldData['fieldName'] == 'type') fieldData['fieldName'] = 'resource_id';
	if (fieldData['fieldName'] == 'category') fieldData['fieldName'] = 'attribute_id';
	if (fieldData['fieldName'] == '' || fieldData['ID'] == '') {
		//error
	}
	//update DB table in AJAX
	var data = {
		'action': 'update-entry-resAtt',
		'fieldName': fieldData['fieldName'],
		'ID': fieldData['ID'],
		'table': fieldData['table'],
		'newValue': newVal,
		'entry_id': document.querySelector('[name="entry_info_entry_id"]').value
	};
    var xhr = new XMLHttpRequest();
    xhr.open("POST", ajaxurl);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send(new URLSearchParams(data));

    xhr.onreadystatechange = function () {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            try {
                //response = JSON.parse(xhr.response);
                //console.log(response);
                //update the date/time and user info
                document.querySelector('#' + fieldData['type'] + 'user_' + fieldData['ID']).innerHTML  = response.user;
                document.querySelector('#' + fieldData['type'] + 'dateupdate_' + fieldData['ID']).innerHTML = response.dateupdate;
            } catch (e) {

            }
        }
    }
}