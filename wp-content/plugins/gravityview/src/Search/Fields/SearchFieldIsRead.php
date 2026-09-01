<?php
/**
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Search\Fields;

/**
 * Represents a search field that filters entries by read status.
 *
 * @since 2.42
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @extends \GV\Search\Fields\Search_Field_Choices
 */
final class SearchFieldIsRead extends \GV\Search\Fields\Search_Field_Choices {
	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-visibility';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'is_read';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected static string $field_type = 'select';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Is Read', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Filter on read entries', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_choices(): array {
		return [
			[
				'text'  => __( 'Read', 'gk-gravityview' ),
				'value' => '1',
			],
			[
				'text'  => __( 'Unread', 'gk-gravityview' ),
				'value' => '0',
			],
		];
	}
}
