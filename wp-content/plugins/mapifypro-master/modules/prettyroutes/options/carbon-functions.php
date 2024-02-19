<?php

use \PrettyRoutes\Carbon\Carbon_DataStore_Base;

if ( !function_exists('carbon_get_post_meta') ) :

function carbon_get_post_meta($id, $name, $type = null) {
	$name = $name[0] == '_' ? $name: '_' . $name;

	if ( $type == 'complex' ) {
		return carbon_get_complex_fields('CustomField', $name, $id);
	} else if ( $type == 'map' ) {
		$raw_meta = get_post_meta($id, $name, true);
		$coordinates = explode(',', $raw_meta);
		
		return array('lat'=>(float)$coordinates[0], 'lng'=>(float)$coordinates[1]);
	} else if ( $type == 'map_with_address' ) {
		$partial_meta = carbon_get_post_meta($id, $name, 'map');
		$partial_meta['address'] = get_post_meta($id, $name . '-address', true);
		
		return $partial_meta;
	}

	return get_post_meta($id, $name, true);
}

endif;

if ( !function_exists('carbon_get_complex_fields') ) :

	function carbon_get_complex_fields($type, $name, $id = null) {
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
				$input_groups = carbon_expand_nested_field($input_groups, $row, $field_name);
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