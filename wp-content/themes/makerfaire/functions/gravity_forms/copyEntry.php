<?php
/*
 * Used to ask the logged in maker if they want to copy entries from previous faires
 */
add_filter( 'gform_pre_render', 'maybe_copyEntry',999 );
function maybe_copyEntry( $form ) {
  if(!isset($_GET['copyEntry'])){
    //check form type
    switch ($form['form_type']){
      case 'Exhibit':
      case 'Presentation':
      case 'Performance':
      case 'Startup Sponsor':
      case 'Sponsor':
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;
        //check for previous entries
        $maker_id = chkPrevEntries($email);
        if($maker_id!=''){
          //show modal offering to copy previous entries
          echo getModalData($maker_id);
        }else{
         // echo 'You do not have previous entries';
        }
        break;
    }
  }else{
    //copy previous entry data
    echo 'Copying data for entry '.$_GET['copyEntry'].'<br/>';

    $entry2Copy = $_GET['copyEntry'];
    $entry = GFAPI::get_entry($entry2Copy);

    foreach($form['fields'] as &$field){
      $fieldID = (string) $field['id'];
      $fieldType = $field['type'];
      switch ($fieldType) {
        case 'textarea':
        case 'text':
        case 'website':
        case 'number':
        case 'phone':
        case 'email':
        case 'select':
          if(isset($entry[$fieldID])) {
            $field['defaultValue'] = $entry[$fieldID];
          }
          break;
        case 'checkbox':
          $fieldChoices = $field['choices'];
          foreach($field['inputs'] as $key => $input){
            $fieldChoiceID = (string) $input['id'];
            //if this field is checked on the entry we need to update the associated array for choices
            if(isset($entry[$fieldChoiceID]) && $entry[$fieldChoiceID]!='') {
              $fieldChoices[$key]['isSelected'] = true;
            }
          }
          $field['choices'] = $fieldChoices;
          break;

        case 'radio':
          if(isset($entry[$fieldID]) && $entry[$fieldID]!=''){
            $fieldChoices = $field['choices'];
            foreach($field['choices'] as $key=>$choice){
              $value = ($choice['value'] != ''? $choice['value']:$choice['text']);

              if((string) $value == (string)$entry[$fieldID]){
                $fieldChoices[$key]['isSelected'] = true;
              }
            }
            $field['choices'] = $fieldChoices;
          }
          break;

        case 'name':
        case 'address':
          $fieldInputs = $field['inputs'];
          foreach($field['inputs'] as $key=>$input){
            $fieldID = (string) $input['id'];

            //if this field is set on the entry we need to update the default value
            if(isset($entry[$fieldID]) && $entry[$fieldID]!='') {
              $fieldInputs[$key]['defaultValue'] = $entry[$fieldID];
            }
          }
          $field['inputs'] = $fieldInputs;
          break;

        case 'list':
        case 'section':
        case 'html':
        case 'page':
        case 'date':
        case 'fileupload':
          //var_dump($field);
          //echo '<br/>';
          //break;
          //do nothing
          break;
        default:
          //echo 'field id '.$fieldID.' type is '.$fieldType.'<br/>';
          break;
      }
    }
  }
  return $form;
}

function chkPrevEntries($email){
  if($email=='')  return '';

  global $wpdb;

  $maker_id = $wpdb->get_var( $wpdb->prepare( "select maker_id from wp_mf_maker where Email like %s", $email));
  if($maker_id!=''){
    return $maker_id;
  }

  return '';
}

function getModalData($maker_id){
  global $current_user;
  if($current_user->user_firstname=='' && $current_user->user_lastname==''){
    $name = $current_user->user_login;
  }else{
    $name = $current_user->user_firstname.' '.$current_user->user_lastname;
  }
  $currentURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  $prevEntries = getPrevEntries($maker_id);

  $return = '
  <!-- Modal -->
  <div id="copyModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Copy Previous Entry</h4>
        </div>
        <div class="modal-body">
          <p>Hello '.$name.'<br/><br/>You have previously filled out a similiar entry for another faire.  Would you like to copy the data into this appliation?</p>';
  foreach ($prevEntries as $entryID=>$prevEntry){
    $return .=  '<div class="row">'
              .   '<div class="col-sm-2">'.$prevEntry['faire'].'</div>'
              .   '<div class="col-sm-2">'.$entryID.'</div>'
              .   '<div class="col-sm-6">'.$prevEntry['title'].'</div>'
              .   '<div class="col-sm-2"><a href="'.$currentURL.'?copyEntry='.$entryID.'">Copy this Entry</a></div>'
              . '</div>';
  }
  $return .= '
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>

    </div>
  </div>
  <script>
    jQuery("#copyModal").modal("show");
  </script>';
  return $return;
}

function getPrevEntries($maker_id) {
  global $wpdb;
  $return = array();

  //TBD should pull by matching form type to current application
  //    but not all faires have form type set in wp_mf_entity as of right now
  $entries =
    $wpdb->get_results(
      $wpdb->prepare(
        "SELECT wp_mf_entity.lead_id, wp_mf_maker_to_entity.maker_type, wp_mf_entity.presentation_title, wp_mf_entity.status, wp_mf_entity.faire "
      . "FROM `wp_mf_maker_to_entity` "
      . "left outer join wp_mf_entity on wp_mf_maker_to_entity.entity_id = wp_mf_entity.lead_id "
      . "WHERE `maker_id` LIKE %s "
      . "AND    wp_mf_entity.status != 'trash' "
      . "AND    presentation_title  != '' "
      . "GROUP BY lead_id order by lead_id DESC", $maker_id)
    );
  foreach($entries as $entry){
    $return[$entry->lead_id] = array('maker_type'=>$entry->maker_type, 'title'=>$entry->presentation_title, 'status'=>$entry->status, 'faire'=>$entry->faire,);
  }
  return $return;
}