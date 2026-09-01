<?php
/**
 * Entry Link field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Entry_Link class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for entry_link fields
 *
 * @since 3.0.0
 */
class EntryLink extends \GravityView_Field {

	public $name = 'entry_link';

	public $contexts = ['multiple'];

	/**
	 * @var bool
	 * @since 1.15.3
	 */
	public $is_sortable = false;

	/**
	 * @var bool
	 * @since 1.15.3
	 */
	public $is_searchable = false;

	public $group = 'featured';

	public $icon = 'dashicons-media-default';

	public function __construct() {
		$this->label       = esc_html__( 'Link to Single Entry', 'gk-gravityview' );
		$this->description = esc_html__( 'A dedicated link to the single entry with customizable text.', 'gk-gravityview' );
		parent::__construct();
	}

	/**
	 * Add as a default field, outside those set in the Gravity Form form
	 *
	 * @since 2.10 Moved here from GravityView_Admin_Views::get_entry_default_fields
	 *
	 * @param array        $entry_default_fields Existing fields
	 * @param string|array $form form_ID or form object
	 * @param string       $zone Either 'single', 'directory', 'edit', 'header', 'footer'
	 *
	 * @return array
	 */
	public function add_default_field( $entry_default_fields, $form = [], $zone = '' ) {

		if ( 'directory' !== $zone ) {
			return $entry_default_fields;
		}

		$entry_default_fields[ $this->name ] = [
			'label' => $this->label,
			'type'  => $this->name,
			'desc'  => $this->description,
			'icon'  => $this->icon,
			'group' => $this->group,
		];

		return $entry_default_fields;
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		// Always a link!
		unset( $field_options['show_as_link'], $field_options['search_filter'] );

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$add_options                    = [];
		$add_options['entry_link_text'] = [
			'type'       => 'text',
			'label'      => __( 'Link Text:', 'gk-gravityview' ),
			'desc'       => null,
			'value'      => __( 'View Details', 'gk-gravityview' ),
			'merge_tags' => true,
		];

		$this->add_field_support( 'lightbox', $field_options );
		$this->add_field_support( 'new_window', $field_options );

		return $add_options + $field_options;
	}
}
