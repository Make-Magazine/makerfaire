jQuery(document).ready(function() {
  jQuery('.edit').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry.php', {
      indicator : 'Saving...',
      cancel    : 'Cancel',
      submit    : 'OK',
      tooltip   : 'Click to edit...',
      submitdata: function () {
          return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val() };
      }
  });
  jQuery('.edit_area').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry.php', {
      type      : 'textarea',
      cancel    : 'Cancel',
      submit    : 'OK',
      indicator : 'Saving...',
      tooltip   : 'Click to edit...',
      submitdata: function () {
        return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val()};
      }
  });
  jQuery('.ajaxupload').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry_fileupload.php', {
        indicator : 'Saving...',
        type      : 'ajaxupload',
        submit    : 'Upload',
        cancel    : 'Cancel',
        tooltip   : "Click to upload..."
  });

});


