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
	 * Get location fields array.
	 *
	 * @return [type] [description]
	 */
	public static function get_location_fields() {

		return array(
			''                  => __( 'N/A', 'gfgeo' ),
			'status'            => '',
			'place_name'        => __( 'Place Name', 'gfgeo' ),
			'street_number'     => __( 'Street Number', 'gfgeo' ),
			'street_name'       => __( 'Street Name', 'gfgeo' ),
			'street'            => __( 'Street ( number + name )', 'gfgeo' ),
			'street_bw'         => __( 'Street ( name + number )', 'gfgeo' ),
			'premise'           => __( 'Premise', 'gfgeo' ),
			'subpremise'        => __( 'Subpremise', 'gfgeo' ),
			'neighborhood'      => __( 'Neighborhood', 'gfgeo' ),
			'city'              => __( 'City', 'gfgeo' ),
			'county'            => __( 'County', 'gfgeo' ),
			'region_code'       => __( 'Region Code ( state code )', 'gfgeo' ),
			'region_name'       => __( 'Region Name ( state name )', 'gfgeo' ),
			'postcode'          => __( 'Postcode / Zipcode', 'gfgeo' ),
			'country_code'      => __( 'Country Code', 'gfgeo' ),
			'country_name'      => __( 'Country Name', 'gfgeo' ),
			'address'           => __( 'Address', 'gfgeo' ),
			'formatted_address' => __( 'Formatted Address', 'gfgeo' ),
			'latitude'          => __( 'Latitude', 'gfgeo' ),
			'longitude'         => __( 'Longitude', 'gfgeo' ),
			'distance_text'     => __( 'Distance ( text )', 'gfgeo' ),
			'distance_value'    => __( 'Distance ( value )', 'gfgeo' ),
			'duration_text'     => __( 'Duration ( text )', 'gfgeo' ),
			'duration_value'    => __( 'Duration ( value in seconds )', 'gfgeo' ),
		);
	}

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
	 * Generate map link.
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

		// if array it is coordiantes.
		if ( is_array( $data ) ) {

			$map_link['a']     = '<a style="font-size:13px;text-decoration: underline" class="gfgeo-map-link coordinates" href="' . esc_url( 'http://www.google.com/maps/place/' . $data['latitude'] . ',' . $data['longitude'] ) . '" target="_blank">';
			$map_link['title'] = __( 'View in Google Maps', 'gfgeo' );
			$map_link['/a']    = '</a>';

			// otherwise, address if this is string.
		} else {

			$map_link['a']     = '<a style="font-size:13px;text-decoration: underline" class="gfgeo-map-link address" href="' . esc_url( 'http://www.google.com/maps/place/' . str_replace( ' ', '+', $data ) ) . '" target="_blank">';
			$map_link['title'] = __( 'View in Google Maps', 'gfgeo' );
			$map_link['/a']    = '</a>';

		}

		$map_link = apply_filters( 'gfgeo_map_link_output', $map_link, $data );

		return implode( '', $map_link );
	}


	/**
	 * Display bp profile fields dropdown.
	 *
	 * @param  [type] $field [description].
	 */
	public static function bp_profile_fields_dropdown( $field ) {

		global $bp;

		if ( bp_is_active( 'xprofile' ) ) {

			if ( function_exists( 'bp_has_profile' ) ) {

				if ( bp_has_profile( 'hide_empty_fields=0' ) ) { ?>

					<?php $field = esc_attr( $field ); ?>

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field ?>"> 
						<?php esc_html_e( 'BuddyPress Profile Field', 'gfgeo' ); ?>
					</label> 

					<select 
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_xprofile_field" 
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
			<li>
				<label for="gmw-<?php echo esc_attr( $name ); ?>" class="section_label">
					<?php echo esc_html( $name ); ?>
				</label> 

				<div class="custom-field-content">

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta"> 
						<?php esc_html_e( 'Post Meta ( custom field )', 'gfgeo' ); ?>
					</label> 
					<input 
						style="display: block"
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta"
						type="text" 
						size="35" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_post_meta" 
						class="" 
						onkeyup="SetFieldProperty( jQuery( this ).attr( 'name' ), this.value );"
					/>

					<label for="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta ?>"> 
						<?php esc_html_e( 'User Meta', 'gfgeo' ); ?>
					</label> 
					<input 
						style="display: block"
						name="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta"
						type="text" 
						size="35" 
						id="gfgeo_<?php echo $field; // WPCS: XSS ok. ?>_user_meta" 
						class="" 
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

		// make sure field is array.
		$field          = (array) $field;
		$field_id       = esc_attr( $form_id . '_' . $field['id'] );
		$geocoder_id    = ! empty( $field['gfgeo_geocoder_id'] ) ? esc_attr( $field['formId'] . '_' . $field['gfgeo_geocoder_id'] ) : '';
		$found_message  = ! empty( $field['gfgeo_location_found_message'] ) ? esc_attr( $field['gfgeo_location_found_message'] ) : '';
		$failed_message = ! empty( $field['gfgeo_hide_location_failed_message'] ) ? 1 : 0;
		$button_label   = ! empty( $field['gfgeo_locator_button_label'] ) ? $field['gfgeo_locator_button_label'] : '';

		$button_label = apply_filters( 'gfgeo_locator_button_label', $button_label, $form_id, $field, $type );

		$ip_locator = ! empty( $field['gfgeo_ip_locator_status'] ) ? $field['gfgeo_ip_locator_status'] : '';

		// generate the button element.
		$output  = '';
		$output .= '<div id="gfgeo-locator-button-wrapper-' . $field_id . '" class="gfgeo-locator-button-wrapper ' . esc_attr( $type ) . '">';

		if ( 'infield' === $type ) {

			$output .= '<img id="gfgeo-infield-locator-button-' . $field_id . '" style="box-shadow: none;border-radius:0" src="' . GFGEO_URL . '/assets/images/locator.png" class="gfgeo-locator-button infield-locator skip-lazy" data-geocoder_id="' . $geocoder_id . '" data-locator_id="' . $field_id . '" data-ip_locator="' . $ip_locator . '" data-found_message="' . $found_message . '" data-hide_failed_message="' . $failed_message . '" />';

		} else {
			$output .= '<input type="button" id="gfgeo-locator-button-' . $field_id . '" data-geocoder_id="' . $geocoder_id . '" data-locator_id="' . $field_id . '" data-found_message="' . $found_message . '" class="gfgeo-locator-button" data-ip_locator="' . $ip_locator . '" value="' . $button_label . '" data-hide_failed_message="' . $failed_message . '" />';
		}

		// loader.
		$output .= '<img class="gfgeo-locator-loader loader-' . $field_id . ' skip-lazy" style="display:none;box-shadow: none;border-radius:0" src="' . GFGEO_URL . '/assets/images/loader.gif" />';

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
