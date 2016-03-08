jQuery( document ).ready(function() {
  var $tableheader = jQuery('th#header');
	var $tableheadentry = jQuery('th#details');

	jQuery(function() {
    jQuery($tableheadentry).click(
      function() {
        jQuery(this) .parents('table.entry-detail-view') .children('tbody') .toggle();
      }
    );
    jQuery($tableheader).click(
      function() {
        jQuery(this) .parents('table.entry-detail-view') .children('tbody') .toggle();
      }
    );

    jQuery('#datetimepicker').datetimepicker({value:'2015/04/15 05:03',step:10});
    jQuery('#datetimepickerstart').datetimepicker({
      formatTime:'g:i a',
      formatDate:'d.m.Y',
      defaultTime:'10:00 am'
    });
    jQuery('#datetimepickerend').datetimepicker({
      formatTime:'g:i a',
      formatDate:'d.m.Y',
      defaultTime:'10:00 am'
    });

    jQuery('#gf_admin_page_title').click(
      function() {
        window.location="/wp-admin/admin.php?page=gf_entries&view=entry&id=20&lid="+prompt('Enter your ID!', ' ');
      }
    );
  });

  //function to make certain fields in the individual entry display stand out
  //and to apply the same class to the parent field.
  jQuery('.entryStandout').each(function(){
    jQuery(this).parent().addClass("standoutParent");
  });

  //on update of rating submit ajax to update value in database
  jQuery('.star-rating :radio').change(
    function(){
      var entry_id = jQuery("input[name=entry_info_entry_id]").val();;
      var data = {
        'action': 'update-entry-rating',
        'rating_entry_id': entry_id,
        'rating': this.value,
        'rating_user': userSettings.uid
      };
      jQuery.post(ajaxurl, data, function(response) {
        jQuery('#updateMSG').text(response);
      });
    }
  );

  /* gf entry summary tabs */
  jQuery( "#tabs" ).tabs({
    active: 0
  });
  /* gf entry summary resource section - allow 'real time' edit of values */
  jQuery('.editable').click(function(e){
    var that = jQuery(this);
    if (that.find('input').length > 0 || that.find('textarea').length > 0 || that.find('select').length > 0) {
        return;
    }
    var i=0;
    var id=jQuery(this).attr('id');
    e.stopPropagation();      //<-------stop the bubbling of the event here
    var value = jQuery('#'+id).html();

    updateVal('#'+id, value);
  });

  //create input or textarea elements where admin can edit values
  function updateVal(currentEle, value) {
    if(jQuery(currentEle).hasClass('textAreaEdit')){
      var cols = Math.round(jQuery(currentEle).width()/10); //determine how many columns wide the textarea should be
      jQuery(currentEle).html('<textarea class="thVal" cols="'+cols+'" rows="4">'+value+'</textarea>');
    }else if(jQuery(currentEle).hasClass('dropdown')){
      //build dropdown
      var fieldData = breakDownEle(currentEle.replace("#", ""));

      if(fieldData['fieldName']=='type'){
        var type_id = jQuery('#restype_'+fieldData['ID']).attr('data-typeID');
        var item_id = jQuery('#resitem_'+fieldData['ID']).attr('data-itemID');
        setType(item_id,type_id,fieldData['ID']);
      }
    }else if(jQuery(currentEle).hasClass('numeric')) {
      jQuery(currentEle).html('<input class="thVal" type="number" value="'+value+'" />');
    }else{
      jQuery(currentEle).html('<input class="thVal" maxlength="4" type="text" size="4" value="'+value+'" />');
    }
    console.log(jQuery(currentEle).html());
    jQuery(".thVal").focus();
    jQuery(".thVal").focusout(function () {
      //update value in db
      updateDB(jQuery(".thVal").val().trim(),currentEle);
      if(jQuery(currentEle).hasClass('dropdown')){
        jQuery(currentEle).html(jQuery(".thVal").find("option:selected").text());
      }else{
        jQuery(currentEle).html(jQuery(".thVal").val().trim());
      }
    });
  }

  //update custom meta fields on change
  jQuery(" .metafield").change(function(){

    var meta_field = jQuery(this).attr('id');

    //set meta field status to a processing spinner
    jQuery("#"+meta_field+'Status').html('<i class="fa fa-spinner fa-spin fa-2x"></i>');

    //update meta field on GF entry screen
    var entry_id = jQuery("input[name=entry_info_entry_id]").val();
    var data = {
      'action': 'update-entry-meta',
      'meta_entry_id': entry_id,
      'meta_key': meta_field,
      'meta_value': this.value,
    };

    jQuery.post(ajaxurl, data, function(response) {
      if(response=='updated'){
        //after update - set meta field status to success
        jQuery("#"+meta_field+'Status').html('<i style="color:green" class="fa fa-check"></i>');
      }else{
        //after update - set meta field status to failed
        jQuery("#"+meta_field+'Status').html('<i style="color:red" class="fa fa-times"></i>');
      }
    });
  });
});

/* input - fieldID
 *      format - typeFieldName_dataID
 * outuput - fieldData
 *      format - array(db table name, section type (res or att),field name, data id )
 */
function breakDownEle(currentEle){
  var fieldData = [];
  if(currentEle.indexOf("res")!=-1){ //resource table
    fieldData['table'] = 'wp_rmt_entry_resources';
    var type    = 'res';
  }else if(currentEle.indexOf("att")!=-1){ //attribute table
    fieldData['table'] = 'wp_rmt_entry_attributes';
    var type   = 'att';
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
function addRow(addTo){
  var tableRow = '';
  if(addTo=='resource'){
    //add resource
    type = 'res';
    dataArray = resourceArray;
  }else if(addTo=='attribute'){
    //add attribute
    type = 'att';
    dataArray = attributeArray;
  }

  var tableRow = '<tr id="'+type+'RowNew">';
  //build table columns
  for (i = 0; i < dataArray.length; i++) {
    tableRow += '<td  class="'+dataArray[i]['class']+'" id="'+dataArray[i]['id']+'">';
    if(dataArray[i]['display']=='dropdown'){
      tableRow += buildDropDown(dataArray[i]['id']);
    }else if(dataArray[i]['display']=='numeric'){
      tableRow += '<input  size="4"  class="thVal" type="number" />';
    }else if(dataArray[i]['display']=='text'){
      tableRow += '<input  size="4"  class="thVal" type="text" />';
    }else if(dataArray[i]['display']=='textarea'){
      tableRow += '<textarea  class="thVal" cols="20" rows="4"></textarea>';
    }else{
      tableRow += dataArray[i]['display'];
    }
    tableRow += '</td>';
  }
  //add action row
  tableRow += '<td id="actions" class="noSend"><p onclick="insertRowDB(\''+type+'\')"><i class="fa fa-check"></i></p><p onclick="jQuery(\'#'+type+'RowNew\').remove();"><i class="fa fa-ban"></i></p></td>';
  tableRow += '</tr>';
  var tbody = jQuery('#'+type+'Table tbody');

  if (tbody.children().length == 0) {
    tbody.html(tableRow);
  }else{
    alert('but not here');
    jQuery('#'+type+'Table > tbody > tr:first').before(tableRow);
  }
}

//item drop down for entry resources
function buildDropDown(type){
  var itemSel = '';
  if(type=='resitem'){
    var itemSel = '<select onchange="setType(this.value,\'\',\'\')" class="thVal"><option>Select Item</option>';
    jQuery.each(items, function(objKey,objValue) {
       itemSel += '<option value="'+objValue.key+'">'+objValue.value+'</option>';
    });
    itemSel += '</select>';
  }else if(type=='attcategory'){
    var itemSel = '<select class="thVal"><option>Select Item</option>';
    jQuery.each(attributes, function(objKey,objValue) {
       itemSel += '<option value="'+objValue.key+'">'+objValue.value+'</option>';
    });
    itemSel += '</select>';
  }
  return itemSel;
}
var resourceArray=[{'id':'resitem','class':'noSend','display':"dropdown"},
                   {'id':'restype','class':'editable dropdown','display':'dropdown'},
                   {'id':'resqty','class':'editable numeric','display':'numeric'},
                   {'id':'rescomment','class':'editable textareaEdit','display':'textarea'},
                   {'id':'resuser',class:'','display':''},
                   {'id':'resdateupdate',class:'','display':''}
                 ];
var attributeArray=[{'id':'attcategory','class':'','display':"dropdown"},
                   {'id':'attvalue','class':'editable textareaEdit', 'display':'textarea'},
                   {'id':'attcomment','class':'editable textareaEdit','display':'textarea'},
                   {'id':'attuser',class:'','display':''},
                   {'id':'attdateupdate',class:'','display':''}
                 ];
function setType(itemID,typeID,id){ //build type drop down based on item drop down
  if (types[itemID]) {
    var options = '<option value = "">Select Type</option>';
    for (i in types[itemID]) {
      var type = types[itemID][i];
      selected = '';
      if(i==typeID) selected = 'selected';
      options += '<option value = "' + i + '" '+selected+'>' + type + '</option>';
    }

    var typeSel = '<select class="thVal">'+options+'</select>';
    if(id==''){
      jQuery('#resRowNew #restype').html(typeSel);
    }else{
      jQuery('#resRow'+id+' #restype_'+id).html(typeSel);
    }
  }
}
function resAttDelete(currentEle){
  var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
  if (r == true) {
    jQuery(currentEle).remove(); //delete the row
    currentEle = currentEle.replace("#", ""); //remove hashtag
    var fieldData = breakDownEle(currentEle);
    var rowID = currentEle.replace("Row", "");
    var rowID = rowID.replace("att", "");
    var rowID = rowID.replace("res", "");
    //send delete
    var data = {
        'action': 'delete-entry-resAtt',
        'ID': rowID,
        'table': fieldData['table']
      };
    jQuery.post(ajaxurl, data, function(response) {
      //
    });
  }
}
function insertRowDB(type){
  var fieldNames = '';var fieldValues = '';
  var insertArr = {};
  insertArr['entry_id'] = jQuery('[name="entry_info_entry_id"]').val();
  //update DB table with AJAX
  jQuery('#'+type+'RowNew td').each(function(i, obj) {
    //set fieldNames and fieldValues to a comma separated string of data
    value = jQuery(this).find(".thVal").val();
    if(!jQuery(this).hasClass('noSend')){
      //get field name from column id
      fName = jQuery(this).attr('id');
      //remove the type from the field name (i.e.res or att)
      fName = fName.replace(type, "");
      if(fName =='type')     fName ='resource_id';
      if(fName =='category') fName ='attribute_id';

      insertArr[fName]=value;
    }
    that = jQuery(this).find(".thVal");
    //display the dropdown value not the id
    if( that.is('select') ) {
      jQuery(this).html(jQuery(that).find("option:selected").text());
    }else{
      //set textarea and input to html
      jQuery(this).html(value);
    }

  });

  if(type=='res'){
    var table     = 'wp_rmt_entry_resources';
    var dataArray = resourceArray;
  }else if(type=='att'){
    var table     = 'wp_rmt_entry_attributes';
    var dataArray = attributeArray;
  }
  var data = {
        'action': 'update-entry-resAtt',
        'insertArr': insertArr,
        'ID': 0,
        'table': table
      };
  jQuery.post(ajaxurl, data, function(response) {
    //set actions column
    jQuery('#resRowNew #actions').html('<p onclick="jQuery(\'#resRow'+response.ID+'\').remove();"><i class="fa fa-minus-circle"></i></p>');

    //update fields with returned row id
    for (i = 0; i < dataArray.length; i++) {
      jQuery('#'+type+'RowNew #'+dataArray[i]['id']).attr('id',dataArray[i]['id']+'_'+response.ID);
    }

    //after adding row set row id to the correct value
    jQuery('#resRowNew').attr('id','resRow'+response.ID);

    //update the date/time and user info
    jQuery('#'+type+'user_'+response.ID).html(response.user);
    jQuery('#'+type+'dateupdate_'+response.ID).html(response.dateupdate);
  });
}
function updateDB(newVal,currentEle){
  var fieldName = '';  var ID = ''; var table=""; var type="";
  //remove #
  currentEle = currentEle.replace("#", "");
  var fieldData = breakDownEle(currentEle);
  if(fieldData['fieldName'] =='type')     fieldData['fieldName'] ='resource_id';
  if(fieldData['fieldName'] =='category') fieldData['fieldName'] ='attribute_id';
  if (fieldData['fieldName']=='' || fieldData['ID']==''){
    //error
  }
  //update DB table in AJAX
  var data = {
        'action': 'update-entry-resAtt',
        'fieldName': fieldData['fieldName'],
        'ID': fieldData['ID'],
        'table': fieldData['table'],
        'newValue':newVal
      };
  jQuery.post(ajaxurl, data, function(response) {
    //update the date/time and user info
    jQuery('#'+fieldData['type']+'user_'+fieldData['ID']).html(response.user);
    jQuery('#'+fieldData['type']+'dateupdate_'+fieldData['ID']).html(response.dateupdate);
  });
}