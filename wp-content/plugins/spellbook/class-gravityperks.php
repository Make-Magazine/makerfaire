<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

class GravityPerks {

	public static $version = SPELLBOOK_VERSION;
	public static $tooltip_template = '<h6>%s</h6> %s';

	private static $basename;
	private static $slug = 'gravityperks';
	private static $min_gravity_forms_version = '2.2';
	private static $min_wp_version = '4.8';
	/**
	 * @var GWAPI
	 */
	private static $api;

	/**
	 * TODO: review...
	 *
	 * Need to store a modified version of the form object based the on the gform_admin_pre_render hook for use
	 * in perk hooks.
	 *
	 * Example usage: GWPreventSubmit::add_form_setting()
	 *
	 * @var array
	 */
	public static $form;

	/**
	 * Set to true by the GWPerk class when any perk enqueues a form setting via the
	 * GWPerk::enqueue_form_setting() function
	 *
	 * @var bool
	 */
	public static $has_form_settings;

	/**
	 * Set to true by the GWPerk class when any perk enqueues a field setting via the
	 * GWPerk::enqueue_field_setting() function.
	 *
	 * @var bool
	 */
	private static $has_field_settings;

	/**
	 * When displaying a plugin row message, the first message display will also output a small style to fix the bottom
	 * border styling issue which WP handles for plugins with updates, but not with notices.
	 *
	 * @see self::display_plugin_row_message()
	 *
	 * @var mixed
	 *
	 */
	private static $plugin_row_styled;

	// CACHE VARIABLES //

	private static $installed_plugins;

	// UPDATER //

	/**
	 * Used to determine if the plugin being installed/updated is a Gravity Wiz product and one that should trigger
	 * a link back to Spellbook. Used to work around not having some of the plugin data available in the arguments.
	 *
	 * @var bool
	 */
	private static $handled_spellbook_product;


	// INITIALIZE //

	public static function init() {

		self::define_constants();
		self::$basename = plugin_basename( __FILE__ );

		require_once( self::get_base_path() . '/includes/functions.php' );
		require_once( self::get_base_path() . '/includes/deprecated.php' );
		require_once( self::get_base_path() . '/includes/class-gw-telemetry.php' );

		// Register REST API routes
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_routes' ) );

		load_plugin_textdomain( 'gravityperks', false, basename( dirname( __FILE__ ) ) . '/languages/' );

		if ( ! self::is_gravity_forms_supported() ) {
			self::handle_error( 'gravity_forms_required' );
		} elseif ( ! self::is_wp_supported() ) {
			self::handle_error( 'wp_required' );
		}

		self::maybe_setup();
		self::load_api();

		// Initialize telemetry
		new GW_Telemetry();

		self::register_scripts();

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			global $pagenow;

			self::include_admin_files();

			// show Perk item in GF menu
			add_filter( 'gform_addon_navigation', array( 'GWPerks', 'add_menu_item' ) );

			// show various plugin messages after the plugin row
			add_action( 'after_plugin_row_spellbook/spellbook.php', array( 'GWPerks', 'after_plugin_row' ), 10, 2 );
			add_action( 'after_plugin_row', array( 'GWPerks', 'after_product_plugin_row' ), 10, 2 );

			if ( self::is_gravity_perks_page() ) {

				if ( rgget( 'view' ) ) {
					require_once( self::get_base_path() . '/admin/manage_perks.php' );
					GWPerksPage::load_perk_settings();
				}

			}

			if ( self::is_gf_version_lte( '2.5-beta-1' ) && self::is_gravity_page() ) {

				add_action( 'gform_editor_js', array( 'GWPerks', 'add_form_editor_tabs' ), 1 );

			}
		} elseif ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

			add_action( 'wp_ajax_gwp_manage_perk', array( 'GWPerks', 'manage_perk' ) );
			add_action( 'wp_ajax_gwp_dismiss_pointers', array( __CLASS__, 'dismiss_pointers' ) );

		}

		add_filter( 'admin_body_class', array( __CLASS__, 'add_helper_body_classes' ) );

		add_action( 'gform_logging_supported', array( __CLASS__, 'enable_logging_support' ) );

		// Add Perks tab to form editor.
		add_action( 'gform_field_settings_tabs', array( __CLASS__, 'add_perks_tab' ) );
		add_action( 'gform_field_settings_tab_content_gravity-perks', array( __CLASS__, 'add_perks_tab_settings' ) );

		add_action( 'gform_field_standard_settings', array( __CLASS__, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_appearance_settings', array( __CLASS__, 'dynamic_setting_actions' ), 10, 2 );
		add_action( 'gform_field_advanced_settings', array( __CLASS__, 'dynamic_setting_actions' ), 10, 2 );

		add_action( 'activate_plugin', array( __CLASS__, 'register_perk_activation_hooks' ) );

		add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_spellbook_link' ), 10, 2 );

		add_filter( 'plugin_auto_update_setting_html', array( __CLASS__, 'disable_auto_updater' ), 10, 3 );

		add_filter( 'gform_settings_menu', array( __CLASS__, 'remove_gwapi_lite_settings_pages' ) );
		add_filter( 'gspc_plugin_settings_fields', '__return_empty_array' );

		// load and init all active perks
		self::initialize_perks();

	}

	public static function remove_gwapi_lite_settings_pages( $settings_tabs ) {
		// Remove element with the 'name' of 'gravity-connect'
		foreach ( $settings_tabs as $key => $tab ) {
			if (
				isset( $tab['name'] )
				&& in_array( $tab['name'], array( 'gravity-connect', 'gs-product-configurator' ), true )
			) {
				unset( $settings_tabs[ $key ] );
			}
		}

		return $settings_tabs;
	}

	public static function add_helper_body_classes( $body_class ) {
		if ( is_callable( array( 'GFForms', 'get_page' ) ) && GFForms::get_page() && ! self::is_gf_version_gte( '2.5-beta-1' ) ) {
			$body_class .= ' gf-legacy-ui';
		}
		return $body_class;
	}

	public static function register_perk_activation_hooks( $plugin ) {

		if ( ! GP_Perk::is_perk( $plugin ) ) {
			return;
		}

		$perk = GWPerk::get_perk( $plugin );
		if ( is_wp_error( $perk ) || ! $perk->is_supported() ) {
			return;
		}

		$perk->activate();

	}

	public static function plugin_spellbook_link( $links, $file ) {
		// Ensure that this plugin file exists in the list of installed Gravity Wiz products.
		if ( ! array_key_exists( $file, self::get_installed_plugins() ) ) {
			return $links;
		}

		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php' ) ) . '?page=gwp_perks">' . esc_html__( 'Spellbook', 'spellbook' ) . '</a>' );

		return $links;
	}

	public static function define_constants() {

		if ( ! defined( 'GW_DOMAIN' ) ) {
			define( 'GW_DOMAIN', 'gravitywiz.com' );
		}

		if ( ! defined( 'GW_PROTOCOL' ) ) {
			define( 'GW_PROTOCOL', 'https' );
		}

		define( 'GW_URL', GW_PROTOCOL . '://' . GW_DOMAIN );

		if ( ! defined( 'GWAPI_URL' ) ) {
			define( 'GWAPI_URL', GW_URL . '/gwapi/v7/' );
		}

		define( 'GW_UPGRADE_URL', GW_URL . '/upgrade/' );
		define( 'GW_ACCOUNT_URL', GW_URL . '/account/' );
		define( 'GW_SUPPORT_URL', GW_URL . '/support/' );
		define( 'GW_BUY_URL', GW_URL );

		define( 'GW_GFORM_AFFILIATE_URL', 'http://gwiz.io/gravityforms' );
		define( 'GW_MANAGE_PERKS_URL', admin_url( 'admin.php?page=gwp_perks' ) );

	}

	public static function activation() {
		self::init_perk_as_plugin_functionality();
	}

	public static function disable_auto_updater( $html, $plugin_file, $plugin_data ) {
		$can_update = self::$api->can_auto_update( $plugin_file );

		if ( $can_update === null || $can_update === true ) {
			return $html;
		}

		switch ( rgar( $can_update, 'code' ) ) {
			case 'invalid_or_missing_license':
				$message = sprintf( __( 'Auto-updates disabled; enter %s license in Spellbook to enable.', 'spellbook' ), $can_update['suite_name'] );
				$html = '<em>' . $message . '</em>';
				return $html;

			case 'unregistered_product':
				$message = sprintf( __( 'Auto-updates disabled; register %s in Spellbook.', 'spellbook' ), $can_update['plugin_name'] );
				$html = '<em>' . $message . '</em>';
				return $html;

			case 'expired_license':
				$message = sprintf( __( 'Auto-updates disabled; %s license has expired.', 'spellbook' ), $can_update['suite_name'] );
				$html = '<em>' . $message . '</em>';
				return $html;

			case 'free_plugin_missing_email_or_license':
				$message = __( 'Auto-updates disabled; free plugins require a license or email to enable auto-updates.', 'spellbook' );
				$html = '<em>' . $message . '</em>';
				return $html;
		}

		return $html;
	}

	/**
	 * Get all active perks, load Perk objects, and initialize.
	 *
	 * By default, perks are only initialized by Gravity Perks. Since they are plugins they have the option to
	 * initialize themselves; however, they will need to use a different init function name than "init" as this
	 * will always be loaded by default.
	 *
	 * IF IS NETWORK ADMIN
	 *     - only init network-activated perks
	 *     - only handle errors for network-activated perks
	 * IF IS SINGLE ADMIN
	 *     - init network-activated perks and single-activated perks
	 *     - only handles errors for
	 *
	 */
	private static function initialize_perks() {

		$network_perks = get_site_option( 'gwp_active_network_perks' );

		// if on the network admin, only handle network-activated perks
		$perks = is_network_admin() ? array() : get_option( 'gwp_active_perks' );

		if ( ! $network_perks ) {
			$network_perks = array();
		}

		if ( ! $perks ) {
			$perks = array();
		}

		$perks = array_merge( $network_perks, $perks );

		foreach ( $perks as $perk_file => $perk_data ) {

			$perk = GWPerk::get_perk( $perk_file );

			// New perks (which have a 'parent' property) will be initialized by Gravity Forms via the Add-on Framework.
			if ( is_wp_error( $perk ) || ! $perk->is_old_school() ) {
				continue;
			}

			if ( $perk->is_supported() ) {

				$perk->init();

			} else {

				foreach ( $perk->get_failed_requirements() as $requirement ) {
					self::handle_error( gwar( $requirement, 'code' ), $perk_file, gwar( $requirement, 'message' ) );
				}
			}
		}

	}

	/**
	 * Include admin files required on all pages
	 *
	 */
	private static function include_admin_files() {
		require_once( self::get_base_path() . '/model/notice.php' );
	}

	private static function maybe_setup() {

		// maybe set up Gravity Perks; only on admin requests for single site installs and always for multisite
		$is_non_ajax_admin = is_admin() && ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) === false;
		if ( ! $is_non_ajax_admin && ! is_multisite() ) {
			return;
		}

		$has_version_changed = get_option( 'gperks_version' ) != self::$version;

		// making sure version has really changed; gets around aggressive caching issue on some sites that cause setup to run multiple times
		if ( $has_version_changed && is_callable( array( 'GFForms', 'get_wp_option' ) ) ) {
			$has_version_changed = GFForms::get_wp_option( 'gperks_version' ) != self::$version;
		}

		if ( ! $has_version_changed ) {
			return;
		}

		self::setup();

	}

	private static function setup() {
		update_option( 'gperks_version', self::$version );

	}

	// CLASS INTERFACE //

	/**
	 * Called by perks when the "Perks" field settings tab is required.
	 */
	public static function enqueue_field_settings() {
		self::$has_field_settings = true;
	}

	public static function add_perks_tab( $tabs ) {
		$tabs[] = array(
			'id'             => 'gravity-perks',
			'title'          => __( 'Perks', 'gravity-perks' ),
			'toggle_classes' => array(),
			'body_classes'   => array( 'panel-block-tabs__body--settings' ),
		);
		return $tabs;
	}

	public static function add_perks_tab_settings() {
		do_action( 'gws_field_settings' );
		do_action( 'gperk_field_settings' );
	}



	// ERRORS AND NOTICES //

	public static function handle_error( $error_slug, $plugin_file = false, $message = '' ) {
		global $pagenow;

		$plugin_file = $plugin_file ? $plugin_file : self::$basename;
		$is_perk = $plugin_file != self::$basename;
		$action = $is_perk ? array( 'GWPerks', 'after_product_plugin_row' ) : array( 'GWPerks', 'after_plugin_row' );

		$is_plugins_page = self::is_plugins_page();

		switch ( $error_slug ) {

			case 'gravity_forms_required':
				$message = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			case 'wp_required':
				$message = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			case 'gravity_perks_required':
				$message = self::get_message( $error_slug, $plugin_file );
				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_maybe_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

				break;

			default:
				if ( ! $message || ! $is_plugins_page ) {
					return;
				}

				$message_function = array(
					new GP_Late_Static_Binding( array(
						'message' => $message,
						'class'   => 'error',
					) ),
					'GravityPerks_display_admin_message',
				);

				add_action( 'admin_notices', $message_function );
				add_action( 'network_admin_notices', $message_function );

		}

		if ( isset( $message_function ) ) {
			wp_enqueue_style( 'gwp-plugins', self::get_base_url() . '/styles/plugins.css' );
		}

		return;
	}

	public static function get_message( $message_slug, $plugin_file = false ) {

		$min_gravity_forms_version = self::$min_gravity_forms_version;
		$min_wp_version = self::$min_wp_version;

		// if a $plugin_file is provided AND it is not the same as the base plugin, let's assume it is a perk
		$is_perk = $plugin_file && $plugin_file != self::$basename;

		if ( $is_perk ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$perk = GWPerk::get_perk( $plugin_file );
			$perk_data = GWPerk::get_perk_data( $plugin_file );

			if ( $perk->is_old_school() ) {
				$min_gravity_forms_version = $perk->get_property( 'min_gravity_forms_version' );
				$min_wp_version = $perk->get_property( 'min_wp_version' );
			} else {
				$requirements = $perk->parent->minimum_requirements();

				$min_gravity_forms_version = rgars( $requirements, 'gravityforms/version' );
				$min_wp_version = rgars( $requirements, 'wordpress/version' );
			}
		}

		switch ( $message_slug ) {

			case 'gravity_forms_required':
				if ( class_exists( 'GFForms' ) ) {
					return sprintf( __( 'Current Gravity Forms version (%1$s) does not meet minimum Gravity Forms version requirement (%2$s).', 'spellbook' ),
						GFForms::$version, $min_gravity_forms_version );
				} else {
					return sprintf( __( 'Gravity Forms %1$s or greater is required. Activate it now or %2$spurchase it today!%3$s', 'spellbook' ),
						$min_gravity_forms_version, '<a href="' . GW_GFORM_AFFILIATE_URL . '">', '</a>' );
				}

			case 'wp_required':
				if ( isset( $perk ) ) {
					return sprintf( __( '%1$s requires WordPress %2$s or greater. You must upgrade WordPress in order to use this perk.', 'spellbook' ),
						$perk_data['Name'], $min_wp_version );
				} else {
					return sprintf( __( 'Spellbook requires WordPress %1$s or greater. You must upgrade WordPress in order to use Spellbook.', 'spellbook' ),
						$min_wp_version );
				}

			case 'gravity_perks_required':
				return sprintf( __( '%1$s requires Spellbook %2$s or greater. Activate it now or %3$spurchase it today!%4$s', 'spellbook' ),
					$perk_data['Name'],
					$perk->get_property( 'min_gravity_perks_version' ),
					'<a href="' . gw_add_utm_params(GW_BUY_URL, array(
						'page' => 'plugins',
						'component' => 'plugin-notice',
						'text' => 'spellbook-required'
					)) . '" target="_blank">',
					'</a>'
				);
		}

		return '';
	}


	public static function after_plugin_row( $plugin_file, $plugin_data ) {

		$template = '<p>%s</p>';

		if ( ! self::is_gravity_forms_supported() ) {

			$message = self::get_message( 'gravity_forms_required' );
			self::display_plugin_row_message( sprintf( $template, $message ), $plugin_data, true, $plugin_file );

		} elseif ( ! self::is_wp_supported() ) {

			$message = self::get_message( 'wp_required' );
			self::display_plugin_row_message( sprintf( $template, $message ), $plugin_data, true, $plugin_file );

		}

	}

	public static function after_product_plugin_row( $plugin_file, $plugin_data ) {
		$can_update = self::$api->can_auto_update( $plugin_file );

		if ( $can_update === null || $can_update === true ) {
			return;
		}

		switch ( rgar( $can_update, 'code' ) ) {
			case 'invalid_or_missing_license':
				$message = sprintf(
					__( 'Enter your license for %1$s in <a href="%2$s">Spellbook</a> to receive access to automatic upgrades and support for this %3$s. Need a license key? <a href="%4$s" target="_blank">Purchase one now.</a>', 'spellbook' ),
					$can_update['suite_name'],
					GW_MANAGE_PERKS_URL,
					$can_update['type'],
					gw_add_utm_params(GW_BUY_URL, array(
						'page' => 'plugins',
						'component' => 'plugin-row',
						'text' => 'invalid-license'
					)),
				);

				self::display_plugin_row_message( "<p>{$message}</p>", $plugin_data, true, $plugin_file );
				break;

			case 'unregistered_product':
				$message = sprintf(
					__( 'Register %1$s in <a href="%2$s">Spellbook</a> to receive access to automatic upgrades and support for this %3$s.', 'spellbook' ),
					$can_update['plugin_name'],
					GW_MANAGE_PERKS_URL,
					$can_update['type'],
				);

				self::display_plugin_row_message( "<p>{$message}</p>", $plugin_data, true, $plugin_file );
				break;

			case 'expired_license':
				$message = sprintf(
					__( 'Your license for %1$s has expired. <a href="%2$s" target="_blank">Renew</a> to receive access to automatic upgrades and support for this %3$s.', 'spellbook' ),
					$can_update['suite_name'],
					gw_add_utm_params(rgars( $can_update, 'license_data/extend_url', GW_ACCOUNT_URL ), array(
						'page' => 'plugins',
						'component' => 'plugin-row',
						'text' => 'expired-license'
					)),
					$can_update['type'],
				);

				self::display_plugin_row_message( "<p>{$message}</p>", $plugin_data, true, $plugin_file );
				break;

			case 'free_plugin_missing_email_or_license':
				$message = sprintf(
					__( 'Updating free plugins in Spellbook requires a license or email. <a href="%1$s" target="_blank">Enter in Spellbook</a>', 'spellbook' ),
					GW_MANAGE_PERKS_URL,
				);

				self::display_plugin_row_message( "<p>{$message}</p>", $plugin_data, true, $plugin_file );
				break;
		}
	}

	public static function display_admin_message( $message, $class ) {
		?>

		<div id="message" class="<?php echo $class; ?> gwp-message">
			<p><?php echo $message; ?></p>
		</div>

		<?php
	}

	public static function display_plugin_row_message( $message, $plugin_data, $is_error = false, $plugin_file = false ) {

		$id = sanitize_title( $plugin_data['Name'] );
		$is_active = false;

		if ( $plugin_file ) {
			$is_active = is_network_admin() ? is_plugin_active_for_network( $plugin_file ) : is_plugin_active( $plugin_file );
		}

		$active = $is_active ? 'active' : 'inactive';

		?>

		<style type="text/css" scoped>
			<?php printf( '#%1$s td, #%1$s th', $id ); ?>
			,
			<?php printf( 'tr[data-slug="%1$s"] td, tr[data-slug="%1$s"] th', $id ); ?>
				{
				border-bottom: 0;
				box-shadow: none !important;
				-webkit-box-shadow: none !important;
			}

			.gwp-plugin-notice td {
				box-shadow: none !important;
				padding: 0 !important;
			}

			.gwp-plugin-notice+tr[data-slug]:not(.plugin-update-tr) {
				box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
			}

			/*.gwp-plugin-notice + tr.active[data-slug]:not(.plugin-update-tr) > * {*/
			/*    box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1) !important;*/
			/*}*/
			tr.plugin-update-tr.active.gwp-plugin-notice>* {
				box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.1) !important;
			}

			.plugin-update-tr[data-slug^="gp-"]+tr[data-slug]:not(.plugin-update-tr) {
				box-shadow: inset 0 1px 0 rgba(0, 0, 0, 0.1);
			}
		</style>

		<tr class="plugin-update-tr <?php echo $active; ?> gwp-plugin-notice">
			<td colspan="4" class="colspanchange">
				<div class="update-message notice inline notice-error notice-alt"><?php echo $message; ?></div>
			</td>
		</tr>

		<?php
	}



	// IS SUPPORTED //

	public static function is_gravity_forms_supported( $min_version = false ) {
		$min_version = $min_version ? $min_version : self::$min_gravity_forms_version;
		return class_exists( 'GFCommon' ) && version_compare( GFCommon::$version, $min_version, '>=' );
	}

	public static function is_wp_supported( $min_version = false ) {
		$min_version = $min_version ? $min_version : self::$min_wp_version;
		return version_compare( get_bloginfo( 'version' ), $min_version, '>=' );
	}

	public static function get_version() {
		return self::$version;
	}



	// PERKS AS PLUGINS //

	/**
	 * Initalize all functionality that enables Perks to be plugins but also managed as perks.
	 *
	 */
	public static function register_rest_routes() {
		require_once( self::get_base_path() . '/includes/api/class-rest-products-controller.php' );
		require_once( self::get_base_path() . '/includes/api/class-rest-license-controller.php' );
		require_once( self::get_base_path() . '/includes/api/class-rest-spellbook-controller.php' );

		$products_controller = new GravityPerks_REST_Products_Controller();
		$products_controller->register_routes();

		$license_controller = new GravityPerks_REST_License_Controller();
		$license_controller->register_routes();

		$spellbook_controller = new GravityPerks_REST_Spellbook_Controller();
		$spellbook_controller->register_routes();
	}

	public static function init_perk_as_plugin_functionality() {

		require_once( 'model/class-gp-plugin.php' );
		require_once( 'model/class-gp-feed-plugin.php' );

		add_filter( 'extra_plugin_headers', array( __CLASS__, 'extra_perk_headers' ) );

		// any time a plugin is activated/deactivated, refresh the list of active perks
		add_action( 'update_site_option_active_sitewide_plugins', array( __CLASS__, 'refresh_active_sitewide_perks' ) );
		add_action( 'update_option_active_plugins', array( __CLASS__, 'refresh_active_perks' ) );

		// any time a plugin is activated/deactivated, reorder plugin loading to load Gravity Perks first
		add_filter( 'pre_update_site_option_active_sitewide_plugins', array( __CLASS__, 'reorder_plugins' ) );
		add_filter( 'pre_update_option_active_plugins', array( __CLASS__, 'reorder_plugins' ) );

		// Filter bulk updates as update/installation of products is mostly done with AJAX so this is when most
		// people will see messages.
		add_filter( 'upgrader_process_complete', array( __CLASS__, 'add_bulk_plugin_notices' ), 10, 2 );
		add_filter( 'update_bulk_plugins_complete_actions', array( __CLASS__, 'add_bulk_plugins_actions' ), 10, 3 );

		// display "back to perks" link on plugins page
		add_action( 'pre_current_active_plugins', array( __CLASS__, 'display_perks_status_message' ) );

		if ( is_multisite() ) {

			// prevent perks from being network activated if Gravity Perks is not network activated, priority 11 so it fires after 'save_last_modified_plugin'
			add_action( 'admin_action_activate', array( __CLASS__, 'require_gravity_perks_network_activation' ), 11 );

		}

		// initiate a fix for Windows servers where the GP package file name is too long and prevents installs/updates from processing
		add_filter( 'upgrader_pre_download', array( __CLASS__, 'maybe_shorten_edd_filename' ), 10, 4 );

		do_action( 'gperks_loaded' );

	}

	/**
	 * Check if the URL that is about to be downloaded is an EDD package URL. If so, hook our function to shorten
	 * the filename.
	 *
	 * @param mixed  $return
	 * @param string $package The URL of the file to be downloaded.
	 *
	 * @return mixed
	 */
	public static function maybe_shorten_edd_filename( $return, $package ) {
		if ( strpos( $package, '/edd-sl/package_download/' ) !== false ) {
			add_filter( 'wp_unique_filename', array( __CLASS__, 'shorten_edd_filename' ), 10, 2 );
		}
		return $return;
	}

	/**
	 * Truncate the temporary filename to 50 characters. This resolves issues with some Windows servers which
	 * can not handle very long filenames.
	 *
	 * @param string $filename
	 * @param string $ext
	 *
	 * @return string
	 */
	public static function shorten_edd_filename( $filename, $ext ) {
		$filename = substr( $filename, 0, 50 ) . $ext;
		remove_filter( 'wp_unique_filename', array( __CLASS__, 'shorten_edd_filename' ), 10 );
		return $filename;
	}

	public static function add_bulk_plugin_notices( $upgrader, $hook_extra ) {
		$plugins = rgar( $hook_extra, 'plugins' );
		$available_products = self::$api->get_products();
		$errors = array();

		if ( ! is_array( $plugins ) || empty( $plugins ) ) {
			return;
		}

		// Loop through plugins and see if they are in the list of products from GWAPI, if so, set a flag.
		foreach ( $plugins as $plugin ) {
			if ( ! array_key_exists( $plugin, $available_products ) ) {
				continue;
			}

			self::$handled_spellbook_product = true;

			$can_update = self::$api->can_auto_update( $plugin );

			if ( ! $can_update || $can_update === true ) {
				continue;
			}

			switch ( rgar( $can_update, 'code' ) ) {
				case 'invalid_or_missing_license':
					$errors[ $can_update['type'] . '_invalid' ] = sprintf(
						__( 'Your %1$s license is invalid or missing. <a href="%2$s" target="_blank">Enter in Spellbook</a> or <a href="%3$s" target="_blank">Buy License</a>.', 'spellbook' ),
						$can_update['suite_name'],
						GW_MANAGE_PERKS_URL,
						GW_BUY_URL,
					);
					break;

				case 'unregistered_product':
					$errors[] = sprintf( __( '%s is unregistered. <a href="%s" target="_blank">Register in Spellbook</a>', 'spellbook' ), $can_update['plugin_name'], GW_MANAGE_PERKS_URL );
					break;

				case 'expired_license':
					$errors[ $can_update['type'] . '_expired' ] = sprintf(
						__( 'Your %1$s license has expired. <a href="%2$s" target="_blank">Renew License</a>', 'spellbook' ),
						$can_update['suite_name'],
						rgars( $can_update, 'license_data/extend_url', GW_ACCOUNT_URL ),
					);
					break;

				case 'free_plugin_missing_email_or_license':
					$errors[] = sprintf(
						__( 'Updating free plugins in Spellbook requires a license or email. <a href="%1$s" target="_blank">Enter in Spellbook</a>', 'spellbook' ),
						GW_MANAGE_PERKS_URL,
					);
					break;
			}
		}

		$template = '<div class="notice notice-warning gp-plugin-action" style="margin: 10px 0;">
				<p>
					%1$s
				</p>

				<ul style="list-style: disc; padding-left: 20px;">
					%2$s
				</ul>
			</div>';

		$error_list_items = array();

		foreach ( $errors as $error ) {
			$error_list_items[] = '<li>' . $error . '</li>';
		}

		$message_singular = __( '<strong>Uh-oh!</strong> We ran into a problem when updating your Gravity Wiz plugins.', 'spellbook' );
		$message_plural = __( '<strong>Uh-oh!</strong> We ran into some problems when updating your Gravity Wiz plugins.', 'spellbook' );

		if ( count( $errors ) > 0 ) {
			$upgrader->skin->feedback( sprintf(
				$template,
				( count( $errors ) == 1 ) ? $message_singular : $message_plural,
				implode( '', $error_list_items )
			) );
		}
	}

	public static function add_bulk_plugins_actions( $actions, $plugin_info ) {
		if ( ! isset( self::$handled_spellbook_product ) ) {
			return $actions;
		}

		$actions['spellbook'] = '<a href="' . GW_MANAGE_PERKS_URL . '">' . __( 'Manage in Spellbook', 'spellbook' ) . '</a>';

		return $actions;
	}

	/**
	 * Pull the "Perk" header out of the plugin header data. Used to determine if the plugin is intended to be
	 * run by Spellbook.
	 *
	 */
	public static function extra_perk_headers( $headers ) {
		array_push( $headers, 'Perk' );
		return $headers;
	}

	/**
	 * Refresh the list of active perks. Triggered anytime the "active_plugins" or "active_sitewide_plugins" option is updated.
	 * This option is updated anytime a plugin is activated or deactivated.
	 *
	 */
	public static function refresh_active_perks( $old_value ) {

		$plugins = self::get_plugins();
		$perks = array();
		$network_perks = array();

		foreach ( $plugins as $plugin_file => $plugin ) {

			// skip all non-perk plugins
			if ( rgar( $plugin, 'Perk' ) != 'True' ) {
				continue;
			}

			if ( is_multisite() && is_plugin_active_for_network( $plugin_file ) ) {
				$network_perks[ $plugin_file ] = $plugin;
			} elseif ( is_plugin_active( $plugin_file ) ) {
				$perks[ $plugin_file ] = $plugin;
			}
		}

		// if multsite, update network perks
		if ( is_multisite() ) {
			update_site_option( 'gwp_active_network_perks', $network_perks );
		}

		// update active perks every time
		update_option( 'gwp_active_perks', $perks );

	}

	public static function refresh_active_sitewide_perks( $old_value ) {
		self::refresh_active_perks( $old_value );
	}

	/**
	 * Update plugin loading order. Anytime the "active_plugins" option is updated, this function reorders the plugins, placing
	 * Spellbook as the first plugin to load to ensure that it is loaded before any individual Perk plugin.
	 *
	 */
	public static function reorder_plugins( $plugins ) {

		$perks_file = plugin_basename( __FILE__ );

		$index = array_search( $perks_file, $plugins );
		if ( $index === false ) {
			$index = array_key_exists( $perks_file, $plugins ) ? $perks_file : false;
		}

		if ( $index === false ) {
			return $plugins;
		}

		$perks_item = array( $index => $plugins[ $index ] );
		unset( $plugins[ $index ] );

		if ( is_numeric( $index ) ) {
			array_unshift( $plugins, $perks_file );
		} else {
			$plugins = array_merge( $perks_item, $plugins );
		}

		return $plugins;
	}


	// TODO SPELLBOOK AUDIT

	public static function display_perks_status_message() {

		// @todo Revisit this function; see pre 1.2.21 version for original version. Has been stripped down for now.

		$error = isset( $_GET['gwp_error'] ) ? $_GET['gwp_error'] : false;
		if ( ! $error ) {
			return;
		}

		$is_error = true;
		$message = '';

		switch ( $error ) {
			case 'networkperks':
				$message = __( '<strong>Spellbook</strong> must be network activated before a <strong>perk</strong> can be network activated.', 'spellbook' );
				break;
		}

		?>

		<div class="<?php echo $is_error ? 'error' : 'updated'; ?> gwp-message">
			<p><?php echo $message; ?></p>
		</div>

		<style type="text/css">
			#message+div.gwp-message {
				margin-top: -17px;
				border-top-style: dotted;
				border-top-right-radius: 0;
				border-top-left-radius: 0;
			}
		</style>

		<?php

	}

	// TODO SPELLBOOK AUDIT
	public static function require_gravity_perks_network_activation() {

		if ( ! is_network_admin() || self::is_gravity_perks_network_activated() ) {
			return;
		}

		$plugin = gwar( $_REQUEST, 'plugin' );
		if ( ! GWPerk::is_perk( $plugin ) ) {
			return;
		}

		$redirect = self_admin_url( 'plugins.php?gwp_error=networkperks&plugin=' . $plugin );
		wp_redirect( esc_url_raw( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $plugin ), $redirect ) ) );
		exit;

	}

	public static function is_gravity_perks_network_activated() {

		if ( ! is_multisite() ) {
			return false;
		}

		foreach ( wp_get_active_network_plugins() as $plugin ) {
			$plugin_file = plugin_basename( $plugin );
			if ( plugin_basename( dirname( __FILE__ ) . '/spellbook.php' ) == $plugin_file && is_plugin_active_for_network( $plugin_file ) ) {
				return true;
			}
		}

		return false;
	}



	// API & LICENSING //
	public static function can_manage_license() {
		return current_user_can( 'activate_plugins' );
	}

	public static function load_api() {
		require_once( dirname( __FILE__ ) . '/includes/class-gwapi.php' );
		self::$api = new GWAPI( array(
			'plugin_file' => plugin_basename( __FILE__ ),
			'version'     => GWPerks::get_version(),
		) );
	}

	/**
	 * @return GWAPI
	 */
	public static function get_api() {
		return self::$api;
	}

	/**
	 * @deprecated Call self::$api->get_license_key( 'perk' )
	 */
	public static function get_license_key() {
		return self::$api->get_license_key( 'perk' );
	}

	/**
	 * @deprecated Call self::$api directly.
	 */
	public static function has_valid_license( $flush = false ) {
		return self::$api->has_valid_license( $flush, 'perk' );
	}

	/**
	 * @deprecated Call self::$api directly.
	 */
	public static function get_license_data( $flush = false ) {
		return self::$api->get_license_data( 'perk', $flush );
	}

	// TODO SPELLBOOK AUDIT
	public static function get_api_status() {
		return self::$api->get_api_status();
	}

	// TODO SPELLBOOK AUDIT
	public static function get_api_error_message() {

		$message = __( 'Oops! Your site is having some trouble communicating with our API.', 'spellbook' );
		$message .= sprintf( '&nbsp;<a href="%s" target="_blank">%s</a>', 'https://' . GW_DOMAIN . '/documentation/troubleshooting-licensing-api/', __( 'Let\'s get this fixed.', 'spellbook' ) );

		return $message;
	}

	/**
	 * Retrieve all installed Gravity Wiz Plugins.
	 */
	public static function get_installed_plugins() {

		if ( ! empty( self::$installed_plugins ) ) {
			return self::$installed_plugins;
		}

		$plugins = self::get_plugins();
		$available_plugins = self::$api->get_products();
		$installed_plugins = array();

		foreach ( $plugins as $plugin_file => $plugin_data ) {
			if ( isset( $available_plugins[ $plugin_file ] ) ) {
				$installed_plugins[ $plugin_file ] = $plugin_data;
			}
		}

		return $installed_plugins;

	}

	// WP ADMIN INTEGRATION //

	public static function load_page() {
		require_once( self::get_base_path() . '/admin/manage_perks.php' );
		GWPerksPage::load_page();
	}

	/**
	 * Hook into Gravity Forms menu and add "Perks" as a submenu item.
	 *
	 * @param mixed $addon_menus
	 */
	public static function add_menu_item( $addon_menus ) {
		// Add separator styling
		add_action( 'admin_head', function () {
			?>
			<style>
				/* Add separator before Spellbook */
				#adminmenu .toplevel_page_gf_edit_forms .wp-submenu li a[href*="gf_help"]:after {
					border-bottom: 1px solid rgba(255, 255, 255, 0.2);
					display: block;
					float: left;
					margin: 13px -15px 8px;
					content: '';
					width: calc(100% + 26px);
				}

				@media screen and (max-width: 782px) {
					#adminmenu .toplevel_page_gf_edit_forms .wp-submenu li a[href*="gf_help"]:after {
						margin: 20px -20px 8px -20px;
						width: calc(100% + 30px);
					}
				}

				/* Ensure proper clearing */
				#adminmenu .toplevel_page_gf_edit_forms .wp-submenu li {
					clear: both;
				}
			</style>
			<?php
		} );

		// Move Spellbook to end of menu
		add_action( 'admin_menu', function () {
			global $submenu;
			if ( isset( $submenu['gf_edit_forms'] ) ) {
				// Find and remove Spellbook
				foreach ( $submenu['gf_edit_forms'] as $key => $item ) {
					if ( $item[2] === 'gwp_perks' ) {
						$spellbook = $item;
						unset( $submenu['gf_edit_forms'][ $key ] );
						// Add it back at the end
						$submenu['gf_edit_forms'][] = $spellbook;
						break;
					}
				}
			}
		}, 100 );

		// If on the Spellbook page, disable WP notifications
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'gwp_perks' ) {
			add_filter( 'wp_admin_notice_markup', '__return_empty_string' );
			add_action( 'admin_notices', function() {
				remove_all_actions( 'admin_notices' );
			}, PHP_INT_MIN );
		}

		// Add Spellbook menu item
		$menu = array(
			'label'      => __( 'Spellbook', 'spellbook' ),
			'permission' => 'update_plugins',
			'name'       => 'gwp_perks',
			'callback'   => array( __CLASS__, 'load_page' ),
		);

		$addon_menus[] = $menu;

		return $addon_menus;
	}

	/**
	 * Register scripts and init the gperk object
	 *
	 */
	public static function register_scripts() {

		// @todo Should we make Gravity Perks load from gform_loaded so we can safely Iassume GF has been loaded?
		if ( ! class_exists( 'GFCommon' ) ) {
			return;
		}

		wp_register_style( 'gwp-admin', self::get_base_url() . '/styles/admin.css' );
		wp_register_style( 'gwp-asmselect', self::get_base_url() . '/styles/jquery.asmselect.css' );

		wp_register_script( 'gwp-common', self::get_base_url() . '/scripts/common.js', array( 'jquery' ), GravityPerks::$version, false );
		wp_register_script( 'gwp-admin', self::get_base_url() . '/scripts/admin.js', array( 'jquery', 'gwp-common' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-frontend', self::get_base_url() . '/scripts/frontend.js', array( 'jquery', 'gwp-common' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-repeater', self::get_base_url() . '/scripts/repeater.js', array( 'jquery' ), GravityPerks::$version, true );
		wp_register_script( 'gwp-asmselect', self::get_base_url() . '/scripts/jquery.asmselect.js', array( 'jquery' ), GravityPerks::$version, true );

		// register our scripts with Gravity Forms so they are not blocked when noconflict mode is enabled
		add_filter( 'gform_noconflict_scripts', array( __CLASS__, 'register_noconflict_scripts' ) );
		add_filter( 'gform_noconflict_styles', array( __CLASS__, 'register_noconflict_styles' ) );

		require_once( GFCommon::get_base_path() . '/currency.php' );

		wp_localize_script( 'gwp-common', 'gperk', array(
			'baseUrl'      => self::get_base_url(),
			'gformBaseUrl' => GFCommon::get_base_url(),
			'currency'     => RGCurrency::get_currency( GFCommon::get_currency() ),
		) );

		add_action( 'admin_enqueue_scripts', array( 'GWPerks', 'enqueue_scripts' ) );

	}

	/**
	 * Enqueue Javascript
	 *
	 * In the admin, include admin.js (and common.js by dependency) on all Gravity Form and Gravity Perk pages.
	 * On the front-end, common.js and frontend.js are included when enqueued by a perk.
	 *
	 */
	public static function enqueue_scripts() {

		GWPerks::enqueue_styles();

		if ( self::is_gravity_perks_page() || self::is_gravity_page() ) {
			wp_enqueue_script( 'gwp-admin' );
		}

	}

	public static function enqueue_styles() {

		if ( self::is_gravity_perks_page() || self::is_gravity_page() ) {
			wp_enqueue_style( 'gwp-admin' );
		}

	}

	public static function register_noconflict_scripts( $scripts ) {
		return array_merge( $scripts, array( 'gwp-admin', 'gwp-frontend', 'gwp-common', 'gwp-asmselect' ) );
	}

	public static function register_noconflict_styles( $styles ) {
		return array_merge( $styles, array( 'gwp-admin', 'gwp-asmselect' ) );
	}


	// AJAX //

	public static function manage_perk() {
		require_once( GWPerks::get_base_path() . '/admin/manage_perks.php' );
		GWPerksPage::ajax_manage_perk();
	}

	public static function json_and_die( $data ) {
		echo json_encode( $data );
		die();
	}



	// HELPERS //

	public static function get_base_url() {
		$folder = basename( dirname( __FILE__ ) );
		return plugins_url( $folder );
	}

	public static function get_base_path() {
		return dirname( __FILE__ );
	}

	public static function is_gravity_page() {
		return class_exists( 'RGForms' ) ? RGForms::is_gravity_page() : false;
	}

	public static function is_plugins_page() {
		global $pagenow;

		$query_action = isset( $_GET['action'] ) ? $_GET['action'] : false;

		return $pagenow == 'plugins.php' && ! $query_action;
	}

	public static function is_gravity_perks_page( $page = false ) {

		$current_page = self::get_current_page();
		$gp_pages = array( 'gwp_perks', 'gwp_settings' );

		if ( $page ) {
			return $current_page == $page;
		}

		return in_array( $current_page, $gp_pages );
	}

	private static function get_current_page() {
		$current_page = trim( strtolower( rgget( 'page' ) ) );

		return $current_page;
	}

	/**
	 * @return array An array of installed plugins.
	 */
	public static function get_plugins( $clear_cache = false ) {
		// Ensure that WordPress plugin functions are loaded before calling them as get_plugins() can be called at various times.
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		$plugins = get_plugins();

		if ( $clear_cache || ! self::plugins_have_perk_plugin_header( $plugins ) ) {
			wp_cache_delete( 'plugins', 'plugins' );
			$plugins = get_plugins();
		}

		return $plugins;
	}

	/**
	 * Confirm whether our custom plugin header 'Perk' is available.
	 *
	 * When activating Gravity Perks, the plugin cache has already been created without the custom 'Perk' header.
	 *
	 */
	public static function plugins_have_perk_plugin_header( $plugins ) {
		foreach ( $plugins as $plugin ) {
			if ( rgar( $plugin, 'Perk' ) ) {
				return true;
			}
		}

		return false;
	}


	public static function is_pointer_dismissed( $pointer_name ) {
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		return in_array( $pointer_name, $dismissed );
	}

	public static function dismiss_pointer( $pointer ) {

		if ( is_array( $pointer ) ) {
			foreach ( $pointer as $pntr ) {
				self::dismiss_pointer( $pntr );
			}
		} else {

			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );
			if ( in_array( $pointer, $dismissed ) ) {
				return;
			}

			$dismissed[] = $pointer;
			$dismissed = implode( ',', $dismissed );

			update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', $dismissed );

		}

	}

	public static function dismiss_pointers() {
		require_once( self::get_base_path() . '/admin/manage_perks.php' );

		check_ajax_referer( 'gwp-dismiss-pointers', 'security' );

		foreach ( GWPerksPage::get_perk_pointers() as $perk_pointer ) {
			self::dismiss_pointer( $perk_pointer['name'] );
		}
	}

	public static function format_changelog( $string ) {
		if ( empty( $string ) ) {
			return '';
		}

		return wp_kses_post( $string );
	}

	/**
	 * There are on-going issues with this Markdown library and versions of PHP 7.1+. Usage is currently limited to
	 * formatting the changelog of Gravity Perks.
	 *
	 * @deprecated 3.0.13, we now format changelogs as HTML from the API and pass them through wp_kses_post().
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	public static function markdown( $string ) {
		_deprecated_function( __method__, '3.0.13' );
	}

	public static function dynamic_setting_actions( $position, $form_id ) {

		$action = current_filter() . '_' . $position;

		if ( did_action( $action ) < 1 ) {
			do_action( $action, $form_id );
			//echo $action . '<br />';
		}
	}

	public static function drop_tables( $tables ) {
		global $wpdb;

		$tables = is_array( $tables ) ? $tables : array( $tables );

		foreach ( $tables as $table ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}

	}

	// LOGGING //

	public static function enable_logging_support( $plugins ) {
		$plugins['spellbook'] = __( 'Spellbook', 'spellbook' );
		return $plugins;
	}

	public static function log_error( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'spellbook', $message, KLogger::ERROR );
		}
	}

	public static function log_debug( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'spellbook', $message, KLogger::DEBUG );
		}
	}

	public static function log( $message ) {
		$backtrace = debug_backtrace();
		$caller = $backtrace[1];
		$method = '';
		if ( isset( $caller['class'] ) && $caller['class'] ) {
			$method .= $caller['class'] . '::';
		}
		$method .= $caller['function'];
		self::log_debug( sprintf( '%s: %s', $method, $message ) );
	}

	// REEVALUATE ALL CODE BELOW THIS LINE //

	/**
	 * Adds a new "Perks" tab to the form and/or field settings where perk objects can
	 * will load their form settings
	 *
	 * @param array $form: GF Form object
	 */
	public static function add_form_editor_tabs() {

		// Editor has changed in Gravity Forms 2.5.
		if ( self::is_gf_version_gte( '2.5-beta-1' ) ) {
			return;
		}

		if ( ! self::$has_form_settings && ! self::$has_field_settings ) {
			return;
		}

		?>

		<style type="text/css">
			.gws-child-setting {
				display: none;
				padding: 10px 0 10px 15px;
				margin: 6px 0 0 6px;
				border-left: 2px solid #eee;
			}
		</style>

		<script type="text/javascript">

			jQuery(document).ready(function ($) {

				<?php if ( self::$has_form_settings ) : ?>
					gperk.addTab($('#form_settings'), '#gws_form_tab', '<?php _e( 'Perks', 'spellbook' ); ?>');
				<?php endif; ?>

				<?php if ( self::$has_field_settings ) : ?>
					gperk.addTab($('#field_settings'), '#gws_field_tab', '<?php _e( 'Perks', 'spellbook' ); ?>');
				<?php endif; ?>

			});

		</script>

		<?php if ( self::$has_form_settings ) : ?>
			<div id="gws_form_tab">
				<ul class="gforms_form_settings">
					<?php do_action( 'gws_form_settings' ); ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php if ( self::$has_field_settings ) : ?>
			<div id="gws_field_tab">
				<ul class="gforms_field_settings">
					<?php do_action( 'gws_field_settings' ); ?>
					<?php do_action( 'gperk_field_settings' ); ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * Get all perk options or optionally specify a slug to get a specific perk's options.
	 * If slug provided and no options found, return default perk options.
	 *
	 * @param mixed $slug Perk slug
	 * @return array $options array or array of of perk options arrays
	 */
	public static function get_perk_options( $slug = false ) {

		$all_perk_options = get_option( 'gwp_perk_options' );

		if ( ! $all_perk_options ) {
			$all_perk_options = array();
		}

		if ( $slug ) {
			foreach ( $all_perk_options as $perk_options ) {
				if ( $perk_options['slug'] == $slug ) {
					return $perk_options;
				}
			}
			require_once( self::get_base_path() . '/model/perk.php' );
			return GWPerk::get_default_perk_options( $slug );
		}

		return $all_perk_options;
	}

	public static function update_perk_option( $updated_options ) {

		$all_perk_options = self::get_perk_options();
		$is_new = true;

		foreach ( $all_perk_options as &$perk_options ) {

			if ( $perk_options['slug'] == $updated_options['slug'] ) {
				$is_new = false;
				$perk_options = $updated_options;
			}
		}

		if ( $is_new ) {
			$all_perk_options[ $updated_options['slug'] ] = $updated_options;
		}

		return update_option( 'gwp_perk_options', $all_perk_options );
	}

	public static function is_debug() {

		$enabled_via_constant = defined( 'GP_DEBUG' ) && GP_DEBUG;
		$enabled_via_query = isset( $_GET['gp_debug'] ) && current_user_can( 'update_core' );

		return $enabled_via_constant || $enabled_via_query;
	}

	public static function get_gravityforms_db_version() {

		if ( method_exists( 'GFFormsModel', 'get_database_version' ) ) {
			$db_version = GFFormsModel::get_database_version();
		} else {
			$db_version = GFForms::$version;
		}

		return $db_version;
	}

	/**
	 * Check if installed version of Gravity Forms is less than or equal to the specified version.
	 *
	 * @param string $version Version to compare with Gravity Forms' version.
	 *
	 * @return bool
	 */
	public static function is_gf_version_lte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '<=' );
	}

	/**
	 * Check if installed version of Gravity Forms is greater than or equal to the specified version.
	 *
	 * @param string $version Version to compare with Gravity Forms' version.
	 *
	 * @return bool
	 */
	public static function is_gf_version_gte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '>=' );
	}

}

class GWPerks extends GravityPerks {
}
