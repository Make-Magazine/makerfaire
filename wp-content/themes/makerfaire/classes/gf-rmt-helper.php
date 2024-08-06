<?php
/* Class to manage all things RMT related */
if (!class_exists('GFRMTHELPER')) {
  die();
}

class GFRMTHELPER {
  public static function get_user($entry, $form = array()) {
    if (empty($form) && isset($entry['form_id'])) {
      $form = GFAPI::get_form($entry['form_id']);
    }
    $form_type = $form['form_type'];

    //find current user
    $current_user = wp_get_current_user();
    $user = $current_user->ID;

    //If Payment form, set the user to show the as a payment         
    if ($form_type == 'Payment') $user = 0;
    return $user;
  }

  /* Loop an entry through the RMT rules and set fields according */
  public static function buildRmtData($entry, $form) {
    global $wpdb;

    $form_type = $form['form_type'];
    $entryID   = $entry['id'];
    //check for a Easy Passthrough token to find original entry ID
    $ep_token = rgget('ep_token');

    //if the ep_token is set, need to find the original entry ID to update instead
    if ($ep_token != '') {
      //find the associated entry id based on the token
      $origEntryID = $wpdb->get_var(
        $wpdb->prepare(
          "SELECT entry_id FROM wp_gf_entry_meta WHERE `meta_key` = '%s' AND `meta_value` = '%s'",
          'fg_easypassthrough_token',
          $ep_token
        )
      );

      $entryID = ($origEntryID != '' ? $origEntryID : $entryID);
    }

    //find the faire_location for this entry
    $faire_location = $wpdb->get_var("SELECT faire_location "
      . "   FROM wp_mf_faire "
      . "   WHERE FIND_IN_SET (" . $form['id'] . ", replace(wp_mf_faire.non_public_forms, \" \", \"\") )> 0 OR "
      . "         FIND_IN_SET (" . $form['id'] . ", replace(wp_mf_faire.form_ids, \" \", \"\"))> 0 order by id desc limit 1");

    /* RMT logic is stored in wp_rmt_rules and wp_rmt_rules_logic */
    //pull all RMT rules
    $sql = "SELECT rules.id as rule_id, rules.form_type, rules.rmt_type, rules.rmt_field, rules.value, rules.comment, "
      . " logic.field_number, logic.operator, logic.value as logic_value "
      . "FROM wp_rmt_rules rules, `wp_rmt_rules_logic` logic "
      . "WHERE rules.id=logic.rule_id "
      . "ORDER BY `rule_id` ASC";

    //build rule array
    $rules = array();
    foreach ($wpdb->get_results($sql) as $row) {
      $rules[$row->rule_id]['form_type'] = $row->form_type;
      $rules[$row->rule_id]['rmt_type']  = $row->rmt_type;
      $rules[$row->rule_id]['rmt_field'] = $row->rmt_field;
      $rules[$row->rule_id]['value']     = $row->value;
      $rules[$row->rule_id]['comment']   = $row->comment;
      $rules[$row->rule_id]['logic'][] = array(
        'field_number' => $row->field_number,
        'operator'     => $row->operator,
        'value'        => $row->logic_value
      );
    }

    //loop through rules
    foreach ($rules as $rule) {
      $pass = false;
      //loop through logic, as soon as one fails, we exit the foreach loop
      foreach ($rule['logic'] as $logic) {
        $field_number = $logic['field_number'];

        //what field are we looking for
        if ($field_number == 'faire_location') {
          $entryfield = $faire_location;
        } elseif ($field_number == 'form_type') {
          $entryfield = $form_type;
        } elseif (isset($entry[$field_number])) {
          $entryfield = $entry[$field_number];
        } else {
          $entryfield = '';
        }

        //check logic here
        if ($logic['operator'] == 'is') {
          if (strtolower($entryfield) == strtolower($logic['value'])) {
            $pass = true;
          } else {
            $pass = false;
            break;
          }
        } elseif ($logic['operator'] == 'not') {
          if (strtolower($entryfield) != strtolower($logic['value'])) {
            $pass = true;
          } else {
            $pass = false;
            break;
          }
        } elseif ($logic['operator'] == 'contains') {
          $pos = stripos($entryfield, $logic['value']);
          if ($pos !== false) {
            $pass = true;
          } else {
            $pass = false;
            break;
          }
        } else {
          //other operator logic goes here
        }
      } //end loop through each rule logic

      //logic met - set RMT field
      if ($pass) {
        //look if there is a a field in the value or comment field (these are surrounded by {} )
        $value   = findFieldData($rule['value'], $entry);
        $comment = findFieldData($rule['comment'], $entry);

        if ($rule['rmt_type'] == 'resource') {
          //update resource for entry
          self::rmt_update_resource($entryID, $rule['rmt_field'], (int) $value, $comment);
        } elseif ($rule['rmt_type'] == 'attribute') {
          //update attribute for entry
          self::rmt_update_attribute($entryID, $rule['rmt_field'], $value, $comment);
        }
      }
    } //end loop through rules
  } //end function

  /* update one field for a RMT item for an entry */
  public static function rmt_update_field($table, $fieldName, $newValue, $rowID) {
    global $wpdb;

    //first let's pull the current data so we can report the change in the change report
    $current  = $wpdb->get_row("select * from $table where ID= $rowID");

    $entry    = GFAPI::get_entry($current->entry_id);
    if ($table == 'wp_rmt_entry_resources') {
      $rmt_field = $current->resource_id;
    } elseif ($table == 'wp_rmt_entry_attributes') {
      $rmt_field = $current->attribute_id;
    } elseif ($table == 'wp_rmt_entry_attn') {
      $rmt_field = $current->attn_id;
    }

    $user = self::get_user($entry);

    //now update the RMT item
    $wpdb->get_results("update " . $table . ' set ' . $fieldName . '="' . $newValue . '",user= ' . $user . ' where ID=' . $rowID);

    updateChangeRPT(array(RMTchangeArray($entry, $rmt_field, $current->$fieldName, $newValue, 'Resource changed - ' . $fieldName)));
  }

  /* Update RMT resource data for an entry */
  public static function rmt_update_resource($entryID, $resource_id, $qty, $comment) {
    global $wpdb;
    $entry  = GFAPI::get_entry($entryID);
    $form   = GFAPI::get_form($entry['form_id']);
    $user   = self::get_user($entry, $form);

    $rowID  = 0;

    //default to adding the resource
    $type = 'insert';

    //find the category ID for this resource
    $cat_id = $wpdb->get_var("select resource_category_id from wp_rmt_resources where id = " . $resource_id);

    //Look for any resources set within the same category type (ie. chairs, tables, electricity, etc)    
    $res = $wpdb->get_row('SELECT entry_res.*, res.resource_category_id, res.description '
      . ' FROM `wp_rmt_entry_resources` entry_res, wp_rmt_resources res '
      . ' where entry_id=' . $entryID . ' and entry_res.resource_id = res.ID and resource_category_id=' . $cat_id);

    //check if this resource category has been set for this entry
    if (!is_null($res)) { //resource found of the same category
      //Is it the same resource?
      if ($res->resource_id == $resource_id) {
        //is there anything to update
        if ($res->qty == $qty && $res->comment == $comment) {
          //exit, there is nothing to update
          return;
          //is the resource unlocked OR is this a payment form?  
        } elseif ($res->lockBit == 0 || $form['form_type'] == 'Payment') {
          //update the resource
          $type = 'update';
        }
      } else {
        //Payment forms are allowed to have multiple resources of the same category
        //if this isn't a payment form and the resource is unlocked
        //what if they put 05 amps on their form but then paid for 10 amps
        if ($form['form_type'] != 'Payment' && $res->lockBit == 0) {
          //update the resource
          $type = 'update';
        }
      }
    }

    if ($type == 'update') {
      $rowID = $res->ID;
      //update the resource
      $wpdb->get_results('update `wp_rmt_entry_resources` '
        . ' set `resource_id` = ' . $resource_id . ', `qty` = ' . $qty . ', user=' . $user . ', update_stamp=now() where id=' . $res->ID);

      //did the resource itself change
      if ($res->resource_id != $resource_id) { //update the change report               
        $chgRPTins[] = RMTchangeArray($entry, $resource_id, $res->resource_id, $resource_id, 'Resource changed(' . $res->description . ')');
      }

      //did the qty change
      if ($res->qty != $qty) { //update the change report        
        $chgRPTins[] = RMTchangeArray($entry, $resource_id, $res->qty, $qty, 'Resource qty changed(' . addslashes($res->description) . ')');
      }

      //did the comment change
      if ($res->comment != $comment) { //update the change report        
        $chgRPTins[] = RMTchangeArray($entry, $resource_id, $res->comment, $comment, 'Resource comment changed(' . addslashes($res->description) . ')');
      }
    } elseif ($type == 'insert') {

      //insert this resource
      $wpdb->get_results("INSERT INTO `wp_rmt_entry_resources`  (`entry_id`, `resource_id`, `qty`, `comment`, user) "
        . " VALUES (" . $entryID . "," . $resource_id . "," . $qty . ',"' . $comment . '",' . $user . ')');

      $rowID = $wpdb->insert_id;

      //get some info about this resource
      $res = $wpdb->get_row("select description from wp_rmt_resources where ID = " . $resource_id);
      $note = 'Resource Added (' . addslashes($res->description) . ') : qty=' . $qty . ($comment != '' ? ', comment=' . $comment : '');

      //update change report      
      $chgRPTins[] = RMTchangeArray($entry, $resource_id, '', 'New', $note);
    }

    /* Update change report */
    if (!empty($chgRPTins))  updateChangeRPT($chgRPTins);

    //lock the resource if this a payment form
    if ($form['form_type'] == 'Payment') {
      self::rmt_set_lock_ind(1, $rowID, 'resource', $user);
    }

    return $rowID;
  }

  /* Update RMT attribute data for an entry */
  public static function rmt_update_attribute($entryID, $attribute_id, $value, $comment) {
    global $wpdb;
    $rowID = 0;

    $entry = GFAPI::get_entry($entryID);
    $form  = GFAPI::get_form($entry['form_id']);
    $user   = self::get_user($entry, $form);

    //look to see if this attribute is already set
    $res = $wpdb->get_row('SELECT * from wp_rmt_entry_attributes where entry_id=' . $entryID . ' and attribute_id=' . $attribute_id);

    //has this attribute been set for this entry?
    if (!is_null($res)) { //attribute found      
      //is the attribute unlocked?
      if ($res->lockBit == 0) {
        //if this is a payment record, append the payment comment to the end of the existing comment
        if (isset($form['form_type']) && $form['form_type'] == 'Payment') {
          $comment = $res->comment . '<br/>' . $form['form_type'] . ' Form Comment - ' . $comment;
        }

        //if there are changes, update the record
        if ($res->comment != $comment || $res->value != $value) {
          $rowID = $res->ID;
          $wpdb->get_results('update `wp_rmt_entry_attributes` '
            . ' set comment="' . $comment . '", user=' . $user . ', value="' . $value . '",	update_stamp=now()'
            . ' where id=' . $res->ID);

          //get description for this attribute      
          $att = $wpdb->get_row('SELECT category as description FROM `wp_rmt_entry_att_categories` where ID=' . $attribute_id);

          //determine what changed in order to update the change report        
          if ($res->value != $value)
            $chgRPTins[] = RMTchangeArray($entry, $attribute_id, $res->value, $value,  'Attribute value changed (' . addslashes($att->description) . ')');
          if ($res->comment != $comment)
            $chgRPTins[] = RMTchangeArray($entry, $attribute_id, $res->comment, $comment,  'Attribute comment changed (' . addslashes($att->description) . ')');
        } else {
          //nothing to update, exit
          return;
        }
      }
    } else {
      //add the attribute
      $wpdb->get_results("INSERT INTO `wp_rmt_entry_attributes`(`entry_id`, `attribute_id`, `value`,`comment`,user) "
        . " VALUES (" . $entryID . "," . $attribute_id . ',"' . $value . '","' . $comment . '",' . $user . ')');
      $rowID = $wpdb->insert_id;

      //update change report      
      $att = $wpdb->get_row('SELECT category as description FROM `wp_rmt_entry_att_categories` where ID=' . $attribute_id);
      $note = 'Attribute Added (' . addslashes($att->description) . ') : value=' . $value . ($comment != '' ? ', comment=' . $comment : '');
      $chgRPTins[] = RMTchangeArray($entry, $attribute_id, '', 'New', $note);
    }

    /* Update change report */
    if (!empty($chgRPTins))  updateChangeRPT($chgRPTins);

    //lock the attribute if this a payment form
    if ($form['form_type'] == 'Payment') {
      self::rmt_set_lock_ind(1, $rowID, 'attribute', $user);
    }

    return $rowID;
  }

  /* Update RMT attention data for an entry */
  public static function rmt_update_attention($entryID, $attn_id, $comment) {
    global $wpdb;
    $rowID = 0;

    $entry = GFAPI::get_entry($entryID);
    $form  = GFAPI::get_form($entry['form_id']);

    $user   = self::get_user($entry, $form);

    //add the ATTENTION
    $wpdb->get_results("INSERT INTO `wp_rmt_entry_attn`(`entry_id`, `attn_id`, `comment`,user) "
      . " VALUES (" . $entryID . "," . $attn_id . ',"' . $comment . '",' . $user . ')');
    $rowID = $wpdb->insert_id;

    //update change report      
    $att = $wpdb->get_row('SELECT value as description FROM `wp_rmt_attn` where ID=' . $attn_id);
    $note = 'Attention Added (' . addslashes($att->description) . ')' . ($comment != '' ? ': comment=' . $comment : '');
    $chgRPTins[] = RMTchangeArray($entry, $attn_id, '', 'New', $note);

    /* Update change report */
    if (!empty($chgRPTins))  updateChangeRPT($chgRPTins);

    return $rowID;
  }

  public static function rmt_set_lock_ind($lockBit, $rowID, $type) {
    global $wpdb;
    $chgRPTins = array();

    if ($rowID == 0) {
      return;
    }

    if ($type == 'resource') {
      //pull previous data to update change report      
      $res = $wpdb->get_row('SELECT entry_id, lockBit, resource_id as field, description ' .
        'FROM wp_rmt_entry_resources ' .
        'left outer join wp_rmt_resources ' .
        'on resource_id=wp_rmt_resources.ID ' .
        'where wp_rmt_entry_resources.id=' . $rowID);

      //update resource
      $wpdb->get_results('update wp_rmt_entry_resources set lockBit=' . $lockBit . ' where id=' . $rowID);
    } elseif ($type == 'attribute') {
      //pull previous data to update change report      
      $res = $wpdb->get_row('SELECT entry_id, lockBit, attribute_id as field, category as description ' .
        'FROM wp_rmt_entry_attributes ' .
        'left outer join wp_rmt_entry_att_categories ' .
        'on attribute_id=wp_rmt_entry_att_categories.ID ' .
        'where wp_rmt_entry_attributes.id=' . $rowID);

      //update attribute
      $wpdb->get_results('update wp_rmt_entry_attributes set lockBit=' . $lockBit . ' where id=' . $rowID);
    } else {
      return;
    }

    //get entry
    $entry_id = $res->entry_id;
    $entry = GFAPI::get_entry($entry_id);

    //Build Change report data 
    $chgRPTins[] = RMTchangeArray($entry, $res->field, ($res->lockBit == 0 ? 'Unlocked' : 'Locked'), ($lockBit == 0 ? 'Unlocked' : 'Locked'), ucfirst($type) . ' (' . addslashes($res->description) . ')');

    /* Update change report */
    if (!empty($chgRPTins))  updateChangeRPT($chgRPTins);
  }

  public static function rmt_delete($rowID = 0, $table = '', $entryID = 0) {
    global $wpdb;
    if ($rowID == 0 || $table == '' || $entryID == 0) {
      return;
    }

    $entry = GFAPI::get_entry($entryID);

    //first get the current value of the RMT field    
    $resAtt = $wpdb->get_row('SELECT * FROM ' . $table . ' where ID=' . $rowID);

    //We can now delete it
    $sql = "DELETE from " . $table . " where ID =" . $rowID;
    $wpdb->get_results($sql);

    //update change report for RMT data
    $chgRPTins = array();

    switch ($table) {
      case 'wp_rmt_entry_resources':
        $fieldID     = $resAtt->resource_id;
        $res         = $wpdb->get_row('SELECT description FROM `wp_rmt_resources` where ID=' . $fieldID);
        $chgRPTins[] = RMTchangeArray($entry, $fieldID, '', 'Deleted', 'Resource Deleted (' . addslashes($res->description) . ')');
        break;
      case 'wp_rmt_entry_attributes':
        $attribute_id = $resAtt->attribute_id;
        $res = $wpdb->get_row('SELECT category as description FROM `wp_rmt_entry_att_categories` where ID=' . $attribute_id);
        $chgRPTins[] = RMTchangeArray($entry, $attribute_id, '', 'Deleted', 'Attribute Deleted (' . addslashes($res->description) . ')');
        break;
      case 'wp_rmt_entry_attn':
        $attn_id = $resAtt->attn_id;
        $res = $wpdb->get_row('SELECT value as description FROM wp_rmt_attn where ID=' . $attn_id);
        $chgRPTins[] = RMTchangeArray($entry, $attn_id, '', 'Deleted', 'Attention Deleted (' . addslashes($res->description) . ')');
        break;
      default:
        break;
    }

    /* Update change report */
    if (!empty($chgRPTins))  updateChangeRPT($chgRPTins);
  }

  /* Retrieves RMT data for an entry */
  public static function rmt_get_entry_data($entry_id) {
    global $wpdb;

    $return_array = array('attributes' => array(), 'resources' => array(), 'attention' => array());

    //gather resource data
    $results = $wpdb->get_results("SELECT er.ID, er.lockBit, er.qty, er.comment, er.user, er.update_Stamp as dateUpdated,
     wp_rmt_resource_categories.category as category, wp_rmt_resource_categories.ID as category_id, 
     type as resource, wp_rmt_resources.id as resource_id, wp_rmt_resources.token as token "
      . "FROM `wp_rmt_entry_resources` er, wp_rmt_resources, wp_rmt_resource_categories "
      . "where er.resource_id = wp_rmt_resources.ID "
      . "and resource_category_id = wp_rmt_resource_categories.ID  "
      . "and er.entry_id = " . $entry_id . " ORDER BY `dateUpdated` DESC");

    foreach ($results as $result) {
      if ($result->user == NULL) {
        $dispUser = 'Initial';
      } elseif ($result->user == 0) {
        $dispUser = 'Payment';
      } else {
        $userInfo = get_userdata($result->user);
        $dispUser = $userInfo->display_name;
      }

      $update_stamp = esc_html(GFCommon::format_date($result->dateUpdated, false, 'm/d/y h:i a'));
      $return_array['resources'][] = array(
        'id'            => $result->ID,
        'lock'          => $result->lockBit,
        'qty'           => $result->qty,
        'comment'       => $result->comment,
        'user'          => $dispUser,
        'category_id'   => $result->category_id,
        'category'      => $result->category,
        'resource_id'   => $result->resource_id,
        'resource'      => $result->resource,
        'token'         => $result->token,
        'last_updated'  => $update_stamp
      );
    }

    //gather attribute data
    $sql = "SELECT wp_rmt_entry_attributes.*, attribute_id, value, " .
      "wp_rmt_entry_att_categories.category as attribute, token
            FROM `wp_rmt_entry_attributes`, wp_rmt_entry_att_categories
            where attribute_id = wp_rmt_entry_att_categories.ID
            and entry_id = " . $entry_id . " order by category";

    $results = $wpdb->get_results($sql);

    foreach ($results as $result) {
      if ($result->user == NULL) {
        $dispUser = 'Initial';
      } elseif ($result->user == 0) {
        $dispUser = 'Payment';
      } else {
        $userInfo = get_userdata($result->user);
        $dispUser = $userInfo->display_name;
      }
      $update_stamp = esc_html(GFCommon::format_date($result->update_stamp, false, 'm/d/y h:i a'));
      $return_array['attributes'][] = array(
        'id'            => $result->ID,
        'lock'          => $result->lockBit,
        'value'         => $result->value,
        'comment'       => $result->comment,
        'user'          => $dispUser,
        'attribute'     => $result->attribute,
        'attribute_id'  => $result->attribute_id,
        'token'         => $result->token,
        'last_updated'  => $update_stamp,
      );
    }

    //gather attention data
    $results = $wpdb->get_results("SELECT wp_rmt_entry_attn.*, wp_rmt_attn.value, token 
                FROM `wp_rmt_entry_attn`, wp_rmt_attn
                where wp_rmt_entry_attn.attn_id = wp_rmt_attn.ID
                and entry_id = " . $entry_id . " order by wp_rmt_attn.value");

    foreach ($results as $result) {
      if ($result->user == NULL) {
        $dispUser = 'Initial';
      } else {
        $userInfo = get_userdata($result->user);
        $dispUser = $userInfo->display_name;
      }

      $update_stamp = esc_html(GFCommon::format_date($result->update_stamp, false, 'm/d/y h:i a'));
      $return_array['attention'][] = array(
        'id'            => $result->ID,
        'attn_id'       => $result->attn_id,
        'attention'     => $result->value,
        'comment'       => $result->comment,
        'user'          => $dispUser,
        'token'         => $result->token,
        'last_updated'  => $update_stamp
      );
    }
    return $return_array;
  }

  /* Retrieves RMT table data */
  public static function rmt_table_data() {
    global $wpdb;

    $return  = array();
    $itemArr = array();
    $typeArr = array();

    //build Item to type drop down array
    $sql = "SELECT wp_rmt_resource_categories.ID as item_id, wp_rmt_resource_categories.category as item, wp_rmt_resources.ID as type_id, wp_rmt_resources.type FROM `wp_rmt_resource_categories` right outer join wp_rmt_resources on wp_rmt_resource_categories.ID= wp_rmt_resources.resource_category_id ORDER BY `wp_rmt_resource_categories`.`category` ASC, type ASC";
    $results = $wpdb->get_results($sql);
    $itemArr = array();
    foreach ($results as $result) {
      if (!isset($itemArr[$result->item_id])) {
        $itemArr[$result->item_id] = $result->item;
      }
      if (!isset($typeArr[$result->item_id][$result->type_id])) {
        $typeArr[$result->item_id][$result->type_id] = $result->type;
      }
    }
    $return['resource_categories'] = $itemArr;
    $return['resources'] = $typeArr;

    //Build Attribute type array
    $attArr = array();

    $sql = "SELECT ID, category FROM wp_rmt_entry_att_categories";
    $results = $wpdb->get_results($sql);

    foreach ($results as $result) {
      $attArr[] = array('key' => $result->ID, 'value' => $result->category);
    }
    $return['attItems'] = $attArr;

    //build attention drop down values
    $attnArr = array();

    $sql = "SELECT ID, value FROM wp_rmt_attn";
    $results = $wpdb->get_results($sql);

    foreach ($results as $result) {
      $attnArr[] = array('key' => $result->ID, 'value' => $result->value);
    }
    $return['attnItems'] = $attnArr;

    return $return;
  }

  /*
   * This function is called when there is an entry update or new entry submission
   * $type - this tells us if this is a new submission or an update to the entry
	*/
  public static function gravityforms_makerInfo($entry, $form) {
    //build/update RMT data
    self::buildRmtData($entry, $form);

    //update/insert into maker tables
    self::updateMakerTables($entry['id']);
  }

  /* Function to add/update the maker data tables for entity/project and maker data
   *
   *  Entity/project and maker data only saved for
   *   - Exhibit
   *   - Presentation
   *   - Performance
   *   - Startup Sponsor
   *   - Sponsor
   *  All other form types are skipped
   */
  public static function updateMakerTables($entryID) {
    global $wpdb;
    $entry    = GFAPI::get_entry($entryID);
    $form_id  = $entry['form_id'];
    $form     = GFAPI::get_form($form_id);

    //exit this function if form type is not an Exhibit, Presentation or Sponsor
    $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');
    if (
      $form_type != 'Exhibit' &&
      $form_type != 'Presentation' &&
      $form_type != 'Performance' &&
      $form_type != 'Workshop' &&
      $form_type != 'Sponsor' &&
      $form_type != 'Startup Sponsor' &&
      $form_type != 'Master'
    ) {
      return;
    }

    //build Maker Data Array
    $data = self::buildMakerData($entry, $form);
    $makerData  = $data['maker'];

    $entityData = $data['entity'];

    $categories = (is_array($entityData['categories']) ? implode(',', $entityData['categories']) : '');

    /*
     * Update Entity Table - wp_mf_entity
     * fields: lead_id, form_id, presentation_title, presentation_type, special_request, OnsitePhone,
     * desc_short, desc_long, project_photo, status, category, faire, mobile_app_discover, form_type, project_video
     */
    $wp_mf_entitysql = "insert into wp_mf_entity (lead_id, form_id, presentation_title, presentation_type, special_request, "
      . "     OnsitePhone, desc_short, desc_long, project_photo, status, category, faire, mobile_app_discover, "
      . "     form_type, project_video,inspiration,last_change_date) "
      . " VALUES ('" . $entryID . "'," . $entityData['form_id'] . ','
      . ' "' . $entityData['project_name']            . '", '
      . ' "' . $entityData['presentation_type']       . '", '
      . ' "' . $entityData['special_request']         . '", '
      . ' "' . $entityData['onsitePhone']             . '", '
      . ' "' . $entityData['public_description']      . '", '
      . ' "' . $entityData['private_description']     . '", '
      . ' "' . $entityData['project_photo']           . '", '
      . ' "' . $entityData['status']                  . '", '
      . ' "' . $categories                            . '", '
      . ' "' . $entityData['faire']                   . '", '
      . '  ' . $entityData['mobile_app_discover']     . ','
      . ' "' . $form_type                             . '", '
      . ' "' . $entityData['project_video']           . '", '
      . ' "' . $entityData['inspiration']             . '", '
      . ' now()'
      . ') '
      . ' ON DUPLICATE KEY UPDATE presentation_title  = "' . $entityData['project_name']            . '", '
      . '                         presentation_type   = "' . $entityData['presentation_type']       . '", '
      . '                         special_request     = "' . $entityData['special_request']         . '", '
      . '                         OnsitePhone         = "' . $entityData['onsitePhone']             . '", '
      . '                         desc_short          = "' . $entityData['public_description']      . '", '
      . '                         desc_long           = "' . $entityData['private_description']     . '", '
      . '                         project_photo       = "' . $entityData['project_photo']           . '", '
      . '                         status              = "' . $entityData['status']                  . '", '
      . '                         category            = "' . $categories . '", '
      . '                         faire               = "' . $entityData['faire']                   . '", '
      . '                         form_id             =  ' . $entityData['form_id']                 . ','
      . '                         mobile_app_discover = "' . $entityData['mobile_app_discover']     . '", '
      . '                         form_type           = "' . $form_type                             . '", '
      . '                         project_video       = "' . $entityData['project_video']           . '", '
      . '                         inspiration         = "' . $entityData['inspiration']             . '", '
      . '                         last_change_date    = now()';
    $wpdb->get_results($wp_mf_entitysql);

    /*  Update Maker Table - wp_mf_maker table
     *    $makerData types - contact, presenter, presenter2-7
     */

    //loop thru
    foreach ($makerData as $type => $typeArray) {
      $firstName = (isset($typeArray['first_name']) ? esc_sql($typeArray['first_name']) : '');
      $lastName  = (isset($typeArray['last_name'])  ? esc_sql($typeArray['last_name'])  : '');
      $email     = (isset($typeArray['email'])      ? esc_sql($typeArray['email'])      : '');

      if ((trim($firstName) == '' && trim($lastName) == '') || trim($email) == '') {
        //don't write the record, no maker here.  Move along
      } else {
        $bio        = (isset($typeArray['bio'])         ? htmlentities($typeArray['bio'])   : '');
        $phone      = (isset($typeArray['phone'])       ? esc_sql($typeArray['phone'])      : '');
        $twitter    = (isset($typeArray['twitter'])     ? esc_sql($typeArray['twitter'])    : '');
        $photo      = (isset($typeArray['photo'])       ? esc_sql($typeArray['photo'])      : '');
        $website    = (isset($typeArray['website'])     ? esc_sql($typeArray['website'])    : '');
        $phone_type = (isset($typeArray['phone_type'])  ? esc_sql($typeArray['phone_type']) : '');
        $age_range  = (isset($typeArray['age_range'])   ? esc_sql($typeArray['age_range'])  : '');
        $city       = (isset($typeArray['city'])        ? esc_sql($typeArray['city'])       : '');
        $state      = (isset($typeArray['state'])       ? esc_sql($typeArray['state'])      : '');
        $country    = (isset($typeArray['country'])     ? esc_sql($typeArray['country'])    : '');
        $zipcode    = (isset($typeArray['zipcode'])     ? esc_sql($typeArray['zipcode'])    : '');
        $address    = (isset($typeArray['address'])     ? esc_sql($typeArray['address'])    : '');
        $address2   = (isset($typeArray['address2'])    ? esc_sql($typeArray['address2'])   : '');
        $role       = (isset($typeArray['role'])        ? esc_sql($typeArray['role'])       : '');

        /*  GUID
         * If this maker is already in the DB - pull the maker_id, else let's create one
         */
        $results = $wpdb->get_results($wpdb->prepare("SELECT maker_id FROM wp_mf_maker WHERE email=%s", $email));
        $guid = ($wpdb->num_rows != 0 ? $guid = $results[0]->maker_id : createGUID($entryID . '-' . $type));
        $wp_mf_makersql = "INSERT INTO `wp_mf_maker`"
          . "             (`First Name`, `Last Name`, `Bio`, `Email`, `phone`, "
          . "              `TWITTER`, `maker_id`, `Photo`, `website`, `phone_type`, "
          . "              `age_range`, `city`, `state`, `country`, `zipcode`, "
          . "              `address`, `address2`, last_change_date) "
          . '  VALUES ("' . $firstName . '","' . $lastName . '","' . $bio . '","' . $email . '","' . $phone . '",'
          . '          "' . $twitter . '","' . $guid . '","' . $photo . '","' . $website . '","' . $phone_type . '",'
          . '          "' . $age_range . '","' . $city . '","' . $state . '","' . $country . '","' . $zipcode . '",'
          . '          "' . $address . '","' . $address2 . '", now())'
          . '  ON DUPLICATE KEY UPDATE maker_id="' . $guid . '", last_change_date=now()';

        //only update non blank fields
        $wp_mf_makersql .= ($firstName  != '' ? ', `First Name` = "' . $firstName   . '"' : ''); //first name
        $wp_mf_makersql .= ($lastName   != '' ? ', `Last Name`  = "' . $lastName    . '"' : ''); //last name
        $wp_mf_makersql .= ($bio        != '' ? ', `Bio`        = "' . $bio         . '"' : ''); //bio
        $wp_mf_makersql .= ($phone      != '' ? ', `phone`      = "' . $phone       . '"' : ''); //phone
        $wp_mf_makersql .= ($twitter    != '' ? ', `TWITTER`    = "' . $twitter     . '"' : ''); //twitter
        $wp_mf_makersql .= ($photo      != '' ? ', `Photo`      = "' . $photo       . '"' : ''); //photo
        $wp_mf_makersql .= ($website    != '' ? ', `website`    = "' . $website     . '"' : ''); //website
        $wp_mf_makersql .= ($phone_type != '' ? ', `phone_type` = "' . $phone_type  . '"' : ''); //phone_type
        $wp_mf_makersql .= ($age_range  != '' ? ', `age_range`  = "' . $age_range   . '"' : ''); //age_range
        $wp_mf_makersql .= ($city       != '' ? ', `city`       = "' . $city        . '"' : ''); //city
        $wp_mf_makersql .= ($state      != '' ? ', `state`      = "' . $state       . '"' : ''); //state
        $wp_mf_makersql .= ($country    != '' ? ', `country`    = "' . $country     . '"' : ''); //country
        $wp_mf_makersql .= ($zipcode    != '' ? ', `zipcode`    = "' . $zipcode     . '"' : ''); //zipcode
        $wp_mf_makersql .= ($address    != '' ? ', `address`    = "' . $address     . '"' : ''); //address
        $wp_mf_makersql .= ($address2   != '' ? ', `address2`   = "' . $address2    . '"' : ''); //address2

        $wpdb->get_results($wp_mf_makersql);

        //build maker to entity table
        //(key is on maker_id, entity_id and maker_type.  if record already exists, no update is needed)
        $wp_mf_maker_to_entity = "INSERT INTO `wp_mf_maker_to_entity` (`maker_id`, `entity_id`, `maker_type`,`maker_role`) "
          . ' VALUES ("' . $guid . '",' . $entryID . ',"' . $type . '", "' . $role . '")  '
          . ' ON DUPLICATE KEY UPDATE maker_id="' . $guid . '", maker_role="' . $role . '";';

        $wpdb->get_results($wp_mf_maker_to_entity);
      }
    }
  }

  //function to build the maker data table to update the wp_mf_maker table
  public static function buildMakerData($lead, $form) {
    global $wpdb;
    $form_type = (isset($form['form_type'])  ? $form['form_type'] : '');

    $entry_id     = $lead['id'];
    $form_id      = $form['id'];

    //      109 - group name    151 - project name
    $project_name = (isset($lead['109']) && trim($lead['109']) != '' ? $lead['109'] : (isset($lead['151']) ? $lead['151'] : ''));

    // Check if this is a group of makers, one maker or multiple makers
    $isGroup    = false; //default to false
    $isOneMaker = true;
    if (isset($lead['105'])  && $lead['105'] != '') {
      $isGroup = (stripos($lead['105'], 'group') !== false || stripos($lead['105'], 'team') !== false ? true : false);
      $isOneMaker = (strpos($lead['105'], 'One')    !== false ? true : false);
    }

    /*
     * Build Maker Array
     */
    $makerArray = array();

    //Contact
    $makerArray['contact'] =
      array(
        'first_name'  => (isset($lead['96.3'])  ? $lead['96.3'] : ''),
        'last_name'   => (isset($lead['96.6'])  ? $lead['96.6'] : ''),
        'bio'         => '',
        'email'       => (isset($lead['98'])    ? $lead['98'] : ''),
        'phone'       => (isset($lead['99'])    ? $lead['99'] : ''),
        'twitter'     => (isset($lead['201'])   ? $lead['201'] : ''),
        'photo'       => '',
        'website'     => '',
        'phone_type'  => (isset($lead['148'])   ? $lead['148'] : ''),
        'age_range'   => '',
        'city'        => (isset($lead['101.3'])   ? $lead['101.3'] : ''),
        'state'       => (isset($lead['101.4'])   ? $lead['101.4'] : ''),
        'country'     => (isset($lead['101.6'])   ? $lead['101.6'] : ''),
        'zipcode'     => (isset($lead['101.5'])   ? $lead['101.5'] : ''),
        'address'     => (isset($lead['101.1'])   ? $lead['101.1'] : ''),
        'address2'    => (isset($lead['101.2'])   ? $lead['101.2'] : ''),
        'role'        => 'contact'
      );

    // Presenter / Maker 1
    if (!$isGroup) {
      /*
       * if this isn't a group we need to have a valid email for the presenter(maker 1) record.
       *    if it is not set, use contact email
       */
      $email = (isset($lead['161']) && $lead['161'] != '' ? $lead['161'] : $lead['98']);
      $makerArray['presenter'] = array(
        'first_name'  => (isset($lead['160.3']) ? $lead['160.3'] : ''),
        'last_name'   => (isset($lead['160.6']) ? $lead['160.6'] : ''),
        'bio'         => (isset($lead['234'])   ? $lead['234'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['185'])   ? $lead['185'] : ''),
        'twitter'     => (isset($lead['201'])   ? $lead['201'] : ''),
        'photo'       => (isset($lead['217'])   ? $lead['217'] : ''),
        'website'     => (isset($lead['209'])   ? $lead['209'] : ''),
        'phone_type'  => (isset($lead['200'])   ? $lead['200'] : ''),
        'age_range'   => (isset($lead['310'])   ? $lead['310'] : ''),
        'city'        => (isset($lead['369.3']) ? $lead['369.3'] : ''),
        'state'       => (isset($lead['369.4']) ? $lead['369.4'] : ''),
        'country'     => (isset($lead['369.6']) ? $lead['369.6'] : ''),
        'zipcode'     => (isset($lead['369.5']) ? $lead['369.5'] : ''),
        'address'     => (isset($lead['369.1']) ? $lead['369.1'] : ''),
        'address2'    => (isset($lead['369.2']) ? $lead['369.2'] : ''),
        'role'        => (isset($lead['443'])   ? $lead['443'] : ''), //not set for performance/presentation
      );
    } else { // if field 105 indicates this is a group,
      //address information not set for group use contact address
      $city        = (isset($lead['101.3']) ? $lead['101.3'] : '');
      $state       = (isset($lead['101.4']) ? $lead['101.4'] : '');
      $country     = (isset($lead['101.6']) ? $lead['101.6'] : '');
      $zipcode     = (isset($lead['101.5']) ? $lead['101.5'] : '');
      $address     = (isset($lead['101.1']) ? $lead['101.1'] : '');
      $address2    = (isset($lead['101.2']) ? $lead['101.2'] : '');

      //  set Presenter/Maker 1 to the group information
      $makerArray['presenter'] = array(
        'first_name'  => $project_name,
        'last_name'   => '',
        'bio'         => (isset($lead['110'])   ? $lead['110'] : ''),
        'email'       => $entry_id . '-group@makermedia.com',
        'phone'       => (isset($lead['99'])    ? $lead['99'] : ''),
        'twitter'     => (isset($lead['322'])   ? $lead['322'] : ''),
        'photo'       => (isset($lead['111'])   ? $lead['111'] : ''),
        'website'     => (isset($lead['112'])   ? $lead['112'] : ''),
        'age_range'   => (isset($lead['309'])   ? $lead['309'] : ''), //not set for performance/presentation
        'phone_type'  => (isset($lead['148'])   ? $lead['148'] : ''),
        'city'        => $city,
        'state'       => $state,
        'country'     => $country,
        'zipcode'     => $zipcode,
        'address'     => $address,
        'address2'    => $address2,
        'role'        => 'group'
      );
    }

    // we need to have at least 1 presenter/maker.  if these fields are empty, pull from the contact info
    if (trim($makerArray['presenter']['first_name']) == '' && trim($makerArray['presenter']['last_name']) == '') {
      //let's try to get the name from the contact info
      $firstName  =  (isset($makerArray['contact']['first_name']) ? esc_sql($makerArray['contact']['first_name']) : '');
      $lastName   =  (isset($makerArray['contact']['last_name'])  ? esc_sql($makerArray['contact']['last_name'])  : '');
    }

    // If sponsor, Set Presenter/Maker 1 name to company name
    if ($form['form_type'] == 'Sponsor') {
      $makerArray['presenter']['first_name'] = htmlentities($project_name);
      $makerArray['presenter']['last_name']  = ' ';
    }

    // only set the below data if the entry is not marked as one maker
    if (!$isOneMaker) {
      //set presenter 2 email, default if blank
      $email = (isset($lead['162']) && $lead['162'] != '' ? $lead['162'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter2@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group2@makermedia.com';
      }

      $makerArray['presenter2'] = array(
        'first_name'  => (isset($lead['158.3']) ? $lead['158.3'] : ''),
        'last_name'   => (isset($lead['158.6']) ? $lead['158.6'] : ''),
        'bio'         => (isset($lead['258'])   ? $lead['258'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['192'])   ? $lead['192'] : ''),
        'twitter'     => (isset($lead['208'])   ? $lead['208'] : ''),
        'photo'       => (isset($lead['224'])   ? $lead['224'] : ''),
        'website'     => (isset($lead['216'])   ? $lead['216'] : ''),
        'phone_type'  => (isset($lead['199'])   ? $lead['199'] : ''),
        'age_range'   => (isset($lead['311'])   ? $lead['311'] : ''),
        'city'        => (isset($lead['370.3']) ? $lead['370.3'] : ''),
        'state'       => (isset($lead['370.4']) ? $lead['370.4'] : ''),
        'country'     => (isset($lead['370.6']) ? $lead['370.6'] : ''),
        'zipcode'     => (isset($lead['370.5']) ? $lead['370.5'] : ''),
        'address'     => (isset($lead['370.1']) ? $lead['370.1'] : ''),
        'address2'    => (isset($lead['370.2']) ? $lead['370.2'] : ''),
        'role'        => (isset($lead['444'])   ? $lead['444'] : '') //not set for performance/presentation
      );

      //set presenter 3 email, default if blank
      $email = (isset($lead['167']) && $lead['167'] != '' ? $lead['167'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter3@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group3@makermedia.com';
      }
      $makerArray['presenter3'] = array(
        'first_name'  => (isset($lead['155.3']) ? $lead['155.3'] : ''),
        'last_name'   => (isset($lead['155.6']) ? $lead['155.6'] : ''),
        'bio'         => (isset($lead['259'])   ? $lead['259'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['190'])   ? $lead['190'] : ''),
        'twitter'     => (isset($lead['207'])   ? $lead['207'] : ''),
        'photo'       => (isset($lead['223'])   ? $lead['223'] : ''),
        'website'     => (isset($lead['215'])   ? $lead['215'] : ''),
        'phone_type'  => (isset($lead['193'])   ? $lead['193'] : ''),
        'age_range'   => (isset($lead['312'])   ? $lead['312'] : ''),
        'city'        => (isset($lead['371.3']) ? $lead['371.3'] : ''),
        'state'       => (isset($lead['371.4']) ? $lead['371.4'] : ''),
        'country'     => (isset($lead['371.6']) ? $lead['371.6'] : ''),
        'zipcode'     => (isset($lead['371.5']) ? $lead['371.5'] : ''),
        'address'     => (isset($lead['371.1']) ? $lead['371.1'] : ''),
        'address2'    => (isset($lead['371.2']) ? $lead['371.2'] : ''),
        'role'        => (isset($lead['445'])   ? $lead['445'] : '') //not set for performance/presentation
      );

      //set presenter 4 email, default if blank
      $email = (isset($lead['166']) && $lead['166'] != '' ? $lead['166'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter4@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group4@makermedia.com';
      }
      $makerArray['presenter4'] = array(
        'first_name'  => (isset($lead['156.3']) ? $lead['156.3'] : ''),
        'last_name'   => (isset($lead['156.6']) ? $lead['156.6'] : ''),
        'bio'         => (isset($lead['260'])   ? $lead['260'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['191'])   ? $lead['191'] : ''),
        'twitter'     => (isset($lead['206'])   ? $lead['206'] : ''),
        'photo'       => (isset($lead['222'])   ? $lead['222'] : ''),
        'website'     => (isset($lead['214'])   ? $lead['214'] : ''),
        'phone_type'  => (isset($lead['198'])   ? $lead['198'] : ''),
        'age_range'   => (isset($lead['313'])   ? $lead['313'] : ''),
        'city'        => (isset($lead['372.3']) ? $lead['372.3'] : ''),
        'state'       => (isset($lead['372.4']) ? $lead['372.4'] : ''),
        'country'     => (isset($lead['372.6']) ? $lead['372.6'] : ''),
        'zipcode'     => (isset($lead['372.5']) ? $lead['372.5'] : ''),
        'address'     => (isset($lead['372.1']) ? $lead['372.1'] : ''),
        'address2'    => (isset($lead['372.2']) ? $lead['372.2'] : ''),
        'role'        => (isset($lead['446'])   ? $lead['446'] : '') //not set for performance/presentation
      );

      //set presenter 5 email, default if blank
      $email = (isset($lead['165']) && $lead['165'] != '' ? $lead['165'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter5@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group5@makermedia.com';
      }
      $makerArray['presenter5'] = array(
        'first_name'  => (isset($lead['157.3']) ? $lead['157.3'] : ''),
        'last_name'   => (isset($lead['157.6']) ? $lead['157.6'] : ''),
        'bio'         => (isset($lead['261'])   ? $lead['261'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['189'])   ? $lead['189'] : ''),
        'twitter'     => (isset($lead['205'])   ? $lead['205'] : ''),
        'photo'       => (isset($lead['220'])   ? $lead['220'] : ''),
        'website'     => (isset($lead['213'])   ? $lead['213'] : ''),
        'phone_type'  => (isset($lead['195'])   ? $lead['195'] : ''),
        'age_range'   => (isset($lead['314'])   ? $lead['314'] : ''),
        'city'        => (isset($lead['373.3']) ? $lead['373.3'] : ''),
        'state'       => (isset($lead['373.4']) ? $lead['373.4'] : ''),
        'country'     => (isset($lead['373.6']) ? $lead['373.6'] : ''),
        'zipcode'     => (isset($lead['373.5']) ? $lead['373.5'] : ''),
        'address'     => (isset($lead['373.1']) ? $lead['373.1'] : ''),
        'address2'    => (isset($lead['373.2']) ? $lead['373.2'] : ''),
        'role'        => (isset($lead['447'])   ? $lead['447'] : '') //not set for performance/presentation
      );

      //set presenter 6 email, default if blank
      $email = (isset($lead['164']) && $lead['164'] != '' ? $lead['164'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter6@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group6@makermedia.com';
      }
      $makerArray['presenter6'] = array(
        'first_name'  => (isset($lead['159.3']) ? $lead['159.3'] : ''),
        'last_name'   => (isset($lead['159.6']) ? $lead['159.6'] : ''),
        'bio'         => (isset($lead['262'])   ? $lead['262'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['188'])   ? $lead['188'] : ''),
        'twitter'     => (isset($lead['204'])   ? $lead['204'] : ''),
        'photo'       => (isset($lead['221'])   ? $lead['221'] : ''),
        'website'     => (isset($lead['211'])   ? $lead['211'] : ''),
        'phone_type'  => (isset($lead['197'])   ? $lead['197'] : ''),
        'age_range'   => (isset($lead['315'])   ? $lead['315'] : ''),
        'city'        => (isset($lead['374.3']) ? $lead['374.3'] : ''),
        'state'       => (isset($lead['374.4']) ? $lead['374.4'] : ''),
        'country'     => (isset($lead['374.6']) ? $lead['374.6'] : ''),
        'zipcode'     => (isset($lead['374.5']) ? $lead['374.5'] : ''),
        'address'     => (isset($lead['374.1']) ? $lead['374.1'] : ''),
        'address2'    => (isset($lead['374.2']) ? $lead['374.2'] : ''),
        'role'        => (isset($lead['448'])   ? $lead['448'] : '') //not set for performance/presentation
      );

      //set presenter 7 email, default if blank
      $email = (isset($lead['163']) && $lead['163'] != '' ? $lead['163'] : '');
      if (!$isGroup && $email == '') {
        $email = $entry_id . '-presenter7@makermedia.com';
      } elseif ($email == '') {
        $email = $entry_id . '-group7@makermedia.com';
      }
      $makerArray['presenter7'] = array(
        'first_name'  => (isset($lead['154.3']) ? $lead['154.3'] : ''),
        'last_name'   => (isset($lead['154.6']) ? $lead['154.6'] : ''),
        'bio'         => (isset($lead['263'])   ? $lead['263'] : ''),
        'email'       => $email,
        'phone'       => (isset($lead['187'])   ? $lead['187'] : ''),
        'twitter'     => (isset($lead['203'])   ? $lead['203'] : ''),
        'photo'       => (isset($lead['219'])   ? $lead['219'] : ''),
        'website'     => (isset($lead['212'])   ? $lead['212'] : ''),
        'phone_type'  => (isset($lead['196'])   ? $lead['196'] : ''),
        'age_range'   => (isset($lead['316'])   ? $lead['316'] : ''),
        'city'        => (isset($lead['375.3']) ? $lead['375.3'] : ''),
        'state'       => (isset($lead['375.4']) ? $lead['375.4'] : ''),
        'country'     => (isset($lead['375.6']) ? $lead['375.6'] : ''),
        'zipcode'     => (isset($lead['375.5']) ? $lead['375.5'] : ''),
        'address'     => (isset($lead['375.1']) ? $lead['375.1'] : ''),
        'address2'    => (isset($lead['375.2']) ? $lead['375.2'] : ''),
        'role'        => (isset($lead['449'])   ? $lead['449'] : '') //not set for performance/presentation
      );
    }

    /*
     * set entity information
     */
    $leadCategory = array();
    $MAD          = 0;

    //Categories (current fields in use)
    foreach ($lead as $leadKey => $leadValue) {
      if (trim($leadValue != '')) {
        //4 additional categories
        $pos = strpos($leadKey, '321');
        if ($pos !== false) {
          $leadCategory[] = $leadValue;
        }
        //main catgory
        $pos = strpos($leadKey, '320');
        if ($pos !== false) {
          $leadCategory[] = $leadValue;
        }
        //check the flag field 304
        $pos = strpos($leadKey, '304');
        if ($pos !== false) {
          if ($leadValue == 'Mobile App Discover')  $MAD = 1;
        }
      }
    }

    //verify we only have unique categories
    $leadCategory = array_unique($leadCategory);

    //determine faire
    $faire = $wpdb->get_var('select faire from wp_mf_faire where FIND_IN_SET (' . $form_id . ', wp_mf_faire.form_ids)> 0');

    // NOTE (ts): For 'Workshop' update... this may be the spot where the image is set... TBD

    //default project photo to field 22. 
    $project_photo = (isset($lead['22']) ? $lead['22'] : '');

    //for BA24, the primary photo was changed to a multi image to allow cropping.
    $field = gfapi::get_field($form_id, 22);
    if ($field['multipleFiles']) {
      $value = json_decode($project_photo);

      //if the array is empty, set this back to blank
      if (is_array($value) && !empty($value)) {
        $project_photo = $value[0];
      } else {
        $project_photo = '';
      }
    }

    //if the main project photo isn't set but the photo gallery is, use the first image in the photo gallery
    if ($project_photo == '') {
      // this returns an array of image urls from the additional images field
      $project_gallery = (isset($lead['878']) ? json_decode($lead['878']) : '');
      if (is_array($project_gallery)) {
        $project_photo = $project_gallery[0];
      }
    }


    //if this is a Presentation Form AND the presenter photo is set, override
    if ($form_type == 'Presentation' && isset($makerArray['presenter']['photo']) && $makerArray['presenter']['photo']) {
      $project_photo = $makerArray['presenter']['photo'];
    }

    //if the entry status is active, use field 303 as the status, else use entry status
    if ($lead['status'] == 'active') {
      $status = (isset($lead['303']) ? htmlentities($lead['303']) : '');
    } else {
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
        'project_video'       => (isset($lead['32'])  ? htmlentities($lead['32'])  : ''),
        'status'              => $status,
        'categories'          => $leadCategory,
        'faire'               => $faire,
        'mobile_app_discover' => $MAD,
        'form_id'             => $form_id,
        'inspiration'         => (isset($lead['287'])  ? htmlentities($lead['287'])  : ''),
      );
    $return = array('maker' => $makerArray, 'entity' => $entityArray);
    return $return;
  }
}

function RMTchangeArray($entry, $field_id, $field_before, $field_after, $fieldLabel) {
  $form = GFAPI::get_form($entry['form_id']);
  $form_type = $form['form_type'];

  //find current user
  $current_user = wp_get_current_user();
  $user = $current_user->ID;

  //If Payment form, set the user to show the as a payment         
  if ($form_type == 'Payment') $user = 0;

  $return = array(
    'user_id'           => $user,
    'lead_id'           => $entry['id'],
    'form_id'           => $entry['form_id'],
    'field_id'          => $field_id,
    'field_before'      => $field_before,
    'field_after'       => $field_after,
    'fieldLabel'        => $fieldLabel,
    'status_at_update'  => $entry['303']
  );
  return $return;
}

function findFieldData($var, $entry) {
  //check if we need to set an entry field as the value
  $pos = strpos($var, '{');
  while ($pos !== false) {
    $endPos = strpos($var, '}');
    $field_id  = substr($var, $pos + 1, $endPos - $pos - 1);
    $req_field = substr($var, $pos, $endPos - $pos + 1);
    if (isset($entry[$field_id])) {
      //if the field is an array, create a comma separated list
      $fieldData = (is_array($entry[$field_id]) ? implode(',', $entry[$field_id]) : $entry[$field_id]);
    } else {
      $fieldData = '';
    }
    $var = str_replace($req_field, $fieldData, $var);
    $pos = strpos($var, '{');
  }
  return $var;
}
