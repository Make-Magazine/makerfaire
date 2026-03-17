<?php

namespace GV\Search\Policies;

use GravityView_Field_Repeater;
use GravityView_Widget_Search;
use GV\GF_Form;
use GV\View;
use GV\Widget_Collection;
use GVCommon;
use JsonException;

/**
 * The Search Fields Policy handles View-dependent field searchability rules.
 *
 * It answers questions like:
 * - What fields are searchable for a View?
 * - Is a specific field searchable?
 * - Is a form valid for a View?
 *
 * @since $ver$
 */
final class Search_Fields_Policy {
	/**
	 * Cache for searchable fields per View.
	 *
	 * @since $ver$
	 *
	 * @var array<string, array>
	 */
	private static array $searchable_fields_cache = [];

	/**
	 * The View instance.
	 *
	 * @since $ver$
	 *
	 * @var View
	 */
	private View $view;

	/**
	 * Creates a new Search_Fields_Policy instance.
	 *
	 * @since $ver$
	 *
	 * @param View $view The View.
	 */
	public function __construct( View $view ) {
		$this->view = $view;
	}

	/**
	 * Clears all internal caches.
	 *
	 * @since    $ver$
	 *
	 * @internal Used for testing purposes only.
	 */
	public static function clear_cache(): void {
		self::$searchable_fields_cache = [];
	}

	/**
	 * Returns the fields (or its keys) that are searchable.
	 *
	 * @since $ver$
	 *
	 * @param bool $with_full_field Whether to return the entire field.
	 *
	 * @return string[]|array[]
	 */
	private function get_searchable_fields( bool $with_full_field = false ): array {
		$cache_key = $this->view->ID . '_' . ( $with_full_field ? '1' : '0' );
		if ( isset( self::$searchable_fields_cache[ $cache_key ] ) ) {
			return self::$searchable_fields_cache[ $cache_key ];
		}

		$searchable_fields = array_merge(
			$this->get_searchable_fields_from_sidebar_widgets(),
			$this->get_searchable_fields_from_gravityview_widgets(),
		);

		if ( ! $with_full_field ) {
			$searchable_fields = array_column( $searchable_fields, 'field' );
			$searchable_fields = array_values( array_unique( $searchable_fields ) );
		}

		/**
		 * @deprecated 2.14 Use `gk/gravityview/search/searchable-fields/allowed`.
		 */
		$searchable_fields = apply_filters_deprecated(
			'gravityview/search/searchable_fields/whitelist',
			[ $searchable_fields, $this->view, $with_full_field ],
			'2.14',
			'gravityview/search/searchable_fields/allowlist'
		);

		/**
		 * @deprecated $ver$ Use `gk/gravityview/search/searchable-fields/allowed`.
		 */
		$searchable_fields = apply_filters_deprecated(
			'gravityview/search/searchable_fields/allowlist',
			[ $searchable_fields, $this->view, $with_full_field ],
			'$ver$',
			'gk/gravityview/search/searchable-fields/allowed'
		);

		/**
		 * Modifies the fields able to be searched using the Search Bar.
		 *
		 * @since  $ver$
		 *
		 * @param array $searchable_fields Array of GravityView-formatted fields or field IDs.
		 * @param View  $view              Object of View being searched.
		 * @param bool  $with_full_field   Whether $searchable_fields contains full field arrays or just IDs.
		 */
		$searchable_fields = apply_filters(
			'gk/gravityview/search/searchable-fields/allowed',
			$searchable_fields,
			$this->view,
			$with_full_field
		);

		self::$searchable_fields_cache[ $cache_key ] = $searchable_fields;

		return $searchable_fields;
	}

	/**
	 * Returns whether the field ID is searchable.
	 *
	 * @since $ver$
	 *
	 * @param string   $field_id The field ID.
	 * @param int|null $form_id  The optional Form ID.
	 *
	 * @return bool Whether the field is searchable.
	 */
	public function is_field_searchable( string $field_id, ?int $form_id = null ): bool {
		$searchable_fields = $this->get_searchable_fields( true );
		$searchable_fields_ids = array_column( $searchable_fields, 'field' );

		if ( ! $form_id ) {

			// Either the field or ALL fields are searchable.
			return [] !== array_intersect( [ 'search_all', $field_id ], $searchable_fields_ids );
		}

		// Form is not in this View.
		if ( ! $this->is_valid_form( $form_id ) ) {
			return false;
		}

		// All valid form's fields are allowed, when `search_all` is available.
		if ( in_array( 'search_all', $searchable_fields_ids, true ) ) {
			return true;
		}

		foreach ( $searchable_fields as $search_field ) {
			if (
				$field_id === (string) $search_field['field']
				&& $form_id === (int) $search_field['form_id']
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns whether the form ID is valid for the View.
	 *
	 * @since $ver$
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return bool Whether the form ID is valid.
	 */
	private function is_valid_form( int $form_id ): bool {
		if ( ! $form_id ) {
			return false;
		}

		$form = GF_Form::by_id( $form_id );
		if ( ! $form ) {
			return false;
		}

		// It's the View form ID.
		if ( ( $this->view->form->ID ?? 0 ) === $form->ID ) {
			return true;
		}

		$forms = View::get_joined_forms( $this->view->ID );
		foreach ( $forms as $joined_form ) {
			if ( (int) ( $joined_form->ID ?? null ) === $form_id ) {
				// Form is one of the joined forms.
				return true;
			}
		}

		return false;
	}

	/**
	 * Return searchable fields from sidebar widgets.
	 *
	 * @since $ver$
	 *
	 * @return array The search fields.
	 */
	private function get_searchable_fields_from_sidebar_widgets(): array {
		$widgets = (array) get_option( 'widget_gravityview_search', [] );

		$searchable_fields = [];
		if ( ! $widgets ) {
			return $searchable_fields;
		}

		foreach ( $widgets as $widget ) {
			if ( ! is_array( $widget ) ) {
				continue;
			}

			if (
				empty( $widget['view_id'] )
				|| ( (int) $widget['view_id'] !== (int) $this->view->ID )
			) {
				continue;
			}

			$fields = $widget['search_fields'] ?? null;
			if ( ! $fields ) {
				continue;
			}

			if ( is_string( $fields ) ) {
				try {
					$fields = json_decode( $fields, true, 512, JSON_THROW_ON_ERROR );
				} catch ( JsonException $e ) {
					$fields = [];
				}
			}

			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field ) {
				if ( empty( $field['form_id'] ) ) {
					$field['form_id'] = $this->view->form ? $this->view->form->ID : 0;
				}
				$searchable_fields[] = $field;
			}
		}

		return $searchable_fields;
	}

	/**
	 * Return searchable fields from GravityView widgets.
	 *
	 * @since $ver$
	 *
	 * @return array The search fields.
	 */
	private function get_searchable_fields_from_gravityview_widgets(): array {
		$search_widget     = new GravityView_Widget_Search();
		$searchable_fields = [];

		$search_widgets = $this->view->widgets->by_id( $search_widget->get_widget_id() );

		// For nested/embedded Views, the widgets-collection may be cleared by the shortcode handler.
		// Reload widgets from the View's raw configuration if needed.
		if ( $search_widgets->count() === 0 ) {
			$widgets_config = (array) GVCommon::get_directory_widgets( $this->view->ID );
			if ( $widgets_config ) {
				$widgets_collection = Widget_Collection::from_configuration( $widgets_config );
				$search_widgets     = $widgets_collection->by_id( $search_widget->get_widget_id() );
			}
		}

		foreach ( $search_widgets->all() as $widget ) {
			if ( ! $widget instanceof GravityView_Widget_Search ) {
				continue;
			}

			foreach ( $widget->get_search_fields( $this->view ) as $field ) {
				if ( empty( $field['form_id'] ) ) {
					$field['form_id'] = $this->view->form ? $this->view->form->ID : 0;
				}
				$searchable_fields[] = $field;
			}
		}

		// Add subfields of searchable repeater fields.
		return $this->add_repeater_subfields( $searchable_fields );
	}

	/**
	 * Adds subfields of searchable repeater fields to the searchable fields list.
	 *
	 * @since $ver$
	 *
	 * @param array $searchable_fields The searchable fields.
	 *
	 * @return array The searchable fields with repeater subfields added.
	 */
	private function add_repeater_subfields( array $searchable_fields ): array {
		$additional_fields       = [];
		$repeater_fields_by_form = [];

		foreach ( $searchable_fields as $search_field ) {
			$form_id = (int) ( $search_field['form_id'] ?? 0 );
			if ( ! $form_id ) {
				continue;
			}

			$repeater_fields_by_form[ $form_id ] ??= GravityView_Field_Repeater::get_repeater_field_ids( $form_id );

			// Find all children of this field if it is a repeater.
			foreach ( $repeater_fields_by_form[ $form_id ] as $child_id => $parent_ids ) {
				if ( ! in_array( (int) $search_field['field'], $parent_ids, true ) ) {
					continue;
				}

				$additional_fields[] = [
					'field'   => (string) $child_id,
					'form_id' => $form_id,
					'input'   => $search_field['input'] ?? '',
				];
			}
		}

		return array_merge( $searchable_fields, $additional_fields );
	}
}
