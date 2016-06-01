<?php
//add jquery for gravity forms
add_filter('gform_register_init_scripts', 'gform_addScript');
function gform_addScript($form) {
  $script = '(function(){' .
      'jQuery("input[type=radio][name=input_1]").change(function(){
          if (jQuery(this).val().indexOf("Standard Presentation") > -1) {
              //disable "45 minutes" option
              jQuery("input[name=\'input_2.3\']").attr("disabled",true);
              //if option is already checked, uncheck it
              jQuery("input[name=\'input_2.3\']").attr("checked",false);
          }else{
              jQuery("input[name=\'input_2.3\']").attr("disabled",false);
          }
      });' .
  '})(jQuery);';

  GFFormDisplay::add_init_script($form['id'], 'formScript', GFFormDisplay::ON_PAGE_RENDER, $script);

  return $form;
}