<?php
/**
 * Add additional Merge Tags and Merge Tag modifiers to GravityForms
 *
 * @since 1.0
 * @package GravityWP_Advanced_Merge_Tags
 */

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r -- print_r is used for debug logging.
// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore,WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

namespace GravityWP\Advanced_Merge_Tags;

defined( 'ABSPATH' ) || die();

use GFForms;
use GFFormsModel;
use GFAddOn;
use GFAPI;
use GF_Field;
use GF_Fields;
use DateTime;
use GF_Field_Number;
use GFCommon;
use GravityWP\LicenseHandler;

GFForms::include_addon_framework();
/**
 * Add additional Merge Tags and Merge Tag modifiers to GravityForms
 *
 * @since 1.0
 */
class GravityWP_Advanced_Merge_Tags extends GFAddOn {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since  1.0
	 * @access private
	 * @var    GravityWP_Advanced_Merge_Tags $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Contains cached entries.
	 *
	 * @since  1.0
	 * @access private
	 * @var    array<mixed> $_entry Entry cache.
	 */
	private static $_entry = array();

	/**
	 * Defines the version of the GravityWP Advanced Merge Tags Add-On.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_version Contains the version.
	 */
	protected $_version = GWP_ADVANCED_MERGE_TAGS_VERSION;

	/**
	 * Defines the minimum Gravity Forms version required.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_min_gravityforms_version The minimum version required.
	 */
	protected $_min_gravityforms_version = GWP_ADVANCED_MERGE_TAGS_MIN_GF_VERSION;

	/**
	 * Defines the plugin slug.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gravitywp-advanced-merge-tags';

	/**
	 * Defines the main plugin file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gravitywp-advanced-merge-tags\gravitywp-advanced-merge-tags.php';

	/**
	 * Defines the full path to this class file.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;

	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string The URL of the Add-On.
	 */
	protected $_url = 'https://gravitywp.com';

	/**
	 * Defines the title of this add-on.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_title The title of the add-on.
	 */
	protected $_title = 'GravityWP - Advanced Merge Tags';

	/**
	 * Defines the short title of the add-on.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $_short_title The short title.
	 */
	protected $_short_title = 'Advanced Merge Tags';

	/**
	 * Defines the slug on gravitywp.com.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $gwp_site_slug Slug on gravitywp.com.
	 */
	public $gwp_site_slug = 'advanced-merge-tags';

	/**
	 * Members plugin integration.
	 *
	 * @var array<string> $_capabilities Capabilities.
	 */
	protected $_capabilities = array(
		'gravitywp-advanced-merge-tags_form_settings',
		'gravitywp-advanced-merge-tags_plugin_settings',
		'gravitywp-advanced-merge-tags_uninstall',
	);

	/**
	 * Capability Form settings.
	 *
	 * @var string|array<string> A string or an array of capabilities or roles that have access to the form settings.
	 */
	protected $_capabilities_form_settings = 'gravitywp-advanced-merge-tags_form_settings';

	// ------------ Permissions -----------
	/**
	 * Capability Plugin Settings.
	 *
	 * @var string|array<string> A string or an array of capabilities or roles that have access to the settings page.
	 */
	protected $_capabilities_settings_page = 'gravitywp-advanced-merge-tags_plugin_settings';

	/**
	 * Capability Uninstall.
	 *
	 * @var string|array<string> A string or an array of capabilities or roles that can uninstall the plugin.
	 */
	protected $_capabilities_uninstall = 'gravitywp-advanced-merge-tags_uninstall';

	/**
	 * Store the initialized gwp license handler
	 *
	 * @since  1.0.2
	 * @access private
	 * @var    Object $_gwp_license_handler License Handler instance.
	 */
	private $_gwp_license_handler = null;

	/**
	 * Used to cache the access_settings of different forms.
	 *
	 * @var array<mixed> $_access_settings Cached access_settings of different forms.
	 */
	private $_access_settings = array();


	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return GravityWP_Advanced_Merge_Tags $_instance An instance of the GravityWP_Advanced_Merge_Tags class
	 */
	public static function get_instance() {

		if ( self::$_instance === null ) {
			self::$_instance = new GravityWP_Advanced_Merge_Tags();
		}

		return self::$_instance;
	}

	/**
	 * Hook functions in frontend init.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init() {
		// Init license handler.
		if ( $this->_gwp_license_handler === null ) {
			$this->_gwp_license_handler = new GravityWP\LicenseHandler\LicenseHandler( __CLASS__, plugin_dir_path( __FILE__ ) . 'gravitywp-advanced-merge-tags.php' );
		}
		parent::init();

		// This should run before field mergetags and field modifiers are processed to prevent mergetags like {gwp_eeid:5} being processed as if it was a field merge tag for the current entry.
		add_filter( 'gform_pre_replace_merge_tags', array( $this, 'gwp_process_mergetags' ), 9, 7 );
		// Also hook into the regular merge tag filter, otherwise the advanced merge tags won't work in places where field merge tags are not allowed.
		add_filter( 'gform_replace_merge_tags', array( $this, 'gwp_process_mergetags' ), 9, 7 );

		// As 'gform_merge_tag_filter' applies strtolower() to all modifiers, we use this filter to catch modifiers which need to support case-sensitive arguments.
		add_filter( 'gform_pre_replace_merge_tags', array( $this, 'gwp_process_modifiers' ), 10, 7 );

		add_filter( 'gform_form_settings_menu', array( $this, 'filter_form_settings_menu' ) );

		add_filter( 'gform_merge_tag_value_pre_calculation', array( $this, 'filter_get_calculation_value' ), 10, 6 );

		if ( $this->get_plugin_setting( 'gwp_replace_field_merge_tags_in_administrative_fields' ) === '1' ) {
			add_filter( 'gform_save_field_value', array( $this, 'filter_replace_admin_field_variables' ), 10, 4 );
		}

		if ( gravitywp_advanced_merge_tags()->get_plugin_setting( 'gwp_replace_merge_tags_in_post_content' ) === '1' ) {
			add_filter( 'the_content', array( $this, 'replace_post_content_merge_tags' ), 9, 1 ); // This must execute before prio 10, otherwise wptexturize replaces the quotes.
		}
	}

	/**
	 * Hook functions in admin.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function init_admin() {
		parent::init_admin();
	}

	/**
	 * Register scripts.
	 *
	 * @since  1.0
	 *
	 * @return array<mixed>
	 */
	public function scripts() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'  => $this->get_slug() . '_scripts',
				'deps'    => array( 'jquery', 'gform_gravityforms' ),
				'src'     => $this->get_base_url() . "/js/scripts{$min}.js",
				'version' => $this->_version,
				'enqueue' => array(
					array( $this, 'frontend_script_callback' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Check if the fronted scripts should be enqueued.
	 *
	 * @since  1.0
	 *
	 * @param array<mixed> $form The form currently being processed.
	 *
	 * @return bool If the script should be enqueued.
	 */
	public function frontend_script_callback( $form ) {

		return $form && $this->has_calculation_with_advanced_mergetags( $form );
	}

	/**
	 * Check if the fronted scripts should be enqueued.
	 *
	 * @since  1.0
	 *
	 * @param array<mixed> $form The form currently being processed.
	 *
	 * @return bool If the script should be enqueued.
	 */
	private function has_calculation_with_advanced_mergetags( $form ) {

		foreach ( $form['fields'] as &$field ) {
			if ( $field->has_calculation() && strpos( $field->calculationFormula, ':gwp_' ) !== false ) {
				return true;
			}
		}

		return false;
	}

	// # PLUGIN SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Define plugin settings fields.
	 *
	 * @since  1.0.2
	 *
	 * @return array<mixed>
	 */
	public function plugin_settings_fields() {
		// Retrieve license fields.
		$license_fields = $this->_gwp_license_handler->plugin_settings_license_fields();

		$settings = array(
			'license_section'  => array_merge( array( 'id' => 'license_section' ), $license_fields ),
			'settings_section' => array(
				'id'     => 'settings_section',
				'title'  => 'Plugin settings',
				'fields' => array(
					array(
						'label'               => esc_html__( 'Replace field merge tags in Administrative Text fields', 'gravitywpadvancedmergetags' ),
						'type'                => 'toggle',
						'name'                => 'gwp_replace_field_merge_tags_in_administrative_fields',
						'tooltip'             => esc_html__( 'Replace field merge tags like {field:1} in text fields with administrative visibility after an entry is submitted.', 'gravitywpadvancedmergetags' ),
						'class'               => 'medium',
						'default_value'       => '0',
						'validation_callback' => array( $this, 'validate_string_setting' ),
					),
					array(
						'label'               => esc_html__( 'Replace merge tags in post content', 'gravitywpadvancedmergetags' ),
						'type'                => 'toggle',
						'name'                => 'gwp_replace_merge_tags_in_post_content',
						'tooltip'             => esc_html__( 'Replace merge tags in post content.', 'gravitywpadvancedmergetags' ),
						'class'               => 'medium',
						'default_value'       => '0',
						'validation_callback' => array( $this, 'validate_string_setting' ),
					),
					array(
						'type'          => 'radio',
						'name'          => 'gwp_display_errors',
						'label'         => esc_html__( 'Display errors as merge tag output when something goes wrong.' ),
						'tooltip'       => esc_html__( 'Choose what type of error is being displayed for incorrect usage of modifier. Default is none.' ),
						'default_value' => 'none',
						'horizontal'    => false,
						'required'      => true,
						'choices'       => array(
							array(
								'tooltip' => esc_html__( 'Display nothing on errors.' ),
								'label'   => esc_html__( 'Disable display errors (default)' ),
								'value'   => 'none',
							),
							array(
								'tooltip' => esc_html__( 'Display a standard error indicator without details.' ),
								'label'   => esc_html__( 'Display standard error message:' ) . ' ####',
								'value'   => 'standard',
							),
							array(
								'tooltip' => esc_html__( 'This option will display a detailed error message.' ),
								'label'   => esc_html__( 'Display detailed errors, like:' ) . ' ## missing required argument ##',
								'value'   => 'detailed',
							),
						),
					),
				),
			),
		);

		return $settings;
	}

	/**
	 * Return the plugin's icon for the plugin/form settings menu.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_menu_icon() {
		return 'gform-icon--merge-tag';
	}

	/**
	 * Logs an error and returns a error message to replace the merge tag with, depending on the plugin settings.
	 *
	 * @param string $error_message   The type of error. Used to determine what error message to display.
	 * @param string $method          Merge tag / modifier, for the error log.
	 * @param string $additional_info Additional information for the error log.
	 *
	 * @return string
	 */
	public function gwp_error_handler( $error_message, $method, $additional_info = '' ) {

		// Log error.
		$this->log_error( $method . '(): ' . $error_message . '; ' . $additional_info );

		// Get Advanced Merge Tags error setting and return error according this setting.
		$error_handler_setting = $this->get_plugin_setting( 'gwp_display_errors' );

		if ( $error_handler_setting === 'none' ) {
			return '';
		} elseif ( $error_handler_setting === 'standard' ) {
			return '####';
		} elseif ( $error_handler_setting === 'detailed' ) {
			return '## ' . $error_message . ' ##';
		} else {
			return '';
		}
	}

	// # FORM SETTINGS -----------------------------------------------------------------------------------------------

	/**
	 * Define form settings fields.
	 *
	 * @since  1.0
	 *
	 * @param array<mixed> $form The form object.
	 *
	 * @return array<mixed>
	 */
	public function form_settings_fields( $form ) {

		$settings = array(
			array(
				'title'  => 'Advanced Merge Tags',
				'fields' => array(),
			),
			array(
				'title'  => esc_html__( 'Entry access', 'gravitywpadvancedmergetags' ),
				'fields' => array(
					array(
						'name'          => 'access_form_level',
						'label'         => esc_html__( 'Access to entry values', 'gravitywpadvancedmergetags' ),
						'type'          => 'radio',
						'default_value' => 'access_denied',
						'choices'       => $this->get_access_choices( 'form' ),
						/* translators: 1. Open strong tag 2. Close strong tag */
						'description'   => sprintf( esc_html__( 'Who is allowed to see the entry values of this form when retrieved using an advanced merge tag.', 'gravitywpadvancedmergetags' ), '<strong>', '</strong>' ),
					),
					array(
						'name'        => 'access_field_level',
						'label'       => esc_html__( 'Field settings', 'gravitywpadvancedmergetags' ),
						'type'        => 'generic_map',
						'dependency'  => array(
							'field'  => 'access_form_level',
							'values' => array( 'fields' ),
						),
						'key_field'   => array(
							'title'        => esc_html__( 'Field', 'gravitywpadvancedmergetags' ),
							'choices'      => $this->get_field_map_choices( $form['id'] ),
							'allow_custom' => false,
						),
						'value_field' => array(
							'choices'      => $this->get_access_choices( 'field' ),
							'title'        => esc_html__( 'Access', 'gravitywpadvancedmergetags' ),
							'allow_custom' => false,
						),
						'description' => sprintf( esc_html__( 'Add the fields which can be accessed.', 'gravitywpadvancedmergetags' ), '<strong>', '</strong>' ),
					),
				),
			),
			array(
				'title'  => esc_html__( 'User access', 'gravitywpadvancedmergetags' ),
				'fields' => array(
					array(
						'name'  => 'allow_gwp_user_role',
						'label' => esc_html__( 'Allow the :gwp_user_role modifier to be used with entry values from this form', 'gravitywpadvancedmergetags' ),
						'type'  => 'toggle',
					),
				),
			),
		);

		return $settings;
	}

	/**
	 * Get field map choices for specific form, including 'created_by'.
	 *
	 * @since  unknown
	 * @access public
	 *
	 * @uses GF_Addon::get_field_map_choices()
	 *
	 * @param int                 $form_id             Form ID to display fields for.
	 * @param array<mixed>|string $field_type          Field types to only include as choices. Defaults to null.
	 * @param array<mixed>|string $exclude_field_types Field types to exclude from choices. Defaults to null.
	 *
	 * @return array<mixed>
	 */
	public static function get_field_map_choices( $form_id, $field_type = null, $exclude_field_types = null ) {

		$fields = parent::get_field_map_choices( $form_id, $field_type, $exclude_field_types );

		// If field types not restricted insert the created_by entry meta field between the other entry meta. This is not included by default.
		if ( is_null( $field_type ) ) {
			array_splice(
				$fields,
				3,
				0,
				array(
					array(
						'value' => 'created_by',
						'label' => esc_html__(
							'Created by (user)',
							'gravitywpadvancedmergetags'
						),
					),
				)
			);
		}

		return $fields;
	}

	/**
	 * Returns the suported access choices for this plugin for the specified level (form or field).
	 *
	 * @param string $level form or field level.
	 *
	 * @return array<mixed>
	 */
	public static function get_access_choices( $level = 'field' ) {

		$access_choices = array();

		if ( $level === 'form' ) {
			$access_choices[] = array(
				'value' => 'access_denied',
				'label' => 'No access',
			);
		}

		if ( $level === 'field' ) {
			$access_choices[] = array(
				'value' => '',
				'label' => 'Choose access level',
			);
		}

		/*
		TO BE IMPLEMENTED
		$access_choices[] = array(
		'value' => 'capability',
		'label' => 'Users with capability',
		);
		*/

		$access_choices[] = array(
			'value' => 'logged_in',
			'label' => 'Logged in users',
		);

		$access_choices[] = array(
			'value' => 'everyone',
			'label' => 'Everyone',
		);

		if ( $level === 'form' ) {
			$access_choices[] = array(
				'value' => 'fields',
				'label' => 'Specify per field',
			);
		}

		return $access_choices;
	}

	/**
	 * Target for the gform_form_settings_menu hook.
	 * Updated the icon.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $menu_items The form settings menu items.
	 *
	 * @return array<mixed>
	 */
	public function filter_form_settings_menu( $menu_items ) {

		foreach ( $menu_items as &$menu_item ) {

			if ( $menu_item['name'] === 'gravitywpadvancedmergetags' ) {
				$menu_item['icon'] = 'dashicons-arrow-right-alt';
			}
		}

		return $menu_items;
	}

	/**
	 * Get supported modifiers.
	 *
	 * @since 1.0
	 *
	 * @return array<string> Supported modifiers.
	 */
	public static function get_supported_modifiers() {
		// Array of GravityWP Merge Tag modifiers. Add a function preceded with 'modifier_' for processing.
		return array(
			'gwp_get_matched_entry_value',
			'gwp_count_matched_entries',
			'gwp_sum_matched_entries_values',
			'gwp_avg_matched_entries_values',
			'gwp_get_matched_entries_values',
			'gwp_case',
			'gwp_length',
			'gwp_word_count',
			'gwp_reverse',
			'gwp_urlencode',
			'gwp_urldecode',
			'gwp_remove_accents',
			'gwp_append',
			'gwp_replace',
			'gwp_substring',
			'gwp_date_format',
			'gwp_encrypt',
			'gwp_decrypt',
			'gwp_sanitize',
			'gwp_user_role',
			'gwp_json_get',
			'gwp_censor',
		);
	}

	/**
	 * Get supported advanced mergetags (with arguments).
	 *
	 * @since 1.0
	 *
	 * @return array<string> Supported advanced mergetags.
	 */
	public static function get_supported_advanced_mergetags() {
		// Array of GravityWP advanced merge tags. Add a function preceded with 'mergetag_' for processing.
		return array(
			'gwp_parent_slug',
			'gwp_date_created',
			'gwp_date_updated',
			'gwp_date_field',
			'gwp_now',
			'gwp_generate_token',
			'gwp_url',
			'gwp_entry',
			'gwp_eeid',
			'gwp_post_id',
			'gwp_user',
			'gwp_get_matched_entry_value',
			'gwp_calculate',
		);
	}

	/**
	 * Get supported regular mergetags (without arguments).
	 *
	 * @since 1.0
	 *
	 * @return array<string> Supported regular mergetags.
	 */
	public static function get_supported_regular_mergetags() {
		// Array of GravityWP basic merge tags. Add a function preceded with 'mergetag_' for processing.
		return array(
			// 'Basic mergetag name'
			'gwp_current_timestamp',
		);
	}

	/**
	 * Replaces all GravityWP Advanced Merge Tags modifiers including the ones which requires support for case-sensitive arguments.
	 *
	 * @param string       $text The current text in which Merge Tags are being replaced.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Text with case-sensitive GravityWP Advanced Merge Tags modifiers replaced.
	 */
	public static function gwp_process_modifiers( $text, $form, $entry, $url_encode, $esc_html, $nl2br = false, $format = 'html' ) {

		if ( strpos( $text, ':gwp_' ) === false ) {
			// skip early if text has no gwp_ modifier.
			return $text;
		}

		$atts_cs_modifiers_regex = '/{[^{}]*?:(\d+(\.\d+)?):((%s).*?)}/mi';
		$gwp_atts_cs_modifiers   = self::get_supported_modifiers();
		$matches                 = array();

		// Check for a GravityWP case sensitive modifiers and execute the corresponding function.
		foreach ( $gwp_atts_cs_modifiers as $gwp_atts_cs_modifier ) {
			preg_match_all( sprintf( $atts_cs_modifiers_regex, $gwp_atts_cs_modifier ), $text, $matches, PREG_SET_ORDER );

			foreach ( $matches as $match ) {
				$input_id = $match[1];

				/**
				 * START part 1 of GFCommon::replace_field_variable()
				 * Basic checks, get lead value (raw entry value).
				 */
				$field = GFFormsModel::get_field( $form, $input_id );

				// If field is not in the form, don't replace the merge tag.
				if ( ! $field ) {
					continue;
				}

				if ( ! $field instanceof GF_Field ) {
					$field = GF_Fields::create( $field );
				}

				// Get field value from lead.
				$value     = GFFormsModel::get_lead_field_value( $entry, $field );
				$raw_value = $value;

				// If values are in an array we are dealing with a field with multiple inputs.
				if ( is_array( $value ) ) {
					$value = rgar( $value, $input_id );
				}
				/**
				 * END part 1 of GFCommon::replace_field_variable()
				 */

				// Separate default GF modifiers from AMT modifiers.
				$modifier_atts        = self::parse_mergetag_atts( html_entity_decode( $match[3], ENT_QUOTES, 'UTF-8' ) ); // decode html quotes, when combined with GPPA nested mergetags are sometimes passed like "gwp_replace search=&quot; &quot; replace=&quot;-&quot; modifier1=&#039;gwp_case to=lower&#039;".
				$sep_gwp_gf_modifiers = explode( ':', $modifier_atts[0] );
				$modifier_atts[0]     = $sep_gwp_gf_modifiers[0];
				$modifier             = ! empty( $sep_gwp_gf_modifiers[1] ) ? $sep_gwp_gf_modifiers[1] : '';

				// execute part 2 of GFCommon::replace_field_variable().
				self::gf_replace_field_variable_part2( $value, $input_id, $entry, $form, $modifier, $raw_value, $field, $url_encode, $esc_html, $format, $nl2br );

				// Proces AMT modifiers. NOTE: gf_replace_field_variable_part3() is executed here.
				self::process_amt_modifiers( $replace, $match[4], $value, $input_id, $modifier_atts, $field, $raw_value, $form, $entry, $url_encode, $esc_html, $format, $nl2br );

				// Part 4 of GFCommon::replace_field_variable(): Clear merge tag modifiers from the field object.
				$field->set_modifiers( array() );

				$text = str_replace( $match[0], $replace, $text );
			}
		}

		return $text;
	}

	/**
	 * Process Advanced Merge Tag modifiers.
	 *
	 * Applies the specified Advanced Merge Tag modifier to the value.
	 *
	 * @since 1.0
	 *
	 * @param string       $replace The value to be replaced with the modified value.
	 * @param string       $amt_modifier The name of the Advanced Merge Tag modifier to be applied.
	 * @param string       $value The original value to be modified.
	 * @param string       $input_id The ID of the input being modified.
	 * @param array<mixed> $modifier_atts The attributes of the modifier.
	 * @param GF_Field     $field The field object associated with the input.
	 * @param string       $raw_value The raw value of the input.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param string       $format The format of the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @return void
	 */
	public static function process_amt_modifiers( &$replace, &$amt_modifier, &$value, &$input_id, &$modifier_atts, &$field, &$raw_value, &$form, &$entry, &$url_encode, &$esc_html, &$format, &$nl2br ) {
		$method = 'modifier_' . $amt_modifier;
		if ( method_exists( gravitywp_advanced_merge_tags(), $method ) ) {

			$replace = self::$method( $value, $input_id, $modifier_atts, $field, $raw_value, $format, $form, $entry );

			$replace = self::gwp_process_nested_modifiers( $modifier_atts, $replace, $input_id, $field, $raw_value, $format, $form, $entry );

			// Format the html output of gwp_get_matched_entries_values. @todo, move this to the modifier function.
			if ( $amt_modifier === 'gwp_get_matched_entries_values' ) {
				$replace  = wp_kses_post( $replace );
				$esc_html = false;
				if ( isset( $modifier_atts['return_format'] ) && $modifier_atts['return_format'] === 'multiselect' ) {
					// skip the mergetag/shorcode encoding, which breaks the json string, we did this for each value separately.
					return;
				}
			}
		} else {
			// Try to process as regular custom modifier.
			$replace = apply_filters( 'gform_merge_tag_filter', $value, $input_id, $amt_modifier, $field, $raw_value, $format );
		}
		// Prevent mergetag / shortcode injection.
		self::gf_replace_field_variable_part3( $replace, $field, $url_encode, $esc_html, $format, $nl2br );
	}

	/**
	 * Copy of part 2 of GFCommon:replace_field_variables.
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $input_id The field’s ID.
	 * @param array<mixed> $entry The entry array.
	 * @param array<mixed> $form The form object.
	 * @param string       $modifier The merge tag modifier.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param GF_Field     $field The current field.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @return void
	 */
	public static function gf_replace_field_variable_part2( &$value, &$input_id, &$entry, &$form, &$modifier, &$raw_value, &$field, &$url_encode, &$esc_html, &$format, &$nl2br ) {

		/**
		 * START part 2 of GFCommon::replace_field_variable().
		 * Implements support for regular GF modifiers after the advanced modifier. Example {:1:gwp_substring:value start=1 length=2}.
		 */
		$modifiers = array_map( 'trim', explode( ',', $modifier ) );
		$field->set_modifiers( $modifiers );

		if ( in_array( 'urlencode', $modifiers, true ) ) {
			$url_encode = true;
		}

		$value = $field->get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br );

		if ( in_array( 'label', $modifiers, true ) ) {
			if ( empty( $value ) ) {
				$value = '';
			} else {
				$field->set_context_property( 'use_admin_label', in_array( 'admin', $modifiers, true ) );
				$value = $format === 'text' ? sanitize_text_field( GFCommon::get_label( $field ) ) : esc_html( GFCommon::get_label( $field ) );
			}
		} elseif ( $modifier === 'numeric' ) {
			$number_format = $field->numberFormat ? $field->numberFormat : 'decimal_dot';
			$value         = GFCommon::clean_number( $value, $number_format );
		} elseif ( $modifier === 'qty' && $field->type === 'product' ) {
			// Getting quantity associated with product field.
			$products = GFCommon::get_product_fields( $form, $entry, false, false );
			$value    = 0;
			foreach ( $products['products'] as $product_id => $product ) {
				if ( $product_id == $field->id ) { // phpcs:ignore
					$value = $product['quantity'];
				}
			}
		}
		/* END part 2 GFCommon::replace_field_variable() */
	}

	/**
	 * Copy of part 3 of GFCommon:replace_field_variables.
	 *
	 * @param string   $replace The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param GF_Field $field The current field.
	 * @param bool     $url_encode Whether to URL-encode output.
	 * @param bool     $esc_html Indicates if the esc_html function should be applied.
	 * @param string   $format Determines how the value should be formatted. Default is html.
	 * @param bool     $nl2br Indicates if the nl2br function should be applied.
	 * @return void
	 */
	public static function gf_replace_field_variable_part3( &$replace, &$field, &$url_encode, &$esc_html, &$format, &$nl2br ) {
		/**
		 * START part 3 of GFCommon::replace_field_variable().
		 * Prevent injection of merge tags / shortcodes. Format for desired context.
		 */
		// Replace [] with html entities to prevent outputting shortcodes which are entered in the front.
		if ( ! in_array( $field->type, array( 'html', 'section', 'signature' ), true ) ) {
			$replace = GFCommon::encode_shortcodes( $replace );
		}

		// Encode left curly bracket so that merge tags entered in the front end are displayed as is and not parsed.
		$replace = GFCommon::encode_merge_tag( $replace );

		$replace = GFCommon::format_variable_value( $replace, $url_encode, $esc_html, $format, $nl2br );
		/**
		 * END part 3 of GFCommon::replace_field_variable().
		 */
	}

	/**
	 * Processes nested modifiers.
	 *
	 * @param mixed        $modifier_atts Array of modifier attributes.
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $input_id The field’s ID.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string  The modified value.
	 */
	public static function gwp_process_nested_modifiers( $modifier_atts, $value, $input_id, $field, $raw_value, $format, $form, $entry ) {
		// Process additional modifiers, if present.
		if ( ! empty( $modifier_atts ) ) {
			$modifiers = self::atts_to_modifiers( $modifier_atts );
			foreach ( $modifiers as $modifier ) {
				$gwp_modifier = explode( ' ', $modifier )[0];
				if ( in_array( $gwp_modifier, self::get_supported_modifiers(), true ) ) {
					// In case this is a nested Advanced Merge Tags case sensitive modifiers.
					$nested_method        = 'modifier_' . $gwp_modifier;
					$nested_modifier_atts = self::parse_mergetag_atts( $modifier );
					$value                = self::$nested_method( $value, $input_id, $nested_modifier_atts, $field, $raw_value, $format, $form, $entry );
				} else {
					// Apply regular Merge Tag modifiers which are hooked to the case insensitive default GF merge tag filter. This includes third party modifiers.
					$value = apply_filters( 'gform_merge_tag_filter', $value, $input_id, $modifier, $field, $raw_value, $format );
					if ( $value === false ) {
						$value = '';
					}
				}
			}
		}
		return $value;
	}

	/**
	 * Replaces all GravityWP Basic and Advanced Merge Tags.
	 *
	 * @param string             $text The current text in which Merge Tags are being replaced.
	 * @param false|array<mixed> $form The current form.
	 * @param false|array<mixed> $entry The current entry.
	 * @param bool               $url_encode Whether to URL-encode output.
	 * @param bool               $esc_html Indicates if the esc_html function should be applied.
	 * @param bool               $nl2br Indicates if the nl2br function should be applied.
	 * @param string             $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Text with GravityWP Advanced Merge Tags replaced.
	 */
	public static function gwp_process_mergetags( $text, $form, $entry, $url_encode, $esc_html, $nl2br = false, $format = 'html' ) {

		if ( empty( $text ) || strpos( $text, '{gwp_' ) === false ) {
			return $text;
		}

		/**
		 * Basic Merge Tags
		 *
		 * Add Merge Tags to find in this array:
		 * '{mergetag}' => 'function_to_execute'.
		 */
		$gwp_merge_tags = self::get_supported_regular_mergetags();

		/* process Merge Tags without attributes */
		foreach ( $gwp_merge_tags as $gwp_merge_tag ) {

			if ( strpos( $text, '{' . $gwp_merge_tag . '}' ) !== false ) {
				$method = 'mergetag_' . $gwp_merge_tag;
				$value  = self::$method( $form, $entry, $url_encode, $esc_html, $nl2br, $format, $gwp_merge_tag );

				$value = GFCommon::format_variable_value( $value, $url_encode, $esc_html, $format, $nl2br );

				$text = str_replace( '{' . $gwp_merge_tag . '}', $value, $text );
			}
		}

		/**
		 * Advanced Merge Tags with attributes
		 */
		$atts_merge_tags_regex = '/{%s(.*?)}/ism'; // Regex pattern for Advanced Mergetags.
		$gwp_atts_merge_tags   = self::get_supported_advanced_mergetags();

		/* process Merge Tags with attributes */
		foreach ( $gwp_atts_merge_tags as $gwp_atts_merge_tag ) {

			if ( $gwp_atts_merge_tag === 'gwp_calculate' ) {
				// use an experimental pattern that can capture nested {} characters.
				preg_match_all( sprintf( '/{%s((?:[^{}]|\{[^{}]*\})*)\}/ism', $gwp_atts_merge_tag ), $text, $matches, PREG_SET_ORDER );
			} else {
				// use default, battle tested, pattern.
				preg_match_all( sprintf( $atts_merge_tags_regex, $gwp_atts_merge_tag ), $text, $matches, PREG_SET_ORDER );
			}

			$method = 'mergetag_' . $gwp_atts_merge_tag;

			foreach ( $matches as $match ) {

				$mergetag_atts = self::parse_mergetag_atts( $match[1] );

				$value = self::$method( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format );

				$value = GFCommon::format_variable_value( $value, $url_encode, $esc_html, $format, $nl2br );

				$text = str_replace( $match[0], $value, $text );
			}
		}

		return $text;
	}

	/**
	 * Replace variables in administrative fields values before they are saved to the database.
	 *
	 * @param string       $value The fields input value.
	 * @param array<mixed> $lead The current entry object.
	 * @param GF_Field     $field The current field object.
	 * @param array<mixed> $form The current form object.
	 *
	 * @return string
	 */
	public function filter_replace_admin_field_variables( $value, $lead, $field, $form ) {
		// Text Fields only, when adding other field types in the future be aware of serialized values (e.g. List Fields).
		if ( ! is_a( $field, 'GF_Field' ) ) {
			return $value;
		}

		/**
		 * Filter the field type to replace variables in administrative fields values before they are saved to the database.
		 *
		 * @since 1.4.2
		 *
		 * @param array<string> $field_types The field types.
		 * @param string       $value The fields input value.
		 * @param array<mixed> $lead The current entry object.
		 * @param GF_Field     $field The current field object.
		 * @param array<mixed> $form The current form object.
		 */
		$field_types = apply_filters( 'gwp_amt_replace_admin_field_variables', array( 'text' ), $value, $lead, $field, $form );

		if ( $field->is_administrative() && in_array( $field->type, $field_types, true ) && ! is_serialized( $value ) ) {
			$value = GFCommon::replace_variables( $value, $form, $lead, false, false, false, 'text' );
		}
		return $value;
	}

	/**
	 * Retrieve the mergetag attributes regex.
	 *
	 * This is a copy of WordPress (v5.6) get_shortcode_atts_regex().
	 *
	 * @since 1.0.1
	 *
	 * @return string The shortcode attribute regular expression
	 */
	public static function get_mergetag_atts_regex() {
		// note: this rexeg is also used client side in scripts.js.
		return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
	}

	/**
	 * Retrieve all attributes from the mergetag attribute string.
	 *
	 * The attributes list has the attribute name as the key and the value of the
	 * attribute as the value in the key/value pair.
	 *
	 * This function is based on the WordPress shortcode_parse_atts() function, with the stripcslashes() removed for allowing datetime strings as attributes.
	 *
	 * @since 1.0.1
	 *
	 * @param string $text The mergetag attribute string.
	 *
	 * @return array<mixed>|string List of attribute values.
	 *                      Returns empty array if '""' === trim( $text ).
	 *                      Returns empty string if '' === trim( $text ).
	 *                      All other matches are checked for not empty().
	 */
	public static function parse_mergetag_atts( $text ) {
		$atts    = array();
		$pattern = self::get_mergetag_atts_regex();
		$text    = preg_replace( "/[\x{00a0}\x{200b}]+/u", ' ', $text );
		if ( preg_match_all( $pattern, $text, $match, PREG_SET_ORDER ) ) {
			foreach ( $match as $m ) {
				if ( ! empty( $m[1] ) ) {
					$atts[ strtolower( $m[1] ) ] = $m[2];
				} elseif ( ! empty( $m[3] ) ) {
					$atts[ strtolower( $m[3] ) ] = $m[4];
				} elseif ( ! empty( $m[5] ) ) {
					$atts[ strtolower( $m[5] ) ] = $m[6];
				} elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
					$atts[] = $m[7];
				} elseif ( isset( $m[8] ) && strlen( $m[8] ) ) {
					$atts[] = $m[8];
				} elseif ( isset( $m[9] ) ) {
					$atts[] = $m[9];
				}
			}

			// Reject any unclosed HTML elements.
			foreach ( $atts as &$value ) {
				if ( false !== strpos( $value, '<' ) ) {
					if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
						$value = '';
					}
				}
			}
		} else {
			$atts = ltrim( $text );
		}

		return $atts;
	}

	/**
	 * Process gwp_length modifier
	 *
	 * Counts the chars in a value;
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns the char count.
	 */
	public static function modifier_gwp_length( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		return (string) mb_strlen( $value );
	}

	/**
	 * Process gwp_json_get modifier.
	 *
	 * Returns a value from a json array.
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns the char count.
	 */
	public static function modifier_gwp_json_get( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		$atts = shortcode_atts(
			array(
				'0'         => null, // Contains the modifier (without attributes).
				'path'      => '',   // json path, like level1/level2/key.
				'not_found' => '',   // return if not found.
			),
			$modifier_atts
		);

		$decoded = json_decode( html_entity_decode( $value ), true );
		if ( $decoded === null ) {
			gravitywp_advanced_merge_tags()->log_debug( __METHOD__ . '(): gwp_json_get: json_decode failed. Value: ' . print_r( $value, true ) );
		}

		$return = rgars( $decoded, $atts['path'], $atts['not_found'] );

		if ( is_array( $return ) ) {
			$return = wp_json_encode( $return );
		} elseif ( is_scalar( $return ) ) {
			$return = strval( $return );
		} else {
			$return = '';
		}

		if ( $format === 'html' ) {
			$return = htmlentities( $return );
		}
		return $return;
	}

	/**
	 * Process gwp_word_count modifier
	 *
	 * Counts the words in a value;
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns the word count.
	 */
	public static function modifier_gwp_word_count( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		$atts = shortcode_atts(
			array(
				'0'                     => null, // Contains the modifier (without attributes).
				'additional_separators' => '',   // A list of additional characters which will be considered as separator.
			),
			$modifier_atts
		);

		$regex = '/[^\s' . $atts['additional_separators'] . ']+/';

		// disregard html within the Rich Text editor.
		if ( $field->type === 'textarea' && $field->useRichTextEditor ) {
			$value = str_replace( '<', ' <', $value );
			$value = preg_replace( '/&nbsp;|&#160;/i', ' ', $value );
			$value = trim( strip_tags( $value ) );
		}

		/**
		 * Allow adaptation of the Word Count regex.
		 *
		 * @since 1.2
		 *
		 * @param string   $regex The regex expression.
		 * @param string   $value The fields value.
		 * @param array    $atts  The mergetag attributes.
		 * @param GF_Field $field The Field object.
		 */
		$regex   = apply_filters( 'gravitywp_advancedmergetags_wordcount_regex', $regex, $value, $atts, $field );
		$matches = array();

		$word_count = preg_match_all( $regex, $value, $matches );

		if ( $word_count === false ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'count failed due to an error', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		/**
		 * Allow adaptation of the Word Count result.
		 *
		 * @since 1.2
		 *
		 * @param int      $word_count The regex expression.
		 * @param array<mixed>    $matches    The matched words.
		 * @param string   $value      The fields value.
		 * @param array    $atts       The mergetag attributes.
		 * @param GF_Field $field      The Field object.
		 */
		$word_count = apply_filters( 'gravitywp_advancedmergetags_wordcount_result', $word_count, $matches, $value, $atts, $field );

		return (string) $word_count;
	}

	/**
	 * Process gwp_reverse modifier
	 *
	 * Reverses a string.
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns the reversed $value.
	 */
	public static function modifier_gwp_reverse( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		return (string) strrev( $value );
	}

	/**
	 * Process gwp_substring modifier
	 *
	 * Get a part of a string.
	 *
	 * @since 1.1.2
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns the substring of $value.
	 */
	public static function modifier_gwp_substring( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		$atts = shortcode_atts(
			array(
				'0'      => null, // Contains the modifier (without attributes).
				'start'  => '',   // Where does the substring start.
				'length' => null, // Length of the substring.
			),
			$modifier_atts
		);

		// Length must be postivive int or null if omitted.
		if ( $atts['length'] === null ) {
			$length = null;
		} else {
			$length = absint( $atts['length'] );
		}

		// Start can be negative.
		$start = (int) $atts['start'];

		return mb_substr( $value, $start, $length );
	}

	/**
	 * Process gwp_urlencode modifier
	 *
	 * Url encodes the field value with the purpose to pass it as an url parameter for dynamic population.
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns an url_encoded value.
	 */
	public static function modifier_gwp_urlencode( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		// List-field.
		if ( $field->type === 'list' ) {
			// Use the raw value in stead of the display value, which contains html table tags.
			$values = maybe_unserialize( $raw_value );
			if ( empty( $values ) ) {
				return $value;
			}
			foreach ( $values as $key => $num ) {
				$values[ $key ] = implode( '|', $num );
			}
			$return_val = implode( ',', $values );
			return urlencode( $return_val );
		}

		if ( $field->type === 'checkbox' && is_array( $raw_value ) ) {
			// filter null and ''.
			$checked_values = array_filter( $raw_value, 'strlen' );
			return urlencode( implode( ',', $checked_values ) );
		}
		// Default.
		return urlencode( $value );
	}

		/**
		 * Process gwp_urlencode modifier
		 *
		 * Url encodes the field value with the purpose to pass it as an url parameter for dynamic population.
		 *
		 * @since 1.0
		 *
		 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
		 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
		 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
		 * @param GF_Field     $field The current field.
		 * @param mixed        $raw_value The raw value submitted for this field.
		 * @param string       $format Whether the text is formatted as html or text.
		 *
		 * @return string  Returns an url_encoded value.
		 */
	public static function modifier_gwp_urldecode( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		// Default.
		return urldecode( $value );
	}

	/**
	 * Process gwp_sanitize modifier
	 *
	 * Applies different sanitize functions to the value.
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Returns a sanitized value.
	 */
	public static function modifier_gwp_sanitize( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		$atts = shortcode_atts(
			array(
				'0'    => null,   // Contains the modifier (without attributes).
				'type' => 'sanitize_text_field',
			),
			$modifier_atts
		);

		switch ( $modifier_atts['type'] ) {
			case 'sanitize_text_field':
				return sanitize_text_field( $value );
			case 'sanitize_title_with_dashes':
				return sanitize_title_with_dashes( $value );
			case 'trim':
				return trim( $value );
			case 'absint':
				return (string) absint( $value );
			default:
				return sanitize_text_field( $value );
		}

		// Default.
		return $value;
	}

	/**
	 * Process gwp_get_matched_entry_value modifier
	 *
	 * Finds all entries where the match_id property value of an entry equals the Merge Tag value ($value). The property of the newest entry found is returned (with default sort_order and offset).
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string If succesfull return the specified matched entry value, otherwise return '' or error;
	 */
	public static function modifier_gwp_get_matched_entry_value( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {

		$atts = shortcode_atts(
			array(
				'0'          => null,   // Contains the modifier (without attributes).
				'form_id'    => null,   // Which form-entries to search.
				'match_id'   => null,   // Entry property value to match the Merge Tag value with.
				'return_id'  => null,   // Entry property value to return.
				'sort_order' => 'desc', // Define sort order of the matched entries.
				'offset'     => '0',    // Define which matched entry to get.
			),
			$modifier_atts
		);

		// check required arguments.
		if ( empty( $atts['form_id'] ) || empty( $atts['match_id'] ) || empty( $atts['return_id'] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing required argument', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		switch ( $atts['sort_order'] ) {
			case 'asc':
				$arg_sort_order = 'ASC';
				break;
			case 'rand':
				$arg_sort_order = 'RAND';
				break;
			default:
				$arg_sort_order = 'DESC';
		}

		// Prepare search criteria.
		$search_criteria = array();

		$search_criteria['status'] = 'active';

		$search_criteria['field_filters'][] = array(
			'key'      => $atts['match_id'],
			'operator' => 'is',
			'value'    => is_array( $raw_value ) ? $raw_value[ $merge_tag ] : $raw_value,
		);

		// Add additional search filters.
		if ( ! self::add_filters_to_search_criteria( $modifier_atts, $search_criteria, $form, $entry ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'incorrect search filters', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		// Sort-order.
		$sorting = array(
			'key'        => 'id',
			'direction'  => $arg_sort_order,
			'is_numeric' => true,
		);

		// Offset / page size.
		$paging = array(
			'offset'    => $atts['offset'],
			'page_size' => 1, // just 1 entry needed, which is faster and saves memory.
		);

		// get entry.
		$entry_array = GFAPI::get_entries( absint( $atts['form_id'] ), $search_criteria, $sorting, $paging );

		// return value or property of the entry which matches all criteria.
		if ( ! empty( $entry_array ) ) {
			return self::get_allowed_field_value( absint( $atts['form_id'] ), $entry_array[0], $atts['return_id'] );
		} else {
			return '';
		}
	}

	/**
	 * Process gwp_current_timestamp Merge Tag
	 *
	 * @since 1.0
	 *
	 * @return int Returns the current time measured in the number of seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 */
	public static function mergetag_gwp_current_timestamp() {
		return time();
	}

	/**
	 * Process gwp_parent_slug Merge Tag.
	 *
	 * Returns the parent slug of the current page. Default is depth=0, which return the direct parent. depth='top' returns the highest parent.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 *
	 * @return string Return the specified parent page slug if it exists, otherwise return ''.
	 */
	public static function mergetag_gwp_parent_slug( $mergetag_atts ) {
		$atts = shortcode_atts(
			array(
				'depth' => '0',
			),
			$mergetag_atts
		);

		// Get an array of Ancestors and Parents if they exist.
		global $post;
		$parents = get_post_ancestors( $post->ID );

		if ( empty( $parents ) ) {
			return '';
		}

		$parent_count = count( $parents );

		if ( ctype_digit( $atts['depth'] ) && ( $parent_count > $atts['depth'] ) ) {
			$parent_id = $parents[ $atts['depth'] ];
		} elseif ( 'top' === $atts['depth'] ) {
			// Get the top Level page->ID, which is the last item in the array.
			$parent_id = $parents[ $parent_count - 1 ];
		} else {
			// invalid argument.
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid depth argument', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		return get_post( $parent_id )->post_name;
	}

	/**
	 * Process gwp_entry Merge Tag.
	 *
	 * Returns an entry property.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @param string       $format The format of the output.
	 *
	 * @return string
	 */
	public static function mergetag_gwp_entry( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$properties = explode( ':', $mergetag_atts[0] );

		if ( ! isset( $properties[1] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid property', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		$value = rgar( $entry, $properties[1] );
		if ( is_null( $value ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'property does not exist', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// execute AMT modifiers if present.
		if ( isset( $properties[2] ) ) {
			$modifier_atts    = $mergetag_atts;
			$modifier_atts[0] = $properties[2];
			$input_id         = $properties[1];
			$field            = GFAPI::get_field( $form, $input_id );
			if ( ! is_a( $field, 'GF_Field' ) ) {
				// mock a field object.
				$field = new GF_Field( array( 'type' => 'entry_property' ) );
			}
			self::process_amt_modifiers( $value, $modifier_atts[0], $value, $input_id, $modifier_atts, $field, $value, $form, $entry, $url_encode, $esc_html, $nl2br, $format );
		}

		return $value;
	}

	/**
	 * Process gwp_get_matched_entry_value Merge Tag.
	 *
	 * Returns an entry value from a matched entry.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @param string       $format The format of the output.
	 *
	 * @return string
	 */
	public static function mergetag_gwp_get_matched_entry_value( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$value = $mergetag_atts['value'] ?? '';

		if ( ! $value ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing value paramater', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// execute gwp_get_matched_entry_value modifier.
		$modifier_atts    = $mergetag_atts;
		$modifier_atts[0] = 'gwp_get_matched_entry_value';
		$input_id         = '';
		$field            = GFAPI::get_field( $form, $input_id );
		if ( ! is_a( $field, 'GF_Field' ) ) {
			// mock a field object.
			$field = new GF_Field( array( 'type' => 'entry_property' ) );
		}
		// prevent double nl2br and esc_html in $this->process_amt_modifiers. $this->gwp_process_mergetags() will format the output.
		$nl2br2      = false;
		$esc_html2   = false;
		$url_encode2 = false;
		self::process_amt_modifiers( $value, $modifier_atts[0], $value, $input_id, $modifier_atts, $field, $value, $form, $entry, $url_encode2, $esc_html2, $format, $nl2br2 );

		return $value;
	}

	/**
	 * Process gwp_eeid Merge Tag.
	 *
	 * Returns an entry property of the entry with the encrypted eeid URL parameter.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @param string       $format The format of the output.
	 *
	 * @return string The value of the entry property.
	 */
	public static function mergetag_gwp_eeid( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$properties = explode( ':', $mergetag_atts[0] );
		// by default we retrieve the 'eeid' url_parameter, but it is possible to use other parameters like 'task'.  both {gwp_eeid_task:1} and {gwp_eeid task:1} can be used to access those..
		$parameter = ltrim( $properties[0], '_ ' ) !== '' ? ltrim( $properties[0], '_ ' ) : 'eeid';

		$eeid = rgget( $parameter );

		// Revert urlsafe encoding to regular base64.
		$decoded_eeid = strtr( $eeid, '-_', '+/' );

		// Handle possible errors.
		$entry_id = (int) GFCommon::openssl_decrypt( $decoded_eeid );
		if ( ! $entry_id ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid id', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		if ( ! isset( self::$_entry[ $entry_id ] ) ) {
			self::$_entry[ $entry_id ] = GFAPI::get_entry( $entry_id );
		}

		if ( is_wp_error( self::$_entry[ $entry_id ] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'entry not found', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		if ( ! isset( $properties[1] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid property', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		$raw_value = rgar( self::$_entry[ $entry_id ], $properties[1] );
		if ( is_null( $raw_value ) ) {
			gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'property does not exist', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// Get the Form and create a Field object.
		$form  = GFAPI::get_form( self::$_entry[ $entry_id ]['form_id'] );
		$field = false;
		if ( is_numeric( $properties[1] ) ) {
			$field = GFAPI::get_field( $form, $properties[1] );
		}

		if ( $field instanceof GF_Field ) {
			$modifier = isset( $properties[2] ) && strpos( $properties[2], 'gwp_' ) === 0 ? ( isset( $properties[3] ) ? $properties[3] : '' ) : $properties[2] ?? '';
			$field->set_modifiers( explode( ',', $modifier ) );
			$value = $raw_value;
			self::gf_replace_field_variable_part2( $value, $input_id, self::$_entry[ $entry_id ], $form, $modifier, $raw_value, $field, $url_encode, $esc_html, $format, $nl2br );
		} else {
			$value       = $raw_value;
			$field       = new GF_Field();
			$field->type = 'entry_property';
		}

		if ( isset( $properties[2] ) && strpos( $properties[2], 'gwp_' ) === 0 ) {
			$modifier_atts    = $mergetag_atts;
			$modifier_atts[0] = $properties[2];
			self::process_amt_modifiers( $value, $modifier_atts[0], $value, $properties[1], $modifier_atts, $field, $value, $form, self::$_entry[ $entry_id ], $url_encode, $esc_html, $nl2br, $format );
		} else {
			// prevents mergetag / shortcode injection.
			self::gf_replace_field_variable_part3( $value, $field, $url_encode, $esc_html, $format, $nl2br );
		}

		return $value;
	}
	/**
	 * Process gwp_url Merge Tag.
	 *
	 * Returns the current url, site_url, home_url or a part of the url.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 *
	 * @return string
	 */
	public static function mergetag_gwp_url( $mergetag_atts ) {
		$atts = shortcode_atts(
			array(
				'type'  => '',
				'index' => '',
			),
			$mergetag_atts
		);

		if ( $atts['type'] === 'site_url' ) {
			return site_url();
		}

		if ( $atts['type'] === 'home_url' ) {
			return home_url();
		}

		$scheme = isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) ? 'https' : 'http';
		if ( $atts['type'] === 'scheme' ) {
			return $scheme;
		}

		$domain = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		if ( $atts['type'] === 'domain' ) {
			if ( $atts['index'] !== '' ) {
				$domain = explode( '.', $domain );
				$index  = (int) $atts['index'];
				if ( $index < 0 ) {
					$index = count( $domain ) + $index;
				}
				return $domain[ $index ] ?? '';
			} else {
				return $domain;
			}
		}

		$path = isset( $_SERVER['PATH_INFO'] ) ? sanitize_text_field( wp_unslash( $_SERVER['PATH_INFO'] ) ) : '';
		if ( empty( $path ) ) {
			$path = parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			$path = $path['path'] ?? '';
		}

		if ( $atts['type'] === 'path' ) {
			if ( $atts['index'] !== '' ) {
				$path  = trim( $path, '/' );
				$path  = explode( '/', $path );
				$index = (int) $atts['index'];
				if ( $index < 0 ) {
					$index = count( $path ) + $index;
				}
				return $path[ $index ] ?? '';
			} else {
				return $path;
			}
		}

		return $scheme . '://' . $domain . $path;
	}

	/**
	 * Process gwp_user Merge Tag.
	 *
	 * Returns a user property, while allowing other modifiers.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @param string       $format The format of the output.
	 *
	 * @return string
	 */
	public static function mergetag_gwp_user( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		global $current_user;
		$properties = explode( ':', $mergetag_atts[0] );

		if ( ! isset( $properties[1] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid property', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// Prevent leaking hashed passwords.
		$value = $properties[1] == 'user_pass' ? '' : $current_user->get( $properties[1] );

		if ( is_null( $value ) || $value === false ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'property does not exist', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// execute AMT modifiers if present.
		if ( isset( $properties[2] ) ) {
			$modifier_atts    = $mergetag_atts;
			$modifier_atts[0] = $properties[2];
			$input_id         = $properties[1];
			// mock a field object.
			$field = new GF_Field( array( 'type' => 'user_property' ) );
			self::process_amt_modifiers( $value, $modifier_atts[0], $value, $input_id, $modifier_atts, $field, $value, $form, $entry, $url_encode, $esc_html, $nl2br, $format );
		}

		return $value;
	}

	/**
	 * Process gwp_calculate Merge Tag.
	 *
	 * Returns the result of a calculation formula and format the number.
	 *
	 * @since 1.0
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The form array.
	 * @param array<mixed> $entry The entry array.
	 * @param bool         $url_encode Whether to URL encode the output.
	 * @param bool         $esc_html Whether to escape HTML entities in the output.
	 * @param bool         $nl2br Whether to convert newlines to HTML line breaks in the output.
	 * @param string       $format The format of the output.
	 *
	 * @return string
	 */
	public static function mergetag_gwp_calculate( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$atts = shortcode_atts(
			array(
				'formula'       => '',
				'number_format' => 'decimal_point',
				'thousands_sep' => 'true',
				'currency'      => '',
			),
			$mergetag_atts
		);

		if ( empty( $entry ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'no entry available', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// Convert merge tags with advanced modifiers. This might break the formula if the output is not numeric. But we leave that to the responsibility of the user.
		$formula = self::gwp_process_modifiers( $atts['formula'], $form, $entry, false, false, false, 'text' );

		// Create a number field with type calculation. This allows us to use GFCommon::calculate.
		$field = new GF_Field_Number(
			array(
				'enableCalculation'  => true,
				'calculationFormula' => $formula,
				'numberFormat'       => $atts['number_format'],
				'type'               => 'calculation',
			)
		);

		$value = GFCommon::calculate( $field, $form, $entry );

		$include_thousands_sep = $atts['thousands_sep'] !== 'false';

		$value = GFCommon::format_number( $value, $atts['number_format'], $atts['currency'], $include_thousands_sep );

		return $value;
	}

	/**
	 * Process gwp_date_created Merge Tag.
	 *
	 * @since 1.0.1
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Return the formatted / modified datetime of when the entry was created.
	 */
	public static function mergetag_gwp_date_created( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$atts = shortcode_atts(
			array(
				'format'   => 'Y-m-d H:i:s',
				'modify'   => '',
				'timezone' => wp_timezone_string(),
			),
			$mergetag_atts
		);

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['modify'] ) ) {
			$atts['modify'] = self::replace_nested_merge_tags( $atts['modify'], $form, $entry );
		}

		$datetime = rgar( $entry, 'date_created', '' );
		return self::modify_format_datetime( $datetime, 'Y-m-d H:i:s', $atts['format'], $atts['modify'], 'UTC', $atts['timezone'] );
	}

	/**
	 * Process gwp_date_updated Merge Tag.
	 *
	 * @since 1.0.1
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Return the formatted / modified datetime of when the entry was updated.
	 */
	public static function mergetag_gwp_date_updated( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$atts     = shortcode_atts(
			array(
				'format'   => 'Y-m-d H:i:s',
				'modify'   => '',
				'timezone' => wp_timezone_string(),
			),
			$mergetag_atts
		);
		$datetime = rgar( $entry, 'date_updated', '' );

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['modify'] ) ) {
			$atts['modify'] = self::replace_nested_merge_tags( $atts['modify'], $form, $entry );
		}

		return self::modify_format_datetime( $datetime, 'Y-m-d H:i:s', $atts['format'], $atts['modify'], 'UTC', $atts['timezone'] );
	}

	/**
	 * Process gwp_date_field Merge Tag.
	 *
	 * @since 1.0.1
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Return the formatted / modified datetime of a datefield value.
	 */
	public static function mergetag_gwp_date_field( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $mergetag_atts['id'] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing required argument', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		$atts = shortcode_atts(
			array(
				'format'         => 'Y-m-d H:i:s',
				'modify'         => '',
				'timezone'       => wp_timezone_string(),
				'field_timezone' => wp_timezone_string(),
			),
			$mergetag_atts
		);

		$datetime = rgar( $entry, $mergetag_atts['id'] );
		$field    = GFFormsModel::get_field( $form, $mergetag_atts['id'] );

		if ( empty( $field ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'field not found', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		if ( empty( $datetime ) ) {
			return '';
		}

		if ( ! empty( $mergetag_atts['field_format'] ) ) {
			$field_format = $mergetag_atts ['field_format'];
		} elseif ( $field->type === 'date' ) {
			// This is how GF Datefields stores dates (regardless of field display settings).
			$field_format = 'Y-m-d';
		} else {
			// return if field_format is unknown.
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'unknown field format', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $mergetag_atts, true ) );
		}

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['modify'] ) ) {
			$atts['modify'] = self::replace_nested_merge_tags( $atts['modify'], $form, $entry );
		}

		return self::modify_format_datetime( $datetime, $field_format, $atts['format'], $atts['modify'], $atts['field_timezone'], $atts['timezone'] );
	}

	/**
	 * Process gwp_now merge tag
	 *
	 * @since 1.2.4
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Return the current formatted / modified time.
	 */
	public static function mergetag_gwp_now( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$atts = shortcode_atts(
			array(
				'format'   => 'Y-m-d H:i:s',
				'modify'   => '',
				'timezone' => wp_timezone_string(),
			),
			$mergetag_atts
		);

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['modify'] ) ) {
			$atts['modify'] = self::replace_nested_merge_tags( $atts['modify'], $form, $entry );
		}

		return self::modify_format_datetime( (string) time(), 'U', $atts['format'], $atts['modify'], 'UTC', $atts['timezone'] );
	}

	/**
	 * Process gwp_post_id merge tag
	 *
	 * @since 1.2.4
	 *
	 * @param array<mixed> $mergetag_atts Contains the Merge Tags attributes.
	 * @param array<mixed> $form The current form.
	 * @param array<mixed> $entry The current entry.
	 * @param bool         $url_encode Whether to URL-encode output.
	 * @param bool         $esc_html Indicates if the esc_html function should be applied.
	 * @param bool         $nl2br Indicates if the nl2br function should be applied.
	 * @param string       $format Determines how the value should be formatted. Default is html.
	 *
	 * @return string Return a post id or.
	 */
	public static function mergetag_gwp_post_id( $mergetag_atts, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {
		$atts = shortcode_atts(
			array(
				'url' => '',
			),
			$mergetag_atts
		);

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['url'] ) ) {
			$atts['url'] = self::replace_nested_merge_tags( $atts['url'], $form, $entry );
		}

		$post_id = url_to_postid( $atts['url'] );

		if ( ! $post_id ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'post-id not found', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts['url'], true ) );
		}

		return (string) $post_id;
	}

	/**
	 * Helper function for different mergetags which modify and format date and time.
	 *
	 * @since 1.0.1
	 *
	 * @param string $datetime_str     String containing datetime.
	 * @param string $current_format   Format of $datetime_str. @see https://www.php.net/manual/en/datetime.format.php.
	 * @param string $target_format    The desired date format.
	 * @param string $modify           Modifies $datetime with a relative datetime string. @see https://www.php.net/manual/en/datetime.formats.relative.php.
	 * @param string $current_timezone Shift to this timezone. @see https://www.php.net/manual/en/timezones.php.
	 * @param string $target_timezone  Shift to this timezone.
	 *
	 * @return string Return the formatted / modified datetime.
	 */
	public static function modify_format_datetime( string $datetime_str, $current_format, $target_format, $modify = '', $current_timezone = 'UTC', $target_timezone = '' ) {

		$source_timezone = timezone_open( $current_timezone );
		if ( $source_timezone === false ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid timezone', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $current_timezone, true ) );
		}

		$datetime_str = DateTime::createFromFormat( $current_format, $datetime_str, $source_timezone );

		if ( $datetime_str === false ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid or empty date string', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $datetime_str, true ) );
		}

		// Modify.
		if ( ! empty( $modify ) ) {
			try {
				$datetime_str = $datetime_str->modify( $modify );
			} catch ( \Exception $e ) {
				return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'invalid date modifier', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modify, true ) . '; ' . $e->getMessage() );
			}
		}

		// Shift timezone. @todo refactor and do proper error checking + user feedback.
		! empty( $target_timezone ) ? $target_timezone = timezone_open( $target_timezone ) : $target_timezone = false;

		// Format. // @todo proper error checking + user feedback.
		if ( $datetime_str !== false && $target_timezone !== false ) {
			$datetime_str = wp_date( $target_format, $datetime_str->getTimestamp(), $target_timezone );
		}

		return $datetime_str && $target_timezone ? $datetime_str : '';
	}

	/**
	 * Process gwp_count_matched_entries modifier
	 *
	 * Counts all active entries where the match_id property value of an entry equals the Merge Tag value ($value).
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string If succesfull return the specified matched entries count, otherwise return '' or error;
	 */
	public static function modifier_gwp_count_matched_entries( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {

		$atts = shortcode_atts(
			array(
				'0'        => null,   // Contains the modifier (without attributes).
				'form_id'  => null,   // Which form-entries to search.
				'match_id' => null,   // Entry property value to match the Merge Tag value with.
			),
			$modifier_atts
		);

		// check required arguments.
		if ( empty( $atts['form_id'] ) || empty( $atts['match_id'] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing required argument', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		// Prepare search criteria.
		$search_criteria = array();

		$search_criteria['status'] = 'active';

		$search_criteria['field_filters'][] = array(
			'key'      => $atts['match_id'],
			'operator' => 'is',
			'value'    => is_array( $raw_value ) ? $raw_value[ $merge_tag ] : $raw_value,
		);

		// Add additional search filters.
		if ( ! self::add_filters_to_search_criteria( $modifier_atts, $search_criteria, $form, $entry ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'incorrect search filters', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		return number_format_i18n( GFAPI::count_entries( absint( $atts['form_id'] ), $search_criteria ) );
	}

	/**
	 * Process gwp_sum_matched_entries_values modifier
	 *
	 * Sums the numeric values of all specified fields from all active entries where the match_id property value of an entry equals the Merge Tag value ($value).
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string If succesfull return the specified matched entries count, otherwise return '' or error;
	 */
	public static function modifier_gwp_sum_matched_entries_values( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {

		$atts = shortcode_atts(
			array(
				'0'         => null,   // Contains the modifier (without attributes).
				'form_id'   => null,   // Which form-entries to search.
				'match_id'  => null,   // Entry property value to match the Merge Tag value with.
				'return_id' => null,   // field id to sum.
			),
			$modifier_atts
		);

		// Check if field is accessible.
		if ( self::get_access_level( (int) $atts['form_id'], $atts['return_id'] ) === 'forbidden' ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'access not allowed', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		// check required arguments.
		if ( empty( $atts['form_id'] ) || empty( $atts['match_id'] ) || empty( $atts['return_id'] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing required argument', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		// Prepare search criteria.
		$search_criteria = array();

		$search_criteria['status'] = 'active';

		$search_criteria['field_filters'][] = array(
			'key'      => $atts['match_id'],
			'operator' => 'is',
			'value'    => is_array( $raw_value ) ? $raw_value[ $merge_tag ] : $raw_value,
		);

		// Add additional search filters.
		if ( ! self::add_filters_to_search_criteria( $modifier_atts, $search_criteria, $form, $entry ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'incorrect search filters', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		$entry_ids = GFAPI::get_entry_ids( absint( $atts['form_id'] ), $search_criteria );

		if ( count( $entry_ids ) > 0 ) {
			global $wpdb;
			$entry_meta_table = GFFormsModel::get_entry_meta_table_name();

			// Prepare the %d arguments for each entryid, merge the sql arguments, prepare and execute the query.
			$placeholders = implode( ',', array_fill( 0, count( $entry_ids ), '%d' ) );
			$sql_args     = array_merge( array( absint( $atts['form_id'] ) ), $entry_ids, array( $atts['return_id'] ) );
			$sql          = $wpdb->prepare(
				"SELECT SUM(`meta_value`) as result_value FROM {$entry_meta_table} WHERE `form_id` IN(%s) AND `entry_id` IN({$placeholders}) AND `meta_key` IN(%s)",
				$sql_args
			);
			$result       = $wpdb->get_results( $sql, ARRAY_A );

			if ( $wpdb->last_error || $result === null ) {
				return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'query error', 'gravitywpadvancedmergetags' ), __METHOD__, $wpdb->last_error );
			}
		} else {
			return '0';
		}

		$result    = rgars( $result, '0/result_value', '0' );
		$precision = strlen( substr( strrchr( $result, '.' ), 1 ) );
		return number_format_i18n( $result, $precision );
	}

	/**
	 * Process gwp_remove_accents modifier.
	 *
	 * Mergetag-modifier: gwp_remove_accents -> Converts all accent characters to ASCII characters.
	 * https://developer.wordpress.org/reference/functions/remove_accents/
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  Return the value with all accent characters converted to ASCII characters.
	 */
	public static function modifier_gwp_remove_accents( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {
		return remove_accents( $value );
	}

	/**
	 * Process gwp_replace modifier.
	 *
	 * Mergetag-modifier: gwp_replace -> Modifier to add content before or behind value if the value is not empty.
	 *
	 * @since 1.1
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string  Return the value after replacement
	 */
	public static function modifier_gwp_replace( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {
		$atts = shortcode_atts(
			array(
				'0'              => null, // Contains the modifier (without attributes).
				'search'         => '', // Contains the modifier (without attributes).
				'replace'        => '', // Target case.
				'case_sensitive' => 'true',
			),
			$modifier_atts
		);

		$search  = self::replace_nested_merge_tags( $atts['search'], $form, $entry );
		$replace = self::replace_nested_merge_tags( $atts['replace'], $form, $entry );

		if ( $atts['case_sensitive'] === 'false' ) {
			$value = str_ireplace( $search, $replace, $value );
		} else {
			$value = str_replace( $search, $replace, $value );
		}

		return $value;
	}

	/**
	 * Process gwp_censor modifier.
	 *
	 * Mergetag-modifier: gwp_censor -> Modifier to censor blacklisted words using a specified character or a fixed-length string.
	 *
	 * @since 1.6
	 *
	 * @param string       $value The current merge tag value to be filtered.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag.
	 * @param array<mixed> $modifier_atts Array of the modifier attributes.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string Return the value after censorship
	 */
	public static function modifier_gwp_censor( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {
		$atts = shortcode_atts(
			array(
				'0'           => null,    // Contains the modifier (without attributes).
				'mask_char'   => '*',     // Character used for replacement.
				'ignore_case' => 'true',  // Perform case-insensitive search.
				'mask_string' => '',      // Fixed length string to use for replacement.
				'partial'     => 'false', // show first/last letter.
			),
			$modifier_atts
		);

		// Get disallowed keys from WordPress settings.
		$disallowed_keys = explode( "\n", get_option( 'disallowed_keys' ) );

		// Prepare regex pattern for each disallowed word.
		foreach ( $disallowed_keys as &$key ) {
			$key = trim( $key );
			if ( ! empty( $key ) ) {
				$key = preg_quote( $key, '/' );
			}
		}

		// Check if case-insensitive search is enabled.
		$regex_modifier = ( $atts['ignore_case'] == 'true' ) ? 'i' : '';

		$pattern = '/\b(' . implode( '|', $disallowed_keys ) . ')\b/' . $regex_modifier;

		// Perform replacement based on disallowed words.
		$value = preg_replace_callback(
			$pattern,
			function ( $matches ) use ( $atts ) {
				$word_length = strlen( $matches[0] );

				if ( $atts['partial'] === 'true' ) {
					$word_length = $word_length - 2;
				}

				// Use mask string if defined and not empty.
				if ( ! empty( $atts['mask_string'] ) ) {
					$return = $atts['mask_string'];
				} else {
					// Replace all characters in the word with the mask_char.
					$return = str_repeat( $atts['mask_char'], $word_length );
				}

				if ( $atts['partial'] === 'true' ) {
					$return = substr( $matches[0], 0, 1 ) . $return . substr( $matches[0], -1, 1 );
				}
				return $return;
			},
			$value
		);

		return $value;
	}


	/**
	 * Process gwp_append modifier.
	 *
	 * Mergetag-modifier: gwp_append -> Modifier to add content before or behind value if the value is not empty.
	 *
	 * @since 1.1
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string  Return the value with appended strings.
	 */
	public static function modifier_gwp_append( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {
		$atts = shortcode_atts(
			array(
				'0'      => null, // Contains the modifier (without attributes).
				'before' => '', // String to append before (prepend).
				'after'  => '', // String to append after.
			),
			$modifier_atts
		);

		$atts['before'] = self::replace_nested_merge_tags( $atts['before'], $form, $entry );
		$atts['after']  = self::replace_nested_merge_tags( $atts['after'], $form, $entry );

		if ( ! empty( $value ) ) {
			// Only append if value is not empty.
			return $atts['before'] . $value . $atts['after'];
		}

		return $value;
	}

	/**
	 * Process gwp_case modifier.
	 *
	 * Counts all active entries where the match_id property value of an entry equals the Merge Tag value ($value).
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string  If succesfull return the specified matched entries count, otherwise return $value.
	 */
	public static function modifier_gwp_case( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {

		$atts = shortcode_atts(
			array(
				'0'  => null, // Contains the modifier (without attributes).
				'to' => null, // Target case.
			),
			$modifier_atts
		);

		switch ( $atts['to'] ) {
			case 'upper':
				return mb_strtoupper( $value );
			case 'lower':
				return mb_strtolower( $value );
			case 'upper_first':
				return ucfirst( $value );
			case 'lower_first':
				return lcfirst( $value );
			case 'upper_words':
				return ucwords( $value );
		}

		return $value;
	}
	/**
	 * Process gwp_encrypt
	 *
	 * DOCSTRING
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string
	 */
	public static function modifier_gwp_encrypt( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {

		$atts  = shortcode_atts(
			array(
				'0' => null, // Contains the modifier (without attributes).
			),
			$modifier_atts
		);
		$value = GFCommon::openssl_encrypt( $value );
		// convert to urlsafe string.
		$value = rtrim( strtr( $value, '+/', '-_' ), '=' );

		return $value;
	}

	/**
	 * Process gwp_decrypt
	 *
	 * DOCSTRING
	 *
	 * @since 1.0
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 *
	 * @return string
	 */
	public static function modifier_gwp_decrypt( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format ) {

		$atts = shortcode_atts(
			array(
				'0' => null, // Contains the modifier (without attributes).
			),
			$modifier_atts
		);

		// replace urlsafe chars to regular base64 chars.
		$decoded_value = strtr( $value, '-_', '+/' );
		$value         = GFCommon::openssl_decrypt( $decoded_value );

		if ( $value === false ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'decryption failed', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts, true ) );
		}
		return $value;
	}

	/**
	 * Process gwp_date_format modifier.
	 *
	 * Formats a date or text field to a certain output.
	 *
	 * @since 1.2
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string  If succesfull return the specified matched entries count, otherwise return $value.
	 */
	public static function modifier_gwp_date_format( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {

		$atts = shortcode_atts(
			array(
				'format'         => 'Y-m-d H:i:s',
				'modify'         => '',
				'timezone'       => wp_timezone_string(),
				'field_timezone' => wp_timezone_string(),
				'field_format'   => false,
			),
			$modifier_atts
		);

		if ( $modifier_atts ['field_format'] ?? false ) {
			$field_format = $modifier_atts ['field_format'];
		} elseif ( $field->type === 'date' ) {
			switch ( $field->dateFormat ) {
				case 'mdy':
					$field_format = 'm/d/Y';
					break;
				case 'dmy':
					$field_format = 'd/m/Y';
					break;
				case 'dmy_dash':
					$field_format = 'd-m-Y';
					break;
				case 'dmy_dot':
					$field_format = 'd.m.Y';
					break;
				case 'ymd_slash':
					$field_format = 'Y/m/d';
					break;
				case 'ymd_dash':
					$field_format = 'Y-m-d';
					break;
				case 'ymd_dot':
					$field_format = 'Y.m.d';
					break;
				default:
					return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'unknown date field format', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
			}
		} else {
			// Field format unknown.
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'unknown date field format', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );

		}

		// Allow mergetags to be used as modifier string.
		if ( ! empty( $atts['modify'] ) ) {
			$atts['modify'] = self::replace_nested_merge_tags( $atts['modify'], $form, $entry );
		}

		return self::modify_format_datetime( $value, $field_format, $atts['format'], $atts['modify'], $atts['field_timezone'], $atts['timezone'] );
	}

	/**
	 * Generates a random token
	 *
	 * @since 1.2.5
	 *
	 * @param array<mixed> $mergetag_atts Array with attributes.
	 *
	 * @return string $token A unique id of a certain length.
	 */
	public static function mergetag_gwp_generate_token( $mergetag_atts ) {
		$atts = shortcode_atts(
			array(
				'charset' => 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz1234567890',
				'length'  => '16',
				'unique'  => 'false',
				'prefix'  => '',
				'postfix' => '',
				'retries' => '10',
			),
			$mergetag_atts
		);

		$charset        = $atts['charset'];
		$length         = intval( $atts['length'] );
		$unique         = $atts['unique'];
		$retries        = max( absint( $atts['retries'] ), 1 ); // Ensure minimum retries of 1.
		$token          = '';
		$charset_length = strlen( $charset );

		if ( ! is_int( $length ) || $length < 8 ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'Invalid length', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts, true ) );
		}

		for ( $i = 0; $i < $length; $i++ ) {
			$random_pos = random_int( 0, $charset_length - 1 );
			$token     .= $charset[ $random_pos ];
		}

		$token = $atts['prefix'] . $token . $atts['postfix'];

		if ( $unique === 'true' ) {
			if ( ! self::check_unique( $token ) ) {
				$mergetag_atts['counter'] = isset( $mergetag_atts['counter'] ) ? ++$mergetag_atts['counter'] : 1;
				if ( $mergetag_atts['counter'] >= $retries ) {
					return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'Could not generate a unique token.', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts, true ) );
				}
				$token = self::mergetag_gwp_generate_token( $mergetag_atts );
			}
		}

		return $token;
	}


	/**
	 * Retrieves the user roles based on a field value with user_login, user_email or user_id.
	 *
	 * @since 1.5
	 *
	 * @param string       $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string       $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<mixed> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field     $field The current field.
	 * @param mixed        $raw_value The raw value submitted for this field.
	 * @param string       $format Whether the text is formatted as html or text.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string
	 */
	public static function modifier_gwp_user_role( $value, $merge_tag, $modifier_atts, $field, $raw_value, $format, $form, $entry ) {

		if ( ! $value ) {
			return '';
		}

		// Check if allowed in form settings.
		$gwp_amt  = self::get_instance();
		$settings = $gwp_amt->get_form_settings( $form );
		if ( rgar( $settings, 'allow_gwp_user_role', '0' ) !== '1' ) {
			return $gwp_amt->gwp_error_handler( esc_html__( 'access not allowed', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		$atts = shortcode_atts(
			array(
				'match'     => 'login',
				'subset'    => '',
				'separator' => ',',
				'return'    => 'all',
				'unknown'   => false,
			),
			$modifier_atts
		);

		$user = get_user_by( $atts['match'], $value );
		if ( ! $user ) {
			if ( $atts['unknown'] === false ) {
				return $gwp_amt->gwp_error_handler( esc_html__( 'unknown user', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts, true ) );
			} else {
				return $atts['unknown'];
			}
		}

		if ( $atts['subset'] ) {
			// return only if the user roles are present in the subset and also in order as listed.
			$subset = explode( ',', $atts['subset'] );
			$slugs  = array_intersect( $subset, $user->roles );
		} else {
			// no subset, return any role the user may have.
			$slugs = $user->roles;
		}

		if ( $atts['return'] === 'all' ) {
			return implode( $atts['separator'], $slugs );
		}

		if ( absint( $atts['return'] ) ) {
			$slugs = array_slice( $slugs, 0, (int) $atts['return'], true );
			return implode( $atts['separator'], $slugs );
		}

		return $gwp_amt->gwp_error_handler( esc_html__( 'invalid return parameter', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $atts, true ) );
	}

	/**
	 * Helper function - Check if value is unique by running a search query in the DB.
	 *
	 * @since 1.2.5
	 *
	 * @param string $value The value to search.
	 *
	 * @return bool $is_unique.
	 */
	public static function check_unique( $value ) {

		global $wpdb;

		// Get entry meta table name.
		$entry_meta_table        = GFFormsModel::get_entry_meta_table_name();
		$draft_submissions_table = GFFormsModel::get_draft_submissions_table_name();

		$queries = array();

		$queries['entries'] = $wpdb->prepare( "SELECT COUNT(meta_value) FROM {$entry_meta_table} WHERE meta_value LIKE %s", '%' . $value . '%' );

		$queries['draft_submissions'] = $wpdb->prepare( "SELECT COUNT(submission) FROM {$draft_submissions_table} WHERE submission LIKE %s", '%' . $value . '%' );

		/**
		 * If result found, value is not unique. Return false.
		 * If no results found, value is unique. Return true.
		 * If null, query is false. Skip query.
		 */
		foreach ( $queries as $query ) {

			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			if ( $wpdb->get_var( $query ) > 0 ) {
				return false;
			}
		}
			return true;
	}


	/**
	 * Helper function - Add additional field filters to a search_criteria array from an attribute array. Filters will be added to $search_criteria['field_filters'][].
	 *
	 * Adds all subsequent pairs of filter1, operator1, value1, etc.
	 *
	 * @since 1.0.2
	 *
	 * @param array<mixed> $attributes Parsed array of mergetag attributes.
	 * @param array<mixed> $search_criteria Array, passed as reference.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return bool Returns true if successfull, false if not.
	 */
	public static function add_filters_to_search_criteria( array $attributes, array &$search_criteria, $form, $entry ) {
		/* shortcode operator => GF_Query operator */
		$supported_operators = array(
			'is'                 => 'is',
			'isnot'              => 'isnot',
			'contains'           => 'contains',
			'greater_than'       => '>',
			'less_than'          => '<',
			'greater_than_or_is' => '>=',
			'less_than_or_is'    => '<=',
		);

		// Additional filters.
		$i       = 1;
		$success = true;
		while ( ! empty( $attributes[ 'filter' . $i ] ) ) {
			$operator = $supported_operators[ $attributes[ 'operator' . $i ] ] ?? '';
			if ( ! empty( $operator ) && isset( $attributes[ 'value' . $i ] ) ) {
				$search_criteria['field_filters'][] = array(
					'key'      => $attributes[ 'filter' . $i ],
					'operator' => $operator,
					'value'    => self::replace_nested_merge_tags( $attributes[ 'value' . $i ], $form, $entry ),
				);
			} else {
				$success = false;
			}
			++$i;
		}
		return $success;
	}

	/**
	 * Helper function - Replace nested merge tags like *|user:user_id|* with {user:user_id} and have them replaced via GFCommon::replace_variables() to support 3th party merge tags.
	 *
	 * @since 1.2
	 *
	 * @param string       $text Text containing one or more merge tags.
	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string
	 */
	public static function replace_nested_merge_tags( $text, $form, $entry ) {
		$search_pattern  = '/\*\|(.*?)\|\*/';
		$replace_pattern = '{\1}';
		$text            = preg_replace( $search_pattern, $replace_pattern, $text );
		$text            = GFCommon::replace_variables( $text, $form, $entry, false, false, false, 'text' );
		return $text;
	}
	/**
	 * Process get_matched_entries_values modifier
	 *
	 * Gets multiple values from active entries where the match_id property value of an entry equals the Merge Tag value ($value). Returns the values in html format.
	 *
	 * Example:
	 * {Referencefield:5:get_matched_entries_values form_id=6 match_id=22 wrap_tag=table wrap_class=jurdy row_tag=tr col_tag=td value=caption_lastword col_class=lof3_ col_class_postfix=value options=created_by:col_average filter1=usrreturn_id1=27.1 return_id2=2
	 *
	 * @since 1.0.2
	 *
	 * @param string               $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string               $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields).
	 * @param array<string,string> $modifier_atts Array of the modifier attributes, where $modifier_atts['0'] contains the modifier string.
	 * @param GF_Field             $field The current field.
	 * @param mixed                $raw_value The raw value submitted for this field.
	 * @param string               $format Whether the text is formatted as html or text.
	 *
	 * @return string If succesfull return the specified matched entries count, otherwise return '' or error;
	 */
	public static function modifier_gwp_get_matched_entries_values( $value, $merge_tag, $modifier_atts, $field, $raw_value, &$format ) {

		// check required arguments.
		if ( empty( $modifier_atts['form_id'] ) || empty( $modifier_atts['match_id'] ) ) {
			return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'missing arguments', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) );
		}

		$search_form_id           = $modifier_atts['form_id'];
		$match_id                 = $modifier_atts['match_id'];
		$wrap_tag                 = $modifier_atts['wrap_tag'] ?? '';
		$wrap_class               = $modifier_atts['wrap_class'] ?? '';
		$row_separator            = esc_html( $modifier_atts['row_separator'] ?? '' );
		$row_tag                  = $modifier_atts['row_tag'] ?? '';
		$row_class                = $modifier_atts['row_class'] ?? '';
		$col_tag                  = $modifier_atts['col_tag'] ?? '';
		$col_separator            = esc_html( $modifier_atts['col_separator'] ?? '' );
		$col_class                = $modifier_atts['col_class'] ?? '';
		$col_class_postfix_source = $modifier_atts['col_class_postfix'] ?? '';
		$options                  = $modifier_atts['options'] ?? '';
		$no_results               = $modifier_atts['no_results'] ?? '';
		$return_format            = $modifier_atts['return_format'] ?? 'html';

		// Prepare search criteria.
		$search_criteria = self::atts_to_search_filter( $modifier_atts );
		$sorting         = self::atts_to_sorting( $modifier_atts );

		$search_criteria['field_filters'][] = array(
			'key'      => $match_id,
			'operator' => 'is',
			'value'    => is_array( $raw_value ) ? $raw_value[ $merge_tag ] : $raw_value,
		);

		if ( strpos( $options, 'filtercurrentuser' ) !== false ) {
			$search_criteria['field_filters'][] = array(
				'key'   => 'created_by',
				'value' => strval( get_current_user_id() ),
			);
		}

		// get all entries matching criteria.
		$entry_array = GFAPI::get_entries( absint( $search_form_id ), $search_criteria, $sorting );

		if ( ! empty( $entry_array ) ) {

			// Handle multiselect format.
			if ( $return_format === 'multiselect' ) {
				$multiselect_array = array();

				foreach ( $entry_array as $key => $entry ) {
					$i = 1;
					while ( isset( $modifier_atts[ 'return_id' . $i ] ) ) {
						$value_field_id = $modifier_atts[ 'return_id' . $i ];
						// Prevent mergetag / shortcode injection.
						$entry_value = self::get_allowed_field_value( absint( $search_form_id ), $entry, $value_field_id );
						self::gf_replace_field_variable_part3( $entry_value, $field, $url_encode, $esc_html, $format, $nl2br );
						$multiselect_array[] = $entry_value;
						++$i;
					}
				}
				$return_value = count( $multiselect_array ) ? trim( json_encode( array_values( array_unique( $multiselect_array ) ) ), '"' ) : '';
				if ( $return_value === false ) {
					return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'multiselect failed', 'gravitywpadvancedmergetags' ), __METHOD__, print_r( $modifier_atts, true ) . 'failed encoding' . print_r( $multiselect_array, true ) );
				}
				$format = 'text';
				return $return_value;
			}

			// Handle other formats.
			$col_values = array();

			$value  = '';
			$tmpstr = '';
			// initialize here,because these are used outside the foreach loop.
			$key          = 0;
			$value_number = -1;

			// generate html for the entries.
			foreach ( $entry_array as $key => $entry ) {
				// initialize, used for row _average calculation.
				$row_total           = 0;
				$row_value           = 0;
				$row_average_divider = 0;

				// initialize, used for generation of css_classes.
				$value_number = -1;

				// generate html opening tags for each entry.
				$tmpstr .= ( ! empty( $modifier_atts['wrap_tag'] ) ) ? "<$wrap_tag class='$wrap_class gwp-entry gwp-entry-{$key}'>" : '';
				$tmpstr .= ( ! empty( $row_tag ) ) ? "<$row_tag class='$row_class gwp-entry-inner gwp-entry-inner-{$key}'>" : '';

				// generate html for each fieldvalue.
				$i = 1;
				while ( isset( $modifier_atts[ 'return_id' . $i ] ) ) {
					$text_value = '';
					$css_value  = '';
					++$value_number;
					$value_field_id = $modifier_atts[ 'return_id' . $i ];

					// set value from selected source.
					$value_source = $modifier_atts[ 'return_value' . $i ] ?? ( $modifier_atts['return_value'] ?? '' );

					if ( $value_source === '' && ! in_array( 'filter1', $modifier_atts, true ) ) {
						// fallback to value, value1, value2, etc. for backwards compatiblity reasons, but only when no filter is set, which uses value1 also.
						$value_source = $modifier_atts[ 'value' . $i ] ?? ( $modifier_atts['value'] ?? '' );
					}
					if ( $value_source === '' ) {
						$value_source = 'value'; // default.
					}
					switch ( $value_source ) {
						case 'value':
							$text_value = self::get_allowed_field_value( absint( $search_form_id ), $entry, $value_field_id );
							$row_value  = $text_value;
							break;
						case 'caption_firstword':
							$words      = explode( ' ', trim( GFAPI::get_field( absint( $search_form_id ), $value_field_id )->label ) );
							$text_value = $words[0];
							break;
						case 'caption_lastword':
							$words      = explode( ' ', trim( GFAPI::get_field( absint( $search_form_id ), $value_field_id )->label ) );
							$text_value = array_pop( $words );
							break;
						default:
							$text_value = $value_source;
					}

					// set css postfix value from selected source.
					switch ( $col_class_postfix_source ) {
						case 'value':
							$css_value = self::get_allowed_field_value( absint( $search_form_id ), $entry, $value_field_id );
							$row_value = $css_value;
							break;
						case 'caption_firstword':
							$words     = explode( ' ', trim( GFAPI::get_field( absint( $search_form_id ), $value_field_id )->label ) );
							$css_value = $words[0];
							break;
						case 'caption_lastword':
							$words     = explode( ' ', trim( GFAPI::get_field( absint( $search_form_id ), $value_field_id )->label ) );
							$css_value = array_pop( $words );
							break;
						default:
							$css_value = $col_class_postfix_source;
					}

					// calculate row average, executed if one or more averages must be shown.
					if ( strpos( $options, 'average' ) !== false ) {
						// initialize if needed.
						if ( empty( $col_values[ $i ] ) ) {
							$col_values[ $i ]['total']   = 0;
							$col_values[ $i ]['divider'] = 0;
						}

						if ( is_numeric( $row_value ) ) {
							$row_total                   += $row_value;
							$row_average_divider         += 1;
							$col_values[ $i ]['total']   += $row_value;
							$col_values[ $i ]['divider'] += 1;
						} else {
							$col_values[ $i ]['total']   += 0;
							$col_values[ $i ]['divider'] += 0;
						}
					}

					if ( empty( $col_tag ) ) {
						// add a textual row separator if not the last row. Note: text separator is not supported for average and createdby columns.
						$tmp_col_sep = isset( $modifier_atts[ 'return_id' . strval( $i ) ] ) ? $col_separator : '';
						$tmpstr     .= $text_value . $tmp_col_sep;
					} else {
						// build the value html.
						$tmpstr .= "<$col_tag class='{$col_class}{$css_value} gwp-value gwp-value-{$value_number}'>$text_value</$col_tag>";
					}

					++$i;
				}

				// optionally add display_name of entry creator.
				if ( strpos( $options, 'created_by' ) !== false ) {

					++$value_number;

					$user_data               = get_userdata( absint( self::get_allowed_field_value( absint( $search_form_id ), $entry, 'created_by' ) ) );
					$created_by_display_name = $user_data ? $user_data->display_name : '';
					$tmpstr                 .= "<$col_tag class='{$col_class}created_by gwp-value gwp-value-$value_number'>$created_by_display_name</$col_tag>";
				}

				if ( strpos( $options, 'row_average' ) !== false ) {
					++$value_number;
					if ( $row_average_divider !== 0 ) {
						$row_average = $row_total / $row_average_divider;
						$tmpstr     .= "<$col_tag class='$col_class" . round( $row_average ) . " gwp-row-average gwp-value gwp-value-$value_number'>" . number_format( $row_average, 1, ',', '' ) . "</$col_tag>";
					}
				}

				// Add a textual row separator if row tag is empty and this is not the last row. Note: text separator is not supported for average rows.
				if ( empty( $row_tag ) && ! empty( $row_separator ) && $key + 1 < count( $entry_array ) ) {
					$tmpstr .= $row_separator;
				}

				// close the opening tags.
				$tmpstr .= ( ! empty( $row_tag ) ) ? "</$row_tag>" : '';
				$tmpstr .= ( ! empty( $wrap_tag ) ) ? "</$wrap_tag>" : '';
			}

			// generate extra col average html.
			if ( strpos( $options, 'col_average' ) !== false ) {
				++$key;
				$tmpstr .= ( ! empty( $wrap_tag ) ) ? "<$wrap_tag class='$wrap_class gwp-col-average gwp-entry gwp-entry-{$key}'>" : '';
				$tmpstr .= ( ! empty( $row_tag ) ) ? "<$row_tag class='$row_class gwp-col-average-inner gwp-entry-inner gwp-entry-inner-{$key}'>" : '';

				// initialize.
				$total_col_average            = array();
				$total_col_average['total']   = 0;
				$total_col_average['divider'] = 0;

				// calculate.
				foreach ( $col_values as $col_key => $col_value ) {
					if ( ! empty( $col_value['divider'] ) ) {
						$col_average                   = $col_value['total'] / $col_value['divider'];
						$total_col_average['total']   += $col_average;
						$total_col_average['divider'] += 1;
						$col_average_rounded           = round( $col_average );
						$col_average                   = number_format( $col_average, 1, ',', '' );
					} else {
						$col_average_rounded = '';
						$col_average         = '';
					}

					// build column HTML for average row.
					$tmpstr .= "<$col_tag class='$col_class$col_average_rounded gwp-value gwp-value-$col_key'>$col_average</$col_tag>";
				}

				// add extra collumns to col average row.
				if ( strpos( $options, 'created_by' ) !== false ) {
					$tmpstr .= "<$col_tag class='{$col_class}created_by gwp-value gwp-value-$value_number'></$col_tag>";
				}

				if ( strpos( $options, 'row_average' ) !== false ) {
					$total_col_average_value         = '';
					$total_col_average_value_rounded = '';

					if ( ! empty( $total_col_average['divider'] ) && strpos( $options, 'total_col_average' ) !== false ) {
						$total_col_average_value         = $total_col_average['total'] / $total_col_average['divider'];
						$total_col_average_value_rounded = round( $total_col_average_value );
						$total_col_average_value         = number_format( $total_col_average_value, 1, ',', '' );
					}

					$tmpstr .= "<$col_tag class='$col_class{$total_col_average_value_rounded} gwp-row-average gwp-value gwp-value-$value_number'>$total_col_average_value</$col_tag>";
				}

				// Close the opening tags.
				$tmpstr .= ( ! empty( $row_tag ) ) ? "</$row_tag>" : '';
				$tmpstr .= ( ! empty( $wrap_tag ) ) ? "</$wrap_tag>" : '';

			}
			// Only output html that is allowed for posts.
			$value = wp_kses_post( $tmpstr );
		} else {
			// No matched entries found.
			return $no_results;

		}

		return $value;
	}

	/**
	 * Helper function: Converts an attributes array to a search filter for GFAPI::get_entries().
	 *
	 * @param array<string,string> $atts array of attributes.
	 * @return array<mixed>
	 */
	public static function atts_to_search_filter( array $atts ) {
		// Prepare search criteria.
		$search_criteria = array();

		$search_criteria['status'] = 'active';

		// Additional field filters.
		if ( isset( $atts['filter1'] ) ) {
			/* shortcode operator => GF_Query operator */
			$supported_operators = array(
				'is'                 => 'is',
				'isnot'              => 'isnot',
				'contains'           => 'contains',
				'greater_than'       => '>',
				'less_than'          => '<',
				'greater_than_or_is' => '>=',
				'less_than_or_is'    => '<=',
			);

			$i = 1;
			while ( ! empty( $atts[ 'filter' . $i ] ) ) {
				$operator = $supported_operators[ $atts[ 'operator' . $i ] ] ?? '';
				if ( ! empty( $operator ) && isset( $atts[ 'value' . $i ] ) ) {
					$search_criteria['field_filters'][] = array(
						'key'      => $atts[ 'filter' . $i ],
						'operator' => $operator,
						'value'    => $atts[ 'value' . $i ],
					);
				}
				++$i;
			}
		}

		return $search_criteria;
	}

	/**
	 * Helper function: Converts an attributes array to a sort_order for GFAPI::get_entries
	 *
	 * @param array<string,string> $atts array of attributes.
	 * @return array<string,mixed>
	 */
	public static function atts_to_sorting( array $atts ) {
		// Sort-order.
		if ( isset( $atts['sort_order'] ) ) {
			switch ( strtolower( $atts['sort_order'] ) ) {
				case 'asc':
					$arg_sort_order = 'ASC';
					break;
				case 'rand':
					$arg_sort_order = 'RAND';
					break;
				default:
					$arg_sort_order = 'DESC';
			}
		} else {
			$arg_sort_order = 'DESC';
		}

		return array(
			'key'        => 'id',
			'direction'  => $arg_sort_order,
			'is_numeric' => true,
		);
	}

	/**
	 * Helper function: Converts an attributes array to a array of additional modifier strings.
	 *
	 * @param array<string,string> $atts array of attributes.
	 * @return array<mixed>
	 */
	public static function atts_to_modifiers( $atts ) {
		if ( is_array( $atts ) && isset( $atts['modifier1'] ) ) {
			$modifiers = array();
			$i         = 1;

			while ( ! empty( $atts[ 'modifier' . $i ] ) ) {
				$modifiers[] = $atts[ 'modifier' . $i ];
				++$i;
			}
			return $modifiers;
		}
		return array();
	}

	/**
	 * Helper function: Retrieve a field value from a certain form entry if allowed in the form settings.
	 *
	 * @param int          $form_id Form id.
	 * @param array<mixed> $entry Entry to retrieve the value from.
	 * @param int|string   $field_id Field id or meta key to retrieve the value from.
	 * @return string
	 */
	public static function get_allowed_field_value( $form_id, $entry, $field_id ) {
		$access_level = self::get_access_level( $form_id, $field_id );
		// Return the value if allowed.
		switch ( $access_level ) {
			case 'logged_in':
				if ( is_user_logged_in() ) {
					return rgar( $entry, $field_id );
				}
				break;
			case 'everyone':
				return rgar( $entry, $field_id );
		}

		// Return error.
		return gravitywp_advanced_merge_tags()->gwp_error_handler( esc_html__( 'access not allowed', 'gravitywpadvancedmergetags' ), __METHOD__, "$form_id: $field_id" );
	}

	/**
	 * Helper function: Retrieve the access level as specified in the form settings. Returns 'forbidden' if no level was set.
	 *
	 * @param int        $form_id Form id.
	 * @param int|string $field_id Field id or meta key to retrieve the value from.
	 * @return string
	 */
	public static function get_access_level( $form_id, $field_id ) {
		// Get the instance of the class, because mergetag functions are called statically.
		$gwp_amt = self::get_instance();

		// Get form_access settings if not already cached.
		if ( ! isset( $gwp_amt->_access_settings[ $form_id ] ) ) {
			$form            = GFAPI::get_form( $form_id );
			$access_settings = rgar( $form, 'gravitywp-advanced-merge-tags' );

			$gwp_amt->_access_settings[ $form_id ]['access_form_level'] = rgar( $access_settings, 'access_form_level' );

			if ( $gwp_amt->_access_settings[ $form_id ]['access_form_level'] === 'fields' ) {
				$gwp_amt->_access_settings[ $form_id ]['access_field_level'] = isset( $access_settings['access_field_level'] ) ? array_column( $access_settings['access_field_level'], 'value', 'key' ) : array();
			}
		}

		// Determine access level for this form or field.
		if ( $gwp_amt->_access_settings[ $form_id ]['access_form_level'] === 'fields' ) {
			return rgars( $gwp_amt->_access_settings, $form_id . '/access_field_level/' . $field_id );
		} // else:
		return rgars( $gwp_amt->_access_settings, $form_id . '/access_form_level', 'forbidden' );
	}

	/**
	 * Function: filter_get_calculation_value.
	 * This converts merge tag modifiers in calculations.
	 * Note: each of this conversions should match the output of the frontend function.
	 *
	 * @author GravityWP
	 * @since v1.3
	 *
	 * @param string       $value The field value after the merge tag was processed.
	 * @param string       $input_id The field or input ID from the merge tag.
	 * @param string       $modifier The modifier from the merge tag e.g. value.
	 * @param GF_Field     $field The calculation field currently being processed.

	 * @param array<mixed> $form The form object.
	 * @param array<mixed> $entry The entry array.
	 *
	 * @return string
	 */
	public function filter_get_calculation_value( $value, $input_id, $modifier, $field, $form, $entry ) {

		// convert gwp_word_count.
		if ( strpos( $modifier, 'gwp_word_count' ) !== false ) {
			$mergetag_field = GFAPI::get_field( $form, $input_id );
			$mergetag_value = rgar( $entry, $input_id );
			if ( is_null( $mergetag_value ) ) {
				gravitywp_advanced_merge_tags()->log_error( __METHOD__ . "(): $input_id value not found in entry." );
			}
			// for now no attributes are supported in calculations.
			return self::modifier_gwp_word_count( $mergetag_value, $input_id, array(), $mergetag_field, $mergetag_value, 'text' );
		}

		// convert gwp_substring.
		if ( strpos( $modifier, 'gwp_substring' ) !== false ) {
			$mergetag_field = GFAPI::get_field( $form, $input_id );
			$mergetag_value = rgar( $entry, $input_id );
			if ( is_null( $mergetag_value ) ) {
				gravitywp_advanced_merge_tags()->log_error( __METHOD__ . "(): $input_id value not found in entry." );
			}

			$modifier_atts = self::parse_mergetag_atts( $modifier );
			return self::modifier_gwp_substring( $mergetag_value, $input_id, $modifier_atts, $mergetag_field, $mergetag_value, 'text' );
		}

		return $value;
	}

	/**
	 * Replace mergetags in post content.
	 *
	 * @param $string $content The content.
	 * @return string
	 */
	public static function replace_post_content_merge_tags( $content ) {
		return GFCommon::replace_variables_prepopulate( $content );
	}
}


/**
 * Returns an instance of the GravityWP_Advanced_Merge_Tags class
 *
 * @since  1.0
 * @return GravityWP_Advanced_Merge_Tags An instance of the GravityWP_Advanced_Merge_Tags class
 */
function gravitywp_advanced_merge_tags() { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed
	return GravityWP_Advanced_Merge_Tags::get_instance();
}
