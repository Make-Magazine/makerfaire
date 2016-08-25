<?php
//$tableOptions defines any foreign keys in the table that we need to pull additional data for
$tableOptions = array();
//resources
$tableOptions['wp_rmt_resources']['fkey'] = array(
        array('fkey' => 'resource_category_id', 'referenceTable'   => 'wp_rmt_resource_categories', 'referenceField'   => 'ID', 'referenceDisplay' => 'category'));
//vendor resources
$tableOptions['wp_rmt_vendor_resources']['fkey']  = array(
        array('fkey' => 'vendor_id',   'referenceTable' => 'wp_rmt_vendors',   'referenceField' => 'ID', 'referenceDisplay' => 'company_name'),
        array('fkey' => 'resource_id', 'referenceTable' => 'wp_rmt_resources', 'referenceField' => 'ID', 'referenceDisplay' => 'item'));
$tableOptions['wp_rmt_vendor_orders']['fkey']  = array(
        array('fkey' => 'vendor_resource_id', 'referenceTable'   => 'wp_rmt_vendor_resources', 'referenceField'   => 'ID', 'referenceDisplay' => 'ID'),
        array('fkey' => 'faire_id',           'referenceTable'   => 'wp_mf_faire',             'referenceField'   => 'ID', 'referenceDisplay' => 'faire'));
$tableOptions['wp_mf_faire_area']['fkey']    = array(
        array('fkey' => 'faire_id',     'referenceTable' => 'wp_mf_faire',      'referenceField'   => 'ID', 'referenceDisplay' => 'faire'));
$tableOptions['wp_mf_faire_subarea']['fkey']    = array(
        array('fkey' => 'area_id',      'referenceTable' => 'wp_mf_faire_area', 'referenceField'   => 'ID', 'referenceDisplay' => 'area'));

$tableOptions['wp_mf_faire_subarea']['addlFields']['faire'] = array('fieldName' => 'faire', 'filterType'=>'dropdown', 'fieldLabel' => 'Faire',
    'fkey' => array('fkey' => 'faire', 'referenceTable' => 'wp_mf_faire', 'referenceField'   => 'ID', 'referenceDisplay' => 'faire'),
    'dataSql' =>'(SELECT faire_id from wp_mf_faire_area where wp_mf_faire_area.ID = area_id) as faire'
    );
$tableOptions['wp_mf_faire_subarea']['addlFields']['assCount'] = array('fieldName' => 'assCount', 'fieldLabel' => 'Assigned',
    'dataSql' =>'(SELECT count(*) from wp_mf_location where wp_mf_faire_subarea.ID = subarea_id) as assCount'
    );

$tableOptions['wp_rmt_entry_attributes']['fkey']    = array(
        array('fkey' => 'attribute_id', 'referenceTable' => 'wp_rmt_entry_att_categories', 'referenceField'   => 'ID', 'referenceDisplay' => 'category'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',                    'referenceField'   => 'ID', 'referenceDisplay' => 'user_email'));
$tableOptions['wp_rmt_entry_attn']['fkey']    = array(
        array('fkey' => 'attn_id',      'referenceTable' => 'wp_rmt_attn', 'referenceField' => 'ID', 'referenceDisplay' => 'value'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',    'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));
$tableOptions['wp_rmt_entry_resources']['fkey']    = array(
        array('fkey' => 'resource_id',  'referenceTable' => 'wp_rmt_resources', 'referenceField' => 'ID', 'referenceDisplay' => 'type'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',         'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));

//Global Faire table
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
        'fieldName' => 'venue_address_region', 'filterType'=>'dropdown','fieldLabel'=>'Region', 'enableCellEdit' => true, 'width' => 150,
        'options' => array( 'Europe'        =>  'Europe',         'North America' =>  'North America',
                  'Asia'          =>  'Asia',           'Australia'     =>  'Australia',
                  'South America' =>  'South America',  'Middle East'   =>  'Middle East',
                  'PACIFIC'       =>  'Pacific',        'Africa'        =>  'Africa')
    );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
    'fieldName' => 'event_type', 'filterType'=>'dropdown','fieldLabel'=>'Event Type', 'enableCellEdit' => true,
    'options' => array('Mini' => 'Mini', 'Featured' => 'Featured', 'Flagship' => 'Flagship', 'School' => 'School')
  );
