<?php
/**
 * Input value object for view-delete operations.
 *
 * @package     GravityKit\GravityView\View
 * @license     GPL2+
 * @since       TBD
 */

namespace GravityKit\GravityView\View;

use GravityKit\GravityView\Contracts\Input;
use WP_Error;

/**
 * Validated input for `ViewDeleter::delete()`.
 *
 * `force=false` → soft-delete via status transition to trash.
 * `force=true`  → permanent delete via wp_delete_post(force_delete=true).
 *
 * Matches WP REST DELETE conventions (DELETE /views/{id}?force=true|false).
 *
 * @since 3.0.0
 */
final class DeleteViewInput implements Input {

	/**
	 * The View post id.
	 *
	 * @since 3.0.0
	 *
	 * @var int
	 */
	private int $view_id;

	/**
	 * Whether to force-delete (permanent removal).
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private bool $force;

	/**
	 * Optimistic-concurrency token (If-Match header value).
	 *
	 * @since 3.0.0
	 *
	 * @var string|null
	 */
	private ?string $if_match;

	/**
	 * Whether this is a dry-run.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private bool $dry_run;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param int         $view_id View post id.
	 * @param bool        $force Force-delete flag.
	 * @param string|null $if_match If-Match token.
	 * @param bool        $dry_run Dry-run flag.
	 */
	public function __construct( int $view_id, bool $force = false, ?string $if_match = null, bool $dry_run = false ) {
		$this->view_id  = $view_id;
		$this->force    = $force;
		$this->if_match = $if_match;
		$this->dry_run  = $dry_run;
	}

	/**
	 * Construct an instance from a parsed payload array. Returns WP_Error on invalid input.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data Parsed payload array.
	 *
	 * @return self|WP_Error
	 */
	public static function from_array( array $data ) {
		$view_id = isset( $data['id'] ) ? (int) $data['id'] : 0;
		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gv_rest_invalid_input',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		// FILTER_VALIDATE_BOOLEAN correctly handles "false"/"true"/"0"/"1"
		// strings from REST query parameters. (bool) cast treats any
		// non-empty string as true — including "false".
		$force = isset( $data['force'] )
			? (bool) filter_var( $data['force'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE )
			: false;

		return new self(
			$view_id,
			$force,
			isset( $data['if_match'] ) ? (string) $data['if_match']
				: ( isset( $data['ifMatch'] ) ? (string) $data['ifMatch'] : null ),
			self::flag( $data, 'dry_run' )
		);
	}

	/**
	 * Get the view_id.
	 *
	 * @since 3.0.0
	 */
	public function view_id(): int {
		return $this->view_id;
	}

	/**
	 * Returns true when force-delete (permanent) was requested.
	 *
	 * @since 3.0.0
	 */
	public function force(): bool {
		return $this->force;
	}

	/**
	 * Get the optimistic-concurrency token (If-Match).
	 *
	 * @since 3.0.0
	 */
	public function if_match(): ?string {
		return $this->if_match;
	}

	/**
	 * Returns true when this is a dry-run.
	 *
	 * @since 3.0.0
	 */
	public function dry_run(): bool {
		return $this->dry_run;
	}

	/**
	 * Parse a REST boolean flag.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $data Parsed payload.
	 * @param string $key  Flag key.
	 * @return bool
	 */
	private static function flag( array $data, string $key ): bool {
		if ( ! array_key_exists( $key, $data ) ) {
			return false;
		}

		return (bool) filter_var( $data[ $key ], FILTER_VALIDATE_BOOLEAN );
	}
}
