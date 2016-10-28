jQuery(document).ready(function() {
  jQuery('.mfEdit').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry.php', {
      indicator : 'Saving...',
      cancel    : '<button type="button" class="btn btn-default btn-sm editable-cancel"><i class="glyphicon glyphicon-remove"></i></button>',
      submit    : '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="glyphicon glyphicon-ok"></i></button>',
      tooltip   : 'Click to edit...',
      submitdata: function () {
          return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val() };
      }
  });
  jQuery('.mfEdit_area').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry.php', {
      type      : 'textarea',
      cancel    : '<button type="button" class="btn btn-default btn-sm editable-cancel"><i class="glyphicon glyphicon-remove"></i></button>',
      submit    : '<button type="submit" class="btn btn-primary btn-sm editable-submit"><i class="glyphicon glyphicon-ok"></i></button>',
      indicator : 'Saving...',
      tooltip   : 'Click to edit...',
      submitdata: function () {
        return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val()};
      }
  });
  jQuery('.mfEditUpload').editable('/wp-content/themes/makerfaire/MAT/MAT_update_entry_fileupload.php', {
        type      : 'ajaxupload',
        submit    : 'Upload',
        cancel    : 'Cancel',
        tooltip   : "Click to upload...",
        submitdata: function () {
          return { 'entry_id': jQuery('#entry_id').val()};
        }
  });

});


