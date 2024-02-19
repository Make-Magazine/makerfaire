<?php

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'mapify_acf_group_607567f3b0b1e',
		'title' => 'MapifyPro Settings',
		'fields' => array(
			array(
				'key' => 'mapify_acf_field_607567ff4331b',
				'label' => 'Load ShareThis',
				'name' => 'mpfy_load_sharethis',
				'type' => 'select',
				'instructions' => 'MapifyPro uses ShareThis for map location sharing. If you are already using ShareThis you may wish to disable this in order to avoid loading it twice.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'y' => 'Yes',
					'n' => 'No',
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
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'mapifypro-settings',
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

	acf_add_local_field_group(array(
		'key' => 'mapify_acf_group_62a179e82e0b0',
		'title' => 'Troubleshoot MapifyPro',
		'fields' => array(
			array(
				'key' => 'mapify_acf_field_62a17a0fd338f',
				'label' => 'Enable JavaScript Cache Reset',
				'name' => 'mapifypro_enable_javascript_cache_buster',
				'type' => 'true_false',
				'instructions' => 'Enable this setting if your MapifyPro map is broken because of a JavaScript caching issue.',
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
				'ui_on_text' => 'Enable',
				'ui_off_text' => 'Disable',
			),
			array(
				'key' => 'mapify_acf_field_62a17a98d3390',
				'label' => 'Cache Reset Interval',
				'name' => 'mapifypro_cache_buster_creation_interval',
				'type' => 'select',
				'instructions' => 'This is the interval that a random querystring variable will be re-generated. Any caching product will detect the new querystring, and will serve the most recent JavaScript code to ensure that your site visitors will view the most recent version of MapifyPro plugin that has been installed.<br>If you wish to test this feature, the "<b>On every page load</b>" option is recommended. If that works for you, then "<b>Hourly</b>" is recommended. Reminder to prefix the ver with the version number "<b>v' . MAPIFY_PLUGIN_VERSION . '</b>", even if the cache reset feature is turned off, so now, if the vcache reset feature is turned off it will be "<b>ver=v' . MAPIFY_PLUGIN_VERSION . '</b>", if turned on, it will be "<b>ver=' . MAPIFY_PLUGIN_VERSION . '-62a8023c8b016</b>',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'always' => 'On every page load',
					'hourly' => 'Hourly',
					'twice_a_day' => 'Twice a day',
					'daily' => 'Daily',
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
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'mapifypro-settings',
				),
			),
		),
		'menu_order' => 1,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'field',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_rest' => 0,
	));
	
endif;