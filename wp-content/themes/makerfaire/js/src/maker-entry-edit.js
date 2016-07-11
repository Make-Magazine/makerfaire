/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function() {
  jQuery('.edit').editable('/wp-admin/admin-ajax.php', {
      indicator : 'Saving...',
      cancel    : 'Cancel',
      submit    : 'OK',
      tooltip   : 'Click to edit...',
      submitdata: function () {
          return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val() };
      }
  });
  jQuery('.edit_area').editable('/wp-admin/admin-ajax.php', {
      type      : 'textarea',
      cancel    : 'Cancel',
      submit    : 'OK',
      indicator : 'Saving...',
      tooltip   : 'Click to edit...',
      submitdata: function () {
        return { action: 'MAT-update-entry', 'entry_id': jQuery('#entry_id').val() };
      }
  });

});


