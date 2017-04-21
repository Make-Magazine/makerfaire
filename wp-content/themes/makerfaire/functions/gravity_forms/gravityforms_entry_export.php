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
            array('Add/Change/Del Ind', array('add','change','del')),
            array('Form ID - Numeric', 'numeric'),
            array('Entry ID - Numeric', 'numeric'),
            array('SubArea ID - Numeric', 'numeric')
          ),
          'fieldCount'  => 8
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
          while (($getData = fgetcsv($file, 10000, ",")) !== FALSE){
            if(count($getData)!= $numCols){
              $error .= 'Incorrect number of columns for the '. ucfirst($fileType) . ' import.<br/>'
                     .  'Uploaded '. count($getData).' columns, expected '.$numCols.' columns.';
              break;
            }

            $passCriteria = true;
            foreach($getData as $key=>$data){
              $data = trim($data);
              if($row!=1){ //skip first row of headers
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
            }
            //if passed criteria, process row
            if($passCriteria){
              $data2Process[] = $getData;
            }
            $row++;
          }

          fclose($file);
          //process data after all rows are imported
          if(!empty($data2Process)){
            $error = 'File Uploaded Succesfully';
            if($fileType=='location'){
              foreach($data2Process as $data){
                $entryID    = $data[2];
                $subArea    = $data[3];
                $location   = $data[4];
                $spaceSize  = $data[5];
                $exposure   = $data[6];
                $note       = $data[7];

                switch ($data[0]) {
                  case 'add':
                    $sql = "INSERT INTO `wp_mf_location` (`entry_id`, `subarea_id`, `location`) VALUES ($entryID,$subArea,$location)";
                    //if Space Size is set, update the Attribute
                    $sql = "insert into wp_rmt_entry_attributes (entry_id, attribute_id, value, user, lockBit) values ($entryID, 2, '$spaceSize', 321, 1)";
                    //if Exposure is set, update the Attribute
                    $sql = "insert into wp_rmt_entry_attributes (entry_id, attribute_id, value, user, lockBit) values ($entryID, 4, '$exposure', 321, 1)";
                    //if Note is set, update the Attention field
                    $sql = "insert into wp_rmt_entry_attn (entry_id, attn_id, comment, user) values ($entryID, 9, '$note', 321)";
                    break;
                  case 'change':
                    $sql = "UPDATE `wp_mf_location` SET `subarea_id`= $subArea, `location=$location where entry_id=$entryID";
                    //if Space Size is set, update the Attribute
                    $sql = "insert into wp_rmt_entry_attributes (entry_id, attribute_id, value, user, lockBit) values ($entryID, 2, '$spaceSize', 321, 1)";
                    //if Exposure is set, update the Attribute
                    $sql = "insert into wp_rmt_entry_attributes (entry_id, attribute_id, value, user, lockBit) values ($entryID, 4, '$exposure', 321, 1)";
                    //if Note is set, update the Attention field
                    $sql = "insert into wp_rmt_entry_attn (entry_id, attn_id, comment, user) values ($entryID, 9, '$note', 321)";
                    break;
                  case 'del':
                    $sql = "DELETE from `wp_mf_location` where entry_id=$entryID";
                    break;
                }
              }
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
        <li>Do not upload more than 15 records at a time. It will time out!</li>
        <li>Row 1: Field Labels</li>
        <li>Row 2: Start of Data</li>
      </ul>
    </div>
    <div id="location" class="tab-pane fade">
      <h3>Location Import Layout</h3>
      <table width="95%">
        <tr><th>Column</th><th>Value(s)</th></tr>
        <tr>
          <td>A</td>
          <td>
            Add    = Add the entry to the area/subarea/location entered.<br/>
            Change = If entry is assigned to a area/subarea, change it to the area/subarea and location entered.<br/>
            Del    = If entry is assigned to a area/subarea, remove it from the area/subarea.
          </td>
          <td>Required</td>
        </tr>
        <tr><td>B</td><td>Form ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>C</td><td>Entry ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>D</td><td>SubArea ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>E</td><td>Location</td><td></td></tr>
        <tr><td>F</td><td>Space Size - updates/adds Space Size Attribute </td><td></td></tr>
        <tr><td>G</td><td>Exposure - updates/adds Exposure Attribute </td><td></td></tr>
        <tr><td>H</td><td>Notes - updates/adds RMT Notes</td><td></td></tr>
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
      <table width="95%">
        <tr><th>Column</th><th>Value(s)</th></tr>
        <tr>
          <td>A</td>
          <td>
            Add    = Add the entry to the area/subarea/location entered.<br/>
            Change = If entry is assigned to a area/subarea, change it to the area/subarea and location entered.<br/>
            Del    = If entry is assigned to a area/subarea, remove it from the area/subarea.
          </td>
          <td>Required</td>
        </tr>
        <tr><td>B</td><td>Form ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>C</td><td>Entry ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>D</td><td>SubArea ID (Numeric Value)</td><td>Required</td></tr>
        <tr><td>E</td><td>Location</td><td></td></tr>
        <tr><td>F</td><td>Space Size - updates/adds Space Size Attribute </td><td></td></tr>
        <tr><td>G</td><td>Exposure - updates/adds Exposure Attribute </td><td></td></tr>
        <tr><td>H</td><td>Notes - updates/adds RMT Notes</td><td></td></tr>
      </table>
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


