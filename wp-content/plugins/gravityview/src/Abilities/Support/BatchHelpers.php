<?php
/**
 * Shared helpers for GravityView batch-capable abilities.
 *
 * @package GravityView
 */

namespace GravityKit\GravityView\Abilities\Support;

use GravityKit\GravityView\Foundation\Abilities\Support\BatchContract;
use GravityKit\GravityView\Permissions\Permissions;
use GravityKit\GravityView\View\Contracts\BatchableSlotRepository;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Common validation and response wrapping for batch ability handlers.
 */
final class BatchHelpers {

	/**
	 * Hard upper bound on items per batch call. Sized so the validation +
	 * persistence pass for the largest realistic payload still fits inside
	 * ViewLock's 5-second window.
	 */
	public const MAX_BATCH_SIZE = 50;

	/**
	 * Hard lower bound on items per batch call. An empty batch is treated as
	 * malformed input — callers should not be invoking the batch path at all.
	 */
	public const MIN_BATCH_SIZE = 1;

	/**
	 * Build the shared batch output schema wrapped around a singleton schema.
	 *
	 * @param array $singleton_schema Existing singleton output schema.
	 * @return array
	 */
	public static function output_schema( array $singleton_schema ): array {
		return [
			'oneOf' => [
				$singleton_schema,
				[
					'type'       => 'object',
					'properties' => [
						'batch_results' => [
							'type'  => 'array',
							'items' => [
								'type'       => 'object',
								'properties' => [
									'index'  => [ 'type' => 'integer' ],
									'result' => [ 'type' => [ 'object', 'null' ] ],
								],
								'required'   => [ 'index', 'result' ],
							],
						],
						'dry_run'       => [ 'type' => 'boolean' ],
						'version'       => [ 'type' => 'string' ],
					],
					'required'   => [ 'batch_results', 'dry_run', 'version' ],
				],
			],
		];
	}

	/**
	 * Build the common top-level batch input properties.
	 *
	 * @param array $item_schema Batch item schema.
	 * @return array
	 */
	public static function batch_input_properties( array $item_schema ): array {
		return [
			'version' => [
				'type'        => 'string',
				'description' => __( 'Optimistic-concurrency token from a prior read. Required when using `batch`.', 'gk-gravityview' ),
			],
			'dry_run' => [
				'type'        => 'boolean',
				'description' => __( 'Run validation and planning without persisting. In batch mode the batch path skips the post-meta write and version bump. In singleton mode dry_run is honored via GravityView\'s Bootstrap::with_dry_run meta-suppression layer.', 'gk-gravityview' ),
			],
			'batch'   => [
				'type'        => 'array',
				'minItems'    => 1,
				'maxItems'    => self::MAX_BATCH_SIZE,
				'description' => __( 'Apply N similar operations atomically. All items are validated against the starting state before any persist. If ANY item fails validation, NONE persist and 400 is returned with per-item failures. Mutually exclusive with the singleton fields on this ability.', 'gk-gravityview' ),
				'items'       => $item_schema,
			],
		];
	}

	/**
	 * Validate shared batch input, call the repository, and wrap its rows.
	 *
	 * @param array                   $input      Batch ability input.
	 * @param BatchableSlotRepository $repository Repository implementing the batch contract.
	 * @return array|WP_Error
	 */
	public static function handle_repository_batch( array $input, BatchableSlotRepository $repository ) {
		$top_level = self::validate_top_level( $input );
		if ( is_wp_error( $top_level ) ) {
			return $top_level;
		}

		$permission = ( new Permissions() )->can_modify_view_child_slot( (int) $top_level['view_id'] );
		if ( is_wp_error( $permission ) ) {
			return $permission;
		}
		if ( true !== $permission ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to modify this View.', 'gk-gravityview' ),
				[ 'status' => 403 ]
			);
		}

		$rows = $repository->batch_apply(
			(int) $top_level['view_id'],
			(array) $top_level['items'],
			(string) $top_level['version'],
			(bool) $top_level['dry_run']
		);

		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		return BatchContract::result_envelope(
			$rows,
			(bool) $top_level['dry_run'],
			[ 'version' => self::version_from_rows( $rows, (string) $top_level['version'] ) ]
		);
	}

	/**
	 * Validate batch-only top-level keys.
	 *
	 * @param array $input Batch ability input.
	 * @return array|WP_Error
	 */
	private static function validate_top_level( array $input ) {
		foreach ( array_keys( $input ) as $key ) {
			if ( ! in_array( $key, [ 'id', 'version', 'dry_run', 'batch' ], true ) ) {
				return new WP_Error(
					'gk_batch_mixed_mode',
					__( 'Batch input is mutually exclusive with singleton input fields.', 'gk-gravityview' ),
					[
						'status' => 400,
						'field'  => (string) $key,
					]
				);
			}
		}

		$view_id = (int) ( $input['id'] ?? 0 );
		if ( $view_id <= 0 ) {
			return new WP_Error(
				'gk_batch_invalid_view_id',
				__( 'A valid View id is required.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$version = isset( $input['version'] ) ? trim( (string) $input['version'] ) : '';
		if ( '' === $version ) {
			return new WP_Error(
				'gk_batch_missing_version',
				__( 'Batch input requires a version token.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( array_key_exists( 'dry_run', $input ) && ! is_bool( $input['dry_run'] ) ) {
			return new WP_Error(
				'gk_batch_invalid_dry_run',
				__( 'dry_run must be a boolean when using batch input.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		if ( empty( $input['batch'] ) || ! is_array( $input['batch'] ) ) {
			return new WP_Error(
				'gk_batch_invalid_items',
				__( 'batch must contain at least one item.', 'gk-gravityview' ),
				[ 'status' => 400 ]
			);
		}

		$count = count( $input['batch'] );
		if ( $count > self::MAX_BATCH_SIZE ) {
			return new WP_Error(
				'gk_batch_invalid_size',
				sprintf(
					/* translators: %d: max items allowed per batch call */
					__( 'batch cannot contain more than %d items.', 'gk-gravityview' ),
					self::MAX_BATCH_SIZE
				),
				[
					'status' => 400,
					'count'  => $count,
				]
			);
		}

		foreach ( $input['batch'] as $index => $item ) {
			if ( ! is_array( $item ) ) {
				return new WP_Error(
					'gk_batch_invalid_item',
					__( 'Every batch item must be an object.', 'gk-gravityview' ),
					[
						'status' => 400,
						'index'  => (int) $index,
					]
				);
			}
		}

		return [
			'view_id' => $view_id,
			'version' => $version,
			'dry_run' => (bool) ( $input['dry_run'] ?? false ),
			'items'   => $input['batch'],
		];
	}

	/**
	 * Pull the shared version out of repository item rows.
	 *
	 * @param array  $rows             Repository result rows.
	 * @param string $fallback_version Version to use if rows omit it.
	 * @return string
	 */
	private static function version_from_rows( array $rows, string $fallback_version ): string {
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['result'] ) || ! is_array( $row['result'] ) ) {
				continue;
			}
			if ( isset( $row['result']['version'] ) && is_string( $row['result']['version'] ) ) {
				return $row['result']['version'];
			}
		}

		return $fallback_version;
	}
}
