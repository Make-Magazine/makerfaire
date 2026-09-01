<?php

namespace GravityKit\AdvancedFilter\QueryFilters\Querying\Field\Source;

use GravityKit\AdvancedFilter\QueryFilters\Querying\Field\FieldCriteria;

/**
 * Resolves a field to the {@see ChoiceSource} that owns its choices.
 *
 * @since 2.14.0
 */
final class ChoiceSourceManager {
	/**
	 * The terminal fallback, tried after every registered source.
	 *
	 * @since 2.14.0
	 *
	 * @var ChoiceSource
	 */
	private ChoiceSource $fallback;

	/**
	 * Registered sources, in resolution order.
	 *
	 * @since 2.14.0
	 *
	 * @var ChoiceSource[]
	 */
	private array $sources = [];

	/**
	 * Resolved sources keyed by `form_id:field_id`.
	 *
	 * @since 2.14.0
	 *
	 * @var array<string, ChoiceSource>
	 */
	private array $resolved = [];

	/**
	 * @since 2.14.0
	 *
	 * @param ChoiceSource $fallback The terminal fallback source.
	 */
	public function __construct( ChoiceSource $fallback ) {
		$this->fallback = $fallback;
	}

	/**
	 * Registers a source ahead of the fallback, ignoring a source equal to one already registered.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSource $source The source to register.
	 */
	public function register( ChoiceSource $source ): void {
		if ( $this->has( $source ) ) {
			return;
		}

		$this->sources[] = $source;
	}

	/**
	 * Whether a source equal to $source is already registered.
	 *
	 * @since 2.14.0
	 *
	 * @param ChoiceSource $source The source to look for.
	 *
	 * @return bool True when an equal source is registered.
	 */
	public function has( ChoiceSource $source ): bool {
		foreach ( $this->sources as $registered ) {
			if ( $registered->equals( $source ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether any registered source or the terminal fallback claims the given field.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The owning form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return bool True when the field's choices can be resolved.
	 */
	public function supports( int $form_id, string $field_id ): bool {
		foreach ( $this->sources as $source ) {
			if ( $source->supports( $form_id, $field_id ) ) {
				return true;
			}
		}

		return $this->fallback->supports( $form_id, $field_id );
	}

	/**
	 * Counts the field's choices through its resolved source, accurate up to `$threshold`.
	 *
	 * @since 2.14.0
	 *
	 * @param int      $form_id   The owning form ID.
	 * @param string   $field_id  The field ID.
	 * @param int|null $threshold The count that triggers the search endpoint; a source may stop counting once it reaches this. Null counts exactly.
	 *
	 * @return int The exact count, or at least `$threshold` when the source stops early.
	 */
	public function count( int $form_id, string $field_id, ?int $threshold = null ): int {
		$criteria = ( new FieldCriteria( $form_id, $field_id ) )->with_threshold( $threshold );

		return $this->resolve_source( $form_id, $field_id )->count( $criteria );
	}

	/**
	 * Whether the field's resolved source wants the search endpoint without being counted first.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The owning form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return bool True when the source forces the endpoint.
	 */
	public function prefers_endpoint( int $form_id, string $field_id ): bool {
		return $this->resolve_source( $form_id, $field_id )->prefers_endpoint();
	}

	/**
	 * Resolves the source that claims the given field, falling back to the terminal source.
	 *
	 * @since 2.14.0
	 *
	 * @param int    $form_id  The owning form ID.
	 * @param string $field_id The field ID.
	 *
	 * @return ChoiceSource The owning source.
	 */
	public function resolve_source( int $form_id, string $field_id ): ChoiceSource {
		$key = $form_id . ':' . $field_id;

		if ( isset( $this->resolved[ $key ] ) ) {
			return $this->resolved[ $key ];
		}

		foreach ( $this->sources as $source ) {
			if ( $source->supports( $form_id, $field_id ) ) {
				return $this->resolved[ $key ] = $source;
			}
		}

		return $this->resolved[ $key ] = $this->fallback;
	}
}
