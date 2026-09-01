<?php
/**
 * Beaver Builder Integration
 *
 * Provides Beaver Builder-specific field definitions and transformations.
 *
 * @package GravityKit\GravityView\PageBuilder
 * @since 3.0.0
 */

namespace GravityKit\GravityView\PageBuilder\BeaverBuilder;

use GravityKit\GravityView\PageBuilder\PageBuilder;

/** If this file is called directly, abort. */
if ( ! defined( 'GRAVITYVIEW_DIR' ) ) {
	die();
}

/**
 * Beaver Builder-specific integration class.
 *
 * Extends the base PageBuilder with Beaver Builder-specific
 * type mappings and transformations.
 *
 * @since 3.0.0
 */
class BeaverBuilder extends PageBuilder {

	/**
	 * Builder identifier.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	protected $builder = 'beaver';

	/**
	 * Attribute allowlist: Beaver Builder settings use the same camelCase keys as block attributes.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	const ATTRIBUTE_MAPPING = [
		'viewId'         => 'viewId',
		'pageSize'       => 'pageSize',
		'sortField'      => 'sortField',
		'sortDirection'  => 'sortDirection',
		'searchField'    => 'searchField',
		'searchValue'    => 'searchValue',
		'searchOperator' => 'searchOperator',
		'startDate'      => 'startDate',
		'endDate'        => 'endDate',
		'offset'         => 'offset',
		'classValue'     => 'classValue',
		'singleTitle'    => 'singleTitle',
		'backLinkLabel'  => 'backLinkLabel',
		'postId'         => 'postId',
	];

	/**
	 * Map generic field type to Beaver Builder-specific type.
	 *
	 * @since 3.0.0
	 *
	 * @param string $generic_type Generic type: 'text', 'number', 'select', etc.
	 *
	 * @return string Beaver Builder-specific type.
	 */
	protected function map_field_type( $generic_type ) {
		$type_mapping = [
			'text'   => 'text',
			'number' => 'unit',   // Beaver Builder uses 'unit' for numbers.
			'select' => 'select',
		];

		return $type_mapping[ $generic_type ] ?? $generic_type;
	}

	/**
	 * Enqueue the sort field script for dynamic, per-View sort field loading.
	 *
	 * Registers and enqueues a JS file that dynamically updates the Sort Field
	 * <select> options based on the currently selected View. Preloads per-View
	 * sort field options as inline data (no AJAX).
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function enqueue_sort_field_script() {
		$handle = 'gk-gravityview-bb-sort-field';

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$path = \GravityKit\GravityView\Utils\Assets::path( 'js/page-builder/gv-builder-sort-field.js' );

			wp_register_script(
				$handle,
				\GravityKit\GravityView\Utils\Assets::url( 'js/page-builder/gv-builder-sort-field.js' ),
				[],
				is_readable( $path ) ? filemtime( $path ) : GV_PLUGIN_VERSION,
				true
			);

			wp_localize_script( $handle, 'gkGravityViewBuilderSortField', [
				'defaultLabel'     => esc_html__( 'Default', 'gk-gravityview' ),
				'sortFieldsByView' => $this->get_sort_fields_by_view(),
			] );
		}

		wp_enqueue_script( $handle );
	}

	/**
	 * Build a mapping of View ID → sortable field options.
	 *
	 * Precomputes the sort field options for each published View so the JS
	 * can instantly filter the Sort Field dropdown without AJAX round-trips.
	 *
	 * @since 3.0.0
	 *
	 * @return array<int, array<array{value: string, label: string}>> View ID → sort field options.
	 */
	protected function get_sort_fields_by_view() {
		$mapping = [];

		foreach ( array_keys( $this->get_views_list() ) as $view_id ) {
			$mapping[ $view_id ] = $this->get_sort_field_options_for_view( $view_id );
		}

		return $mapping;
	}
}
