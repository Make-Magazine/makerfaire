/* Edit entry public facing page */
function showEdit(){
    jQuery('#viewEntry').hide();
    jQuery('#editEntry').show();
}

jQuery('#projectGallery img').click(function () {
    jQuery('body').append('<div id="dialog"><img src="' + jQuery(this).attr('src') + '" width="100%" /></div>');
    jQuery('#dialog').dialog({
        dialogClass: "hide-heading",
        modal: true,
        close: function(event, ui) {
            jQuery(this).remove();
        },
        open: function(event, ui) { 
          jQuery('.ui-widget-overlay').bind('click', function(){ 
              jQuery("#dialog").dialog('close');
          }); 
      }
    });
});