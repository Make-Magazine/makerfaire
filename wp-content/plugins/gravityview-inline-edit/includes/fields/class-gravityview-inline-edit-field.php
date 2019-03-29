<?php
/**
 * @file class-gravityview-inline-edit-field.php
 *
 * @since 1.0
 */

/**
 * Modify field settings by extending this class.
 *
 * @since 1.0
 */
abstract class GravityView_Inline_Edit_Field {

	/**
	 * @var string The name of the field type in Gravity Forms (GF_Field->type)
	 *
	 * @since 1.0
	 */
	public $gv_field_name;

	/**
	 * @var string The name of the input type used by x-editable ("gvlist", "name", "text")
	 *
	 * @since 1.0
	 */
	public $inline_edit_type = 'text';

	/**
	 * @var bool Override the displayed value with the stored $entry value
	 *
	 * @since 1.0
	 */
	public $set_value = false;

	/**
	 * @var bool Does the field use "standard" live update shared by Name, Address, and Checkboxes
	 *
	 * @since 1.0
	 */
	public $standard_live_update = false;

	/**
	 * @var bool Whether the live update should pass values from _get_inline_edit_value as-is or JSON-encode
	 *
	 * @since 1.0
	 */
	public $live_update_json_encode = true;

	/**
	 * Stores all the GravityView inline edit field templates
	 *
	 * @since 1.0
	 *
	 * @var array
	 */
	private static $_field_templates = array();

	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Get all the custom field templates output to the inline edit script
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_field_templates() {
		return self::$_field_templates;
	}

	/**
	 * Add a custom field template to the inline edit script templates
	 *
	 * @since 1.0
	 * @since 1.0.1 Added $form_id and $field_id To fix issues with multiple of same type of field
	 *
	 * @see GF_Field::get_field_input()
	 *
	 * @param string $type Name of template (like 'checklist')
	 * @param string $template Inline edit form HTML for the template type (normally GF_Field::get_field_input())
	 * @param int    $form_id ID of the Gravity Forms form the field is connected to
	 * @param int|string $field_id ID or meta name of the field
	 *
	 * @return void
	 */
	public function add_field_template( $type, $template = '', $form_id = null, $field_id = null ) {

		$template_name = $form_id && $field_id ? "{$type}_{$form_id}_{$field_id}" : $type;

		self::$_field_templates[ $template_name ] = $template;
	}

	/**
	 * Add the filter to add the attributes
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {

		add_filter( "gravityview-inline-edit/{$this->gv_field_name}-wrapper-attributes", array(
			$this,
			'modify_inline_edit_attributes',
		), 10, 6 );

		add_filter( "gravityview-inline-edit/entry-updated/{$this->inline_edit_type}", array(
			$this,
			'updated_result',
		), 10, 4 );

	}

	/**
	 * On edit, update an inline edit update response just before it is sent back
	 *
	 * @since 1.0
	 *
	 * @param bool|WP_Error $update_result
	 * @param array $entry The Entry Object that's been updated
	 * @param int $form_id The Form ID
	 * @param GF_Field $gf_field GF_Field The field that's been updated
	 *
	 * @return bool|WP_Error|array Returns original result, if not a number field. Otherwise, returns a response array. Empty if no calculation fields, otherwise multi-dimensional array with `data` and `selector` keys
	 */
	public function updated_result( $update_result, $entry = array(), $form_id = 0, GF_Field $gf_field ) {
		$return = $update_result;

		if ( $this->standard_live_update ) {

			$value = method_exists( $this, '_get_inline_edit_value' ) ? $this->_get_inline_edit_value( $gf_field, $entry ) : rgar( $entry, $gf_field->id );

			$return = array(
				array(
					'selector' => "#gv-inline-editable-{$entry['id']}-{$entry['form_id']}-{$gf_field->id}",
					'value'    => $value,
				),
			);

			foreach ( $gf_field->inputs as $input ) {
				$input_array = explode( '.', $input['id'] );

				$return_value = $value;

				if ( $this->live_update_json_encode && isset( $input_array[1] ) ) {
					$return_value = json_encode( array( $input_array[1] => rgar( $entry, $input['id'] ) ) );
				}

				$return[] = array(
					'selector' => str_replace( '.', '-', "#gv-inline-editable-{$entry['id']}-{$entry['form_id']}-{$input['id']}" ),
					'value'    => $return_value,
				);
			}
		}

		return $return;
	}

	/**
	 * Add value and type inline attributes, and enqueue custom field scripts
	 *
	 * @since 1.0
	 *
	 * @param array $wrapper_attributes The attributes of the container <div> or <span>
	 * @param string $field_input_type The field input type
	 * @param int $field_id The field ID
	 * @param array $entry The entry
	 * @param array $form The current Form
	 * @param GF_Field $gf_field Gravity Forms field object. Is an instance of GF_Field
	 *
	 * @return array $wrapper_attributes, with `data-type` and `data-value` atts added
	 */
	public function modify_inline_edit_attributes( $wrapper_attributes, $field_input_type, $field_id, $entry, $current_form, $gf_field ) {

		if ( $this->set_value ) {
			$wrapper_attributes['data-value'] = rgar( $entry, $field_id );
		}

		$wrapper_attributes['data-type'] = $this->inline_edit_type;

		// Only try to enqueue if script is registered, preventing possible PHP warnings
		if ( wp_script_is( 'gv-inline-edit-' . $this->inline_edit_type, 'registered' ) ) {
			wp_enqueue_script( 'gv-inline-edit-' . $this->inline_edit_type );
		}

		return $wrapper_attributes;
	}

}
