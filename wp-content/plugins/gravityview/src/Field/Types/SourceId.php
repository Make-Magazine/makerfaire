<?php
/**
 * Source ID field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Source_ID class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

/**
 * Add custom options for source_url fields
 */
class SourceId extends \GravityView_Field {

	var $name = 'source_id';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'contains', 'starts_with', 'ends_with'];

	var $group = 'meta';

	var $icon = 'dashicons-admin-links';

	public function __construct() {
		// Only load on Gravity Forms 2.9 and up.
		if ( version_compare( \GFForms::$version, '2.9-beta', '<' ) ) {
			return;
		}

		$this->label       = esc_html__( 'Source ID', 'gk-gravityview' );
		$this->description = esc_html__( 'The post or page where the form was submitted.', 'gk-gravityview' );

		parent::__construct();
	}

	/**
	 * @inheritDoc
	 */
	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		// Don't link to entry; doesn't make sense.
		unset( $field_options['show_as_link'] );

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$add_options                     = [];
		$add_options['link_to_source']   = [
			'type'       => 'checkbox',
			'label'      => __( 'Link to URL', 'gk-gravityview' ),
			'desc'       => __( 'Link to the page where the entry was submitted.', 'gk-gravityview' ),
			'value'      => false,
			'merge_tags' => false,
		];
		$add_options['link_text']   = [
			'type'       => 'select',
			'label'      => __( 'Link Text', 'gk-gravityview' ),
			'desc'       => __( 'What should the link show?', 'gk-gravityview' ),
			'value'      => 'source_id',
			'merge_tags' => false,
			'choices'    => [
				'source_id'  => __( 'ID of Source', 'gk-gravityview' ),
				'page_title' => __( 'Title of Source', 'gk-gravityview' ),
				'custom'     => __( 'Custom Text', 'gk-gravityview' ),
			],
			'requires'   => 'link_to_source=1',
		];
		$add_options['source_link_text'] = [
			'type'       => 'text',
			'label'      => __( 'Link Text:', 'gk-gravityview' ),
			'desc'       => __( 'Customize the link text. If empty, the link text will be the source ID.', 'gk-gravityview' ),
			'value'      => null,
			'merge_tags' => 'force',
			'requires'   => 'link_text=custom',
			'class'      => 'widefat',
		];

		return $add_options + $field_options;
	}
}
