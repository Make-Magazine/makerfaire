<?php
/**
 * GravityView Maps Extension - Settings class
 * Adds a general setting to the GravityView settings screen
 *
 * @package   GravityView_Maps
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
 * @copyright Copyright 2015, Katz Web Services, Inc.
 *
 * @since 1.0.0
 */

class GravityView_Maps_Form_Fields extends GravityView_Maps_Component {


	function load() {

		// loads custom form fields classes
		$this->load_gf_field_classes();

		// Add button to right menu
		add_filter( 'gform_add_field_buttons', array( $this, 'add_field' ), 10, 1 );

		// Set defaults
		add_action( 'gform_editor_js_set_default_values', array( $this, 'set_field_defaults' ) );

		add_action( 'init', array( $this, 'register_scripts_and_styles' ) );
		add_action( 'gform_enqueue_scripts', array( $this, 'public_enqueue_scripts' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );

	}


	public function load_gf_field_classes() {

		if ( ! class_exists( 'GF_Field' ) || ! class_exists( 'GF_Fields' ) ) {
			return;
		}

		require $this->loader->includes_dir . 'class-gravityview-maps-gf-field-icon-picker.php';

		GF_Fields::register( new GravityView_Maps_GF_Field_Icon_Picker() );
	}


	/**
	 * Add GravityView Maps Map Icon form field
	 */
	function add_field( $field_groups ) {

		foreach ( $field_groups as &$group ) {
			if ( 'gravityview_fields' === $group['name'] ) {
				$group['fields'][] = array(
					'class'     => 'button',
					'data-type' => 'gvmaps_icon_picker',
					'value'     => __( 'Map Icon Picker', 'gravityview-maps' ),
					'onclick'   => "StartAddField('gvmaps_icon_picker');",
				);
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
		wp_register_style( 'gvmaps-icon-picker-style', plugins_url( 'assets/css/gv-maps-fields.css', $this->loader->_path ), null, $this->loader->plugin_version );

		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_register_script( 'gvmaps-icon-picker', plugins_url( 'assets/js/gv-maps-fields'.$script_debug.'.js', $this->loader->_path ), array( 'jquery' ), $this->loader->plugin_version );
	}

	/**
	 * Enqueue scripts and styles on GF Form (public)
	 * @param $form
	 * @param $is_ajax
	 */
	function public_enqueue_scripts( $form, $is_ajax ) {
		// todo: enqueue the script only if icon picker is present
		wp_enqueue_style( 'gvmaps-icon-picker-style' );
		wp_enqueue_script( 'gvmaps-icon-picker' );

	}

	/**
	 * Enqueue script & styles on GF Edit Entry Admin
	 * @param $hook
	 */
	function admin_enqueue_scripts( $hook ) {
		$is_entry_detail_edit = apply_filters( 'gform_is_entry_detail_edit', GFForms::get_page() === 'entry_detail_edit' );
		if ( ! $is_entry_detail_edit ) {
			return;
		}

		wp_enqueue_style( 'gvmaps-icon-picker-style' );
		wp_enqueue_script( 'gvmaps-icon-picker' );
	}


}