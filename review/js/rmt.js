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

//function to add a new row to the RMT table on page
function addRow(addTo, entryID) {
    var table = document.getElementById(addTo + '_' + entryID);
    var row = table.insertRow(table.rows.length);

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

    //add id to row
    row.setAttribute("class", type + "RowNew", 0);
    var cell = '';

    cell = row.insertCell(0);
    cell.setAttribute("class", 'actionRow', 0);
    cell.innerHTML = '<span onclick="insertRowDB(\'' + type + '\',' + entryID + ')">' + '<i class="bi bi-check-circle"></i></span><br/>' +
        '<span onclick="document.getElementById(\'' + type + 'RowNew\').remove();">' +
        '<i class="bi bi-ban"></i>' +
        '</span>';

    //build table columns (need to add in reverse order)
    for (i = dataArray.length - 1; i >= 0; i--) {
        if (dataArray[i]['display'] == 'dropdown') {
            cell = row.insertCell(0);
            cell.innerHTML = buildDropDown(dataArray[i]['id'], entryID);
        } else if (dataArray[i]['display'] == 'numeric') {
            cell = row.insertCell(0);
            cell.innerHTML = '<input  size="4"  class="' + dataArray[i]['display'] + '" type="number" />';
        } else if (dataArray[i]['display'] == 'text') {
            cell = row.insertCell(0);
            cell.innerHTML = '<input  size="4"  class="' + dataArray[i]['display'] + '" type="text" />';
        } else if (dataArray[i]['display'] == 'textarea') {
            cell = row.insertCell(0);
            cell.innerHTML = '<textarea  class="' + dataArray[i]['display'] + '" cols="20" rows="4"></textarea>';
        } else {
            cell = row.insertCell(0);
            cell.innerHTML = dataArray[i]['display'];
        }
        cell.setAttribute("class", dataArray[i]['class'], 0);
        cell.setAttribute("class", dataArray[i]['id'], 0);
    }
    //empty cell for the id to be set
    cell = row.insertCell(0);
    cell.innerHTML = '';

    return;
}

//build the item drop down for entry resources
function buildDropDown(type, entryID) {
    var items = review.rmt.res_items;
    var attributes = review.rmt.att_items;
    var attentions = review.rmt.attn_items;
    var itemSel = '';
    if (type == 'resitem') {
        var itemSel = '<select onchange="setType(this.value,\'\',\'\',\'' + entryID + '\')" class="dropdown">' +
            '<option>Select Item</option>';

        for (const [key, value] of Object.entries(items)) {
            itemSel += '<option value="' + key + '">' + value + '</option>';
        }

        itemSel += '</select>';
    } else if (type == 'attcategory') {
        var itemSel = '<select class="dropdown"><option>Select Item</option>';

        for (const [key, value] of Object.entries(attributes)) {
            itemSel += '<option value="' + key + '">' + value + '</option>';
        }

        itemSel += '</select>';
    } else if (type == 'attnvalue') {
        var itemSel = '<select class="dropdown"><option>Select Item</option>';

        for (const [key, value] of Object.entries(attentions)) {
            itemSel += '<option value="' + key + '">' + value + '</option>';
        }

        itemSel += '</select>';
    }

    return itemSel;
}

//static definition of resource field layout
var resourceArray = [{ 'id': 'reslock', 'class': 'lock', 'display': '' },
{ 'id': 'resitem', 'class': 'noSend', 'display': "dropdown" },
{ 'id': 'restype', 'class': 'editable dropdown', 'display': 'dropdown' },
{ 'id': 'resqty', 'class': 'editable numeric', 'display': 'numeric' },
{ 'id': 'rescomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
{ 'id': 'resuser', class: '', 'display': '' },
{ 'id': 'resdateupdate', class: '', 'display': '' }
];

//static definition of attribute field layout
var attributeArray = [
{ 'id': 'attcategory', 'class': '', 'display': "dropdown" },
{ 'id': 'attvalue', 'class': 'editable textareaEdit', 'display': 'textarea' },
{ 'id': 'attcomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
{ 'id': 'attuser', class: '', 'display': '' },
{ 'id': 'attdateupdate', class: '', 'display': '' }
];

//static definition of attention field layout
var attentionArray = [{ 'id': 'attnvalue', 'class': '', 'display': "dropdown" },
{ 'id': 'attncomment', 'class': 'editable textareaEdit', 'display': 'textarea' },
{ 'id': 'attnuser', class: '', 'display': '' },
{ 'id': 'attndateupdate', class: '', 'display': '' }
];

//build the resource type drop down based on item drop down
function setType(itemID, typeID, id, entryID) {
    //find the row we are trying to set the type for
    var row = document.getElementById('resource_' + entryID).querySelector('.resRowNew');
   
    var types = review.rmt.res_types;

    if (types[itemID]) {
        var options = '<option value = "">Select Type</option>';
        for (const [key, type] of Object.entries(types[itemID])) {
            selected = '';
            if (key == typeID) selected = 'selected';
            options += '<option value = "' + key + '" ' + selected + '>' + type + '</option>';
        }

        var typeSel = '<select class="dropdown">' + options + '</select>';
        row.querySelector('.restype').innerHTML = typeSel;
    }
}

//add RMT items to the database
function insertRowDB(type, entryID) {    
    var insertArr = {};
    insertArr['entry_id'] = entryID;

    //if the type is resource, need to make sure everything is filled out correctly
    if (type == "res") { //resource
        var row         = document.getElementById('resource_' + entryID).querySelector('.resRowNew');
        var resitemIdx  = row.querySelector('.resitem').querySelector('.dropdown').selectedIndex;
        var resitem     = row.querySelector('.resitem').querySelector('.dropdown').options[resitemIdx];
        
        var restypeIdx  = row.querySelector('.restype').querySelector('.dropdown').selectedIndex;
        var restype     = row.querySelector('.restype').querySelector('.dropdown').options[restypeIdx];

        var resqty      = row.querySelector('.resqty').querySelector('.numeric');
        var comment     = row.querySelector('.rescomment').querySelector('.textarea').value;

        //must select a resource item, type and quantity
        if (resitem.value == '' || restype.value == '' || resqty.value == '') {
            alert("You must select a resource item, type and enter a quantity.");
            return;
        }

        //set data to pass in ajax
        insertArr['resource_id'] = restype.value;
        insertArr['qty']         = resqty.value;
        insertArr['comment']     = comment;
        table                    = 'wp_rmt_entry_resources';
        action                   = 'update-entry-resAtt';
    } else if (type == 'att') { //attribute        
        var row         = document.getElementById('attribute_' + entryID).querySelector('.attRowNew');                
        var attCatIdx   = row.querySelector('.attcategory').querySelector('.dropdown').selectedIndex;
        var attcategory = row.querySelector('.attcategory').querySelector('.dropdown').options[attCatIdx];

        var attvalue    = row.querySelector('.attvalue').querySelector('.textarea').value;
        var comment     = row.querySelector('.attcomment').querySelector('.textarea').value;

        //must select an attribute
        if (attcategory.value == '') {
            alert("You must select an attribute item.");
            return;
        }

        //prepare data to pass in ajax
        var table = 'wp_rmt_entry_attributes';
        insertArr['attribute_id']   = attcategory.value;
        insertArr['value']          = attvalue;
        insertArr['comment']        = comment;
    } else if (type == 'attn') {
        //must at least select an attention value
        var row         = document.getElementById('attention_' + entryID).querySelector('.attnRowNew');
        
        var attnCatIdx  = row.querySelector('.attnvalue').querySelector('.dropdown').selectedIndex;
        var attnvalue   = row.querySelector('.attnvalue').querySelector('.dropdown').options[attnCatIdx];
        var comment     = row.querySelector('.attncomment').querySelector('.textarea').value;

        //must select an attention
        if (attnvalue.value == '') {
            alert("You must select an attention item.");
            return;
        }

        //prepare data to pass in ajax
        var table = 'wp_rmt_entry_attn';
        insertArr['attn_id'] = attnvalue.value;
        insertArr['comment'] = comment;
    }

    row.querySelector('.actionRow').innerHTML= 'Please Wait';
    axios.post(ajaxurl, {
        'action': 'update-entry-resAtt',
        'insertArr': insertArr,
        'ID': 0,
        'table': table
    }, {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    }).then(function (resData) {
        response = resData.data;
        //if everything worked, let's update the row
        if(response.message =='Saved') {
            
            id = response.ID;
            //update the id
            row.setAttribute("id", type + "Row"+id, 0);
            row.setAttribute("class", '', 0);

            if(type=='res'){
                //set item to locked                
                row.querySelector('.reslock').innerHTML = '<span class="lockIcon" onclick="resAttLock(\'resRow'+id,1+'\')"><i class="bi bi-lock-fill"></i></span>';
                row.querySelector('.reslock').setAttribute("id", 'resRow'+id+'_lock', 0);

                //set item to text instead of dropdown             
                row.querySelector('.resitem').innerHTML = resitem.text;
                row.querySelector('.resitem').setAttribute("id", 'resRow'+id+'_item', 0);

                //set type to text instead of dropdown
                row.querySelector('.restype').setAttribute("id", 'resRow'+id+'_type', 0);
                row.querySelector('.restype').innerHTML = restype.text;

                //set qty to text instead of input field
                row.querySelector('.resqty').setAttribute("id", 'resRow'+id+'_qty', 0);
                row.querySelector('.resqty').innerHTML = resqty.value;               
            } else if (type == 'att') { //attribute  
                //set id of row with newly added attribute
                row.querySelector('.attcategory').innerHTML = attcategory.text;

                //set category to text instead of dropdown                             
                row.querySelector('.attcategory').setAttribute("id", 'attRow'+id+'_attrribute', 0);    
                 
                //set value to text instead of input field
                row.querySelector('.attvalue').setAttribute("id", 'attRow'+id+'_value', 0);
                row.querySelector('.attvalue').innerHTML = attvalue;     
            } else if (type == 'attn') { //attention
                //set id of row with newly added attribute
                row.querySelector('.attnvalue').setAttribute("id", 'attnRow'+id+'_attrribute', 0);    

                //set value to text instead of dropdown             
                row.querySelector('.attnvalue').innerHTML = attnvalue.text;                
            }

            //set comment to text instead of input field
            row.querySelector('.'+type+'comment').setAttribute("id", type+'Row'+id+'_comment', 0);
            row.querySelector('.'+type+'comment').innerHTML = comment;

            //set user
            row.querySelector('.'+type+'user').innerHTML = response.user;

            //set date/time        
			row.querySelector('.'+type+'dateupdate').innerHTML = response.dateupdate;

            //set action row to delete icon
            row.querySelector('.actionRow').innerHTML= '<span onclick="resAttDelete(\''+type+'Row'+id+'\','+id+')"><i class="bi bi-dash-circle" "=""></i></span>';
        }
    
    })
        .catch(function (error) {
            console.log(error);
        });

}