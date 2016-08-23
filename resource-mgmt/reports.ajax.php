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
  }elseif($type =="ent2resource"){
    ent2resource($table);
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
    if($selFields->type=='checkbox' || $selFields->type=='radio' || $selFields->type=='select'){
      //remove everything after the period
      $baseField = strpos($selFields->id, ".") ? substr($selFields->id, 0, strpos($selFields->id, ".")) : $selFields->id;
      $fieldArr[$baseField][] = array('field'=>'field_'.str_replace('.','_',$selFields->id),
                                      'choice'=>$selFields->choices,
                                      'type'=>$selFields->type
          );
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
        $fieldID  = $fieldIDArr[$entry['field_number']]['fieldID'];
        $setValue = (isset($entryData[$entry['lead_id']]['field_'.$fieldID])?$entryData[$entry['lead_id']]['field_'.$fieldID].' '.$value: $value);
        $entryData[$entry['lead_id']]['field_'.$fieldID]  = $setValue;
      }else{
        $entryData[$entry['lead_id']]['field_'.str_replace('.','_',$entry['field_number'])]  = $value;
      }
    }
  }

  foreach($entryData as $entryID=>$dataRow){
    if(!empty($fieldArr)){
      //check selected checkbox and radio fields.  If at least one of the selections are not there, we need to skip this entry
      $remove = true;
      foreach($fieldArr as $field){
        foreach($field as $fieldRow){
          if(isset($dataRow[$fieldRow['field']])){
            if($fieldRow['type']=='radio'||$fieldRow['type']=='select'){ //check value
              if($dataRow[$fieldRow['field']] == $fieldRow['choice']){
                $remove = false;
              }
            }else{
              $remove=false;
            }
          }
        }
      }
      if($remove){
        unset ($entryData[$entryID]);
        continue; //skip this record
      }
    }
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
            $sql = 'SELECT qty,type,comment FROM `wp_rmt_entry_resources`, wp_rmt_resources '
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
          $resources = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");
          $entryRes = array();

          foreach($resources as $resource){
            $comment = ($incComments && $resource['comment']!=''?" (".$resource['comment'].")":'');
            $entryRes[] = $resource['qty'] .' : '.$resource['type'].$comment;
          }
          $data['columnDefs']['res_'.$selRMT->id]=   array('field'=> 'res_'.str_replace('.','_',$selRMT->id),'displayName'=>$selRMT->value);
          $entryData[$entryID]['res_'.$selRMT->id] = implode(', ',$entryRes);
        }
        if($type=='attribute'){
          if($selRMT->id!='all'){
            $sql = 'select value from wp_rmt_entry_attributes where entry_id ='.$entryID.' and attribute_id='.$selRMT->id;
          }else{
            $sql = 'select concat(category," ",value) as value from wp_rmt_entry_attributes,wp_rmt_entry_att_categories where '
                    . ' entry_id ='.$entryID
                    . ' and attribute_id= wp_rmt_entry_att_categories.ID';
          }
          //echo $sql.'<br/>';
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
  } //end entry data loop

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
  $orderBy = '';
  //build columnDefs
  foreach($tableFields[$table] as $fields){
    if(isset($fields['orderBy']))
      $orderBy = ' order by '.$fields['fieldName'].' '.$fields['orderBy'];
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
    if(isset($fields['cellTooltip']))   $vars['cellTooltip']  = $fields['cellTooltip'];
    if(isset($fields['cellTemplate']))  $vars['cellTemplate'] = $fields['cellTemplate'];
    if(isset($fields['cellFilter']))    $vars['cellFilter'] = $fields['cellFilter'];
    if(isset($fields['visible']))       $vars['visible']      = $fields['visible'];
    if(isset($fields['type']))       $vars['type']      = $fields['type'];

    $vars['name']     = $fields['fieldName'];
    $vars['minWidth'] = 100;
    $vars['width']    = (isset($fields['width'])?$fields['width']:'*');
    $columnDefs[] = $vars;
  }

  //build data
  $data['columnDefs'] = $columnDefs;

  //get table data
  $query = "select * ".$sql." from ".$table.$where.$orderBy;
//echo '$query='.$query;
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
  $optionquery = "select " . $referenceField . ", " . $referenceDisplay . " from "   . $referenceTable." order by ".$referenceDisplay."  asc";
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
function ent2resource($faire){
  global $mysqli;

    $sql = "select wp_rg_lead.id as 'entry_id', wp_rg_lead.form_id, wp_mf_faire.faire,

    ( select value from wp_rg_lead_detail where wp_rg_lead_detail.lead_id = wp_rg_lead.id and field_number=303) as status,
    ( select value from wp_rg_lead_detail where wp_rg_lead_detail.lead_id = wp_rg_lead.id and field_number=151) as proj_name,
          (select area from wp_mf_faire_area, wp_mf_faire_subarea where wp_mf_faire_subarea.id = subarea_id and wp_mf_faire_subarea.area_id = wp_mf_faire_area.id) as area,
          (select subarea from wp_mf_faire_subarea where wp_mf_faire_subarea.id = subarea_id) as subarea,
          wp_rmt_entry_resources.resource_id, wp_rmt_entry_resources.qty as 'resource_qty', wp_rmt_entry_resources.comment as 'resource_comment',
          wp_rmt_entry_attributes.attribute_id, wp_rmt_entry_attributes.value as 'attribute_value', wp_rmt_entry_attributes.comment as 'attribute_comment',
          (select token from wp_rmt_resources where wp_rmt_resources.id = resource_id)          as res_label,
          (select token from wp_rmt_entry_att_categories where wp_rmt_entry_att_categories.id = attribute_id)          as att_label,
          wp_rmt_entry_attn.attn_id as 'attn_id', wp_rmt_entry_attn.comment as 'attn_comment',
          wp_mf_location.id as 'location_id', wp_mf_location.subarea_id, wp_mf_location.location,
          (select value from wp_rmt_attn where wp_rmt_attn.id = attn_id)          as attn_type

          from wp_rg_lead
          left outer join wp_mf_faire on INSTR (wp_mf_faire.form_ids,wp_rg_lead.form_id) > 0
          left outer join wp_rmt_entry_resources 	 on wp_rmt_entry_resources.entry_id = wp_rg_lead.id
          left outer join wp_rmt_entry_attributes  on wp_rmt_entry_attributes .entry_id = wp_rg_lead.id
          left outer join wp_rmt_entry_attn    on wp_rmt_entry_attn .entry_id = wp_rg_lead.id
          left outer join wp_mf_location on wp_mf_location.entry_id = wp_rg_lead.id
          where status = 'active' and faire is not NULL order by faire DESC";
  $entries = $mysqli->query($sql) or trigger_error($mysqli->error."[$sql]");

  $entryData = array();
  $resArray = array();
  $attArray = array();
  $attnArray = array();
  foreach($entries as $entry){
    //set resource data
    $data[$entry['entry_id']]['entry_id']   = $entry['entry_id'];
    $data[$entry['entry_id']]['form_id']    = $entry['form_id'];
    $data[$entry['entry_id']]['faire']      = $entry['faire'];
    $data[$entry['entry_id']]['status']     = $entry['status'];
    $data[$entry['entry_id']]['proj_name']  = $entry['proj_name'];

    if($entry['resource_id'] != NULL){
      $data[$entry['entry_id']]['resource'][$entry['resource_id']] = array('qty'=> $entry['resource_qty'], 'comment'=>$entry['resource_comment']);
      //add resource to resource array
      $resArray[$entry['resource_id']] = $entry['res_label'];
    }
    //set attribute data
    if($entry['attribute_id'] != NULL){
      //set resource data
      $data[$entry['entry_id']]['attribute'][$entry['attribute_id']] = array('value'=> $entry['attribute_value'], 'comment'=>$entry['attribute_comment']);
      //add attribute to attribute array
      $attArray[$entry['attribute_id']] = $entry['att_label'];
    }
    //set attention data
    if($entry['attn_id'] != NULL){
      //set resource data
      $data[$entry['entry_id']]['attention'][$entry['attn_id']] = array('comment'=>$entry['attn_comment']);
      //add attribute to attribute array
      $attnArray[$entry['attn_id']] = $entry['attn_type'];
    }
    //set location data
    if($entry['location_id'] != NULL){
      //set resource data
      $data[$entry['entry_id']]['location'] = array('subarea'=> $entry['subarea'], 'location'=>$entry['location'], 'area'=>$entry['area']);
    }
  }

  //default columns
  $columnDefs[] = array('field' => 'faire', 'displayName'=>'Faire', 'width'=>'*');
  $columnDefs[] = array('field' => 'entry_id', 'displayName'=>'Entry ID', 'width'=>'*');
  $columnDefs[] = array('field' => 'proj_name', 'displayName'=>'Entry Name', 'width'=>'*');
  $columnDefs[] = array('field' => 'form_id', 'displayName'=> 'Form', 'width'=>'*');
  $columnDefs[] = array('field' => 'status', 'displayName'=>'Status', 'width'=>'*',
      'sort'=> array(
          'direction'=> 'uiGridConstants.ASC',
          'priority'=> 0
        ), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'location.area', 'displayName'=>'Area',
      'sort'=> array(
          'direction'=> 'uiGridConstants.ASC',
          'priority'=> 1
        ), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'location.subarea', 'displayName'=>'Subarea',
      'sort'=> array(
          'direction'=> 'uiGridConstants.ASC',
          'priority'=> 3
        ), 'enableSorting'=> true);
  $columnDefs[] = array('field' => 'location.location', 'displayName'=>'Location',
      'sort'=> array(
          'direction'=> 'uiGridConstants.ASC',
          'priority'=> 2
        ), 'enableSorting'=> true);

  $resArray  = array_unique($resArray);
  $attArray  = array_unique($attArray);
  $attnArray = array_unique($attnArray);
  foreach($resArray as $key=>$resource){
   $columnDefs[] = array('displayName'            => $resource,
                         'field'=> 'resource.'.$key.'.qty');
   $columnDefs[] = array('displayName'            => $resource .' - comment',
                         'field'=> 'resource.'.$key.'.comment');
  }
  foreach($attArray as $key=>$attribute){
   $columnDefs[] = array('displayName'            => $attribute,
                         'field'=> 'attribute.'.$key.'.value');
   $columnDefs[] = array('displayName'            => $attribute .' - comment',
                         'field'=> 'attribute.'.$key.'.comment');
  }
  foreach($attnArray as $key=>$attention){
   $columnDefs[] = array('displayName'            => $attention,
                         'field'=> 'attention.'.$key.'.comment');
  }
  //var_dump($columnDefs);
  $retData = array();
  usort($data, "cmpEntryID");
  //sort data by status, area, subarea, location

  $retData['data'] = $data;
  $retData['columnDefs'] = $columnDefs;
  echo json_encode($retData);
  exit;
}