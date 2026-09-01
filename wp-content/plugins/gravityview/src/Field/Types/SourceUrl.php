<?php
/**
 * Source URL field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Source_URL class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for source_url fields
 *
 * @since 3.0.0
 */
class SourceUrl extends \GravityView_Field {

	var $name = 'source_url';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'contains', 'starts_with', 'ends_with'];

	var $group = 'meta';

	var $icon = 'dashicons-admin-links';

	public function __construct() {
		$this->label       = esc_html__( 'Source URL', 'gk-gravityview' );
		$this->description = esc_html__( 'The URL of the page where the form was submitted.', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		// Don't link to entry; doesn't make sense.
		unset( $field_options['show_as_link'] );

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$add_options                     = [];
		$add_options['link_to_source']   = [
			'type'       => 'checkbox',
			'label'      => __( 'Link to URL:', 'gk-gravityview' ),
			'desc'       => __( 'Display as a link to the Source URL', 'gk-gravityview' ),
			'value'      => false,
			'merge_tags' => false,
		];
		$add_options['source_link_text'] = [
			'type'       => 'text',
			'label'      => __( 'Link Text:', 'gk-gravityview' ),
			'desc'       => __( 'Customize the link text. If empty, the link text will be the URL.', 'gk-gravityview' ),
			'value'      => null,
			'merge_tags' => 'force',
			'requires'   => 'link_to_source',
			'class'      => 'widefat',
		];

		return $add_options + $field_options;
	}
}
