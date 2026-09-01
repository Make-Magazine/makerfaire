<?php

namespace GravityKit\GravityView\Foundation\AccountConnection\Storage;

use GravityKit\GravityView\Foundation\AccountConnection\ConnectClient;
use GravityKit\GravityView\Foundation\AccountConnection\TokenStore;
use WP_Error;

/**
 * Account storage for consuming products: versioned key-value entries in the connected
 * GravityKit.com account, over the signed channel.
 *
 * Consumers reach this ONLY through the winning Foundation copy's facade
 * (`GravityKitFoundation::account_storage()`; a null return means the winning copy predates this
 * component). Namespaces are product slugs; keys are the consumer's vocabulary (encode dimensions
 * in the key, e.g. `user:5:prefs` or `blog:7:user:5:prefs` under a network connection — nothing
 * is auto-prefixed). All inputs and outputs are plain arrays and WP_Error.
 *
 * Entry versioning: every entry carries a server-owned monotonic `version`; pass
 * `expected_version` to write/delete for compare-and-set (a conflict returns the current entry
 * in the error data for merging). `schema_version` is the consumer-owned payload format marker,
 * stored and returned but never interpreted — migrate your own value on read.
 *
 * The `scope` arg selects the connection slot ('site' default, 'network' explicit — never
 * auto-escalated and never derived from request input). It is NOT a capability check: callers
 * exposing network storage to users own their own authorization gate.
 *
 * @since 1.26.0
 */
final class AccountStorage {
	/**
	 * The connection scope this component requires.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	const SCOPE = 'storage';

	/**
	 * Catch-all namespace used when a caller does not name a product.
	 *
	 * @since 1.26.0
	 *
	 * @var string
	 */
	const DEFAULT_NAMESPACE = 'gravitykit';

	/**
	 * Advisory client-side cap on the canonical value size (1 MB); the server is authoritative.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_VALUE_BYTES = 1048576;

	/**
	 * Advisory client-side cap on raw file bytes; the server is authoritative.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_FILE_BYTES = 10485760;

	/**
	 * Maximum keys per read_many call.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MAX_KEYS_PER_READ = 50;

	/**
	 * Default mirror freshness window in seconds.
	 *
	 * @since 1.26.0
	 *
	 * @var int
	 */
	const MIRROR_TTL = 300;

	/**
	 * Error codes on which the last-synced mirror entry may be served stale: pure transport
	 * failure only. Authorization and response-integrity failures never serve the mirror.
	 *
	 * @since 1.26.0
	 *
	 * @var string[]
	 */
	const STALE_SERVABLE_CODES = [ 'gk_connection_offline' ];

	/**
	 * Singleton instance.
	 *
	 * @since 1.26.0
	 *
	 * @var self|null
	 */
	private static $instance;

	/**
	 * Builds a ConnectClient for a resolved store; injectable for tests.
	 *
	 * @since 1.26.0
	 *
	 * @var callable
	 */
	private $client_factory;

	/**
	 * Whether init() already ran.
	 *
	 * @since 1.26.0
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Constructor.
	 *
	 * @since 1.26.0
	 *
	 * @param callable|null $client_factory Optional factory: fn( TokenStore ): ConnectClient.
	 */
	public function __construct(?callable $client_factory = null ) {
		$this->client_factory = $client_factory ?? static function ( TokenStore $store ) {
			return new ConnectClient( $store );
		};
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @since 1.26.0
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hooks the mirror purge to explicit disconnects.
	 *
	 * @since 1.26.0
	 *
	 * @return void
	 */
	public function init(): void {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;

		// Every connection requests `storage` here so consuming products just call the API and never
		// touch scopes — each Foundation domain supplies its own scope this way.
		add_filter( 'gk/foundation/connection/requested-scopes', [ $this, 'add_storage_scope' ] );

		add_action( 'gk/foundation/connection/disconnected', [ $this, 'on_disconnected' ] );
	}

	/**
	 * Adds the `storage` scope to every connection handshake.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $scopes Requested scopes (filter input; normalized to an array).
	 *
	 * @return array
	 */
	public function add_storage_scope( $scopes ): array {
		$scopes   = is_array( $scopes ) ? $scopes : [];
		$scopes[] = self::SCOPE;

		return array_values( array_unique( $scopes ) );
	}

	/**
	 * Purges the disconnected scope's mirror: its entries are dead with the connection.
	 *
	 * @since 1.26.0
	 *
	 * @param array $context Event context with the connection `scope`.
	 *
	 * @return void
	 */
	public function on_disconnected( $context ): void {
		$scope = is_array( $context ) && 'network' === ( $context['scope'] ?? '' ) ? 'network' : 'site';

		( new StorageMirror( $scope ) )->purge();
	}

	/**
	 * Reads one entry.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace.
	 * @param string $key       Storage key.
	 * @param array  $args      {
	 *     Optional arguments.
	 *
	 *     @type string $scope Connection slot. Default: 'site'. Accepts 'network' or 'site'.
	 *     @type bool   $fresh Bypass the mirror freshness window and force a network read.
	 * }
	 *
	 * @return array|WP_Error {value, version, schema_version, updated_at, stale} or an error
	 *                        (not_found, scope_not_granted, gk_connection_missing, transport codes).
	 */
	public function read( string $namespace, string $key, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$namespace = $context['namespace'];

		$mirror = $context['mirror'];
		$entry  = $mirror->get( $context['fingerprint'], $namespace, $key );

		if ( empty( $args['fresh'] ) && null !== $entry && $this->is_fresh( $entry ) ) {
			return $this->entry_output( $entry, false );
		}

		$result = $context['client']->signed_request(
			'POST',
			'/storage/get',
			[
				'namespace' => $namespace,
				'key'       => $key,
			]
		);

		if ( $result instanceof WP_Error ) {
			return $this->read_failure( $result, $context, $namespace, [ $key => $entry ], false );
		}

		$valid = $this->validate_response( $result, 'entry' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		$fetched = $result['entry'];

		$mirror->put( $context['fingerprint'], $namespace, $key, $fetched + [ 'synced_at' => time() ] );

		return $this->entry_output( $fetched, false );
	}

	/**
	 * Reads up to {@see self::MAX_KEYS_PER_READ} entries in one signed round trip.
	 *
	 * @since 1.26.0
	 *
	 * @param string   $namespace Product namespace.
	 * @param string[] $keys      Storage keys.
	 * @param array    $args      Optional arguments (see {@see self::read()}).
	 *
	 * @return array|WP_Error {entries: {key => entry}, missing: string[], stale: bool} or an error.
	 */
	public function read_many( string $namespace, array $keys, array $args = [] ) {
		if ( empty( $keys ) || count( $keys ) > self::MAX_KEYS_PER_READ ) {
			return new WP_Error( 'invalid_request', 'Between 1 and ' . self::MAX_KEYS_PER_READ . ' keys are required.' );
		}

		$keys    = array_values( array_unique( $keys ) );
		$context = $this->resolve( $namespace, $keys, $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$namespace = $context['namespace'];

		$mirror   = $context['mirror'];
		$mirrored = [];

		foreach ( $keys as $key ) {
			$mirrored[ $key ] = $mirror->get( $context['fingerprint'], $namespace, $key );
		}

		// Serve entirely from the mirror only when every requested key is present and fresh; any
		// gap means one network call for the whole batch.
		if ( empty( $args['fresh'] ) ) {
			$all_fresh = true;

			foreach ( $mirrored as $entry ) {
				if ( null === $entry || ! $this->is_fresh( $entry ) ) {
					$all_fresh = false;
					break;
				}
			}

			if ( $all_fresh ) {
				return [
					'entries' => array_map( [ $this, 'entry_fields' ], $mirrored ),
					'missing' => [],
					'stale'   => false,
				];
			}
		}

		$result = $context['client']->signed_request(
			'POST',
			'/storage/get-many',
			[
				'namespace' => $namespace,
				'keys'      => $keys,
			]
		);

		if ( $result instanceof WP_Error ) {
			return $this->read_failure( $result, $context, $namespace, $mirrored, true );
		}

		$valid = $this->validate_response( $result, 'batch' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		$entries = [];

		foreach ( (array) $result['entries'] as $key => $entry ) {
			if ( ! is_array( $entry ) ) {
				continue;
			}

			$entries[ (string) $key ] = $this->entry_fields( $entry );

			$mirror->put( $context['fingerprint'], $namespace, (string) $key, $entry + [ 'synced_at' => time() ] );
		}

		$missing = array_values( array_map( 'strval', (array) ( $result['missing'] ?? [] ) ) );

		foreach ( $missing as $missing_key ) {
			$mirror->forget( $namespace, $missing_key );
		}

		return [
			'entries' => $entries,
			'missing' => $missing,
			'stale'   => false,
		];
	}

	/**
	 * Writes one entry (write-through: the mirror updates only on verified success).
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace.
	 * @param string $key       Storage key.
	 * @param array  $value     Entry value.
	 * @param array  $args      {
	 *     Optional arguments.
	 *
	 *     @type string   $scope            Connection slot. Default: 'site'. Accepts 'network' or 'site'.
	 *     @type int      $schema_version   Consumer payload format marker. Default: 1.
	 *     @type int|null $expected_version Compare-and-set: the version this write is based on;
	 *                                      0 means create-only. Omit for last-write-wins.
	 *     @type bool     $snapshot         Keep the pre-write state in revision history. Default:
	 *                                      true. Pass false for high-churn writes whose history
	 *                                      would be noise.
	 * }
	 *
	 * @return array|WP_Error {version, schema_version, updated_at} or an error (version_conflict
	 *                        carries the current entry in its data body for merging).
	 */
	public function write( string $namespace, string $key, array $value, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$namespace = $context['namespace'];

		$encoded = wp_json_encode( $value );

		if ( false === $encoded ) {
			return new WP_Error( 'gk_connection_encode_failed', 'The value could not be encoded.' );
		}

		if ( strlen( $encoded ) > self::MAX_VALUE_BYTES ) {
			return new WP_Error( 'too_large', 'The value exceeds the per-entry size cap.', [ 'status' => 413 ] );
		}

		$schema_version = (int) ( $args['schema_version'] ?? 1 );

		$body = [
			'namespace'      => $namespace,
			'key'            => $key,
			'value'          => $value,
			'schema_version' => $schema_version,
		];

		if ( isset( $args['expected_version'] ) ) {
			$body['expected_version'] = (int) $args['expected_version'];
		}

		if ( isset( $args['snapshot'] ) && false === $args['snapshot'] ) {
			$body['snapshot'] = false;
		}

		$result = $context['client']->signed_request( 'POST', '/storage/set', $body );

		if ( $result instanceof WP_Error ) {
			return $this->write_failure( $result, $context );
		}

		$valid = $this->validate_response( $result, 'write' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		$entry = [
			'value'          => $value,
			'version'        => (int) $result['version'],
			'schema_version' => $schema_version,
			'updated_at'     => (string) ( $result['updated_at'] ?? '' ),
			'synced_at'      => time(),
		];

		$context['mirror']->put( $context['fingerprint'], $namespace, $key, $entry );

		return [
			'version'        => $entry['version'],
			'schema_version' => $schema_version,
			'updated_at'     => $entry['updated_at'],
		];
	}

	/**
	 * Deletes one entry (idempotent: an absent entry succeeds with deleted false).
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace.
	 * @param string $key       Storage key.
	 * @param array  $args      {
	 *     Optional arguments.
	 *
	 *     @type string   $scope            Connection slot. Default: 'site'. Accepts 'network' or 'site'.
	 *     @type int|null $expected_version Compare-and-set: conflict when the entry moved on.
	 *     @type bool     $snapshot         Keep the final state restorable in history. Default:
	 *                                      true. Pass false and the delete cannot be restored.
	 * }
	 *
	 * @return array|WP_Error {deleted: bool} or an error.
	 */
	public function delete( string $namespace, string $key, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$namespace = $context['namespace'];

		$body = [
			'namespace' => $namespace,
			'key'       => $key,
		];

		if ( isset( $args['expected_version'] ) ) {
			$body['expected_version'] = (int) $args['expected_version'];
		}

		if ( isset( $args['snapshot'] ) && false === $args['snapshot'] ) {
			$body['snapshot'] = false;
		}

		$result = $context['client']->signed_request( 'POST', '/storage/delete', $body );

		if ( $result instanceof WP_Error ) {
			return $this->write_failure( $result, $context );
		}

		$valid = $this->validate_response( $result, 'delete' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		$context['mirror']->forget( $namespace, $key );

		return [ 'deleted' => $result['deleted'] ];
	}

	/**
	 * Lists every entry the connection has stored in a namespace (metadata only, newest first).
	 *
	 * The index a product uses to inventory or clear what it saved to the account. Values and file
	 * bytes are never returned — read a value with {@see self::read()}. Not mirrored: always a live
	 * signed round trip, capped server-side at the per-namespace entry limit.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param array  $args      Optional: scope ('network'|'site').
	 *
	 * @return array|WP_Error {namespace, entries: array<int,array{key,type,size,version,updated_at,file?}>} or an error.
	 */
	public function list( string $namespace, array $args = [] ) {
		$context = $this->resolve( $namespace, [], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$namespace = $context['namespace'];

		$result = $context['client']->signed_request( 'POST', '/storage/list', [ 'namespace' => $namespace ] );

		if ( $result instanceof WP_Error ) {
			// A revoked connection loses its local residue too, so a later read() can't serve the
			// mirror as if the connection were still live.
			if ( 'connection_revoked' === $result->get_error_code() ) {
				$context['mirror']->purge();
			}

			return $result;
		}

		if ( ! isset( $result['entries'] ) || ! is_array( $result['entries'] ) ) {
			return new WP_Error( 'invalid_response', 'The storage list response was malformed.' );
		}

		// Normalize to a well-formed shape: a malformed element must never become a false "deleted"
		// in delete_all() or a fatal in a consumer that trusts the shape.
		$entries = [];

		foreach ( $result['entries'] as $entry ) {
			// Enforce the canonical key shape (not emptiness) so a valid single-char key like "0"
			// survives, and reject anything the server would never have accepted.
			if ( ! is_array( $entry )
				|| ! isset( $entry['key'] )
				|| ! is_string( $entry['key'] )
				|| ! preg_match( '/^[A-Za-z0-9][A-Za-z0-9._:-]{0,127}$/', $entry['key'] )
			) {
				continue;
			}

			$type = 'file' === ( $entry['type'] ?? '' ) ? 'file' : 'json';

			// A file entry without a well-formed metadata block is unusable — drop it rather than
			// hand a consumer a `type=file` entry missing the documented `file{}` shape.
			if ( 'file' === $type && ! is_array( $entry['file'] ?? null ) ) {
				continue;
			}

			$clean = [
				'key'        => $entry['key'],
				'type'       => $type,
				'size'       => is_scalar( $entry['size'] ?? null ) ? (int) $entry['size'] : 0,
				'version'    => is_scalar( $entry['version'] ?? null ) ? (int) $entry['version'] : 0,
				'updated_at' => is_scalar( $entry['updated_at'] ?? null ) ? (string) $entry['updated_at'] : '',
			];

			if ( 'file' === $type ) {
				$file          = $entry['file'];
				$clean['file'] = [
					'name'   => is_scalar( $file['name'] ?? null ) ? (string) $file['name'] : '',
					'type'   => is_scalar( $file['type'] ?? null ) ? (string) $file['type'] : '',
					'sha256' => is_scalar( $file['sha256'] ?? null ) ? (string) $file['sha256'] : '',
				];
			}

			$entries[] = $clean;
		}

		return [
			'namespace' => $namespace,
			'entries'   => $entries,
		];
	}

	/**
	 * Deletes every entry the connection has stored in a namespace.
	 *
	 * Convenience over {@see self::list()} + {@see self::delete()}: lists the namespace and deletes
	 * each entry (each delete retains its own restorable history, exactly like a single delete). NOT
	 * atomic — a mid-run failure leaves the remaining entries deletable on a retry; the returned
	 * counts report what actually happened.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param array  $args      Optional: scope ('network'|'site').
	 *
	 * @return array|WP_Error {deleted:int, failed:int, keys:string[]} or an error if the list failed.
	 */
	public function delete_all( string $namespace, array $args = [] ) {
		$list = $this->list( $namespace, $args );

		if ( $list instanceof WP_Error ) {
			return $list;
		}

		$deleted = 0;
		$failed  = 0;
		$keys    = [];

		foreach ( $list['entries'] as $entry ) {
			$key = (string) ( $entry['key'] ?? '' );

			if ( '' === $key ) {
				continue;
			}

			$result = $this->delete( $namespace, $key, $args );

			if ( $result instanceof WP_Error ) {
				++$failed;

				continue;
			}

			// delete() is idempotent: `deleted => false` means it was already gone (a concurrent
			// delete), so it must not be counted or reported as removed by this call.
			if ( ! empty( $result['deleted'] ) ) {
				++$deleted;
				$keys[] = $key;
			}
		}

		return [
			'deleted' => $deleted,
			'failed'  => $failed,
			'keys'    => $keys,
		];
	}

	/**
	 * Stores a file (raw bytes, e.g. a ZIP) as a file entry. Files are never mirrored.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param string $key       Storage key.
	 * @param string $path      Readable local file path.
	 * @param array  $args      Optional: scope, expected_version, filename, content_type.
	 *
	 * @return array|WP_Error {version, updated_at, size, sha256} or an error.
	 */
	public function store_file( string $namespace, string $key, string $path, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		if ( ! is_readable( $path ) || ! is_file( $path ) ) {
			return new WP_Error( 'invalid_request', 'The file path is not readable.' );
		}

		// Preflight the size so an oversized file is refused without reading it into memory.
		$size = filesize( $path );

		if ( false !== $size && $size > self::MAX_FILE_BYTES ) {
			return new WP_Error( 'too_large', 'The file exceeds the per-entry size cap.', [ 'status' => 413 ] );
		}

		$bytes = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $bytes || '' === $bytes ) {
			return new WP_Error( 'invalid_request', 'The file could not be read or is empty.' );
		}

		if ( strlen( $bytes ) > self::MAX_FILE_BYTES ) {
			return new WP_Error( 'too_large', 'The file exceeds the per-entry size cap.', [ 'status' => 413 ] );
		}

		$body = [
			'namespace' => $context['namespace'],
			'key'       => $key,
			'file'      => [
				'name' => (string) ( $args['filename'] ?? basename( $path ) ),
				'type' => (string) ( $args['content_type'] ?? 'application/octet-stream' ),
				'data' => base64_encode( $bytes ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Binary file bytes are transport-encoded for the JSON body, not obfuscated.
			],
		];

		if ( isset( $args['expected_version'] ) ) {
			$body['expected_version'] = (int) $args['expected_version'];
		}

		if ( isset( $args['snapshot'] ) && false === $args['snapshot'] ) {
			$body['snapshot'] = false;
		}

		$result = $context['client']->signed_request( 'POST', '/storage/set', $body );

		if ( $result instanceof WP_Error ) {
			return $this->write_failure( $result, $context );
		}

		$valid = $this->validate_response( $result, 'write' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		// A stale mirrored json predecessor under this key must not survive the type change.
		$context['mirror']->forget( $context['namespace'], $key );

		return [
			'version'    => (int) $result['version'],
			'updated_at' => (string) ( $result['updated_at'] ?? '' ),
			'size'       => strlen( $bytes ),
			'sha256'     => hash( 'sha256', $bytes ),
		];
	}

	/**
	 * Fetches a file entry's bytes, verifying integrity against the stored sha256.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param string $key       Storage key.
	 * @param array  $args      Optional: scope; save_to (path to write the bytes to — a TRUSTED
	 *                          capability: callers must never forward request-derived paths).
	 *
	 * @return array|WP_Error {name, content_type, size, sha256, version, bytes?|saved_to?} or an error.
	 */
	public function fetch_file( string $namespace, string $key, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$result = $context['client']->signed_request(
			'POST',
			'/storage/get',
			[
				'namespace'    => $context['namespace'],
				'key'          => $key,
				'include_data' => true,
			]
		);

		if ( $result instanceof WP_Error ) {
			if ( 'connection_revoked' === $result->get_error_code() ) {
				$context['mirror']->purge();
			}

			if ( 'not_found' === $result->get_error_code() ) {
				$context['mirror']->forget( $context['namespace'], $key );
			}

			return $result;
		}

		// The authoritative answer is a file: any mirrored json predecessor under this key is stale.
		$context['mirror']->forget( $context['namespace'], $key );

		$valid = $this->validate_response( $result, 'entry' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		$entry = $result['entry'];

		if ( 'file' !== ( $entry['type'] ?? '' ) ) {
			return new WP_Error( 'invalid_request', 'The entry is not a file.' );
		}

		$file  = $entry['file'];
		$bytes = base64_decode( (string) ( $file['data'] ?? '' ), true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- Reverses the transport encoding of file bytes; strict mode plus the sha256 check below verify integrity.

		// Verify integrity end to end: the payload rode a signed response, but the hash proves the
		// stored bytes themselves survived intact.
		if ( false === $bytes || hash( 'sha256', $bytes ) !== (string) ( $file['sha256'] ?? '' ) ) {
			return new WP_Error( 'gk_connection_bad_response', 'The file failed integrity verification.' );
		}

		$output = [
			'name'         => (string) ( $file['name'] ?? '' ),
			'content_type' => (string) ( $file['type'] ?? '' ),
			'size'         => strlen( $bytes ),
			'sha256'       => (string) $file['sha256'],
			'version'      => (int) ( $entry['version'] ?? 0 ),
		];

		if ( isset( $args['save_to'] ) && is_string( $args['save_to'] ) ) {
			$written = file_put_contents( $args['save_to'], $bytes ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

			// A partial write is a failure: the verified byte count must land in full.
			if ( strlen( $bytes ) !== (int) $written ) {
				return new WP_Error( 'invalid_request', 'The file could not be fully written to save_to.' );
			}

			$output['saved_to'] = $args['save_to'];

			return $output;
		}

		$output['bytes'] = $bytes;

		return $output;
	}

	/**
	 * Lists an entry's retained revision history (most recent first).
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param string $key       Storage key.
	 * @param array  $args      Optional: scope.
	 *
	 * @return array|WP_Error {revisions: [{version, type, size, deleted, created_at, value?|file?}]} or an error.
	 */
	public function revisions( string $namespace, string $key, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$result = $context['client']->signed_request(
			'POST',
			'/storage/revisions',
			[
				'namespace' => $context['namespace'],
				'key'       => $key,
			]
		);

		if ( $result instanceof WP_Error ) {
			return $this->write_failure( $result, $context );
		}

		$valid = $this->validate_response( $result, 'revisions' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		return [ 'revisions' => $result['revisions'] ];
	}

	/**
	 * Restores a retained revision as a NEW version of the entry.
	 *
	 * @since 1.26.0
	 *
	 * @param string $namespace Product namespace ('' = the gravitykit catch-all).
	 * @param string $key       Storage key.
	 * @param int    $version   Revision version to restore.
	 * @param array  $args      Optional: scope.
	 *
	 * @return array|WP_Error {version, restored_from, updated_at} or an error.
	 */
	public function restore( string $namespace, string $key, int $version, array $args = [] ) {
		$context = $this->resolve( $namespace, [ $key ], $args );

		if ( $context instanceof WP_Error ) {
			return $context;
		}

		$result = $context['client']->signed_request(
			'POST',
			'/storage/restore',
			[
				'namespace' => $context['namespace'],
				'key'       => $key,
				'version'   => $version,
			]
		);

		if ( $result instanceof WP_Error ) {
			return $this->write_failure( $result, $context );
		}

		$valid = $this->validate_response( $result, 'restore' );

		if ( $valid instanceof WP_Error ) {
			return $valid;
		}

		// The restored content replaced the current value server-side; any mirrored copy is stale.
		$context['mirror']->forget( $context['namespace'], $key );

		return [
			'version'       => (int) $result['version'],
			'restored_from' => (int) ( $result['restored_from'] ?? $version ),
			'updated_at'    => (string) ( $result['updated_at'] ?? '' ),
		];
	}

	/**
	 * Validates a signed 2xx response against its route contract.
	 *
	 * A verified-but-malformed body must fail loudly, never be mirrored or returned as success
	 * (e.g. a write acknowledged as version 0, or a read whose entry lost its version).
	 *
	 * @since 1.26.0
	 *
	 * @param array  $result Decoded verified response.
	 * @param string $contract Contract name: 'entry', 'write', 'delete', 'batch', 'revisions'.
	 *
	 * @return true|WP_Error
	 */
	private function validate_response( array $result, string $contract ) {
		switch ( $contract ) {
			case 'entry':
				$ok = $this->valid_entry( $result['entry'] ?? null );
				break;
			case 'write':
			case 'restore':
				$ok = is_int( $result['version'] ?? null ) && $result['version'] >= 1
					&& is_string( $result['updated_at'] ?? null )
					&& is_int( $result['schema_version'] ?? null )
					&& is_string( $result['namespace'] ?? null ) && is_string( $result['key'] ?? null );

				if ( $ok && 'restore' === $contract ) {
					$ok = is_int( $result['restored_from'] ?? null ) && $result['restored_from'] >= 1;
				}
				break;
			case 'delete':
				$ok = is_bool( $result['deleted'] ?? null )
					&& is_string( $result['namespace'] ?? null ) && is_string( $result['key'] ?? null );
				break;
			case 'batch':
				$ok = is_array( $result['entries'] ?? null ) && is_array( $result['missing'] ?? null );

				if ( $ok ) {
					foreach ( $result['entries'] as $entry ) {
						if ( ! $this->valid_entry( $entry ) ) {
							$ok = false;
							break;
						}
					}

					foreach ( $result['missing'] as $missing_key ) {
						if ( ! is_string( $missing_key ) ) {
							$ok = false;
							break;
						}
					}
				}
				break;
			case 'revisions':
				$ok = is_array( $result['revisions'] ?? null );

				if ( $ok ) {
					foreach ( $result['revisions'] as $revision ) {
						if ( ! is_array( $revision ) || ! is_int( $revision['version'] ?? null ) || $revision['version'] < 1
							|| ! in_array( $revision['type'] ?? '', [ 'json', 'file' ], true )
							|| ! is_int( $revision['schema_version'] ?? null )
							|| ! is_int( $revision['size'] ?? null )
							|| ! is_bool( $revision['deleted'] ?? null )
							|| ! is_string( $revision['created_at'] ?? null )
							|| ( 'json' === $revision['type'] && ! array_key_exists( 'value', $revision ) )
							|| ( 'file' === $revision['type'] && ( ! is_array( $revision['file'] ?? null )
								|| ! is_string( $revision['file']['name'] ?? null )
								|| ! is_string( $revision['file']['sha256'] ?? null ) ) ) ) {
							$ok = false;
							break;
						}
					}
				}
				break;
			default:
				$ok = false;
		}

		if ( ! $ok ) {
			return new WP_Error( 'gk_connection_bad_response', 'The storage response was malformed.' );
		}

		return true;
	}

	/**
	 * Whether an entry payload has the full typed shape: json entries carry a value, file entries
	 * carry complete file metadata.
	 *
	 * @since 1.26.0
	 *
	 * @param mixed $entry Candidate entry.
	 *
	 * @return bool
	 */
	private function valid_entry( $entry ): bool {
		if ( ! is_array( $entry ) || ! is_int( $entry['version'] ?? null ) || $entry['version'] < 1
			|| ! is_int( $entry['schema_version'] ?? null ) || ! is_string( $entry['updated_at'] ?? null ) ) {
			return false;
		}

		$type = $entry['type'] ?? null;

		if ( 'file' === $type ) {
			$file = $entry['file'] ?? null;

			return is_array( $file ) && is_string( $file['name'] ?? null ) && is_string( $file['type'] ?? null )
				&& is_string( $file['sha256'] ?? null ) && is_int( $file['size'] ?? null );
		}

		return 'json' === $type && is_array( $entry['value'] ?? null );
	}

	/**
	 * Validates inputs and resolves the connection slot into a request context.
	 *
	 * @since 1.26.0
	 *
	 * @param string   $namespace Product namespace.
	 * @param string[] $keys      Storage keys to validate.
	 * @param array    $args      Caller arguments (only `scope` is read here).
	 *
	 * @return array|WP_Error Context {client, mirror, fingerprint, scope} or an error.
	 */
	private function resolve( string $namespace, array $keys, array $args ) {
		if ( '' === $namespace ) {
			$namespace = self::DEFAULT_NAMESPACE;
		}

		if ( ! preg_match( '/^[a-z0-9][a-z0-9-]{1,62}[a-z0-9]$/', $namespace ) ) {
			return new WP_Error( 'invalid_namespace', 'Namespaces are 3-64 lowercase alphanumeric/hyphen characters (use your product slug).' );
		}

		foreach ( $keys as $key ) {
			if ( ! is_string( $key ) || ! preg_match( '/^[A-Za-z0-9][A-Za-z0-9._:-]{0,127}$/', $key ) ) {
				return new WP_Error( 'invalid_key', 'Keys are 1-128 characters: alphanumeric, dot, underscore, colon, or hyphen.' );
			}
		}

		// The slot is explicit and never escalates: anything but 'network' collapses to 'site',
		// and an empty site slot is a miss — never a silent fallback to the network connection.
		$scope      = 'network' === ( $args['scope'] ?? '' ) ? TokenStore::SCOPE_NETWORK : TokenStore::SCOPE_SITE;
		$store      = new TokenStore( $scope );
		$connection = $store->get();

		if ( ! is_array( $connection ) || ! empty( $connection['decrypt_failed'] ) || empty( $connection['connection_id'] ) ) {
			return new WP_Error( 'gk_connection_missing', 'No usable account connection is available for this scope.' );
		}

		// Local fail-fast on the granted scopes persisted at exchange; the server enforces the
		// same gate authoritatively.
		if ( ! in_array( self::SCOPE, (array) ( $connection['scopes'] ?? [] ), true ) ) {
			return new WP_Error( 'scope_not_granted', 'The connection was not granted the storage scope. Reconnect to enable it.', [ 'status' => 403 ] );
		}

		return [
			'namespace'   => $namespace,
			'client'      => call_user_func( $this->client_factory, $store ),
			'mirror'      => new StorageMirror( TokenStore::SCOPE_NETWORK === $scope ? 'network' : 'site' ),
			'fingerprint' => hash( 'sha256', (string) $connection['connection_id'] ),
			'scope'       => $scope,
		];
	}

	/**
	 * Passes a failed write/delete through, purging the mirror when the connection is revoked so
	 * later reads cannot serve a dead connection's residue. (An externally revoked connection is
	 * otherwise caught on the next network read; the mirror window bounds the gap to its TTL.)
	 *
	 * @since 1.26.0
	 *
	 * @param WP_Error $error   The signed-channel error.
	 * @param array    $context Request context.
	 *
	 * @return WP_Error
	 */
	private function write_failure( WP_Error $error, array $context ): WP_Error {
		if ( 'connection_revoked' === $error->get_error_code() ) {
			$context['mirror']->purge();
		}

		return $error;
	}

	/**
	 * Maps a failed network read to the stale mirror (transport failures only) or the error.
	 *
	 * @since 1.26.0
	 *
	 * @param WP_Error $error    The signed-channel error.
	 * @param array    $context  Request context.
	 * @param string   $namespace Product namespace.
	 * @param array    $mirrored Mirrored entries keyed by requested key (null when absent).
	 * @param bool     $batch    Whether the caller expects the batch envelope. A one-key batch is
	 *                           legal, so the shape must never be inferred from the entry count.
	 *
	 * @return array|WP_Error
	 */
	private function read_failure( WP_Error $error, array $context, string $namespace, array $mirrored, bool $batch ) {
		$code = $error->get_error_code();

		// A revoked connection loses its local residue too: fail closed, never serve the mirror.
		if ( 'connection_revoked' === $code ) {
			$context['mirror']->purge();

			return $error;
		}

		if ( 'not_found' === $code ) {
			foreach ( array_keys( $mirrored ) as $key ) {
				$context['mirror']->forget( $namespace, (string) $key );
			}

			return $error;
		}

		if ( ! in_array( $code, self::STALE_SERVABLE_CODES, true ) ) {
			return $error;
		}

		// Serve stale only when EVERY requested key has a mirrored entry: partially answering a
		// batch would misreport the unmirrored keys as missing.
		foreach ( $mirrored as $entry ) {
			if ( null === $entry ) {
				return $error;
			}
		}

		if ( ! $batch ) {
			return $this->entry_output( reset( $mirrored ), true );
		}

		return [
			'entries' => array_map( [ $this, 'entry_fields' ], $mirrored ),
			'missing' => [],
			'stale'   => true,
		];
	}

	/**
	 * Whether a mirrored entry is inside the freshness window.
	 *
	 * @since 1.26.0
	 *
	 * @param array $entry Mirrored entry.
	 *
	 * @return bool
	 */
	private function is_fresh( array $entry ): bool {
		/**
		 * Filters the mirror freshness window.
		 *
		 * @since 1.26.0
		 *
		 * @param int $ttl Seconds a mirrored entry serves without a network re-read. Default: 300.
		 */
		$ttl = (int) apply_filters( 'gk/foundation/connection/storage/mirror-ttl', self::MIRROR_TTL );

		return ( time() - (int) ( $entry['synced_at'] ?? 0 ) ) < $ttl;
	}

	/**
	 * Returns a single-entry read result.
	 *
	 * @since 1.26.0
	 *
	 * @param array $entry Entry.
	 * @param bool  $stale Whether it was served from the mirror after a transport failure.
	 *
	 * @return array
	 */
	private function entry_output( array $entry, bool $stale ): array {
		return $this->entry_fields( $entry ) + [ 'stale' => $stale ];
	}

	/**
	 * Normalizes an entry's wire fields.
	 *
	 * @since 1.26.0
	 *
	 * @param array $entry Entry.
	 *
	 * @return array
	 */
	private function entry_fields( array $entry ): array {
		$fields = [
			'type'           => (string) ( $entry['type'] ?? 'json' ),
			'value'          => $entry['value'] ?? null,
			'version'        => (int) ( $entry['version'] ?? 0 ),
			'schema_version' => (int) ( $entry['schema_version'] ?? 1 ),
			'updated_at'     => (string) ( $entry['updated_at'] ?? '' ),
		];

		// File entries carry metadata instead of a value; bytes only travel via fetch_file().
		if ( is_array( $entry['file'] ?? null ) ) {
			$fields['file'] = $entry['file'];

			unset( $fields['file']['data'] );
		}

		return $fields;
	}
}
