<?php

/*
 * This action is fired before the detail is displayed on the entry detail page
 */

add_action("gform_entry_detail_content_before", "mf_entry_detail_head", 10, 2);

/*
 *  Funtion to modify the header on the entry detail page
 */
function mf_entry_detail_head($form, $lead) {
  $page_title =
   '<span>'. __( 'Entry #', 'gravityforms' ) . absint( $lead['id'] ).'</span>';
  $page_subtitle =
    '<span class="gf_admin_page_subtitle">'
  . '  <span class="gf_admin_page_formid">ID: '. $form['id'] . '</span>'
  . '  <span class="gf_admin_page_formname">Form Name: '. $form['title'] .'</span>';
  $statuscount=get_makerfaire_status_counts( $form['id'] );
  foreach($statuscount as $statuscount){
    $page_subtitle .= '<span class="gf_admin_page_formname">'.  $statuscount['label'].'('.  $statuscount['entries'].')</span>';
  }
  $page_subtitle .= '</span>';

  //return to entries link
  $outputVar = '';
  if(isset($_GET['filterField'])){
    foreach($_GET['filterField'] as $newValue){
        $outputVar .= '&filterField[]='.$newValue;
    }
  }
  $outputURL = admin_url( 'admin.php' ) . "?page=mf_entries&view=entries&id=".$form['id']  . $outputVar;
  if(isset($_GET['sort']))    $outputURL .= '&sort='.rgget('sort');
  if(isset($_GET['filter']))  $outputURL .= '&filter='.rgget( 'filter' );
  if(isset($_GET['dir']))     $outputURL .= '&dir='.rgget( 'dir' );
  if(isset($_GET['star']))    $outputURL .= '&star='.rgget( 'star' );
  if(isset($_GET['read']))    $outputURL .= '&read='.rgget( 'read' );
  if(isset($_GET['paged']))   $outputURL .= '&paged='.rgget( 'paged' );
  if(isset($_GET['faire']))   $outputURL .= '&faire='.rgget( 'faire' );
  $outputURL = '<a href="'. $outputURL .'">Return to entries list</a>';

  ?>
  <script>
    //remove sections for form switcher and form name editing
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').removeClass("gform_settings_page_title gform_settings_page_title_editable");
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').prop('onclick',null).off('click');
    jQuery('.form_switcher_arrow').remove();
    jQuery('#form_switcher_container').remove();
    //change page title
    jQuery('h2.gf_admin_page_title #gform_settings_page_title').html('<?php echo $page_title;?>');

    //change page subtitle to have status counts
    jQuery('h2.gf_admin_page_title .gf_admin_page_subtitle').html('<?php echo $page_subtitle;?>');

    //add in Return to Entries List link
    jQuery('h2.gf_admin_page_title div.gf_entry_detail_pagination').append('<?php echo $outputURL;?>');

  </script>
  <?php
}

/*
 *        AJAX Section
 * Process sidebar updates via ajax
 * This is where our custom post action handing occurs
 */

add_action( 'wp_ajax_MFupdate-entry', 'mf_admin_MFupdate_entry' );
function mf_admin_MFupdate_entry(){
  //Get the current action
  $mfAction = $_POST['mfAction'];

  //Only process if there was a gravity forms action
  if (!empty($mfAction)){
    $entry_info_entry_id=$_POST['entry_info_entry_id'];
    $lead = GFAPI::get_entry( $entry_info_entry_id );
    $form_id    =  isset($lead['form_id']) ? $lead['form_id'] : 0;
    $form = RGFormsModel::get_form_meta($form_id);
    $entry_status =  isset($lead['303']) ? $lead['303'] : '';

    switch ($mfAction ) {
      // Entry Management Update
      case 'update_entry_management' :
        set_entry_status_content($lead,$form);
        break;
      case 'update_entry_status' :
        set_entry_status($lead,$form);
        break;
      case 'update_ticket_code' :
        $ticket_code = $_POST['entry_ticket_code'];
        $entry_info_entry_id=$_POST['entry_info_entry_id'];
        mf_update_entry_field($entry_info_entry_id,'308',$ticket_code);
        break;
      case 'update_entry_schedule' :
        set_entry_schedule($lead,$form);
        break;
      case 'delete_entry_schedule' :
        delete_entry_schedule($lead,$form);
        break;
      case 'update_entry_location' :
        set_entry_location($lead,$form);
        break;
      case 'delete_entry_location' :
        delete_entry_location($lead,$form);
        break;
      case 'change_form_id' :
        set_form_id($lead,$form);
        break;
      case 'duplicate_entry_id' :
        duplicate_entry_id($lead,$form);
        break;
      case 'send_conf_letter' :
        //first update the schedule if one is set
        set_entry_schedule($lead,$form);
        //then send confirmation letter
        $notifications_to_send = GFCommon::get_notifications_to_send( 'confirmation_letter', $form, $lead );
        foreach ( $notifications_to_send as $notification ) {
          if($notification['isActive']){
            GFCommon::send_notification( $notification, $form, $lead );
          }
        }
        mf_add_note( $entry_info_entry_id, 'Confirmation Letter sent');
        break;
      //Sidebar Note Add
      case 'add_note_sidebar' :
        add_note_sidebar($lead, $form);
        break;
      //Sidebar Note Delete
      case 'delete_note_sidebar' :
        if(is_array($_POST['note'])){
          delete_note_sidebar($_POST['note']);
        }
        break;
    }

    //update the change report with any changes
    GVupdate_changeRpt($form,$entry_info_entry_id,$lead);
    // Return the original form which is required for the filter we're including for our custom processing.
    return $form;

  }
}

