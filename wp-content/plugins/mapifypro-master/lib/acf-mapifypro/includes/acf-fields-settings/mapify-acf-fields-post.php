<?php

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'mapify_acf_group_619f0887a40d7',
		'title' => 'MapifyPro Map Location',
		'fields' => array(
			array(
				'key' => 'mapify_acf_field_619f08c91a57a',
				'label' => 'Enable this post to be added to Maps',
				'name' => 'mapify_blog_post_as_map_location',
				'type' => 'true_false',
				'instructions' => 'Note that if this post was previously used as a Map Location, then this location will still be shown on the Map.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
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
	
endif;