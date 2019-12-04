<?php
/**
 * Gravity Forms Geolocation - form submission class.
 *
 * @package gravityforms-geolocation.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * GFGEO_Form_Submission class
 *
 * The class responsible for updating location for posts, entries and users after form submission.
 *
 * @author Fitoussi Eyal
 * @since 2.0
 */
class GFGEO_Form_Submission {

	/**
	 * __constructor
	 */
	public function __construct() {

		// If GEO my WP exists.
		add_action( 'gform_after_submission', array( $this, 'form_submission' ), 10, 5 );

		// When using Post Creation add-on.
		add_action( 'gform_advancedpostcreation_post_after_creation', array( $this, 'post_creation' ), 10, 4 );

		// Update user location after activation ( manually or automatically ).
		add_action( 'gform_user_registered', array( $this, 'gmw_pre_update_user_location' ), 10, 3 );

		// Update user location when user updated.
		add_action( 'gform_user_updated', array( $this, 'gmw_pre_update_user_location' ), 10, 3 );
	}

	/**
	 * When using the Post Creation add-on.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  obejct $feed    feed object.
	 * @param  ojbect $entry   entry.
	 * @param  object $form    form.
	 */
	public function post_creation( $post_id, $feed, $entry, $form ) {

		// Remove this filter. We will trigger the function below.
		remove_action( 'gform_after_submission', array( $this, 'form_submission' ), 10, 5 );

		$temp_entry            = $entry;
		$temp_entry['post_id'] = $post_id;

		$this->form_submission( $temp_entry, $form );
	}

	/**
	 * Execute some functions on form submission.
	 *
	 * Save meta fields and saved information to GEO my WP tables
	 *
	 * @param  array $entry entry created.
	 *
	 * @param  array $form  proccessed form.
	 *
	 * @return [type]        [description]
	 */
	public function form_submission( $entry, $form ) {

		// look for post ID to see if post was created or updated.
		$post_id = ! empty( $entry['post_id'] ) ? $entry['post_id'] : 0;

		// look for user ID if logged in.
		$user_id = get_current_user_id();

		// abort if we don't update either post or user.
		if ( empty( $post_id ) && empty( $user_id ) ) {
			return;
		}

		// get address fields slug.
		$address_fields = array_keys( GFGEO_Helper::get_location_fields() );

		unset( $address_fields[0], $address_fields[1] );

		// loop through form fields.
		foreach ( $form['fields'] as $field ) {

			// Save/delete address custom fields.
			if ( 'gfgeo_address' === $field['type'] && ! empty( $field['postCustomFieldName'] ) ) {

				$field_value = ! empty( $_POST[ 'input_' . $field['id'] ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] ] ) ) : ''; // WPCS: CSRF ok.

				if ( ! empty( $field_value ) ) {

					update_post_meta( $post_id, $field['postCustomFieldName'], $field_value );

				} else {

					delete_post_meta( $post_id, $field['postCustomFieldName'] );
				}
			}

			// Save/delete coordinates custom fields.
			if ( 'gfgeo_coordinates' === $field['type'] && ! empty( $field['postCustomFieldName'] ) ) {

				$field_value = ! empty( $_POST[ 'input_' . $field['id'] ] ) ? maybe_unserialize( $_POST[ 'input_' . $field['id'] ] ) : array(); // WPCS: CSRF ok, sanitization ok.

				$field_value = array_map( 'sanitize_text_field', $field_value );

				if ( ! empty( $field_value ) && array_filter( $field_value ) ) {

					if ( empty( $field['gfgeo_custom_field_method'] ) ) {

						$cf_latlng = $field_value['latitude'] . ',' . $field_value['longitude'];

						update_post_meta( $post_id, $field['postCustomFieldName'], sanitize_text_field( $cf_latlng ) );

					} else {

						update_post_meta( $post_id, $field['postCustomFieldName'], maybe_serialize( $field_value ) );
					}
				} else {

					delete_post_meta( $post_id, $field['postCustomFieldName'] );
				}
			}

			if ( 'address' === $field['type'] && ! empty( $field['postCustomFieldName'] ) ) {

				$address_array = array(
					$field['id'] . '.1' => ! empty( $_POST[ 'input_' . $field['id'] . '_1' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_1' ] ) ) : '', // WPCS: CSRF ok.
					$field['id'] . '.2' => ! empty( $_POST[ 'input_' . $field['id'] . '_2' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_2' ] ) ) : '', // WPCS: CSRF ok.
					$field['id'] . '.3' => ! empty( $_POST[ 'input_' . $field['id'] . '_3' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_3' ] ) ) : '', // WPCS: CSRF ok.
					$field['id'] . '.4' => ! empty( $_POST[ 'input_' . $field['id'] . '_4' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_4' ] ) ) : '', // WPCS: CSRF ok.
					$field['id'] . '.5' => ! empty( $_POST[ 'input_' . $field['id'] . '_5' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_5' ] ) ) : '', // WPCS: CSRF ok.
					$field['id'] . '.6' => ! empty( $_POST[ 'input_' . $field['id'] . '_6' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field['id'] . '_6' ] ) ) : '', // WPCS: CSRF ok.
				);

				if ( array_filter( $address_array ) ) {

					update_post_meta( $post_id, $field['postCustomFieldName'], $address_array );

				} else {

					delete_post_meta( $post_id, $field['postCustomFieldName'] );
				}
			}

			// look for geocoder field.
			if ( 'gfgeo_geocoder' === $field['type'] ) {

				$field_value = ! empty( $_POST[ 'input_' . $field['id'] ] ) ? maybe_unserialize( $_POST[ 'input_' . $field['id'] ] ) : array(); // WPCS: CSRF ok, sanitization ok, XSS ok.

				$field_value = array_map( 'sanitize_text_field', $field_value );

				// verify geocoded data.
				if ( empty( $field_value ) || empty( $field_value['status'] ) ) {

					$geocoded_ok = false;

				} else {

					$geocoded_ok = true;

					// get geocoded data.
					$geocoded_data = $field_value;
				}

				// save complete geocoded data as serialized array in custom field if needed.
				if ( 0 !== absint( $post_id ) && ! empty( $field->postCustomFieldName ) ) {

					if ( $geocoded_ok ) {
						update_post_meta( $post_id, sanitize_key( $field->postCustomFieldName ), maybe_serialize( $geocoded_data ) );
					} else {
						delete_post_meta( $post_id, sanitize_key( $field->postCustomFieldName ) );
					}
				}

				// save compelete geocoded data as a serialized array in user meta if needed.
				if ( 0 !== absint( $user_id ) && ! empty( $field->gfgeo_user_meta_field ) ) {

					if ( $geocoded_ok ) {
						update_user_meta( $user_id, sanitize_key( $field->gfgeo_user_meta_field ), maybe_serialize( $geocoded_data ) );
					} else {
						delete_user_meta( $user_id, sanitize_key( $field->gfgeo_user_meta_field ) );
					}
				}

				// check if BuddyPress enabled.
				$buddypress_enabled = ( class_exists( 'BuddyPress' ) ) ? true : false;

				// loop through and update specific meta fields.
				foreach ( $address_fields as $address_field ) {

					// senitize value.
					$value = ( $geocoded_ok ) ? sanitize_text_field( $geocoded_data[ $address_field ] ) : false;

					// update post meta.
					if ( 0 !== absint( $post_id ) ) {

						$cfield = 'gfgeo_' . $address_field . '_post_meta';

						if ( ! empty( $field->$cfield ) ) {

							if ( $geocoded_ok ) {
								update_post_meta( $post_id, sanitize_key( $field->$cfield ), $value );
							} else {
								delete_post_meta( $post_id, sanitize_key( $field->$cfield ) );
							}
						}
					}

					// update user meta.
					if ( 0 !== absint( $user_id ) ) {

						$cfield = 'gfgeo_' . $address_field . '_user_meta';

						if ( ! empty( $field->$cfield ) ) {

							if ( $geocoded_ok ) {
								update_user_meta( $user_id, sanitize_key( $field->$cfield ), $value );
							} else {
								delete_user_meta( $user_id, sanitize_key( $field->$cfield ) );
							}
						}

						// update bp profile field.
						if ( $buddypress_enabled ) {

							$cfield = 'gfgeo_' . $address_field . '_xprofile_field';

							if ( ! empty( $field->$cfield ) ) {

								if ( $geocoded_ok ) {
									xprofile_set_field_data( (int) $field->$cfield, (int) $user_id, $value );
								} else {
									xprofile_delete_field_data( (int) $field->$cfield, (int) $user_id );
								}
							}
						}
					}
				}

				// update GEO my WP posts table if needed.
				if ( class_exists( 'GEO_my_WP' ) ) {

					// update post data in GEO my WP.
					if ( ! empty( $post_id ) && ! empty( $field->gfgeo_gmw_post_integration ) ) {

						if ( $geocoded_ok ) {
							$this->gmw_update_post_location( $post_id, $geocoded_data, $field, $entry, $form );
						} else {
							$this->gmw_delete_post_location( $post_id, $field, $entry, $form );
						}
					}

					// update user location in GEO my WP if not already updated with the gform_user_updated action.
					if ( ! empty( $user_id ) && ! empty( $field->gfgeo_gmw_user_integration ) && ! did_action( 'gform_user_updated' ) ) {

						if ( $geocoded_ok ) {
							$this->gmw_update_user_location( $user_id, $geocoded_data, $field, $entry, $form );
						} else {
							$this->gmw_delete_user_location( $user_id, $field, $entry, $form );
						}
					}
				}
			}
		}
	}


	/**
	 * Update GEO my WP posts table.
	 *
	 * @param  integer $post_id       the post ID.
	 *
	 * @param  array   $geocoded_data geocoded address.
	 *
	 * @param  object  $field         the field object.
	 * @param  array   $entry         the form entry.
	 * @param  array   $form          the form being processed.
	 *
	 * @return [type]                [description]
	 */
	public function gmw_update_post_location( $post_id, $geocoded_data, $field, $entry, $form ) {

		$contact_info = array();

		$contact_info['phone'] = ( ! empty( $field->gfgeo_gmw_post_integration_phone ) && ! empty( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_phone ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_phone ] ) ) : ''; // WPCS: CSRF ok.

		$contact_info['fax'] = ( ! empty( $field->gfgeo_gmw_post_integration_fax ) && ! empty( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_fax ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_fax ] ) ) : ''; // WPCS: CSRF ok.

		$contact_info['email'] = ( ! empty( $field->gfgeo_gmw_post_integration_email ) && ! empty( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_email ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_email ] ) ) : ''; // WPCS: CSRF ok.

		$contact_info['website'] = ( ! empty( $field->gfgeo_gmw_post_integration_website ) && ! empty( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_website ] ) ) ? sanitize_text_field( wp_unslash( $_POST[ 'input_' . $field->gfgeo_gmw_post_integration_website ] ) ) : ''; // WPCS: CSRF ok.

		// for GEO my WP previus v3.0.
		if ( version_compare( GMW_VERSION, '3.0', '<' ) ) {

			// Save information to database.
			global $wpdb;

			$wpdb->replace(
				$wpdb->prefix . 'places_locator',
				array(
					'post_id'           => $post_id,
					'feature'           => 0,
					'post_type'         => get_post_type( $post_id ),
					'post_title'        => get_the_title( $post_id ),
					'post_status'       => 'publish',
					'street_number'     => $geocoded_data['street_number'],
					'street_name'       => $geocoded_data['street_name'],
					'street'            => $geocoded_data['street'],
					'apt'               => $geocoded_data['premise'],
					'city'              => $geocoded_data['city'],
					'state'             => $geocoded_data['region_code'],
					'state_long'        => $geocoded_data['region_name'],
					'zipcode'           => $geocoded_data['postcode'],
					'country'           => $geocoded_data['country_code'],
					'country_long'      => $geocoded_data['country_name'],
					'address'           => $geocoded_data['address'],
					'formatted_address' => $geocoded_data['formatted_address'],
					'phone'             => $contact_info['phone'],
					'fax'               => $contact_info['fax'],
					'email'             => $contact_info['email'],
					'website'           => $contact_info['website'],
					'lat'               => $geocoded_data['latitude'],
					'long'              => $geocoded_data['longitude'],
					'map_icon'          => '_default.png',
				)
			); // WPCS: db call ok, cache ok.

		} elseif ( class_exists( 'GMW_Location' ) ) {

			$location_args = array(
				'object_type'       => 'post',
				'object_id'         => (int) $post_id,
				'user_id'           => $form['postAuthor'],
				'parent'            => 0,
				'status'            => 1,
				'featured'          => 0,
				'title'             => get_the_title( $post_id ),
				'latitude'          => $geocoded_data['latitude'],
				'longitude'         => $geocoded_data['longitude'],
				'street_number'     => $geocoded_data['street_number'],
				'street_name'       => $geocoded_data['street_name'],
				'street'            => $geocoded_data['street'],
				'premise'           => $geocoded_data['premise'],
				'neighborhood'      => $geocoded_data['neighborhood'],
				'city'              => $geocoded_data['city'],
				'county'            => $geocoded_data['county'],
				'region_name'       => $geocoded_data['region_name'],
				'region_code'       => $geocoded_data['region_code'],
				'postcode'          => $geocoded_data['postcode'],
				'country_name'      => $geocoded_data['country_name'],
				'country_code'      => $geocoded_data['country_code'],
				'address'           => $geocoded_data['address'],
				'formatted_address' => $geocoded_data['formatted_address'],
				'place_id'          => '',
				'map_icon'          => '_default.png',
			);

			// save location.
			$location_id = GMW_Location::update( $location_args );

			foreach ( $contact_info as $key => $value ) {

				if ( ! empty( $value ) ) {
					gmw_update_location_meta( $location_id, $key, $value );
				}
			}
		} else {
			return;
		}

		// hook and do something with the information.
		do_action( 'gfgeo_after_gmw_post_location_saved', $post_id, $geocoded_data, $contact_info, $entry, $form );
	}

	/**
	 * Delete post location
	 *
	 * @param  integer $post_id post ID.
	 * @param  object  $field   the field object.
	 * @param  array   $entry   the form entry.
	 * @param  array   $form    the form.
	 */
	public function gmw_delete_post_location( $post_id, $field, $entry, $form ) {

		if ( version_compare( GMW_VERSION, '3.0', '<' ) && class_exists( 'GMW_Location' ) ) {

			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					"
	    			DELETE FROM {$wpdb->prefix}places_locator 
	    			WHERE `post_id` = %d",
					array( $post_id )
				)
			); // WPCS: db call ok, cache ok.

		} elseif ( function_exists( 'gmw_delete_location_by_object' ) ) {

			gmw_delete_location_by_object( 'post', $post_id, true );
		}
	}

	/**
	 * Delete post location
	 *
	 * @param  integer $user_id the user ID.
	 * @param  object  $field   the field object.
	 * @param  array   $entry   the form entry.
	 * @param  array   $form    the form.
	 */
	public function gmw_delete_user_location( $user_id, $field, $entry, $form ) {

		if ( version_compare( GMW_VERSION, '3.0', '<' ) && class_exists( 'GMW_Location' ) ) {

			global $wpdb;

			$wpdb->query(
				$wpdb->prepare(
					'
	    			DELETE FROM wppl_friends_locator 
	    			WHERE `member_id` = %d',
					array( $user_id )
				)
			); // WPCS: db call ok, cache ok.

		} elseif ( function_exists( 'gmw_delete_location_by_object' ) ) {

			gmw_delete_location_by_object( 'user', $user_id, true );
		}
	}

	/**
	 * Get some data before updating user location in GEO my WP database.
	 *
	 * This data is being collected when using Gravity Forms update and register user actions.
	 *
	 * @param  integer $user_id user ID.
	 * @param  [type]  $config  [description].
	 * @param  array   $entry   the form entry.
	 *
	 * @return [type]          [description]
	 */
	public function gmw_pre_update_user_location( $user_id, $config, $entry ) {

		// check form ID.
		if ( empty( $config['form_id'] ) ) {
			return;
		}

		// get the form object.
		$form = GFAPI::get_form( $config['form_id'] );

		if ( empty( $form ) ) {
			return;
		}

		// loop through form fields and look for geocoder fields.
		foreach ( $form['fields'] as $field ) {

			// check if geocoder field set for gmw user integration.
			if ( 'gfgeo_geocoder' === $field['type'] && ! empty( $field->gfgeo_gmw_user_integration ) ) {

				// geocoder ID.
				$geocoder_id = $field['id'];

				// verify geocoded data.
				if ( ! empty( $entry[ $geocoder_id ] ) ) {

					$geocoded_data = maybe_unserialize( $entry[ $geocoder_id ] );

					// verify geocoded data.
					if ( ! empty( $geocoded_data['status'] ) && ! empty( $geocoded_data['latitude'] ) && ! empty( $geocoded_data['longitude'] ) ) {
						$this->gmw_update_user_location( $user_id, $geocoded_data, $field, $entry, $form );
					} else {
						$this->gmw_delete_user_location( $user_id, $field, $entry, $form );
					}
				}
			}
		}
	}

	/**
	 * Update user location in GEO my WP database.
	 *
	 * @param  integer $user_id       the user ID.
	 * @param  array   $geocoded_data geocoded data.
	 * @param  object  $field         the field object.
	 * @param  array   $entry         the form entry.
	 * @param  array   $form          the prcessed form.
	 *
	 * @return [type]                [description]
	 */
	public function gmw_update_user_location( $user_id, $geocoded_data, $field, $entry, $form ) {

		$map_icon = ( isset( $_POST['map_icon'] ) ) ? sanitize_text_field( wp_unslash( $_POST['map_icon'] ) ) : '_default.png'; // WPCS: CSRF ok.

		if ( version_compare( GMW_VERSION, '3.0', '<' ) ) {

			// default fields.
			$defaults = array(
				'member_id'         => $user_id,
				'street'            => '',
				'apt'               => '',
				'city'              => '',
				'state'             => '',
				'state_long'        => '',
				'zipcode'           => '',
				'country'           => '',
				'country_long'      => '',
				'address'           => '',
				'formatted_address' => '',
				'lat'               => '',
				'long'              => '',
				'map_icon'          => '_default.png',
			);

			$geocoded_data = wp_parse_args( $geocoded_data, $defaults );

			// save location into GEO my WP user table in database.
			global $wpdb;

			$wpdb->replace(
				'wppl_friends_locator',
				array(
					'member_id'         => $user_id,
					'street'            => $geocoded_data['street'],
					'apt'               => $geocoded_data['premise'],
					'city'              => $geocoded_data['city'],
					'state'             => $geocoded_data['region_code'],
					'state_long'        => $geocoded_data['region_name'],
					'zipcode'           => $geocoded_data['postcode'],
					'country'           => $geocoded_data['country_code'],
					'country_long'      => $geocoded_data['country_name'],
					'address'           => $geocoded_data['address'],
					'formatted_address' => $geocoded_data['formatted_address'],
					'lat'               => $geocoded_data['latitude'],
					'long'              => $geocoded_data['longitude'],
					'map_icon'          => $map_icon,
				)
			); // WPCS: db call ok, cache ok.

		} elseif ( class_exists( 'GMW_Location' ) ) {

			$user_data = get_userdata( $user_id );

			$location_args = array(
				'object_type'       => 'user',
				'object_id'         => (int) $user_id,
				'user_id'           => (int) $user_id,
				'parent'            => 0,
				'status'            => 1,
				'featured'          => 0,
				'title'             => $user_data->display_name,
				'latitude'          => $geocoded_data['latitude'],
				'longitude'         => $geocoded_data['longitude'],
				'street_number'     => $geocoded_data['street_number'],
				'street_name'       => $geocoded_data['street_name'],
				'street'            => $geocoded_data['street'],
				'premise'           => $geocoded_data['premise'],
				'neighborhood'      => $geocoded_data['neighborhood'],
				'city'              => $geocoded_data['city'],
				'county'            => $geocoded_data['county'],
				'region_name'       => $geocoded_data['region_name'],
				'region_code'       => $geocoded_data['region_code'],
				'postcode'          => $geocoded_data['postcode'],
				'country_name'      => $geocoded_data['country_name'],
				'country_code'      => $geocoded_data['country_code'],
				'address'           => $geocoded_data['address'],
				'formatted_address' => $geocoded_data['formatted_address'],
				'place_id'          => '',
				'map_icon'          => $map_icon,
			);

			// save location.
			$location_id = GMW_Location::update( $location_args );

		} else {
			return;
		}

		// hook and do something with the information.
		do_action( 'gfgeo_after_gmw_user_location_saved', $user_id, $geocoded_data, $entry, $form );
	}
}
$gfgeo_form_submission = new GFGEO_Form_Submission();
