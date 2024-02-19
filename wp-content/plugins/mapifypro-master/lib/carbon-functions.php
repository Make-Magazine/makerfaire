<?php

use \Mpfy\Carbon\Carbon_DataStore_Base;

if ( !function_exists('mpfy_carbon_get_post_meta') ) :

function mpfy_carbon_get_post_meta($id, $name, $type = null) {
	$name = $name[0] == '_' ? $name: '_' . $name;

	if ( $type == 'complex' ) {
		return mpfy_carbon_get_complex_fields('CustomField', $name, $id);
	} else if ( $type == 'map' ) {
		$raw_meta = get_post_meta($id, $name, true);
		$coordinates = explode(',', $raw_meta);
		
		return array('lat'=>(float)$coordinates[0], 'lng'=>(float)$coordinates[1]);
	} else if ( $type == 'map_with_address' ) {
		$partial_meta = mpfy_carbon_get_post_meta($id, $name, 'map');
		$partial_meta['address'] = get_post_meta($id, $name . '-address', true);
		
		return $partial_meta;
	}

	return get_post_meta($id, $name, true);
}

endif;


if ( !function_exists('mpfy_carbon_get_theme_option') ) :

function mpfy_carbon_get_theme_option($name, $type = null) {
	if ( $type == 'complex' ) {
		return mpfy_carbon_get_complex_fields('ThemeOptions', $name);
	} else if ( $type == 'map' ) {
		$raw_meta = get_option($name);
		$coordinates = explode(',', $raw_meta);
		
		return array('lat'=>(float)$coordinates[0], 'lng'=>(float)$coordinates[1]);
	} else if ( $type == 'map_with_address' ) {
		$partial_meta = mpfy_carbon_get_theme_option($name, 'map');
		$partial_meta['address'] = get_option($name . '-address');
		
		return $partial_meta;
	}

	return get_option($name);
}

endif;


if ( !function_exists('mpfy_carbon_get_term_meta') ) :

function mpfy_carbon_get_term_meta($id, $name, $type = null) {
	$name = $name[0] == '_' ? $name: '_' . $name;

	if ( $type == 'complex' ) {
		return mpfy_carbon_get_complex_fields('TermMeta', $name, $id);
	}

	return get_metadata('term', $id, $name, true);
}

endif;


if ( !function_exists('mpfy_carbon_get_complex_fields') ) :

function mpfy_carbon_get_complex_fields($type, $name, $id = null) {
	$datastore = Carbon_DataStore_Base::factory($type);
	
	if ( $id ) {
		$datastore->set_id($id);
	}

	$group_rows = $datastore->load_values($name);
	$input_groups = array();

	foreach ($group_rows as $row) {
		if ( !preg_match('~^' . preg_quote($name, '~') . '(?P<group>\w*)-_?(?P<key>.*?)_(?P<index>\d+)_?(?P<sub>\w+)?(-(?P<trailing>.*))?$~', $row['field_key'], $field_name) ) {
				continue;
		}
		
		$row['field_value'] = maybe_unserialize($row['field_value']);

		$input_groups[ $field_name['index'] ]['_type'] = $field_name['group'];
		if ( !empty($field_name['trailing']) ) {
			$input_groups = mpfy_carbon_expand_nested_field($input_groups, $row, $field_name);
		} else if ( !empty($field_name['sub']) ) {
			$input_groups[ $field_name['index'] ][ $field_name['key'] ][$field_name['sub'] ] = $row['field_value'];
		} else {
			$input_groups[ $field_name['index'] ][ $field_name['key'] ] = $row['field_value'];
		}
	}

	// create groups list with loaded fields
	ksort($input_groups);

	return $input_groups;
}

endif;


if ( !function_exists('mpfy_carbon_expand_nested_field') ) :

function mpfy_carbon_expand_nested_field($input_groups, $row, $field_name) {
	if ( !preg_match('~^' . preg_quote($field_name['key'], '~') . '(?P<group>\w*)-_?(?P<key>.*?)_(?P<index>\d+)_?(?P<sub>\w+)?(-(?P<trailing>.*))?$~', $field_name['key'] . '_' . $field_name['sub'] . '-' . $field_name['trailing'], $subfield_name) ) {
		return $input_groups;
	}

	$input_groups[ $field_name['index'] ][$field_name['key']][ $subfield_name['index'] ]['_type'] = $subfield_name['group'];

	if ( !empty($subfield_name['trailing']) ) {
		$input_groups[ $field_name['index'] ][$field_name['key']] = mpfy_carbon_expand_nested_field($input_groups[ $field_name['index'] ][$field_name['key']], $row, $subfield_name);
	} else if ( !empty($subfield_name['sub']) ) {
		$input_groups[ $field_name['index'] ][$field_name['key']][ $subfield_name['index'] ][$subfield_name['key']][$subfield_name['sub']] = $row['field_value'];
	} else {
		$input_groups[ $field_name['index'] ][$field_name['key']][ $subfield_name['index'] ][$subfield_name['key']] = $row['field_value'];
	}

	return $input_groups;
}

endif;


if ( !function_exists('mpfy_carbon_twitter_widget_registered') ) :

function mpfy_carbon_twitter_widget_registered() {
	global $wp_widget_factory;
	$widget_enabled = !empty($wp_widget_factory->widgets) && !empty($wp_widget_factory->widgets['CrbLatestTweetsWidget']);
	$manually_enabled = defined('ENABLE_TWITTER_CONFIG') && ENABLE_TWITTER_CONFIG;

	return $widget_enabled || $manually_enabled;
}

endif;


if ( !function_exists('mpfy_carbon_twitter_widget_activated') ) :

function mpfy_carbon_twitter_widget_activated() {
	return is_active_widget(false, false, 'carbon_latest_tweets', true);
}

endif;


if ( !function_exists('mpfy_carbon_twitter_is_configured') ) :

function mpfy_carbon_twitter_is_configured() {
	$option_names = array(
		'twitter_oauth_access_token',
		'twitter_oauth_access_token_secret',
		'twitter_consumer_key',
		'twitter_consumer_secret'
	);
	$configured = true;

	foreach ($option_names as $optname) {
		if (!get_option($optname)) {
			$configured = false;
			break;
		}
	}

	return $configured;
}

endif;


if ( !function_exists('mpfy_carbon_twitter_is_config_valid') ) :

function mpfy_carbon_twitter_is_config_valid() {
	$tweets = TwitterHelper::get_tweets('cnn', 1, true);
	if (!$tweets) {
		return false;
	}
	return true;
}

endif;


if ( !function_exists('mpfy_carbon_twitter_widget_no_config_warning') ) :

function mpfy_carbon_twitter_widget_no_config_warning() {
	?>
	<div id="message" class="error">
		<p>
			<?php 
			printf(
				__("You've inserted a \"Latest Tweets\" widget, but it will not work unless you configure your twitter settings. In order to do that, go to %sTheme Options &raquo; Twitter Settings%s", 'crb'),
				'<a href="' . admin_url('/admin.php?page=crbn-twitter-settings.php') . '">',
				'</a>'
			);
			?>
		</p>
	</div>
	<?php
}

endif;


if ( !function_exists('mpfy_carbon_twitter_widget_wrong_config_warning') ) :

function mpfy_carbon_twitter_widget_wrong_config_warning() {
	?>
	<div id="message" class="error">
		<p><?php _e('Warning: You seem to have configured your Twitter settings, but they are invalid. Please configure them in order to be able to use the "Latest Tweets" widget.', 'crb'); ?></p>
		<p>
			<?php 
			printf(
				__("In order to do that, go to %sTheme Options &raquo; Twitter Settings%s", 'crb'),
				'<a href="' . admin_url('/admin.php?page=crbn-twitter-settings.php') . '">',
				'</a>'
			);
			?>
		</p>
	</div>
	<?php
}

endif;


add_action('admin_menu', 'mpfy_carbon_twitter_widget_config_check');

if ( !function_exists('mpfy_carbon_twitter_widget_config_check') ) :

function mpfy_carbon_twitter_widget_config_check() {
	if (!mpfy_carbon_twitter_widget_registered() || !mpfy_carbon_twitter_widget_activated()) {
		return;
	}

	if (!mpfy_carbon_twitter_is_configured()) {
		add_action('admin_notices', 'mpfy_carbon_twitter_widget_no_config_warning');
	} elseif(!mpfy_carbon_twitter_is_config_valid()) {
		add_action('admin_notices', 'mpfy_carbon_twitter_widget_wrong_config_warning');
	}
}

endif;


if ( ! function_exists( 'mpfy_carbon_clean_class_tag' ) ) :

function mpfy_carbon_clean_class_tag( $tag ) {
	$clean_tag = str_replace( 'Mpfy\\Carbon\\', '', $tag );
	return 'Mpfy_' . $clean_tag;
}

endif;
