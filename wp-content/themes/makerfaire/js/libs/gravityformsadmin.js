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
    if (that.find('input').length > 0) {
        return;
    }else if (that.find('textarea').length > 0) {
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
    }else{
      jQuery(currentEle).html('<input class="thVal" maxlength="4" type="text" size="4" value="'+value+'" />');
    }
    jQuery(".thVal").focus();
    /* //if they press the enter key it is backing out of edit
    jQuery(".thVal").keyup(function (event) {
      if (event.keyCode == 13) { //enter key
        //update value in db
        updateDB(jQuery(".thVal").val().trim(),currentEle);
        jQuery(currentEle).html(jQuery(".thVal").val().trim());
      }
    });*/

    jQuery(".thVal").focusout(function () {
      //update value in db
      updateDB(jQuery(".thVal").val().trim(),currentEle);
      jQuery(currentEle).html(jQuery(".thVal").val().trim());
    });
  }
});

function addResRow(){
  var itemSel = '<select onchange="setType(this.value)" class="thVal"><option>Select Item</option>';
  jQuery.each(items, function(objKey,objValue) {
     itemSel += '<option value="'+objValue.key+'">'+objValue.value+'</option>';
  });
  itemSel += '</select>';
  jQuery('#resTable > tbody > tr:first').before('<tr id="resRowNew"><td  class="noSend" id="item">'+itemSel+'</td><td id="resource_id"></td><td id="qty"><input  size="4"  class="thVal" type="text" /></td><td id="comment"><textarea  class="thVal" cols="20" rows="4"></textarea></td><td id="actions" class="noSend"><p onclick="insertRowDB(\'res\')"><i class="fa fa-check"></i></p><p onclick="jQuery(\'#resRowNew\').remove();"><i class="fa fa-ban"></i></p></td></tr>');
}
function setType(item){
  if (types[item]) {
    var options = '<option value = "">Select Type</option>';
    for (i in types[item]) {
      var type = types[item][i];
      options += '<option value = "' + i + '">' + type + '</option>';
    }
    var typeSel = '<select onchange="setType(this.value)" class="thVal">'+options+'</select>';
    jQuery('#resRowNew #resource_id').html(typeSel);
  }
}
function resAttDelete(currentEle){
  var r = confirm("Are you sure want to delete this row (this cannot be undone)!");
  if (r == true) {
    jQuery(currentEle).remove();
    if(currentEle.indexOf("res")!=-1){ //resource table
      var table = 'wp_rmt_entry_resources';
      currentEle = currentEle.replace("#res", "");
    }else if(currentEle.indexOf("att")!=-1){ //attribute table
      var table = 'wp_rmt_entry_attributes';
      currentEle = currentEle.replace("#att", "");
    }
    var ID = currentEle.replace("Row", "");
    //send delete
    var data = {
        'action': 'delete-entry-resAtt',
        'ID': ID,
        'table': table
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
      fName = jQuery(this).attr('id');
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
    var table = 'wp_rmt_entry_resources';
  }else if(type=='att'){
    var table = 'wp_rmt_entry_attributes';
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
    //after adding row set row id to the correct value
    jQuery('#resRowNew').attr('id','resRow'+response.ID);
  });
}
function updateDB(newVal,currentEle){
  var fieldName = '';  var ID = ''; var table="";
  //remove #
  currentEle = currentEle.replace("#", "");
  //are we updating entry resources or entry attributes
  if(currentEle.indexOf("res")!=-1){ //resource table
    table = 'wp_rmt_entry_resources';
    //get field name
    currentEle = currentEle.replace("res", "");
    fieldName = currentEle.substr(0, currentEle.indexOf('_'));
    //get ID
    ID = currentEle.substr(currentEle.indexOf("_") + 1);
  }else if(currentEle.indexOf("att")!=-1){ //attribute table
    table = 'wp_rmt_entry_attributes';
    //get field name
    currentEle = currentEle.replace("att", "");
    fieldName = currentEle.substr(0, currentEle.indexOf('_'));
    //get ID
    ID = currentEle.substr(currentEle.indexOf("_") + 1);
  }
  if (fieldName=='' || ID==''){
    //error
  }
  //update DB table in AJAX
  var data = {
        'action': 'update-entry-resAtt',
        'fieldName': fieldName,
        'ID': ID,
        'table': table,
        'newValue':newVal
      };
  jQuery.post(ajaxurl, data, function(response) {
    //jQuery('#updateMSG').text(response);
    //alert(response.message + " ID:" + response.ID);
  });
}