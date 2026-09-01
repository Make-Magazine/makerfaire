<?php

declare( strict_types=1 );

namespace GravityKit\GravityView\Foundation\Abilities\Support;

/**
 * Batch contract helpers for GravityKit abilities.
 *
 * Batch-capable product abilities must be all-or-nothing for live writes:
 * validate the whole batch against the starting state, plan mutations in
 * memory, then persist once. Do not loop singleton handlers.
 *
 * Validation rejection should return a 400 WP_Error before any item is
 * attempted. Apply failure should return a 200 envelope with one item error
 * and later items marked unsuccessful because nothing persisted.
 *
 * Batch items validate against the starting state. Later items must not depend
 * on earlier items' persisted side effects, because live batches persist once
 * after the plan succeeds. Products should cap per-ability batch size in JSON
 * Schema (`maxItems`); 50 items is the recommended upper bound so validation
 * and persistence fit inside a ViewLock-style 5s lock window.
 *
 * @since 1.23.0
 */
final class BatchContract {

	/**
	 * Determines whether input is using the batch shape.
	 *
	 * Detection is shape-only: an empty `batch` array still counts as batch
	 * input. Enforce minimum batch size via JSON Schema (`minItems`), not here,
	 * so an empty batch fails with a schema error instead of falling through
	 * to the singleton path and failing with a misleading missing-field error.
	 *
	 * @since 1.23.0
	 *
	 * @param mixed $input Raw ability input.
	 * @return bool
	 */
	public static function is_batch_input( $input ): bool {
		if ( ! is_array( $input ) ) {
			return false;
		}

		$batch = $input['batch'] ?? null;

		return is_array( $batch );
	}

	/**
	 * Builds the canonical batch response envelope.
	 *
	 * The v1 contract is all-or-nothing: every row that makes it into the
	 * envelope is by definition successful (validation failures short-circuit
	 * to a top-level WP_Error before the envelope is built; persist failures
	 * never write the envelope at all). The envelope therefore omits the
	 * per-row `ok`/`error` fields and the redundant `would_apply` top-level
	 * field. If a future contract wants partial-success 200 responses, this
	 * is the place to add them back with real semantics.
	 *
	 * Each input item may carry `index` (preserved) and `result` (echoed).
	 *
	 * @since 1.23.0
	 *
	 * @param array $items   Per-item result rows.
	 * @param bool  $dry_run Whether the batch was a dry-run.
	 * @param array $context Optional context, including `version`.
	 * @return array<string,mixed>
	 */
	public static function result_envelope( array $items, bool $dry_run, array $context = [] ): array {
		$results = [];

		foreach ( array_values( $items ) as $position => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$results[] = [
				'index'  => isset( $item['index'] ) ? (int) $item['index'] : $position,
				'result' => $item['result'] ?? null,
			];
		}

		return [
			'batch_results' => $results,
			'dry_run'       => $dry_run,
			'version'       => isset( $context['version'] ) && is_string( $context['version'] ) ? $context['version'] : '',
		];
	}
}
