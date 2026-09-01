<?php
/**
 * Ability: gk-gravityview/view-status-set
 *
 * Change a View's `post_status` (publish / draft / pending / private /
 * trash). Without this ability, callers had to drop out of the
 * inspector surface and PUT to `/wp-json/wp/v2/gravityview/{id}` —
 * which 404s on most installs because the post type isn't REST-enabled
 * for that namespace.
 *
 * Wire location: `/wp-json/wp-abilities/v1/abilities/gk-gravityview/view-status-set/run`
 * HTTP method:   POST (write, idempotent — same status set twice is a no-op)
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
				'name'                => 'gk-gravityview/view-status-set',
				'label'               => __( 'Set View Status', 'gk-gravityview' ),
				'description'         => __( "Change a View's post status (publish / draft / pending / private / trash). Idempotent — calling with the same status twice is a no-op. Returns the new status + the previous status so callers can roll back.", 'gk-gravityview' ),
				'category'            => 'gk-gravityview-views',
				'input_schema'        => [
					'type'                 => 'object',
					'properties'           => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'View post id.', 'gk-gravityview' ),
						],
						'status'  => [
							'type'        => 'string',
							'enum'        => [ 'publish', 'draft', 'pending', 'private', 'trash' ],
							'description' => __( 'New post status.', 'gk-gravityview' ),
						],
						'ifMatch' => [
							'type'        => 'string',
							'description' => __( 'Optimistic-concurrency token from a prior read.', 'gk-gravityview' ),
						],
						'dry_run' => \GravityKit\GravityView\Abilities\Bootstrap::dry_run_input_schema(),
					],
					'required'             => [ 'id', 'status' ],
					'additionalProperties' => false,
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'view_id'         => [ 'type' => 'integer' ],
						'status'          => [
							'type'        => 'string',
							'description' => __( 'Resolved post_status after the write.', 'gk-gravityview' ),
						],
						'previous_status' => [
							'type'        => 'string',
							'description' => __( 'post_status before the write — useful for rollback.', 'gk-gravityview' ),
						],
						'changed'         => [ 'type' => 'boolean' ],
						'dry_run'         => [ 'type' => 'boolean' ],
						'would_apply'     => [ 'type' => 'boolean' ],
						'version'         => [ 'type' => 'string' ],
					],
					'required'   => [ 'view_id', 'status', 'previous_status', 'changed' ],
				],
				'execute_callback'    => static function ( $input ) {
					$view_id = \GravityKit\GravityView\Abilities\Bootstrap::require_view_id( $input );
					if ( is_wp_error( $view_id ) ) {
						return $view_id;
					}
					$post = get_post( $view_id );

					$new_status = (string) ( $input['status'] ?? '' );
					$valid      = [ 'publish', 'draft', 'pending', 'private', 'trash' ];
					if ( ! in_array( $new_status, $valid, true ) ) {
						return new \WP_Error(
							'gv_rest_invalid_status',
							sprintf(
								/* translators: 1: provided status, 2: comma-separated valid statuses */
								__( 'Invalid status "%1$s". Must be one of: %2$s.', 'gk-gravityview' ),
								$new_status,
								implode( ', ', $valid )
							),
							[ 'status' => 400 ]
						);
					}

						$previous = (string) $post->post_status;
					if ( $previous === $new_status ) {
						return [
							'view_id'         => $view_id,
							'status'          => $new_status,
							'previous_status' => $previous,
							'changed'         => false,
							'version'         => ( new \GravityKit\GravityView\View\Concurrency\ViewVersionComputer() )->compute( $view_id ),
						];
					}

						$precondition = ( new \GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker() )->check(
							$view_id,
							isset( $input['ifMatch'] ) ? (string) $input['ifMatch'] : null
						);
					if ( is_wp_error( $precondition ) ) {
						return $precondition;
					}

						$is_dry  = (bool) ( $input['dry_run'] ?? false );
						$planned = [
							'view_id'         => $view_id,
							'status'          => $new_status,
							'previous_status' => $previous,
							'changed'         => true,
						];

						$result = \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_dry_run(
							$is_dry,
							static function () use ( $view_id, $new_status, $planned ) {
								return \GravityKit\GravityView\Foundation\Abilities\Support\DryRun::with_post_write_guard(
									static function () use ( $view_id, $new_status ) {
										if ( 'trash' === $new_status ) {
											return wp_trash_post( $view_id );
										}

										return wp_update_post(
											[
												'ID' => $view_id,
												'post_status' => $new_status,
											],
											true
										);
									},
									$planned
								);
							}
						);

					if ( $is_dry ) {
						return \GravityKit\GravityView\Abilities\Bootstrap::mark_dry_run( $result, true );
					}
					if ( is_wp_error( $result ) ) {
						return $result;
					}
					if ( ! $result ) {
						return new \WP_Error( 'gv_rest_status_change_failed', __( 'Failed to change View status.', 'gk-gravityview' ), [ 'status' => 500 ] );
					}

					return [
						'view_id'         => $view_id,
						'status'          => $new_status,
						'previous_status' => $previous,
						'changed'         => true,
						'version'         => ( new \GravityKit\GravityView\View\Concurrency\ViewVersionComputer() )->compute( $view_id ),
					];
				},
				'permission_callback' => static function ( $input ) {
					$view_id    = (int) ( $input['id'] ?? 0 );
					$new_status = (string) ( $input['status'] ?? '' );
					if ( $view_id <= 0 ) {
						return false;
					}
					// Status-conditional rules (edit baseline + publish for
					// publish/private + delete for trash) centralized in
					// Permissions::can_set_view_status. Returns WP_Error
					// directly so specific deny reason (forbidden_publish vs
					// forbidden_delete) reaches the response.
					return ( new \GravityKit\GravityView\Permissions\Permissions() )
						->can_set_view_status( $view_id, $new_status );
				},
				'meta'                => [
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						// Trash IS destructive — sends to wp_trash_post which removes
						// the View from public visibility and (after retention period)
						// hard-deletes it. Matches DeleteView(force=false), which
						// routes through the same can_set_view_status('trash') gate.
						'destructive' => true,
						'idempotent'  => true,
						'next_steps'  => \GravityKit\GravityView\Abilities\Bootstrap::next_steps_for( 'gk-gravityview/view-status-set' ),
					],
				],
            ]
		);
	}
);
