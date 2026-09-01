<?php
/**
 * Ability: gk-gravityview/grid-row-move
 *
 * Moves a grid row within a zone. Per-zone scoped — grid rows are
 * materialized per (surface, zone) and order is encoded in each zone's
 * area-key array.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/grid-row-move/run`
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
				'name'                => 'gk-gravityview/grid-row-move',
				'label'               => __( 'Move Grid Row', 'gk-gravityview' ),
				'description'         => __( 'Move a grid row within a zone. Per-zone scoped: on the fields surface, one row_uid is materialized across multiple zones (directory and single); each zone tracks its own ordering. Cross-zone moves are NOT supported here — use remove + add for that.', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-grid',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'           => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'surface'      => [
							'type'        => 'string',
							'enum'        => [ 'fields', 'widgets' ],
							'description' => __( 'Surface the row belongs to: fields or widgets.', 'gk-gravityview' ),
						],
						'zone'         => [
							'type'        => 'string',
							'description' => __( 'Zone within the surface (e.g., directory or single on the fields surface).', 'gk-gravityview' ),
						],
						'row_uid'      => [
							'type'        => 'string',
							'description' => __( 'Row UID to move.', 'gk-gravityview' ),
						],
						'new_position' => [
							'type'        => 'integer',
							'minimum'     => 0,
							'description' => __( '0-based target index in the zone\'s area-key sequence.', 'gk-gravityview' ),
						],
						'ifMatch'      => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read.', 'gk-gravityview' ),
						],
						'dry_run'      => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'surface', 'zone', 'row_uid', 'new_position' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'  => [ 'type' => 'integer' ],
						'surface'  => [ 'type' => 'string' ],
						'zone'     => [ 'type' => 'string' ],
						'row_uid'  => [ 'type' => 'string' ],
						'position' => [ 'type' => 'integer' ],
						'version'  => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'surface', 'zone', 'row_uid', 'position', 'version' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id      = (int) ( $input['id'] ?? 0 );
					$surface      = (string) ( $input['surface'] ?? '' );
					$zone         = (string) ( $input['zone'] ?? '' );
					$row_uid      = (string) ( $input['row_uid'] ?? '' );
					$new_position = (int) ( $input['new_position'] ?? 0 );

					$row = \GravityKit\GravityView\View\RowSlotIdentity::from_array(
						[
							'view_id' => $view_id,
							'surface' => $surface,
							'zone'    => $zone,
							'row_uid' => $row_uid,
						]
					);
					if ( is_wp_error( $row ) ) {
						return $row;
					}

					$is_dry = (bool) ( $input['dry_run'] ?? false );
					$repo   = new \GravityKit\GravityView\View\Grid\GridRowRepository();

					return \GravityKit\GravityView\Abilities\Bootstrap::with_dry_run(
						$is_dry,
						static function () use ( $repo, $row, $new_position, $input ) {
							return $repo->move(
								$row,
								$new_position,
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
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/grid-row-move' ),
					],
				],
            ]
		);
	}
);
