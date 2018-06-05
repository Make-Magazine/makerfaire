<?php
/*
 * Used to ask the logged in maker if they want to copy entries from previous faires
 */
add_filter( 'gform_pre_render', 'maybe_copyEntry',999 );
function maybe_copyEntry( $form ) {
  if(!isset($form['form_type'])){
    return $form;
  }
  //only use copy entry modal on page 1
  $current_page = GFFormDisplay::get_current_page( $form['id'] );
  if ( $current_page == 1 ) {
    //if this is gravity view do not use modal copy entry
    if(isset($_GET['view']) && $_GET['view']=='entry'){
      return $form;
    }
    $entry2Copy = (isset($_GET['copyEntry'])?$_GET['copyEntry']:'');
    if($entry2Copy==''){
      //check form type
      switch ($form['form_type']){
        case 'Exhibit':
        case 'Presentation':
        case 'Performance':
        case 'Startup Sponsor':
        case 'Sponsor':
          $current_user = wp_get_current_user();

          //require_once our model
          require_once( get_template_directory().'/models/maker.php' );

          //instantiate the model
          $maker   = new maker($current_user->user_email);

          $tableData = $maker->get_table_data();

          if(!empty($tableData['data'])){
            //show modal offering to copy previous entries
            echo getModalData($tableData);
          }else{
           // echo 'You do not have previous entries';
          }
          break;
      }
    }else{
      if($entry2Copy!='none'){
        //copy previous entry data
        echo 'Copying data from entry '.$_GET['copyEntry'].'<br/>';

        $entry2Copy = (int) $_GET['copyEntry'];
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
              //do nothing
              break;
            default:
              //echo 'field id '.$fieldID.' type is '.$fieldType.'<br/>';
              break;
          }
        }
      }
    }
  }
  return $form;
}

function getModalData($tableData){
  global $current_user;
  if($current_user->user_firstname=='' && $current_user->user_lastname==''){
    $name = $current_user->user_login;
  }else{
    $name = $current_user->user_firstname.' '.$current_user->user_lastname;
  }
  $currentURL = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

  $prevEntries = $tableData['data'];
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
          <p>Hello '.$name.',</p><br/>'
          . '<p>We noticed you\'ve applied before. Would you like to copy data from a previous entry into this application?'
          . '<br/><small><i>If you copy an entry, you will have the chance to make edits and upload new images before submitting your new application.</i></small></p>'
          . '<hr/>'
          . '<div class="pre-scrollable">';
  $return .=  '<div class="row header hidden-xs ">'
              .   '<div class="col-sm-2 col-md-3 ">Faire</div>'
              .   '<div class="col-sm-2">Type</div>'
              .   '<div class="col-sm-1">ID</div>'
              .   '<div class="col-sm-4">Name</div>'
              .   '<div class="col-sm-3 col-md-2"></div>'
              . '</div>';
  foreach ($prevEntries as $prevEntry){
    if($prevEntry['maker_type']=='contact'){ //contact or entry creator
      $return .=  '<div class="row striped">'
              .   '<div class="col-sm-2 col-md-3">'.$prevEntry['faire_name'].'</div>'
              .   '<div class="col-sm-2">'.$prevEntry['form_type'].'</div>'
              .   '<div class="col-sm-1">'.$prevEntry['lead_id'].'</div>'
              .   '<div class="col-sm-4">'.$prevEntry['presentation_title'].'</div>'
              .   '<div class="col-sm-3 col-md-2"><a href="'.$currentURL.'?copyEntry='.$prevEntry['lead_id'].'">Copy this Entry</a></div>'
              . '</div>';
    }
  }

  $return .= '</div>
        </div> <!-- close .modal-body-->
        <div class="modal-footer">
          <button type="button" class="btn btn-default"><a href="'.$currentURL.'?copyEntry=none">Start from Scratch</a></button>
        </div>
      </div>

    </div>
  </div>
  <script>
    jQuery("#copyModal").modal("show");
  </script>';
  return $return;
}
