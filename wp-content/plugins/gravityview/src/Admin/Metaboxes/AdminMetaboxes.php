<?php
/**
 * Register and render the admin metaboxes for GravityView.
 *
 * PSR-4 migration of the legacy GravityView_Admin_Metaboxes class.
 *
 * @package GravityKit\GravityView\Admin\Metaboxes
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Admin\Metaboxes;

use GravityKit\GravityView\Utils\Path;

class AdminMetaboxes {

	static $metaboxes_dir;

	/**
	 * @var int The post ID of the current View
	 */
	var $post_id = 0;

	/**
	 * Whether hooks have already been added (prevents double-registration).
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private static $hooks_added = false;

	/**
	 *
	 */
	function __construct() {
		self::$metaboxes_dir = Path::dir( 'Admin/Metaboxes' );

		if ( ! self::$hooks_added ) {
			$this->initialize();
			self::$hooks_added = true;
		}
	}

	/**
	 * Add WordPress hooks
	 *
	 * @since 1.7.2
	 */
	function initialize() {

		add_action( 'add_meta_boxes', [ $this, 'register_metaboxes' ] );

		add_action( 'add_meta_boxes_gravityview', [ $this, 'update_priority' ] );

		// information box
		add_action( 'post_submitbox_misc_actions', [ $this, 'render_direct_access_status' ], 9 );
		add_action( 'post_submitbox_misc_actions', [ $this, 'render_shortcode_hint' ] );
		add_action( 'post_submitbox_misc_actions', [ $this, 'render_customize_design_link' ], 11 );
	}

	/**
	 * GravityView wants to have the top (`normal`) metaboxes all to itself, so we move all plugin/theme metaboxes down to `advanced`
	 *
	 * @since 1.15.2
	 */
	function update_priority() {
		global $wp_meta_boxes;

		if ( ! empty( $wp_meta_boxes['gravityview'] ) ) {
			foreach ( [ 'high', 'core', 'low' ] as $position ) {
				if ( isset( $wp_meta_boxes['gravityview']['normal'][ $position ] ) ) {
					foreach ( $wp_meta_boxes['gravityview']['normal'][ $position ] as $key => $meta_box ) {
						if ( ! preg_match( '/^gravityview_/ism', $key ) ) {
							$wp_meta_boxes['gravityview']['advanced'][ $position ][ $key ] = $meta_box;
							unset( $wp_meta_boxes['gravityview']['normal'][ $position ][ $key ] );
						}
					}
				}
			}
		}
	}

	function register_metaboxes() {
		global $post;

		// On Comment Edit, for example, $post isn't set.
		if ( empty( $post ) || ! is_object( $post ) || ! isset( $post->ID ) ) {
			return;
		}

		// select data source for this view
		add_meta_box( 'gravityview_select_form', $this->get_data_source_header( $post->ID ), [ $this, 'render_data_source_metabox' ], 'gravityview', 'normal', 'high' );

		// select view type/template
		add_meta_box( 'gravityview_select_template', __( 'Choose a View Type', 'gk-gravityview' ), [ $this, 'render_select_template_metabox' ], 'gravityview', 'normal', 'high' );

		// View Configuration box
		add_meta_box( 'gravityview_view_config', __( 'Layout', 'gk-gravityview' ), [ $this, 'render_view_configuration_metabox' ], 'gravityview', 'normal', 'high' );

		$this->add_settings_metabox_tabs();

		// Other Settings box
		add_meta_box( 'gravityview_settings', __( 'Settings', 'gk-gravityview' ), [ $this, 'settings_metabox_render' ], 'gravityview', 'normal', 'core' );
	}

	/**
	 * Render the View Settings metabox
	 *
	 * @since 1.8
	 * @param WP_Post $post
	 */
	function settings_metabox_render( $post ) {

		/**
		 * Before rendering GravityView metaboxes.
		 *
		 * @since 1.8
		 *
		 * @param WP_Post $post The current View post object.
		 */
		do_action( 'gravityview/metaboxes/before_render', $post );

		$metaboxes = \GravityView_Metabox_Tabs::get_all();

		include Path::file( 'Admin/Metaboxes/views/gravityview-navigation.php' );
		include Path::file( 'Admin/Metaboxes/views/gravityview-content.php' );

		/**
		 * After rendering GravityView metaboxes.
		 *
		 * @since 1.8
		 *
		 * @param WP_Post $post The current View post object.
		 */
		do_action( 'gravityview/metaboxes/after_render', $post );
	}

	/**
	 * Add default tabs to the Settings metabox
	 *
	 * @since 1.8
	 */
	private function add_settings_metabox_tabs() {

		$metaboxes = [
			[
				'id'            => 'template_settings',
				'title'         => __( 'View Settings', 'gk-gravityview' ),
				'file'          => 'view-settings.php',
				'icon-class'    => 'dashicons-admin-generic',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'styles',
				'title'         => __( 'Styles', 'gk-gravityview' ),
				'file'          => 'view-styles.php',
				'icon-class'    => 'dashicons-admin-appearance',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'multiple_entries',
				'title'         => __( 'Multiple Entries', 'gk-gravityview' ),
				'file'          => 'multiple-entries.php',
				'icon-class'    => 'dashicons-admin-page',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'single_entry', // Use the same ID as View Settings for backward compatibility
				'title'         => __( 'Single Entry', 'gk-gravityview' ),
				'file'          => 'single-entry.php',
				'icon-class'    => 'dashicons-media-default',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'edit_entry', // Use the same ID as View Settings for backward compatibility
				'title'         => __( 'Edit Entry', 'gk-gravityview' ),
				'file'          => 'edit-entry.php',
				'icon-class'    => 'dashicons-welcome-write-blog',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'delete_entry',
				'title'         => __( 'Delete Entry', 'gk-gravityview' ),
				'file'          => 'delete-entry.php',
				'icon-class'    => 'dashicons-trash',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'sort_filter',
				'title'         => __( 'Filter &amp; Sort', 'gk-gravityview' ),
				'file'          => 'sort-filter.php',
				'icon-class'    => 'dashicons-sort',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'permissions', // Use the same ID as View Settings for backward compatibility
				'title'         => __( 'Permissions', 'gk-gravityview' ),
				'file'          => 'permissions.php',
				'icon-class'    => 'dashicons-lock',
				'callback'      => '',
				'callback_args' => '',
			],
			[
				'id'            => 'advanced',
				'title'         => __( 'Custom Code', 'gk-gravityview' ),
				'file'          => 'custom-code.php',
				'icon-class'    => 'dashicons-editor-code',
				'callback'      => '',
				'callback_args' => '',
			],
		];

		/**
		 * Modify the default settings metabox tabs.
		 *
		 * @since 1.8
		 *
		 * @param array $metaboxes Array of metabox tab configurations.
		 */
		$metaboxes = apply_filters( 'gravityview/metaboxes/default', $metaboxes );

		foreach ( $metaboxes as $m ) {

			$tab = new \GravityView_Metabox_Tab( $m['id'], $m['title'], $m['file'], $m['icon-class'], $m['callback'], $m['callback_args'] );

			\GravityView_Metabox_Tabs::add( $tab );

		}

		unset( $tab );
	}

	/**
	 * Generate the title for Data Source, which includes the Action Links once configured.
	 *
	 * @since 1.8
	 *
	 * @param int $post_id ID of the current post
	 *
	 * @return string "Data Source", plus links if any
	 */
	private function get_data_source_header( $post_id ) {
		/**
		 * This method is running before GravityView's been fully set up; likely being called by another plugin.
		 *
		 * @see https://github.com/gravityview/GravityView/issues/1684
		 */
		if ( ! class_exists( \GravityKit\GravityView\Admin\AdminViews::class ) ) {
			return __( 'Data Source', 'gk-gravityview' );
		}

		$current_form = gravityview_get_form( gravityview_get_form_id( $post_id ) );

		$links = \GravityView_Admin_Views::get_connected_form_links( $current_form, false );

		if ( ! empty( $links ) ) {
			$links = '<span class="alignright gv-form-links">' . $links . '</span>';
		}

		$output = $links;

		if ( ! $current_form ) {
			// Starting from GF 2.6, GF's form_admin.js script requires window.form and window.gf_vars objects to be set when any element has a .merge-tag-support class.
			// Since we don't yet have a form when creating a new View, we need to mock those objects.
			$_id        = isset( $_GET['id'] ) ? $_GET['id'] : null;
			$_GET['id'] = - 1; // This is needed for GFCommon::gf_vars() to return the mergeTags property.

			if ( function_exists( 'error_reporting' ) ) {
				// Store the original error reporting level.
				$original_error_reporting = error_reporting();

				// Turn off warnings.
				error_reporting( $original_error_reporting & ~E_WARNING );
			}

			$output .= sprintf(
				'<script type="text/javascript">var form = %s; %s</script>',
				'{fields: []}',
				@\GFCommon::gf_vars( false ) // Need to silence errors because the form doesn't exist and GF doesn't expect that.
			);

			if ( function_exists( 'error_reporting' ) ) {
				error_reporting( $original_error_reporting );
			}

			$_GET['id'] = $_id;
		}

		return __( 'Data Source', 'gk-gravityview' ) . $output;
	}

	/**
	 * Render html for 'select form' metabox
	 *
	 * @param object $post
	 * @return void
	 */
	public function render_data_source_metabox( $post ) {

		include Path::file( 'Admin/Metaboxes/views/data-source.php' );
	}

	/**
	 * Render html for 'select template' metabox
	 *
	 * @param object $post
	 * @return void
	 */
	public function render_select_template_metabox( $post ) {

		include Path::file( 'Admin/Metaboxes/views/select-template.php' );
	}

	/**
	 * Generate the script tags necessary for the Gravity Forms Merge Tag picker to work.
	 *
	 * @param  int $curr_form Form ID
	 * @return null|string     Merge tags html; NULL if $curr_form isn't defined.
	 */
	public static function render_merge_tags_scripts( $curr_form ) {

		if ( empty( $curr_form ) ) {
			return null;
		}

		$form = gravityview_get_form( $curr_form );

		$get_id_backup = isset( $_GET['id'] ) ? $_GET['id'] : null;

		if ( isset( $form['id'] ) ) {
			$form_script = 'var form = ' . \GFCommon::json_encode( $form ) . ';';

			// The `gf_vars()` method needs a $_GET[id] variable set with the form ID.
			$_GET['id'] = $form['id'];

		} else {
			$form_script = 'var form = new Form();';
		}

		$output = '<script type="text/javascript" data-gv-merge-tags="1">' . $form_script . "\n" . \GFCommon::gf_vars( false ) . '</script>';

		// Restore previous $_GET setting
		$_GET['id'] = $get_id_backup;

		return $output;
	}

	/**
	 * Render html for 'View Configuration' metabox
	 *
	 * @param mixed $post
	 * @return void
	 */
	function render_view_configuration_metabox( $post ) {

		// Use nonce for verification
		wp_nonce_field( 'gravityview_view_configuration', 'gravityview_view_configuration_nonce' );

		// Selected Form
		$curr_form = gravityview_get_form_id( $post->ID );

		$view = \GV\View::from_post( $post );

		/**
		 * Selected templates
		 *
		 * @deprecated 3.0.0
		 *             Use $directory_entries_template instead.
		 */
		$curr_template              = gravityview_get_directory_entries_template_id( $post->ID );
		$directory_entries_template = gravityview_get_directory_entries_template_id( $post->ID );
		$single_entry_template      = gravityview_get_single_entry_template_id( $post->ID );

		echo self::render_merge_tags_scripts( $curr_form );

		include Path::file( 'Admin/Metaboxes/views/view-configuration.php' );
	}

	/**
	 * Render html View General Settings
	 *
	 * @param object $post
	 * @return void
	 */
	function render_view_settings_metabox( $post ) {

		// View template settings
		$current_settings = gravityview_get_template_settings( $post->ID );

		include Path::file( 'Admin/Metaboxes/views/view-settings.php' );
	}

	/**
	 * Render shortcode hint in the Publish metabox
	 *
	 * @return void
	 */
	function render_shortcode_hint() {
		global $post;

		// Only show this on GravityView post types.
		if ( false === gravityview()->request->is_admin( '', null ) ) {
			return;
		}

		// If the View hasn't been configured yet, don't show embed shortcode
		if ( ! gravityview_get_directory_fields( $post->ID ) && ! gravityview_get_directory_widgets( $post->ID ) ) {
			return;
		}

		include Path::file( 'Admin/Metaboxes/views/shortcode-hint.php' );
	}

	/**
	 * Render a "Customize design" shortcut in the Publish metabox.
	 *
	 * Surfaces the Styles tab from the (always-visible) Publish sidebar.
	 * Without this shortcut the Styles tab lives in the Settings metabox
	 * far below the layout area, and non-technical users routinely fail
	 * to find it (UX testing showed 5+ misclicks before discovery). The
	 * inline JS handler scrolls to the Settings metabox and activates
	 * the Styles tab in the existing jQuery UI tabs widget.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	function render_customize_design_link() {
		global $post;

		if ( false === gravityview()->request->is_admin( '', null ) ) {
			return;
		}

		// Don't surface the design shortcut on a freshly created View
		// that hasn't been configured yet — we'd send the user to a
		// Styles tab that has nothing meaningful in it.
		if ( ! gravityview_get_directory_fields( $post->ID ) && ! gravityview_get_directory_widgets( $post->ID ) ) {
			return;
		}
		?>
		<div class="misc-pub-section misc-pub-gv-customize-design">
			<span class="dashicons dashicons-admin-appearance" aria-hidden="true"></span>
			<a href="#gravityview_styles" class="gv-customize-design-link">
				<?php esc_html_e( 'Customize design', 'gk-gravityview' ); ?>
			</a>
		</div>
		<script>
			jQuery( function( $ ) {
				$( '.gv-customize-design-link' ).on( 'click', function( e ) {
					e.preventDefault();
					var $settings = $( '#gravityview_settings' );
					if ( ! $settings.length ) {
						return;
					}
					var $stylesTab = $settings.find( 'a[href="#gravityview_styles"]' ).first();
					if ( $stylesTab.length ) {
						$stylesTab.trigger( 'click' );
					}
					var reduceMotion = window.matchMedia &&
						window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
					$settings[ 0 ].scrollIntoView( {
						behavior: reduceMotion ? 'auto' : 'smooth',
						block:    'start'
					} );
				} );
			} );
		</script>
		<style>
			.misc-pub-gv-customize-design .dashicons {
				color: #82878c;
				margin-right: 4px;
			}
		</style>
		<?php
	}

	/**
	 * Render Direct Access setting in the Publish metabox.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	function render_direct_access_status() {
		global $post;

		// Only show this on GravityView post types.
		if ( false === gravityview()->request->is_admin( '', null ) ) {
			return;
		}

		// If the View hasn't been configured yet, don't show embed shortcode
		if ( ! gravityview_get_directory_fields( $post->ID ) && ! gravityview_get_directory_widgets( $post->ID ) ) {
			return;
		}

		include Path::file( 'Admin/Metaboxes/views/direct-access-status.php' );
	}
}
