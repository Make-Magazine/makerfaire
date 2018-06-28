<?php
/*
 * used to hold the tableFields array that defines what data is returned for reports
 */
$tableFields = array();
/*
 * Create table definitons for returning data
 */

//entry resource table
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'faire', 'filterType'=>'dropdown', 'fieldLabel' => 'Faire',
  'fkeySQL' => "select ID, faire as field from wp_mf_faire order by faire asc");
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'area', 'filterType'=>'text');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'subarea', 'filterType'=>'text');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'location', 'filterType'=>'text');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'entry_id', 'filterType' => 'entrylink');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'form_id', 'filterType' => 'text', 'fieldLabel' => 'form_id','visible' => false);
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'item', 'filterType'   => 'dropdown', 'fieldLabel' => 'Item',
  'fkeySQL' => "select ID, category as field from wp_rmt_resource_categories order by category asc");
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'resource_id', 'filterType' => 'dropdown', 'fieldLabel'  => 'Type',
  'fkeySQL' => "select ID, type as field from wp_rmt_resources order by type asc");
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'qty',     'filterType' => 'number', 'fieldLabel'  => 'Qty');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'exName',   'filterType'   => 'text', 'fieldLabel' => 'Exhibit Name');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'contact_name',   'filterType'   => 'text', 'fieldLabel' => 'Maker Name');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'contact_phone',   'filterType'   => 'text', 'fieldLabel' => 'Maker Phone');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'summary',   'filterType'   => 'text', 'fieldLabel' => 'Summary');
$tableFields['wp_rmt_entry_resources']['colDefs'][] = array('fieldName' => 'status',   'filterType'   => 'dropdown', 'fieldLabel' => 'Status',
    'options' => array('Proposed'=>'Proposed','Accepted'=>'Accepted','Rejected'=>'Rejected', 'Wait List'=>'Wait List','Cancelled'=>'Cancelled'));
$tableFields['wp_rmt_entry_resources']['query'] =
        'select entry_id, qty, resource_id, wp_gf_entry.form_id, wp_mf_faire.faire,'
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key = "303" and wp_gf_entry_meta.entry_id = wp_rmt_entry_resources.entry_id ) as status, '
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key =  "16" and wp_gf_entry_meta.entry_id = wp_rmt_entry_resources.entry_id  limit 1) as summary ,'
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key =  "99" and wp_gf_entry_meta.entry_id = wp_rmt_entry_resources.entry_id  limit 1) as contact_phone, '
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key =  "96" and wp_gf_entry_meta.entry_id = wp_rmt_entry_resources.entry_id  limit 1) as contact_name, '
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key = "151" and wp_gf_entry_meta.entry_id = wp_rmt_entry_resources.entry_id  limit 1) as exName, '
        . '(select wp_rmt_resources.resource_category_id from  wp_rmt_resources where wp_rmt_entry_resources.resource_id = wp_rmt_resources.ID  limit 1) as item, '
        . '(SELECT location from wp_mf_location where wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as location, '
        . '(SELECT subarea from   wp_mf_faire_subarea,wp_mf_location where  wp_mf_location.subarea_id=wp_mf_faire_subarea.ID and wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as subarea, '
        . '(SELECT area from   wp_mf_faire_area, wp_mf_faire_subarea,wp_mf_location where  wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID and wp_mf_location.subarea_id=wp_mf_faire_subarea.ID and wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as area '
        . 'from wp_rmt_entry_resources, wp_gf_entry, wp_mf_faire '
        . 'where wp_gf_entry.id = entry_id '
        . 'and   find_in_set (wp_gf_entry.form_id,wp_mf_faire.form_ids) > 0 '
        . 'and   wp_mf_faire.id = '. $faire;
//faire
$cellToolTipTemplate = '<div class="ui-grid-cell-contents wrap" title="{{COL_FIELD}}" data-toggle="tooltip" >{{ COL_FIELD }}</div>';
$dateCellTemplate    =
'<div class="ui-grid-cell-contents">{{ COL_FIELD | date:\'M/d/yy h:mm a\'}}</div>';

/*
 *      change report
 *   wp_mf_lead_detail_changes
 */
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'lead_id', 'filterType'   => 'entrylink', 'width'  => 75,);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'user_email','filterType' => 'text', 'fieldLabel' => 'User Email','cellTemplate'=> $cellToolTipTemplate);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'date_updated', 'filterType'  => 'text', 'fieldLabel'  => 'Date Updated', 'width' => 150, 'cellTemplate'=> '<div class="ui-grid-cell-contents">{{COL_FIELD | date:"M-d-yy h:mm a"}}</div>');
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'field_id',     'filterType' => 'text', 'width' => 80);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'fieldLabel',   'filterType'  => 'text','fieldLabel'  => 'Field Label','cellTemplate'=> $cellToolTipTemplate);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'field_before', 'filterType'  => 'text', 'fieldLabel'  => 'Value Before', 'width' => 250, 'cellTemplate'=> $cellToolTipTemplate);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'field_after',  'filterType'  => 'text', 'fieldLabel'  => 'Value After', 'width' => 250, 'cellTemplate'=> $cellToolTipTemplate);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'status_at_update', 'filterType'  => 'text', 'visible' => false);
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'status',       'filterType'  => 'dropdown', 'fieldLabel'  => 'Current Status', 'visible'=>false,
    'options'     => array('Proposed'=>'Proposed','Accepted'=>'Accepted','Rejected'=>'Rejected','Wait List'=>'Wait List','Cancelled'=>'Cancelled'));
$tableFields['wp_mf_lead_detail_changes']['colDefs'][] = array('fieldName' => 'form_id', 'filterType' => 'text', 'visible' => false);
$tableFields['wp_mf_lead_detail_changes']['query'] =
          'SELECT lead_id, DATE_FORMAT(date_updated,"%Y-%m-%dT%TZ") as date_updated, field_id, fieldLabel, field_before, field_after, status_at_update, wp_gf_entry.form_id, '
        . '     (SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key="303" and wp_gf_entry_meta.entry_id =wp_mf_lead_detail_changes.lead_id ) as status, '
        . '     (SELECT user_email FROM `wp_users` where wp_users.ID =user_id ) as user_email '
        . 'FROM wp_mf_lead_detail_changes '
        . 'left outer join wp_gf_entry on wp_gf_entry.id = lead_id '
        . 'left outer join  wp_mf_faire on find_in_set (wp_gf_entry.form_id,wp_mf_faire.form_ids) > 0 '
        . 'where  wp_mf_faire.ID = '.$faire
        . '  ORDER BY `date_updated` DESC';

/*
 * assigned location report
 *      wp_mf_location
 */
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'area',     'filterType'=>'dropdown', 'width' => 80,
    'fkeySQL' => "select ID, area as field from wp_mf_faire_area".($faire!=''?' where faire_id='.$faire:'')." order by area asc");
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'subarea_id', 'filterType'=>'dropdown', 'fieldLabel' => 'Subarea',
    'fkeySQL' => "select wp_mf_faire_subarea.ID, subarea as field from wp_mf_faire_subarea".($faire!=''?' left outer join wp_mf_faire_area on area_id=wp_mf_faire_area.id where subarea !="" and faire_id='.$faire:' where subarea !="" ')." order by subarea asc");
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'entry_id', 'filterType' => 'entrylink', 'width'=> 85);
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'exName',   'filterType' => 'text', 'fieldLabel' => 'Exhibit Name');
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'location', 'filterType' => 'text');
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'start_dt', 'filterType' => 'text');
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'end_dt',   'filterType'   => 'text');
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'schedType',   'filterType'   => 'text');
$tableFields['wp_mf_location']['colDefs'][] = array('fieldName' => 'form_id', 'filterType' => 'text', 'fieldLabel' => 'form_id','visible' => false);
$tableFields['wp_mf_location']['query'] =
          'SELECT wp_mf_location.location,wp_mf_location.entry_id, wp_mf_location.subarea_id, '
                . 'wp_mf_faire_subarea.subarea, wp_mf_faire_subarea.nicename, wp_mf_faire_subarea.area_id, '
                . 'wp_mf_faire_area.area, wp_mf_schedule.start_dt, wp_mf_schedule.end_dt, wp_mf_schedule.type as schedType, wp_gf_entry.form_id, '
        . '(SELECT meta_value as value FROM `wp_gf_entry_meta` where meta_key = "151" and wp_gf_entry_meta.entry_id = wp_mf_location.entry_id  limit 1) as exName '
        . 'FROM    wp_mf_location '
        . 'left outer join  wp_mf_faire_subarea on wp_mf_location.subarea_id = wp_mf_faire_subarea.ID '
        . 'left outer join  wp_mf_faire_area    on wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID '
        . 'left outer join  wp_mf_schedule      on wp_mf_schedule.location_id = wp_mf_location.ID '
        . 'left outer join  wp_gf_entry         on wp_gf_entry.ID = wp_mf_location.entry_id '
        . 'left outer join  wp_mf_faire         on find_in_set (form_id,wp_mf_faire.form_ids) > 0  where  wp_mf_faire.ID = '.$faire;
