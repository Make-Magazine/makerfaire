<?php
/*
ajax to populate resource management table
*/

require_once 'config.php';
require_once 'table.fields.defs.php';

$json = file_get_contents('php://input');
$obj = json_decode($json);

$type           = (isset($obj->type)?$obj->type:'');
$table          = (isset($obj->table)?$obj->table:'');
$formSelect     = (isset($obj->formSelect)?$obj->formSelect:'');
$formType       = (isset($obj->formType)?$obj->formType:'');
$selectedFields = (isset($obj->selectedFields)?$obj->selectedFields:'');
$rmtData        = (isset($obj->rmtData)?$obj->rmtData:'');
$location       = (isset($obj->location)?$obj->location:false);
$payment        = (isset($obj->payments)?$obj->payments:false);
$faire          = (isset($obj->faire)?$obj->faire:'');
$status         = (isset($obj->status)?$obj->status:'');

if($type != ''){
  if($type =="tableData"){
    if($table=='formData'){
      getBuildRptData();
    }elseif($table=='wp_mf_entity_tasks'){
      pullEntityTasks($formSelect);
    }else{
      //build report data
      retrieveRptData($table,$faire);
    }
  }elseif($type =="customRpt"){
    if(($formSelect != '' || $formType!='') && $selectedFields!=''){
      cannedRpt($obj);
      //build report
     // buildRpt($formSelect, $formType, $selectedFields, $rmtData, $location, $payment, $faire, $status);
    }else{
      invalidRequest('Error: Form or Fields not selected');
    }
  }elseif($type =="ent2resource"){
    ent2resource($table,$faire);
  }else{
    invalidRequest('Invalid Request type');
  }
}else{
	invalidRequest('Request Type Not Sent');
}

/* Build your own report function */
function cannedRpt(){
  global $wpdb; global $obj;
  $formSelect     = (isset($obj->formSelect)  ? $obj->formSelect:array());
  $formTypeArr    = (isset($obj->formType)    ? $obj->formType:array());
  $faire          = (isset($obj->faire)       ? $obj->faire:'');

  $dispFormID     = (isset($obj->dispFormID)  ? $obj->dispFormID:true);
  $useFormSC      = (isset($obj->useFormSC)   ? $obj->useFormSC:false);
  $orderBy        = (isset($obj->orderBy)     ? $obj->orderBy:'');

  $selectedFields = (isset($obj->selectedFields)  ? $obj->selectedFields:array());
  $rmtData        = (isset($obj->rmtData)     ? $obj->rmtData:array());
  $location       = (isset($obj->location)    ? $obj->location:false);
  $payment        = (isset($obj->payments)    ? $obj->payments:false);

  $entryIDorder   = (isset($obj->entryIDorder)   ? $obj->entryIDorder:10);
  $formIDorder    = (isset($obj->formIDorder)    ? $obj->formIDorder:20);
  $formTypeorder  = (isset($obj->formTypeorder)  ? $obj->formTypeorder:30);
  $locationOrder  = (isset($obj->locationOrder)  ? $obj->locationOrder:40);

  $forms      = implode(",",$formSelect);
  $formTypes  = implode("', '",$formTypeArr);
  if(!empty($formTypes)) $formTypes = "'".$formTypes."'";

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
  $data['columnDefs'][] = array('field'=>'entry_id','displayName' =>($useFormSC?'ENTRY ID':'Entry Id'), 'displayOrder'=>$entryIDorder);
  $visible = ($dispFormID ? false : true);
  $data['columnDefs'][] = array('field'=>'form_id','visible'=> $visible, 'displayOrder'=>$formIDorder);
  $data['columnDefs'][] = array('field'=>'form_type','displayName' =>($useFormSC?'TYPE':'Form Type'), 'displayOrder'=>$formTypeorder);

  //pull all entries based on formSelect, faire, and status
  //question: does wp_mf_entity have the information i need for all forms?? non exhibits missing information?

  //build array of requested fields
  $fieldSQL       = ''; //sql to pull field_numbers
  $fieldIDarr     = array(); //unique array of field ID's
  $fieldArr       = array(); //array of field data keyed by field id
  $combineFields  = array();
  $fieldQuery     = array(" field_number like '376' ");
  $acceptedOnly   = true;

  //build list of categories
  $categories = get_categories(array( 'taxonomy' => 'makerfaire_category', 'hide_empty' => false ));
  foreach($categories as $category){
    $catCross[$category->term_id] = $category->name;
  }

  $exactCriteria = array();
  //loop thru selected fields and build sql for entry details and array of requested fields
  foreach($selectedFields as $selFields){
      //build wp_rg_lead_detail query
      if($selFields->type=='name'){
        //for name field
        foreach($selFields->inputs as $choice){
          $combineFields[$selFields->id][] = $choice->id;

          //set criteria for this field id
          $fieldIDArr[$choice->id] = $selFields;
        }

        //return all selected options
        $fieldQuery[] = " field_number like '".$selFields->id.".%' ";
      } elseif($selFields->type=='radio' || $selFields->type == 'select' || $selFields->type == 'checkbox'){
        if($selFields->choices == 'all'){
          $fieldQuery[] = " field_number like '".$selFields->id.".%' ";
          $fieldIDArr[$selFields->id] = $selFields; //search for all values
        }else{
          $fieldQuery[] = " field_number like '".$selFields->id."' ";
          $fieldIDArr[$selFields->id][] = $selFields; //search for specific values
        }

      }else{ //text/textarea
        $fieldQuery[] = " field_number like '".$selFields->id."' ";
        //set criteria for this field id
        $fieldIDArr[$selFields->id] = $selFields;
      }

      //add requested field to columns
      if(isset($selFields->hide)&&$selFields->hide==true){
        //don't add this field to display
        $data['columnDefs'][$selFields->id] = array('field'=> 'field_'.str_replace('.','_',$selFields->id), 'displayName'=>$selFields->label, 'type'=>'string',visible=> false,
                                                      'displayOrder' => (isset($selFields->order)?$selFields->order:9999));
      }else{
        $data['columnDefs'][$selFields->id] = array('field'=> 'field_'.str_replace('.','_',$selFields->id), 'displayName'=>$selFields->label, 'type'=>'string',
                                                      'displayOrder' => (isset($selFields->order)?$selFields->order:9999));
      }


    if(isset($selFields->exact) && $selFields->exact){
      $exactCriteria[$selFields->id] = $selFields->choices;
    }
  }

  $fieldSQL  = implode(" or ",$fieldQuery);

  //form type is not set on entries prior to BA16
  $sql = "SELECT  entity.lead_id,
                  entity.status,
                  entity.form_type,
                  wp_rg_lead.form_id,
                  wp_rg_lead.status"
         ." FROM  `wp_mf_entity` entity
            JOIN  wp_rg_lead on wp_rg_lead.id = entity.lead_id
            JOIN  wp_mf_faire on wp_mf_faire.ID  ='$faire'

           WHERE  FIND_IN_SET (`wp_rg_lead`.`form_id`,wp_mf_faire.form_ids)> 0
             AND 	wp_rg_lead.status = 'active'"
          . (!empty($status)    ? " AND entity.status      in(".$status.")" : '')
          . (!empty($forms)     ? " AND wp_rg_lead.form_id in(".$forms.")" : '')
          . (!empty($formTypes) ? " AND entity.form_type   in(".$formTypes.",'')" : '');

  $entries = $wpdb->get_results($sql,ARRAY_A);
  $entryData = array();

  //loop thru entries
  foreach($entries as $entry){
    $lead_id = $entry['lead_id'];
    $passCriteria = true;
    $fieldData = array();

    //Are specific detail fields requested?
    if(!empty($fieldSQL)){
      //pull entry specifc detail based on requested fields
      $detailSQL = "SELECT detail.*, detail_long.value as 'long value'
                      FROM wp_rg_lead_detail detail
                    left OUTER join wp_rg_lead_detail_long detail_long
                        ON detail.id = detail_long.lead_detail_id"
            . " where detail.lead_id = $lead_id "
            . " and ($fieldSQL) "
            . " ORDER BY lead_id asc, field_number asc";
      $details = $wpdb->get_results($detailSQL,ARRAY_A);
      $cmInd = '';
      foreach($details as $detail){
        if($detail['field_number'] == 376){
          $cmInd = $detail['value'];
        }
        //field 320 is stored as category number, use cross reference to find text value
        if($detail['field_number'] == 320){
          $value = (isset($catCross[$detail['value']])?$catCross[$detail['value']]:$detail['value']);
        }else{
          $value = (isset($detail['long_value']) && $detail['long_value']!=''?$detail['long_value']:$detail['value']);
        }
        $value = htmlspecialchars_decode ($value);
        $value = convert_smart_quotes($value);

        //check if we pulled by specific field id or if this was a request for all values
        $fieldID = $detail['field_number'];
        if(isset($fieldIDArr[$fieldID])){
          $fieldCritArr = $fieldIDArr[$fieldID];
        }else{//let's look for the base id
          //remove everything after the period
          $fieldID      = (strpos($fieldID, ".") ? substr($fieldID, 0, strpos($fieldID, ".")) : $fieldID);
          $fieldCritArr = (isset($fieldIDArr[$fieldID])?$fieldIDArr[$fieldID]:'');
        }

        // radio and select options
        if(is_array($fieldCritArr)){
          //default to failing criteria
          $passCriteria = false;

          //radio and select boxes must match one of the passed values
          foreach($fieldCritArr as $fieldCriteria){
            if($fieldCriteria->type == 'radio' || $fieldCriteria->type=='select'){ //check value
              if($fieldCriteria->choices == $value){
                $passCriteria = true;
              }
            }
          }
        }
        if(!$passCriteria){
          //exit detail loop
          break;
        }
        //build output for field data - format is field_55_4 for field id 55.4
        $fieldKey             = 'field_'.str_replace('.','_',$fieldID);
        $fieldData[$fieldKey] = (isset($fieldData[$fieldKey]) ? $fieldData[$fieldKey]."\r":'') . $value;
      }

      //exclude exact fields if they do not match
      //  (doing this outside of the details loop as the requested field may not be set

      foreach($exactCriteria as $exact=>$criteria){
        $data2Test = (isset($fieldData['field_'.$exact])?$fieldData['field_'.$exact]:'');
        if($data2Test != $criteria){
          $passCriteria = false;
        }
      }

      //combine name fileds
      foreach($combineFields as $combFieldID=>$combFieldArr){
        $combinedField = '';
        foreach($combFieldArr as $combField){
          if(isset($fieldData['field_'.str_replace('.','_',$combField)])){
            if($combField!='')  $combinedField .= ' '.$fieldData['field_'.str_replace('.','_',$combField)];
            unset($fieldData['field_'.str_replace('.','_',$combField)]);
          }
        }
        $fieldData['field_'.$combFieldID]= trim($combinedField);
      }

      $form_type = $entry['form_type'];
      //record prior to BA16
      if($form_type==''){
        $formPull = GFAPI::get_form( $entry['form_id'] );
        $form_type = (isset($formPull['form_type']) ? $formPull['form_type']:'');

        //if certain form types were selected, only return those form types
        if(!empty($formTypeArr)){
          if(in_array($form_type, $formTypeArr)){
            //continue with this record
          }else{
            $passCriteria = false;; //skip this record
          }
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

      if($cmInd == 'Yes'){
        $form_type = 'CM';
      }

      //add data to array
      if($passCriteria) {
        $colDefs = array();
        //pull rmt data and location information
        if(!empty($rmtData)){
          $rmtRetData = pullRmtData($rmtData,$lead_id,$useFormSC);
          $fieldData =  array_merge($fieldData,$rmtRetData['data']);
          $colDefs   =  array_merge($colDefs,$rmtRetData['colDefs']);
        }
        if($location) {
          $locRetData = pullLocData($lead_id,$useFormSC,$locationOrder);
          $fieldData =  array_merge($fieldData,$locRetData['data']);
          $colDefs   =  array_merge($colDefs,$locRetData['colDefs']);
        }
        if($payment) {
          $PayRetData = pullPayData($lead_id);
          $fieldData =  array_merge($fieldData,$PayRetData['data']);
          $colDefs   =  array_merge($colDefs,$PayRetData['colDefs']);
        }
        $entryData[$lead_id]              = $fieldData;
        $entryData[$lead_id]['entry_id']  = $lead_id;
        $entryData[$lead_id]['form_id']   = $entry['form_id'];
        $entryData[$lead_id]['form_type'] = $form_type;

        //merge in RMT data and location info
        $data['columnDefs'] = array_merge($data['columnDefs'],$colDefs);
      }
    }
  }

  //return data
  $data['data'] = array_values($entryData);

  //sort columns by display order
  /*usort($data['columnDefs'], function($a, $b) {
    return (float) $a["displayOrder"] - (float) $b["displayOrder"];
  });*/
  usort(
    $data['columnDefs'],
    function($a, $b) {
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
} //end function

function pullRmtData($rmtData, $entryID, $useFormSC){
  global $wpdb;
  $return = array();
  $return['data'] = array();
  $return['colDefs'] = array();
  $colDefs2Sort = array();
  $incComments = false;

  //process resources
  if(isset($rmtData->resource) && !empty($rmtData->resource)){
    foreach($rmtData->resource as $selRMT){
      if($selRMT->id!='all'){
        $sql = 'SELECT  qty,type,comment, token,resource_id '
            . ' FROM   `wp_rmt_entry_resources`, wp_rmt_resources '
            . ' where   resource_id = wp_rmt_resources.ID and'
            . '         resource_category_id = '.$selRMT->id .' and'
            . '         entry_id ='.$entryID;
      }else{
        $sql = 'SELECT  resource_id, qty, concat(type, " ", wp_rmt_resource_categories.category) as type, comment '
            . ' FROM    `wp_rmt_entry_resources`, wp_rmt_resources, wp_rmt_resource_categories '
            . ' WHERE   resource_id = wp_rmt_resources.ID and'
            . '         resource_category_id = wp_rmt_resource_categories.ID and'
            . '         entry_id ='.$entryID;
      }

      //loop thru data
      $resources = $wpdb->get_results($sql,ARRAY_A);
      $entryRes = array();
      $displayOrder = $selRMT->order;
      if(isset($selRMT->aggregated) && $selRMT->aggregated==false){
        $aggrType='uiGridConstants.aggregationTypes.sum';
        foreach($resources as $resource){
          $displayOrder = $displayOrder + $resource['resource_id'];
          $colDefs2Sort['res_'.$resource['token']]             = array('field'=> 'res_'.$resource['token'],
              'displayName'=>$resource['token'],
              'aggregationType'=> $aggrType,
              'displayOrder' => $displayOrder+.1);
          $return['data']['res_'.$resource['token']] = $resource['qty'];

          //resource comments
          $dispComments = (isset($selRMT->comments)? $selRMT->comments:true);
          if($dispComments){
            $colDefs2Sort['res_'.$resource['token'].'_comment']  = array('field'=> 'res_'.$resource['token'].'_comment','displayName'=>$resource['token'].' - comment','displayOrder' => $displayOrder+.2);
            $return['data']['res_'.$resource['token'].'_comment'] = $resource['comment'];
          }
        }
      }else{
        foreach($resources as $resource){
          $comment = ($incComments && $resource['comment']!=''?" (".$resource['comment'].")":'');
          $entryRes[] = $resource['qty'] .' : '.$resource['type'].$comment;
        }
        $return['colDefs']['res_'.$selRMT->id]=   array('field'=> 'res_'.str_replace('.','_',$selRMT->id),
            'displayName'=>$selRMT->value,
            'displayOrder' => $displayOrder);
        $return['data']['res_'.$selRMT->id] = implode(', ',$entryRes);
      }
    }
  }

  //process attribute
  if(isset($rmtData->attribute) && !empty($rmtData->attribute)){
    foreach($rmtData->attribute as $selRMT){
      if($selRMT->id!='all'){
        $sql = 'select value from wp_rmt_entry_attributes where entry_id ='.$entryID.' and attribute_id='.$selRMT->id;
      }else{
        $sql = 'select concat(category," ",value) as value from wp_rmt_entry_attributes,wp_rmt_entry_att_categories where '
                . ' entry_id ='.$entryID
                . ' and attribute_id= wp_rmt_entry_att_categories.ID';
      }

      //loop thru data
      $attributes = $wpdb->get_results($sql,ARRAY_A);
      $entryAtt = array();

      foreach($attributes as $attribute){
        //search and replace
        if($useFormSC){
          $value = formSC($attribute['value']);
        }else{
          $value = $attribute['value'];
        }
        $entryAtt[] = $value;
      }
      $return['columnDefs']['att_'.$selRMT->id] = array('field'=> 'att_'.str_replace('.','_',$selRMT->id),
                                                        'displayName'=>$selRMT->value,
                                                        'displayOrder' => (isset($selRMT->order)?$selRMT->order:9999));
      $return['data']['att_'.$selRMT->id] = implode(', ',$entryAtt);
    }
  }

  //process attention
  if(isset($rmtData->attention) && !empty($rmtData->attention)){
    foreach($rmtData->attention as $selRMT){
      if($selRMT->id!='all'){
        $sql = 'select comment from wp_rmt_entry_attn where entry_id ='.$entryID.' and attn_id='.$selRMT->id;
      }else{
        $sql = 'select concat(wp_rmt_attn.value," ",comment) as comment from wp_rmt_entry_attn,wp_rmt_attn where '
                . ' entry_id ='.$entryID
                . ' and attn_id= wp_rmt_attn.ID';
      }
      //loop thru data
      $attentions = $wpdb->get_results($sql,ARRAY_A);
      $entryAttn = array();

      foreach($attentions as $attention){
        $entryAttn[] = $attention['comment'];
      }
      $return['columnDefs']['attn_'.$selRMT->id] = array('field'=> 'attn_'.str_replace('.','_',$selRMT->id),
                                                      'displayName'=>$selRMT->value,
                                                      'displayOrder' => (isset($selRMT->order)?$selRMT->order:9999));
      $return['data']['attn_'.$selRMT->id]  = implode(', ',$entryAttn);
    }
  }

  //process meta
  if(isset($rmtData->meta) && !empty($rmtData->meta)){
    //build array of requested RMT data
    $reqArr    = array();
    $reqMetaArr = array();

    foreach($rmtData->meta as $reqData){
      $reqArr[] = $reqData->id;
      $reqMetaArr[$reqData->id] = $reqData;
    }

    //meta id's are alpha
    $reqIDs = implode("', '", $reqArr);
    if(!empty($reqIDs)) $reqIDs = "'".$reqIDs."'";

    $sql = "SELECT meta_key,meta_value FROM `wp_rg_lead_meta` where meta_key in(".$reqIDs.") and lead_id = ".$entryID;

    //loop thru data
    $metas = $wpdb->get_results($sql,ARRAY_A);
    $entryMeta = array();

    foreach($metas as $meta){
      $selRMT     = $reqMetaArr[$meta['meta_key']];
      $return['colDefs']['meta_'.$selRMT->id] = array('field'       => 'meta_'.str_replace('.','_',$selRMT->id),
                                                      'displayName' => $selRMT->value,
                                                      'displayOrder' => (isset($selRMT->order)?$selRMT->order:9999)
                                                    );
      $return['data']['meta_'.$selRMT->id] = $meta['meta_value'];
    }
  }

  //sort $colDefs2Sort array by displayName
  uasort($colDefs2Sort, function($a, $b) {
    return strcmp($a["displayName"], $b["displayName"]);
  });
  $return['colDefs'] = array_merge($return['colDefs'],$colDefs2Sort);
  return $return;
}

/* Pull Location information */
function pullLocData($entryID, $useFormSC, $locationOrder) {
  global $wpdb;
  //global $useFormSC;
  $return = array();
  $return['data'] = array();
  $return['colDefs'] = array();

  //schedule information
  if($entryID!=''){
    //get scheduling information for this lead
    $sql = "SELECT  area.area,subarea.subarea,subarea.nicename, location.location
            FROM wp_mf_location location,
                    wp_mf_faire_subarea subarea,
                    wp_mf_faire_area area
            where       location.entry_id   = $entryID
                    and subarea.id          = location.subarea_id
                    and area.id             = subarea.area_id";

    $results = $wpdb->get_results($sql);
    if($wpdb->num_rows > 0){
      foreach($results as $row){
        $subarea = ($row->nicename!=''&&$row->nicename!=''?$row->nicename:$row->subarea);

        $return['colDefs']['area']=   array('field'=> 'area','displayName'=>($useFormSC?'A':'Area'), 'displayOrder'=>$locationOrder);
        $area = $row->area;
        if($useFormSC){
          $area = str_replace(' ','',$area);
          $area = str_replace('Zone','Z',$area);
          $area = str_replace('Out','O',$area);
        }
        $return['data']['area'] = $area;
        $return['colDefs']['subarea']=   array('field'=> 'subarea', 'displayName'=>($useFormSC?'SUBAREA':'Subarea'), 'displayOrder'=>$locationOrder+1);
        $return['data']['subarea'] = $row->subarea;
        $return['colDefs']['location']=   array('field'=> 'location','displayName'=>($useFormSC?'LOC':'Location'), 'displayOrder'=>$locationOrder+2);
        $return['data']['location'] = $row->location;
      }
    }
  }
  return $return;
}

function pullPayData($entryID) {
  global $wpdb;
  $return = array();
  $return['data'] = array();
  $return['colDefs'] = array();

  //add payment information
  if($entryID!=''){
    //get scheduling information for this lead
    $paysql =  "select  wp_rg_lead_meta.lead_id as pymt_entry,
                        wp_gf_addon_payment_transaction.transaction_type,
                        wp_gf_addon_payment_transaction.transaction_id,
                        wp_gf_addon_payment_transaction.amount,
                        wp_gf_addon_payment_transaction.date_created
                  from  wp_rg_lead_meta
                  left  outer join wp_gf_addon_payment_transaction
                        on wp_rg_lead_meta.lead_id = wp_gf_addon_payment_transaction.lead_id
                 where  meta_value = $entryID "
            . "    and wp_rg_lead_meta.meta_key like 'entry_id' "
            . "    and wp_gf_addon_payment_transaction.transaction_type='payment'";

    $payresults = $wpdb->get_results($paysql);
    if($wpdb->num_rows > 0){
      //add payment data to report
      $pay_det = '';
      $transaction_id = array();
      $amount = 0;
      $date_created = array();

      foreach($payresults as $payrow){
        $transaction_id[]= $payrow->transaction_id;   //payment transaction ID (from paypal)
        $amount          = $amount + $payrow->amount; //payment amt
        $date_created[]  = $payrow->date_created;     //payment date

        $payEntry = GFAPI::get_entry($payrow->pymt_entry);
        $payForm  = GFAPI::get_form($payEntry['form_id']);

        foreach($payForm['fields'] as $payFields){
          if($payFields['type']=='product'){
            if($payFields['inputType']=='singleproduct'){
              if(is_array($payFields['inputs'])){
                foreach($payFields['inputs'] as $input){
                  $pay_det .= $input['label'].': ';
                  $pay_det .= $payEntry[$input['id']]."\r";
                }
                $pay_det.= "\r";
              }
            }else{
              $pay_det.= $payFields['label'].': ';
              $pay_det.= $payEntry[$payFields['id']]."\r";
            }
          }
        }
      }
      //payment transaction ID (from paypal)
      $return['colDefs']['trx_id']=   array('field'=> 'trx_id','displayName'=>'Pay trxID');
      $return['data']['trx_id'] = implode("\r", $transaction_id);

      //payment amt
      $return['colDefs']['pay_amt']=   array('field'=> 'pay_amt','displayName'=>'Pay amount','cellFilter'=> 'currency');
      $return['data']['pay_amt'] = $amount;

      //payment date
      $return['colDefs']['pay_date']=   array('field'=> 'pay_date','displayName'=>'Pay date');
      $return['data']['pay_date'] = implode("\r", $date_created);

      //payment details
      $return['colDefs']['pay_det']=   array('field'=> 'pay_det','displayName'=>'Payment Details');
      $return['data']['pay_det'] = $pay_det;
    }
  }

  return $return;
}

/* Build your own report function */
function buildRpt($formSelect=array(),$formTypeArr=array(),$selectedFields=array(), $rmtData=array(),$location=false,$payment=false,$faire='', $status){
  global $wpdb;
  $forms = implode(",",$formSelect);

  $data['columnDefs'] = array();
  $data['columnDefs'][] = array('field'=>'entry_id');
  $data['columnDefs'][] = array('field'=>'form_id');
  $data['columnDefs'][] = array('field'=>'form_type');

  //TBD - remove duplicate field ID's
  $fieldArr   = array();
  $fieldIDArr = array();
  $combineFields = array();
  $acceptedOnly = true;

  //build list of categories
  $categories = get_categories(array( 'taxonomy' => 'makerfaire_category', 'hide_empty' => false ));
  foreach($categories as $category){
    $catCross[$category->term_id] = $category->name;
  }

  //build an array of selected fields
  foreach($selectedFields as $selFields){
    //build field array
    if($selFields->type=='checkbox' || $selFields->type=='radio' || $selFields->type=='select'){
      //remove everything after the period
      $baseField = strpos($selFields->id, ".") ? substr($selFields->id, 0, strpos($selFields->id, ".")) : $selFields->id;
      $fieldArr[$baseField][] = array('field'=>'field_'.str_replace('.','_',$selFields->id),
                                      'choice'=>$selFields->choices, 'type'=>$selFields->type, 'exact'=>(isset($selFields->exact)?$selFields->exact:''));
    }

    //create array of selected field id's
    $fieldIDArr[$selFields->id] = $selFields->id;
    if($selFields->type=='name'){
      foreach($selFields->inputs as $choice){
        $fieldIDArr[$choice->id] = $choice->id;
        $combineFields[ $selFields->id][]=$choice->id;
        //$data['columnDefs'][$choice->id] =   array('field'=> 'field_'.str_replace('.','_',$choice->id),'displayName'=>$selFields->label);
      }
    }

    //build grid columns. using the id as an index to avoid dups
    $data['columnDefs'][$selFields->id] =   array('field'=> 'field_'.str_replace('.','_',$selFields->id),'displayName'=>$selFields->label);
    if($selFields->id==303){
      if($selFields->choices!='Accepted'){
        $acceptedOnly = false;
      }
    }
  }

  $fieldIDquery = '';

  //build $fieldIDquery for sql
  foreach($fieldIDArr as $fieldID){
    if($fieldIDquery =='') {
      $fieldIDquery=" field_number like '".$fieldID."' ";
    }else{
      $fieldIDquery.=" or field_number like '".$fieldID."' ";
    }
  }

  $entryData = array();
  //find all entries given criteria
  $sql = "SELECT *
        FROM wp_rg_lead". ($faire!=''? ', wp_mf_faire':'') .
      " where wp_rg_lead.status='active' "
          . ($forms!=''? ' and wp_rg_lead.form_id in('.$forms.')':'')
          . ($faire!=''? ' and wp_mf_faire.id ='. $faire.' and FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0':'');

  //loop thru entry data and build array
  $entries = $wpdb->get_results($sql,ARRAY_A);
  //if($wpdb->num_rows > 0){
  foreach($entries as $entry){
    //pull form data and see if it matches the requested form type
    $formPull = GFAPI::get_form( $entry['form_id'] );
    $formType = (isset($formPull['form_type'])?$formPull['form_type']:'');

    //if certain form types were selected, only return those form types
    if(!empty($formTypeArr)){
      if(in_array($formType, $formTypeArr)){
        //continue with this record
      }else{
        continue; //skip this record
      }
    }

    $lead_id = $entry['id'];
    //pull entry specifc detail based on requested fields
    $detailSQL = "SELECT wp_rg_lead_detail.*,wp_rg_lead_detail_long.value as 'long value'
                  FROM wp_rg_lead_detail
                  left OUTER join wp_rg_lead_detail_long
                    ON wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id"
            . " where wp_rg_lead_detail.lead_id = $lead_id "
            . " and ($fieldIDquery) "
            . " ORDER BY lead_id asc, field_number asc";

    $entrydetail = $wpdb->get_results($detailSQL,ARRAY_A);
    foreach($entrydetail as $detail){
      //build data
      $entryData[$lead_id]['entry_id'] = $lead_id;
      $entryData[$lead_id]['form_id']  = $entry['form_id'];
      $entryData[$lead_id]['form_type']  = $formType;

      //field 320 is stored as category number, use cross reference to find text value
      if($detail['field_number']==320){
        $value = (isset($catCross[$detail['value']])?$catCross[$detail['value']]:$detail['value']);
      }else{
        $value = (isset($detail['long_value']) && $detail['long_value']!=''?$detail['long_value']:$detail['value']);
      }
      $value = htmlspecialchars_decode ($value);

      //build output for field data - format is field_55_4 for field id 55.4
      $entryData[$lead_id]['field_'.str_replace('.','_',$detail['field_number'])]  = $value;
    } //end entry detail loop
    //combine name fileds
    foreach($combineFields as $combFieldID=>$combFieldArr){
      $combinedField = '';
      foreach($combFieldArr as $combField){
        if(isset($entryData[$lead_id]['field_'.str_replace('.','_',$combField)])){
          if($combField!='')  $combinedField .= ' '.$entryData[$lead_id]['field_'.str_replace('.','_',$combField)];
        }
      }
      $entryData[$lead_id]['field_'.$combFieldID]= trim($combinedField);
    }
  } //end nentries loop

  $colDefs2Sort = array();
  foreach($entryData as $entryID=>$dataRow){
    if(!empty($fieldArr)){
      // check selected checkbox and radio fields.
      // If at least one of the selections are not there, we need to skip this entry
      foreach($fieldArr as $field){
        $remove = true; //default to remove this entry
        foreach($field as $fieldRow) {
          //radio and select boxes must match one of the passed values
          if($fieldRow['type']=='radio' || $fieldRow['type']=='select'){ //check value
            if(isset($fieldRow['exact'])&&$fieldRow['exact']==true){
              if(isset($dataRow[$fieldRow['field']])){
                if($dataRow[$fieldRow['field']] == $fieldRow['choice']){
                  $remove = false;
                }
              }
            }else{ //not set to exact, then pass the field
              $remove = false;
            }
          } else{ //just include checkbox and text fields
            $remove = false;
          }
        } //end loop thru field
        //does this entry meet the field criteria?  no, exclude the record
        if($acceptedOnly && $dataRow['field_303']!='Accepted')  $remove = true;

        //Did they pass the criteria for this field?
        // If not, then we need to stop processing fields and drop this entry
        if($remove){
          //echo 'break on '.$entryID.'<br/>';
          break; //exit the field loop
        }
      }
      if($remove){
        //echo 'skipping - '.$entryID.'<br/><br/><br/>';
        unset ($entryData[$entryID]);
        continue; //skip this entry
      }
    }

    //if we passed the field criteria, let's now include the other items such as RMT, location and payment info
    $incComments = false;
    //pull RMT data
    foreach($rmtData as $type=>$rmt){
      if($type=='comments'){
        $incComments = $rmt;
        $rmt = array();
      }

      //$key holds the type - resource,attribute, attention, meta
      foreach($rmt as $selRMT){
        //all data should be checked at this point, but double check here to be safe
        if($selRMT->checked){
          //echo 'looking for '.$type.' of '.$selRMT->id.'<br/>';
        }
        if($type=='resource'){
          if($selRMT->id!='all'){
            $sql = 'SELECT qty,type,comment, token '
                . ' FROM `wp_rmt_entry_resources`, wp_rmt_resources '
                . ' where resource_id = wp_rmt_resources.ID and'
                    . ' resource_category_id = '.$selRMT->id .' and'
                    . ' entry_id ='.$entryID;
          }else{
            $sql = 'SELECT qty, concat(type, " ", wp_rmt_resource_categories.category) as type, comment '
                  . 'FROM `wp_rmt_entry_resources`, wp_rmt_resources, wp_rmt_resource_categories '
                  . ' where resource_id = wp_rmt_resources.ID and'
                  . ' resource_category_id = wp_rmt_resource_categories.ID and'
                  . ' entry_id ='.$entryID;
          }
          //loop thru data
          $resources = $wpdb->get_results($sql,ARRAY_A);
          $entryRes = array();
          if(isset($selRMT->aggregated) && $selRMT->aggregated==false){
            foreach($resources as $resource){
              $colDefs2Sort['res_'.$resource['token']] =   array('field'=> 'res_'.$resource['token'],'displayName'=>$resource['token']);
              $colDefs2Sort['res_'.$resource['token'].'_comment']  = array('field'=> 'res_'.$resource['token'].'_comment','displayName'=>$resource['token'].' - comment');
              $entryData[$entryID]['res_'.$resource['token']] = $resource['qty'];
              $entryData[$entryID]['res_'.$resource['token'].'_comment'] = $resource['comment'];
            }
          }else{
            foreach($resources as $resource){
              $comment = ($incComments && $resource['comment']!=''?" (".$resource['comment'].")":'');
              $entryRes[] = $resource['qty'] .' : '.$resource['type'].$comment;
            }
            $data['columnDefs']['res_'.$selRMT->id]=   array('field'=> 'res_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
            $entryData[$entryID]['res_'.$selRMT->id] = implode(', ',$entryRes);
          }
        }
        if($type=='attribute'){
          if($selRMT->id!='all'){
            $sql = 'select value from wp_rmt_entry_attributes where entry_id ='.$entryID.' and attribute_id='.$selRMT->id;
          }else{
            $sql = 'select concat(category," ",value) as value from wp_rmt_entry_attributes,wp_rmt_entry_att_categories where '
                    . ' entry_id ='.$entryID
                    . ' and attribute_id= wp_rmt_entry_att_categories.ID';
          }

          //loop thru data
          $attributes = $wpdb->get_results($sql,ARRAY_A);
          $entryAtt = array();

          foreach($attributes as $attribute){
            $entryAtt[] = $attribute['value'];
          }
          $data['columnDefs']['att_'.$selRMT->id]=   array('field'=> 'att_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['att_'.$selRMT->id] = implode(', ',$entryAtt);
        }

        //attention fields
        if($type=='attention'){
          if($selRMT->id!='all'){
            $sql = 'select comment from wp_rmt_entry_attn where entry_id ='.$entryID.' and attn_id='.$selRMT->id;
          }else{
            $sql = 'select concat(wp_rmt_attn.value," ",comment) as comment from wp_rmt_entry_attn,wp_rmt_attn where '
                    . ' entry_id ='.$entryID
                    . ' and attn_id= wp_rmt_attn.ID';
          }
          //loop thru data
          $attentions = $wpdb->get_results($sql,ARRAY_A);
          $entryAttn = array();

          foreach($attentions as $attention){
            $entryAttn[] = $attention['comment'];
          }
          $data['columnDefs']['attn_'.$selRMT->id]=   array('field'=> 'attn_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['attn_'.$selRMT->id] = implode(', ',$entryAttn);
        }

          //meta fields
        if($type=='meta'){
          $sql = "SELECT meta_value FROM `wp_rg_lead_meta` where meta_key = '$selRMT->id' and lead_id =".$entryID;

          //loop thru data
          $metas = $wpdb->get_results($sql,ARRAY_A);
          $entryMeta = array();

          foreach($metas as $meta){
            $entryMeta[] = $meta['meta_value'];
          }
          $data['columnDefs']['meta_'.$selRMT->id]=   array('field'=> 'meta_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['meta_'.$selRMT->id] = implode(', ',$entryMeta);
        }
      }
    }//end RMT

    //schedule information
    if($location){
      global $wpdb;
      if($entryID!=''){
        //get scheduling information for this lead
        $sql = "SELECT  area.area,subarea.subarea,subarea.nicename, location.location
                FROM wp_mf_location location,
                        wp_mf_faire_subarea subarea,
                        wp_mf_faire_area area

                where       location.entry_id   = $entryID
                        and subarea.id          = location.subarea_id
                        and area.id             = subarea.area_id";

        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){
          foreach($results as $row){
            $subarea = ($row->nicename!=''&&$row->nicename!=''?$row->nicename:$row->subarea);

            $data['columnDefs']['area']=   array('field'=> 'area','displayName'=>'Area');
            $entryData[$entryID]['area'] = $row->area;
            $data['columnDefs']['subarea']=   array('field'=> 'subarea','displayName'=>'Subarea');
            $entryData[$entryID]['subarea'] = $row->subarea;
            $data['columnDefs']['location']=   array('field'=> 'location','displayName'=>'Location');
            $entryData[$entryID]['location'] = $row->location;
          }
        }
      }
    } //end location

    //add payment information
    if($payment){
      if($entryID!=''){
        //get scheduling information for this lead
        $paysql = "select  wp_rg_lead_meta.lead_id as pymt_entry,
                  wp_gf_addon_payment_transaction.transaction_type,
                  wp_gf_addon_payment_transaction.transaction_id,
                  wp_gf_addon_payment_transaction.amount,
                  wp_gf_addon_payment_transaction.date_created
                from wp_rg_lead_meta
                left outer join wp_gf_addon_payment_transaction on wp_rg_lead_meta.lead_id = wp_gf_addon_payment_transaction.lead_id
                where meta_value = $entryID and wp_rg_lead_meta.meta_key like 'entry_id' and wp_gf_addon_payment_transaction.transaction_type='payment'";

        $payresults = $wpdb->get_results($paysql);
        if($wpdb->num_rows > 0){
          //add payment data to report
          foreach($payresults as $payrow){
            //payment transaction ID (from paypal)
            $data['columnDefs']['trx_id']=   array('field'=> 'trx_id','displayName'=>'Pay trxID');
            $entryData[$entryID]['trx_id'] = $payrow->transaction_id;

            //payment amt
            $data['columnDefs']['pay_amt']=   array('field'=> 'pay_amt','displayName'=>'Pay amount');
            $entryData[$entryID]['pay_amt'] = $payrow->amount;

            //payment amt
            $data['columnDefs']['pay_date']=   array('field'=> 'pay_date','displayName'=>'Pay date');
            $entryData[$entryID]['pay_date'] = $payrow->date_created;
            $payEntry = GFAPI::get_entry($payrow->pymt_entry);

            $payForm = GFAPI::get_form($payEntry['form_id']);
            $pay_det = '';
            foreach($payForm['fields'] as $payFields){
              if($payFields['type']=='product'){
                if($payFields['inputType']=='singleproduct'){
                  if(is_array($payFields['inputs'])){
                    foreach($payFields['inputs'] as $input){
                      $pay_det .= $input['label'].': ';
                      $pay_det .= $payEntry[$input['id']]." "."\n";
                    }
                    $pay_det.= " "."\n";
                  }
                }else{
                  $pay_det.= $payFields['label'].': ';
                  $pay_det.= $payEntry[$payFields['id']]." "."\n";
                }
              }
            }
            //payment details
            $data['columnDefs']['pay_det']=   array('field'=> 'pay_det','displayName'=>'Payment Details');
            $entryData[$entryID]['pay_det'] = $pay_det;
          }
        }
      }
    }
  } //end entry data loop

  foreach($entryData as $row){
    $data['data'][] = $row;
  }
  //sort $colDefs2Sort array by displayName
  usort($colDefs2Sort, function($a, $b) {
    return strcmp($a["displayName"], $b["displayName"]);
  });
  $data['columnDefs'] = array_merge($data['columnDefs'],$colDefs2Sort);
  //reindex columnDefs as the grid will blow up if the indexes aren't in order
  $data['columnDefs'] = array_values($data['columnDefs']);

  echo json_encode($data);
  exit;
}

function retrieveRptData($table,$faire){
  global $wpdb; global $tableFields;
  $sql   = '';
  $where = '';
  $orderBy = '';
  //build columnDefs
  foreach($tableFields[$table] as $fields){
    if(isset($fields['orderBy']))
      $orderBy = ' order by '.$fields['orderBy'];
    if(isset($fields['dataSql'])) $sql   .= ','.$fields['dataSql'];
    if(isset($fields['limit'])){
      if($where==''){
        $where .= ' where ';
      }else{
        $where .= ' and ';
      }
      $where .= $fields['fieldName'] .' '. $fields['limit']['opt'].' '.$fields['limit']['value'];
    }
    $vars = array();
    switch($fields['filterType']){
      case 'dropdown':
        $options = array();  $selectOptions=array();
        //retrieve dropdown info
        if(isset($fields['fkey'])){
          $fkeyData      = getFkeyData($fields['fkey'],$fields['fieldName']);
          $options       = $fkeyData[0];
          $selectOptions = $fkeyData[1];
          //additional select options outside of fkey
          if(isset($fields['options'])){
            foreach($fields['options'] as $optKey=>$option){
              $options[]       = array('id'    => $optKey, 'fkey'   => $option);
              $selectOptions[] = array('value' => $optKey, 'label' => $option);
            }
          }

        }else{
          //use defined options`
          foreach($fields['options'] as $optKey=>$option){
            $options[]       = array('id'    => $optKey, 'fkey'   => $option);
            $selectOptions[] = array('value' => $optKey, 'label' => $option);
          }
        }

        //sort options by fkey and selected options by label
        //usort($options, "cmpfkey");
        //usort($selectOptions, "cmpval");

        $vars = array('displayName'=> (isset($fields['fieldLabel'])?$fields['fieldLabel']:$fields['fieldName']),
                      'filter'=> array('selectOptions'=>$selectOptions),
                      'cellFilter'               => 'griddropdown:this',
                      'editDropdownOptionsArray' => $options
                      );
        break;
      case 'entrylink':
        $vars = array('cellTemplate'=>'<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id=9&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>');
        break;
      case 'hidden':
        $vars = array('visible'=>false);
        break;
      case 'custom':
      case 'number':
      case 'text':
      default:
        break;
    }
    if(isset($fields['cellTooltip']))   $vars['cellTooltip']  = $fields['cellTooltip'];
    if(isset($fields['cellTemplate']))  $vars['cellTemplate'] = $fields['cellTemplate'];
    if(isset($fields['cellFilter']))    $vars['cellFilter']   = $fields['cellFilter'];
    if(isset($fields['visible']))       $vars['visible']      = $fields['visible'];
    if(isset($fields['type']))          $vars['type']         = $fields['type'];

    $vars['name']     = $fields['fieldName'];
    $vars['minWidth'] = 100;
    $vars['width']    = (isset($fields['width'])?$fields['width']:'*');
    $columnDefs[] = $vars;
  }

  //build data
  $data['columnDefs'] = $columnDefs;

  //get table data
  $query = "select * ".$sql." from ".$table.$where.$orderBy;

  //loop thru entry data and build array
  $result = $wpdb->get_results($query,ARRAY_A);

  //create array of table data
  foreach($result as $row){
    if(isset($row['faire']) && $faire!='' && $row['faire']==$faire){
      $data['data'][]= $row;
    }
  }
  echo json_encode($data);
  exit;
}

function invalidRequest($message='') {
	$data = array();
	$data['success'] = false;
	$data['message'] = ($message!=''?$message:"Invalid request.");
	echo json_encode($data);
	exit;
}

function getFkeyData($tabFkeyData,$fkey){
  global $mysqli;
  $referenceTable   = $tabFkeyData['referenceTable'];
  $referenceField   = $tabFkeyData['referenceField'];
  $referenceDisplay = $tabFkeyData['referenceDisplay'];
  //build options drop down
  $options = array();
  $selectOptions = array();
  $optionquery = "select " . $referenceField . ", " . $referenceDisplay . " from "   . $referenceTable." order by ".$referenceDisplay."  asc";
  //echo $optionquery;
  $result = $mysqli->query( $optionquery );
  while ($row = $result->fetch_assoc()) {
    $options[]       = array('id'    => intval($row[$referenceField]), 'fkey'  => $row[$referenceDisplay]);
    $selectOptions[] = array('value' => intval($row[$referenceField]), 'label' => $row[$referenceDisplay]);
  }
  return(array($options,$selectOptions));
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
function getBuildRptData(){
  global $mysqli;
  $data = array();
  //return form data
  $formReturn = array();
  $forms = RGFormsModel::get_forms( null, 'title' );
  foreach ( $forms as $form ) {
    //exclude master form
    if($form->id!=9){
      $formReturn[] = array('id'=>absint( $form->id ), 'name' => htmlspecialchars_decode( $form->title));
    }
  }
  $form = RGFormsModel::get_form_meta( 9 );

  //field list (from form 9)
  $fieldReturn = array();
  $sql = 'select display_meta from wp_rg_form_meta where form_id=9';
  $result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    $json = json_decode($row['display_meta']);
    $jsonArray = (array) $json->fields;
    foreach($jsonArray as &$array){
      $array->id = (float) $array->id;
      $array = (array) $array;
    }
    usort($jsonArray, "cmp");
    $fieldData = array();
    $fieldData[]='entry id';
    foreach($jsonArray as $field){
      switch ($field['type']) {
        case 'html':
        case 'section':
        case 'page':
          break;
        default:
          $fieldLabel = (isset($field['label']) ? $field['label']:'');
          $label = (isset($field['adminLabel']) && $field['adminLabel'] != '' ? $field['adminLabel'] : $fieldLabel);

          if($field['type']=='checkbox'||$field['type']=='radio'||$field['type']=='select'||$field['type']=='address'){
            if(isset($field['inputs']) && !empty($field['inputs'])){
              foreach($field['inputs'] as $choice){
                $label = ($label!=''?$label:$choice->label);
                $fieldReturn[] = array('id' => $choice->id, 'label' => $label, 'choices'=>$choice->label,'type'=>$field['type']);
              }
            }else{
              foreach($field['choices'] as $choice){
                $label = ($label!=''?$label:$choice->value);
                $fieldReturn[] = array('id' => $field['id'], 'label' => $label, 'choices'=>$choice->value,'type'=>$field['type']);
              }
            }
          }else{
            $inputs = ($field['type']=='name'?$field['inputs']:'');
            $fieldReturn[] = array('id'     => $field['id'], 'label' => $label, 'choices'=>'','type'=>$field['type'],'inputs'=>$inputs );
          }

      }
    }
  }

  //RMT fields
  //resources
  $sql = 'SELECT * FROM `wp_rmt_resource_categories`'; //ID, category
  $result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  $data['rmt']['resource'][]=array('id'=>'all','value'=>'All Resources');
  while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    $data['rmt']['resource'][]=array('id'=>$row['ID'],'value'=>$row['category']);
  }

  //attributes
  $data['rmt']['attribute'][]=array('id'=>'all','value'=>'All Attributes');
  $sql = 'SELECT * FROM `wp_rmt_entry_att_categories`'; //returns ID, category, token
  $result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    $data['rmt']['attribute'][]=array('id'=>$row['ID'],'value'=>$row['category']);
  }

  //attention
  $data['rmt']['attention'][]=array('id'=>'all','value'=>'All Attention');
  $sql = 'SELECT * FROM `wp_rmt_attn`'; //returns ID, value, token
  $result = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  while ( $row = $result->fetch_array(MYSQLI_ASSOC) ) {
    $data['rmt']['attention'][]=array('id'=>$row['ID'],'value'=>$row['value']);
  }

  //meta fields
  $data['rmt']['meta'][]=array('id'=>'res_status', 'type'=>'meta','value'=>'Resource Status');
  $data['rmt']['meta'][]=array('id'=>'res_assign', 'type'=>'meta','value'=>'Resource Assign To');
  //$data['rmt']['meta'][]=array('id'=>$row['entryRating'],'type'=>'meta','value'=>'Entry Rating');
  //$data['rmt']['meta'][]=array('id'=>$row['entry_id'],'type'=>'meta','value'=>'Linked to Entry');

  $data['forms']  = $formReturn;
  $data['fields'] = $fieldReturn;
  echo json_encode($data);
  exit;
}

//this function cross references faire entries to their assigned resources and attributes
function ent2resource($table, $faire){
  global $wpdb;
  $data = array();
  $columnDefs = array();

  //find all non trashed entries for selected faires
    $sql = "select wp_rg_lead.id as 'entry_id', wp_rg_lead.form_id, wp_mf_faire.faire,
              (select value from wp_rg_lead_detail where wp_rg_lead_detail.lead_id = wp_rg_lead.id and field_number=303) as status,
              (select value from wp_rg_lead_detail where wp_rg_lead_detail.lead_id = wp_rg_lead.id and field_number=151) as proj_name,
              (select area from wp_mf_faire_area, wp_mf_faire_subarea where wp_mf_faire_subarea.id = subarea_id and wp_mf_faire_subarea.area_id = wp_mf_faire_area.id) as area,
              wp_mf_faire_subarea.subarea,
              wp_mf_faire_area.area,
              wp_mf_location.location, wp_mf_location.id as location_id
            from wp_rg_lead
              left outer join wp_mf_faire          on find_in_set (wp_rg_lead.form_id,wp_mf_faire.form_ids) > 0
              left outer join wp_mf_location       on wp_mf_location.entry_id = wp_rg_lead.id
              left outer join wp_mf_faire_subarea  on wp_mf_location.subarea_id = wp_mf_faire_subarea.id
              left outer join wp_mf_faire_area     on wp_mf_faire_subarea.area_id = wp_mf_faire_area.id
            where status = 'active' and
                  faire is not NULL and
                  form_id!=1 and form_id!=9 and
                  wp_mf_faire.ID=".$faire.
          " order by wp_rg_lead.id asc";

  //loop thru entry data and build array
  $entries = $wpdb->get_results($sql,ARRAY_A);

  $entryData = array();
  $resArray  = array();
  $attArray  = array();
  $attnArray = array();

  foreach($entries as $entry){
    if($entry['status'] !='Accepted' && $entry['status']!='Proposed') continue; //skip this record

    $dbdata = array();
    //set basic data
    $dbdata['entry_id']   = $entry['entry_id'];
    $dbdata['form_id']    = $entry['form_id'];
    //pull form data and see if it matches the requested form type
    $formPull = GFAPI::get_form( $entry['form_id'] );
    $formType = (isset($formPull['form_type'])?$formPull['form_type']:'');

    //do not return Presenation records
    if($formType=='Presentation') continue; //skip this record

    $dbdata['form_type']  = $formType;
    $dbdata['faire']      = $entry['faire'];
    $dbdata['status']     = $entry['status'];
    $dbdata['proj_name']  = $entry['proj_name'];

    //set location data
    if($entry['location_id'] != NULL){
      //set resource data
      $dbdata['location'] = array('subarea'=> $entry['subarea'], 'location'=>$entry['location'], 'area'=>$entry['area']);
    }


    //pull resource information
    $resSql = "select wp_rmt_entry_resources.resource_id,
                      wp_rmt_entry_resources.qty as 'resource_qty',
                      wp_rmt_entry_resources.comment as 'resource_comment',
                      token as res_label
          from wp_rmt_entry_resources,wp_rmt_resources where wp_rmt_entry_resources.entry_id = ".$entry['entry_id']." and wp_rmt_resources.id = resource_id";
    $resources = $wpdb->get_results($resSql,ARRAY_A);
    foreach($resources as $resource){
      $dbdata['resource'][$resource['resource_id']] = array('qty'=> $resource['resource_qty'], 'comment'=>$resource['resource_comment']);
      //add resource to resource array
      $resArray[$resource['resource_id']] = $resource['res_label'];
    }

    // pull attribute data
    $attSql = "select wp_rmt_entry_attributes.attribute_id,
                      wp_rmt_entry_attributes.value as 'attribute_value',
                      wp_rmt_entry_attributes.comment as 'attribute_comment',
                      token  as att_label
            from wp_rmt_entry_attributes, wp_rmt_entry_att_categories
            where wp_rmt_entry_attributes .entry_id = ".$entry['entry_id']." and
            wp_rmt_entry_att_categories.id = attribute_id";
    $attributes = $wpdb->get_results($attSql,ARRAY_A);
    foreach($attributes as $attribute){
      //set resource data
      $dbdata['attribute'][$attribute['attribute_id']] = array('value'=> $attribute['attribute_value'], 'comment'=>$attribute['attribute_comment']);
      //add attribute to attribute array
      $attribute[$attribute['attribute_id']] = $attribute['att_label'];
    }

    // pull attention data
    $attnSql = "select attn_id as 'attn_id', comment as 'attn_comment', wp_rmt_attn.value as attn_type
                from wp_rmt_entry_attn, wp_rmt_attn
                where wp_rmt_attn.id = attn_id and wp_rmt_entry_attn.entry_id = ".$entry['entry_id'];
    $attentions = $wpdb->get_results($attnSql,ARRAY_A);
    foreach($attentions as $attention){
      //set resource data
      $dbdata['attention'][$attention['attn_id']] = array('comment'=>$attention['attn_comment']);
      //add attribute to attribute array
      $attnArray[$attention['attn_id']] = $attention['attn_type'];
    }

    $data[$entry['entry_id']] = $dbdata;
  }

  //default columns
  $columnDefs[] = array('field' => 'faire', 'displayName'=>'Faire', 'width'=>'50');
  $columnDefs[] = array('field' => 'status', 'displayName'=>'Status', 'width'=>'100','sort'=> array('direction'=> 'uiGridConstants.ASC','priority'=> 0), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'entry_id', 'displayName'=>'Entry ID', 'width'=>'75');
  $columnDefs[] = array('field' => 'form_type', 'displayName'=> 'Form Type', 'width'=>'150');
  $columnDefs[] = array('field' => 'proj_name', 'displayName'=>'Entry Name', 'width'=>'*');


  $columnDefs[] = array('field' => 'location.area', 'displayName'=>'Area', 'sort'=> array('direction'=> 'uiGridConstants.ASC', 'priority'=> 1), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'location.subarea', 'displayName'=>'Subarea', 'sort'=> array('direction'=> 'uiGridConstants.ASC', 'priority'=> 3), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'location.location', 'displayName'=>'Location','sort'=> array('direction'=> 'uiGridConstants.ASC', 'priority'=> 2), 'enableSorting'=> true);

  $resArray  = array_unique($resArray);
  $attArray  = array_unique($attArray);
  $attnArray = array_unique($attnArray);
  $colDefs2Sort = array();
  foreach($resArray as $key=>$resource){
   $colDefs2Sort[] = array('displayName' => $resource, 'field'=> 'resource.'.$key.'.qty');
   $colDefs2Sort[] = array('displayName' => $resource .' - comment', 'field'=> 'resource.'.$key.'.comment');
  }
  foreach($attArray as $key=>$attribute){
   $colDefs2Sort[] = array('displayName' => $attribute, 'field'=> 'attribute.'.$key.'.value');
   $colDefs2Sort[] = array('displayName' => $attribute .' - comment', 'field'=> 'attribute.'.$key.'.comment');
  }
  foreach($attnArray as $key=>$attention){
   $colDefs2Sort[] = array('displayName' => $attention, 'field'=> 'attention.'.$key.'.comment');
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
  $columnDefs = array_merge($columnDefs,$colDefs2Sort);
  $retData['data']        = $data;
  $retData['columnDefs']  = $columnDefs;
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
    "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
    "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
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
  $chr = array_keys  ($chr_map); // but: for efficiency you should
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
  $sql = "SELECT    tasks.lead_id, tasks.created, tasks.completed, tasks.description, tasks.required, meta.meta_value,meta.lead_id as other_entry,
    (select value from wp_rg_lead_detail where field_number = 151 and lead_id = tasks.lead_id) as project_name,
    (select form_id from wp_rg_lead where id = tasks.lead_id) as form_id
          FROM      wp_mf_entity_tasks AS tasks
          LEFT JOIN wp_rg_lead_meta AS meta
                 ON meta.`form_id` = $formSelect
                AND meta.meta_key = 'entry_id'
                AND tasks.lead_id = meta.meta_value
          WHERE     tasks.`form_id` = $formSelect
          UNION ALL
          SELECT    NULL, NULL, NULL, NULL, NULL, meta_value,lead_id as other_entry,
          (select value from wp_rg_lead_detail where field_number = 151 and lead_id = meta_value) as project_name,
          (select form_id from wp_rg_lead where id = meta_value) as form_id
          FROM      wp_rg_lead_meta
          WHERE     meta_value NOT IN
                    (SELECT  lead_id FROM    wp_mf_entity_tasks)
                AND `form_id` = $formSelect
                AND meta_key = 'entry_id'";

  $result = $wpdb->get_results($sql);

  $data['columnDefs'] = array(
      array("name"=>"lead_id","displayName"=>"Entry","width"=>"65",
          cellTemplate=> '<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id=9&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>'
          ),
      array("name"=>"project_name","displayName"=>"Project Name","width"=>"300"),
      array("name"=>"created","width"=>"150"),
      array("name"=>"completed","width"=>"150"),
      array("name"=>"description","width"=>"150"),
      array("name"=>"required","displayName"=>"Required","width"=>"100"),
      array("name"=>"not_assigned","displayName"=>"Not Assigned"),
      array("name"=>"other_entry","displayName"=>"Other Entry ID",
          cellTemplate=> '<div class="ui-grid-cell-contents"><a href="/wp-admin/admin.php?page=gf_entries&view=entry&id=9&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>'
          ),
  );

  //create array of table data
  foreach($result as $row){
    if($row->lead_id == NULL && $row->meta_value != NULL){
      $not_assigned = 'Yes';
      $lead_id = $row->meta_value;
    }else {
      $not_assigned = '';
      $lead_id = $row->lead_id;
    }
    $data['data'][] = array('lead_id'     => $lead_id,
                            'project_name' => $row->project_name,
                            'created'     => $row->created,
                            'completed'   => $row->completed,
                            'description' => $row->description,
                            'required'    => ($row->required==1?'Yes':'No'),
                            'not_assigned' => $not_assigned,
                            'other_entry'  => $row->other_entry
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
  $value = str_replace("'",'',$value);
  $value = str_replace(' x ','x',$value);

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
  $value = str_replace('inside','In',$value);
  $value = str_replace('outside','Out',$value);
  $value = str_replace('either','i/o',$value);
  $value = str_replace(' under a','',$value);
  $value = str_replace(' under','',$value);
  $value = str_replace('tents','tent',$value);
  $value = str_replace('tent','Tent',$value);
  $value = str_replace('dark','Dark',$value);
  $value = str_replace('large','Lg',$value);
  $value = str_replace('either','i/o',$value);
  $value = str_replace('?','Out on grass',$value);

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