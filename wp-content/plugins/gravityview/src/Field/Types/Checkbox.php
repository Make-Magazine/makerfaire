<?php
/**
 * Checkbox field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Checkbox class.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

use GF_Query;
use GF_Query_Column;
use GFAPI;
use GravityKit\GravityView\GravityForms\QueryExtensions\GFQueryCallFirstCheckedChoice;
use ReflectionException;
use ReflectionMethod;

/**
 * @since 3.0.0
 */
class Checkbox extends \GravityView_Field {

	var $name = 'checkbox';

	var $is_searchable = true;

	/**
	 * @see \GFCommon::get_field_filter_settings Gravity Forms suggests checkboxes should just be "is"
	 * @var array
	 */
	var $search_operators = ['is', 'in', 'not in', 'isnot', 'contains'];

	var $_gf_field_class_name = 'GF_Field_Checkbox';

	var $group = 'standard';

	var $icon = 'dashicons-yes';

	public function __construct() {
		$this->label = esc_html__( 'Checkbox', 'gk-gravityview' );
		parent::__construct();

		// Static callback: FieldRegistry::create() constructs new instances per render,
		// and identical static callables collapse into a single registration.
		add_action( 'gravityview/view/query', [ __CLASS__, 'sort_by_first_checked_choice' ] );
	}

	/**
	 * Sort by the first checked choice when a query orders by a Checkbox parent field ID.
	 *
	 * Selections live in per-input meta rows (`2.1`, `2.2`, …) with no row for the parent
	 * ID, so ordering by it silently returns the natural order. A correlated subquery over
	 * the input meta rows yields the first checked choice's stored value.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query $query The entries query.
	 *
	 * @return void
	 */
	public static function sort_by_first_checked_choice( $query ) {
		if ( ! $query instanceof GF_Query ) {
			return;
		}

		$order     = $query->_introspect()['order'];
		$rewritten = false;

		foreach ( $order as $key => $term ) {
			$column = $term[0] ?? null;

			if ( ! $column instanceof GF_Query_Column ) {
				continue;
			}

			$field_id = $column->field_id;

			// Only whole-number IDs are the parent field; `2.1` already targets one input.
			if ( ! is_numeric( $field_id ) || floor( (float) $field_id ) !== (float) $field_id ) {
				continue;
			}

			if ( $column->is_entry_column() || $column->is_meta_column() ) {
				continue;
			}

			$source  = $column->source;
			$form_id = is_array( $source ) ? reset( $source ) : $source;
			$field   = GFAPI::get_field( $form_id, $field_id );

			if ( ! $field || 'checkbox' !== $field->type || empty( $field->inputs ) ) {
				continue;
			}

			$order[ $key ] = [ new GFQueryCallFirstCheckedChoice( $column ), $term[1] ?? GF_Query::ASC ];
			$rewritten     = true;
		}

		if ( ! $rewritten ) {
			return;
		}

		// The order list is private; write it back into the slot the query reads.
		( function () use ( $order ) {
			$this->order = $order;
		} )->bindTo( $query, self::query_order_scope( $query ) )();
	}

	/**
	 * Resolves the class that declares the query's `$order` property.
	 *
	 * Private properties are per-declaring-class: GF_Patched_Query redeclares `$order` so
	 * its own slot is the live one, while a plain subclass inherits GF_Query's. Binding to
	 * the runtime class would silently write a dynamic property in the inheriting shape.
	 *
	 * @since 3.2.0
	 *
	 * @param GF_Query $query The entries query.
	 *
	 * @return string The declaring class name.
	 */
	private static function query_order_scope( $query ) {
		// Anchor on _introspect() rather than on a property named `order`: the class whose
		// _introspect() runs is the one whose slot the query actually reads, so a subclass
		// that shadows `order` without overriding _introspect() cannot misdirect the write.
		try {
			$scope = ( new ReflectionMethod( $query, '_introspect' ) )->getDeclaringClass();
		} catch ( ReflectionException $e ) {
			return get_class( $query );
		}

		// A static `order` is not reachable through `$this`, so keep walking for the real slot.
		while ( $scope && ( ! $scope->hasProperty( 'order' ) || $scope->getProperty( 'order' )->isStatic() ) ) {
			$scope = $scope->getParentClass();
		}

		return $scope ? $scope->getName() : get_class( $query );
	}

	/**
	 * Add `choice_display` setting to the field.
	 *
	 * @since 1.17
	 *
	 * @param array  $field_options
	 * @param string $template_id
	 * @param string $field_id
	 * @param string $context
	 * @param string $input_type
	 * @param int    $form_id
	 *
	 * @return array
	 */
	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		// Set the $_field_id var.
		$field_options = parent::field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id );

		if ( $this->is_choice_value_enabled() ) {

			$desc    = esc_html__( 'This input has a label and a value. What should be displayed?', 'gk-gravityview' );
			$default = 'value';
			$choices = [
				'tick'  => __( 'A check mark, if the input is checked', 'gk-gravityview' ),
				'value' => __( 'Value of the input', 'gk-gravityview' ),
				'label' => __( 'Label of the input', 'gk-gravityview' ),
			];
		} else {
			$desc    = '';
			$default = 'tick';
			$choices = [
				'tick'  => __( 'A check mark, if the input is checked', 'gk-gravityview' ),
				'label' => __( 'Label of the input', 'gk-gravityview' ),
			];
		}

		// Whole-number ids are the parent checkbox field; compound ids like `8.2`
		// are a single input under it. Non-numeric ids (standalone schema probes)
		// count as parent. Cast to float so PHP 8 doesn't coerce numeric strings.
		if ( ! is_numeric( $field_id ) || floor( (float) $field_id ) === (float) $field_id ) {
			unset( $choices['tick'] );

			// Add display format setting for parent checkbox fields only.
			$field_options['display_format'] = [
				'type'     => 'radio',
				'label'    => __( 'Display Format:', 'gk-gravityview' ),
				'value'    => 'default',
				'desc'     => __( 'Choose how multiple checkbox values should be displayed.', 'gk-gravityview' ),
				'choices'  => [
					'default' => __( 'Bulleted list (default)', 'gk-gravityview' ),
					'csv'     => __( 'Comma-separated values', 'gk-gravityview' ),
				],
				'group'    => 'display',
				'priority' => 110,
			];
		}

		$field_options['choice_display'] = [
			'type'     => 'radio',
			'class'    => 'vertical',
			'label'    => __( 'What should be displayed:', 'gk-gravityview' ),
			'value'    => $default,
			'desc'     => $desc,
			'choices'  => $choices,
			'group'    => 'display',
			'priority' => 100,
		];

		return $field_options;
	}

	/**
	 * Format checkbox field values as CSV when display_format is set to 'csv'.
	 *
	 * @since 2.19
	 *
	 * @param array                $value Array of checkbox values.
	 * @param \GF_Field            $field Gravity Forms field object.
	 * @param bool                 $show_label Whether to show labels (true) or values (false).
	 * @param array                $entry Gravity Forms entry array.
	 * @param \GV\Template_Context $gravityview The template context.
	 *
	 * @return string CSV-formatted string of checkbox values.
	 */
	public static function format_checkbox_csv( $value, $field, $show_label, $entry, $gravityview ) {
		$filtered_values = array_filter(
            (array) $value,
            function ( $item ) {
				return '' !== $item;
			}
        );

		if ( empty( $filtered_values ) ) {
			return '';
		}

		$csv_values = [];
		foreach ( $filtered_values as $item_value ) {
			// If not showing labels, just use the raw value.
			if ( ! $show_label || ! isset( $field->choices ) || ! is_array( $field->choices ) ) {
				$csv_values[] = $item_value;
				continue;
			}

			// Find the label for this value.
			$choice_label = $item_value;
			foreach ( $field->choices as $choice ) {
				if ( ! isset( $choice['value'] ) || $choice['value'] !== $item_value ) {
					continue;
				}

				$choice_label = isset( $choice['text'] ) ? $choice['text'] : $item_value;
				break;
			}
			$csv_values[] = $choice_label;
		}

		/**
		 * Modify the separator used for CSV display of checkbox values.
		 *
		 * @since 2.19
		 *
		 * @param string               $separator   The separator to use between values. Default: ', '.
		 * @param array                $entry       Gravity Forms entry array.
		 * @param \GF_Field            $field       Gravity Forms field object.
		 * @param \GV\Template_Context $gravityview The template context.
		 */
		$separator = apply_filters( 'gravityview/field/checkbox/csv_separator', ', ', $entry, $field, $gravityview );

		return implode( $separator, $csv_values );
	}
}
