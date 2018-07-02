<?php

/* Changes to gravity view for maker admin tool */

//Maker Admin - add new fields in gravity view
add_filter('gravityview_additional_fields','gv_add_faire',10,2);
function gv_add_faire($additional_fields){
  $additional_fields[] = array("label_text" => "Faire",        "desc"          => "Display Faire Name",
                               "field_id"   => "faire_name",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Cancel Entry", "desc"          => "Maker Cancel Entry Link",
                               "field_id"   => "cancel_link",  "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "View Faire Sign", "desc"       => "View Faire Sign",
                               "field_id"   => "maker_sign_link",  "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Copy Entry",   "desc"          => "Maker Copy Entry Link",
                               "field_id"   => "copy_entry",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  $additional_fields[] = array("label_text" => "Maker Delete Entry",   "desc"          => "Maker Delete Entry Link",
                               "field_id"   => "delete_entry",   "label_type"    => "field",
                               "input_type" => "text",         "field_options" => NULL, "settings_html"=> NULL);
  return $additional_fields;
}

//Maker Admin - populate new fields in gravity view
add_filter('gform_entry_field_value','gv_faire_name',10,4);
function gv_faire_name($display_value, $field, $entry, $form){
    global $wpdb;

    $form_id = $entry['form_id'];
    $sql = "select faire,faire_name from wp_mf_faire where FIND_IN_SET ($form_id,wp_mf_faire.form_ids)> 0";
    $faire = $wpdb->get_results($sql);
    $faire_name = (isset($faire[0]->faire_name) ? $faire[0]->faire_name:'');
    $faire_id   = (isset($faire[0]->faire)      ? $faire[0]->faire:'');
    if($field["type"]=='faire_name'){
        $display_value = $faire_name;
    }elseif($field["type"]=='cancel_link'){
        $display_value = '<a href="#cancelEntry" data-toggle="modal" data-projName="'.$entry['151'].'" data-entry-id="'.$entry['id'].'">Cancel</a>';
    }elseif($field["type"]=='copy_entry'){
        $display_value = '<a href="#copy_entry" data-toggle="modal" data-entry-id="'.$entry['id'].'">Copy</a>';
    }elseif($field["type"]=='delete_entry'){
        $display_value = '<a href="#deleteEntry" data-toggle="modal" data-projName="'.$entry['151'].'" data-entry-id="'.$entry['id'].'">Delete</a>';
    }elseif($field["type"]=='maker_sign_link'){
      ///wp-content/themes/makerfaire/fpdi/makersigns.php?eid=$entry['id']&faire=BA16
      $faireVar = ($faire_id!=''? '&faire='.$faire_id:'');
      $display_value = '<a href="/maker-sign/'.$entry['id'].'/'.$faireVar.'" target="_blank">Faire Sign</a>';
    }

    return $display_value;
}

/* Maker Admin - After the user has updated their entry
 * Change the returned success message, including the link
 */
function gv_my_update_message( $message, $view_id, $entry, $back_link ) {

    // view/edit public information link, send user back to
    $findme   = 'edit-public-information/';
    $pos = strpos($back_link, $findme);

    if ($pos === false) {
      //when edit full entry from MFP
      $link = str_replace( 'entry/'.$entry['id'].'/', '', $back_link );
      return 'Entry Updated. <a href="'.esc_url($link).'">Return to your entry list</a>';
    }else{
      //when edit entry from public entry page
      $link = str_replace('edit-public-information','maker',$back_link);
      $return = 'Entry Updated. <a href="'.$link.'/edit">Return to your entry public page</a>';
    }

    return $return;
}
add_filter( 'gravityview/edit_entry/success', 'gv_my_update_message', 10, 4 );

/* Maker Admin -
 * Customise the cancel(back) button link
 */
function gv_my_edit_cancel_link( $back_link, $form, $entry, $view_id ) {

    // view/edit public information link, send user back to
    $findme   = 'edit-public-information/';
    $pos = strpos($back_link, $findme);

    if ($pos === false) {
      //when edit full entry from MFP
      return str_replace( 'entry/'.$entry['id'].'/', '', $back_link );
    }else{
      //when edit entry from public entry page
      $link = str_replace('edit-public-information','maker',$back_link);
      //https://makerfaire.staging.wpengine.com/edit-public-information/entry/58985/?edit=1849218c64&gvid=636924
      return '/maker/entry/'.$entry['id'].'/edit/';
    }

    return $return;
}
add_filter( 'gravityview/edit_entry/cancel_link', 'gv_my_edit_cancel_link', 10, 4 );

/* Used in maker admin, this function is called by ajax to allow a user to cancel
 * an entry and to send a notification */
function makerCancelEntry(){
  $entryID = (isset($_POST['cancel_entry_id']) ? $_POST['cancel_entry_id']:0);
  $reason  = (isset($_POST['cancel_reason'])   ? $_POST['cancel_reason']  :'');
  if($entryID!=0){
    //get entry data and form data
    $lead = GFAPI::get_entry(esc_attr($entryID));
    $form = GFAPI::get_form( $lead['form_id']);

    //Update Status to Cancelled
    mf_update_entry_field($entryID,'303','Cancelled');

    //Make a note of the cancellation
    $cancelText = "The Exhibit has been cancelled by the maker.  Reason given is: ".stripslashes($reason);
    mf_add_note($entryID,$cancelText);

    //update maker and entity table
    GFRMTHELPER::updateMakerTables($entryID); //update maker table information

    //Handle notifications for acceptance
    $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_cancel_exhibit', $form, $lead );
    foreach ( $notifications_to_send as $notification ) {
      if($notification['isActive']){
          GFCommon::send_notification( $notification, $form, $lead );
      }
    }
    echo 'Thank You, Exhibit ID '.$entryID.' has been cancelled';
    gf_do_action( array( 'gform_after_update_entry', $lead['form_id']), $form, $entryID, $lead );
  }else{
    echo 'Error in cancelling this exhibit.';
  }

  exit;
}
add_action( 'wp_ajax_maker-cancel-entry', 'makerCancelEntry' );

/* Used in maker admin, this function is called by ajax to allow a user to delete
 * an entry and to send a notification */
function makerDeleteEntry() {
  $entryID = (isset($_POST['delete_entry_id']) ? $_POST['delete_entry_id']:0);
  if($entryID!=0){
    //get entry data and form data
    $lead = GFAPI::get_entry(esc_attr($entryID));
    $form = GFAPI::get_form( $lead['form_id']);

    $trashed = GFAPI::update_entry_property( $entryID, 'status', 'trash' );
    new GravityView_Cache;

    if( ! $trashed ) {
      echo new WP_Error( 'trash_entry_failed', __('Moving the entry to the trash failed.', 'gravityview' ) );
    }

    //Make a note of the delete
    mf_add_note($entryID,"The Exhibit has been deleted by the maker.");

    //update maker and entity table
    GFRMTHELPER::updateMakerTables($entryID); //update maker table information
    //
    //Handle notifications for acceptance
    $notifications_to_send = GFCommon::get_notifications_to_send( 'maker_delete_exhibit', $form, $lead );
    foreach ( $notifications_to_send as $notification ) {
        if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
        }
    }
    echo 'Thank You, Exhibit ID '.$entryID.' has been deleted';

  }else{
    echo 'Error in deleting this entry.';
  }
  exit;
}
add_action( 'wp_ajax_maker-delete-entry', 'makerDeleteEntry' );

//disable gravity view cache
add_filter('gravityview_use_cache', '__return_false');

/* Used in maker admin this function is called via
 * ajax to copy an entry into a new form
 */
function makeAdminCopyEntry(){
  $entryID    = (isset($_POST['copy_entry_id']) ? $_POST['copy_entry_id']:0);
  $copy2Form  = (isset($_POST['copy2Form'])   ? $_POST['copy2Form']  :'');
  $view_id    = (isset($_POST['view_id'])?$_POST['view_id']:0);

  if($entryID!=0 and $copy2Form != '' && $view_id!=0){
    //get entry data
    $lead = GFAPI::get_entry(esc_attr($entryID));

    //get new form field ID's
    $form = GFAPI::get_form( $copy2Form);

    /*The following fields will not be copied from one entry to another
     * Page 4 review fields:
     * 295 - Are you 18 years or older
     * 114 - Full Name
     * 297 - I am the parent and/or legal guardian of
     * 115 - Date
     * 117 - Release and consent
     * all admin only fields
     */
    $doNotCopy = array(295,114,297,115,117);

    /*loop thru fields in existing entry and if they are in the new form copy them */
    $newEntry = array();
    $newEntry['form_id'] = $copy2Form;
    foreach($form['fields'] as $field){
        //skip doNotCopy fields
        if(!in_array($field['id'], $doNotCopy)){
            //do not copy admin only fields
            $adminOnly = (isset($field['adminOnly']) ? $field['adminOnly'] : FALSE);
            if(!$adminOnly){
                if(is_array($field['inputs'])){
                    foreach($field['inputs'] as $inputs){
                        $fieldID = $inputs['id'];
                        if(isset($lead[$fieldID])){
                            $newEntry[$fieldID] = $lead[$fieldID];
                        }
                    }
                }
                if(isset($lead[$field['id']])){
                    $newEntry[$field['id']] = $lead[$field['id']];
                }
            }
        }
    }
    $newEntry['303'] = 'In Progress'; //in-progress
    $newEntry_id = GFAPI::add_entry( $newEntry );
    $entry = GFAPI::get_entry($newEntry_id);
    $href = GravityView_Edit_Entry::get_edit_link( $entry, $view_id );

    echo 'New Entry created:'.$newEntry_id.'. Please click <a href="entry/'.$newEntry_id.'/'.$href.'">here</a> to finish the submission process';
  }else{
    echo 'Error in creating a new entry. Proper data was not received.';
  }

  exit;
}
add_action( 'wp_ajax_make-admin-copy-entry', 'makeAdminCopyEntry' );


/* Allows a user to delete entries in gravity view. used in maker admin */
add_filter('gravityview/delete-entry/mode','gView_trash_entry');
function gView_trash_entry(){
    return 'trash';
}