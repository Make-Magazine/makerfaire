<?php
/*
 * used to hold the tableFields array that defines what data is returned for reports
 */
$tableFields = array();
/*
 * Create table definitons for returning data
 * layout - index = table name
 *  array('fieldName'   => 'resource_category_id',      //name of the field in the table
              'filterType'   => 'text',                  //filter type - text, drop down, etc
              'fieldLabel'  => 'Item',                  //label to display
              'fkey'        => array(                   //if field data is pulled from somewhere else use this section
                    'referenceTable'   => 'wp_rmt_resource_categories',   //fkey table
                    'referenceField'   => 'ID',                           //fkey ID
                    'referenceDisplay' => 'category')                     //fkey field to display
        ),
 */

//entry resource table
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'faire', 'filterType'=>'dropdown',
    'fkey'       => array('referenceTable'   => 'wp_mf_faire',
                          'referenceField'   => 'ID',
                          'referenceDisplay' => 'faire'),
    'grouping'=>array('groupPriority'=> 0), 'sort'=>array('priority'=> 0, 'direction'=> 'asc'),
    'dataSql' =>'(SELECT wp_mf_faire.ID from wp_mf_faire, wp_rg_lead where wp_rg_lead.id = entry_id and INSTR (wp_mf_faire.form_ids,wp_rg_lead.form_id)> 0) as faire'
    );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'area', 'filterType'=>'text',
    'grouping'=>array('groupPriority'=> 1), 'sort'=>array('priority'=> 1, 'direction'=> 'asc'),
        'dataSql' =>'(SELECT area '
    . '               from   wp_mf_faire_area,wp_mf_faire_subarea,wp_mf_location '
    . '               where  wp_mf_faire_subarea.area_id = wp_mf_faire_area.ID '
    . '               and    wp_mf_location.subarea_id=wp_mf_faire_subarea.ID '
    . '               and    wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as area'
    );

$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'subarea', 'filterType'=>'text',
    'grouping'=>array('groupPriority'=> 2), 'sort'=>array('priority'=> 2, 'direction'=> 'asc'),
        'dataSql' =>'(SELECT subarea '
    . '               from   wp_mf_faire_subarea,wp_mf_location '
    . '               where  wp_mf_location.subarea_id=wp_mf_faire_subarea.ID '
    . '               and    wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as subarea'
    );

$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'location', 'filterType'=>'text',
    'grouping'=>array('groupPriority'=> 3), 'sort'=>array('priority'=> 3, 'direction'=> 'asc'),
        'dataSql' =>'(SELECT location from wp_mf_location where wp_mf_location.entry_id = wp_rmt_entry_resources.entry_id limit 1) as location'
    );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'entry_id', 'filterType'   => 'entrylink', 'fieldLabel' => 'Type');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'item', 'filterType'   => 'dropdown', 'fieldLabel' => 'Item',
  'grouping'=>array('groupPriority'=> 4), 'sort'=>array('priority'=> 4, 'direction'=> 'asc'),
  'dataSql' => '(select wp_rmt_resources.resource_category_id '
    . '           from  wp_rmt_resources '
    . '           where wp_rmt_entry_resources.resource_id = wp_rmt_resources.ID  limit 1) as item',
  'fkey'      => array('referenceTable'   => 'wp_rmt_resource_categories',
                       'referenceField'   => 'ID',
                       'referenceDisplay' => 'category')
 );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'resource_id',          'filterType'   => 'dropdown',    'fieldLabel'  => 'Type',
    'grouping'   => array( 'groupPriority'=> 5), 'sort'       => array('priority'=> 5, 'direction'=> 'asc'),
                                                 'fkey'      => array('referenceTable'   => 'wp_rmt_resources',
                                                                      'referenceField'   => 'ID',
                                                                      'referenceDisplay' => 'type')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'qty',     'filterType' => 'number', 'fieldLabel'  => 'Qty');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'comment', 'filterType' => 'text', 'fieldLabel'  => 'Comment');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'user',    'filterType' => 'dropdown',   'fieldLabel'  => 'User Updated',
                                                  'fkey'       => array('referenceTable'   => 'wp_users',
                                                                        'referenceField'   => 'ID',
                                                                        'referenceDisplay' => 'user_email'),
                                                  'options' =>array(null=>'Initial','0'=>'Payment')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'update_stamp', 'filterType'   => 'text',     'fieldLabel'  => 'Update Stamp');
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'res_status',   'filterType'   => 'dropdown', 'fieldLabel' => 'Resource Status',
                                                 'dataSql' => "(SELECT meta_value FROM `wp_rg_lead_meta` where meta_key = 'res_status' and lead_id =entry_id ) as res_status",
                                                 'options' =>array('review'=>'Review','ready'=>'Ready')
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'res_assign',   'filterType'   => 'text', 'fieldLabel' => 'Resource Assign To',
                                                 'dataSql' => "(SELECT meta_value FROM `wp_rg_lead_meta` where meta_key = 'res_assign' and lead_id =entry_id ) as res_assign"
                                                );
$tableFields['wp_rmt_entry_resources'][] = array('fieldName' => 'status',   'filterType'   => 'dropdown', 'fieldLabel' => 'Status',
                                                 'dataSql' => "(SELECT value FROM `wp_rg_lead_detail` where field_number=303 and lead_id =entry_id ) as status",
                                                 'options' => array('Proposed'=>'Proposed','Accepted'=>'Accepted','Rejected'=>'Rejected',
                                                                    'Wait List'=>'Wait List','Cancelled'=>'Cancelled'),
                                                );

//change report
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'lead_id',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'form_id',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'user_id',   'filterType' => 'dropdown',   'fieldLabel'  => 'User Updated',
                                                  'fkey'       => array('referenceTable'   => 'wp_users',
                                                                        'referenceField'   => 'ID',
                                                                        'referenceDisplay' => 'user_email'),
                                                  'options' =>array(null=>'Initial','0'=>'Payment'));
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'date_updated',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'field_id',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'field_before',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'field_after',   'filterType'   => 'text');
$tableFields['wp_rg_lead_detail_changes'][] = array('fieldName' => 'fieldLabel',   'filterType'   => 'text');

//faire report
$tableFields['wp_mf_faire_subarea'][] = array('fieldName' => 'faire',   'filterType'   => 'text',
                                              'dataSql' => "(SELECT faire "
                                                          . " FROM `wp_mf_faire`,wp_mf_faire_area "
                                                          . " where wp_mf_faire.ID = wp_mf_faire_area.faire_id and wp_mf_faire_area.ID= area_id) "
                                                          . " as faire"
                                              );
$tableFields['wp_mf_faire_subarea'][] = array('fieldName' => 'area_id',   'filterType' => 'dropdown',   'fieldLabel'  => 'Area',
                                          'fkey'       => array('referenceTable'   => 'wp_mf_faire_area',
                                                                'referenceField'   => 'ID',
                                                                'referenceDisplay' => 'area'));
$tableFields['wp_mf_faire_subarea'][] = array('fieldName' => 'subarea',   'filterType'   => 'text');



//entry table
$tableFields['wp_rg_lead'][] = array('fieldName' => 'id',      'filterType'   => 'entrylink',  'fieldLabel' => 'Exhibit ID');
$tableFields['wp_rg_lead'][] = array('fieldName' => 'form_id', 'filterType'   => 'text',  'fieldLabel' => 'Form',
                                     'limit'=>array('opt'=>'in','value'=>"(46, 45, 47, 48,60)"));
$tableFields['wp_rg_lead'][] = array('fieldName' => 'status',  'filterType'   => 'hidden',  'fieldLabel' => 'Status',
                                     'limit'=>array('opt'=>'=','value'=>"'active'"));
$tableFields['wp_rg_lead'][] = array('fieldName' => 'exName',   'filterType'   => 'text', 'fieldLabel' => 'Exhibit Name',
                                                 'dataSql' => "(SELECT value FROM `wp_rg_lead_detail` where field_number = '151' and lead_id =wp_rg_lead.id  limit 1) as exName"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'contact_name',   'filterType'   => 'text', 'fieldLabel' => 'Maker Name',
                                                 'dataSql' => "(SELECT value FROM `wp_rg_lead_detail` where field_number = '96' and lead_id =wp_rg_lead.id  limit 1) as contact_name"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'contact_phone',   'filterType'   => 'text', 'fieldLabel' => 'Maker Phone',
                                                 'dataSql' => "(SELECT value FROM `wp_rg_lead_detail` where field_number = '99' and lead_id =wp_rg_lead.id  limit 1) as contact_phone"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'space_size',   'filterType'   => 'text', 'fieldLabel' => 'Space Size',
                                                 'dataSql' => "(SELECT value FROM `wp_rmt_entry_attributes` where attribute_id = '2' and entry_id =wp_rg_lead.id  limit 1) as space_size"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'tbl6x30',   'filterType'   => 'text', 'fieldLabel' => 'Tables - 6x30',
                                                 'dataSql' => "(SELECT qty FROM `wp_rmt_entry_resources` where resource_id = '94' and entry_id =wp_rg_lead.id  limit 1) as tbl6x30"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'tbl8x30',   'filterType'   => 'text', 'fieldLabel' => 'Tables - 8x30',
                                                 'dataSql' => "(SELECT qty FROM `wp_rmt_entry_resources` where resource_id = '95' and entry_id =wp_rg_lead.id  limit 1) as tbl8x30"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'folding',   'filterType'   => 'text', 'fieldLabel' => 'Chairs - Folding',
                                                 'dataSql' => "(SELECT qty FROM `wp_rmt_entry_resources` where resource_id = '113' and entry_id =wp_rg_lead.id  limit 1) as folding"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'internet',   'filterType'   => 'text', 'fieldLabel' => 'Internet',
                                                 'dataSql' => "(SELECT value FROM `wp_rmt_entry_attributes` where attribute_id = '11' and entry_id =wp_rg_lead.id  limit 1) as internet"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'noise',   'filterType'   => 'text', 'fieldLabel' => 'Noise',
                                                 'dataSql' => "(SELECT value FROM `wp_rmt_entry_attributes` where attribute_id = '9' and entry_id =wp_rg_lead.id  limit 1) as noise"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'summary',   'filterType'   => 'text', 'fieldLabel' => 'Summary',
                                                 'dataSql' => "(SELECT value FROM `wp_rg_lead_detail` where field_number = '16' and lead_id =wp_rg_lead.id  limit 1) as summary"
                                                );
$tableFields['wp_rg_lead'][] = array('fieldName' => 'exposure',   'filterType'   => 'text', 'fieldLabel' => 'Exposure',
                                                 'dataSql' => "(SELECT value FROM `wp_rmt_entry_attributes` where attribute_id = '4' and entry_id =wp_rg_lead.id limit 1) as exposure"
                                                );