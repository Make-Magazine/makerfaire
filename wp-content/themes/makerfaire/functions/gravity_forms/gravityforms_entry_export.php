<?php
// custom MF entries export
add_filter( 'gform_export_menu', 'my_custom_export_menu_item' );
function my_custom_export_menu_item( $menu_items ) {

  $menu_items[] = array(
    'name' => 'mf_custom_export_entries',
    'label' => __( 'MF Export Entries' )
  );

  $menu_items[] = array(
    'name' => 'mf_custom_import_entries',
    'label' => __( 'MF Import Entries' )
  );

  return $menu_items;
}

add_action( 'gform_export_page_mf_custom_import_entries', 'mf_custom_import_entries' );
function mf_custom_import_entries() {
  if ( ! GFCommon::current_user_can_any( 'gravityforms_edit_forms' ) ) {
    wp_die( 'You do not have permission to access this page' );
  }

  GFExport::page_header('MF Custom Import');

  //import data
  $error  = '';
  $fieldAray = array(
      'location' => array(
          'fields' => array(
            array('Entry ID - Numeric', 'numeric'),
            array('SubArea ID - Numeric', 'numeric'),
            array('Location', ''),
            array('Final Space Size',''),
            array('Exposure', ''),
            array('Conf L Notes',''),
            array('Form ID - Numeric', 'numeric')
          ),
          'fieldCount'  => 7
          ),
      'entry'    => array()
  );

  if(isset($_POST['import_mf_data'])){
    ini_set("auto_detect_line_endings", "1");
    check_admin_referer( 'gf_import_forms', 'gf_import_forms_nonce' );
    if ( ! empty( $_FILES['fileToUpload']['tmp_name'] ) ) {
      //check for any errors
      if ($_FILES["fileToUpload"]["error"] > 0) {
        $error =  "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";
      } else {
        //check if CSV file
        if($_FILES["fileToUpload"]["size"] > 0) {
          $fileType     = $_POST['importType'];
          $importFields = $fieldAray[$fileType]['fields'];
          $numCols      = $fieldAray[$fileType]['fieldCount'];
          $filename     = $_FILES["fileToUpload"]["tmp_name"];
          $file         = fopen($filename, "r");
          $row          = 1;
          $data2Process = array();
          while (($csvFile = fgetcsv($file, 10000, ",")) !== FALSE){
            if(count($csvFile)!= $numCols){
              $error .= 'Incorrect number of columns for the '. ucfirst($fileType) . ' import.<br/>'
                     .  'Uploaded '. count($csvFile).' columns, expected '.$numCols.' columns.';
              break;
            }
            if($row!=1){  //skip header rows
              $passCriteria = true;
              //row 1 contains field names
              foreach($csvFile as $key=>$data){
                $data = trim($data);
                //check for required data
                if(isset($importFields[$key])){
                  if(is_array($importFields[$key][1])){
                    //check if value is in data array
                    if(!in_array(strtolower($data),$importFields[$key][1])){
                      $error .= 'Error on row '.$row.'. Data in column "'.$importFields[$key][0].'" is invalid. Row skipped.<br/>';
                      $passCriteria = false;
                    }
                  }elseif($importFields[$key][1]=='numeric'){
                    //check if value is numeric
                    if(!is_numeric($data)){
                      $error .= 'Error on row '.$row.'. Data in column "'.$importFields[$key][0].'" is not numeric. Row skipped.<br/>';
                      $passCriteria = false;
                    }
                  }
                }
              }
              //if passed criteria, process row
              if($passCriteria){
                $data2Process[] = $csvFile;
              }
            }
            $row++;
          }

          fclose($file);
          //process data after all rows are imported
          if(!empty($data2Process)){
            global $wpdb;
            $error = 'File Uploaded Succesfully';
            if($fileType=='location'){
              $chgRPTins = array();
              foreach($data2Process as $data){;
                $entryID    = $data[0];
                $subArea    = $data[1];
                $location   = $data[2];
                $spaceSize  = $data[3];
                $exposure   = $data[4];
                $note       = $data[5];
                $formID     = $data[6];

                /*  SubArea/Location  */
                //delete any previously assigned subareas/locations
                $wpdb->query("delete from wp_mf_location where entry_id = $entryID");

                //now add in the uploaded  subareas/locations
                $wpdb->insert('wp_mf_location',array('entry_id'=>$entryID,'subarea_id'=>$subArea,'location'=>$location),array('%d','%d','%s'));

                $user = 321;
                //update attributes
                $return = updateAttribute($entryID, 2, $spaceSize,$user,$formID);
                if(!empty($return)) $chgRPTins[] = $return;
                $return = updateAttribute($entryID, 4, $exposure,$user,$formID);
                if(!empty($return)) $chgRPTins[] = $return;
                //if $space size is empty we need to set the resource status
                if($spaceSize==''){
                  gform_update_meta( $entryID, 'res_status', 'review',$formID);
                }

                /*    Confirmation comments     */
                //first clear out any confirmation comments, then add from upload

                $res = $wpdb->get_row("select * from wp_rmt_entry_attn where entry_id = $entryID and attn_id = 13");
                if ( null !== $res ) { //update conf comment
                  $wpdb->update(
                      'wp_rmt_entry_attn',
                      array('entry_id'=>$entryID,'attn_id'=>13,'comment'=>$note,'user'=>$user),
                      array( 'ID' => $res->ID ),
                      array('%d','%d', '%s','%d')
                    );
                  //update change report if the conf comment changed
                  if($res->comment != $note)
                    $chgRPTins[] = array(
                                  'user_id'           => $user,
                                  'lead_id'           => $entryID,
                                  'form_id'           => $formID,
                                  'field_id'          => 13,
                                  'field_before'      => $res->comment,
                                  'field_after'       => $note,
                                  'fieldLabel'        => 'RMT Attention: Confirmation Comment',
                                  'status_at_update'  => '');
                }else{ ///insert
                  $wpdb->insert('wp_rmt_entry_attn',array('entry_id'=>$entryID,'attn_id'=>13,'comment'=>$note,'user'=>$user),array('%d','%d', '%s','%d'));
                  $chgRPTins[] = array(
                                  'user_id'           => $user,
                                  'lead_id'           => $entryID,
                                  'form_id'           => $formID,
                                  'field_id'          => 13,
                                  'field_before'      => '',
                                  'field_after'       => $note,
                                  'fieldLabel'        => 'RMT Attention: Confirmation Comment',
                                  'status_at_update'  => '');
                }

              } //end foreach

              if(!empty($chgRPTins))
                updateChangeRPT($chgRPTins);
            }
          }else{
            $error .= 'No Data to process<br/>';
          }
        }else{
          $error = "File size was 0.";
        }
      }
		}
  }

  ?>

  <h3>MakerFaire Entry Import</h3>

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#import">File Import</a></li>
    <li><a data-toggle="tab" href="#home">Basic Info</a></li>
    <li><a data-toggle="tab" href="#location">Location Layout</a></li>
    <li><a data-toggle="tab" href="#entry">Entry Layout</a></li>
  </ul>

  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
      <?php echo $error;?>
      <form method="post" enctype="multipart/form-data" style="margin-top:10px;">
        <?php wp_nonce_field( 'gf_import_forms', 'gf_import_forms_nonce' ); ?>
        <table class="padd" width="80%">
          <tr>
            <td><label>Type of Import:</label></td>
            <td>
              <input type="radio" name="importType" value="location" checked> Location Import<br>
              <input type="radio" name="importType" value="entry"> Entry Import
            </td>
          </tr>
          <tr>
            <td><label>File to upload:</label><br/><i>Ensure this matches file layout</i></td>
            <td>
              <input type="file" name="fileToUpload" id="fileToUpload">
              <input type="submit" value="<?php esc_html_e( 'Import', 'gravityforms' ) ?>" name="import_mf_data" class="button button-large button-primary" />
            </td>
          </tr>
        </table>
      </form>
    </div>
    <div id="home" class="tab-pane fade">
      <ul class="bullet">
        <li>File format - CSV </li>
        <li>Row 1: Field Labels</li>
        <li>Row 2: Start of Data</li>
      </ul>
    </div>
    <div id="location" class="tab-pane fade">
      <h3>Location Import Layout</h3>
      <table width="95%">
        <tr><th>Column</th><th>Value(s)</th></tr>
        <tr><td>A</td><td>Entry ID(Numeric)</td><td>Required</td></tr>
        <tr><td>B</td><td>SubArea ID(Numeric)</td><td>Required</td></tr>
        <tr><td>C</td><td>Location</td><td></td></tr>
        <tr><td>D</td><td>Space Size - updates/adds Space Size Attribute<br/><i>(if blank will set review status to not ready)</i></td></tr>
        <tr><td>E</td><td>Exposure - updates/adds Exposure Attribute </td><td></td></tr>
        <tr><td>F</td><td>Confirmation Comment - updates/adds RMT Notes</td><td></td></tr>
        <tr><td>G</td><td>Form ID(Numeric)</td><td>Required</td></tr>
      </table>
    </div>
    <div id="entry" class="tab-pane fade">
      <h3>Entry Import Layout</h3>
      <ul>
        <li>Row 1: Field ID's</li>
        <li>Row 2: Field Names</li>
        <li>Row 3: Start of Data</li>
        <li>Column A: Faire ID</li>
        <li>Column B: Form ID</li>
        <li>Column C-?? fields</li>
      </ul>
    </div>
  </div>

  <?php
  GFExport::page_footer();
}

// display content for custom menu item when selected
add_action( 'gform_export_page_mf_custom_export_entries', 'mf_custom_export_entries' );
function mf_custom_export_entries() {

  GFExport::page_header('MF Custom Export');

  ?>
  <div class="dropdown" style="position:absolute">
    <button class="btn btn-default dropdown-toggle" type="button" id="mfexportdata" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
      Select Form
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="mfexportdata">
      <?php
      //create a crypt key to pass to entriesExport.php to avoid outside from accessing
      $date  = date('mdY');
      $crypt = crypt($date, AUTH_SALT);
      $forms = RGFormsModel::get_forms( null, 'title' );
      foreach ( $forms as $form ) { ?>
        <li><a href="/wp-content/themes/makerfaire/devScripts/entriesExport.php?formID=<?php echo absint( $form->id ).'&auth='.$crypt; ?>"><?php echo esc_html( $form->title ); ?></a></li>
        <?php
      }
      ?>
    </ul>
  </div>


  <?php
  GFExport::page_footer();

}

//function to update attributes for mf import
function updateAttribute($entryID, $attribute_id,$attvalue,$user,$formID){
  global $wpdb;
  $chgRPTins = '';
  $res = $wpdb->get_row("select wp_rmt_entry_attributes.*, wp_rmt_entry_att_categories.token"
                      . " from wp_rmt_entry_attributes left outer join wp_rmt_entry_att_categories on wp_rmt_entry_att_categories.ID=attribute_id"
                      . ' where entry_id = '.$entryID.' and attribute_id = '.$attribute_id);
   //matching record found
  if ( null !== $res ) {  //update the attribute
    if($res->value!=$attvalue){
      $wpdb->update('wp_rmt_entry_attributes',array('value'=>$attvalue,'user'=>$user,'update_stamp'=>'now()'),array('ID'=>$res->ID),array('%s','%d','%s'));
      if($wpdb->last_error !== '') :
        $wpdb->print_error();
      endif;
      //update change report
      $chgRPTins = array(
          'user_id'           => $user,
          'lead_id'           => $entryID,
          'form_id'           => $formID,
          'field_id'          => $attribute_id,
          'field_before'      => $res->value,
          'field_after'       => $attvalue,
          'fieldLabel'        => 'RMT attribute: '.$res->token.' -  value',
          'status_at_update'  => '');
    }
  }else{ //add the attribute
    $wpdb->insert('wp_rmt_entry_attributes',array('entry_id'=>$entryID,'attribute_id'=>$attribute_id,'value'=>$attvalue,'user'=>$user),array('%d','%d', '%s','%d'));
    if($wpdb->last_error !== '') :
      $wpdb->print_error();
    endif;
    //update change report
    $res = $wpdb->get_row('SELECT token FROM `wp_rmt_entry_att_categories` where ID='.$attribute_id);
    $chgRPTins = array(
          'user_id'           => $user,
          'lead_id'           => $entryID,
          'form_id'           => $formID,
          'field_id'          => $attribute_id,
          'field_before'      => '',
          'field_after'       => $attvalue,
          'fieldLabel'        => 'RMT attribute: '.$res->token.' -  value',
          'status_at_update'  => '');
  }

  return $chgRPTins;
}

