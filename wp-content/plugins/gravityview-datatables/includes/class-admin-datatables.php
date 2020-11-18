<?php
/**
 * GravityView Extension -- DataTables ADMIN
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityView <hello@gravityview.co>
 * @link      http://gravityview.co
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

	}

	/**
	 * Add DataTables Extension settings
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

		$defaults = apply_filters( 'gravityview_dt_default_settings', array() );

		$ds = wp_parse_args( $settings, $defaults );

		do_action( 'gravityview_datatables_settings_row', $ds );

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
		if ( ! isset( $_POST['post_type'] ) || 'gravityview' != $_POST['post_type'] ) {
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

	}

	/**
	 * Add admin script to the whitelist
	 */
	function register_no_conflict( $required ) {
		$required[] = 'gravityview_datatables_admin';
		return $required;
	}

}

new GV_Extension_DataTables_Admin;
