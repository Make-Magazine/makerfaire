<?php

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'prettyroutes_acf_group_60adf4912a2b7',
		'title' => 'Route Map Options',
		'fields' => array(
			array(
				'key' => 'prettyroutes_acf_field_60adfba9ef555',
				'label' => 'Map Information',
				'name' => 'map_information',
				'type' => 'prettyroutes_map_status',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
			),
			array(
				'key' => 'prettyroutes_acf_field_60adf4a7b93ed',
				'label' => 'Center Mode',
				'name' => '_route_map_center_option',
				'type' => 'select',
				'instructions' => '<b>Auto:</b> The map will automatically center on all displayed routes.<br><b>Manual:</b> The display map will be centered through the map below.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'auto' => 'Auto',
					'manual' => 'Manual',
				),
				'default_value' => false,
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'prettyroutes_acf_field_60ae101b8209b',
				'label' => 'Map Center',
				'name' => 'map_center',
				'type' => 'prettyroutes_map_location',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'lattitude' => '-37.847097612887985',
				'longitude' => '144.97131142516656',
				'zoom_level' => 7,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'route_map',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'field',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));
	
endif;