<?php
/**
 * Deletes GravityView Views with concurrency and dry-run support.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\Foundation\Abilities\Support\DryRun;
use GravityKit\GravityView\View\Concurrency\ViewLock;
use GravityKit\GravityView\View\Concurrency\ViewPreconditionChecker;
use GravityKit\GravityView\View\Concurrency\ViewVersionComputer;
use WP_Error;

/**
 * View delete service.
 *
 * @since 3.0.0
 */
final class ViewDeleter {
	use SlotRepositoryTrait;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param ViewLock|null                $lock         View-lock service.
	 * @param ViewPreconditionChecker|null $precondition Precondition checker.
	 * @param ViewVersionComputer|null     $version      Version computer.
	 */
	public function __construct(
		?ViewLock $lock = null,
		?ViewPreconditionChecker $precondition = null,
		?ViewVersionComputer $version = null
	) {
		$this->init_slot_repository_services( $lock, $precondition, $version );
	}

	/**
	 * Delete a View.
	 *
	 * @since 3.0.0
	 *
	 * @param DeleteViewInput $input Delete input.
	 * @return array<string,mixed>|WP_Error
	 */
	public function delete( DeleteViewInput $input ) {
		$view_id = $input->view_id();

		return $this->with_lock(
			$view_id,
			function () use ( $input, $view_id ) {
				$pre = $this->check_precondition( $view_id, $input->if_match() );
				if ( is_wp_error( $pre ) ) {
					return $pre;
				}

				$post = get_post( $view_id );
				if ( ! $post || 'gravityview' !== $post->post_type ) {
					return new WP_Error(
						'gv_rest_view_not_found',
						__( 'The View could not be found.', 'gk-gravityview' ),
						[
							'status'  => 404,
							'view_id' => $view_id,
						]
					);
				}

				$force           = $input->force();
				$previous_status = (string) $post->post_status;
				$title           = (string) $post->post_title;
				$permalink       = get_permalink( $view_id );
				$planned         = [
					'view_id'         => $view_id,
					'deleted'         => false,
					'force'           => $force,
					'mode'            => $force ? 'force' : 'trash',
					'title'           => $title,
					'previous_status' => $previous_status,
					'permalink'       => is_string( $permalink ) ? $permalink : '',
				];

				return DryRun::with_dry_run(
					$input->dry_run(),
					function () use ( $force, $planned, $view_id ) {
						$result = DryRun::with_post_write_guard(
							function () use ( $force, $view_id ) {
								return $force ? wp_delete_post( $view_id, true ) : wp_trash_post( $view_id );
							},
							$planned
						);

						if ( is_array( $result ) && ! empty( $result['dry_run'] ) ) {
							return $result;
						}

						if ( ! $result ) {
							return new WP_Error(
								'gv_rest_delete_failed',
								__( 'Failed to delete the View.', 'gk-gravityview' ),
								[
									'status'  => 500,
									'view_id' => $view_id,
								]
							);
						}

						$response            = $planned;
						$response['deleted'] = true;

						// version applies only to trash: the View still exists and is
						// bumped for concurrency. A force-deleted View is gone, so the
						// key is omitted — the output schema declares it optional, and
						// null fails its string type.
						if ( ! $force ) {
							$version = $this->bump_version( $view_id );
							if ( '' === $version ) {
								return new WP_Error(
									'gv_rest_version_bump_failed',
									__( 'The View was trashed but could not be marked updated.', 'gk-gravityview' ),
									[ 'status' => 500 ]
								);
							}
							$response['version'] = $version;
						}

						/**
						 * Fires after a View has been deleted via the abilities/REST surface.
						 *
						 * @since 3.0.0
						 *
						 * @param int  $view_id The deleted View id.
						 * @param bool $force   True for force-delete (post hard-removed), false for trash.
						 */
						do_action( 'gk/gravityview/rest/view/deleted', $view_id, $force );

						return $response;
					}
				);
			}
		);
	}
}
