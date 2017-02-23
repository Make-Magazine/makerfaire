<?php
/* Class to update all stuff maker related */
if ( ! class_exists( 'GFRMTHELPER' ) ) {
	die();
}

class GFRMTHELPER {
  function __construct(){
    global $wpdb;
  }

	/*
   * This function is called when there is an entry update or new entry submission
   * $type - this tells us if this is a new submission or an update to the entry
	*/
	public static function gravityforms_makerInfo($entry,$form,$type='update') {
    //build/update RMT data
    self::buildRmtData($entry, $form, $type);

    //update/insert into maker tables
    self::updateMakerTables($entry['id']);
	}

  public static function buildRmtData($entry, $form, $type='update'){
    global $wpdb;
    $attribute   = array();
    $resource    = array();
    $entryID     = $entry['id'];
    $form_type   = $form['form_type'];

    //original entry ID
    if($form['id']==105){
      $origEntryID = (isset($entry['448']) ? $entry['448'] : '');
    }else{
      $origEntryID = (isset($entry['20']) ? $entry['20'] : '');
    }

    global $current_user;
    $user = (isset($current_user->ID) ? $current_user->ID:NULL);

    //set faire_location for this entry
    $faire_location= $wpdb->get_var("select faire_location from wp_mf_faire where FIND_IN_SET (".$form['id'] . ",wp_mf_faire.form_ids)> 0");

    /* RMT logic is stored in wp_rmt_rules and wp_rmt_rules_logic */

    //pull all RMT rules
    $sql = "SELECT rules.id as rule_id, rules.form_type, rules.rmt_type, rules.rmt_field, rules.value, rules.comment, "
                . " logic.field_number, logic.operator, logic.value as logic_value "
         . "FROM wp_rmt_rules rules, `wp_rmt_rules_logic` logic "
         . "WHERE rules.id=logic.rule_id "
         . "ORDER BY `rule_id` ASC";

    $rules = array();
    foreach($wpdb->get_results($sql) as $row){
      //build rule array
      $rules[$row->rule_id]['form_type'] = $row->form_type;
      $rules[$row->rule_id]['rmt_type']  = $row->rmt_type;
      $rules[$row->rule_id]['rmt_field'] = $row->rmt_field;
      $rules[$row->rule_id]['value']     = $row->value;
      $rules[$row->rule_id]['comment']   = $row->comment;
      $rules[$row->rule_id]['logic'][] = array(
          'field_number' => $row->field_number,
          'operator'     => $row->operator,
          'value'        => $row->logic_value);
    }

    foreach($rules as $rule){
      $pass = false;
      foreach($rule['logic'] as $logic){
        $field_number = $logic['field_number'];
        if($field_number=='faire_location'){
          $entryfield = $faire_location;
        }elseif($field_number=='form_type'){
          $entryfield = $form_type;
        }elseif(isset($entry[$field_number])){
          $entryfield = $entry[$field_number];
        }else{
          $entryfield = '';
        }

        //check logic here
        if($logic['operator'] == 'is') {
          if($entryfield == $logic['value']) {
            $pass = true;
          }else{
            $pass = false;
            break;
          }
        } elseif($logic['operator'] == 'not') {
          if($entryfield != $logic['value']) {
            $pass = true;
          }else{
            $pass = false;
            break;
          }
        } elseif($logic['operator'] == 'contains') {
          $pos = strpos($entryfield, $logic['value']);
          if ($pos !== false){
            $pass = true;
          }else{
            $pass = false;
            break;
          }
        } else {
          //other operator logic goes here
        }
      }

      //logic met - set RMT field
      if($pass){
        //look if there is a a field in the value or comment field (these are surrounded by {} )
        $value   = findFieldData($rule['value'], $entry);
        $comment = findFieldData($rule['comment'], $entry);

        if($rule['rmt_type']=='resource') {
          //set $value and $comment {}
          $resource[] = array($rule['rmt_field'],$value,$comment);
        } elseif($rule['rmt_type']=='attribute') {
          $attribute[] = array($rule['rmt_field'],$value,$comment);
        }
      }
    }

    //if form type=payment we need to map resource fields back to the original entry
    if($form_type == 'Payment' ){
      //get original entry id
      $entryID = ($origEntryID != '' ? $origEntryID:$entryID);
      //check if any electrical resources have been set
      $sql = "SELECT wp_rmt_entry_resources.ID "
              . " from wp_rmt_entry_resources, wp_rmt_resources, wp_rmt_resource_categories "
              . " where resource_id=wp_rmt_resources.ID and "
              . "       resource_category_id=wp_rmt_resource_categories.ID and "
              . "       entry_id = $entryID and "
              . "       wp_rmt_resource_categories.category like '%electrical%'";
      //if an electrical resource has been set, delete it
      $resourceElec = $wpdb->get_var($sql);

      if($resourceElec != NULL){ //if result, update.
        //delete any electrical resources MF-901
        $wpdb->delete( 'wp_rmt_entry_resources', array( 'ID' => $resourceElec ) );
      }
    }


    //if this is a payment form overwrite the user
    if($form_type == 'Payment'){
      $user = 0;  //user = 0 - payment form
    }

    $chgRPTins = array();

    /*
     *        R M T
     *  R E S O U R C E S
     *
     */
    foreach($resource as $value){
      $resource_id = $value[0];
      $qty         = $value[1];
      $comment     = htmlspecialchars($value[2]);

      /* If Payment form, we allow them to set multiple items for the same category
       *    If the resource is already set
       *        if the qty is 0 - delete resource
       *        else            - update existing resource
       *    else if the resource is not set
       *        if the qty is not 0 - add resource
       * if form type is not payment
       *    if the entry already has a resource set with the same category - overwrite
       *    else - add new
       */

      //on new records the user is always null unless this is a payment form
      if($form_type == 'Payment'){
        $user = 'NULL';
        // is resource already set?
        $res = $wpdb->get_row("select wp_rmt_entry_resources.*, wp_rmt_resources.token "
                . " from wp_rmt_entry_resources"
                . " left outer join wp_rmt_resources on wp_rmt_resources.ID=resource_id"
                . ' where entry_id='.$entryID.' and resource_id ='.$resource_id);
        //matching record found
        if ( null !== $res ) {  // yes, is qty 0?
          if($res->lockBit==0){ //do not update if this resource is locked
            if($qty==0){  // yes, delete
              $wpdb->get_results('delete from `wp_rmt_entry_resources` where id='.$res->ID);
              $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->qty, '', 'RMT Resource: '.$res->token.' -  qty');
              $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->comment, '', 'RMT Resource: '.$res->token.' - comment');
            } else { // no, update
              //If there are changes, update this record
              if($res->resource_id != $resource_id || $res->qty != $qty || $res->comment != $comment){
                $wpdb->get_results('update `wp_rmt_entry_resources` '
                      . ' set `resource_id` = '.$resource_id.', `qty` = '.$qty.',user='.$user.',comment="'.$comment.'", update_stamp=now() where id='.$res->ID);

                //update change report
                if($res->qty!=$qty)
                  $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->qty, $qty, 'RMT Resource: '.$res->token.' -  qty');
                if($res->comment != $comment)
                  $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->comment, $comment, 'RMT Resource: '.$res->token.' - comment');
                if($res->resource_id!=$resource_id)
                  $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->resource_id, $resource_id, 'RMT Resource: id changed');
              }
            }
          }
        } elseif($qty!=0) { //no record found, if qty is not 0 - add
          //insert this record
          $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources`  (`entry_id`, `resource_id`, `qty`, `comment`, user) "
                  . " VALUES (".$entryID.",".$resource_id .",".$qty . ',"' . $comment.'",'.$user.')');
          //update change report
          $res         = $wpdb->get_row('SELECT token FROM `wp_rmt_resources` where ID='.$resource_id);
          $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, '', $qty, 'RMT Resource: '.$res->token.' -  qty');
          $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, '', $comment, 'RMT Resource: '.$res->token.' - comment');
        }
      } else { //all other form types
        $cat_id = $wpdb->get_var("select resource_category_id from wp_rmt_resources where id = ".$resource_id);

        //find if they already have a resource set with the same Item (ie. chairs, tables, electricity, etc)
        $res = $wpdb->get_row('SELECT entry_res.*, res.resource_category_id, res.token '
                            . ' FROM `wp_rmt_entry_resources` entry_res,wp_rmt_resources res '
                            . ' where entry_id='.$entryID.' and entry_res.resource_id = res.ID and resource_category_id='.$cat_id);

        //matching record found
        if ( null !== $res ) {
          //check lockbit
          if($res->lockBit==0){
            //If there are changes, update this record
            if($res->resource_id!=$resource_id || $res->qty!=$qty){
              $wpdb->get_results('update `wp_rmt_entry_resources` '
                    . ' set `resource_id` = '.$resource_id.', `qty` = '.$qty.', user='.$user.', update_stamp=now() where id='.$res->ID);

              //update change report
              if($res->qty!=$qty)
                $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->qty, $qty, 'RMT resource: '.$res->token.' -  qty');
              if($res->resource_id!=$resource_id)
                $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, $res->resource_id, $resource_id, 'RMT resource: id changed');
            }
          }
        }else{
          //insert this record
          $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources`  (`entry_id`, `resource_id`, `qty`, `comment`, user) "
                          . " VALUES (".$entryID.",".$resource_id .",".$qty . ',"' . $comment.'",'.$user.')');

          //update change report
          $res         = $wpdb->get_row('SELECT token FROM `wp_rmt_resources` where ID='.$resource_id);
          $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, '', $qty, 'RMT resource: '.$res->token.' -  qty');
          $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $resource_id, '', $comment, 'RMT resource: '.$res->token.' - comment');
        }
      } //end check for payment form type
    }

    /*
     *        R M T
     *  A T T R I B U T E S
     *
     */
    foreach($attribute as $value){
      $attribute_id = $value[0];
      $attvalue     = htmlspecialchars($value[1]);
      $comment      = htmlspecialchars($value[2]);

      //check if attribute is locked
      $res = $wpdb->get_row("select wp_rmt_entry_attributes.*, wp_rmt_entry_att_categories.token"
                          . " from wp_rmt_entry_attributes"
                          . " left outer join wp_rmt_entry_att_categories on wp_rmt_entry_att_categories.ID=attribute_id"
                          . ' where entry_id = '.$entryID.' and attribute_id = '.$attribute_id);
       //matching record found
      if ( null !== $res ) {
        if($res->lockBit==0){  //If this attribute is not locked, update this record
          //if this is a payment record, append the payment comment to the end of the existing comment
          if($form_type == 'Payment'){
            $comment = $res->comment.'<br/>'.$form_type . ' Form Comment - ' . $comment;
          }
          //if there are changes, update the record
          if($res->comment!=$comment || $res->value!=$attvalue){
            $wpdb->get_results('update `wp_rmt_entry_attributes` '
                  . ' set comment="'.$comment.'", user='.$user.', value="'.$attvalue .'",	update_stamp=now()'
                  . ' where id='.$res->ID);
            //update change report
            if($res->comment!=$comment)
              $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $attribute_id, $res->comment, $comment, 'RMT attribute: '.$res->token.' -  comment');
            if($res->value!=$attvalue)
              $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $attribute_id, $res->value, $attvalue, 'RMT attribute: '.$res->token.' -  value');
          }
        }
      }else{
        $wpdb->get_results("INSERT INTO `wp_rmt_entry_attributes`(`entry_id`, `attribute_id`, `value`,`comment`,user) "
                      . " VALUES (".$entryID.",".$attribute_id .',"'.$attvalue . '","' . $comment.'",'.$user.')');

        //update change report
        $res = $wpdb->get_row('SELECT token FROM `wp_rmt_entry_att_categories` where ID='.$attribute_id);
        $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $attribute_id, '', $attvalue, 'RMT attribute: '.$res->token.' -  value');
        $chgRPTins[] = RMTchangeArray($user, $entryID, $form['id'], $attribute_id, '', $comment, 'RMT attribute: '.$res->token.' -  comment');
      }
    }

    /*
     *    C H A N G E
     *    R E P O R T
     *
     */
    //Write to the change report if this is a payment form or if this is an update thru MAT or admin resources tab
    if($type=='update' || $form_type == 'Payment'){
      if(!empty($chgRPTins))  updateChangeRPT($chgRPTins);
    }

    /*
     *  R E S O U R C E     S T A T U S
     *  R E S O U R C E     A S S I G N     T O
     *
     * note: resource assign to values can be found in
     *      wp-content/themes/makerfaire/functions/gravity_forms/gravityforms_entry_meta.php
     *      in custom_entry_meta function
     */

    /*  set default values */
    $assignTo    = 'na';//not assigned to anyone
    $status      = 'ready';//ready

    /* MF-1644 new logic based on indicators
     *    1) CMIndicator(376) = Yes
     *         Resource Status needs to be set to Review
     *         Resource Assign To set to Kerry
     *    2) CMIndicator = No + FeeIndicator (434) = Yes
     *         Resource Statues => Review
     *         Resource Assign To => Siana
     *    3) If CM=no and Fee indicator=No
     *         Resource status= ready (unless any of the other logic turns it into review)
     */
    if(isset($entry['376']) && $entry['376'] == 'Yes') { //cm indicator
      $status   = 'review';
      $assignTo = 'cm_team';
    }elseif(isset($entry['434']) && $entry['434'] =='Yes') { //fee indicator
      $status   = 'review';
      $assignTo = 'fee_team';
    }elseif( isset($entry['83']) && $entry['83'] == 'Yes'){  //field 83
      $status   = 'review';
      $assignTo = 'fire';
    }elseif(isset($entry['73']) && $entry['73'] == 'Yes' &&
            isset($entry['75']) && $entry['75'] == 'Other. Power request specified in the Special Power Requirements box'){
      $status   = 'review';
      $assignTo = 'power';
    }elseif(isset($entry['64']) && $entry['64'] != ''){
      $status   = 'review';
      $assignTo = 'special_request'; //Kerry
    }
    //overrides all other logic
    if($form_type == 'Payment') {
      $status = 'ready';
    }

    // update custom meta field (do not update if meta already exists)
    $res_status = gform_get_meta( $entryID, 'res_status' );
    $res_assign = gform_get_meta( $entryID, 'res_assign' );

    //  if the current status or assign to is blank, or
    //  if the calculated assign to is different than the curent assign to,
    //      update the vaues
    if($assignTo != $res_assign || empty($res_status) || empty($res_assign)) {
      //update the status and assign to
      gform_update_meta( $entryID, 'res_status', $status, $form['id'] );
      gform_update_meta( $entryID, 'res_assign', $assignTo, $form['id'] );
    }
  }

  /*
   * This table will add/update records to the following tables:
   *    wp_mf_entity, wp_mf_maker, wp_mf_maker_to_entity
   */
   public static function updateMakerTables($entryID){
    global $wpdb;
    $entry    = GFAPI::get_entry($entryID);
    $form_id  = $entry['form_id'];
    $form     = GFAPI::get_form($form_id);

    //build Maker Data Array
    $data = self::buildMakerData($entry,$form);
    $makerData  = $data['maker'];
    $entityData = $data['entity'];

    $categories = is_array($entityData['categories'] ? implode(',',$entityData['categories']) :'');
    /*
     * Update Entity Table - wp_mf_entity
     */
    $wp_mf_entitysql = "insert into wp_mf_entity (lead_id, presentation_title, presentation_type, special_request, "
                    . "     OnsitePhone, desc_short, desc_long, project_photo, status,category,faire,mobile_app_discover,form_id) "
                    . " VALUES ('" . $entryID             . "',"
                            . ' "' . $entityData['project_name']            . '", '
                            . ' "' . $entityData['presentation_type']       . '", '
                            . ' "' . $entityData['special_request']         . '", '
                            . ' "' . $entityData['onsitePhone']             . '", '
                            . ' "' . $entityData['public_description']      . '", '
                            . ' "' . $entityData['private_description']     . '", '
                            . ' "' . $entityData['project_photo']           . '", '
                            . ' "' . $entityData['status']                  . '", '
                            . ' "' . $categories . '", '
                            . ' "' . $entityData['faire']                   . '", '
                            . '  ' . $entityData['mobile_app_discover']     . ','
                            . '  ' . $entityData['form_id'].') '
                    . ' ON DUPLICATE KEY UPDATE presentation_title  = "'.$entityData['project_name']            . '", '
                    . '                         presentation_type   = "'.$entityData['presentation_type']       . '", '
                    . '                         special_request     = "'.$entityData['special_request']         . '", '
                    . '                         OnsitePhone         = "'.$entityData['onsitePhone']             . '", '
                    . '                         desc_short          = "'.$entityData['public_description']      . '", '
                    . '                         desc_long           = "'.$entityData['private_description']     . '", '
                    . '                         project_photo       = "'.$entityData['project_photo']           . '", '
                    . '                         status              = "'.$entityData['status']                  . '", '
                    . '                         category            = "'.$categories. '", '
                    . '                         faire               = "'.$entityData['faire']                   . '", '
                    . '                         form_id             =  '.$entityData['form_id']                 . ','
                    . '                         mobile_app_discover = "'.$entityData['mobile_app_discover']     . '"';
    $wpdb->get_results($wp_mf_entitysql);

    /*  Update Maker Table - wp_mf_maker table
     *    $makerData types - contact, presenter, presenter2-7
     */

    //loop thru
    foreach($makerData as $type => $typeArray){
      $firstName = (isset($typeArray['first_name']) ? esc_sql($typeArray['first_name']) : '');
      $lastName  = (isset($typeArray['last_name'])  ? esc_sql($typeArray['last_name'])  : '');
      $email     = (isset($typeArray['email'])      ? esc_sql($typeArray['email'])      : '');

      if((trim($firstName) == '' && trim($lastName) == '') || trim($email) == '') {
        //don't write the record, no maker here.  Move along
      }else{
        $bio      = (isset($typeArray['bio'])     ? htmlentities($typeArray['bio']) : '');
        $phone    = (isset($typeArray['phone'])   ? esc_sql($typeArray['phone'])    : '');
        $twitter  = (isset($typeArray['twitter']) ? esc_sql($typeArray['twitter'])  : '');
        $photo    = (isset($typeArray['photo'])   ? esc_sql($typeArray['photo'])    : '');
        $website  = (isset($typeArray['website']) ? esc_sql($typeArray['website'])  : '');

        /*  GUID
         * If this maker is already in the DB - pull the maker_id, else let's create one
         */
        $results = $wpdb->get_results($wpdb->prepare("SELECT maker_id FROM wp_mf_maker WHERE email=%s", $email) );
        $guid = ($wpdb->num_rows != 0?$guid = $results[0]->maker_id: createGUID($entryID .'-'.$type));

        $wp_mf_makersql = "INSERT INTO wp_mf_maker (`First Name`, `Last Name`, `Bio`, `Email`, `phone`, `TWITTER`,  `maker_id`, `Photo`, `website`) "
                        . ' VALUES ("'.$firstName.'","'.$lastName.'","'.$bio.'","'.$email.'", "'.$phone.'", '
                                 . '"'.$twitter.'", "'.$guid.'", "'.$photo.'", "'.$website.'")'
                        . ' ON DUPLICATE KEY UPDATE maker_id="'.$guid.'"';

        //only update non blank fields
        $wp_mf_makersql .= ($firstName != '' ? ', `First Name` = "' . $firstName . '"' : '');
        $wp_mf_makersql .= ($lastName  != '' ? ', `Last Name`  = "' . $lastName  . '"' : '');
        $wp_mf_makersql .= ($bio       != '' ? ', `Bio`        = "' . $bio       . '"' : '');
        $wp_mf_makersql .= ($phone     != '' ? ', `phone`      = "' . $phone     . '"' : '');
        $wp_mf_makersql .= ($twitter   != '' ? ', `TWITTER`    = "' . $twitter   . '"' : '');
        $wp_mf_makersql .= ($photo     != '' ? ', `Photo`      = "' . $photo     . '"' : '');
        $wp_mf_makersql .= ($website   != '' ? ', `website`    = "' . $website   . '"' : '');

        $wpdb->get_results($wp_mf_makersql);

        //build maker to entity table
        //(key is on maker_id, entity_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity` (`maker_id`, `entity_id`, `maker_type`) "
                              . ' VALUES ("'.$guid.'",'.$entryID.',"'.$type.'")  ON DUPLICATE KEY UPDATE maker_id="'.$guid.'";';

        $wpdb->get_results($wp_mf_maker_to_entity);
      }
    }
  }

  //function to build the maker data table to update the wp_mf_maker table
  public static function buildMakerData($lead,$form){
    global $wpdb;
    $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');
    $entry_id     = $lead['id'];
		$form_id      = $form['id'];
    $project_name = (isset($lead['109'])&&$lead['109']!='' ? $lead['109']:(isset($lead['151']) ? $lead['151']:''));

    // Load Names
    $isGroup =false;
    if(isset($lead['105'])){
      $isGroup =(strpos($lead['105'], 'group') !== false?true:false);
    }

    $isOneMaker =false;
    if(isset($lead['105'])&&$lead['105']!=''){
      $isOneMaker =(strpos($lead['105'], 'One') !== false?true:false);
    }

    /*
     * Build Maker Array
     */
    $makerArray = array();

    //Contact
    $makerArray['contact'] =
        array(
          'first_name'  => (isset($lead['96.3'])  ? $lead['96.3']:''),
          'last_name'   => (isset($lead['96.6'])  ? $lead['96.6']:''),
          'bio'         => '',
          'email'       => (isset($lead['98'])    ? $lead['98']:''),
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => '',
          'website'     => ''
      );

    // Presenter / Maker 1
    if(!$isGroup){
      //if this isn't a group we need to have a valid email for the presenter(maker 1) record.
      // if not set, use contact email
      $email = (isset($lead['161'])&&$lead['161']!='' ? $lead['161']:$entry_id.'-presenter@makermedia.com');
      $makerArray['presenter'] = array(
          'first_name'  => (isset($lead['160.3']) ? $lead['160.3']:''),
          'last_name'   => (isset($lead['160.6']) ? $lead['160.6']:''),
          'bio'         => (isset($lead['234'])   ? $lead['234']:''),
          'email'       => $email,
          'phone'       => (isset($lead['185'])   ? $lead['185']:''),
          'twitter'     => (isset($lead['201'])   ? $lead['201']:''),
          'photo'       => (isset($lead['217'])   ? $lead['217']:''),
          'website'     => (isset($lead['209'])   ? $lead['209']:''),
      );
    }else{
      // if field 105 indicates this is a group,
      //  set Presenter/Maker 1 to the group information
      $makerArray['presenter'] = array(
          'first_name'  => $project_name,
          'last_name'   => '',
          'bio'         => (isset($lead['110'])   ? $lead['110']:''),
          'email'       => $entry_id.'-group@makermedia.com',
          'phone'       => (isset($lead['99'])    ? $lead['99']:''),
          'twitter'     => (isset($lead['322'])   ? $lead['322']:''),
          'photo'       => (isset($lead['111'])   ? $lead['111']:''),
          'website'     => (isset($lead['112'])   ? $lead['112']:''),
      );
    }

    // we need to have at least 1 presenter/maker.  if these fields are empty, pull from the contact info
    if(trim($makerArray['presenter']['first_name'])=='' && trim($makerArray['presenter']['last_name'])==''){
      //let's try to get the name from the contact info
      $firstName  =  (isset($makerArray['contact']['first_name']) ? esc_sql($makerArray['contact']['first_name']) : '');
      $lastName   =  (isset($makerArray['contact']['last_name'])  ? esc_sql($makerArray['contact']['last_name'])  : '');
    }

    // If sponsor, Set Presenter/Maker 1 name to company name
    if($form['form_type']=='Sponsor'){
        $makerArray['presenter']['first_name'] = htmlentities($project_name);
        $makerArray['presenter']['last_name']  = ' ';
      }

    // only set the below data if the entry is not marked as one maker
    if(!$isOneMaker){
      $makerArray['presenter2']= array(
          'first_name'  => (isset($lead['158.3']) ? $lead['158.3']:''),
          'last_name'   => (isset($lead['158.6']) ? $lead['158.6']:''),
          'bio'         => (isset($lead['258'])   ? $lead['258']:''),
          'email'       => (isset($lead['162'])   ? $lead['162']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['192'])   ? $lead['192']:''),
          'twitter'     => (isset($lead['208'])   ? $lead['208']:''),
          'photo'       => (isset($lead['224'])   ? $lead['224']:''),
          'website'     => (isset($lead['216'])   ? $lead['216']:''),
      );
      $makerArray['presenter3'] = array(
          'first_name'  => (isset($lead['155.3']) ? $lead['155.3']:''),
          'last_name'   => (isset($lead['155.6']) ? $lead['155.6']:''),
          'bio'         => (isset($lead['259'])   ? $lead['259']:''),
          'email'       => (isset($lead['167'])   ? $lead['167']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['190'])   ? $lead['190']:''),
          'twitter'     => (isset($lead['207'])   ? $lead['207']:''),
          'photo'       => (isset($lead['223'])   ? $lead['223']:''),
          'website'     => (isset($lead['215'])   ? $lead['215']:''),
      );
      $makerArray['presenter4'] = array(
          'first_name'  => (isset($lead['156.3']) ? $lead['156.3']:''),
          'last_name'   => (isset($lead['156.6']) ? $lead['156.6']:''),
          'bio'         => (isset($lead['260'])   ? $lead['260']:''),
          'email'       => (isset($lead['166'])   ? $lead['166']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['191'])   ? $lead['191']:''),
          'twitter'     => (isset($lead['206'])   ? $lead['206']:''),
          'photo'       => (isset($lead['222'])   ? $lead['222']:''),
          'website'     => (isset($lead['214'])   ? $lead['214']:''),
      );
      $makerArray['presenter5'] = array(
          'first_name'  => (isset($lead['157.3']) ? $lead['157.3']:''),
          'last_name'   => (isset($lead['157.6']) ? $lead['157.6']:''),
          'bio'         => (isset($lead['261'])   ? $lead['261']:''),
          'email'       => (isset($lead['165'])   ? $lead['165']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['189'])   ? $lead['189']:''),
          'twitter'     => (isset($lead['205'])   ? $lead['205']:''),
          'photo'       => (isset($lead['220'])   ? $lead['220']:''),
          'website'     => (isset($lead['213'])   ? $lead['213']:''),
      );
      $makerArray['presenter6'] = array(
          'first_name'  => (isset($lead['159.3']) ? $lead['159.3']:''),
          'last_name'   => (isset($lead['159.6']) ? $lead['159.6']:''),
          'bio'         => (isset($lead['262'])   ? $lead['262']:''),
          'email'       => (isset($lead['164'])   ? $lead['164']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['188'])   ? $lead['188']:''),
          'twitter'     => (isset($lead['204'])   ? $lead['204']:''),
          'photo'       => (isset($lead['221'])   ? $lead['221']:''),
          'website'     => (isset($lead['211'])   ? $lead['211']:''),
      );
      $makerArray['presenter7'] = array(
          'first_name'  => (isset($lead['154.3']) ? $lead['154.3']:''),
          'last_name'   => (isset($lead['154.6']) ? $lead['154.6']:''),
          'bio'         => (isset($lead['263'])   ? $lead['263']:''),
          'email'       => (isset($lead['163'])   ? $lead['163']:$entry_id.'-group@makermedia.com'),
          'phone'       => (isset($lead['187'])   ? $lead['187']:''),
          'twitter'     => (isset($lead['203'])   ? $lead['203']:''),
          'photo'       => (isset($lead['219'])   ? $lead['219']:''),
          'website'     => (isset($lead['212'])   ? $lead['212']:''),
      );
    }

    /*
     * set entity information
     */
    $leadCategory = array();
    $MAD          = 0;

    //Categories (current fields in use)
    foreach($lead as $leadKey=>$leadValue){
      //4 additional categories
      $pos = strpos($leadKey, '321');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //main catgory
      $pos = strpos($leadKey, '320');
      if ($pos !== false) {
        $leadCategory[]=$leadValue;
      }
      //check the flag field 304
      $pos = strpos($leadKey, '304');
      if ($pos !== false) {
        if($leadValue=='Mobile App Discover')  $MAD = 1;
      }
    }

    //verify we only have unique categories
    $leadCategory = array_unique($leadCategory);

    //determine faire
    $faire = $wpdb->get_var('select faire from wp_mf_faire where FIND_IN_SET ('.$form_id.', wp_mf_faire.form_ids)> 0');

    if($form_type == 'Presentation') {
      $project_photo = $makerArray['presenter']['photo'];
    }else{
      $project_photo = (isset($lead['22']) ? $lead['22'] : '');
    }
    //if the entry status is active, use field 303 as the status, else use entry status
    if($lead['status'] == 'active'){
      $status = (isset($lead['303']) ? htmlentities($lead['303']) : '');
    }else{
      $status = $lead['status'];
    }

    $entityArray =
      array(
        'project_photo'       => $project_photo,
        'project_name'        => (isset($lead['151']) ? htmlentities($lead['151']) : ''),
        'presentation_type'   => (isset($lead['1'])   ? htmlentities($lead['1'])   : ''),
        'special_request'     => (isset($lead['64'])  ? htmlentities($lead['64'])  : ''),
        'onsitePhone'         => (isset($lead['265']) ? htmlentities($lead['265']) : ''),
        'public_description'  => (isset($lead['16'])  ? htmlentities($lead['16'])  : ''),
        'private_description' => (isset($lead['11'])  ? htmlentities($lead['11'])  : ''),
        'status'              => $status,
        'categories'          => $leadCategory,
        'faire'               => $faire,
        'mobile_app_discover' => $MAD,
        'form_id'             => $form_id
    );
    $return = array('maker'=>$makerArray,'entity'=>$entityArray);
    return $return;
  }
}

function RMTchangeArray($user, $entryID, $formID, $field_id, $field_before, $field_after, $fieldLabel){
    $return = array('user_id'           => $user,
      'lead_id'           => $entryID,
      'form_id'           => $formID,
      'field_id'          => $field_id,
      'field_before'      => $field_before,
      'field_after'       => $field_after,
      'fieldLabel'        => $fieldLabel,
      'status_at_update'  => '');
    return $return;
  }

  function findFieldData($var, $entry) {
    //check if we need to set an entry field as the value
    $pos = strpos($var, '{');
    while ($pos !== false) {
      $endPos = strpos($var, '}');
      $field_id  = substr($var, $pos+1,$endPos-$pos-1);
      $req_field = substr($var, $pos,$endPos-$pos+1);
      if(isset($entry[$field_id])) {
        //if the field is an array, create a comma separated list
        $fieldData = (is_array($entry[$field_id]) ? implode(',', $entry[$field_id]):$entry[$field_id]);
      }else{
        $fieldData = '';
      }
      $var = str_replace($req_field, $fieldData, $var);
      $pos = strpos($var, '{');
    }
    return $var;
  }