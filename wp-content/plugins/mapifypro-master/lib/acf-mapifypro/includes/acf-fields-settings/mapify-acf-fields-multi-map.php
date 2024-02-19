<?php

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'mapify_acf_group_608a2ec7dae51',
		'title' => 'Multi Map Shortcode Generator',
		'fields' => array(
			array(
				'key' => 'mapify_acf_field_608a372ba67b0',
				'label' => 'Maps To Include',
				'name' => 'maps_to_include',
				'type' => 'relationship',
				'instructions' => 'Choose any maps you want to include to your multi-maps',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array(
					0 => 'map',
				),
				'taxonomy' => '',
				'filters' => array(
					0 => 'search',
				),
				'elements' => '',
				'min' => 1,
				'max' => '',
				'return_format' => 'id',
			),
			array(
				'key' => 'mapify_acf_field_608a37cca67b3',
				'label' => 'Maps Height',
				'name' => 'maps_height',
				'type' => 'number',
				'instructions' => 'In pixels',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 300,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => 100,
				'max' => '',
				'step' => '',
			),
			array(
				'key' => 'mapify_acf_field_608a3768a67b1',
				'label' => 'Label',
				'name' => 'label',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Select Map',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'mapify_acf_field_608a37a8a67b2',
				'label' => 'Label Color',
				'name' => 'label_color',
				'type' => 'color_picker',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '#13c490',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'mapifypro-multi-map',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
	));
	
endif;