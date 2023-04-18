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
        'options' => array('Europe'=>'Europe', 'North America'=>'North America', 'Asia'=>'Asia', 'Australia'=>'Australia', 
                'South America'=>'South America', 'Middle East'=>'Middle East', 'Pacific'=>'Pacific', 'Africa'=>'Africa')
    );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
        'fieldName' => 'venue_address_country', 'filterType'=>'dropdown', 'fieldLabel'=>'Country', 
        'enableCellEdit' => true, 'width' => 150,
        
        'options' => array("Afghanistan"=>"Afghanistan", "Albania"=>"Albania", "Algeria"=>"Algeria",
                "American Samoa"=>"American Samoa", "Andorra"=>"Andorra", "Angola"=>"Angola", "Antarctica"=>"Antarctica", 
                "Antigua and Barbuda"=>"Antigua and Barbuda", "Argentina"=>"Argentina", "Armenia"=>"Armenia", 
                "Aruba"=>"Aruba", "Australia"=>"Australia", "Austria"=>"Austria", "Azerbaijan"=>"Azerbaijan", 
                "Bahamas"=>"Bahamas", "Bahrain"=>"Bahrain", "Bangladesh"=>"Bangladesh", "Barbados"=>"Barbados", 
                "Belarus"=>"Belarus", "Belgium"=>"Belgium", "Belize"=>"Belize", "Benin"=>"Benin", "Bermuda"=>"Bermuda", 
                "Bhutan"=>"Bhutan", "Bolivia"=>"Bolivia", "Bosnia and Herzegovina"=>"Bosnia and Herzegovina", 
                "Botswana"=>"Botswana", "Brazil"=>"Brazil", "British Virgin Islands"=>"British Virgin Islands", 
                "Brunei"=>"Brunei", "Bulgaria"=>"Bulgaria", "Burkina Faso"=>"Burkina Faso", "Burundi"=>"Burundi",  
                "Cambodia"=> "Cambodia",  "Cameroon"=> "Cameroon",  "Canada"=> "Canada",  "Cape Verde"=> "Cape Verde",
                "Cayman Islands"=> "Cayman Islands",  "Central African Republic"=> "Central African Republic",  
                "Chad"=> "Chad",  "Chile"=> "Chile",  "China"=> "China",  "Colombia"=> "Colombia",  "Comoros"=> "Comoros",
                "Costa Rica"=> "Costa Rica",  "Côte D'Ivoire"=> "Côte D'Ivoire",  "Croatia"=> "Croatia",  "Cuba"=> "Cuba",  
                "Cyprus"=> "Cyprus",  "Czech Republic"=> "Czech Republic",  
                "Democratic People's Republic of Korea"=> "Democratic People's Republic of Korea",  
                "Democratic Republic of the Congo"=> "Democratic Republic of the Congo",  "Denmark"=> "Denmark",  
                "Djibouti"=> "Djibouti",  "Dominica"=> "Dominica",  "Dominican Republic"=> "Dominican Republic",  
                "Ecuador"=> "Ecuador",  "Egypt"=> "Egypt",  "El Salvador"=> "El Salvador",  "England"=> "England",  
                "Equatorial Guinea"=> "Equatorial Guinea",  "Eritrea"=> "Eritrea",  "Estonia"=> "Estonia",  
                "Ethiopia"=> "Ethiopia",  "Faroe Islands"=> "Faroe Islands",  "Fiji"=> "Fiji",  "Finland"=> "Finland",  
                "France"=> "France",  "French Polynesia"=> "French Polynesia",  "Gabon"=> "Gabon",  "Gambia"=> "Gambia",  
                "Georgia"=> "Georgia",  "Germany"=> "Germany",  "Ghana"=> "Ghana",  "Greece"=> "Greece",  
                "Greenland"=> "Greenland",  "Grenada"=> "Grenada",  "Guadeloupe"=> "Guadeloupe",  "Guam"=> "Guam",  
                "Guatemala"=> "Guatemala",  "Guinea Bissau"=> "Guinea Bissau",  "Guinea"=> "Guinea",  "Guyana"=> "Guyana",  
                "Haiti"=> "Haiti",  "Honduras"=> "Honduras",  "Hong Kong"=> "Hong Kong",  "Hungary"=> "Hungary",  
                "Iceland"=> "Iceland",  "India"=> "India",  "Indonesia"=> "Indonesia",  "Iran"=> "Iran",  
                "Iraq"=> "Iraq",  "Ireland"=> "Ireland",  "Israel"=> "Israel",  "Italy"=> "Italy",  "Jamaica"=> "Jamaica",  
                "Japan"=> "Japan",  "Jersey"=> "Jersey",  "Jordan"=> "Jordan",  "Kazakhstan"=> "Kazakhstan",  
                "Kenya"=> "Kenya",  "Kiribati"=> "Kiribati",  "Kosovo"=> "Kosovo",  "Kuwait"=> "Kuwait",  
                "Kyrgyzstan"=> "Kyrgyzstan",  "Laos"=> "Laos",  "Latvia"=> "Latvia",  "Lebanon"=> "Lebanon",  
                "Lesotho"=> "Lesotho",  "Liberia"=> "Liberia",  "Libya"=> "Libya",  "Liechtenstein"=> "Liechtenstein",  
                "Lithuania"=> "Lithuania",  "Luxembourg"=> "Luxembourg",  "Macao"=> "Macao",  "Macedonia"=> "Macedonia",  
                "Madagascar"=> "Madagascar",  "Malawi"=> "Malawi",  "Malaysia"=> "Malaysia",  "Maldives"=> "Maldives",  
                "Mali"=> "Mali",  "Malta"=> "Malta",  "Marshall Islands"=> "Marshall Islands",  
                "Mauritania"=> "Mauritania",  "Mauritania"=> "Mauritania",  "Mauritius"=> "Mauritius",  "Mexico"=> "Mexico",
                "Micronesia"=> "Micronesia",  "Moldova"=> "Moldova",  "Monaco"=> "Monaco",  "Mongolia"=> "Mongolia",  
                "Montenegro"=> "Montenegro",  "Morocco"=> "Morocco",  "Mozambique"=> "Mozambique",  
                "Myanmar(Burma)"=> "Myanmar(Burma)",  "Namibia"=> "Namibia",  "Nauru"=> "Nauru",  "Nepal"=> "Nepal",  
                "Netherlands Antilles"=> "Netherlands Antilles",  "Netherlands"=> "Netherlands",  
                "New Caledonia"=> "New Caledonia",  "New Zealand"=> "New Zealand",  "Nicaragua"=> "Nicaragua",  
                "Niger"=> "Niger",  "Nigeria"=> "Nigeria",  "Northern Ireland"=> "Northern Ireland",  
                "Northern Mariana Islands"=> "Northern Mariana Islands",  "Norway"=> "Norway",  "Oman"=> "Oman",  
                "Pakistan"=> "Pakistan",  "Palau"=> "Palau",  "Palestine"=> "Palestine",  "Panama"=> "Panama",  
                "Papua New Guinea"=> "Papua New Guinea",  "Paraguay"=> "Paraguay",  "Peru"=> "Peru",  
                "Philippines"=> "Philippines",  "Poland"=> "Poland",  "Portugal"=> "Portugal",  
                "Puerto Rico"=> "Puerto Rico",  "Qatar"=> "Qatar",  "Republic of the Congo"=> "Republic of the Congo",  
                "Réunion"=> "Réunion",  "Romania"=> "Romania",  "Russia"=> "Russia",  "Rwanda"=> "Rwanda",  
                "Saint Kitts and Nevis"=> "Saint Kitts and Nevis",  "Saint Lucia"=> "Saint Lucia",  
                "Saint Vincent and the Grenadines"=> "Saint Vincent and the Grenadines",  "Samoa"=> "Samoa",  
                "San Marino"=> "San Marino",  "Sào Tomé And Príncipe"=> "Sào Tomé And Príncipe",  
                "Saudi Arabia"=> "Saudi Arabia",  "Scotland"=> "Scotland",  "Senegal"=> "Senegal",  "Serbia"=> "Serbia",  
                "Seychelles"=> "Seychelles",  "Sierra Leone"=> "Sierra Leone",  "Singapore"=> "Singapore", 
                "Slovakia"=> "Slovakia",  "Slovenia"=> "Slovenia",  "Solomon Islands"=> "Solomon Islands",  
                "Somalia"=> "Somalia",  "South Africa"=> "South Africa",  "South Korea"=> "South Korea",  "Spain"=> "Spain",
                "Sri Lanka"=> "Sri Lanka",  "Sudan"=> "Sudan",  "Suriname"=> "Suriname",  "Swaziland"=> "Swaziland",  
                "Sweden"=> "Sweden",  "Switzerland"=> "Switzerland",  "Syria"=> "Syria",  "Taiwan"=> "Taiwan",  
                "Tajikistan"=> "Tajikistan",  "Tanzania"=> "Tanzania",  "Thailand"=> "Thailand",  
                "Timor-Leste"=> "Timor-Leste",  "Togo"=> "Togo",  "Tonga"=> "Tonga",  
                "Trinidad and Tobago"=> "Trinidad and Tobago",  "Tunisia"=> "Tunisia",  "Turkey"=> "Turkey", 
                "Turkmenistan"=> "Turkmenistan",  "Tuvalu"=> "Tuvalu",  "Uganda"=> "Uganda",  "Ukraine"=> "Ukraine", 
                "United Arab Emirates"=> "United Arab Emirates",  "United Kingdom"=> "United Kingdom",  
                "United States"=> "United States",  "Uruguay"=> "Uruguay",  "US Virgin Islands"=> "US Virgin Islands",  
                "Uzbekistan"=> "Uzbekistan",  "Vanuatu"=> "Vanuatu",  "Vatican"=> "Vatican",  "Venezuela"=> "Venezuela",  
                "Vietnam"=> "Vietnam",  "Wales"=> "Wales",  "Yemen"=> "Yemen",  "Zambia"=> "Zambia",  
                "Zimbabwe"=> "Zimbabwe")
           );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
    'fieldName' => 'event_type', 'filterType'=>'dropdown','fieldLabel'=>'Event Type', 'enableCellEdit' => true,
    'options' => array('Mini' => 'Mini', 'Featured' => 'Featured', 'Flagship' => 'Flagship', 'School' => 'School')
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
		'fieldName' => 'event_start_dt', 'fieldLabel'=>'Start Date', 
                'null_on_blank'=>true
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'event_end_dt', 'fieldLabel'=>'End Date', 
                'null_on_blank'=>true
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'cfm_start_dt', 'fieldLabel'=>'CFM Start Date', 
                'cellFilter'=>'date',
                'null_on_blank'=>true
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'cfm_end_dt', 'fieldLabel'=>'CFM End Date', 
                'null_on_blank'=>true
  );
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'lat', 'fieldLabel' => 'Latitude', 
                'null_on_blank'=>true                
);
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
                'fieldName' => 'lng', 'fieldLabel' => 'Longitude', 
                'null_on_blank'=>true
);
$tableOptions['wp_mf_global_faire']['addlFields'][] = array(
		'fieldName' => 'ID', 'type'=>'number',
		'sort' => array('direction' => 'uiGridConstants.DESC', 'priority' => 1)
);

//Ribbons
$tableOptions['wp_mf_ribbons']['addlFields']['form_id'] = array('fieldName' => 'form_id', 'fieldLabel' => 'Form ID',
    'dataSql' =>'(SELECT form_id from wp_gf_entry where wp_gf_entry.ID = entry_id) as form_id'
    );