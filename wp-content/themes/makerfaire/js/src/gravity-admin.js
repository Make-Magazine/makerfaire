//gravity view jQuery
jQuery(document).ready(function() {
  /*
   * Maker Admin - Cancel Entry functionality
   */

  /* Set up the modal for the maker admin cancel entry function */
  jQuery('#cancelEntry').on('show.bs.modal', function(e) {
    //make sure the cancel response is empty
    jQuery(e.currentTarget).find('#cancelResponse').html('');
    jQuery('#cancelText').show();
    //populate the entry id
    var entryId = jQuery(e.relatedTarget).data('entry-id');
    jQuery(e.currentTarget).find('span[name="entryID"]').html(entryId);

    //populate the project Name        
    var projName = jQuery(e.relatedTarget).data('projname');
    jQuery("#projName").html(projName);

  });

  jQuery('#submitCancel').click(function() {
    //disable the submit button
    jQuery('#submitCancel').hide();
    //submit the cancellation via ajax                            
    var entry_id = jQuery("#cancelEntryID").html();
    var cancel_reason = jQuery('textarea[name="cancelReason"]').val();
    var data = {
      'action': 'maker-cancel-entry',
      'cancel_entry_id': entry_id,
      'cancel_reason': cancel_reason
    };
    jQuery('#cancelText').hide();
    jQuery.post(object_name.ajaxurl, data, function(response) {
      jQuery('.modal-title').hide();
      jQuery('#cancelResponse').text(response);
    });
  });

  //modal close - refresh page  
  jQuery('#cancelEntry').on('hidden.bs.modal', function(e) {
    location.reload();
  });

  /*
   * Maker Admin - Copy Entry to new form functionality
   */
  /* Set up the modal for the maker admin copy entry function  */
  jQuery('#copy_entry').on('show.bs.modal', function(e) {
    //make sure the cancel response is empty
    jQuery(e.currentTarget).find('#copyResponse').html('');

    //populate the entry id
    var entryId = jQuery(e.relatedTarget).data('entry-id');
    jQuery(e.currentTarget).find('span[name="entryID"]').html(entryId);

    //enable the submit button
    jQuery('#submitCopy').prop('disabled', false);
  });

  jQuery('#submitCopy').click(function() {
    //disable the submit button
    jQuery('#submitCopy').prop('disabled', true);

    //submit the request via ajax                            
    var entry_id = jQuery("#copyEntryID").html();
    var copy2Form = jQuery('#copy2Form').val();
    var gvViewId = jQuery('.gravityview-view-id').val();

    var data = {
      'action': 'make-admin-copy-entry',
      'copy_entry_id': entry_id,
      'copy2Form': copy2Form,
      'view_id': gvViewId
    };
    jQuery.post(object_name.ajaxurl, data, function(response) {
      jQuery('#copyResponse').html(response);
    });
  });

  /*
   * Maker Admin - Delete Entry functionality
   */

  /* Set up the modal for the maker admin delete entry function */
  jQuery('#deleteEntry').on('show.bs.modal', function(e) {
    //make sure the delete response is empty
    jQuery(e.currentTarget).find('#deleteResponse').html('');
    jQuery('#deleteText').show();
    //show the submit button
    jQuery('#submitDelete').show();
    //hide the close button        
    jQuery("#closeDelete").hide();
    //show the cancel button
    jQuery("#cancelDelete").show();

    //populate the entry id
    var entryId = jQuery(e.relatedTarget).data('entry-id');
    //jQuery(e.currentTarget).find('span[name="deleteEntryID"]').html(entryId);
    jQuery("#deleteEntryID").html(entryId)

    //populate the project Name        
    var projName = jQuery(e.relatedTarget).data('projname');
    jQuery("#delProjName").html(projName);

  });

  jQuery('#submitDelete').click(function() {
    //hide the submit button
    jQuery('#submitDelete').hide();
    //show the close button        
    jQuery("#closeDelete").show();
    //hide the cancel button
    jQuery("#cancelDelete").hide();

    //submit the cancellation via ajax                            
    var entry_id = jQuery("#deleteEntryID").html();
    var data = {
      'action': 'maker-delete-entry',
      'delete_entry_id': entry_id
    };
    jQuery('#deleteText').hide();
    jQuery.post(object_name.ajaxurl, data, function(response) {
      jQuery('.modal-title').hide();
      jQuery('#deleteResponse').text(response);
    });
  });

  //modal close - refresh page  
  jQuery('#closeDelete').click(function() {
    location.reload();
  });
});
