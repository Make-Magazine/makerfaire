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

//faire subarea
$tableOptions['wp_mf_faire_subarea']['addlFields']['faire'] = array('fieldName' => 'faire', 'filterType'=>'dropdown', 'fieldLabel' => 'Faire',
    'fkey' => array('fkey' => 'faire', 'referenceTable' => 'wp_mf_faire', 'referenceField'   => 'ID', 'referenceDisplay' => 'faire'),
    'dataSql' =>'(SELECT faire_id from wp_mf_faire_area where wp_mf_faire_area.ID = area_id) as faire'
    );
$tableOptions['wp_mf_faire_subarea']['addlFields']['assCount'] = array('fieldName' => 'assCount', 'fieldLabel' => 'Assigned',
    'dataSql' =>'(SELECT count(*) from wp_mf_location where wp_mf_faire_subarea.ID = subarea_id) as assCount'
    );

//faire schedule
$tableOptions['wp_mf_schedule']['addlFields']['exName'] = array('fieldName' => 'exName', 'fieldLabel' => 'Exhibit Name');
$tableOptions['wp_mf_schedule']['addlFields']['subarea'] = array('fieldName' => 'subarea', 'fieldLabel' => 'subarea');

//entry attributes
$tableOptions['wp_rmt_entry_attributes']['fkey']    = array(
        array('fkey' => 'attribute_id', 'referenceTable' => 'wp_rmt_entry_att_categories', 'referenceField'   => 'ID', 'referenceDisplay' => 'category'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',                    'referenceField'   => 'ID', 'referenceDisplay' => 'user_email'));

//entry attention
$tableOptions['wp_rmt_entry_attn']['fkey']    = array(
        array('fkey' => 'attn_id',      'referenceTable' => 'wp_rmt_attn', 'referenceField' => 'ID', 'referenceDisplay' => 'value'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',    'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));

//entry resources
$tableOptions['wp_rmt_entry_resources']['fkey']    = array(
        array('fkey' => 'resource_id',  'referenceTable' => 'wp_rmt_resources', 'referenceField' => 'ID', 'referenceDisplay' => 'type'),
        array('fkey' => 'user',         'referenceTable' => 'wp_users',         'referenceField' => 'ID', 'referenceDisplay' => 'user_email'));

//Global Faire table
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
        'fieldName' => 'venue_address_region', 'filterType'=>'dropdown', 'fieldLabel'=>'Region', 
		'enableCellEdit' => true, 'width' => 150,
        'options' => array( 'Europe'        =>  'Europe',         'North America' =>  'North America',
                 			'Asia'          =>  'Asia',           'Australia'     =>  'Australia',
                  			'South America' =>  'South America',  'Middle East'   =>  'Middle East',
                  			'PACIFIC'       =>  'Pacific',        'Africa'        =>  'Africa')
    );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
        'fieldName' => 'venue_address_country', 'filterType'=>'dropdown', 'fieldLabel'=>'Country', 
        'enableCellEdit' => true, 'width' => 150,
        
        'options' => array("Afghanistan","Albania","Algeria","American Samoa", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Costa Rica", "Côte D'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Democratic People's Republic of Korea", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "England", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea Bissau", "Guinea", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar(Burma)", "Namibia", "Nauru", "Nepal", "Netherlands Antilles", "Netherlands", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Northern Ireland", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Republic of the Congo", "Réunion", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sào Tomé And Príncipe", "Saudi Arabia", "Scotland", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "US Virgin Islands", "Uzbekistan", "Vanuatu", "Vatican", "Venezuela", "Vietnam", "Wales", "Yemen", "Zambia", "Zimbabwe")
           );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
    'fieldName' => 'event_type', 'filterType'=>'dropdown','fieldLabel'=>'Event Type', 'enableCellEdit' => true,
    'options' => array('Mini' => 'Mini', 'Featured' => 'Featured', 'Flagship' => 'Flagship', 'School' => 'School')
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
		'fieldName' => 'event_start_dt', 'fieldLabel'=>'Start Date'
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'lat', 'type'=>'number', 'fieldLabel' => 'Latitude',
                'sort' => array('direction' => 'uiGridConstants.DESC', 'priority' => 1)
);
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'lng', 'type'=>'number',  'fieldLabel' => 'Longitude',
                'sort' => array('direction' => 'uiGridConstants.DESC', 'priority' => 1)
);
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
		'fieldName' => 'ID', 'type'=>'number',
		'sort' => array('direction' => 'uiGridConstants.DESC', 'priority' => 1)
);

//Ribbons
$tableOptions['wp_mf_ribbons']['addlFields']['form_id'] = array('fieldName' => 'form_id', 'fieldLabel' => 'Form ID',
    'dataSql' =>'(SELECT form_id from wp_gf_entry where wp_gf_entry.ID = entry_id) as form_id'
    );