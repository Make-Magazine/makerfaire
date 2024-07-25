<?php
namespace GravityWP\Advanced_Merge_Tags;

defined( 'ABSPATH' ) || die();

/**
 * This function filters entries in GravityViews based on a user meta property that stores a JSON-encoded array of filter values.
 * It requires the GView Advanced Filters extension to be active.
 *
 * Possible variants:
 * {gwp_gview_advanced_filter:user_meta_key:your_meta_key}
 * {gwp_gview_advanced_filter:user_email:gwp_get_matched_entry_value form_id=1 match_id=2 return_id=3}
 * {gwp_gview_advanced_filter:user_login:gwp_get_matched_entry_value form_id=1 match_id=2 return_id=3}
 *
 *  Usage Example:
 * Define a user meta key 'aiwos_environments' with a value like '["REGELING1","REGELING2"]'.
 * Using {gwp_gview_advanced_filter:user_meta_key:aiwos_environments} in your view like:
 * - 'NAAM REGELING' is '{gwp_gview_advanced_filter:user_meta_key:aiwos_environments}'
 *
 *  will dynamically generate OR-filters like:
 * - 'NAAM REGELING' is 'REGELING1'
 * - 'NAAM REGELING' is 'REGELING2'
 */
class GK_Advanced_Filter_Merge_Tag {
	/**
	 * Constructor for the GK_Advanced_Filter_Merge_Tag class.
	 *
	 * Registers a filter hook to process advanced merge tags in GravityViews.
	 */
	public function __construct() {
		add_filter( 'gk/advanced-filters/filters', array( $this, 'filter_merge_tags' ), 10, 1 );
	}

	/**
	 * This method parses placeholders within the view's filter settings and replaces them with actual values pulled from user meta data,
	 * allowing for dynamic and user-specific view content.
	 *
	 * @param array $filters Existing filters from GravityViews that may contain placeholders for dynamic content.
	 * @return array Modified filters with dynamic values based on user meta data.
	 */
	public function filter_merge_tags( $filters ) {
		// Check if conditions are set in the filters array.
		if ( ! isset( $filters['conditions'] ) ) {
			return $filters;
		}

		$replace_list      = array();
		$current_user_id   = get_current_user_id();
		$allowed_user_keys = array(
			'user_meta_key',
			'user_email',
			'user_login',
		);

		// Iterate through the first layer of GView advanced filter conditions (AND groups).
		foreach ( $filters['conditions'] as $and_index => $and_condition_set ) {
			// Iterate through the second layer of conditions (OR groups).
			foreach ( $and_condition_set['conditions'] as $or_index => $or_condition ) {
				// Identify conditions that need replacement based on specific merge tags.
				if ( is_string( $or_condition['value'] ) && strpos( $or_condition['value'], '{gwp_gview_advanced_filter' ) !== false ) {
					$pattern = '/{gwp_gview_advanced_filter:(.*?)}/';
					$matches = array();
					if ( preg_match( $pattern, $or_condition['value'], $matches ) ) {
						gravitywp_advanced_merge_tags()->log_debug( __METHOD__ . '(): gwp_gview_advanced_filter, processing: ' . $or_condition['value'] ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
						$parameters = explode( ':', $matches[1] );

						// Skip processing if parameters are invalid or missing.
						if ( ! in_array( $parameters[0], $allowed_user_keys, true ) || empty( $parameters[1] ) ) {
							continue;
						}

						// Fetch the appropriate user data based on the parameter.
						if ( $parameters[0] === 'user_meta_key' ) {
							$value = get_user_meta( $current_user_id, $parameters[1], true );
						} else {
							if ( $parameters[0] === 'user_email' ) {
								$value = wp_get_current_user()->user_email;
							} elseif ( $parameters[0] === 'user_login' ) {
								$value = wp_get_current_user()->user_login;
							} else {
								gravitywp_advanced_merge_tags()->log_error( __METHOD__ . '(): Unknown gwp_gview_advanced_filter parameter: ' . $parameters[0] );
								continue;
							}
							// Process gwp_get_matched_entry_value modifier.
							$value = str_replace( '{gwp_gview_advanced_filter:' . $parameters[0] . ':gwp_get_matched_entry_value', "{gwp_get_matched_entry_value value='$value'", $or_condition['value'] );
							$value = GravityWP_Advanced_Merge_Tags::gwp_process_mergetags( $value, false, false, false, false, false, 'text' );
						}

						// Decode bracket characters if necessary.
						$replace = array( '[', ']' );
						$find    = array( '&#91;', '&#93;' );
						$value   = str_replace( $find, $replace, $value );

						if ( is_string( $value ) ) {
							$values_to_filter = json_decode( $value );
						}

						// Continue if there are no valid values to filter.
						if ( empty( $values_to_filter ) ) {
							gravitywp_advanced_merge_tags()->log_debug( __METHOD__ . '(): gwp_gview_advanced_filter no valid values found.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
							continue;
						}
						gravitywp_advanced_merge_tags()->log_debug( __METHOD__ . '(): gwp_gview_advanced_filter, values to filter: ' . print_r( $values_to_filter, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r

						// Add to the list for replacing existing conditions.
						$replace_list[] = array(
							'and_index'        => $and_index,
							'or_index'         => $or_index,
							'values_to_filter' => $values_to_filter,
							'replace'          => $matches[0],
						);
					}
				}
			}
		}

		// Process replacements in the filter conditions.
		if ( count( $replace_list ) > 0 ) {
			foreach ( $replace_list as $replace_item ) {
				$values_to_filter       = $replace_item['values_to_filter'];
				$access_filter_template = $filters['conditions'][ $replace_item['and_index'] ]['conditions'][ $replace_item['or_index'] ];
				$first_condition        = true;

				foreach ( $values_to_filter as $allowed_value ) {
					$new_condition          = $access_filter_template;
					$new_condition['value'] = str_replace( $replace_item['replace'], $allowed_value, $new_condition['value'] );
					$new_condition['_id']   = wp_generate_password( 9, false );

					if ( $first_condition ) {
						$filters['conditions'][ $replace_item['and_index'] ]['conditions'][ $replace_item['or_index'] ] = $new_condition;
						$first_condition = false;
					} else {
						$filters['conditions'][ $replace_item['and_index'] ]['conditions'][] = $new_condition;
					}
				}
			}
		}

		return $filters;
	}
}

new GK_Advanced_Filter_Merge_Tag();
