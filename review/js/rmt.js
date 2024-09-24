var ajaxurl = '/wp-admin/admin-ajax.php';

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
function addRow(addTo, entryID) {    
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
                    '<span onclick="insertRowDB(\'' + type + '\', ' + entryID + ')">' +
                        '<i class="fas fa-check"></i>' +
                    '</span>' +
                    '<span onclick="document.querySelector(\'#rmt' + entryID + ' #' + type + 'RowNew\').remove();">' +
                        '<i class="fas fa-ban"></i>' +
                    '</span>' +
		        '</td>';
	tableRow += '</tr>';

    var tbody = document.querySelector("#rmt" + entryID+" #"+type+"Table tbody");

    if (!tbody.childNodes.length) {
		tbody.innerHTML = tableRow;
	} else {
        var nodes = document.querySelectorAll('#rmt' + entryID + ' #' + type + 'Table > tbody > tr');
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
        //delete the row for this entry
        const element = document.querySelector("#rmt" + entryID + " #" + currentEle);
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
function resAttLock(currentEle, lock, entryID) {
    var lockBit = 0;
    if (lock == 0) {
        lockBit = 1;
    }

    var newLock = '<i class="fas fa-lock-open fa-lg"></i>';
    if (lock == 0) {
        newLock = '<i class="fas fa-lock fa-lg"></i>';
    }

    var lockHtml = '<span class="lockIcon" onclick="resAttLock(\'' + currentEle + '\',' + lockBit + ')">' + newLock + '</span>';
    document.querySelector(currentEle + ' .lock').innerHTML = lockHtml;

    var fieldData = breakDownEle(currentEle);
    var rowID = currentEle.replace("Row", "").replace("attn", "").replace("att", "").replace("res", "").replace("#", "");

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
                response = JSON.parse(xhr.response);
                //console.log(response);
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

function insertRowDB(type, entryID) {

	var allSelected = true;
	var insertArr = {};
	insertArr['entry_id'] = document.querySelector('[name="entry_info_entry_id"]').value;
	if( type == "res" && (!document.querySelector("#resitem .thVal").value || !document.querySelector("#restype .thVal").value || !document.querySelector("#resqty .thVal").value) ) {
        allSelected = false;
    }
	//update DB table with AJAX
    Array.from(document.querySelectorAll('#rmt' + entryID + ' #' + type + 'RowNew td')).forEach(function(el, index){
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
        document.querySelector('#' + type + 'RowNew #actions').innerHTML = '<i class="fas fa-spinner fa-lg"></i>';
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
        data["insertArr[entry_id]"] = entryID;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", ajaxurl);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(new URLSearchParams(data));

        xhr.onreadystatechange = function () {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                try {
                    response = JSON.parse(xhr.response);
                    //console.log(response);
                    //set actions column
                    document.querySelector('#' + type + 'RowNew #actions').innerHTML = '<span onclick="resAttDelete(\'' + type + 'Row' + response.ID + '\', ' + entryID + ')"><i class="fa fa-circle-minus fa-lg"></i></span></td>';

                    //set item to locked
                    document.querySelector('#' + type + 'RowNew .lock').innerHTML = '<span class="lockIcon" onclick="resAttLock(\'#' + type + 'Row' + response.ID + '\',1)">' + '<i class="fas fa-lock fa-lg"></i>' + '</span>';
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
                response = JSON.parse(xhr.response);
                //console.log(response);
                //console.log(fieldData);
                //update the date/time and user info
                document.querySelector('#' + fieldData['type'] + 'user_' + fieldData['ID']).innerHTML  = response.user;
                document.querySelector('#' + fieldData['type'] + 'dateupdate_' + fieldData['ID']).innerHTML = response.dateupdate;
            } catch (e) {

            }
        }
    }
}