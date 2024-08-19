<?php
add_filter("gform_form_tag", "form_tag", 10, 2);
function form_tag($form_tag, $form){
  // this will be applied to form 127 only - event registration
  if ($form['id'] == 127) {
    // this will redirect to the event registration tab on form submission
    $form_tag = preg_replace("|action='(.*?)'|", "action='#event-registration'", $form_tag);
  }
  return $form_tag;
}
