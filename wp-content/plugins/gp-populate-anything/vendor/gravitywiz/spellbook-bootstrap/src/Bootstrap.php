<?php
/**
 * Bootstrap class for Gravity Wiz Plugins
 */


namespace Spellbook;


/**
 * Class Bootstrap
 *
 * Handles bootstrapping the plugin and displaying dependency warnings.
 */
class Bootstrap {

	/**
	 * Path to the root plugin file.
	 *
	 * @var string|null
	 */
	public $_root_file = null;

	/**
	 * In-memory map of all Bootstrap instances, keyed by plugin slug.
	 *
	 * @var array<string, Bootstrap>
	 */
	private static $instances = [];

	/**
	 * Plugins that need a Spellbook notice, collected during individual checks.
	 *
	 * Each entry is an associative array with a 'name' key.
	 *
	 * @var array<string, array{name: string}>
	 */
	private static $plugins_needing_notice = [];

	/**
	 * Whether the consolidated notice callback has been registered.
	 *
	 * @var bool
	 */
	private static $consolidated_notice_registered = false;

	/**
	 * Constructor.
	 *
	 * @param array{root_file: string} $config
	 */
	public function __construct( $config ) {
		$this->_root_file = $config['root_file'];

		$slug                     = self::get_slug_from_root_file( $this->_root_file );
		self::$instances[ $slug ] = $this;

		add_action( 'after_plugin_row_' . plugin_basename( $this->_root_file ), [ $this, 'display_dependency_warning_after_plugin_row' ], 10, 2 );

		// Gravityforms de-registers callbacks to admin_notices and network_admin_notices
		// if their output does not contain "gf-notice". This happens via an admin_head
		// callback registered with priority 10. Register all of the admin_notices
		// callbacks after that filtering so that they do not get de-registered.
		add_action( 'admin_head', [ $this, 'register_notice_hooks' ], 11 );
	}

	/**
	 * Returns all registered Bootstrap instances, keyed by plugin slug.
	 *
	 * @return array<string, Bootstrap>
	 */
	public static function get_instances() {
		return self::$instances;
	}


	/**
	 * Given a root file, returns an instance.
	 *
	 * @return self
	 */
	public static function register( $root_file ) {
		$slug = self::get_slug_from_root_file( $root_file );

		$instances = self::get_instances();
		if ( isset( $instances[ $slug ] ) ) {
			return $instances[ $slug ];
		}

		$instance = new self( [ 'root_file' => $root_file ] );

		return $instance;
	}

	/**
	 * Given a plugin root file, generates a slug for the plugin.
	 *
	 * @return string
	 */
	public static function get_slug_from_root_file( $root_file ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Pass false for markup and translations as this sometimes runs before translations are loaded.
		$plugin_data = get_plugin_data( $root_file, false, false );
		$slug        = sanitize_title( $plugin_data['Name'] );

		return $slug;
	}

	public function register_notice_hooks() {
		add_action( 'admin_notices', [ $this, 'maybe_register_spellbook_notice' ], 10 );

		// Make sure that the spellbook notice is only rendered one time, not once per
		// plugin in which spellbook bootstrap is installed.
		if ( ! self::$consolidated_notice_registered ) {
			add_action( 'admin_notices', [ __CLASS__, 'render_consolidated_spellbook_notice' ], 11 );
			add_action( 'network_admin_notices', [ __CLASS__, 'render_consolidated_spellbook_notice' ], 11 );
			self::$consolidated_notice_registered = true;
		}
	}

	/**
	 * Displays a dependency warning after the plugin row in the plugins list.
	 *
	 * @param string $plugin_file Path to the plugin file relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 *
	 * @return void
	 */
	public function display_dependency_warning_after_plugin_row( $plugin_file, $plugin_data ) {

		if ( self::is_spellbook_installed() && self::is_spellbook_active() ) {
			return;
		}

		if ( self::is_plugin_active( $plugin_file ) ) :
			?>

				<style type="text/css" scoped>
					<?php printf( '#%1$s td, #%1$s th', sanitize_title( $plugin_data['Name'] ) ); ?>
					,
					<?php printf( 'tr[data-slug="%1$s"] td, tr[data-slug="%1$s"] th', sanitize_title( $plugin_data['Name'] ) ); ?>
					{
						border-bottom: 0;
						box-shadow: none !important;
						-webkit-box-shadow: none !important;
					}

					.gwp-plugin-notice td {
						padding: 0 !important;
					}

					.gwp-plugin-notice .update-message p:before {
						content: '\f534';
						font-size: 18px;
					}
				</style>

				<tr class="plugin-update-tr active gwp-plugin-notice">
					<td colspan="4" class="colspanchange">
						<div class="update-message notice inline notice-error notice-alt">
							<p>
								<?php
								printf(
									// translators: Placeholders are opening and closing anchor tags to link to the Gravity Wiz website.
									esc_html__( 'This plugin requires Spellbook. Activate it now or %1$sinstall it today!%2$s', 'spellbook' ),
									'<a href="https://gravitywiz.com/documentation/spellbook/#installation-instructions">', '</a>'
								);
								?>
							</p>
						</div>
					</td>
				</tr>

			<?php
		endif;
	}

	/**
	 * Checks whether a plugin is active, considering network context.
	 *
	 * @param string $plugin_file Path to the plugin file.
	 *
	 * @return bool Whether the plugin is active.
	 */
	private static function is_plugin_active( $plugin_file ) {
		if ( is_network_admin() ) {
			return is_plugin_active_for_network( plugin_basename( $plugin_file ) );
		}

		return is_plugin_active( plugin_basename( $plugin_file ) );
	}

	/**
	 * Checks whether the Spellbook plugin is active.
	 *
	 * @return bool Whether Spellbook is active.
	 */
	private static function is_spellbook_active() {
		return self::is_plugin_active( WP_PLUGIN_DIR . '/spellbook/spellbook.php' );
	}

	/**
	 * Checks whether the Spellbook plugin is installed.
	 *
	 * @return bool Whether Spellbook is installed.
	 */
	private static function is_spellbook_installed() {
		return file_exists( WP_PLUGIN_DIR . '/spellbook/spellbook.php' );
	}

	/**
	 * Determines whether to show the Spellbook notice on the given screen.
	 *
	 * @return string|null The notice type ('warning' or 'error'), or null if no notice should be shown.
	 */
	private static function get_notice_level() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return null;
		}

		$allowed_bases = [ 'dashboard', 'update-core', 'plugins', 'plugin-install' ];
		if ( in_array( $screen->base, $allowed_bases, true ) ) {
			return 'warning';
		}

		$screen_id = (string) $screen->id;
		if ( strpos( $screen_id, 'gf_edit_forms' ) !== false || strpos( $screen_id, 'gf_settings' ) !== false ) {
			return 'error';
		}

		return null;
	}

	/**
	 * Collects plugin info for the consolidated Spellbook admin notice.
	 *
	 * Instead of rendering a notice immediately, this method adds the plugin
	 * to the list of plugins needing a notice. The actual rendering is handled
	 * by {@see render_consolidated_spellbook_notice()} at a lower priority.
	 *
	 * @return void
	 */
	public function maybe_register_spellbook_notice() {
		if ( ! is_admin() ) {
			return;
		}

		if ( self::is_spellbook_installed() && self::is_spellbook_active() ) {
			return;
		}

		if ( ! self::get_notice_level() ) {
			return;
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$name = get_plugin_data( $this->_root_file )['Name'];

		self::$plugins_needing_notice[ sanitize_title( $name ) ] = [
			'name' => $name,
		];
	}

	/**
	 * Renders a single consolidated admin notice for all plugins that need Spellbook.
	 *
	 * This is registered at a lower priority than the individual collectors so that
	 * all plugins have had a chance to add themselves to the list before rendering.
	 *
	 * @return void
	 */
	public static function render_consolidated_spellbook_notice() {
		if ( empty( self::$plugins_needing_notice ) ) {
			return;
		}

		$plugins = self::$plugins_needing_notice;

		// Reset to prevent double-rendering (e.g. if both admin_notices and network_admin_notices fire).
		self::$plugins_needing_notice = [];

		// Determine notice level for the current screen.
		$notice_level = self::get_notice_level();
		$classes      = $notice_level === 'error' ? 'notice notice-error gf-notice' : 'notice notice-warning';

		$download_link = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">here</a>',
			esc_url( 'https://gravitywiz.com/documentation/spellbook/#installation-instructions' )
		);

		$plugin_names = array_values( array_map( function ( $plugin ) {
			return '<strong>' . esc_html( $plugin['name'] ) . '</strong>';
		}, $plugins ) );

		$count = count( $plugin_names );

		if ( $count === 1 ) {
			$plugin_list = $plugin_names[0];
		} elseif ( $count === 2 ) {
			$plugin_list = $plugin_names[0] . ' and ' . $plugin_names[1];
		} elseif ( $count === 3 ) {
			$plugin_list = $plugin_names[0] . ', ' . $plugin_names[1] . ', and ' . $plugin_names[2];
		} else {
			$remaining   = $count - 3;
			$plugin_list = sprintf(
				// translators: %1$s, %2$s, %3$s: plugin names. %4$s: "X other Gravity Wiz plugin(s)".
				'%1$s, %2$s, %3$s, and %4$s',
				$plugin_names[0],
				$plugin_names[1],
				$plugin_names[2],
				sprintf(
					// translators: %d: number of additional plugins.
					_n( '%d other Gravity Wiz plugin', '%d other Gravity Wiz plugins', $remaining, 'spellbook' ),
					$remaining
				)
			);
		}

		$message = sprintf(
			// translators: %1$s: comma-separated list of plugin names. %2$s: download link HTML.
			_n(
				'%1$s requires Spellbook. You can download Spellbook %2$s (it&#039;s free!).',
				'%1$s require Spellbook. You can download Spellbook %2$s (it&#039;s free!).',
				$count,
				'spellbook'
			),
			$plugin_list,
			$download_link
		);

		printf(
			'<div class="%s"><p>%s</p></div>',
			esc_attr( $classes ),
			wp_kses_post( $message )
		);
	}
}

