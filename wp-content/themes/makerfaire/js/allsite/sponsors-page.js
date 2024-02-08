//sponsors page
jQuery(document).ready(function() {
  jQuery("[rel='tooltip']").tooltip();

  jQuery('.thumbnail').hover(
    function() {
      jQuery(this).find('.caption').fadeIn(250); //
    },
    function() {
      jQuery(this).find('.caption').fadeOut(205); //
    }
  );

  //entry page video modal       
  jQuery('#entryModal').on('hidden.bs.modal', function() {
    jQuery('#entryModal iframe').removeAttr('src');
  });
  jQuery('#modalButton').click(function() {
    var src = jQuery('#entryVideo').val();
    jQuery('#entryModal iframe').attr('src', src);
  });
});
