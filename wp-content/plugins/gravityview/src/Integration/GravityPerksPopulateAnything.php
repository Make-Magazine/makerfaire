<?php
/**
 * Add compatibility for Gravity Wiz's Populate Anything.
 *
 * @file      class-gravityview-plugin-hooks-gravity-perks-populate-anything.php
 * @since     2.52.0
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2016, Katz Web Services, Inc.
 *
 * @package   GravityKit\GravityView\Integration
 */

namespace GravityKit\GravityView\Integration;

use GV\Template_Context;

/**
 * Compatibility hooks for Gravity Wiz's Populate Anything.
 *
 * @since 2.52.0
 */
class GravityPerksPopulateAnything extends AbstractPluginHooks {
	/**
	 * Check for the GPPA GravityView compatibility function.
	 *
	 * @since 2.52.0
	 *
	 * @var string
	 */
	protected $function_name = 'gppa_compatibility_gravityview';

	/**
	 * Adds GPPA-specific hooks when the plugin is active.
	 *
	 * @since 2.52.0
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_filter( 'gravityview/template/field/context', [ $this, 'hydrate_choice_field_labels' ], 5 );
	}

	/**
	 * Hydrates choice field labels for GPPA-populated fields.
	 *
	 * GPPA dynamically populates choice fields (Select, Checkbox, Multiselect, Radio)
	 * with values from various sources. When displaying these fields in GravityView,
	 * the first entry may show raw values instead of labels because the choices
	 * haven't been hydrated yet. This method ensures choices are loaded before rendering.
	 *
	 * @since 2.52.0
	 *
	 * @param Template_Context $context The template context.
	 *
	 * @return Template_Context The context with hydrated field choices.
	 */
	public function hydrate_choice_field_labels( $context ) {
		$field = $context->field->field ?? null;

		if ( ! $field instanceof \GF_Field ) {
			return $context;
		}

		$choice_field_types = [ 'checkbox', 'multiselect', 'select', 'radio' ];

		if ( ! in_array( $field->get_input_type(), $choice_field_types, true ) ) {
			return $context;
		}

		if ( ! $context->field || ! $context->view || ! $context->entry ) {
			return $context;
		}

		$settings = $context->field->as_configuration();

		if ( 'label' !== ( $settings['choice_display'] ?? '' ) ) {
			return $context;
		}

		$hydrated_form = gppa_compatibility_gravityview()->hydrate_submitted_entry_choices(
			$context->view->form->form,
			$context->entry->as_entry()
		);

		if ( empty( $hydrated_form ) ) {
			return $context;
		}

		$hydrated_field = \GFFormsModel::get_field( $hydrated_form, $field->id );

		if ( $hydrated_field && ! empty( $hydrated_field->choices ) ) {
			$field->choices = $hydrated_field->choices;
		}

		return $context;
	}
}
