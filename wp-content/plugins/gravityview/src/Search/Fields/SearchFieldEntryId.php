<?php
/**
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Search\Fields;

use GravityKit\GravityView\Search\Querying\SearchFilter;
use GV\View;

/**
 * Represents a search field that searches on entry ID.
 *
 * @since 2.42
 * @since 3.0.0 Migrated to PSR-4 namespace.
 *
 * @extends \GV\Search\Fields\Search_Field
 */
final class SearchFieldEntryId extends \GV\Search\Fields\Search_Field {
	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected string $icon = 'dashicons-tag';

	/**
	 * @inheritdoc
	 * @since 2.42
	 */
	protected static string $type = 'entry_id';

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_name(): string {
		return esc_html__( 'Entry ID', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_default_label(): string {
		return esc_html__( 'Entry ID:', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	public function get_description(): string {
		return esc_html__( 'Search on entry ID', 'gk-gravityview' );
	}

	/**
	 * @inheritDoc
	 * @since 2.42
	 */
	protected function get_input_name(): string {
		return 'gv_id';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 3.0.0
	 */
	public function adjust_filter( SearchFilter $filter, ?View $view = null ): SearchFilter {
		return parent::adjust_filter( $filter, $view )
		             ->with_operator( '=', [ '=' ] )
		             ->with_key( 'id' );
	}
}
