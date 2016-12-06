jQuery( document ).ready(function() {
  /* per mf-1616 we are not currently using this logic
  jQuery(".checkbox_makerfaire_plans input").on("click", function() {
    var value = jQuery(this).attr("value");
    if (value==='Selling at Maker Faire [Commercial Maker]' || value==='Promoting a product or service [Commercial Maker]') {
      var checked1=jQuery('input[value="Selling at Maker Faire [Commercial Maker]"]').attr("checked");
      var checked2=jQuery('input[value="Promoting a product or service [Commercial Maker]"]').attr("checked");
      var disablesizes = (checked1 === 'checked' || checked2==='checked');
      if (disablesizes) {
        jQuery('input[value="10\' x 10\'"]').prop("checked",false);
        jQuery('input[value="10\' x 20\'"]').prop("checked",false);
        jQuery('input[value="Other"]').prop("checked",false);

        jQuery('input[value="10\' x 10\'"]').prop("disabled",true);
        jQuery('input[value="10\' x 20\'"]').prop("disabled",true);
        jQuery('input[value="Other"]').prop("disabled",true);
      } else {
        jQuery('input[value="10\' x 10\'"]').prop("disabled",false);
        jQuery('input[value="10\' x 20\'"]').prop("disabled",false);
        jQuery('input[value="Other"]').prop("disabled",false);
      }
    }
  });*/
	jQuery(".presentation_type input").on("click", function() {
		var value = jQuery(this).attr("value");
    if (value=='Standard Presentation (1-2 presenters)' || value=='Panel Presentation (up to 5 participants, with moderator)') {
      var checked1=jQuery('input[value="Standard Presentation (1-2 presenters)"]').attr("checked");
      var disablesizes = (checked1 == 'checked');
      if (disablesizes) {
        jQuery('input[value="45 minutes"]').prop("checked",false);
        jQuery('input[value="45 minutes"]').prop("disabled",true);
      } else {
        jQuery('input[value="45 minutes"]').prop("disabled",false);
      }
    }
  });
});
//this is used to reset HTML5 fields - email, url and telephone if the section they are in is hidden
gform.addAction('gform_post_conditional_logic_field_action', function (formId, action, targetId, defaultValues, isInit) {
  if (!isInit && action == 'hide') {
    var target = jQuery(targetId),
      email_items = target.find('.ginput_container_email'),
      url_items   = target.find('.ginput_container_website'),
      phone_items = target.find('.ginput_container_phone');

    if (email_items.length) {
      target.find('input[type=email]').val('').change();
    }

    if (url_items.length) {
      target.find('input[type=url]').val('').change();
    }

    if (phone_items.length) {
      target.find('input[type=tel]').val('').change();
    }
  }
});

