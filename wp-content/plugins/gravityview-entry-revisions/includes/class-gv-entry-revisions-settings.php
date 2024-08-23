<?php

if ( ! class_exists( 'GFForms' ) ) {
	return;
}

GFForms::include_addon_framework();

class GV_Entry_Revisions_Settings extends GFAddOn {

	/**
	 * @var string Not used.
	 */
	protected $_min_gravityforms_version = '2.3.3.9';

	/**
	 * @var string Not used.
	 */
	protected $_slug = 'gk-gravityrevisions';

	/**
	 * @var string Not used.
	 */
	protected $_path = 'gk-gravityrevisions/gk-gravityrevisions.php';

	/**
	 * @var string Not used.
	 */
	protected $_full_path = __FILE__;

	/**
	 * @var string Not used.
	 */
	protected $_title = 'GravityRevisions';

	/**
	 * @var string Not used.
	 */
	protected $_short_title = 'GravityRevisions';

	private static $_instance = null;

	/** @var string The setting name for default Inline Edit support. */
	const GRAVITYEDIT_GLOBAL_SETTING = 'inline_edit_create_revisions';

	/** @var int The default value for the global Inline Edit setting. */
	const GRAVITYEDIT_GLOBAL_SETTING_DEFAULT = 1;

	/** @var string The setting name for per-form Inline Edit support. */
	const GRAVITYEDIT_FORM_SETTING = 'inline_edit_create_revisions_per_form';

	public function __construct() {

		if ( self::$_instance ) {
			return self::$_instance;
		}

		$this->_short_title = esc_html__( 'GravityRevisions', 'gk-gravityrevisions' );

		add_filter( 'gform_form_settings_fields', [ $this, 'add_form_settings_fields' ], 20 );

		add_filter( 'gk/foundation/settings/data/plugins', [ $this, 'update_gravityedit_foundation_settings' ], 20 );

		parent::__construct();
	}

	/**
	 * Adds a per-form option to enable or disable GravityRevisions for Inline Edit changes.
	 *
	 * @since 1.2
	 *
	 * @param array $fields Form settings fields.
	 *
	 * @return array
	 */
	public function add_form_settings_fields( $fields ) {
		$gravityedit_setting = null;

		if ( class_exists( 'GravityKitFoundation' ) && GravityKitFoundation::settings() ) {
			$gravityedit_setting = GravityKitFoundation::settings()->get_plugin_setting( 'gravityedit', self::GRAVITYEDIT_GLOBAL_SETTING );
		}

		// If Inline Edit isn't active, don't remove the form setting if it has already been set.
		if ( ! self::is_gravityedit_activated() && ! is_null( $gravityedit_setting ) ) {
			$fields['form_options']['fields'][] = [
				'name'  => self::GRAVITYEDIT_FORM_SETTING,
				'type'  => 'hidden',
				'value' => (string) $gravityedit_setting,
			];

			return $fields;
		}

		$fields['form_options']['fields'][] = [
			'name'          => self::GRAVITYEDIT_FORM_SETTING,
			'type'          => 'radio',
			'label'         => esc_html__( 'Inline Edit Behavior', 'gk-gravityrevisions' ),
			'description'   => '<p class="clear">' . esc_html__( 'Should edits made using the Inline Edit plugin create revisions?', 'gk-gravityrevisions' ) . '</p>',
			'default_value' => (string) $gravityedit_setting ?? self::GRAVITYEDIT_GLOBAL_SETTING_DEFAULT,
			'dependency'    => [
				'live'   => true,
				'fields' => [
					[
						'field'  => 'gv_inline_edit_enable',
						'values' => [ '1', true ],
					],
				],
			],
			'choices'       => [
				[ 'label' => esc_html__( 'Add revisions for edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '1' ],
				[ 'label' => esc_html__( 'Ignore edits made using Inline Edit', 'gk-gravityrevisions' ), 'value' => '0' ],
			],
		];

		return $fields;
	}

	/**
	 * Returns TRUE if the settings "Save" button was pressed
	 *
	 * @since 1.0.3 Fixes conflict with Import Entries plugin
	 *
	 * @return bool True: Settings form is being saved and the GravityRevisions setting is in the posted values (form settings)
	 */
	public function is_save_postback() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return ! rgempty( 'gform-settings-save' ) && ( isset( $_POST['gform_settings_save_nonce'] ) || isset( $_POST['_gravityview-entry-revisions_save_settings_nonce'] ) );
	}

	/**
	 * Get the one instance of the object
	 *
	 * @since 1.0
	 *
	 * @return GV_Entry_Revisions_Settings
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {

			self::$_instance = new self();

			GFAddOn::register( __CLASS__ );
		}

		return self::$_instance;
	}

	/**
	 * Adds a revisions setting to GravityEdit settings.
	 *
	 * @since 1.2.11
	 *
	 * @param array $plugins_data Plugins data.
	 *
	 * @return array $plugins_data
	 */
	function update_gravityedit_foundation_settings( $plugins_data ) {
		if ( ! class_exists( 'GravityKitFoundation' ) || ! GravityKitFoundation::settings() || ! self::is_gravityedit_activated() ) {
			return $plugins_data;
		}

		$plugin_id = 'gravityedit';

		$default_settings = [
			self::GRAVITYEDIT_GLOBAL_SETTING => 1,
		];

		if ( ! isset( $plugins_data[ $plugin_id ] ) || empty( $plugins_data[ $plugin_id ]['sections'][0]['settings'] ) ) {
			return $plugins_data;
		}

		$plugin_settings = GravityKitFoundation::settings()->get_plugin_settings( $plugin_id );

		if ( empty( $plugin_settings ) ) {
			$plugin_settings = $this->migrate_legacy_settings() ?? $default_settings;
		}

		$plugins_data[ $plugin_id ]['sections'][0]['settings'][] = [
			'id'            => self::GRAVITYEDIT_GLOBAL_SETTING,
			'type'          => 'checkbox',
			'title'         => esc_html__( 'Save Revisions', 'gk-gravityrevisions' ),
			'description'   => esc_html__( 'Should revisions be saved for all inline edits?', 'gk-gravityrevisions' ) . '<br><br>' . esc_html__( 'Note: This is the global default. You may override this setting in a form&rsquo;s Settings page.', 'gk-gravityrevisions' ),
			'default_value' => $default_settings[ self::GRAVITYEDIT_GLOBAL_SETTING ],
			'value'         => $plugin_settings[ self::GRAVITYEDIT_GLOBAL_SETTING ] ?? $default_settings[ self::GRAVITYEDIT_GLOBAL_SETTING ],
		];

		return $plugins_data;
	}

	/**
	 * Migrates GravityRevisions <1.2.11 settings from the GF settings framework to Foundation.
	 *
	 * @since 1.2.11
	 *
	 * @return array
	 */
	public function migrate_legacy_settings() {
		$legacy_settings = get_option( 'gravityformsaddon_' . $this->_slug . '_settings' );

		if ( empty( $legacy_settings ) ) {
			return [];
		}

		$plugin_settings = [
			self::GRAVITYEDIT_GLOBAL_SETTING => (int) $legacy_settings[ self::GRAVITYEDIT_GLOBAL_SETTING ] ?? 1,
		];

		GravityKitFoundation::settings()->save_plugin_setting( 'gravityedit', self::GRAVITYEDIT_GLOBAL_SETTING, (int) $legacy_settings[ self::GRAVITYEDIT_GLOBAL_SETTING ] ?? self::GRAVITYEDIT_GLOBAL_SETTING_DEFAULT );

		return $plugin_settings;
	}

	/**
	 * Returns whether the GravityEdit plugin is activated.
	 *
	 * @since 1.2
	 * @since 1.2.11 Converted to static method.
	 *
	 * @return bool
	 */
	public static function is_gravityedit_activated() {
		return defined( 'GRAVITYVIEW_INLINE_VERSION' ) || defined( 'GRAVITYEDIT_VERSION' );
	}

	/**
	 * Don't show the uninstall form
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function render_uninstall() {}

}
