<?php
/* Displays faire sign code */
function build_faire_signs(){
  require_once( TEMPLATEPATH.'/classes/faire_signs.php' );
}

/*This is for the Export all Fields button in the Entry Summary */
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
add_action( 'wp_ajax_createCSVfile', 'createCSVfile' );
add_action( 'admin_post_createCSVfile', 'createCSVfile' );

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

//create zip files of maker signs
function createSignZip(){
  global $wpdb;
  $response = array();
  $statusFilter = (isset($_POST['selstatus'])?$_POST['selstatus']:'');
  $type         = (isset($_POST['seltype'])?$_POST['seltype']:'');
  $faire        = (isset($_POST['faire'])?$_POST['faire']:0);
  $signType     = (isset($_POST['type'])?$_POST['type']:'signs');

  if($signType!='presenter'){
    //create array of subareas
    $sql = "SELECT wp_rg_lead.ID as entry_id, wp_rg_lead.form_id,
          (select value from wp_rg_lead_detail where field_number=303 and wp_rg_lead_detail.lead_id = wp_rg_lead.ID) as entry_status,
          wp_mf_faire_subarea.area_id, wp_mf_faire_area.area, wp_mf_location.subarea_id, wp_mf_faire_subarea.subarea,wp_mf_location.location
          FROM wp_mf_faire, wp_rg_lead
          left outer join wp_mf_location on wp_rg_lead.ID  = wp_mf_location.entry_id
          left outer join wp_mf_faire_subarea on wp_mf_location.subarea_id  = wp_mf_faire_subarea.id
          left outer join wp_mf_faire_area    on wp_mf_faire_subarea.area_id  = wp_mf_faire_area.id
          where faire = '$faire'
          and wp_rg_lead.status  != 'trash'
          and FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0
          and FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.non_public_forms)<= 0";
    $results = $wpdb->get_results($sql);
    $entries = array();

    foreach($results as $row){
      //exclude records based on status filter
      if($statusFilter =='accepted'   && $row->entry_status!='Accepted')  continue;
      if($statusFilter =='accAndProp' && ($row->entry_status!='Accepted' && $row->entry_status!='Proposed')){
        continue;
      }
      $area    = ($row->area    != NULL ? $row->area:'No-Area');
      $subarea = ($row->subarea != NULL ? $row->subarea:'No-subArea');

      //create friendly names for file creation
      $area = str_replace(' ','_',$area);
      $subarea = str_replace(' ','_',$subarea);
      //build array output based on selected type
      if($type=='area') {
        $entries[$area][$row->entry_status][] = $row->entry_id;
      }
      if($type=='subarea') {
        $entries[$area.'-'.$subarea][$row->entry_status][] = $row->entry_id;
      }
      if($type=='faire') {
        $entries['faire'][$row->entry_status][] = $row->entry_id;
      }
    } //end looping thru sql results
  }else{
    $entries = array();
    $select_query = "SELECT entity.lead_id as entry_id
                      FROM    wp_mf_schedule schedule,
                              wp_mf_entity entity

                      where   schedule.entry_id       = entity.lead_id
                              AND entity.status       = 'Accepted'
                              and schedule.faire      = '" . $faire . "' " .
            " group BY   entity.lead_id";
    $results = $wpdb->get_results($select_query);
    $entries = array();

    foreach($results as $row){
      $entries['faire']['Accepted'][] = $row->entry_id;
    }
  }
  $error = '';

  //build zip files based on selected type
  foreach($entries as $typeKey=>$entType){
     //create zip file
    $zip = new ZipArchive();

    $filepath = get_template_directory()."/signs/".$faire.'/'.$signType.'/';
    if (!file_exists($filepath.'zip')) {
      mkdir($filepath.'zip', 0777, true);
    }
    $filename = $faire."-".$typeKey."-faire".$signType.".zip";

    $zip->open($filepath.'zip/'.$filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach($entType as $statusKey=>$status){
      $subPath = $typeKey.'/'.$statusKey.'/';
      foreach($status as $entryID) {
        //write zip file
        $file = $entryID.'.pdf';
        if (file_exists($filepath.$file)) {
          $zip->addFile($filepath.$file,$file);
        }else{
          $error .= 'Missing PDF for ' .$entryID.'<br/>';
        }
      }
    }
    //close zip file
    if (!$zip->status == ZIPARCHIVE::ER_OK)
      echo "Failed to write files to zip\n";
    $zip->close();
  } //end looping thru entry array


  exit;
}
add_action('wp_ajax_createSignZip', 'createSignZip');

function createEntList() {
  global $wpdb;
  $faire = (isset($_POST['faire'])?$_POST['faire']:'');
  $type  = (isset($_POST['type'])?$_POST['type']:'');
  $entList = '';

  $entList = 'Please wait while the PDFs are generated below<br/><br/>';
  if($type!='presenter'){
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

        $entList.= 'Form - '.$formId;
        $entList.=  '('.$wpdb->num_rows . ' entries)';
        $entList.= '<div class="row">';
        foreach($results as $entry){
          $entry_id = $entry->lead_id;
          $entList.= '<div class="col-sm-2 col-lg-1">';
          if($type=='signs'){
            $entList.= '  <a class="fairsign" target="_blank" id="'. $entry_id.'" href="'.($type=='signs'?'/maker-sign/':''). $entry_id.'/'. $faire.'">'. $entry_id.'</a>';
          }else{
            $entList.= '  <a class="fairsign" target="_blank" id="'. $entry_id.'" href="/wp-content/themes/makerfaire/fpdi/tabletag.php?eid='.$entry_id.'&faire='. $faire.'">'. $entry_id.'</a>';
          }
          $entList.= '</div>';
        }
        $entList.='</div>';
      }
    }
  }else{
    $select_query = "SELECT entity.lead_id as entry_id
                      FROM    wp_mf_schedule schedule,
                              wp_mf_entity entity

                      where   schedule.entry_id       = entity.lead_id
                              AND entity.status       = 'Accepted'
                              and schedule.faire      = '" . $faire . "' " .
            " group BY   entity.lead_id";
    $entList .= '<div class="row">';
    $results = $wpdb->get_results($select_query);
    foreach($results as $entry){
      $entry_id = $entry->entry_id;
      $entList .= '<div class="col-sm-2 col-lg-1">';
      $entList .= '  <a class="fairsign" target="_blank" id="'. $entry_id.'" href="/wp-content/themes/makerfaire/fpdi/presenterSigns.php?eid='. $entry_id.'&faire='. $faire.'">'. $entry_id.'</a>';
      $entList .= '</div>';
    }
    $entList .= '</div>';
  }
  $response['entList'] = $entList;
  wp_send_json($response);
  exit;
}
add_action('wp_ajax_createEntList', 'createEntList');
