<?php
/**
 * GravityView entry selection descriptor.
 *
 * @package GravityKit\GravityView\Entry\BackgroundJobs
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BackgroundJobs;

/**
 * Describes entries selected from a GravityView View without storing an
 * unbounded POST payload.
 *
 * @since 3.0.0
 */
final class EntrySelection {
	public const MODE_EXPLICIT = 'explicit';

	public const MODE_ALL = 'all';

	private const MAX_REQUEST_ARGS = 100;

	private const MAX_REQUEST_VALUE_LENGTH = 500;

	/**
	 * Selection mode.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private $mode;

	/**
	 * Explicitly selected entry IDs.
	 *
	 * @since 3.0.0
	 *
	 * @var int[]
	 */
	private $entry_ids = [];

	/**
	 * Entry IDs excluded from a select-all selection.
	 *
	 * @since 3.0.0
	 *
	 * @var int[]
	 */
	private $excluded_ids = [];

	/**
	 * Request arguments needed to rebuild the View result set.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $request_args = [];

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $mode         Selection mode.
	 * @param array  $entry_ids    Explicit entry IDs.
	 * @param array  $excluded_ids Excluded entry IDs for select-all mode.
	 * @param array  $request_args Request arguments needed to rebuild the View result set.
	 */
	public function __construct( string $mode, array $entry_ids = [], array $excluded_ids = [], array $request_args = [] ) {
		$this->mode         = self::MODE_ALL === $mode ? self::MODE_ALL : self::MODE_EXPLICIT;
		$this->entry_ids    = self::normalize_entry_ids( $entry_ids );
		$this->excluded_ids = self::normalize_entry_ids( $excluded_ids );
		$this->request_args = self::normalize_request_args( $request_args );

		if ( self::MODE_ALL === $this->mode ) {
			$this->entry_ids = [];
		} else {
			$this->excluded_ids = [];
		}
	}

	/**
	 * Creates an explicit entry-ID selection.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entry_ids    Entry IDs.
	 * @param array $request_args Request arguments needed to rebuild the View result set.
	 *
	 * @return self
	 */
	public static function from_entry_ids( array $entry_ids, array $request_args = [] ): self {
		return new self( self::MODE_EXPLICIT, $entry_ids, [], $request_args );
	}

	/**
	 * Creates a select-all selection.
	 *
	 * @since 3.0.0
	 *
	 * @param array $excluded_ids Entry IDs excluded from the select-all selection.
	 * @param array $request_args Request arguments needed to rebuild the View result set.
	 *
	 * @return self
	 */
	public static function all( array $excluded_ids = [], array $request_args = [] ): self {
		return new self( self::MODE_ALL, [], $excluded_ids, $request_args );
	}

	/**
	 * Restores a selection from a stored array.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data Stored selection data.
	 *
	 * @return self
	 */
	public static function from_array( array $data ): self {
		return new self(
			(string) ( $data['mode'] ?? self::MODE_EXPLICIT ),
			(array) ( $data['entry_ids'] ?? [] ),
			(array) ( $data['excluded_ids'] ?? [] ),
			(array) ( $data['request_args'] ?? [] )
		);
	}

	/**
	 * Converts the selection to a JSON-safe array.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		return [
			'mode'         => $this->mode,
			'entry_ids'    => $this->entry_ids,
			'excluded_ids' => $this->excluded_ids,
			'request_args' => $this->request_args,
		];
	}

	/**
	 * Returns whether the selection represents all matching View results.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_all(): bool {
		return self::MODE_ALL === $this->mode;
	}

	/**
	 * Returns whether the selection contains explicit entry IDs.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_explicit(): bool {
		return ! $this->is_all();
	}

	/**
	 * Returns the selection mode.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function mode(): string {
		return $this->mode;
	}

	/**
	 * Returns explicit entry IDs.
	 *
	 * @since 3.0.0
	 *
	 * @return int[]
	 */
	public function entry_ids(): array {
		return $this->entry_ids;
	}

	/**
	 * Returns excluded entry IDs.
	 *
	 * @since 3.0.0
	 *
	 * @return int[]
	 */
	public function excluded_ids(): array {
		return $this->excluded_ids;
	}

	/**
	 * Returns captured request arguments.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function request_args(): array {
		return $this->request_args;
	}

	/**
	 * Returns the known number of entries, when known without querying.
	 *
	 * @since 3.0.0
	 *
	 * @return int|null
	 */
	public function known_count(): ?int {
		return $this->is_explicit() ? count( $this->entry_ids ) : null;
	}

	/**
	 * Normalizes an entry ID list.
	 *
	 * @since 3.0.0
	 *
	 * @param array $entry_ids Entry IDs.
	 *
	 * @return int[]
	 */
	public static function normalize_entry_ids( array $entry_ids ): array {
		$entry_ids = array_filter(
			array_map( 'absint', $entry_ids ),
			static function ( int $entry_id ): bool {
				return $entry_id > 0;
			}
		);

		return array_values( array_unique( $entry_ids ) );
	}

	/**
	 * Normalizes request arguments for storage.
	 *
	 * @since 3.0.0
	 *
	 * @param array $request_args Request arguments.
	 *
	 * @return array
	 */
	private static function normalize_request_args( array $request_args ): array {
		$normalized = [];
		$count      = 0;

		foreach ( $request_args as $key => $value ) {
			if ( $count >= self::MAX_REQUEST_ARGS ) {
				break;
			}

			if ( ! is_scalar( $key ) ) {
				continue;
			}

			$key = preg_replace( '/[^a-zA-Z0-9_.-]/', '', (string) $key );

			if ( '' === $key ) {
				continue;
			}

			$value = self::normalize_request_value( $value );

			if ( null === $value ) {
				continue;
			}

			$normalized[ $key ] = $value;
			++$count;
		}

		return $normalized;
	}

	/**
	 * Normalizes a request argument value.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $value Request value.
	 *
	 * @return mixed|null
	 */
	private static function normalize_request_value( $value ) {
		if ( is_array( $value ) ) {
			return self::normalize_request_args( $value );
		}

		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
			return $value;
		}

		if ( is_scalar( $value ) ) {
			return substr( sanitize_text_field( wp_unslash( (string) $value ) ), 0, self::MAX_REQUEST_VALUE_LENGTH );
		}

		return null;
	}
}
