<?php
/**
 * GravityView Extension -- DataTables ADMIN
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.6
 */

class GV_Extension_DataTables_Admin {

	function __construct() {

		$this->initialize();

	}

	function initialize() {

		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_postdata' ) );

		// adding styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_and_styles' ), 999 );
		add_filter( 'gravityview_noconflict_scripts', array( $this, 'register_no_conflict') );
		add_filter( 'gravityview_noconflict_styles', array( $this, 'register_no_conflict') );

		// Every other DataTables setting registers its default via a GV_DataTables_Extension
		// subclass hooking this filter (see class-datatables-extension.php:94). "Save Table
		// State" is rendered directly by this class instead, so without this it never appeared
		// in the merged $defaults array that other extensions' settings_row() callbacks receive.
		add_filter( 'gravityview_dt_default_settings', array( $this, 'default_settings' ) );
	}

	/**
	 * Registers the "Save Table State" default on the shared DataTables defaults filter.
	 *
	 * @since 3.11
	 *
	 * @param array $settings Default settings array.
	 *
	 * @return array
	 */
	function default_settings( $settings ) {
		$settings['save_state'] = 1;

		return $settings;
	}

	/**
	 * Add DataTables settings
	 */
	function register_metabox() {

		$m = array(
			'id' => 'datatables_settings',
			'title' => __( 'DataTables', 'gv-datatables' ),
			'callback' => array( $this, 'render_metabox' ),
			'callback_args' => array(),
			'screen' => 'gravityview',
			'file' => '',
			'icon-class' => 'gv-icon-datatables-icon',
			'context' => 'side',
			'priority' => 'default',
		);

		if( class_exists('GravityView_Metabox_Tab') ) {

			$metabox = new GravityView_Metabox_Tab( $m['id'], $m['title'], $m['file'], $m['icon-class'], $m['callback'], $m['callback_args'] );

			GravityView_Metabox_Tabs::add( $metabox );

			unset( $metabox );

		} else {

			add_meta_box( 'gravityview_' . $m['id'], $m['title'], $m['callback'], $m['screen'], $m['context'], $m['priority'] );

		}


	}

	/**
	 * Render html for metabox
	 *
	 * @access public
	 * @param object $post
	 * @return void
	 */
	function render_metabox( $post ) {
		// Use nonce for verification
		wp_nonce_field( 'gravityview_dt_settings', 'gravityview_dt_settings_nonce' );

		// View DataTables settings
		$settings = get_post_meta( $post->ID, '_gravityview_datatables_settings', true );

		?>
		<table class="form-table">
			<caption><?php esc_html_e( 'Browser Cache', 'gv-datatables' ); ?></caption>
			<tr valign="top">
				<td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[save_state]',
						array(
							'label'   => esc_html__( 'Save Table State', 'gv-datatables' ),
							'desc'    => esc_html__( 'Preserve the table\'s pagination and sorting settings across page reloads.', 'gv-datatables' ),
							'tooltip' => true,
							'article' => array(
								'id'  => '66fc52f2eb6616597e566569',
								'url' => 'https://docs.gravitykit.com/article/1022-datatables-setting-save-table-state',
							),
							'type'    => 'checkbox',
						),
						$settings['save_state'] ?? 1
					);
					?>
				</td>
			</tr>
		</table>
		<?php

		/**
		 * Filters the default DataTables settings for a View.
		 *
		 * Allows DataTables extensions to register their default settings, which are
		 * then merged with the saved View settings using wp_parse_args().
		 *
		 * @since 1.0
		 *
		 * @param array $defaults Default settings array. Each extension should add its
		 *                        settings keys with default values.
		 *
		 * @return array Modified array of default settings.
		 */
		$defaults = apply_filters( 'gravityview_dt_default_settings', [] );

		$ds = wp_parse_args( $settings, $defaults );

		/**
		 * Fires after the DataTables settings metabox content is rendered.
		 *
		 * Allows DataTables extensions to output additional settings fields in the
		 * DataTables metabox on the View editor screen.
		 *
		 * @since 1.0
		 *
		 * @param array   $ds   DataTables settings merged with defaults.
		 * @param WP_Post $post The View post object being edited.
		 */
		do_action( 'gravityview_datatables_settings_row', $ds, $post );
	}

	/**
	 * Save settings
	 *
	 * @access public
	 * @param mixed $post_id
	 * @return void
	 */
	function save_postdata( $post_id ) {

		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		}

		// validate post_type
		if ( ! isset( $_POST['post_type'] ) || 'gravityview' !== $_POST['post_type'] ) {
			return;
		}

		// validate user can edit and save post/page
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		// nonce verification
		if ( isset( $_POST['gravityview_dt_settings_nonce'] ) && wp_verify_nonce( $_POST['gravityview_dt_settings_nonce'], 'gravityview_dt_settings' ) ) {

			if( empty( $_POST['datatables_settings'] ) ) {
				$_POST['datatables_settings'] = array();
			}
			update_post_meta( $post_id, '_gravityview_datatables_settings', $_POST['datatables_settings'] );
		}


	} // end save configuration

	/**
	 * Add script to Views edit screen (admin)
	 * @param  mixed $hook
	 */
	function add_scripts_and_styles( $hook ) {

		// Don't process any scripts below here if it's not a GravityView page.
		if( ! gravityview()->request->is_admin( $hook ) ) { return; }

		$script_debug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_script( 'gravityview_datatables_admin', plugins_url( 'assets/js/datatables-admin-views'.$script_debug.'.js', GV_DT_FILE ), array( 'jquery' ), GV_Extension_DataTables::version );

		wp_enqueue_style( 'gravityview_datatables_admin', plugins_url( 'assets/css/datatables-admin.css', GV_DT_FILE ), array(), GV_Extension_DataTables::version );

		wp_localize_script( 'gravityview_datatables_admin', 'GV_DataTables_Admin', [
			'internal_fields' => wp_list_pluck( GravityView_Fields::get_all( 'gravityview' ), 'name' ),
			'field_used_in_sort' => __( 'This field is currently used in the Sort & Filter settings. Removing it will reset the sort configuration. Are you sure you want to continue?', 'gv-datatables' ),
			'width_budget'    => $this->get_width_budget_strings(),
			'pin_warning'     => __( 'This pin has no effect: pinned columns must be adjacent to the table edge. Move the field or pin the fields between it and the edge.', 'gv-datatables' ),
			'legacy_fixedcolumns_overridden' => __( 'Per-field pins are set; they override this setting.', 'gv-datatables' ),
		] );
	}

	/**
	 * Strings for the column-width budget notice in the View editor.
	 *
	 * Provided fully formed (sprintf placeholders left in place for JS to fill) so the
	 * notice's JS has no English literals of its own; every byte of visible text comes from
	 * here.
	 *
	 * @since 3.12.0
	 *
	 * @return array<string,string>
	 */
	private function get_width_budget_strings() {
		return [
			/* translators: %1$d is the total of the configured widths; %2$s is the list of field labels with the shares they are scaled to. */
			'overBudget'      => __( 'Column widths total %1$d%%. A table is 100%% wide, so they are scaled down to fit: %2$s. A column whose content needs more room than that takes it, unless Column Widths is set to keep every column at its set width.', 'gv-datatables' ),
			/* translators: %1$s is the field label; %2$d is its scaled width. */
			'overBudgetItem'  => _x( '%1$s: %2$d%%', 'Field label and its scaled width', 'gv-datatables' ),
			'listSeparator'   => _x( ', ', 'List separator between scaled column widths', 'gv-datatables' ),
			/* translators: %d is the number of columns left without a width. */
			'starvedSingular' => __( 'The set widths leave no room for the %d column without a width. All widths will be ignored and every column will size by its content.', 'gv-datatables' ),
			/* translators: %d is the number of columns left without a width. */
			'starvedPlural'   => __( 'The set widths leave no room for the %d columns without a width. All widths will be ignored and every column will size by its content.', 'gv-datatables' ),
		];
	}

	/**
	 * Add admin script to the allowlist
	 */
	function register_no_conflict( $required ) {
		$required[] = 'gravityview_datatables_admin';
		return $required;
	}

}

new GV_Extension_DataTables_Admin;
