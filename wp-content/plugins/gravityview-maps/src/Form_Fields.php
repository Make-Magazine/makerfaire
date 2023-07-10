<?php

namespace GravityKit\GravityMaps;

use GFAPI;
use GFForms;
use GF_Fields;
use RGFormsModel;

/**
 * GravityView Maps Extension - Settings class
 * Adds a general setting to the GravityView settings screen
 *
 * @link      http://gravityview.co
 * @since     1.0.0
 * @author    GravityView <hello@gravityview.co>
 * @package   GravityView_Maps
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @license   GPL2+
 */
class Form_Fields extends Component {
	function load() {
		// loads custom form fields classes
		$this->load_gf_field_classes();

		// Add button to right menu
		add_filter( 'gform_add_field_buttons', [ $this, 'add_field' ], 10, 1 );

		// Set defaults
		add_action( 'gform_editor_js_set_default_values', [ $this, 'set_field_defaults' ] );

		add_filter( 'gform_preview_styles', [ $this, 'preview_enqueue_scripts' ] );

		add_action( 'wp', [ $this, 'register_scripts_and_styles' ] );
		add_action( 'admin_init', [ $this, 'register_scripts_and_styles' ] );
		add_action( 'gform_enqueue_scripts', [ $this, 'public_enqueue_scripts' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ], 10, 1 );

		add_action( 'gform_field_standard_settings', [ $this, 'include_geolocation_fields' ], 10, 2 );
		add_action( 'gform_after_submission', [ $this, 'save_gform_geolocation_meta' ], 10, 2 );

		add_filter( 'gform_noconflict_styles', [ $this, 'register_no_conflicts' ] );
		add_filter( 'gform_noconflict_scripts', [ $this, 'register_no_conflicts' ] );
	}

	/**
	 * Get the meta key for the stored map data.
	 *
	 * @since 2.0
	 *
	 * @param string $key Name of data stored
	 * @param        $field_id
	 *
	 * @return string Meta key used to store data using Gravity Forms Entry Meta
	 */
	public static function get_meta_key( $key, $field_id ) {
		return sanitize_key( 'gvmaps_' . $key . '_' . $field_id );
	}

	/**
	 * Allow the return
	 *
	 * @since 2.0
	 *
	 * @param string $type Which type of field we are talking about.
	 *
	 * @return array|null
	 */
	public static function get_geolocation_fields_meta_key_callback( $type ) {
		$map = [
			'internal' => [
				'lat' => static function( $id ) {
					return Form_Fields::get_meta_key( 'lat', $id );
				},
				'long' => static function( $id ) {
					return Form_Fields::get_meta_key( 'long', $id );
				},
			],
			'gravityforms' => [
				'lat' => static function( $id ) {
					return sprintf( '%s.geolocation_latitude', $id );
				},
				'long' => static function( $id ) {
					return sprintf( '%s.geolocation_longitude', $id );
				},
			],
			'gravitywiz' => [
				'lat' => static function( $id ) {
					return sprintf( 'gpaa_lat_%s', $id );
				},
				'long' => static function( $id ) {
					return sprintf( 'gpaa_lng_%s', $id );
				},
			],
			'manual_coordinates' => [
				'lat' => static function( $fields ) {
					if ( ! is_object( $fields ) ) {
						return null;
					}

					return $fields->lat ?: null;
				},
				'long' => static function( $fields ) {
					if ( ! is_object( $fields ) ) {
						return null;
					}

					return $fields->long ?: null;
				},
			],
		];

		if ( ! isset( $map[ $type ] ) ) {
			return null;
		}

		return $map[ $type ];
	}

	/**
	 * Memoization of the Geolocation for GF form fields to avoid running this query multiple times for each form.
	 *
	 * @since 2.0
	 *
	 * @var array
	 */
	protected static $form_gf_geolocation_cache = [];

	/**
	 * Determine if a given form or any (null as $form_id) has gf geolocation fields.
	 *
	 * This method will be memoized to avoid running expensive SQL more than once per request.
	 *
	 * @since 2.0
	 *
	 * @param string|int|null $form_id
	 *
	 * @return array
	 */
	public static function get_gf_geolocation_form_fields( $form_id = null ) {
		global $wpdb;

		// Completely ignore this when GF geolocation doesn't exist.
		if ( ! class_exists( 'GF_Geolocation' ) ) {
			return [];
		}

		// If we have something memoized use it.
		if ( ! empty( static::$form_gf_geolocation_cache[ $form_id ] ) ) {
			return static::$form_gf_geolocation_cache[ $form_id ];
		}

		$form_where    = '';
		$gf_entry_meta = \GFFormsModel::get_entry_meta_table_name();

		if ( is_numeric( $form_id ) ) {
			$form_id    = (int) $form_id;
			$form_where = "AND {$gf_entry_meta}.`form_id` = {$form_id}";
		}

		$sql   = "
			SELECT {$gf_entry_meta}.`form_id`
			FROM {$gf_entry_meta}
			WHERE (
			    1=1
			    AND (
					{$gf_entry_meta}.`meta_key` LIKE '%.geolocation_latitude'
					OR {$gf_entry_meta}.`meta_key` LIKE '%.geolocation_longitude'
				)
			    {$form_where}
			)
			GROUP BY {$gf_entry_meta}.`form_id`;
		";
		$forms = $wpdb->get_col( $sql );

		$data = [];
		foreach ( $forms as $form ) {
			$form   = GFAPI::get_form( $form );
			$fields = array_filter( $form['fields'], static function ( $field ) {
				if ( ! $field instanceof \GF_Field_Address ) {
					return false;
				}

				// Check if geolocation setting is empty.
				if ( empty( $field->ggeolocationEnableGeolocationSuggestions ) ) {
					return false;
				}

				return true;
			} );

			$fields = array_values( array_map( static function ( $field ) {
				return $field->id;
			}, $fields ) );

			static::$form_gf_geolocation_cache[ $form['id'] ] = $data[ $form['id'] ] = $fields;
		}

		return $data;
	}

	public function load_gf_field_classes() {
		if ( ! class_exists( 'GF_Field' ) || ! class_exists( 'GF_Fields' ) ) {
			return;
		}

		try {
			GF_Fields::register( new GF_Field_Icon_Picker() );
		} catch ( \Exception $e ) {
			gravityview()->log->error( 'Could not register GF_Field_Icon_Picker: {exception}', [
				'exception' => $e->getMessage(),
			] );
		}
	}

	/**
	 * Include the geolocation fields on the GV form sidebar.
	 *
	 * @since 2.0
	 *
	 * @param $position
	 * @param $form_id
	 *
	 */
	public function include_geolocation_fields( $position, $form_id ) {
		// Bail if not on the correct position.
		if ( 1100 !== $position ) {
			return;
		}
		?>
		<li class="address_setting field_setting">
			<input type="checkbox" id="field_enable_geolocation_autocomplete" class="field_enable_geolocation_autocomplete" name="field_enable_geolocation_autocomplete"/>
			<label for="field_enable_geolocation_autocomplete" class="inline">
				<?php esc_html_e( 'Enable geolocation autocomplete', 'gk-gravitymaps' ); ?>
				<?php gform_tooltip( 'form_field_enable_geolocation_autocomplete' ); ?>
			</label>
		</li>
		<?php
	}

	/**
	 * Add GravityView Maps Map Icon form field
	 */
	function add_field( $field_groups ) {

		foreach ( $field_groups as &$group ) {
			if ( 'gravityview_fields' === $group['name'] ) {
				$group['fields'][] = [
					'class'            => 'button',
					'data-type'        => 'gvmaps_icon_picker',
					'data-icon'        => 'dashicons-location-alt',
					'data-description' => esc_html__( 'Select the map marker icon that will be shown for each entry.', 'gk-gravitymaps' ),
					'value'            => esc_html__( 'Map Icon Picker', 'gk-gravitymaps' ),
					'onclick'          => "StartAddField('gvmaps_icon_picker');",
				];
				break;
			}
		}

		return $field_groups;
	}

	function set_field_defaults() {
		?>
		case 'gvmaps_icon_picker':
		field.label = "<?php echo esc_js( 'Map Icon', 'gravityview-maps' ); ?>";
		field.inputs = null;
		field.adminOnly = false;
		break;
		<?php
	}

	/**
	 * Register needed scripts and styles
	 */
	function register_scripts_and_styles() {
		$maps_object = Render_Map::get_instance();

		$maps_object->determine_google_script_handle();
		wp_register_script( $maps_object->get_google_script_handle(), $maps_object->get_google_script_url(), [], null );

		wp_register_script( 'gk-maps-iso-3166-1-alpha-2', plugins_url( '/assets/lib/iso-3166-1-alpha-2.js', $this->loader->path ), [ 'underscore', 'gk-maps-base' ], null );

		wp_register_style( 'gvmaps-icon-picker-style', plugins_url( 'assets/css/gv-maps-fields.css', $this->loader->path ), null, $this->loader->plugin_version );

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_script( 'gvmaps-icon-picker', plugins_url( 'assets/js/gv-maps-fields' . $script_debug . '.js', $this->loader->path ), [ 'jquery' ], $this->loader->plugin_version );

		wp_register_script( 'gk-maps-admin-form-fields', plugins_url( 'assets/js/gk-maps-admin-form-fields' . $script_debug . '.js', $this->loader->path ), [ 'jquery', 'gk-maps-base' ], $this->loader->plugin_version );

		wp_register_script(
			'gk-maps-form-address-auto-complete',
			plugins_url( 'assets/js/gk-maps-form-address-auto-complete' . $script_debug . '.js', $this->loader->path ),
			[
				'jquery',
				'gk-maps-iso-3166-1-alpha-2',
				$maps_object->get_google_script_handle(),
				'gk-maps-base',
			],
			$this->loader->plugin_version
		);
	}

	/**
	 * Enqueue scripts and styles on GF Form (public)
	 *
	 * @param $form
	 * @param $is_ajax
	 */
	function public_enqueue_scripts( $form, $is_ajax ) {
		if (
			! empty( $form['id'] )
			&& $this->has_field_with_geolocation( $form['id'] )
		) {
			wp_enqueue_script( 'gk-maps-form-address-auto-complete' );

			$this->localize_fields_auto_complete( $form['id'] );
		}

		$icon_picker_fields = GFAPI::get_fields_by_type( $form, 'gvmaps_icon_picker' );

		if ( empty( $icon_picker_fields ) ) {
			return;
		}

		wp_enqueue_style( 'gvmaps-icon-picker-style' );
		wp_enqueue_script( 'gvmaps-icon-picker' );
	}

	/**
	 * On Gravity Forms After Submission Action we save all the geolocation data to the database.
	 *
	 * @since 2.0
	 *
	 * @param array $entry
	 * @param array $form
	 *
	 */
	public function save_gform_geolocation_meta( $entry, $form ) {
		if ( empty( $form['id'] ) ) {
			return;
		}

		$fields = $this->get_geolocation_fields( $form['id'] );

		// If we got an error just bail.
		if ( is_wp_error( $fields ) ) {
			return;
		}

		$geolocation_form_data = rgar( $_POST, 'gk-gravitymap-geolocation' );
		if ( empty( $geolocation_form_data ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			$geolocation_field_data = rgar( $geolocation_form_data, $field->id );
			if ( empty( $geolocation_field_data ) ) {
				continue;
			}

			if ( isset( $geolocation_field_data['latitude'] ) ) {
				gform_update_meta( $entry['id'], static::get_meta_key( 'lat', $field->id ), $geolocation_field_data['latitude'] );
			}

			if ( isset( $geolocation_field_data['longitude'] ) ) {
				gform_update_meta( $entry['id'], static::get_meta_key( 'long', $field->id ), $geolocation_field_data['longitude'] );
			}

			if ( isset( $geolocation_field_data['data'] ) ) {
				$data = json_decode( stripslashes( $geolocation_field_data['data'] ), true );
				gform_update_meta( $entry['id'], static::get_meta_key( 'data', $field->id ), wp_json_encode( $data ) );
			}
		}
	}

	/**
	 * Given a GForm ID get which fields have geolocation enabled.
	 *
	 * @since 2.0
	 *
	 * @param int|string $form_id
	 *
	 * @return \GF_Field[]|\WP_Error
	 */
	public function get_geolocation_fields( $form_id ) {
		if ( ! is_numeric( $form_id ) ) {
			return new \WP_Error( 'gk-maps-invalid-form-id', null, [ 'form_id' => $form_id ] );
		}
		$form = RGFormsModel::get_form_meta( $form_id );

		if ( empty( $form ) ) {
			return new \WP_Error( 'gk-maps-invalid-form', null, [ 'form_id' => $form_id, 'form' => $form ] );
		}

		if ( empty( $form['fields'] ) ) {
			return new \WP_Error( 'gk-maps-geolocation-fields-dont-exist', null, [ 'form_id' => $form_id, 'form' => $form ] );
		}

		$fields = array_filter( $form['fields'], [ static::class, 'is_valid_geolocation_field' ] );
		$fields = array_values( array_filter( $fields, [ static::class, 'is_field_geolocation_enabled' ] ) );

		/**
		 * Allows filtering of which fields are considered geolocation fields.
		 *
		 * @param array $fields Which fields are geolocation.
		 * @param array $form   Which Gravity Forms meta we are using.
		 */
		$fields = (array) apply_filters( 'gk/gravitymaps/geolocation_fields', $fields, $form );

		if ( empty( $fields ) ) {
			return new \WP_Error( 'gk-maps-geolocation-fields-dont-exist', null, [ 'form_id' => $form_id, 'form' => $form ] );
		}

		return $fields;
	}

	/**
	 * Determine if a given form id has any geolocation enabled fields.
	 *
	 * @since 2.0
	 *
	 * @param int|string $form_id
	 *
	 * @return bool
	 */
	public function has_field_with_geolocation( $form_id, $skip_cache = false ) {
		static $has_field_geolocation_cache = [];

		// Cache the geolocation fields value on memory.
		if ( ! $skip_cache && isset( $has_field_geolocation_cache[ $form_id ] ) ) {
			return $has_field_geolocation_cache[ $form_id ];
		}

		$fields                                  = $this->get_geolocation_fields( $form_id );
		$has_field_geolocation_cache[ $form_id ] = false;
		if ( is_wp_error( $fields ) ) {
			return $has_field_geolocation_cache[ $form_id ];
		}

		if ( empty( $fields ) ) {
			return $has_field_geolocation_cache[ $form_id ];
		}

		$has_field_geolocation_cache[ $form_id ] = 0 < count( $fields );

		return $has_field_geolocation_cache[ $form_id ];
	}

	/**
	 * Determine if a given GF_Field is valid Geolocation enabled field.
	 *
	 * @since 2.0
	 *
	 * @param \GF_Field $field
	 *
	 * @return bool
	 */
	public static function is_valid_geolocation_field( \GF_Field $field ) {
		return $field instanceof \GF_Field_Address;
	}

	/**
	 * Determine if a given GF_Field has geolocation autocomplete enabled.
	 *
	 * @since 2.0
	 *
	 * @param \GF_Field $field
	 *
	 * @return bool
	 */
	public static function is_field_geolocation_enabled( \GF_Field $field ) {
		return isset( $field->EnableGeolocationAutocomplete ) ? $field->EnableGeolocationAutocomplete : false;
	}

	/**
	 * Given the GForm ID creates the JS object for fields autocompletion.
	 *
	 * @since 2.0
	 *
	 * @param int|string $form_id
	 *
	 */
	public function localize_fields_auto_complete( $form_id ) {
		$fields = $this->get_geolocation_fields( $form_id );

		// If we got an error just bail.
		if ( is_wp_error( $fields ) ) {
			return;
		}

		$data = [
			'fields' => $fields,
		];

		wp_localize_script( 'gk-maps-form-address-auto-complete', 'GKMapsFormAddressAutocompleteData', $data );
	}

	/**
	 * Enqueue script & styles on GF Edit Entry Admin
	 *
	 * @param $hook
	 */
	function admin_enqueue_scripts( $hook ) {
		if ( 'gf_edit_forms' === rgget( 'page' ) ) {
			wp_enqueue_script( 'gk-maps-admin-form-fields' );
		}

		$is_entry_detail_edit = apply_filters( 'gform_is_entry_detail_edit', GFForms::get_page() === 'entry_detail_edit' );

		if ( ! $is_entry_detail_edit ) {
			return;
		}

		wp_enqueue_style( 'gvmaps-icon-picker-style' );
		wp_enqueue_script( 'gvmaps-icon-picker' );
	}

	/**
	 * Enqueue scripts and styles on the Gravity Forms Preview page
	 *
	 * @since 1.6
	 *
	 * @param array $styles
	 * @param array $form
	 *
	 * @return array $styles, unmodified
	 */
	function preview_enqueue_scripts( $styles = [], $form = [] ) {
		// Gravity Forms' preview page doesn't call "init"
		$this->register_scripts_and_styles();

		$this->public_enqueue_scripts( $form, false );

		return $styles;
	}

	/**
	 * Add scripts and styles to Gravity Forms No-Conflict mode
	 *
	 * @since 1.7.4
	 *
	 * @param array $registered Existing scripts or styles that have been registered (array of the handles)
	 *
	 * @return array $registered
	 */
	public function register_no_conflicts( $registered ) {
		$registered[] = 'gvmaps-icon-picker';
		$registered[] = 'gvmaps-icon-picker-style';

		return $registered;
	}
}
