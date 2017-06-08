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

  if(isset($_POST['import_mf_data'])){
    ini_set("auto_detect_line_endings", "1");
    check_admin_referer( 'gf_import_forms', 'gf_import_forms_nonce' );
    if ( ! empty( $_FILES['fileToUpload']['tmp_name'] ) ) {
      //check for any errors
      if ($_FILES["fileToUpload"]["error"] > 0) {
        $error =  "Return Code: " . $_FILES["fileToUpload"]["error"] . "<br />";
      } else {
        //get user info
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        //check if CSV file
        $info = pathinfo($_FILES['fileToUpload']['name']);

        if($info['extension'] != 'csv'){
          $error =  "Uploaded file is not a csv file.  Please check your file adn try again.<br />";
        }elseif($_FILES["fileToUpload"]["size"] > 0) {
          $fileType     = $_POST['importType'];
          $filename     = $_FILES["fileToUpload"]["tmp_name"];
          $file         = fopen($filename, "r");
          $row          = 1;
          $data2Process = array();

          if($fileType=='location') {
            $error = processLocation($file,$userID);
          }else{
            $error = processEntry($file,$userID);
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
    <!--<li><a data-toggle="tab" href="#home">Basic Info</a></li>-->
    <li><a data-toggle="tab" href="#location">Location Layout</a></li>
    <li><a data-toggle="tab" href="#entry">Entry Layout</a></li>
  </ul>

  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
      <span class="required"><?php echo $error;?></span>
      <br/>
      <b>** Note: Ensure your uploaded file matches the layout in the tabs.  Your file will be rejected if it does not **</b>
      <hr>
      <form method="post" enctype="multipart/form-data" style="margin-top:10px;">
        <?php wp_nonce_field( 'gf_import_forms', 'gf_import_forms_nonce' ); ?>
        <table class="padd" width="80%">
          <tr>
            <td>Type of Import:</td>
            <td>
              <input type="radio" name="importType" value="location" checked> Location Import &nbsp;<a href="/wp-content/themes/makerfaire/devScripts/location_import.csv" download>Example</a><br>
              <input type="radio" name="importType" value="entry"> Entry Import &nbsp;<a href="/wp-content/themes/makerfaire/devScripts/entry_upload_example.csv" download>Example</a>
            </td>
          </tr>
          <tr>
            <td>File to upload:<br/><small><i>Must be CSV file</i></small></td>
            <td>
              <input type="file" name="fileToUpload" id="fileToUpload">
              <input type="submit" value="<?php esc_html_e( 'Import', 'gravityforms' ) ?>" name="import_mf_data" class="button button-large button-primary" />
            </td>
          </tr>
        </table>
      </form>
    </div>

    <div id="location" class="tab-pane fade">
      <h3>Location Import Layout</h3>

      <table class="border" width="100%">
        <tr>
          <th>entry_id</th>
          <th>subarea_id</th>
          <th>location</th>
          <th>final_space_size</th>
          <th>exposure</th>
          <th>conf_l_notes</th>
          <th>form_id</th>
        </tr>
        <tr>
          <td>Required<br/>Numeric</td>
          <td>Required<br/>Numeric</td>
          <td></td>
          <td>Space Size Attribute<br/>updates/adds<br/><i>(if blank will set review status to not ready)</i></td>
          <td>Exposure Attribute<br/>updates/adds</td>
          <td>Confirmation Comment<br/>updates/adds</td>
          <td>Required<br/>Numeric</td>
        </tr>
      </table>
    </div>

    <div id="entry" class="tab-pane fade">
      <h3>Entry Import Layout</h3>
      <ul class="bullet">
        <li>CSV format</li>
        <li>Row 1 - Field Names/Field ID's (Field Names must match what is in example and field ID's must be numeric</li>
        <li>Row 2 - Start of Data</li>
        <li>Required Fields
          <ul>
            <li>form_id</li>
            <li>303 - Status to set the entry to</li>
            <li>320 - Primary Category</li>
            <li>151 - Project Name</li>
            <li>16 - Public Description (limit to 250 characters</li>
            <li>22 - Photo URL</li>
            <li>105 - Who would you like listed as the maker of the project? (One Maker)</li>
            <li>160.3 - Maker First Name</li>
            <li>160.6 - Maker First Name</li>
            <li>234 - Maker Bio</li>
            <li>217 - Maker Photo</li>
            <li>96.3 - Contact First Name</li>
            <li>96.6 - Contact First Name</li>
            <li>98 - Contact Email</li>
            <li>101.3 - Contact City</li>
            <li>101.4 - Contact State</li>
            <li>101.5 - Contact Zip</li>
            <li>101.6 - Contact Country</li>
          </ul>
        </li>
        <li>Optional Fields
          <ul>
            <li>link_entry_id - used to link the created entry to</li>
            <li>subarea_id - used to assign this entry to a specific subarea</li>
            <li>location - used to assign entry to a specific location</li>
            <li>eid - used to create entry passes</li>
            <li>ticket_code - used to create entry passes</li>
            <li>num_tickets - used to create entry passes</li>
            <li>visible - used to create entry passes</li>
            <li>304 - Used to set Disbale Autorsesponder or other flags</li>
            <li>27 - Project Website</li>
            <li>Any other fields from the form you are importing into</li>
          </ul>
        </li>
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
        $updateStamp=date( "Y-m-d h:i:s", time() ); //TBD correct time this sets the time 6 hours ahead
        $wpdb->update('wp_rmt_entry_attributes',array('value'=>$attvalue,'user'=>$user,'update_stamp'=>$updateStamp,'lockBit'=>1),array('ID'=>$res->ID),array('%s','%d','%s','%s'));
        if($wpdb->last_error !== ''){
          $wpdb->print_error();
        }
      if($res->value!=$attvalue){
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
      $wpdb->insert('wp_rmt_entry_attributes',array('entry_id'=>$entryID,'attribute_id'=>$attribute_id,'value'=>$attvalue,'user'=>$user,'lockBit'=>1),array('%d','%d', '%s','%d'));
      if($wpdb->last_error !== ''){
        $wpdb->print_error();
      }
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

  function processLocation($file,$user) {
    global $numCols;
    $numCols = 7;
    $error = '';
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
          )
      );
    $row = 1;
    //process the imported file
    while (($csvFile = fgetcsv($file, 10000, ",")) !== FALSE){
      if(count($csvFile)!= $numCols){
        $error .= 'Incorrect number of columns for the Location import.<br/>Uploaded '. count($csvFile).' columns, expected '.$numCols.' columns.';
        break;
      }

      if($row!=1){ //skip the header row
        $passCriteria = true;
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
        if($passCriteria){
          $data2Process[] = $csvFile;
        }

      }
      $row++;
    }

    fclose($file); //close the imported file, we are done with it

    //process data after all rows are imported
    if(!empty($data2Process)){
      global $wpdb;
      $error = 'File Uploaded Succesfully';
      $chgRPTins = array();
      foreach($data2Process as $data){
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

        //update attributes
        $return = updateAttribute($entryID, 2, $spaceSize,$user,$formID);
        if(!empty($return)) $chgRPTins[] = $return;
        $return = updateAttribute($entryID, 4, $exposure,$user,$formID);
        if(!empty($return)) $chgRPTins[] = $return;
        //if $space size is empty we need to set the resource status
        if($spaceSize==''){
          gform_update_meta( $entryID, 'res_status', 'review', $formID);
        }

        //ensure 19 and 20 (original space size and exposure are locked)
        $wpdb->update('wp_rmt_entry_attributes',array('lockBit'=>1),array('attribute_id'=>19,'entry_id'=>$entryID),array('%d'),array('%d','%d'));
        $wpdb->update('wp_rmt_entry_attributes',array('lockBit'=>1),array('attribute_id'=>20,'entry_id'=>$entryID),array('%d'),array('%d','%d'));
        /*    Confirmation comments     */
        //first clear out any confirmation comments, then add from upload

        $res = $wpdb->get_row("select * from wp_rmt_entry_attn where entry_id = $entryID and attn_id = 13");
        if ( null !== $res ) { //update conf comment
          $wpdb->update('wp_rmt_entry_attn',array('entry_id'=>$entryID,'attn_id'=>13,'comment'=>$note,'user'=>$user),array('ID'=> $res->ID),array('%d','%d', '%s','%d'));

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

    }else{
      if($error=='') $error .= 'No Data to process<br/>';
    }
    return $error;
  }

  /* Process the impored entry upload file */
  function processEntry($file, $user) {
    $data2Process = array();
    $error  = '';

    $fieldArray = array(
              'form_id'       =>  array('required' => 'yes', 'verification' => 'numeric'),
              'entry_creator' =>  array('required' => 'yes', 'verification' => 'numeric'),
              'link_entry_id' =>  array('required' => 'no',  'verification' => 'numeric'),
              'subarea_id'    =>  array('required' => 'no',  'verification' => 'numeric'),
              'location'      =>  array('required' => 'yes', 'verification' => 'none'),
              'visible'       =>  array('required' => 'no',  'verification' => 'boolean'),
              '303'           =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '320'           =>  array('required' => 'yes', 'verification' => 'numeric'),
              '55'            =>  array('required' => 'no', 'verification' => 'non-blank'),
              '376'           =>  array('required' => 'no',  'verification' => 'yes/no'),
              '130'           =>  array('required' => 'no',  'verification' => 'yes/no'),
              '434'           =>  array('required' => 'no',  'verification' => 'yes/no'),
              '73'            =>  array('required' => 'no',  'verification' => 'yes/no'),
              '151'           =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '16'            =>  array('required' => 'yes', 'verification' => 'non-blank', 'limit'=>250),
              '22'            =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '160.3'         =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '160.6'         =>  array('required' => 'yes', 'verification' => 'none'),
              '234'           =>  array('required' => 'yes', 'verification' => 'none', 'limit'=>250),
              '217'           =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '96.3'          =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '96.6'          =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '98'            =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '101.3'         =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '101.4'         =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '101.5'         =>  array('required' => 'yes', 'verification' => 'non-blank'),
              '101.6'         =>  array('required' => 'yes', 'verification' => 'non-blank')
    );

    $row = 1;
    //read the uploaded file
    while (($csvFile = fgetcsv($file, 10000, ",")) !== FALSE){
      //build array of header to data
      if($row==1){ //check that all required field was passed
        $headerRow = $csvFile;
        //check that all required rows were included
        foreach($fieldArray as $key=>$header){
          if($header['required']=='yes' && !in_array($key, $csvFile)){
            $error .= "Missing required field '".$key."'.<br/>";
          }
        }

        if($error!=''){
          $error .= 'Import Failed.';
          break; //exit the while loop
        }
      }else{
        $dataRow = array();
        foreach($csvFile as $key=>$data){
          $headerKey = $headerRow[$key];
          $dataRow[$headerKey] = $data;
        }
        $data2Process[] = $dataRow;
      }
      $row++;
    }

    $row = 2; //skipped the header row above
    //process the data
    foreach($data2Process as $rowData) { //row loop
      //check for data needing verification before upload
      $passCriteria = true;
      foreach($rowData as $fieldKey=>$field){ //column loop
        if(isset($fieldArray[$fieldKey])){
          $field = trim($field); //remove any extra spaces at the end
          $verification = $fieldArray[$fieldKey]['verification'];
          switch ($verification){
            case 'numeric':
              if(!is_numeric($field)){
                $error .= 'Error on row '.$row.'. Data in column "'.$fieldKey.'" is not numeric. Row skipped.<br/>';
                $passCriteria = false;
              }
              break;
            case 'non-blank':
              if($field == ''){
                $error .= 'Error on row '.$row.'. Data in column "'.$fieldKey.'" cannot be blank. Row skipped.<br/>';
                $passCriteria = false;
              }
              break;
            case 'boolean':
              if($field != 1 && $field !=0 ){
                $error .= 'Error on row '.$row.'. Data in column "'.$fieldKey.'" is invalid.  Valid values are 0 and 1 only. Row skipped.<br/>';
                $passCriteria = false;
              }
              break;
            case 'yes/no';
              if(strtolower($field) != 'yes' && strtolower($field) != 'no' ){
                $error .= 'Error on row '.$row.'. Data in column "'.$fieldKey.'" is invalid.  Valid values are yes and no only. Row skipped.<br/>';
                $passCriteria = false;
              }
              break;
          }
        }
        //process the row if it passed the validation
        if(!$passCriteria){
          break;//exit foreach column loop
        }
      } //end foreach fields

      if($passCriteria){
        addEntry($rowData, $user);
      }
      $row++;
    }//end foreach row

    if(empty($error)) $error = 'Data Uploaded Successfully';

    return $error;
  }

  function addEntry($rowData, $user){
    global $wpdb;
    if(isset($rowData['entry_creator']))  $user = $rowData['entry_creator'];
    $entry  = array('form_id'=>$rowData['form_id'],'status'=>'active',"created_by" => $user, "date_created" => "");
    $entry['form_id'] = $rowData['form_id'];//assign form id

    //first let's strip the numeric field id's and add that to the entry object
    foreach($rowData as $key=>$field){
      if(is_numeric($key)){
        //$entry[$key] = htmlspecialchars($field,ENT_QUOTES, 'UTF-8');
        $entry[$key] = $field;
      }
    }

    $return = GFAPI::add_entry( $entry );
    if( is_wp_error( $return ) ) {
      echo 'attempted to add '.$entry[151].'<br/>';
        echo $return->get_error_message();
        var_dump($entry);
        echo '<br/>';
    }else{
      $entryID = $return;
      //echo 'Created Entry ID '.$entryID;
    }

    //link to another entry id
    if(isset($rowData['link_entry_id']) && is_numeric($rowData['link_entry_id'])){
      //link_entry_id
      $sql = "INSERT INTO wp_rg_lead_rel (`parentID`, `childID`, `form`) values (".$rowData['link_entry_id'].", ".$entryID.", '".$entry['form_id']."')";
      $wpdb->insert('wp_rg_lead_rel',array('parentID'=>$rowData['link_entry_id'],'childID'=>$entryID,'form'=>$entry['form_id']),array('%d','%d', '%d'));
    }

    //add tickets
    if(isset($rowData['eid']) && is_numeric($rowData['eid'])){
      //eid	ticket_code	num_tickets	visible
    }

    //assign subarea and location
    if(isset($rowData['subarea_id']) && is_numeric($rowData['subarea_id'])){
      //subarea_id	location
      $wpdb->insert('wp_mf_location',array('entry_id'=>$entryID,'subarea_id'=>$rowData['subarea_id'],'location'=>$rowData['location']),array('%d','%d','%s'));
    }
    //set resource status to ready
    gform_update_meta( $entryID, 'res_status','ready' );

    //create maker table info
     GFRMTHELPER::updateMakerTables($entryID);
  }
