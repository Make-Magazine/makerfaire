<?php
/**
 * Ability: gk-gravityview/search-field-move
 *
 * Moves a search field within (or across) positions inside the search_bar
 * widget's search_fields_section. Search fields use 5-piece identity:
 * (view_id, widget_area, widget_slot, position, search_slot).
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/search-field-move/run`
 * HTTP method:   POST (write)
 *
 * @since 3.0.0
 *
 * @package GravityView
 */

defined( 'ABSPATH' ) || exit;

add_action(
    'gk/foundation/abilities/register/before',
    static function () {
		GravityKitFoundation::abilities()->register(
            [
				'name'                => 'gk-gravityview/search-field-move',
				'label'               => __( 'Move Search Field', 'gk-gravityview' ),
				'description'         => __( 'Move a search field within (or across) positions in the search bar. Search fields are nested under a specific search_bar widget identified by (widget_area, widget_slot), at search_fields_section[position][search_slot]. Use this to reorder fields within one position bucket or to move across position keys. Locate the target search_bar widget (its widget_area + widget_slot) and the search_slot/position uids in the widgets tree from gv_view_config_get — there is no auto-resolution to a single bar, and uids are server-minted, so re-read them from gv_view_config_get after any structural change.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-search-fields',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'               => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'widget_area'      => [
							'type'        => 'string',
							'description' => __( 'Widget area containing the search_bar widget.', 'gk-gravityview' ),
						],
						'widget_slot'      => [
							'type'        => 'string',
							'description' => __( 'Slot UID of the search_bar widget within widget_area.', 'gk-gravityview' ),
						],
						'from_position'    => [
							'type'        => 'string',
							'description' => __( 'Source position bucket key in search_fields_section.', 'gk-gravityview' ),
						],
						'from_search_slot' => [
							'type'        => 'string',
							'description' => __( 'Source search_slot UID within the from_position bucket.', 'gk-gravityview' ),
						],
						'to_position'      => [
							'type'        => 'string',
							'description' => __( 'Destination position bucket key. May be the same as from_position to reorder within the bucket.', 'gk-gravityview' ),
						],
						'to_search_slot'   => [
							'type'        => 'string',
							'description' => __( 'Destination search_slot UID for ordering within to_position. Existing fields at that key shift to make room. When omitted, the field appends to the bucket.', 'gk-gravityview' ),
						],
						'ifMatch'          => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read.', 'gk-gravityview' ),
						],
						'dry_run'          => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'widget_area', 'widget_slot', 'from_position', 'from_search_slot', 'to_position' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'     => [ 'type' => 'integer' ],
						'widget_area' => [ 'type' => 'string' ],
						'widget_slot' => [ 'type' => 'string' ],
						'position'    => [ 'type' => 'string' ],
						'search_slot' => [ 'type' => 'string' ],
						'version'     => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'widget_area', 'widget_slot', 'position', 'search_slot', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id          = (int) ( $input['id'] ?? 0 );
					$widget_area      = (string) ( $input['widget_area'] ?? '' );
					$widget_slot      = (string) ( $input['widget_slot'] ?? '' );
					$from_position    = (string) ( $input['from_position'] ?? '' );
					$from_search_slot = (string) ( $input['from_search_slot'] ?? '' );
					$to_position      = (string) ( $input['to_position'] ?? '' );
					$to_search_slot   = isset( $input['to_search_slot'] ) ? (string) $input['to_search_slot'] : null;

					$from = \GravityKit\GravityView\View\SearchFieldIdentity::from_array(
						[
							'view_id'     => $view_id,
							'widget_area' => $widget_area,
							'widget_slot' => $widget_slot,
							'position'    => $from_position,
							'search_slot' => $from_search_slot,
						]
					);
					if ( is_wp_error( $from ) ) {
						return $from;
					}

					$is_dry = (bool) ( $input['dry_run'] ?? false );
					$repo   = new \GravityKit\GravityView\View\Search\SearchFieldSlotRepository();

					return \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $repo, $from, $to_position, $to_search_slot, $input ) {
							return $repo->move(
								$from,
								$to_position,
								$to_search_slot,
								isset( $input['ifMatch'] ) ? (string) $input['ifMatch'] : null
							);
						}
					);
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					if ( $view_id <= 0 ) {
						return false;
					}
					return ( new \GravityKit\GravityView\Permissions\Permissions() )->can_modify_view_child_slot( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/search-field-move' ),
					],
				],
            ]
		);
	}
);
