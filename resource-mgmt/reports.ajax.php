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
$selectedFields = (isset($obj->selectedFields)?$obj->selectedFields:'');
$rmtData        = (isset($obj->rmtData)?$obj->rmtData:'');
$location       = (isset($obj->location)?$obj->location:false);
if($type != ''){
  if($type =="tableData"){
    if($table=='formData'){
      getBuildRptData();
    }else{
      //build report data
      retrieveRptData($table);
    }
  }elseif($type =="customRpt"){
    if($formSelect != '' && $selectedFields!=''){
        //build report
        buildRpt($formSelect,$selectedFields,$rmtData,$location);
    }else{
      invalidRequest('Error: Form or Fields not selected');
    }
  }else{
    invalidRequest('Invalid Request type');
  }
}else{
	invalidRequest('Request Type Not Sent');
}

/* Build your own report function */
function buildRpt($formSelect=array(),$selectedFields=array(), $rmtData=array(),$location=false){
  global $mysqli;
  $forms = implode(",",$formSelect);
  $data['columnDefs'] = array();
  $data['columnDefs'][] = array('field'=>'entry_id');
  $data['columnDefs'][] = array('field'=>'form_id');
  $data['columnDefs'][] = array('field'=>'form_type');

  //TBD - remove duplicate field ID's
  $fieldArr   = array();
  $fieldIDArr = array();

  //build an array of selected fields
  foreach($selectedFields as $selFields){
    //build field array
    if(isset($selFields->choices) && $selFields->choices!=''){
      $fieldArr[$selFields->id][] = $selFields->choices;
    }
    //create array of selected field id's
    $fieldIDArr[$selFields->id] = $selFields->id;
    if($selFields->type=='name'){
      foreach($selFields->inputs as $choice){
        $fieldIDArr[$choice->id] = array('fieldID'=>$selFields->id );
      }
    }

    //build grid columns. using the id as an index to avoid dups
    $data['columnDefs'][$selFields->id] =   array('field'=> 'field_'.str_replace('.','_',$selFields->id),'displayName'=>$selFields->label);
  }

  //entry data
  $sql = "SELECT wp_rg_lead_detail.*,wp_rg_lead_detail_long.value as 'long value'
        FROM wp_rg_lead
          left outer join wp_rg_lead_detail
            on wp_rg_lead_detail.lead_id = wp_rg_lead.id
          left OUTER join wp_rg_lead_detail_long
            ON wp_rg_lead_detail.id = wp_rg_lead_detail_long.lead_detail_id
        where wp_rg_lead.form_id in($forms)
        and wp_rg_lead.status='active'
        ORDER BY lead_id asc, field_number asc";

  //loop thru entry data and build array
  $entries = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
  $entryData = array();
  foreach($entries as $entry){
    //if name field
    if(isset($fieldIDArr[$entry['field_number']])){
      //field 320 is stored as category number, use cross reference to find text value
      if($entry['field_number']==320){
        $value = (isset($catCross[$entry['value']])?$catCross[$entry['value']]:$entry['value']);
      }else{
        $value = (isset($entry['long_value']) && $entry['long_value']!=''?$entry['long_value']:$entry['value']);
      }
      $value = htmlspecialchars_decode ($value);
      $entryData[$entry['lead_id']]['entry_id'] = $entry['lead_id'];
      $entryData[$entry['lead_id']]['form_id']  = $entry['form_id'];
      $formPull = GFAPI::get_form( $entry['form_id'] );
      $entryData[$entry['lead_id']]['form_type']  = (isset($formPull['form_type'])?$formPull['form_type']:'');
      if(is_array($fieldIDArr[$entry['field_number']])){
        $fieldID = $fieldIDArr[$entry['field_number']]['fieldID'];
        $setValue = (isset($entryData[$entry['lead_id']]['field_'.$fieldID])?$entryData[$entry['lead_id']]['field_'.$fieldID].' '.$value: $value);
        $entryData[$entry['lead_id']]['field_'.$fieldID]  = $setValue;
      }else{
        $entryData[$entry['lead_id']]['field_'.str_replace('.','_',$entry['field_number'])]  = $value;
      }
    }
  }

  foreach($entryData as $entryID=>$dataRow){
    //pull RMT data
    foreach($rmtData as $type=>$rmt){
      //$key holds the type - resource,attribute, attention, meta
      foreach($rmt as $selRMT){
        //all data should be checked at this point, but double check here to be safe
        if($selRMT->checked){
          //echo 'looking for '.$type.' of '.$selRMT->id.'<br/>';
        }
        if($type=='resource'){
          if($selRMT->id!='all'){
            $sql = 'SELECT qty,type FROM `wp_rmt_entry_resources`, wp_rmt_resources '
                    . ' where resource_id = wp_rmt_resources.ID and'
                    . ' resource_category_id = '.$selRMT->id .' and'
                    . ' entry_id ='.$entryID;
          }else{
            $sql = 'SELECT qty, concat(type, " ", wp_rmt_resource_categories.category) as type '
                  . 'FROM `wp_rmt_entry_resources`, wp_rmt_resources, wp_rmt_resource_categories '
                  . ' where resource_id = wp_rmt_resources.ID and'
                  . ' resource_category_id = wp_rmt_resource_categories.ID and'
                  . ' entry_id ='.$entryID;
          }
          //loop thru data
          $resources = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
          $entryRes = array();

          foreach($resources as $resource){
            $entryRes[] = $resource['qty'] .' : '.$resource['type'];
          }
          $data['columnDefs']['res_'.$selRMT->id]=   array('field'=> 'res_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['res_'.$selRMT->id] = implode(', ',$entryRes);
        }
        if($type=='attribute'){
          if($selRMT->id!='all'){
            $sql = 'select value from wp_rmt_entry_attributes where entry_id ='.$entry['lead_id'].' and attribute_id='.$selRMT->id;
          }else{
            $sql = 'select concat(category," ",value) as value from wp_rmt_entry_attributes,wp_rmt_entry_att_categories where '
                    . ' entry_id ='.$entryID
                    . ' and attribute_id= wp_rmt_entry_att_categories.ID';
          }
          //loop thru data
          $attributes = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
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
          $attentions = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
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
          $metas = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
          $entryMeta = array();

          foreach($metas as $meta){
            $entryMeta[] = $meta['meta_value'];
          }
          $data['columnDefs']['meta_'.$selRMT->id]=   array('field'=> 'meta_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['meta_'.$selRMT->id] = implode(', ',$entryMeta);
        }
      }
    }

    //schedule information
    if($location){
      global $wpdb;
      if($entryID!=''){
        //get scheduling information for this lead
        $sql = "SELECT  area.area,subarea.subarea,subarea.nicename, location.location,
                        schedule.start_dt, schedule.end_dt
                FROM    wp_mf_schedule schedule,
                        wp_mf_location location,
                        wp_mf_faire_subarea subarea,
                        wp_mf_faire_area area

                where       schedule.entry_id   = $entryID
                        and schedule.location_id=location.ID
                        and location.entry_id   = schedule.entry_id
                        and subarea.id          = location.subarea_id
                        and area.id             = subarea.area_id";

        $results = $wpdb->get_results($sql);
        if($wpdb->num_rows > 0){
          foreach($results as $row){
            $subarea = ($row->nicename!=''&&$row->nicename!=''?$row->nicename:$row->subarea);
            $start_dt = strtotime($row->start_dt);
            $end_dt   = strtotime($row->end_dt);

            $data['columnDefs']['area']=   array('field'=> 'area','displayName'=>'Area');
            $entryData[$entryID]['area'] = $row->area;
            $data['columnDefs']['subarea']=   array('field'=> 'subarea','displayName'=>'Subarea');
            $entryData[$entryID]['subarea'] = $row->subarea;
            $data['columnDefs']['location']=   array('field'=> 'location','displayName'=>'Location');
            $entryData[$entryID]['location'] = $row->location;
            $data['columnDefs']['start']=   array('field'=> 'start','displayName'=>'Start');
            $entryData[$entryID]['start'] = date("l, n/j/y, g:i A",$start_dt);
            $data['columnDefs']['end']=   array('field'=> 'end','displayName'=>'End');
            $entryData[$entryID]['end'] = date("l, n/j/y, g:i A",$end_dt);
          }
        }
      }
    }
  }

  foreach($entryData as $row){
    $data['data'][] = $row;
  }

  //reindex columnDefs as the grid will blow up if the indexes aren't in order
  $data['columnDefs'] = array_values($data['columnDefs']);

  echo json_encode($data);
  exit;
}

function retrieveRptData($table){
  global $mysqli;global $tableFields;
  $sql   = '';
  $where = '';

  //build columnDefs
  foreach($tableFields[$table] as $fields){
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
          //use defined options
          foreach($fields['options'] as $optKey=>$option){
            $options[]       = array('id'    => $optKey, 'fkey'   => $option);
            $selectOptions[] = array('value' => $optKey, 'label' => $option);
          }
        }
        $vars = array('displayName'=> (isset($fields['fieldLabel'])?$fields['fieldLabel']:$fields['fieldName']),
                      'filter'=> array('selectOptions'=>$selectOptions),
                      'cellFilter'               => 'griddropdown:this',
                      'headerCellClass'          => '$scope.highlightFilteredHeader',
                      'editDropdownValueLabel'   => 'fkey',
                      'editDropdownIdLabel'      => 'id',
                      'editDropdownOptionsArray' => $options);
        break;
      case 'entrylink':
        $vars = array('cellTemplate'=>'<div class="ui-grid-cell-contents"><a href="http://makerfaire.com/wp-admin/admin.php?page=mf_entries&view=mfentry&lid={{row.entity[col.field]}}" target="_blank"> {{row.entity[col.field]}}</a></div>');
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
    $vars['field']    = $fields['fieldName'];
    $vars['name']     = $fields['fieldName'];
    $vars['minWidth'] = 100;
    $columnDefs[] = $vars;
  }

  //build data
  $data['columnDefs'] = $columnDefs;
  //get table data
  $query = "select * ".$sql." from ".$table.$where;

  $result = $mysqli->query( $query );
  //create array of table data
  while ($row = $result->fetch_assoc()) {
    $data['data'][]= $row;
  }
  echo json_encode($data);
  exit;
}

function invalidRequest($message='')
{
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
  $optionquery = "select " . $referenceField . ", " . $referenceDisplay . " from "   . $referenceTable;
  $result = $mysqli->query( $optionquery );
  while ($row = $result->fetch_assoc()) {
    $options[]       = array('id'    => $row[$referenceField], 'fkey'   => $row[$referenceDisplay]);
    $selectOptions[] = array('value' => $row[$referenceField], 'label' => $row[$referenceDisplay]);
  }
  return(array($options,$selectOptions));
}

function cmp($a, $b) {
    return $a["id"] - $b["id"];
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