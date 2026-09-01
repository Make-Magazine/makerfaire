<?php
/**
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Search\Fields;

/**
 * Represents a search field that filters on starred entries.
 *
 * @since 2.42
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @extends \GV\Search\Fields\Search_Field
 */
final class SearchFieldIsStarred extends \GV\Search\Fields\Search_Field {
	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-star-half';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'is_starred';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected static string $field_type = 'boolean';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Is Starred', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Filter on starred entries', 'gk-gravityview' );
	}
}
