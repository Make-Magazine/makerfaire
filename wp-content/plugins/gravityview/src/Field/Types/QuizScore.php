<?php
/**
 * Quiz Score field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Quiz_Score class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

class QuizScore extends \GravityView_Field {

	var $name = 'quiz_score';

	var $group = 'advanced';

	var $is_searchable = true;

	var $search_operators = ['is', 'isnot', 'greater_than', 'less_than'];

	var $icon = 'dashicons-forms';

	public function __construct() {
		$this->label = esc_html__( 'Quiz Score', 'gk-gravityview' );
		parent::__construct();
	}

	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		if ( 'edit' === $context ) {
			return $field_options;
		}

		$new_fields = [
			'quiz_use_max_score' => [
				'type'       => 'checkbox',
				'label'      => __( 'Show Max Score?', 'gk-gravityview' ),
				'desc'       => __( 'Display score as the a fraction: "[score]/[max score]". If unchecked, will display score.', 'gk-gravityview' ),
				'value'      => true,
				'merge_tags' => false,
			],
		];

		return $new_fields + $field_options;
	}
}
