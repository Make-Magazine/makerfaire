<?php

namespace GV\Search\Fields;

use GF_Field;
use GF_Field_Repeater;
use GF_Query_Column;
use GFAPI;
use GFCommon;
use GFFormsModel;
use GravityView_Field_Repeater;
use GravityView_Fields;
use GravityView_Widget_Search;
use GV\Search\Querying\Search_Filter;
use GV\Search\Search_Policy;
use GV\View;

/**
 * Represents a search field based on a Gravity Forms Field.
 *
 * @since 2.42
 *
 * @extends Search_Field<string>
 */
final class Search_Field_Gravity_Forms extends Search_Field_Choices {
	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'gravity_forms';

	/**
	 * The inner field object.
	 *
	 * @since 2.42
	 *
	 * @var array
	 */
	public array $form_field = [];

	/**
	 * Per-instance memoization of resolved choices.
	 *
	 * `has_choices()` and `get_choices()` are called multiple times during a single
	 * Search Bar render (via `is_sievable()`, `should_be_sieved()`, `get_options()`,
	 * `collect_template_data()`). Choice resolution can be expensive (taxonomy
	 * queries for `post_category`, `get_users(2000)` for the Gravity Flow Assignee
	 * field via the `gk/gravityview/search/field/choices` filter), so resolve once
	 * per field instance.
	 *
	 * @since 2.59.0
	 *
	 * @var array{value: string, text: string}[]|null
	 */
	private ?array $choices_cache = null;

	/**
	 * @inheritDoc
	 *
	 * Make only allow named constructors for this field, due to the dependencies.
	 *
	 * @since 2.42
	 */
	protected function __construct( ?string $label = null, array $data = [] ) {
		// Do not initialize here, it will be called on the named constructors to parse dependencies.
		parent::__construct( $label, $data, false );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function satisfies( array $configuration ): bool {
		if ( $this->is_of_type( $configuration['type'] ?? '' ) ) {
			return true;
		}

		$type = self::generate_field_id(
			$configuration['form_id'] ?? 0,
			$configuration['id'] ?? '0'
		);

		if ( $this->is_of_type( $type ) ) {
			return true;
		}

		return parent::satisfies( $configuration );
	}

	/**
	 * Creates an instance based on a field object.
	 *
	 * @since 2.42
	 *
	 * @param array $field The field object.
	 *
	 * @return self The instance.
	 */
	public static function from_field( array $field ): ?self {
		if ( empty( $field ) ) {
			return null;
		}

		$instance             = new self( $field['label'] ?? '' );
		$instance->form_field = $field;
		$gf_field             = GFAPI::get_field( $field['form_id'] ?? 0, $field['id'] ?? 0 );

		if ( $gf_field ) {
			// Clone to have a copy per field for immutability.
			$instance->field = clone $gf_field;
			// Set remaining params, like `parent` and `id`.
			foreach ( $field as $param => $value ) {
				$instance->field->{$param} = $value;
			}
		}

		$instance->id         = $instance->get_type();
		$instance->item['id'] = $instance->id;

		$instance->init();

		return $instance;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Gravity Forms Field', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Gravity Forms Field', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function to_configuration(): array {
		return array_merge(
			parent::to_configuration(),
			[
				'form_field' => $this->form_field,
				'form_id'    => $this->form_field['form_id'] ?? 0,
			]
		);
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_type(): string {
		return self::generate_field_id(
			(int) ( $this->form_field['form_id'] ?? 0 ),
			(string) ( $this->form_field['id'] ?? '0' )
		);
	}

	/**
	 * Generates a valid Search Field ID.
	 *
	 * @since 2.42
	 *
	 * @param int    $form_id  The form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return string The Search Field ID.
	 */
	public static function generate_field_id( int $form_id, string $field_id ): string {
		return sprintf( '%d::%s', $form_id, $field_id );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function init(): void {
		parent::init();

		$this->item['icon'] = $this->get_field_icon();

		$field = $this->get_gf_field();
		if ( ! $field ) {
			return;
		}

		$this->item['parent'] = $field['parent'] ?? null;
	}

	/**
	 * Returns the icon for the Gravity Forms Field.
	 *
	 * @since 2.42
	 *
	 * @return string The icon class name.
	 */
	private function get_field_icon(): string {
		// Use Gravity Forms' field icon if available.
		$gf_field = $this->get_gf_field();

		$icon = null;
		if ( $gf_field ) {
			// GF 2.9+.
			if ( method_exists( $gf_field, 'get_form_editor_field_type_icon' ) ) {
				$icon = $gf_field->get_form_editor_field_type_icon();
			}

			if ( method_exists( $gf_field, 'get_form_editor_field_icon' ) ) {
				// GF 2.5+.
				$icon = $gf_field->get_form_editor_field_icon();
			}
		}

		// We won't stand for the cog icon by default.
		if ( $icon && ! in_array( $icon, [ 'dashicons-admin-generic', 'gform-icon--cog' ], true ) ) {
			return $icon;
		}

		// Use GravityView's field icon next, if available.
		$field = GravityView_Fields::get( $this->get_field_id() );

		if ( $field ) {
			return $field->get_icon();
		}

		$type = $this->get_field_id();
		if ( is_numeric( $type ) && $gf_field instanceof GF_Field ) {
			$type = $gf_field->get_input_type();
		}

		switch ( $type ) {
			case 'repeater':
				return 'dashicons-controls-repeat';
			case 'geolocation':
				return 'dashicons-admin-site';
			default:
				return 'dashicons-admin-generic';
		}
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function is_of_type( string $type ): bool {
		return $this->get_type() === $type;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_name(): string {
		$field_id = $this->get_field_id();

		return sprintf( 'filter_%s', str_replace( '.', '_', $field_id ) );
	}

	/**
	 * Returns the field ID.
	 *
	 * @since 2.42
	 *
	 * @return string The field ID.
	 */
	private function get_field_id(): string {
		$parts = explode( '::', (string) ( $this->id ?? '' ) );
		$count = count( $parts );

		switch ( $count ) {
			case 1:
				return (string) $this->id;
			case 2:
				return (string) $parts[1];
			default:
				return '0';
		}
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function has_choices(): bool {
		return $this->get_choices() !== [];
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function is_sievable(): bool {
		return $this->has_choices() && ! $this->is_child();
	}

	/**
	 * @inheritDoc
	 *
	 * @since 2.51.0
	 */
	protected function is_parent(): bool {
		$field = $this->get_gf_field();
		if ( ! $field ) {
			return false;
		}

		return ( ( false === strpos( $field->id, '.' ) && $field->get_entry_inputs() ) || ( $field->fields ?? null ) );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 2.51.0
	 */
	protected function get_nesting_level(): int {
		$field = $this->get_gf_field();
		if ( ! $field ) {
			return parent::get_nesting_level();
		}

		$parents = GravityView_Field_Repeater::get_repeater_field_ids( $field->formId ?? 0 );
		$level   = count( $parents[ $field->id ] ?? [] );

		return $level ? $level : parent::get_nesting_level();
	}

	/**
	 * Whether this field has a parent.
	 *
	 * @since 2.42
	 *
	 * @return bool
	 */
	protected function is_child(): bool {
		$field = $this->get_gf_field();
		if ( ! $field ) {
			return parent::is_child();
		}

		return ( $field->parent ?? null ) instanceof GF_Field;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_field_type(): string {
		$field = $this->get_gf_field();

		return GravityView_Widget_Search::get_search_input_types(
			$this->get_field_id(),
			$field ? $field['type'] : $this->form_field['type'] ?? null
		);
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_choices(): array {
		// The constructor's `setting_keys()` calls `get_options()` -> `is_sievable()`
		// -> here before `from_configuration()` has populated `form_field`. Skip
		// caching during that pre-init pass; otherwise the empty result would
		// stick and freeze the field's choices to `[]` forever.
		if ( empty( $this->form_field ) ) {
			return [];
		}

		if ( null !== $this->choices_cache ) {
			return $this->choices_cache;
		}

		$field      = $this->get_gf_field();
		$field_type = $field ? $field->type : $this->get_field_id();
		$choices    = ( $field && ! empty( $field->choices ) ) ? (array) $field->choices : [];

		if ( ! $choices ) {
			switch ( $field_type ) {
				case 'payment_status':
					$choices = GFCommon::get_entry_payment_statuses_as_choices();
					break;
				case 'post_category':
					$choices = gravityview_get_terms_choices();
					break;
			}
		}

		/**
		 * Filters the choices for a Gravity Forms-backed search field.
		 *
		 * Allows integrations to supply choices for field types that don't expose
		 * them on `$field->choices` (e.g., the Gravity Flow Assignee Select field).
		 *
		 * @since 2.59.0
		 *
		 * @param array{value: string, text: string}[] $choices      The current choices.
		 * @param GF_Field|null                        $field        The Gravity Forms field, or null for entry-meta types like `payment_status`.
		 * @param Search_Field_Gravity_Forms           $search_field The search field instance.
		 */
		$this->choices_cache = (array) apply_filters( 'gk/gravityview/search/field/choices', $choices, $field, $this );

		return $this->choices_cache;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_sieved_values(): array {
		global $wpdb;

		$form_id               = $this->view->form->ID;
		$field_id              = $this->get_field_id();
		$entry_table_name      = GFFormsModel::get_entry_table_name();
		$entry_meta_table_name = GFFormsModel::get_entry_meta_table_name();

		$column = new GF_Query_Column( $field_id, $form_id );

		if ( $column->is_entry_column() ) {
			$choices = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT `{$field_id}` FROM `$entry_table_name` WHERE `form_id` = %d",
					$form_id
				)
			);
		} else {
			$key_like = $wpdb->esc_like( $field_id ) . '.%';
			$choices  = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT `meta_value` FROM $entry_meta_table_name WHERE ( `meta_key` LIKE %s OR `meta_key` = %s ) AND `form_id` = %d",
					$key_like,
					$field_id,
					$form_id
				)
			);

			$field = $this->get_gf_field();

			if ( $field && 'json' === ( $field['storageType'] ?? '' ) ) {
				$choices        = array_map( 'json_decode', $choices );
				$_choices_array = [];
				foreach ( $choices as $choice ) {
					if ( ! is_array( $choice ) ) {
						$choice = [ $choice ];
					}

					$_choices_array[] = $choice;
				}

				$choices = array_unique( array_merge( [], ...$_choices_array ) );
			}
			if ( 'post_category' === $field->type ) {
				$choices = array_map(
					static function ( $choice ): string {
						$parts = explode( ':', $choice );

						return reset( $parts );
					},
					$choices
				);
			}
		}

		return $choices;
	}

	/**
	 * Retrieve the Gravity Forms field connected to this search field.
	 *
	 * @since 2.42
	 *
	 * @return GF_Field|null The Gravity Forms field.
	 */
	private function get_gf_field(): ?GF_Field {
		if ( $this->field ) {
			return $this->field;
		}

		$parts = explode( '::', ( $this->get_type() ?? '' ) );
		if ( count( $parts ) !== 2 ) {
			return null;
		}

		[ $form_id, $field_id ] = $parts;

		$field       = GFAPI::get_field( $form_id, $field_id );
		$this->field = $field ? $field : null;

		return $this->field;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_key(): string {
		$field_id = $this->get_field_id();
		if ( ! $field_id ) {
			return parent::get_key();
		}

		return $field_id;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_value() {
		$value      = parent::get_input_value();
		$input_type = $this->get_input_type();

		if ( 'date_range' === $input_type ) {
			$value = is_array( $value ) ? $value : [];

			return $value + [ 'start' => '', 'end' => '' ];
		}

		if ( 'number_range' === $input_type ) {
			$value = is_array( $value ) ? $value : [];

			return $value + [ 'min' => '', 'max' => '' ];
		}

		return $value;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function to_legacy_format(): array {
		$data = parent::to_legacy_format();

		$field = $this->get_gf_field();
		if ( $field ) {
			$data['form_id'] = $field['formId'] ?? null;
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function collect_template_data(): array {
		$data  = parent::collect_template_data();
		$field = $this->get_gf_field();
		if ( $field ) {
			$data['gf_field_type'] = $field->type ?? '';
		}

		return $data;
	}

	/**
	 * Adjusts the filter based on the field type.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	public function adjust_filter( Search_Filter $filter, ?View $view = null ): Search_Filter {
		$filter = parent::adjust_filter( $filter, $view );

		$gf_field   = $this->get_gf_field();
		$field_type = $gf_field->type ?? $filter->key();

		if ( \GFCommon::is_product_field( $field_type ) ) {
			return $this->adjust_numeric_filter( $filter );
		}

		switch ( $field_type ) {
			case 'select':
			case 'workflow_user':
			case 'radio':
				return $this->adjust_select_filter( $filter );

			case 'post_category':
				return $this->adjust_post_category_filter( $filter );

			case 'multiselect':
			case 'workflow_multi_user':
				return $this->adjust_multiselect_filter( $filter );

			case 'checkbox':
				return $this->adjust_checkbox_filter( $filter );

			case 'name':
				return $this->adjust_word_split_filter( $filter );

			case 'address':
				return $this->adjust_address_filter( $filter );

			case 'payment_date':
			case 'date':
				return $this->adjust_date_filter( $filter );

			case 'number':
				return $this->adjust_number_filter( $filter );

			case 'quantity':
			case 'product':
			case 'total':
				return $this->adjust_numeric_filter( $filter );

			case 'repeater':
				return $this->adjust_repeater_filter( $filter );
			default:
				return $filter;
		}
	}

	/**
	 * Adjusts a filter for select-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_select_filter( Search_Filter $filter ): Search_Filter {
		if ( ! is_array( $filter->value() ) ) {
			$filter = $filter->with_operator( $filter->operator(), [ 'is' ] );
		}

		return $filter;
	}

	/**
	 * Adjusts a filter for multiselect-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_multiselect_filter( Search_Filter $filter ): Search_Filter {
		$value = $filter->value();
		if ( ! is_array( $value ) ) {
			return $filter->with_operator( $filter->operator(), [ 'contains' ] );
		}

		$conditions = array_map(
			static fn( $val ): Search_Filter => $filter
				->with_value( $val )
				->with_operator( $filter->operator(), [ 'contains' ] ),
			$value
		);

		return Search_Filter::or( ...$conditions );
	}

	/**
	 * Adjusts a filter for checkbox-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_checkbox_filter( Search_Filter $filter ): Search_Filter {
		$field_id = $filter->key();
		$value    = $filter->value();
		$gf_field = $this->get_gf_field();

		// Handle single checkbox input (e.g., field 1.1).
		$inputs  = (array) ( $gf_field->inputs ?? [] );
		$choices = (array) ( $gf_field->choices ?? [] );

		if (
			false !== strpos( $field_id, '.' )
			&& ! empty( $inputs )
			&& ! empty( $choices )
		) {
			foreach ( $inputs as $k => $input ) {
				if ( ( $input['id'] ?? '' ) === $field_id ) {
					return $filter
						->with_value( $choices[ $k ]['value'] ?? $value )
						->with_operator( $filter->operator(), [ 'is' ] );
				}
			}
		}

		// Handle array of checkbox values.
		if ( ! is_array( $value ) ) {
			return $filter->with_operator( $filter->operator(), [ 'is' ] );
		}

		$conditions = array_map(
			static fn( $val ): Search_Filter => $filter
				->with_value( $val )
				->with_operator( $filter->operator(), [ 'is' ] ),
			$value
		);

		return Search_Filter::or( ...$conditions );
	}

	/**
	 * Adjusts a filter by splitting multi-word values into separate "contains" conditions.
	 *
	 * Used by name, address, and other text fields where word-by-word matching is beneficial.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_word_split_filter( Search_Filter $filter ): Search_Filter {
		$field_id = $filter->key();
		$value    = $filter->value();

		// Only split words for full field (no dot in ID).
		if ( ! is_string( $value ) || false !== strpos( $field_id, '.' ) ) {
			return $filter;
		}

		$words = explode( ' ', $value );
		$words = array_filter( $words, static fn( $word ): bool => ! empty( $word ) && strlen( $word ) > 1 );

		if ( count( $words ) <= 1 ) {
			return $filter;
		}

		$conditions = array_map(
			static fn( $word ): Search_Filter => $filter
				->with_value( $word )
				->with_operator( 'contains', [ 'contains' ] ),
			$words
		);

		return Search_Filter::and( ...$conditions );
	}

	/**
	 * Adjusts a filter for address-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_address_filter( Search_Filter $filter ): Search_Filter {
		$field_id   = $filter->key();
		$input_type = $this->get_input_type();

		// Check if this is a State/Province subfield (input 4) with a dropdown.
		$exploded = explode( '.', $field_id );
		$input_id = (int) ( $exploded[1] ?? 0 );

		if ( 4 === $input_id && ! in_array( $input_type, [ 'text', 'search', 'input_text' ], true ) ) {
			// Dropdown State/Province uses exact match.
			return $filter->with_operator( $filter->operator(), [ 'is' ] );
		}

		// For full address field only (no dot in ID), split words.
		return $this->adjust_word_split_filter( $filter );
	}

	/**
	 * Adjusts a filter for date-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_date_filter( Search_Filter $filter ): Search_Filter {
		$value       = $filter->value();
		$operator    = $filter->operator();
		$date_format = Search_Policy::get_date_php_format();

		// Handle date range (array with start/end).
		if ( is_array( $value ) ) {
			$conditions = [];

			$start = $value['start'] ?? null;
			$end   = $value['end'] ?? null;

			if ( ! empty( $start ) ) {
				$start        = GravityView_Widget_Search::get_formatted_date( $start, 'Y-m-d', $date_format );
				$conditions[] = $filter
					->with_value( $start )
					->with_operator( '>=', [ '>=' ] );
			}

			if ( ! empty( $end ) ) {
				$end          = GravityView_Widget_Search::get_formatted_date( $end, 'Y-m-d', $date_format );
				$conditions[] = $filter
					->with_value( $end )
					->with_operator( '<=', [ '<=' ] );
			}

			if ( empty( $conditions ) ) {
				return $filter;
			}

			return Search_Filter::and( ...$conditions );
		}

		$formatted_date = GravityView_Widget_Search::get_formatted_date( $value, 'Y-m-d', $date_format );
		if ( ! empty( $formatted_date ) && $value !== $formatted_date ) {
			$filter = $filter->with_value( $formatted_date );
		}

		// Preserve range operators if already set (from group processing).
		if ( in_array( $operator, [ '>=', '<=' ], true ) ) {
			return $filter->with_operator( $operator, [ $operator ] );
		}

		if ( 'payment_date' === $filter->key() ) {
			return $filter->with_operator( 'contains', [ 'contains' ] );
		}

		// Single date value uses 'is' operator.
		return $filter->with_operator( $operator, [ 'is' ] );
	}

	/**
	 * Adjusts a filter for number-type fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_number_filter( Search_Filter $filter ): Search_Filter {
		// GF_Query casts Number field values to decimal, which may return unexpected result when the value is blank.
		if ( ! $filter->has_value() ) {
			$filter = $filter->with_value( '-' . PHP_INT_MAX );
		}

		return $this->adjust_numeric_filter( $filter );
	}

	/**
	 * Adjusts a filter for numeric fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_numeric_filter( Search_Filter $filter ): Search_Filter {
		$value = $filter->value();

		// Handle number range (array with min/max).
		if ( ! is_array( $value ) ) {
			return $filter;
		}

		$min = $value['min'] ?? null;
		$max = $value['max'] ?? null;

		if (
			( null !== $min && ! is_numeric( $min ) )
			|| ( null !== $max && ! is_numeric( $max ) )
		) {
			// Invalid values.
			return $filter->with_value( null );
		}

		// Reverse if min > max.
		if ( is_numeric( $min ) && is_numeric( $max ) && $min > $max ) {
			[ $min, $max ] = [ $max, $min ];
		}

		$conditions = [];

		if ( is_numeric( $min ) ) {
			$conditions[] = $filter
				->with_value( $min )
				->with_operator( '>=', [ '>=' ] )
				->with_numeric( true );
		}

		if ( is_numeric( $max ) ) {
			$conditions[] = $filter
				->with_value( $max )
				->with_operator( '<=', [ '<=' ] )
				->with_numeric( true );
		}

		if ( empty( $conditions ) ) {
			return $filter;
		}

		return Search_Filter::and( ...$conditions );
	}

	/**
	 * Adjusts a filter for post_category fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_post_category_filter( Search_Filter $filter ): Search_Filter {
		$value = $filter->value();
		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$conditions = [];
		foreach ( $value as $val ) {
			$cat = get_term( $val, 'category' );
			if ( ! $cat ) {
				continue;
			}

			$conditions[] = $filter
				->with_value( esc_attr( $cat->name ) . ':' . $val )
				->with_operator( $filter->operator(), [ 'is' ] );
		}

		$count = count( $conditions );

		if ( 0 === $count ) {
			return $filter;
		}

		if ( 1 === $count ) {
			return $conditions[0];
		}

		return Search_Filter::or( ...$conditions );
	}

	/**
	 * Adjusts a filter for repeater fields.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The filter to adjust.
	 *
	 * @return Search_Filter The adjusted filter.
	 */
	private function adjust_repeater_filter( Search_Filter $filter ): Search_Filter {
		$field = $this->get_gf_field();
		if ( ! $field ) {
			return $filter;
		}

		$filters = $this->get_nested_fields_filters( $filter, $field );
		if ( ! $filters ) {
			// Remove the filter.
			return $filter->with_value( '' );
		}

		return Search_Filter::or( ...$filters );
	}

	/**
	 * Returns the nested field IDs of fields that have values.
	 *
	 * @since $ver$
	 *
	 * @param Search_Filter $filter The source filter.
	 * @param GF_Field      $field  The field to retrieve the nested field IDs for.
	 *
	 * @return Search_Filter[] The nested field ID's.
	 */
	private function get_nested_fields_filters( Search_Filter $filter, GF_Field $field ): array {
		$result = [];
		foreach ( $field->fields ?? [] as $sub_field ) {
			if ( ! $sub_field instanceof GF_Field_Repeater ) {
				$result[] = [
					$filter
						->with_key( $sub_field->id )
						->with_field_id( $sub_field->id )
						->with_form_id( $field->formId ),
				];

				continue;
			}

			$result[] = $this->get_nested_fields_filters( $filter, $sub_field );
		}

		return array_merge( [], ...$result );
	}
}
