<?php


add_action( 'wp_ajax_createCSVfile', 'createCSVfile' );
add_action( 'admin_post_createCSVfile', 'createCSVfile' );
function build_faire_signs(){
  require_once( TEMPLATEPATH.'/classes/faire_signs.php' );
}

function createCSVfile() {
  //create CSV for individual entries come as a GET request, the mass entry list is a POST request
  $form_id = (isset($_POST['exportForm']) && $_POST['exportForm']!=''?$_POST['exportForm']:'');

  //if the form_id is not set in the post fields, let check the get fields
  if($form_id==''){
    $form_id = (isset($_GET['exForm']) && $_GET['exForm']!=''?$_GET['exForm']:'');
  }
  if($form_id==''){
    die('please select a form');
  }

  $entry_id = (isset($_GET['exEntry']) && $_GET['exEntry']!='' ? $_GET['exEntry']:'');

  //create CSV file
  $form = GFAPI::get_form( $form_id );
  $fieldData = array();

  //put fieldData in a usable array
  foreach($form['fields'] as $field){
    if($field->type!='section' && $field->type!='html' && $field->type!='page')
      $fieldData[$field['id']] = $field;
  }
  $search_criteria['status'] = 'active';
  $entries = array();
  if($entry_id==''){
    $entries = GFAPI::get_entries( $form_id, $search_criteria, null, array('offset' => 0, 'page_size' => 9999) );
  }else{
    //use the submitted entry
    $entries[] = GFAPI::get_entry( $entry_id );
  }

  $output = array('Entry ID','FormID');
  $list = array();
  foreach($fieldData as $field){
    $output[] = $field['label'];
  }
  $list[] = $output;

  foreach($entries as $entry){
    $fieldArray = array($entry['id'],$form_id);
    foreach($fieldData as $field){
      if($field->id==320 || $field->id==321){
        if( in_array( $field->type, array('checkbox', 'select', 'radio') ) ){
          $currency = GFCommon::get_currency();
          $value = RGFormsModel::get_lead_field_value( $entry, $field );
          array_push($fieldArray, GFCommon::get_lead_field_display( $field, $value, $currency, true ));
        }
      }else{
        array_push($fieldArray, (isset($entry[$field->id])?$entry[$field->id]:""));
      }
    }
    $list[] = $fieldArray;
  }

  //write CSV file
  // output headers so that the file is downloaded rather than displayed
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename=form-'.$form_id.($entry_id!=''?'-'.$entry_id:'').'.csv');

  $file = fopen('php://output','w');

  foreach ($list as $line){
    fputcsv($file,$line);
  }

  fclose($file);
  //wp_redirect(  admin_url( 'admin.php?page=mf_export'));
  die();

  exit();
}

function build_pdf_fsp(){
  require_once( TEMPLATEPATH.'/fpdi/FSP.php' );
}

function build_pdf_gsp(){
  require_once( TEMPLATEPATH.'/fpdi/GSP.php' );
 }

//function to create table tags by faire
function genTableTags($faire) {
  global $wpdb;
  //error_log('faire is '.$faire);
  //find the exhibit and sponsor forms by faire
  $sql = "select form_ids from wp_mf_faire where faire='".$faire."'";
  $formIds = $wpdb->get_var($sql);
  //remove any spaces
  $formIds = str_replace(' ', '', $formIds);
  $forms = explode(",", $formIds);
  foreach($forms as $formId){

    $form = GFAPI::get_form($formId);
    if($form['form_type']=='Exhibit' || $form['form_type']=='Sponsor' || $form['form_type']=='Startup Sponsor'){
      $sql = "SELECT wp_rg_lead.id as lead_id, wp_rg_lead_detail.value as lead_status "
          . " FROM `wp_rg_lead`, wp_rg_lead_detail"
          . " where status='active' and field_number=303 and lead_id = wp_rg_lead.id"
          . "   and wp_rg_lead_detail.value!='Rejected' and wp_rg_lead_detail.value!='Cancelled'"
          . "   and wp_rg_lead.form_id=".$formId;
      $results = $wpdb->get_results($sql);

      echo 'Form - '.$formId;
      echo  '('.$wpdb->num_rows . ' entries)';
      echo '<div class="container"><div class="row">';
      foreach($results as $entry){
        $entry_id = $entry->lead_id;

        ?>
        <div class="col-md-2">
          <a class="fairsign" target="_blank" id="<?php echo $entry_id;?>" href="/wp-content/themes/makerfaire/fpdi/tabletag.php?eid=<?php echo $entry_id;?>&faire=<?php echo $faire;?>"><?php echo $entry_id;?></a>
        </div>
        <?php

      }
      echo '</div></div>';

    }
  }
}

add_action( 'gen_table_tags', 'genTableTags', 10, 1 );
