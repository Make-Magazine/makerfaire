<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'mapify_acf_group_604992a03594f',
	'title' => 'Basic Settings',
	'fields' => array(
		array(
			'key' => 'mapify_acf_field_604992b7bb672',
			'label' => 'Mapify Map Status',
			'name' => 'mapify_map_status',
			'type' => 'mapify_map_status',
			'instructions' => 'The image mode will works only if you already have uploaded the custom Maptiles image.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
		),
		array(
			'key' => 'mapify_acf_field_6049c08205a9c',
			'label' => 'Default Pin Image',
			'name' => 'mapify_default_pin_image',
			'type' => 'mapify_pin_uploader',
			'instructions' => 'This image will serve as a default pinpoint for your map. Please save the map to apply the changes.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
		),
		array(
			'key' => 'mapify_acf_field_6049c0c905a9d',
			'label' => 'Enable Mouse Zoom',
			'name' => '_map_enable_zoom',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
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
			'key' => 'mapify_acf_field_6049c10105a9e',
			'label' => 'Enable Manual Zoom',
			'name' => '_map_enable_zoom_manual',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => false,
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'map',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

acf_add_local_field_group(array(
	'key' => 'mapify_acf_group_6049c219a07ff',
	'title' => 'Tooltip & Marker / Pinpoint Settings',
	'fields' => array(
		array(
			'key' => 'mapify_acf_field_6049c22201c80',
			'label' => 'Animated Tooltips',
			'name' => '_map_animate_tooltips',
			'type' => 'select',
			'instructions' => 'Add a subtle animation to the tooltip that appears when hovering on a location.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
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
			'key' => 'mapify_acf_field_6049c2af01c81',
			'label' => 'Animated Pinpoints',
			'name' => '_map_animate_pinpoints',
			'type' => 'select',
			'instructions' => 'Add a subtle animation to the pinpoints as they populate the map.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'yes' => 'Yes',
				'no' => 'No',
			),
			'default_value' => false,
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'return_format' => 'value',
			'ajax' => 0,
			'placeholder' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'map',
			),
		),
	),
	'menu_order' => 1,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

acf_add_local_field_group(array(
	'key' => 'mapify_acf_group_6049938682e7f',
	'title' => 'Image Mode Settings',
	'fields' => array(
		array(
			'key' => 'mapify_acf_field_6049939b7729c',
			'label' => 'Mapify Map Tiles Uploader',
			'name' => 'mapify_map_tiles_uploader',
			'type' => 'mapify_map_tiles_uploader',
			'instructions' => 'You must save this map settings first, before the image can be processed to support multiple zoom levels.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'uploads-folder-name' => 'mpfy',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'map',
			),
		),
	),
	'menu_order' => 2,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

acf_add_local_field_group(array(
	'key' => 'mapify_acf_group_604993ff7ee86',
	'title' => 'Default Zoom Level and Location Settings',
	'fields' => array(
		array(
			'key' => 'mapify_acf_field_60499406a9c44',
			'label' => 'Mapify Map Selector',
			'name' => 'mapify_map_selector',
			'type' => 'mapify_map_selector',
			'instructions' => 'Use this map to preview the style, and to set your default zoom level. You may also drag and drop center location as needed.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'lattitude' => '-1.634541720929286',
			'longitude' => '119.9030508161835',
			'zoom_level' => 4,
			'map_selector' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'map',
			),
		),
	),
	'menu_order' => 3,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

endif;