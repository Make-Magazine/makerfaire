<?php
/*
 *   Setup the settings page for configuring the options
 */
if ( class_exists( 'GFForms' ) ) {
	GFForms::include_addon_framework();
	class GFListFieldDropDown extends GFAddOn {
		protected $_version = '1.8.2';
		protected $_min_gravityforms_version = '2';
		protected $_slug = 'GFListFieldDropDown';
		protected $_full_path = __FILE__;
		protected $_title = 'Drop Down List Field for Gravity Forms';
		protected $_short_title = 'Drop Down List Field';

		public function scripts() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			wp_deregister_script( 'gform_chosen' ); // deregister default chosen script - the default script doesn't support enter add new rows

			$scripts = array(
				array(
					'handle'    => 'itsg_listdropdown_js',
					'src'       => $this->get_base_url() . "/js/listdropdown-script{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery', 'gform_chosen' ),
					'enqueue'   => array( array( $this, 'requires_scripts' ) ),
					'in_footer' => true,
					'callback'  => array( $this, 'localize_scripts' ),
				),
				array(
					'handle'    => 'gform_chosen',
					'src'       => $this->get_base_url() . "/js/chosen.jquery{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery', 'gform_gravityforms' ),
					'enqueue'   => array( array( $this, 'requires_scripts' ) ),
				),
				array(
					'handle'    => 'itsg_listdropdown_admin_js',
					'src'       => $this->get_base_url() . "/js/listdropdown-script-admin{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery' ),
					'enqueue'   => array( array( $this, 'requires_admin_js' ) ),
					'in_footer' => true,
					'callback'  => array( $this, 'localize_scripts_admin' ),
				)
			);

			 return array_merge( parent::scripts(), $scripts );
		} // END scripts

		function requires_admin_js() {
			return GFCommon::is_form_editor();
		} // END requires_admin_js

		public function localize_scripts_admin( $form, $is_ajax ) {
			$settings_array = array(
				'text_drop_down_columns' => esc_js( __( 'Drop Down Columns', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_drop_down_columns_instructions' => esc_js( __( "Place a tick next to the field to make it a drop down field. Enter the drop down options into the box as comma-separated-values, e.g. Mr,Mrs,Miss,Ms", 'gravity-forms-list-field-select-drop-down' ) ),
				'text_make_drop_down' => esc_js( __( 'Make Drop Down', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_drop_down_options' => esc_js( __( 'Drop Down Options', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_enable_enhanced' => esc_js( __( 'Enable enhanced user interface', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_enable_add_options' => esc_js( __( 'Enable add options', 'gravity-forms-list-field-select-drop-down' ) ),
			);

			wp_localize_script( 'itsg_listdropdown_admin_js', 'itsg_listdropdown_admin_js_settings', $settings_array );
		} // END localize_scripts_admin

		public function styles() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$styles = array(
				array(
					'handle'  => 'listdropdown-style',
					'src'     => $this->get_base_url() . "/css/listdropdown-style{$min}.css",
					'version' => $version,
					'media'   => 'screen',
					'enqueue' => array( array( $this, 'requires_scripts' ) ),
				),
				array(
					'handle'    => 'itsg_listdropdown_admin_style',
					'src'       => $this->get_base_url() . "/css/listdropdown-admin-style.css",
					'version'   => $version,
					'media'   => 'screen',
					'enqueue'   => array( array( $this, 'requires_admin_js' ) ),
				)
			);

			return array_merge( parent::styles(), $styles );
		} // END styles

		public function localize_scripts( $form, $is_ajax ) {
			$is_entry_detail = GFCommon::is_entry_detail();

			$settings_array = array(
				'is_entry_detail' => $is_entry_detail ? $is_entry_detail : 0,
				'text_no_results' =>  esc_js( __( 'No results match', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_placeholder' => esc_js( __( 'Select an option', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_no_results_other' =>  esc_js( __( 'No results match, press enter to add: ', 'gravity-forms-list-field-select-drop-down' ) ),
				'text_placeholder_other' => esc_js( __( 'Select or enter an option', 'gravity-forms-list-field-select-drop-down' ) ),
			);

			wp_localize_script( 'itsg_listdropdown_js', 'itsg_gf_listdropdown_js_settings', $settings_array );
		} // END localize_scripts

		public function requires_scripts( $form, $is_ajax ) {
			if ( ! $this->is_form_editor() && is_array( $form ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'list' == $field->get_input_type() ) {
						$has_columns = is_array( $field->choices );
						if ( $has_columns ) {
							foreach( $field->choices as $choice ) {
								if ( rgar( $choice, 'isDropDown' ) )  {
									return true;
								}
							}
						} elseif ( $field->itsg_list_field_dropdown || $field->itsg_list_field_drop_down ) {
							return true;
						}
					}
				}
			}

			return false;
		} // END requires_scripts

    }
    new GFListFieldDropDown();
}