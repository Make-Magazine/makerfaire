<?php

/*
  ajax to populate resource management table
 */

require_once 'config.php';

$json = file_get_contents('php://input');
$obj = json_decode($json);

$type = (isset($obj->type) ? $obj->type : '');
$table = (isset($obj->table) ? $obj->table : '');
$formSelect = (isset($obj->formSelect) ? $obj->formSelect : '');
$formType = (isset($obj->formType) ? $obj->formType : '');
$selectedFields = (isset($obj->selectedFields) ? $obj->selectedFields : '');
$rmtData = (isset($obj->rmtData) ? $obj->rmtData : '');
$location = (isset($obj->location) ? $obj->location : false);
$payment = (isset($obj->payments) ? $obj->payments : false);
$faire = (isset($obj->faire) ? $obj->faire : '');
$status = (isset($obj->status) ? $obj->status : '');

if ($type != '') {
   if ($type == "tableData") {
      if ($table == 'formData') {
         getBuildRptData();
      } elseif ($table == 'wp_mf_entity_tasks') {
         pullEntityTasks($formSelect);
      } else {
         //build report data
         retrieveRptData($table, $faire);
      }
   } elseif ($type == "customRpt") {
      if (($formSelect != '' || $formType != '') && $selectedFields != '') {
         cannedRpt($obj);
      } else {
         invalidRequest('Error: Form or Fields not selected');
      }
   } elseif ($type == "ent2resource") {
      ent2resource($table, $faire, $formType);
   } elseif ($type == 'paymentRpt') {
      paymentRpt($table, $faire);
   } else {
      invalidRequest('Invalid Request type');
   }
} else {
   invalidRequest('Request Type Not Sent');
}

/* Build your own report function */

function cannedRpt() {
   global $wpdb;
   global $obj;
   $useFormSC   = (isset($obj->useFormSC) ? $obj->useFormSC : false);
   $formSelect  = (isset($obj->formSelect) ? $obj->formSelect : array());
   $formTypeArr = (isset($obj->formType) ? $obj->formType : array());
   $faire       = (isset($obj->faire) ? $obj->faire : '');

   $dispFormID    = (isset($obj->dispFormID) ? $obj->dispFormID : false);
   $formTypeLabel = (isset($obj->formTypeLabel) ? $obj->formTypeLabel : ($useFormSC ? "TYPE" : 'Form Type'));
   $entryIDLabel  = (isset($obj->entryIDLabel) ? $obj->entryIDLabel : ($useFormSC ? 'ENTRY ID' : 'Entry Id'));

   $orderBy          = (isset($obj->orderBy) ? $obj->orderBy : '');
   $selectedFields   = (isset($obj->selectedFields) ? $obj->selectedFields : array());
   $rmtData          = (isset($obj->rmtData) ? $obj->rmtData : array());
   $location         = (isset($obj->location) ? $obj->location : false);
   $tickets          = (isset($obj->tickets) ? $obj->tickets : false);
   $payment          = (isset($obj->payments) ? $obj->payments : false);

   $entryIDorder  = (isset($obj->entryIDorder) ? $obj->entryIDorder : 10);
   $formIDorder   = (isset($obj->formIDorder) ? $obj->formIDorder : 20);
   $locationOrder = (isset($obj->locationOrder) ? $obj->locationOrder : 30);
   $ticketsOrder  = (isset($obj->ticketsOrder) ? $obj->ticketsOrder : 70);
   $formTypeorder = (isset($obj->formTypeorder) ? $obj->formTypeorder : 40);
   $paymentOrder  = (isset($obj->paymentOrder) ? $obj->paymentOrder : 50);
   $CMOrder       = (isset($obj->CMOrder) ? $obj->CMOrder : 60);

   $forms      = implode(",", $formSelect);
   $formTypes  = implode("', '", $formTypeArr);
   if (!empty($formTypes))
      $formTypes = "'" . $formTypes . "'";

   /* passed variables
     formSelect - an array of specific form id's to pull data from
     formType - form types to return.
     values - "Exhibit","Performance","Startup Sponsor","Sponsor","Show Management", "All"
     faire - The ID of the faire to pull data from
     status - What entry status to return
     values - an array of statuses to return or all to return all statuses
     selectedFields - an array of a list of fields to return
     id, label, choices, type, inputs, allValues (?values to return?), dispOrder, exact
     rmtData - an array of RMT data to return
     keys - resource, attribute, attention, meta
     contains an array of data to return
     array values - id, value, checked, aggregated
     location: whether or not to return location information. valid values - true/false
    */

   $data['columnDefs'] = array();
   $data['columnDefs'][] = array('field' => 'entry_id', 'displayName' => $entryIDLabel, 'displayOrder' => $entryIDorder);

   $visible = $dispFormID;
   $data['columnDefs'][] = array('field' => 'form_id', 'visible' => $visible, 'displayOrder' => $formIDorder);
   $data['columnDefs'][] = array('field' => 'form_type', 'displayName' => $formTypeLabel, 'displayOrder' => $formTypeorder);

   //pull all entries based on formSelect, faire, and status
   //question: does wp_mf_entity have the information i need for all forms?? non exhibits missing information?
   //build array of requested fields
   $fieldSQL = ''; //sql to pull field_numbers (called meta_key)
   $fieldIDarr = array(); //unique array of field ID's
   $fieldArr = array(); //array of field data keyed by field id
   $fieldIDArr["376"] = (object) array("id" => "376", "label" => "CM Ind", "choices" => "all", "type" => "radio", "order" => $CMOrder);
   $combineFields = array();
   $fieldQuery = array(" meta_key like '376' ");
   $acceptedOnly = true;

   //build list of categories
   $categories = get_categories(array('taxonomy' => 'makerfaire_category', 'hide_empty' => false));
   foreach ($categories as $category) {
      $catCross[$category->term_id] = $category->name;
   }

   $exactCriteria = array();

   //loop thru selected fields and build sql for entry details and array of requested fields
   foreach ($selectedFields as $selFields) {
      $selFieldsID = (string) $selFields->id;
      //build wp_gf_entry_meta query
      if ($selFields->type == 'name') {
         //for name field
         foreach ($selFields->inputs as $choice) {
            $combineFields[$selFieldsID][] = $choice->id;

            //set criteria for this field id
            $fieldIDArr[$choice->id] = $selFields;
         }

         //return all selected options
         $fieldQuery[] = " meta_key like '" . $selFieldsID . ".%' ";
      } elseif ($selFields->type == 'radio' || $selFields->type == 'select' || $selFields->type == 'checkbox') {
         if ($selFields->choices == 'all' && $selFields->type == 'checkbox') {
            $fieldQuery[] = " meta_key like '" . $selFieldsID . ".%' ";
            $fieldIDArr[$selFieldsID] = $selFields; //search for all values
         } else {
            $fieldQuery[] = " meta_key like '" . $selFieldsID . "' ";
            $fieldIDArr[$selFieldsID][] = $selFields; //search for specific values
         }
      } else { //text/textarea
         $fieldQuery[] = " meta_key like '" . $selFieldsID . "' ";
         //set criteria for this field id
         $fieldIDArr[$selFieldsID] = $selFields;
      }

      if ($selFieldsID == '151' && !isset($selFields->order))
         $selFields->order = 25;
      //add requested field to columns
      if (isset($selFields->hide) && $selFields->hide == true) {
         //don't add this field to display
         $data['columnDefs'][$selFieldsID] = array('field' => 'field_' . str_replace('.', '_', $selFieldsID),
             'displayName' => $selFields->label,
             'type' => 'string',
             'visible' => false,
             'displayOrder' => (isset($selFields->order) ? $selFields->order : 9999));
      } else {
         $data['columnDefs'][$selFieldsID] = array('field' => 'field_' . str_replace('.', '_', $selFieldsID),
             'displayName' => $selFields->label,
             'type' => 'string',
             'displayOrder' => (isset($selFields->order) ? $selFields->order : 9999));
      }

      if (isset($selFields->exact) && $selFields->exact) {
         $exactCriteria[$selFieldsID] = $selFields->choices;
      }
   }

   $fieldSQL = implode(" or ", $fieldQuery);

   // After setting up requested field information, Pull entries based on specified criteria
   /* Note: form type is not set on entries prior to BA16 */
   $sql = "SELECT  wp_gf_entry.id as lead_id, wp_gf_entry.form_id
          FROM    wp_gf_entry  "
           . (!empty($faire) ? ' JOIN  wp_mf_faire on wp_mf_faire.ID  =' . $faire : '')
           . " where wp_gf_entry.status = 'active'"
           . (!empty($faire) ? " AND FIND_IN_SET (`wp_gf_entry`.`form_id`,wp_mf_faire.form_ids)> 0" : '')
           . (!empty($forms) ? " AND wp_gf_entry.form_id in(" . $forms . ")" : '');

   $entries = $wpdb->get_results($sql, ARRAY_A);
   $entryData = array();

   //loop thru entries
   foreach ($entries as $entry) {
      $lead_id = $entry['lead_id'];
      $passCriteria = true;
      $fieldData = array();

      //Are specific detail fields requested?
      if (!empty($fieldSQL)) {
         //pull entry specifc detail based on requested fields
         $detailSQL = "SELECT detail.entry_id as lead_id, detail.form_id, detail.meta_key, detail.meta_value as value
                      FROM wp_gf_entry_meta detail"
                 . " where detail.entry_id = $lead_id "
                 . " and ($fieldSQL) "
                 . " ORDER BY detail.entry_id asc, detail.meta_key asc";

         $details = $wpdb->get_results($detailSQL, ARRAY_A);
         $cmInd = '';
         foreach ($details as $detail) {
            $passCriteria = true;
            if ($detail['meta_key'] === "376") {
               $cmInd = $detail['value'];
            }

            //field 320 snd 302 is stored as category number, use cross reference to find text value
            $value = $detail['value'];
            if ($detail['meta_key'] === "320" || strpos($detail['meta_key'], '321.') !== false || strpos($detail['meta_key'], '302.') !== false) {
               $value = get_CPT_name($value);
            }
            $value = stripslashes(convert_smart_quotes(htmlspecialchars_decode($value)));

            //check if we pulled by specific field id or if this was a request for all values
            $fieldID = $detail['meta_key'];

            //remove everything after the period
            $basefieldID = (strpos($fieldID, ".") ? substr($fieldID, 0, strpos($fieldID, ".")) : $fieldID);

            if (isset($fieldIDArr[$fieldID])) {
               $fieldCritArr = $fieldIDArr[$fieldID];
            } else {//let's look for the base id
               $fieldCritArr = (isset($fieldIDArr[$basefieldID]) ? $fieldIDArr[$basefieldID] : '');
            }

            // radio and select options
            if (is_array($fieldCritArr)) {
               //radio and select boxes must match one of the passed values
               foreach ($fieldCritArr as $fieldCriteria) {
                  if ($fieldCriteria->type === 'radio' || $fieldCriteria->type === 'select') { //check value
                     //default to failing criteria
                     $passCriteria = false;
                     if ($fieldCriteria->choices === 'all') {
                        $passCriteria = true;
                        break;
                     } elseif ($fieldCriteria->choices == $value) {
                        $passCriteria = true;
                        break;
                     }
                  }
               }
            } else {
               if(isset($fieldCritArr->type) && isset($fieldCritArr->choices)){                                    
                  if ($fieldCritArr->type === 'checkbox' && $fieldCritArr->choices === 'all') {
                     $fieldID = $basefieldID;
                     // echo 'field key is '.$fieldKey;
                  }
               }
            }
            if (!$passCriteria) {
               //exit detail loop
               break;
            }
            //build output for field data - format is field_55_4 for field id 55.4
            $fieldKey = 'field_' . str_replace('.', '_', $fieldID);
            $fieldData[$fieldKey] = (isset($fieldData[$fieldKey]) ? $fieldData[$fieldKey] . " \r" : '') . $value;
         }
//var_dump($fieldData);
         if (!empty($exactCriteria)) {
            //exclude exact fields if they do not match
            //  (doing this outside of the details loop as the requested field may not be set
            foreach ($exactCriteria as $exact => $criteria) {
               $data2Test = (isset($fieldData['field_' . $exact]) ? $fieldData['field_' . $exact] : '');
               if ($data2Test != $criteria) {
                  $passCriteria = false;
               }
            }
         }

         //combine name fileds
         foreach ($combineFields as $combFieldID => $combFieldArr) {
            $combinedField = '';
            foreach ($combFieldArr as $combField) {
               if (isset($fieldData['field_' . str_replace('.', '_', $combField)])) {
                  if ($combField != '')
                     $combinedField .= ' ' . $fieldData['field_' . str_replace('.', '_', $combField)];
                  unset($fieldData['field_' . str_replace('.', '_', $combField)]);
               }
            }
            $fieldData['field_' . $combFieldID] = trim($combinedField);
         }
         $form_type = '';

         //record prior to BA16
         if ($form_type == '') {
            $formPull = GFAPI::get_form($entry['form_id']);
            $form_type = (isset($formPull['form_type']) ? $formPull['form_type'] : '');
         }

         //if certain form types were selected, only return those form types
         if (!empty($formTypeArr)) {
            if (in_array($form_type, $formTypeArr)) {
               //continue with this record
            } else {
               $passCriteria = false; //skip this record
            }
         }

         //translate form type into shortcodes
         //if($useFormSC){
         switch ($form_type) {
            case 'Show Management':
               $form_type = 'SHOW';
               break;
            case 'Exhibit':
               $form_type = 'MAK';
               if ($cmInd == 'Yes') {
                  $form_type = 'CM';
               }
               break;
            case 'Sponsor':
               $form_type = 'SPR';
               break;
            case 'Startup Sponsor':
               $form_type = 'STAR';
               break;
            case 'Performance':
               $form_type = 'PERF';
               break;
         }
         //}
         //add data to array
         if ($passCriteria) {
            $colDefs = array();
            //pull rmt data and location information
            if (!empty($rmtData)) {
               $rmtRetData = pullRmtData($rmtData, $lead_id, $useFormSC);
               $fieldData = array_merge($fieldData, $rmtRetData['data']);
               $colDefs = array_merge($colDefs, $rmtRetData['colDefs']);
            }
            if ($location) {
               $locRetData = pullLocData($lead_id, $useFormSC, $locationOrder);
               $fieldData = array_merge($fieldData, $locRetData['data']);
               $colDefs = array_merge($colDefs, $locRetData['colDefs']);
            }
            if ($tickets) {
               $tickRetData = pullTickData($lead_id, $useFormSC, $ticketsOrder);
               $fieldData = array_merge($fieldData, $tickRetData['data']);
               $colDefs = array_merge($colDefs, $tickRetData['colDefs']);
            }
            if ($payment) {
               $PayRetData = pullPayData($lead_id, $paymentOrder);
               $fieldData = array_merge($fieldData, $PayRetData['data']);
               $colDefs = array_merge($colDefs, $PayRetData['colDefs']);
            }
            $entryData[$lead_id] = $fieldData;
            $entryData[$lead_id]['entry_id'] = $lead_id;
            $entryData[$lead_id]['form_id'] = $entry['form_id'];
            $entryData[$lead_id]['form_type'] = $form_type;

            //merge in RMT data and location info
            $data['columnDefs'] = array_merge($data['columnDefs'], $colDefs);
         }
      }
   }

   /*
    * after we have pulled data from database
    *   compare against selected criteria
    */

   //return data
   $data['data'] = array_values($entryData);

   //sort columns by display order
   usort(
           $data['columnDefs'], function($a, $b) {
      $result = 0;
      if ($a["displayOrder"] > $b["displayOrder"]) {
         $result = 1;
      } else if ($a["displayOrder"] < $b["displayOrder"]) {
         $result = -1;
      }
      return $result;
   }
   );
   //reindex columnDefs as the grid will blow up if the indexes aren't in order
   $data['columnDefs'] = array_values($data['columnDefs']);

   echo json_encode($data);
   exit;
}

//end function

function pullRmtData($rmtData, $entryID, $useFormSC) {
   global $wpdb;

   //returned data and column definitions
   $return = array();
   $return['data'] = array();
   $return['colDefs'] = array();

   $colDefs2Sort = array();
   $incComments = false; //display comments in a separate field (set at individual
   $aggregated = false; //aggregate comments with value;
   $comments = (isset($rmtData->comments) ? $rmtData->comments : false); //display comments in a separate field (set overall for all RMT data)

   /*
    * Process the requested resources
    * Input:       array of requested resources
    *
    * Values for array:
    * id -         either the id or the requested resource or 'all'
    *              If all is set, return all attributes set for this entry in one column separated by a line break
    * value -      what label should be used for the label row of the report
    * order -      what numeric order should this resource be placed in the report
    * aggregated - return resource comments after the value of the resource surrounded by ()
    * comments -   include resource comments in a separate column
    */

   $value = '';
   //process resources
   if (isset($rmtData->resource) && !empty($rmtData->resource)) {
      foreach ($rmtData->resource as $selRMT) {
         $displayOrder = (isset($selRMT->order) ? $selRMT->order : 100);  //order in returned data where RMT data displays
         $incComments = (isset($selRMT->comments) ? $selRMT->comments : false); //display commments in separate column
         $aggregated = (isset($selRMT->aggregated) ? $selRMT->aggregated : false); //aggregate comments with value
         $displayLabel = (isset($selRMT->value) ? $selRMT->value : '');

         //set sql
         if ($selRMT->id == 'all') {
            //return all attributes set for this entry
            $sql = 'SELECT  resource_id, qty, concat(type, " ", wp_rmt_resource_categories.category) as type, comment '
                    . ' FROM    `wp_rmt_entry_resources`, wp_rmt_resources, wp_rmt_resource_categories '
                    . ' WHERE   resource_id = wp_rmt_resources.ID and'
                    . '         resource_category_id = wp_rmt_resource_categories.ID and'
                    . '         entry_id =' . $entryID;
         } else {
            $sql = 'SELECT  qty,type,comment, token,resource_id '
                    . ' FROM   `wp_rmt_entry_resources`, wp_rmt_resources '
                    . ' where   resource_id = wp_rmt_resources.ID and'
                    . '         resource_category_id = ' . $selRMT->id . ' and'
                    . '         entry_id =' . $entryID;
         }

         //loop thru data
         $resources = $wpdb->get_results($sql, ARRAY_A);
         $entryValue = array();
         $entryComment = array();
         foreach ($resources as $resource) {
            $type = $resource['qty'] . ' : ' . $resource['type'];
            $value = ($useFormSC ? formSC($type) : $type); //find and replace certain data in the value
            //add comment to the value if aggregated and not blank
            if ($aggregated && $resource['comment'] != '') {
               $value .= " (" . $resource['comment'] . ")";
            }
            $entryValue[] = $value;
            $entryComment[] = $resource['comment'];
         }

         //set return data and column definitions
         $return['colDefs']['res_' . $selRMT->id] = array('field' => 'res_' . str_replace('.', '_', $selRMT->id),
             'displayName' => $selRMT->value,
             'displayOrder' => $displayOrder);
         $return['data']['res_' . $selRMT->id] = implode("\r", $entryValue);  //separate each resource with a line break in the csv file
         //set comments column if requested
         if ($incComments) {
            $return['colDefs']['res_' . $selRMT->id . '_comment'] = array('field' => 'res_' . str_replace('.', '_', $selRMT->id) . '_comment',
                'displayName' => $selRMT->value . ' - comment',
                'displayOrder' => $displayOrder + .2);
            $return['data']['res_' . $selRMT->id . '_comment'] = implode("\r", $entryComment);
         }
      } //end foreach $rmtData->resource loop
   } //end resources
   //process attribute
   if (isset($rmtData->attribute) && !empty($rmtData->attribute)) {
      foreach ($rmtData->attribute as $selRMT) {
         if ($selRMT->id != 'all') {
            $sql = 'select value,comment '
                    . 'from wp_rmt_entry_attributes '
                    . 'where entry_id =' . $entryID . ' and attribute_id=' . $selRMT->id;
         } else {
            $sql = 'select concat(category," ",value) as value,comment '
                    . 'from wp_rmt_entry_attributes, wp_rmt_entry_att_categories '
                    . 'where entry_id =' . $entryID
                    . ' and attribute_id = wp_rmt_entry_att_categories.ID';
         }

         //set variables with input
         $displayOrder = (isset($selRMT->order) ? $selRMT->order : 200);  //order in returned data where RMT data displays
         $incComments = (isset($selRMT->comments) ? $selRMT->comments : false); //display commments in separate column
         $aggregated = (isset($selRMT->aggregated) ? $selRMT->aggregated : false); //aggregate comments with value
         $displayLabel = (isset($selRMT->value) ? $selRMT->value : '');

         //loop thru data
         $attributes = $wpdb->get_results($sql, ARRAY_A);
         $entryValue = array();
         $entryComment = array();
         
         foreach ($attributes as $attribute) {
            $value = ($useFormSC ? formSC($attribute['value']) : $attribute['value']); //find and replace certain data in the value
            //add comment to the value if aggregated and not blank
            if ($aggregated && $attribute['comment'] != '') {
               $value .= ' : ' . " (" . $attribute['comment'] . ")";
            }
            $entryValue[] = $value;
            $entryComment[] = $attribute['comment'];  //set entry comment to display in separate column
         }

         //new
         //set return data and column definitions
         $return['colDefs']['att_' . $selRMT->id] = array('field' => 'att_' . str_replace('.', '_', $selRMT->id),
             'displayName' => $selRMT->value,
             'displayOrder' => $displayOrder);
         $return['data']['att_' . $selRMT->id] = implode("\r", $entryValue);  //separate each resource with a line break in the csv file
         //set comments column if requested
         if ($incComments) {
            $return['colDefs']['att_' . $selRMT->id . '_comment'] = array('field' => 'att_' . str_replace('.', '_', $selRMT->id) . '_comment',
                'displayName' => $selRMT->value . ' - comment',
                'displayOrder' => $displayOrder + .2);
            //$return['data']['att_' . $selRMT->id . '_comment'] = $attribute['comment'];
            $return['data']['att_' . $selRMT->id . '_comment'] = implode("\r", $entryComment);
         }
      } //end $rmtData->attribute loop
   } //end attribute data
   //process attention
   if (isset($rmtData->attention) && !empty($rmtData->attention)) {
      foreach ($rmtData->attention as $selRMT) {
         if ($selRMT->id != 'all') {
            $sql = 'select comment from wp_rmt_entry_attn where entry_id =' . $entryID . ' and attn_id=' . $selRMT->id;
         } else {
            $sql = 'select concat(wp_rmt_attn.value," ",comment) as comment from wp_rmt_entry_attn,wp_rmt_attn where '
                    . ' entry_id =' . $entryID
                    . ' and attn_id= wp_rmt_attn.ID';
         }
         //loop thru data
         $attentions = $wpdb->get_results($sql, ARRAY_A);
         $entryAttn = array();

         foreach ($attentions as $attention) {
            $entryAttn[] = $attention['comment'];
         }
         $return['colDefs']['attn_' . $selRMT->id] = array('field' => 'attn_' . str_replace('.', '_', $selRMT->id),
             'displayName' => $selRMT->value,
             'displayOrder' => (isset($selRMT->order) ? $selRMT->order : 300));
         $return['data']['attn_' . $selRMT->id] = implode(', ', $entryAttn);
      }
   }

   //process meta
   if (isset($rmtData->meta) && !empty($rmtData->meta)) {
      //build array of requested RMT data
      $reqArr = array();
      $reqMetaArr = array();

      foreach ($rmtData->meta as $reqData) {
         $reqArr[] = $reqData->id;
         $reqMetaArr[$reqData->id] = $reqData;
      }

      //meta id's are alpha
      $reqIDs = implode("', '", $reqArr);
      if (!empty($reqIDs))
         $reqIDs = "'" . $reqIDs . "'";

      $sql = "SELECT meta_key,meta_value FROM `wp_gf_entry_meta` where meta_key in(" . $reqIDs . ") and entry_id = " . $entryID;

      //loop thru data
      $metas = $wpdb->get_results($sql, ARRAY_A);
      $entryMeta = array();

      foreach ($metas as $meta) {
         $selRMT = $reqMetaArr[$meta['meta_key']];
         $return['colDefs']['meta_' . $selRMT->id] = array('field' => 'meta_' . str_replace('.', '_', $selRMT->id),
             'displayName' => $selRMT->value,
             'displayOrder' => (isset($selRMT->order) ? $selRMT->order : 400)
         );
         $return['data']['meta_' . $selRMT->id] = $meta['meta_value'];
      }
   }

   //sort $colDefs2Sort array by displayName
   uasort($colDefs2Sort, function($a, $b) {
      return strcmp($a["displayName"], $b["displayName"]);
   });
   $return['colDefs'] = array_merge($return['colDefs'], $colDefs2Sort);
   return $return;
}

/* Pull Location information */

function pullLocData($entryID, $useFormSC = false, $locationOrder = 30) {
   global $wpdb;
   //global $useFormSC;
   $return = array();
   $return['data'] = array();
   $return['colDefs'] = array();

   //schedule information
   if ($entryID != '') {
      //get scheduling information for this lead
      $sql = "SELECT  area.area,subarea.subarea,subarea.nicename, location.location
            FROM wp_mf_location location,
                    wp_mf_faire_subarea subarea,
                    wp_mf_faire_area area
            where       location.entry_id   = $entryID
                    and subarea.id          = location.subarea_id
                    and area.id             = subarea.area_id";

      $results = $wpdb->get_results($sql);
      if ($wpdb->num_rows > 0) {
         $locArr = array();
         foreach ($results as $row) {
            //create an array of assigned locations in case there is more than one (due to exhibts being scheduled on stages)
            $subarea = ($row->nicename != '' && $row->nicename != '' ? $row->nicename : $row->subarea);

            $area = $row->area;
            if ($useFormSC) {
               $area = str_replace(' ', '', $area);
               $area = str_replace('Zone', 'Z', $area);
               $area = str_replace('Out', 'O', $area);
            }
            $locArr['area'][] = $area;
            $locArr['subarea'][] = $subarea;
            $locArr['location'][] = $row->location;
         }
         //remove any duplicates
         $locArr['area'] = array_unique($locArr['area']);
         $locArr['subarea'] = array_unique($locArr['subarea']);
         $locArr['location'] = array_unique($locArr['location']);

         //concatenate array values into strings separated by ' and '
         $area = implode(' and ', $locArr['area']);
         $subarea = implode(' and ', $locArr['subarea']);
         $location = implode(' and ', $locArr['location']);

         //populate return data
         $return['colDefs']['area'] = array('field' => 'area', 'displayName' => ($useFormSC ? 'A' : 'Area'), 'displayOrder' => $locationOrder);
         $return['data']['area'] = $area;
         $return['colDefs']['subarea'] = array('field' => 'subarea', 'displayName' => ($useFormSC ? 'SUBAREA' : 'Subarea'), 'displayOrder' => $locationOrder + 1);
         $return['data']['subarea'] = $subarea;
         $return['colDefs']['location'] = array('field' => 'location', 'displayName' => ($useFormSC ? 'LOC' : 'Location'), 'displayOrder' => $locationOrder + 2);
         $return['data']['location'] = $location;
      }
   }
   return $return;
}

/* Pull Location information */

function pullTickData($entryID, $useFormSC = false, $ticketsOrder = 70) {
   global $wpdb;
   //global $useFormSC;
   $return = array();
   $return['data'] = array();
   $return['colDefs'] = array();

   //schedule information
   if ($entryID != '') {
      //get Ticket information for this lead
      $sql = "SELECT  access_code
            FROM    eb_entry_access_code
            where   entry_id   = $entryID";

      $results = $wpdb->get_results($sql);
      if ($wpdb->num_rows > 0) {
         $ticketArr = array();
         foreach ($results as $row) {
            $ticketArr['access_code'][] = $row->access_code;
         }
         //remove any duplicates
         $ticketArr['access_code'] = array_unique($ticketArr['access_code']);

         //concatenate array values into strings separated by '    ' (4 spaces)
         $access_code = implode('    ', $ticketArr['access_code']);

         //populate return data
         $return['colDefs']['access_code'] = array('field' => 'access_code', 'displayName' => 'Access Codes', 'displayOrder' => $ticketsOrder);
         $return['data']['access_code'] = $access_code;
      }
   }
   return $return;
}

function pullPayData($entryID, $paymentOrder = 50) {
   global $wpdb;
   $return = array();
   $return['data'] = array();
   $return['colDefs'] = array();

   //add payment information
   if ($entryID != '') {
      //get scheduling information for this lead
      $paysql = "select  wp_gf_entry_meta.entry_id as pymt_entry,
                        wp_gf_addon_payment_transaction.transaction_type,
                        wp_gf_addon_payment_transaction.transaction_id,
                        wp_gf_addon_payment_transaction.amount,
                        wp_gf_addon_payment_transaction.date_created,
                        (SELECT meta_value FROM `wp_gf_entry_meta` WHERE `entry_id` = pymt_entry and meta_key=797 limit 1) as invoice_id
                  from  wp_gf_entry_meta
                  left  outer join wp_gf_addon_payment_transaction
                        on wp_gf_entry_meta.entry_id = wp_gf_addon_payment_transaction.lead_id
                 where  meta_value = $entryID "
              . "    and wp_gf_entry_meta.meta_key like 'entry_id'";

      $payresults = $wpdb->get_results($paysql);
      if ($wpdb->num_rows > 0) {
         //add payment data to report
         $pay_det    = "";
         $order_id   = "";               
         $invoice_id = "";
         
         $transaction_id = array();
         $amount = 0;
         $date_created = array();           
               
         foreach ($payresults as $payrow) {
            $transaction_id[] = $payrow->transaction_id;   //payment transaction ID (from paypal)
            $amount = $amount + $payrow->amount; //payment amt
            $date_created[] = $payrow->date_created;     //payment date

            $payEntry = GFAPI::get_entry($payrow->pymt_entry);
            $payForm = GFAPI::get_form($payEntry['form_id']);
            $pay_status = $payEntry['payment_status'];

            foreach ($payForm['fields'] as $payFields) {
               if ($payFields['type'] == 'product') {
                  if ($payFields['inputType'] == 'singleproduct') {
                     if (is_array($payFields['inputs'])) {
                        foreach ($payFields['inputs'] as $input) {
                           $pay_det .= $input['label'] . ': ';
                           $pay_det .= $payEntry[$input['id']] . "\r";
                        }
                        $pay_det .= "\r";
                     }
                  } else {
                     $pay_det .= (isset($payFields['label']) ? $payFields['label'] : '') . ': ';
                     $pay_det .= (isset($payEntry[$payFields['id'] . '.2']) ? $payEntry[$payFields['id'] . '.2'] : '') . "\r";
                  }
               }
            }
            
            if($payrow->invoice_id !== '' && $payrow->invoice_id !== NULL){
               //order id
               $order_id .= $payrow->pymt_entry . "\r";               
               $invoice_id .=$payrow->invoice_id ."\r";               
            }    
         }
              
         $return['colDefs']['order_id'] = array('field' => 'order_id', 'displayName' => 'Order ID', 'displayOrder' => $paymentOrder);
         $return['data']['order_id'] = $order_id;

         //Invoice ID
         $return['colDefs']['invoice_id'] = array('field' => 'invoice_id', 'displayName' => 'Invoice ID', 'displayOrder' => $paymentOrder);         
         $return['data']['invoice_id'] = $invoice_id;
         $paymentOrder = $paymentOrder+2;
         
         //payment transaction ID (from paypal)
         $return['colDefs']['trx_id'] = array('field' => 'trx_id', 'displayName' => 'Pay trxID', 'displayOrder' => $paymentOrder);
         $return['data']['trx_id'] = implode("\r", $transaction_id);

         //payment amt
         $return['colDefs']['pay_amt'] = array('field' => 'pay_amt', 'displayName' => 'Pay amount', 'cellFilter' => 'currency', 'displayOrder' => $paymentOrder + 1);
         $return['data']['pay_amt'] = $amount;

         //payment date
         $return['colDefs']['pay_date'] = array('field' => 'pay_date', 'displayName' => 'Pay date', 'displayOrder' => $paymentOrder + 2);
         $return['data']['pay_date'] = implode("\r", $date_created);

         //payment details
         $return['colDefs']['pay_det'] = array('field' => 'pay_det', 'displayName' => 'Payment Details', 'displayOrder' => $paymentOrder + 3);
         $return['data']['pay_det'] = $pay_det;

         $return['colDefs']['pay_status'] = array('field' => 'pay_status', 'displayName' => 'Payment Status', 'displayOrder' => $paymentOrder + 3);
         $return['data']['pay_status'] = $pay_status;
      }
   }

   return $return;
}

/* Pull requested Field Data (no pass/fail logic) */

function pullFieldData($entryID, $reqFields) {
   global $wpdb;
   //global $useFormSC;
   $return = array();
   $return['data'] = array();
   $return['colDefs'] = array();
   $reqIDArr = array_keys($reqFields);
   if ($entryID != '' && is_array($reqIDArr)) {
      $reqIDs = implode(",", $reqIDArr);
      /* $reqFields = array of id's to pull and labels for report */
      $sql = "select meta_value as value, meta_key from wp_gf_entry_meta where meta_key in($reqIDs) and entry_id=$entryID";
      $results = $wpdb->get_results($sql);
      if ($wpdb->num_rows > 0) {
         foreach ($results as $row) {
            $return['data']['field_' . $row->meta_key] = $row->value;
            $return['colDefs']['field_' . $row->meta_key] = array('field' => 'field_' . $row->meta_key, 'displayName' => $reqFields[$row->meta_key]);
         }
      }
   }

   return $return;
}

function retrieveRptData($table, $faire) {
   global $wpdb;
   require_once 'table.fields.defs.php';
   $sql = '';
   $where = '';
   $orderBy = '';
   //build columnDefs
   foreach ($tableFields[$table]['colDefs'] as $fields) {
      $vars = array();
      switch ($fields['filterType']) {
         case 'dropdown':
            $options = array();
            $selectOptions = array();

            //retrieve dropdown info
            if (isset($fields['fkeySQL'])) {
               $fkeyData = getFkeyData($fields['fkeySQL']);
               $options = $fkeyData[0];
               $selectOptions = $fkeyData[1];
               //additional select options outside of fkey
               if (isset($fields['options'])) {
                  foreach ($fields['options'] as $optKey => $option) {
                     $options[] = array('id' => $optKey, 'fkey' => $option);
                     $selectOptions[] = array('value' => $optKey, 'label' => $option);
                  }
               }
            } else {
               //use defined options`
               foreach ($fields['options'] as $optKey => $option) {
                  $options[] = array('id' => $optKey, 'fkey' => $option);
                  $selectOptions[] = array('value' => $optKey, 'label' => $option);
               }
            }

            //sort options by fkey and selected options by label
            //usort($options, "cmpfkey");
            //usort($selectOptions, "cmpval");

            $vars = array('displayName' => ucfirst((isset($fields['fieldLabel']) ? $fields['fieldLabel'] : $fields['fieldName'])),
                'filter' => array('selectOptions' => $selectOptions),
                'cellFilter' => 'griddropdown:this',
                'editDropdownOptionsArray' => $options
            );
            break;
         case 'entrylink':
            $vars = array('cellTemplate' => '<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id={{row.entity.form_id}}&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>');
            break;
         case 'hidden':
            $vars = array('visible' => false);
            break;
         case 'custom':
         case 'number':
         case 'text':
         default:
            break;
      }
      if (isset($fields['cellTooltip']))
         $vars['cellTooltip'] = $fields['cellTooltip'];
      if (isset($fields['cellTemplate']))
         $vars['cellTemplate'] = $fields['cellTemplate'];
      if (isset($fields['cellFilter']))
         $vars['cellFilter'] = $fields['cellFilter'];
      if (isset($fields['visible']))
         $vars['visible'] = $fields['visible'];
      if (isset($fields['type']))
         $vars['type'] = $fields['type'];

      $vars['name'] = $fields['fieldName'];
      $vars['minWidth'] = 100;
      $vars['width'] = (isset($fields['width']) ? $fields['width'] : '*');
      $columnDefs[] = $vars;
   }

   //build data
   $data['columnDefs'] = $columnDefs;

   //get table data
   $query = $tableFields[$table]['query'];

   //loop thru entry data and build array
   $result = $wpdb->get_results($query, ARRAY_A);
   if ($wpdb->last_error !== '') :
      $wpdb->print_error();
   endif;
   //create array of table data
   foreach ($result as $row) {
      $data['data'][] = $row;
   }
   echo json_encode($data);
   exit;
}

function invalidRequest($message = '') {
   $data = array();
   $data['success'] = false;
   $data['message'] = ($message != '' ? $message : "Invalid request.");
   echo json_encode($data);
   exit;
}

function getFkeyData($fkeySQL) {
   global $mysqli;
   //build options drop down
   $options = array();
   $selectOptions = array();

   $result = $mysqli->query($fkeySQL);
   while ($row = $result->fetch_assoc()) {
      $options[] = array('id' => intval($row['ID']), 'fkey' => $row['field']);
      $selectOptions[] = array('value' => intval($row['ID']), 'label' => $row['field']);
   }
   return(array($options, $selectOptions));
}

function cmp($a, $b) {
   return $a['id'] - $b['id'];
}

function cmpfkey($a, $b) {
   return $b['fkey'] - $a['fkey'];
}

function cmpval($a, $b) {
   return $b['label'] - $a['label'];
}

function cmpEntryID($a, $b) {
   return $b['entry_id'] - $a['entry_id'];
}

function getBuildRptData() {
   global $mysqli;
   $data = array();
   //return form data
   $formReturn = array();
   $forms = RGFormsModel::get_forms(null, 'title');
   foreach ($forms as $form) {
      //exclude master form
      if ($form->id != 9) {
         $formReturn[] = array('id' => absint($form->id), 'name' => htmlspecialchars_decode($form->title));
      }
   }
   $form = RGFormsModel::get_form_meta(9);

   //field list (from form 9)
   $fieldReturn = array();
   $sql = 'select display_meta from wp_gf_form_meta where form_id=9';
   $result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
   while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $json = json_decode($row['display_meta']);
      $jsonArray = (array) $json->fields;
      foreach ($jsonArray as &$array) {
         $array->id = (float) $array->id;
         $array = (array) $array;
      }
      usort($jsonArray, "cmp");
      $fieldData = array();
      $fieldData[] = 'entry id';
      foreach ($jsonArray as $field) {
         switch ($field['type']) {
            case 'html':
            case 'section':
            case 'page':
               break;
            default:
               $fieldLabel = (isset($field['label']) ? $field['label'] : '');
               $label = (isset($field['adminLabel']) && $field['adminLabel'] != '' ? $field['adminLabel'] : $fieldLabel);

               if ($field['type'] == 'checkbox' || $field['type'] == 'radio' || $field['type'] == 'select' || $field['type'] == 'address') {
                  //add an option to select all choices
                  $fieldReturn[] = array('id' => $field['id'], 'label' => $label, 'choices' => 'all', 'type' => $field['type']);
                  if (isset($field['inputs']) && !empty($field['inputs'])) {
                     foreach ($field['inputs'] as $choice) {
                        $label = ($label != '' ? $label : $choice->label);
                        $fieldReturn[] = array('id' => $choice->id, 'label' => $label, 'choices' => htmlspecialchars_decode($choice->label), 'type' => $field['type']);
                     }
                  } else {
                     foreach ($field['choices'] as $choice) {
                        $label = ($label != '' ? $label : $choice->value);
                        $fieldReturn[] = array('id' => $field['id'], 'label' => $label, 'choices' => htmlspecialchars_decode(($choice->text != '' ? $choice->text : $choice->value)), 'type' => $field['type']);
                     }
                  }
               } else {
                  $inputs = ($field['type'] == 'name' ? $field['inputs'] : '');
                  $fieldReturn[] = array('id' => $field['id'], 'label' => $label, 'choices' => '', 'type' => $field['type'], 'inputs' => $inputs);
               }
         }
      }
   }

   //RMT fields
   //resources
   $sql = 'SELECT * FROM `wp_rmt_resource_categories`'; //ID, category
   $result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
   $data['rmt']['resource'][] = array('id' => 'all', 'value' => 'All Resources');
   while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data['rmt']['resource'][] = array('id' => $row['ID'], 'value' => $row['category']);
   }

   //attributes
   $data['rmt']['attribute'][] = array('id' => 'all', 'value' => 'All Attributes');
   $sql = 'SELECT * FROM `wp_rmt_entry_att_categories`'; //returns ID, category, token
   $result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
   while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data['rmt']['attribute'][] = array('id' => $row['ID'], 'value' => $row['category']);
   }

   //attention
   $data['rmt']['attention'][] = array('id' => 'all', 'value' => 'All Attention');
   $sql = 'SELECT * FROM `wp_rmt_attn`'; //returns ID, value, token
   $result = $mysqli->query($sql) or trigger_error($mysqli->error . "[$sql]");
   while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data['rmt']['attention'][] = array('id' => $row['ID'], 'value' => $row['value']);
   }

   //meta fields
   $data['rmt']['meta'][] = array('id' => 'res_status', 'type' => 'meta', 'value' => 'Resource Status');
   $data['rmt']['meta'][] = array('id' => 'res_assign', 'type' => 'meta', 'value' => 'Resource Assign To');
   //$data['rmt']['meta'][]=array('id'=>$row['entryRating'],'type'=>'meta','value'=>'Entry Rating');
   //$data['rmt']['meta'][]=array('id'=>$row['entry_id'],'type'=>'meta','value'=>'Linked to Entry');

   $data['forms'] = $formReturn;
   $data['fields'] = $fieldReturn;
   echo json_encode($data);
   exit;
}

//this function cross references faire entries to their assigned resources and attributes
function ent2resource($table, $faire, $type) {
   global $wpdb;
   $data = array();
   $columnDefs = array();

   //find all non trashed entries for selected faires
   $sql = "select wp_gf_entry.id as 'entry_id', wp_gf_entry.form_id, wp_mf_faire.faire,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key='303') as status,
              (select meta_value as value from wp_gf_entry_meta where wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key='151') as proj_name,
              (select area from wp_mf_faire_area, wp_mf_faire_subarea where wp_mf_faire_subarea.id = subarea_id and wp_mf_faire_subarea.area_id = wp_mf_faire_area.id) as area,
              wp_mf_faire_subarea.subarea,
              wp_mf_faire_area.area,
              wp_mf_location.location, wp_mf_location.id as location_id
            from wp_gf_entry
              left outer join wp_mf_faire          on find_in_set (wp_gf_entry.form_id,wp_mf_faire.form_ids) > 0
              left outer join wp_mf_location       on wp_mf_location.entry_id = wp_gf_entry.id
              left outer join wp_mf_faire_subarea  on wp_mf_location.subarea_id = wp_mf_faire_subarea.id
              left outer join wp_mf_faire_area     on wp_mf_faire_subarea.area_id = wp_mf_faire_area.id
            where status = 'active' and
                  faire is not NULL and
                  form_id!=1 and form_id!=9 and
                  wp_mf_faire.ID=" . $faire .
           " order by wp_gf_entry.id asc";

   //loop thru entry data and build array
   $entries = $wpdb->get_results($sql, ARRAY_A);

   $entryData = array();
   $resArray = array();
   $attArray = array();
   $attnArray = array();

   foreach ($entries as $entry) {
      if ($entry['status'] != 'Accepted' && $entry['status'] != 'Proposed')
         continue; //skip this record

      $dbdata = array();
      //set basic data
      $dbdata['entry_id'] = $entry['entry_id'];
      $dbdata['form_id'] = $entry['form_id'];
      //pull form data and see if it matches the requested form type
      $formPull = GFAPI::get_form($entry['form_id']);
      $formType = (isset($formPull['form_type']) ? $formPull['form_type'] : '');
      if ($type != 'all') {
         if (ucwords($type) != $formType)
            continue;
      }
      //do not return Presenation records
      if ($formType == 'Presentation')
         continue; //skip this record

      $dbdata['form_type'] = $formType;
      $dbdata['faire'] = $entry['faire'];
      $dbdata['status'] = $entry['status'];
      $dbdata['proj_name'] = $entry['proj_name'];

      //set location data
      if ($entry['location_id'] != NULL) {
         //set resource data
         $dbdata['location'] = array('subarea' => $entry['subarea'], 'location' => $entry['location'], 'area' => $entry['area']);
      }


      //pull resource information
      $resSql = "select wp_rmt_entry_resources.resource_id,
                      wp_rmt_entry_resources.qty as 'resource_qty',
                      wp_rmt_entry_resources.comment as 'resource_comment',
                      token as res_label,description
          from wp_rmt_entry_resources,wp_rmt_resources where wp_rmt_entry_resources.entry_id = " . $entry['entry_id'] . " and wp_rmt_resources.id = resource_id";
      $resources = $wpdb->get_results($resSql, ARRAY_A);
      foreach ($resources as $resource) {
         $dbdata['resource'][$resource['resource_id']] = array('qty' => $resource['resource_qty'], 'comment' => $resource['resource_comment']);
         //add resource to resource array
         $resArray[$resource['resource_id']] = array('name' => $resource['res_label'], 'desc' => $resource['description']);
      }

      // pull attribute data
      $attSql = "select wp_rmt_entry_attributes.attribute_id,
                      wp_rmt_entry_attributes.value as 'attribute_value',
                      wp_rmt_entry_attributes.comment as 'attribute_comment',
                      token  as att_label,category
            from wp_rmt_entry_attributes, wp_rmt_entry_att_categories
            where wp_rmt_entry_attributes .entry_id = " . $entry['entry_id'] . " and
            wp_rmt_entry_att_categories.id = attribute_id";
      $attributes = $wpdb->get_results($attSql, ARRAY_A);
      foreach ($attributes as $attribute) {
         //set resource data
         $dbdata['attribute'][$attribute['attribute_id']] = array('value' => $attribute['attribute_value'], 'comment' => $attribute['attribute_comment']);
         //add attribute to attribute array
         $attArray[$attribute['attribute_id']] = array('name' => $attribute['att_label'], 'desc' => $attribute['category']);
      }

      // pull attention data
      $attnSql = "select attn_id as 'attn_id', comment as 'attn_comment', wp_rmt_attn.value as attn_type
                from wp_rmt_entry_attn, wp_rmt_attn
                where wp_rmt_attn.id = attn_id and wp_rmt_entry_attn.entry_id = " . $entry['entry_id'];
      $attentions = $wpdb->get_results($attnSql, ARRAY_A);
      foreach ($attentions as $attention) {
         //set resource data
         $dbdata['attention'][$attention['attn_id']] = array('comment' => $attention['attn_comment']);
         //add attribute to attribute array
         $attnArray[$attention['attn_id']] = $attention['attn_type'];
      }

      $data[$entry['entry_id']] = $dbdata;
   }

   //default columns
   $columnDefs[] = array('field' => 'faire', 'displayName' => 'Faire', 'width' => '50');
   $columnDefs[] = array('field' => 'status', 'displayName' => 'Status', 'width' => '100', 'sort' => array('direction' => 'uiGridConstants.ASC', 'priority' => 0), 'enableSorting' => true);
   $columnDefs[] = array('field' => 'entry_id', 'displayName' => 'Entry ID', 'width' => '75');
   $columnDefs[] = array('field' => 'form_type', 'displayName' => 'Form Type', 'width' => '150');
   $columnDefs[] = array('field' => 'proj_name', 'displayName' => 'Entry Name', 'width' => '*');
   $columnDefs[] = array('field' => 'location.area', 'displayName' => 'Area', 'sort' => array('direction' => 'uiGridConstants.ASC', 'priority' => 1), 'enableSorting' => true);
   $columnDefs[] = array('field' => 'location.subarea', 'displayName' => 'Subarea', 'sort' => array('direction' => 'uiGridConstants.ASC', 'priority' => 3), 'enableSorting' => true);
   $columnDefs[] = array('field' => 'location.location', 'displayName' => 'Location', 'sort' => array('direction' => 'uiGridConstants.ASC', 'priority' => 2), 'enableSorting' => true);

   //set up info for the sub header row
   $subHeader = array(
       0 => array(
           'entry_id' => 0,
           'form_id' => 0,
           'form_type' => '',
           'faire' => 'Field Descriptions',
           'status' => 0,
           'proj_name' => '',
       )
   );

   //$resArray  = array_unique($resArray);
   //$attArray  = array_unique($attArray);
   $attnArray = array_unique($attnArray);
   $colDefs2Sort = array();

   foreach ($resArray as $key => $resource) {
      $colDefs2Sort[] = array('displayName' => $resource['name'], 'field' => 'resource.' . $key . '.qty');
      $colDefs2Sort[] = array('displayName' => $resource['name'] . ' - comment', 'field' => 'resource.' . $key . '.comment');
      $subHeader['0']['resource'][$key] = array('qty' => $resource['desc']);
   }
   foreach ($attArray as $key => $attribute) {
      $colDefs2Sort[] = array('displayName' => $attribute['name'], 'field' => 'attribute.' . $key . '.value');
      $colDefs2Sort[] = array('displayName' => $attribute['name'] . ' - comment', 'field' => 'attribute.' . $key . '.comment');
      $subHeader['0']['attribute'][$key] = array('qty' => $attribute['desc']);
   }
   foreach ($attnArray as $key => $attention) {
      $colDefs2Sort[] = array('displayName' => $attention, 'field' => 'attention.' . $key . '.comment');
   }
   //sort $colDefs2Sort array by displayName
   usort($colDefs2Sort, function($a, $b) {
      return strcmp($a["displayName"], $b["displayName"]);
   });
   //array merge $colDefs2Sort with $columnDefs


   $retData = array();
   //usort($data, "cmpEntryID");
   //$data = (array) $data;
   //sort data by status, area, subarea, location
   $columnDefs = array_merge($columnDefs, $colDefs2Sort);
   //merge subheader data with $data
   //$data[''] = $subHeader;
   $retData['data'] = array_merge($subHeader, $data);
   $retData['columnDefs'] = $columnDefs;
   //reindex columnDefs as the grid will blow up if the indexes aren't in order
   $retData['columnDefs'] = array_values($retData['columnDefs']);
   $retData['data'] = array_values($retData['data']);
   echo json_encode($retData);
   exit;
}

function convert_smart_quotes($string) {
   $chr_map = array(
       // Windows codepage 1252
       "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
       "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
       "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
       "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
       "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
       "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
       "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
       "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark
       // Regular Unicode     // U+0022 quotation mark (")
       // U+0027 apostrophe     (')
       "\xC2\xAB" => '"', // U+00AB left-pointing double angle quotation mark
       "\xC2\xBB" => '"', // U+00BB right-pointing double angle quotation mark
       "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
       "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
       "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
       "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
       "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
       "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
       "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
       "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
       "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
       "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
   );
   $chr = array_keys($chr_map); // but: for efficiency you should
   $rpl = array_values($chr_map); // pre-calculate these two arrays
   $string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));

   return $string;
}

function pullEntityTasks($formSelect) {
   global $wpdb;
   $data = array();
   $data['data'] = array();
   $data['columnDefs'] = array();
   $project_link = "/wp-admin/admin.php?page=gf_entries&view=entry&id=111&lid=59591&order=ASC&filter&paged=1&pos=0&field_id&operator";
   //pull data
   $sql = "SELECT    tasks.lead_id, tasks.created, tasks.completed, tasks.description, tasks.required, meta.meta_value,meta.entry_id as other_entry,
    wp_gf_entry.form_id,
    (select meta_value as value from wp_gf_entry_meta where meta_key = '151' and entry_id = tasks.lead_id) as project_name,
    (select form_id from wp_gf_entry where id = tasks.lead_id) as form_id
          FROM      wp_mf_entity_tasks AS tasks
          join      wp_gf_entry on tasks.lead_id= wp_gf_entry.id
          LEFT JOIN wp_gf_entry_meta AS meta
                 ON meta.`form_id` = $formSelect
                AND meta.meta_key = 'entry_id'
                AND tasks.lead_id = meta.meta_value
          WHERE     tasks.`form_id` = $formSelect
          UNION ALL
          SELECT    NULL, NULL, NULL, NULL, NULL, meta_value,entry_id as other_entry, form_id,
          (select meta_value as value from wp_gf_entry_meta where meta_key = '151' and entry_id = meta_value) as project_name,
          (select form_id from wp_gf_entry where id = meta_value) as form_id
          FROM      wp_gf_entry_meta
          WHERE     meta_value NOT IN (SELECT lead_id FROM wp_mf_entity_tasks)
                AND `form_id` = $formSelect
                AND meta_key = 'entry_id'";

   $result = $wpdb->get_results($sql);

   $data['columnDefs'] = array(
       array("name" => "lead_id", "displayName" => "Entry", "width" => "65",
           "cellTemplate" => '<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id={{row.entity.formid}}&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>'
       ),
       array("name" => "formid", "displayName" => "Form ID", "width" => "300", "visible" => false),
       array("name" => "project_name", "displayName" => "Project Name", "width" => "300"),
       array("name" => "created", "width" => "150"),
       array("name" => "completed", "width" => "150"),
       array("name" => "description", "width" => "150"),
       array("name" => "required", "displayName" => "Req?", "width" => "70"),
       array("name" => "oformid", "displayName" => "Other Form ID", "width" => "65"),
       array("name" => "oentry", "displayName" => "Other Entry ID", "width" => "165",
           "cellTemplate" => '<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id={{row.entity.oformid}}&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>'
       ),
       array("name" => "not_assigned", "displayName" => "Not Assigned", "width" => "100")
   );

   //create array of table data
   foreach ($result as $row) {
      if ($row->lead_id == NULL && $row->meta_value != NULL) {
         $not_assigned = 'Yes';
         $lead_id = $row->meta_value;
      } else {
         $not_assigned = '';
         $lead_id = $row->lead_id;
      }
      $other_entry = ($row->other_entry == NULL ? '' : $row->other_entry);
      $data['data'][] = array('lead_id' => $lead_id,
          'formid' => $row->form_id,
          'project_name' => $row->project_name,
          'created' => $row->created,
          'completed' => $row->completed,
          'description' => $row->description,
          'required' => ($row->required == 1 ? 'Yes' : 'No'),
          'not_assigned' => $not_assigned,
          'oformid' => $formSelect,
          'oentry' => $other_entry
      );
   }
   echo json_encode($data);
   exit;
}

function formSC($value) {
   $value = strtolower($value);
   /*        Space Size
    * Contains       Replace entire value With
    *  Tabletop         TABLE
    *  mobile           Mobile
    */
   if (strpos($value, 'tabletop') !== false) {
      $value = 'TABLE';
   }
   if (strpos($value, 'mobile') !== false) {
      $value = 'Mobile';
   }

   /*    Find           Replace
    * single quote       blank
    * ' x '              blank
    */
   $value = str_replace("'", '', $value);
   $value = str_replace(' x ', 'x', $value);

   /*      Exposure
    *   Find      Replace
    *  Inside      In
    *  Outside     Out
    *  Either      i/o
    *  ' under a'  blank
    *  ' under'    blank
    *  tents       tent
    *  tent        Tent
    *  large       Lg
    *  ?           Out on grass
    */
   $value = str_replace('inside', 'In', $value);
   $value = str_replace('outside', 'Out', $value);
   $value = str_replace('either', 'i/o', $value);
   $value = str_replace(' under a', '', $value);
   $value = str_replace(' under', '', $value);
   $value = str_replace('tents', 'tent', $value);
   $value = str_replace('tent', 'Tent', $value);
   $value = str_replace('dark', 'Dark', $value);
   $value = str_replace('large', 'Lg', $value);
   $value = str_replace('either', 'i/o', $value);
   $value = str_replace('?', 'Out on grass', $value);

   /*        Noise
    *  Contains       Replace entire value With
    *    Normal            blank
    *    Amplified         AMP
    *    Repetitive        REP
    *    Loud!             LOUD!
    */
   if (strpos($value, 'normal') !== false) {
      $value = '';
   }
   if (strpos($value, 'amplified') !== false) {
      $value = 'AMP';
   }
   if (strpos($value, 'repetitive') !== false) {
      $value = 'REP';
   }
   if (strpos($value, 'loud') !== false) {
      $value = 'LOUD!';
   }

   /*        Internet
    *   Contains       Replace entire value With
    *    No internet            blank
    *    Nice to have           Nice
    *    must have              MUST
    */
   if (strpos($value, 'no internet') !== false) {
      $value = '';
   }
   if (strpos($value, 'nice to have') !== false) {
      $value = 'Nice';
   }
   if (strpos($value, 'must have') !== false) {
      $value = 'MUST';
   }
   return $value;
}

function paymentRpt($table, $faire) {
   global $wpdb;
   $data = array();
   $data['data'] = array();
   $data['columnDefs'] = array();

   $data['columnDefs'] = array(
       array("field" => "form_id", "displayName" => "Pay Form Id", "visible" => false, "displayOrder" => 20),
       array("field" => "entry_id", "displayName" => "Pay Entry Id", "visible" => false, "displayOrder" => 30),
       array("field" => "origEntry_id", "displayName" => "Entry Id", "displayOrder" => 35),
       array("field" => "origForm_id", "displayName" => "FormId", "displayOrder" => 35),
       array("field" => "form_type", "displayName" => "Form Type", "displayOrder" => 40),
       array("field" => "field_151", "displayName" => "Exhibit Name", "type" => "string", "displayOrder" => 50),
       //array("field"=>"meta_res_status","displayName"=>"Resource Status","displayOrder"=>200),
       array("field" => "field_303", "displayName" => "Status", "type" => "string", "visible" => false, "displayOrder" => 800)
   );

   //find payment invoices for the selected faire
   if ($table == 'sponsorOrder') {
      //requested fields from the sponsor order form
      $reqFields = array(751 => 'Invoice Number',
          444 => 'Company Billing Name',
          446 => 'Billing Email',
          161 => 'Contact Email',
          666 => 'Order Total'
      );
      $sql = 'SELECT wp_gf_entry.id as entry_id,'
              . '    wp_gf_form_meta.form_id,'
              . '    meta_value as "origEntry_id",'
              . '    origLead.form_id as origForm_id, '
              . '(select meta_value as value from wp_gf_entry_meta meta2 where meta2.meta_key = "151" and entry_id=wp_gf_entry_meta.meta_value limit 1) as field_151, '
              . '(select meta_value as value from wp_gf_entry_meta meta2 where meta2.meta_key = "303" and entry_id=wp_gf_entry_meta.meta_value limit 1) as field_303, '
              . '(select meta_value as value from wp_gf_entry_meta meta2 where meta2.meta_key = "303" and entry_id=wp_gf_entry.id limit 1) as status_payentry '
              . 'FROM wp_gf_form_meta '
              . 'left outer join wp_mf_faire on find_in_set (wp_gf_form_meta.form_id,wp_mf_faire.non_public_forms) > 0 '
              . 'left outer join wp_gf_entry on wp_gf_entry.form_id = wp_gf_form_meta.form_id '
              . 'left outer join wp_gf_entry_meta on wp_gf_entry_meta.entry_id = wp_gf_entry.id and meta_key = "entry_id"'
              . 'left outer join wp_gf_entry origLead on origLead.id = meta_value '
              . 'WHERE  display_meta like \'%"form_type":"Payment"%\' and '
              . '       display_meta like \'%"create_invoice":"yes"%\' and '
              . '       wp_mf_faire.id=' . $faire . ' and '
              . '       wp_gf_entry.status="active" ';

      $result = $wpdb->get_results($sql);
      $colDefs = array();
      foreach ($result as $row) {
         //pull form data and see if it matches the requested form type
         $formPull = GFAPI::get_form($row->origForm_id);
         $formType = (isset($formPull['form_type']) ? $formPull['form_type'] : '');
         $retformType = shortFormType($formType);
         if ($row->field_303 == 'Accepted' && $row->status_payentry == 'Accepted') {
            $oEntryID = $row->origEntry_id;
            $fieldData = array(
                'entry_id' => $row->entry_id,
                'form_id' => $row->form_id,
                'origEntry_id' => $oEntryID,
                'origForm_id' => $row->origForm_id,
                'form_type' => $retformType,
                'field_151' => $row->field_151,
                'meta_res_status' => '',
                'field_303' => $row->field_303);

            //order form field data
            $fieldRetData = pullFieldData($row->entry_id, $reqFields);
            $fieldData = array_merge($fieldData, $fieldRetData['data']);
            $colDefs = array_merge($colDefs, $fieldRetData['colDefs']);

            //location data
            $locRetData = pullLocData($oEntryID, false);
            $fieldData = array_merge($fieldData, $locRetData['data']);
            $colDefs = array_merge($colDefs, $locRetData['colDefs']);

            //Payment data
            $PayRetData = pullPayData($oEntryID);
            $fieldData = array_merge($fieldData, $PayRetData['data']);
            $colDefs = array_merge($colDefs, $PayRetData['colDefs']);

            $data['data'][] = $fieldData;
         }
      }
      $data['columnDefs'] = array_merge($data['columnDefs'], $colDefs);
      $data['columnDefs'] = array_values($data['columnDefs']);
   }
   echo json_encode($data);
   exit;
}

function shortFormType($form_type) {
   switch ($form_type) {
      case 'Show Management':
         $form_type = 'SHOW';
         break;
      case 'Exhibit':
         $form_type = 'MAK';
         if ($cmInd == 'Yes') {
            $form_type = 'CM';
         }
         break;
      case 'Sponsor':
         $form_type = 'SPR';
         break;
      case 'Startup Sponsor':
         $form_type = 'STAR';
         break;
      case 'Performance':
         $form_type = 'PERF';
         break;
   }
   return $form_type;
}
