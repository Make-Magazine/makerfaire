<?php
/**
 * Gravity Geolocation helper class.
 *
 * @author Eyal Fitoussi.
 *
 * @package gravityforms-geolocation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GFGEO_Helper class
 */
class GFGEO_Helper {

	/**
	 * [__construct description]
	 */
	public function __construct() {

		// Modify fields settings if needed.
		add_filter( 'gfgeo_field_settings_args', array( $this, 'modify_field_settings' ) );
		add_action( 'gfgeo_update_option_prefix', array( $this, 'update_prefix' ), 10 );
	}

	/**
	 * Get location fields array.
	 *
	 * @return [type] [description]
	 */
	public static function get_location_fields() {

		return array(
			''                   => __( 'Disabled', 'gfgeo' ),
			'status'             => '',
			'place_name'         => __( 'Place Name', 'gfgeo' ),
			'street_number'      => __( 'Street Number', 'gfgeo' ),
			'street_name'        => __( 'Street Name', 'gfgeo' ),
			'street'             => __( 'Street ( number + name )', 'gfgeo' ),
			'street_bw'          => __( 'Street ( name + number )', 'gfgeo' ),
			'premise'            => __( 'Premise', 'gfgeo' ),
			'subpremise'         => __( 'Subpremise', 'gfgeo' ),
			'neighborhood'       => __( 'Neighborhood', 'gfgeo' ),
			'city'               => __( 'City', 'gfgeo' ),
			'county'             => __( 'County', 'gfgeo' ),
			'region_code'        => __( 'Region Code ( state code )', 'gfgeo' ),
			'region_name'        => __( 'Region Name ( state name )', 'gfgeo' ),
			'postcode'           => __( 'Postcode / Zipcode', 'gfgeo' ),
			'country_code'       => __( 'Country Code', 'gfgeo' ),
			'country_name'       => __( 'Country Name', 'gfgeo' ),
			'address'            => __( 'Address', 'gfgeo' ),
			'formatted_address'  => __( 'Formatted Address', 'gfgeo' ),
			'latitude'           => __( 'Latitude', 'gfgeo' ),
			'longitude'          => __( 'Longitude', 'gfgeo' ),
			'distance_text'      => __( 'Distance ( text )', 'gfgeo' ),
			'distance_value'     => __( 'Distance ( value )', 'gfgeo' ),
			'distance_in_meters' => __( 'Distance ( value in meters )', 'gfgeo' ),
			'duration_text'      => __( 'Duration ( text )', 'gfgeo' ),
			'duration_value'     => __( 'Duration ( value in seconds )', 'gfgeo' ),
		);
	}

	/**
	 * Get location fields array.
	 *
	 * @return [type] [description]
	 */
	public static function get_dynamic_directions_fields() {

		return array(
			''                   => __( 'disabled', 'gfgeo' ),
			'distance_text'      => __( 'Distance ( text )', 'gfgeo' ),
			'distance_value'     => __( 'Distance ( value )', 'gfgeo' ),
			'distance_in_meters' => __( 'Distance ( value in meters )', 'gfgeo' ),
			'duration_text'      => __( 'Duration ( text )', 'gfgeo' ),
			'duration_value'     => __( 'Duration ( value in seconds )', 'gfgeo' ),
		);
	}

	/**
	 * Register Google Maps API
	 */
	public static function get_google_maps_url() {

		wp_deregister_script( 'google-maps' );

		// Build Google API url. Elements can be modified via filters.
		return apply_filters(
			'gfgeo_google_maps_api_url',
			array(
				'protocol' => is_ssl() ? 'https' : 'http',
				'url_base' => '://maps.googleapis.com/maps/api/js?',
				'url_data' => http_build_query(
					apply_filters(
						'gfgeo_google_maps_api_args',
						array(
							'libraries' => 'places',
							'region'    => trim( GFGEO_GOOGLE_MAPS_COUNTRY ),
							'language'  => trim( GFGEO_GOOGLE_MAPS_LANGUAGE ),
							'key'       => trim( GFGEO_GOOGLE_MAPS_API ),
						)
					)
				),
			)
		);
	}

	/**
	 * Lk_status.
	 *
	 * @var [type]
	 */
	public static $lk_status = false;

	/**
	 * Check if user update form.
	 *
	 * @param  string $form_id [description].
	 *
	 * @return boolean          [description]
	 */
	public static function is_update_user_form( $form_id = '' ) {

		// verify requierments.
		if ( empty( $form_id ) || ! class_exists( 'GF_User_Registration' ) ) {
			return false;
		}

		// get form registration feeds.
		$ur_feeds = GFAPI::get_feeds( null, $form_id, 'gravityformsuserregistration' );

		// look for user update feed.
		if ( is_array( $ur_feeds ) && ! empty( $ur_feeds[0]['meta']['feedType'] ) && 'update' === $ur_feeds[0]['meta']['feedType'] ) {

			return $ur_feeds;

		} else {

			return false;
		}
	}

	/**
	 * Get user location from GEO my WP database.
	 *
	 * Not being used anymore?
	 *
	 * @param  integer $post_id [description].
	 *
	 * @return [type]           [description]
	 */
	public static function get_gmw_post_location( $post_id = 0 ) {

		if ( empty( $post_id ) || ! class_exists( 'GEO_my_WP' ) ) {
			return false;
		}

		global $wpdb;

		$output = $wpdb->get_row(
			$wpdb->prepare(
				"
    			SELECT 
	    			street,
	    			apt as premise,
	    			city,
	    			state as region_code,
	    			state_long as region_name,
	    			zipcode as postcode,
	    			country as country_code,
	    			country_long as country_name,
	    			address,
	    			formatted_address,
	    			lat as latitude,
	    			`long` as longitude,
	    			map_icon
    			FROM 
    				`{$wpdb->prefix}places_locator` 
    			WHERE 
    				post_id = %d",
				$post_id
			),
			ARRAY_A
		); // WPCS: db call ok, cache ok.

		return ! empty( $output ) ? $output : array();
	}

	/**
	 * Get user location from GEO my WP database.
	 *
	 * Not being used anymore?
	 *
	 * @param  integer $user_id [description].
	 *
	 * @return [type]           [description]
	 */
	public static function get_user_location( $user_id = 0 ) {

		if ( empty( $user_id ) || ! class_exists( 'GEO_my_WP' ) ) {
			return false;
		}

		global $wpdb;

		$output = $wpdb->get_row(
			$wpdb->prepare(
				'
    			SELECT 
	    			street,
	    			apt as premise,
	    			city,
	    			state as region_code,
	    			state_long as region_name,
	    			zipcode as postcode,
	    			country as country_code,
	    			country_long as country_name,
	    			address,
	    			formatted_address,
	    			lat as latitude,
	    			`long` as longitude,
	    			map_icon
    			FROM 
    				`wppl_friends_locator` 
    			WHERE 
    				member_id = %d',
				$user_id
			),
			ARRAY_A
		); // WPCS: db call ok, cache ok.

		return ! empty( $output ) ? $output : array();
	}

	/**
	 * Generate link to Google Maps showing the address.
	 *
	 * @param  [type] $data [description].
	 *
	 * @return [type]       [description]
	 */
	public static function get_map_link( $data ) {

		if ( empty( $data ) ) {
			return '';
		}

		$map_link = array();
		$location = is_array( $data ) ? $data['latitude'] . ',' . $data['longitude'] : str_replace( ' ', '+', $data );

		$map_link['a']     = '<a style="font-size:13px;text-decoration: underline" class="gfgeo-map-link coordinates" href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . $location ) . '" target="_blank">';
		$map_link['title'] = __( 'View in Google Maps', 'gfgeo' );
		$map_link['/a']    = '</a>';

		$map_link = apply_filters( 'gfgeo_map_link_output', $map_link, $data );

		return implode( '', $map_link );
	}

	/**
	 * Output bp profile fields dropdown.
	 *
	 * @param  [type] $field [description].
	 */
	public static function bp_profile_fields_dropdown( $field ) {

		global $bp;

		if ( bp_is_active( 'xprofile' ) ) {

			if ( function_exists( 'bp_has_profile' ) ) {

				if ( bp_has_profile( 'hide_empty_fields=0' ) ) { ?>

					<?php $field = esc_attr( $field ); ?>

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field ?>" class="section_label"> 
						<?php esc_html_e( 'BuddyPress Profile Field', 'gfgeo' ); ?>
					</label> 

					<select 
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field"
						style="width: 100%"
						onchange="SetFieldProperty( 'gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field', jQuery( this ).val() );">
						<option value="">
							<?php esc_html_e( 'Disable', 'gfgeo' ); ?>	
						</option>
						<?php

						while ( bp_profile_groups() ) {

							bp_the_profile_group();

							while ( bp_profile_fields() ) {

								bp_the_profile_field();
								?>
								<?php if ( 'datebox' !== bp_get_the_profile_field_type() ) { ?>
									<option value="<?php bp_the_profile_field_id(); ?>">
										<?php bp_the_profile_field_name(); ?>
									</option>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</select>
					<?php
				}
			}
		}
	}

	/**
	 * Custom meta fields output for form editor.
	 *
	 * @param  string $type user meta or custom field.
	 */
	public static function custom_meta_fields( $type = 'custom_field' ) {

		echo '<ul class="gfgeo-geocoder-meta-fields-wrapper">';

		foreach ( self::get_location_fields() as $field => $name ) {

			if ( 'status' === $field || '' === $field ) {
				continue;
			}

			$field = esc_attr( $field );
			?>
			<li class="gfgeo-setting">
				<label for="gmw-<?php echo esc_attr( $name ); ?>" class="section_label">
					<?php echo esc_html( $name ); ?>
				</label> 

				<div class="custom-field-content">

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta" class="section_label"> 
						<?php esc_html_e( 'Post Meta ( custom field )', 'gfgeo' ); ?>
					</label> 
					<input 
						style="display: block; width: 100%;margin-bottom:20px"
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta"
						type="text" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta" 
						onkeyup="SetFieldProperty( jQuery( this ).attr( 'name' ), this.value );"
					/>

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta ?>" class="section_label"> 
						<?php esc_html_e( 'User Meta', 'gfgeo' ); ?>
					</label> 
					<input 
						style="display: block; width: 100%;margin-bottom:20px"
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta"
						type="text" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta" 
						onkeyup="SetFieldProperty( jQuery( this ).attr( 'name' ), this.value );"
					/>
					<!-- BuddyPress profile fields -->
					<?php
					if ( class_exists( 'BuddyPress' ) ) {
						self::bp_profile_fields_dropdown( $field );
					}
					?>
				</div>
			</li>
		<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Generate Google static map.
	 *
	 * @param  [type] $value       [description].
	 *
	 * @param  [type] $map_field   [description].
	 *
	 * @param  [type] $has_markers [description].
	 *
	 * @param  [type] $where       [description].
	 *
	 * @return [type]              [description]
	 */
	public static function generate_static_map( $value, $map_field, $has_markers, $where ) {

		$map_values = maybe_unserialize( $value );

		// Zoom level.
		if ( ! empty( $value['zoom_level'] ) ) {

			$zoom_level = $value['zoom_level'];

		} else {

			$zoom_level = ! empty( $map_field->gfgeo_zoom_level ) ? $map_field->gfgeo_zoom_level : '7';
		}

		// Map type.
		if ( ! empty( $value['map_type'] ) ) {

			$map_type = $value['map_type'];

		} else {
			$map_type = ! empty( $map_field->gfgeo_map_type ) ? $map_field->gfgeo_map_type : 'ROADMAP';
		}

		// Map center.
		if ( isset( $value['map_center'] ) ) {

			$map_center = $value['map_center'];

		} else {

			$map_center = array(
				'lat' => ! empty( $map_field->gfgeo_map_default_latitude ) ? $map_field->gfgeo_map_default_latitude : '40.7827096',
				'lng' => ! empty( $map_field->gfgeo_map_default_longitude ) ? $map_field->gfgeo_map_default_longitude : '-73.965309',
			);
		}

		$markers = '';

		if ( $has_markers ) {

			$markers           = 'icon:' . $map_values['markers'][0]['marker_url'] . '|' . $map_values['markers'][0]['lat'] . ',' . $map_values['markers'][0]['lng'];
			$count             = 0;
			$map_center['lat'] = $map_values['markers'][0]['lat'];
			$map_center['lng'] = $map_values['markers'][0]['lng'];

			if ( 1 < count( $map_values['markers'] ) ) {

				foreach ( $map_values['markers'] as $marker ) {

					$count++;

					if ( $count > 1 ) {
						$markers .= '&markers=icon:' . $marker['marker_url'] . '| ' . $marker['lat'] . ',' . $marker['lng'];
					}
				}

				$zoom_level = '';
			}
		}

		$url_args = array(
			'center'  => $map_center['lat'] . ',' . $map_center['lng'],
			'markers' => $markers,
			'size'    => '500x300',
			'zoom'    => $zoom_level,
			'maptype' => strtolower( $map_type ),
			'key'     => GFGEO_GOOGLE_MAPS_API,
		);

		// build the map query. Map settings can be modified via the filters below.
		$map_args = apply_filters(
			'gfgeo_google_map_field_map_settings',
			array(
				'protocol' => is_ssl() ? 'https' : 'http',
				'url_base' => '://maps.googleapis.com/maps/api/staticmap?',
				'url_data' => urldecode(
					http_build_query(
						apply_filters(
							'gfgeo_google_map_field_map_settings_args',
							$url_args,
							$where
						),
						'',
						'&amp;'
					)
				),
			),
			$where
		);

		return '<div class="gfgeo-static-map-warpper"><img src="' . esc_url( implode( '', $map_args ) ) . '" /></div>';
	}

	/**
	 * Set plugin's prefix in database so we could easily retrieve it when needed.
	 *
	 * @param string $prefix prefix.
	 */
	public function update_prefix( $prefix ) {
		update_option( 'gfgeo_prefix', $prefix );
	}

	/**
	 * Get directions details.
	 *
	 * @param  [type] $value [description].
	 *
	 * @return [type]        [description]
	 */
	public static function get_directions_details_output( $value ) {

		// unserialize data.
		$value = maybe_unserialize( $value );

		if ( ! is_array( $value ) ) {
			return __( 'Data is not available', 'gfgeo' );
		}

		$count   = 1;
		$output  = '';
		$output .= '<div class="gfgeo-directions-details-wrapper">';
		$output .= '<div class="gfgeo-directions-details-total-distance"><strong>' . __( 'Total Distance:', 'gfgeo' ) . '</strong> ' . $value['complete']['distance']['text'] . '</div>';

		if ( ! empty( $value['complete']['duration']['text'] ) ) {
			$output .= '<div class="gfgeo-directions-details-total-duration"><strong>' . __( 'Total Duration:', 'gfgeo' ) . '</strong> ' . $value['complete']['duration']['text'] . '</div>';
		}

		$output .= '<span class="gfgeo-directions-details-trigger" onClick=\'jQuery( this ).closest( ".gfgeo-directions-details-wrapper" ).find( ".gfgeo-directions-details-inner" ).slideToggle();\'>Show legs details</span>';
		$output .= '<div class="gfgeo-directions-details-inner" >';

		foreach ( $value['legs'] as $leg ) {

			$output .= '<ul class="gfgeo-directions-details-leg-wrapper">';
			$output .= '<li class="gfgeo-directions-details-label">Leg ' . $count . '</li>';
			$output .= '<li class="gfgeo-directions-details-label">Origin ( Geocoder ' . explode( '_', $leg['geocoders'][0], 2 )[1] . ' )</li>';
			$output .= '<li class="gfgeo-directions-details-content">' . $leg['addresses'][0] . '</li>';
			$output .= '<li class="gfgeo-directions-details-label">Destination ( Geocoder ' . explode( '_', $leg['geocoders'][1], 2 )[1] . ' )</li>';
			$output .= '<li class="gfgeo-directions-details-content">' . $leg['addresses'][1] . '</li>';
			$output .= '<li class="gfgeo-directions-details-label">' . __( 'Distance', 'gfgeo' ) . '</li>';
			$output .= '<li class="gfgeo-directions-details-content"><span>' . $leg['distance']['text'] . '</span></li>';

			if ( ! empty( $leg['duration']['text'] ) ) {
				$output .= '<li class="gfgeo-directions-details-label">' . __( 'Duration', 'gfgeo' ) . '</li>';
				$output .= '<li class="gfgeo-directions-details-content"><span>' . $leg['duration']['text'] . '</span></li>';
			}

			$output .= '</ul>';

			$count++;
		}

		$output .= '</div>';
		$output .= self::get_directions_link( $value );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Generate link to Google Maps showing the directions.
	 *
	 * @param  [type] $value [description].
	 *
	 * @return [type]        [description].
	 */
	public static function get_directions_link( $value ) {

		$value = maybe_unserialize( $value );

		if ( empty( $value ) || ! is_array( $value ) ) {
			return __( 'Directions are not available', 'gfgeo' );
		}

		$waypoints = array();
		$wp_link   = '';

		if ( ! empty( $value['waypoints'] ) ) {

			// Remove last waypoint which is the destination location.
			array_pop( $value['waypoints'] );

			foreach ( $value['waypoints'] as $wp ) {
				$waypoints[] = implode( ',', $wp );
			}

			$wp_link = '&waypoints=' . implode( '|', $waypoints );
		}

		$link = 'https://www.google.com/maps/dir/?api=1&origin=' . implode( ',', $value['origin'] ) . $wp_link . '&destination=' . implode( ',', $value['destination'] );

		return '<div class="gfgeo-get-directions-link-wrapper"><a href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'View Directions on Google Maps', 'gfgeo' ) . '</a></div>';
	}

	/**
	 * Modify fields settings only if needed.
	 *
	 * @return [type]           [description]
	 */
	public function modify_field_settings() {
		return array();
	}

	/**
	 * Get locator button.
	 *
	 * @param  integer $form_id the form ID.
	 *
	 * @param  object  $field   the field object.
	 *
	 * @param  string  $type    type of button.
	 *
	 * @return [type]        [description]
	 */
	public static function get_locator_button( $form_id, $field, $type = 'button' ) {

		$form_id      = absint( $form_id );
		$id           = (int) $field->id;
		$field_id     = ! empty( $field->gfgeo_id ) ? esc_attr( $field->gfgeo_id ) : esc_attr( $form_id . '_' . $id );
		$geocoder_id  = ! empty( $field->gfgeo_geocoder_id ) ? esc_attr( $field->gfgeo_geocoder_id ) : esc_attr( $form_id . '_' . $field->gfgeo_geocoder_id );
		$button_label = ! empty( $field->gfgeo_locator_button_label ) ? $field->gfgeo_locator_button_label : '';
		$button_label = apply_filters( 'gfgeo_locator_button_label', $button_label, $form_id, $field, $type );
		$type         = esc_attr( $type );

		$loader_img = GFGEO_URL . '/assets/images/loader.svg';

		// loader.
		$loader = "<img src='{$loader_img}' class='gfgeo-locator-loader gfgeo-icon-spinner loader-{$field_id} skip-lazy' style='display:none;box-shadow:none;border-radius:0' width='16' height='auto' />";

		// generate the button element.
		$output = "<div id='gfgeo-locator-button-wrapper-{$field_id}' class='gfgeo-locator-button-wrapper {$type}-locator'>";

		if ( 'infield' === $type ) {

			$image_url = GFGEO_URL . '/assets/images/locator.svg';

			$output .= "<img src='{$image_url}' id='gfgeo-infield-locator-button-{$field_id}' class='gfgeo-locator-button infield-locator skip-lazy' data-geocoder_id='{$geocoder_id}' data-field_id='{$field_id}' style='box-shadow: none;border-radius:0'  width='16' height='auto' />";
			$output .= $loader;

		} else {
			$output .= "<button id='gfgeo-locator-button-{$field_id}' class='gfgeo-locator-button gfgeo-form-button' data-geocoder_id='{$geocoder_id}' data-field_id='{$field_id}' value='{$button_label}'><span>{$button_label}</span>{$loader}</button>";
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Get countries array values.
	 *
	 * @return [type] [description]
	 */
	public static function get_countries() {

		$countries = array(
			'AF' => 'Afghanistan (‫افغانستان‬‎)',
			'AX' => 'Åland Islands (Åland)',
			'AL' => 'Albania (Shqipëri)',
			'DZ' => 'Algeria (‫الجزائر‬‎)',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia (Հայաստան)',
			'AW' => 'Aruba',
			'AC' => 'Ascension Island',
			'AU' => 'Australia',
			'AT' => 'Austria (Österreich)',
			'AZ' => 'Azerbaijan (Azərbaycan)',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain (‫البحرين‬‎)',
			'BD' => 'Bangladesh (বাংলাদেশ)',
			'BB' => 'Barbados',
			'BY' => 'Belarus (Беларусь)',
			'BE' => 'Belgium (België)',
			'BZ' => 'Belize',
			'BJ' => 'Benin (Bénin)',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan (འབྲུག)',
			'BO' => 'Bolivia',
			'BA' => 'Bosnia and Herzegovina (Босна и Херцеговина)',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil (Brasil)',
			'IO' => 'British Indian Ocean Territory',
			'VG' => 'British Virgin Islands',
			'BN' => 'Brunei',
			'BG' => 'Bulgaria (България)',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi (Uburundi)',
			'KH' => 'Cambodia (កម្ពុជា)',
			'CM' => 'Cameroon (Cameroun)',
			'CA' => 'Canada',
			'IC' => 'Canary Islands (islas Canarias)',
			'CV' => 'Cape Verde (Kabu Verdi)',
			'BQ' => 'Caribbean Netherlands',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic (République centrafricaine)',
			'EA' => 'Ceuta and Melilla (Ceuta y Melilla)',
			'TD' => 'Chad (Tchad)',
			'CL' => 'Chile',
			'CN' => 'China (中国)',
			'CX' => 'Christmas Island',
			'CP' => 'Clipperton Island',
			'CC' => 'Cocos (Keeling) Islands (Kepulauan Cocos (Keeling))',
			'CO' => 'Colombia',
			'KM' => 'Comoros (‫جزر القمر‬‎)',
			'CD' => 'Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)',
			'CG' => 'Congo (Republic) (Congo-Brazzaville)',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'CI' => 'Côte d’Ivoire',
			'HR' => 'Croatia (Hrvatska)',
			'CU' => 'Cuba',
			'CW' => 'Curaçao',
			'CY' => 'Cyprus (Κύπρος)',
			'CZ' => 'Czech Republic (Česká republika)',
			'DK' => 'Denmark (Danmark)',
			'DG' => 'Diego Garcia',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic (República Dominicana)',
			'EC' => 'Ecuador',
			'EG' => 'Egypt (‫مصر‬‎)',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea (Guinea Ecuatorial)',
			'ER' => 'Eritrea',
			'EE' => 'Estonia (Eesti)',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands (Islas Malvinas)',
			'FO' => 'Faroe Islands (Føroyar)',
			'FJ' => 'Fiji',
			'FI' => 'Finland (Suomi)',
			'FR' => 'France',
			'GF' => 'French Guiana (Guyane française)',
			'PF' => 'French Polynesia (Polynésie française)',
			'TF' => 'French Southern Territories (Terres australes françaises)',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia (საქართველო)',
			'DE' => 'Germany (Deutschland)',
			'GH' => 'Ghana (Gaana)',
			'GI' => 'Gibraltar',
			'GR' => 'Greece (Ελλάδα)',
			'GL' => 'Greenland (Kalaallit Nunaat)',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea (Guinée)',
			'GW' => 'Guinea-Bissau (Guiné Bissau)',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard & McDonald Islands',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong (香港)',
			'HU' => 'Hungary (Magyarország)',
			'IS' => 'Iceland (Ísland)',
			'IN' => 'India (भारत)',
			'ID' => 'Indonesia',
			'IR' => 'Iran (‫ایران‬‎)',
			'IQ' => 'Iraq (‫العراق‬‎)',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel (‫תירבע‬‎)',
			'IT' => 'Italy (Italia)',
			'JM' => 'Jamaica',
			'JP' => 'Japan (日本)',
			'JE' => 'Jersey',
			'JO' => 'Jordan (‫الأردن‬‎)',
			'KZ' => 'Kazakhstan (Казахстан)',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'XK' => 'Kosovo (Kosovë)',
			'KW' => 'Kuwait (‫الكويت‬‎)',
			'KG' => 'Kyrgyzstan (Кыргызстан)',
			'LA' => 'Laos (ລາວ)',
			'LV' => 'Latvia (Latvija)',
			'LB' => 'Lebanon (‫لبنان‬‎)',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya (‫ليبيا‬‎)',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania (Lietuva)',
			'LU' => 'Luxembourg',
			'MO' => 'Macau (澳門)',
			'MK' => 'Macedonia (FYROM) (Македонија)',
			'MG' => 'Madagascar (Madagasikara)',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania (‫موريتانيا‬‎)',
			'MU' => 'Mauritius (Moris)',
			'YT' => 'Mayotte',
			'MX' => 'Mexico (México)',
			'FM' => 'Micronesia',
			'MD' => 'Moldova (Republica Moldova)',
			'MC' => 'Monaco',
			'MN' => 'Mongolia (Монгол)',
			'ME' => 'Montenegro (Crna Gora)',
			'MS' => 'Montserrat',
			'MA' => 'Morocco (‫المغرب‬‎)',
			'MZ' => 'Mozambique (Moçambique)',
			'MM' => 'Myanmar (Burma) (မြန်မာ)',
			'NA' => 'Namibia (Namibië)',
			'NR' => 'Nauru',
			'NP' => 'Nepal (नेपाल)',
			'NL' => 'Netherlands (Nederland)',
			'NC' => 'New Caledonia (Nouvelle-Calédonie)',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger (Nijar)',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'KP' => 'North Korea (조선 민주주의 인민 공화국)',
			'NO' => 'Norway (Norge)',
			'OM' => 'Oman (‫عُمان‬‎)',
			'PK' => 'Pakistan (‫پاکستان‬‎)',
			'PW' => 'Palau',
			'PS' => 'Palestine (‫فلسطين‬‎)',
			'PA' => 'Panama (Panamá)',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru (Perú)',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn Islands',
			'PL' => 'Poland (Polska)',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar (‫قطر‬‎)',
			'RE' => 'Réunion (La Réunion)',
			'RO' => 'Romania (România)',
			'RU' => 'Russia (Россия)',
			'RW' => 'Rwanda',
			'BL' => 'Saint Barthélemy (Saint-Barthélemy)',
			'SH' => 'Saint Helena',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin (Saint-Martin (partie française))',
			'PM' => 'Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'São Tomé and Príncipe (São Tomé e Príncipe)',
			'SA' => 'Saudi Arabia (‫المملكة العربية السعودية‬‎)',
			'SN' => 'Senegal (Sénégal)',
			'RS' => 'Serbia (Србија)',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia (Slovensko)',
			'SI' => 'Slovenia (Slovenija)',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia (Soomaaliya)',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia & South Sandwich Islands',
			'KR' => 'South Korea (대한민국)',
			'SS' => 'South Sudan (‫جنوب السودان‬‎)',
			'ES' => 'Spain (España)',
			'LK' => 'Sri Lanka (ශ්‍රී ලංකාව)',
			'VC' => 'St. Vincent & Grenadines',
			'SD' => 'Sudan (‫السودان‬‎)',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen (Svalbard og Jan Mayen)',
			'SZ' => 'Swaziland',
			'SE' => 'Sweden (Sverige)',
			'CH' => 'Switzerland (Schweiz)',
			'SY' => 'Syria (‫سوريا‬‎)',
			'TW' => 'Taiwan (台灣)',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania',
			'TH' => 'Thailand (ไทย)',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TA' => 'Tristan da Cunha',
			'TN' => 'Tunisia (‫تونس‬‎)',
			'TR' => 'Turkey (Türkiye)',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UM' => 'U.S. Outlying Islands',
			'VI' => 'U.S. Virgin Islands',
			'UG' => 'Uganda',
			'UA' => 'Ukraine (Україна)',
			'AE' => 'United Arab Emirates (‫الإمارات العربية المتحدة‬‎)',
			'GB' => 'United Kingdom',
			'US' => 'United States',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan (Oʻzbekiston)',
			'VU' => 'Vanuatu',
			'VA' => 'Vatican City (Città del Vaticano)',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam (Việt Nam)',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara (‫الصحراء الغربية‬‎)',
			'YE' => 'Yemen (‫اليمن‬‎)',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
		);

		return $countries;
	}
}
new GFGEO_Helper();
