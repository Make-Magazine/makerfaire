<?php
/**
 * Ability: gk-gravityview/view-delete
 *
 * Delete a View. With force=false (default), soft-deletes by transitioning
 * to trash. With force=true, permanently deletes via wp_delete_post.
 * Matches WP REST DELETE conventions.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-delete/run`
 * HTTP method:   POST (destructive)
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
				'name'                => 'gk-gravityview/view-delete',
				'label'               => __( 'Delete View', 'gk-gravityview' ),
				'description'         => __( 'Delete a View. Defaults to Trash (force=false, recoverable) — proceed with the default; do NOT pause to ask the user trash-vs-permanent. With force=false the View is soft-deleted by transitioning its status to trash — equivalent to view-status-set with status=trash. With force=true, the View is permanently deleted via wp_delete_post. Matches WP REST DELETE conventions (`?force=true|false`).', 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'force'   => [
							'type'        => 'boolean',
							'default'     => false,
							'description' => __( 'When true, permanently delete (skip trash). When false (default), soft-delete via trash.', 'gk-gravityview' ),
						],
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id' => [ 'type' => 'integer' ],
						'deleted' => [ 'type' => 'boolean' ],
						'force'   => [ 'type' => 'boolean' ],
						'version' => [ 'type' => 'string' ],
						'dry_run' => [ 'type' => 'boolean' ],
					],
					'required'   => [ 'view_id', 'deleted', 'force' ],
				],
				'execute_callback'    => static function ( $input ) {
					$payload = \GravityKit\GravityView\View\DeleteViewInput::from_array( (array) $input );
					if ( is_wp_error( $payload ) ) {
						return $payload;
					}

						$deleter = new \GravityKit\GravityView\View\ViewDeleter();
						return $deleter->delete( $payload );
				},
				'permission_callback' => static function ( $input ) {
					$view_id = (int) ( $input['id'] ?? 0 );
					$force   = (bool) ( $input['force'] ?? false );
					$perms   = new \GravityKit\GravityView\Permissions\Permissions();

					// Soft-delete via status transition: gated by trash-status cap.
					if ( ! $force ) {
						return $perms->can_set_view_status( $view_id, 'trash' );
					}

					// Hard delete: needs delete cap.
					return $perms->can_delete_view( $view_id );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => false,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-delete' ),
					],
				],
            ]
		);
	}
);
