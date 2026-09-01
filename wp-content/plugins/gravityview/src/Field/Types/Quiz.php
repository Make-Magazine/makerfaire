<?php
/**
 * Quiz field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Quiz class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class Quiz extends \GravityView_Field {

	var $name = 'quiz';

	var $group = 'advanced';

	var $icon = 'dashicons-forms';

	public function __construct() {
		$this->label = esc_html__( 'Quiz', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$new_fields = [
			'quiz_show_explanation' => [
				'type'       => 'checkbox',
				'label'      => __( 'Show Answer Explanation?', 'gk-gravityview' ),
				'desc'       => __( 'If the field has an answer explanation, show it?', 'gk-gravityview' ),
				'value'      => false,
				'merge_tags' => false,
			],
		];

		return $new_fields + $field_options;
	}
}
