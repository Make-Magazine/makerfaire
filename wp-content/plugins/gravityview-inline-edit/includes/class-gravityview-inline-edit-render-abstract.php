<?php

abstract class GravityView_Inline_Edit_Render {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	public static $instance = array();

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		if ( ! function_exists( 'get_called_class' ) ) {
			return false;
		}

		$classname = get_called_class();

		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance[ $classname ] ) ) {
			self::$instance[ $classname ] = new $classname;
		}

		return self::$instance[ $classname ];
	}

	/**
	 * GravityView_Inline_Edit_Render constructor.
	 *
	 * @since 1.0
	 *
	 */
	private function __construct() {

		if ( $this->should_add_hooks() ) {
			$this->add_hooks();
		}

	}

	/**
	 * Return whether hooks should be run when initializing the current class
	 *
	 * @since 1.0
	 *
	 * @return bool True: run hooks. False: don't
	 */
	protected function should_add_hooks() {
		return false;
	}


	protected function add_hooks() {
		add_filter( 'gravityview-inline-edit/edit-mode', array( $this, 'filter_inline_edit_mode' ), 1 );
	}

	/**
	 * Modify the inline edit mode
	 *
	 * @since 1.0
	 *
	 * @param string $mode Existing mode. Default: `popup`
	 *
	 * @return string The mode to use. Can be `popup` or `inline`
	 */
	function filter_inline_edit_mode( $mode = '' ) {
		return $mode;
	}

	/**
	 * Wrap each field value with
	 * @param $output
	 * @param $entry
	 * @param string $field_id
	 * @param GF_Field $gf_field
	 * @param array $form
	 *
	 * @return string
	 */
	public function wrap_field_value( $output, $entry, $field_id, $gf_field, $form = array() ) {
		$source = null;

		if ( $gf_field ) {
			$input_type        = $gf_field->type;
			$source            = rgobj( $gf_field, 'choices' );
			$gf_field_id       = $gf_field->id;
			$field_id_exploded = explode( '.', $field_id );
			$input_id          = isset( $field_id_exploded[1] ) ? $field_id_exploded[1] : null;
		} else {
			$input_type  = $field_id;
			$gf_field_id = $input_id = $field_id;
		}

		$ignored_fields = GravityView_Inline_Edit::get_instance()->get_ignored_fields();

		// Don't modify output
		if ( in_array( $input_type, $ignored_fields ) ) {
			return $output;
		}

		//End the party early if the field isn't supported
		$supported_fields = GravityView_Inline_Edit::get_instance()->get_supported_fields();

		//Don't use inline edit for single inputs of a multi-column field
		if ( ! in_array( $input_type, $supported_fields ) ||
		     ( 'list' === $input_type && $gf_field->enableColumns && ! empty( $input_id ) )
		) {
			return '<div class="gv-inline-editable-disabled editable-disabled">' . $output . '</div>';
		}

		$view_id = class_exists( 'GravityView_frontend' ) ? GravityView_frontend::getInstance()->get_context_view_id() : null;

		// TODO: Add dynamic `title` to show the label of the field being edited
		$wrapper_attributes = array(
			'id'           => str_replace( '.', '-', "gv-inline-editable-{$entry['id']}-{$form['id']}-{$field_id}" ),
			'class'        => 'gv-inline-editable-value',
			'data-formid'  => $form['id'],
			'data-entryid' => $entry['id'],
			'data-fieldid' => $gf_field_id,
			'data-inputid' => $input_id,
			'data-viewid'  => $view_id,
		);

		if ( ! empty( $source ) ) {
			$wrapper_attributes['data-source'] = json_encode( $source );
		}

		//Disable inline edit for number fields with calculation
		if ( $gf_field && $gf_field->has_calculation() ) {
			return "<div class='gv-inline-edit-live gv-inline-edit-live-{$entry['id']}-{$entry['form_id']}-{$field_id}'>" . $output . "</div>";
		}

		/**
		 * @filter `gravityview-inline-edit/wrapper-attributes` Modify the attributes being added to an inline editable wrapper HTML tag
		 *
		 * @since 1.0
		 *
		 * @param array $wrapper_attributes The attributes of the container <div> or <span>
		 * @param string $field_input_type The field input type
		 * @param int $field_id The field ID
		 * @param array $entry The entry
		 * @param array $form The current Form
		 * @param GF_Field $gf_field Gravity Forms field object.
		 */
		$wrapper_attributes = apply_filters( "gravityview-inline-edit/wrapper-attributes", $wrapper_attributes, $input_type, $gf_field_id, $entry, $form, $gf_field );

		/**
		 * @filter `gravityview-inline-edit/{$input_type}-wrapper-attributes` Modify the attributes being added to an inline editable link for a specific input type
		 *
		 * @since 1.0
		 *
		 * @param array $wrapper_attributes The attributes of the container <div> or <span>
		 * @param string $field_input_type The field input type
		 * @param int $field_id The field ID
		 * @param array $entry The entry
		 * @param array $form The current Form
		 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
		 */
		$wrapper_attributes = apply_filters( "gravityview-inline-edit/{$input_type}-wrapper-attributes", $wrapper_attributes, $input_type, $gf_field_id, $entry, $form, $gf_field );

		// If only inline elements, use <span>
		if ( $output === strip_tags( $output, '<a><bdo><br><img><map><object><q><script><span><sub><sup><abbr><acronym><cite><code><dfn><em><kbd><strong><samp><var><b><big><i><small><tt>' ) ) {
			$tag_name = 'span';
		} else {
			$tag_name = 'div';
		}

		$atts_output = '';
		foreach ( $wrapper_attributes as $att => $att_value ) {
			$atts_output .= esc_attr( $att ) . '="' . esc_attr( $att_value ) . '" ';
		}

		// Return <tag atts>output</tag>
		return sprintf( '<%s %s>%s</%1$s>', $tag_name, $atts_output, $output );
	}

	/**
	 * Is inline edit enabled for this form or View?
	 *
	 * @since 1.0
	 *
	 * @return bool True: yes, it is. False: nope!
	 */
	protected function is_inline_edit_enabled( $args ) {
		return false;
	}

	/**
	 * Display the toggle button in the View header
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	protected function add_inline_edit_toggle_button() {

		$labels = array(
			'toggle'   => __( 'Toggle Inline Edit', 'gravityview-inline-edit' ),
			'disabled' => __( 'Enable Inline Edit', 'gravityview-inline-edit' ),
			'enabled'  => __( 'Disable Inline Edit', 'gravityview-inline-edit' ),
		);

		/**
		 * @filter `gravityview-inline-edit/toggle-labels` Modify the text displayed on inline edit buttons
		 *
		 * @since 1.0
		 *
		 * @param array $labels The default labels (using `toggle`, `disabled`, `enabled` keys)
		 */
		$labels = apply_filters( 'gravityview-inline-edit/toggle-labels', $labels );

		// When on admin, show as button. Otherwise, don't
		$link_class = is_admin() ? 'button button-primary' : '';

		ob_start();
		/** @define "GRAVITYVIEW_INLINE_DIR" "../" */
		include GRAVITYVIEW_INLINE_DIR . 'templates/toggle.php';
		echo ob_get_clean();
	}
}
