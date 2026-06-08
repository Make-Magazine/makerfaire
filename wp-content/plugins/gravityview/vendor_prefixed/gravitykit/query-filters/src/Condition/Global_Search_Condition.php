<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Condition;

use GF_Query_Condition;

/**
 * Represents a condition that searches all entry meta values for the given words.
 *
 * Uses an EXISTS or NOT EXISTS subquery against the entry meta table to match entries
 * where any meta value contains (or does not contain) the specified search words.
 *
 * @since 2.9.0
 */
final class Global_Search_Condition extends GF_Query_Condition {
	/**
	 * The form ID to search within.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * The search words.
	 *
	 * @since 2.9.0
	 *
	 * @var string[]
	 */
	private $words;

	/**
	 * Whether to use EXISTS or NOT EXISTS.
	 *
	 * @since 2.9.0
	 *
	 * @var bool
	 */
	private $is_excluding = false;

	/**
	 * Require the named constructors.
	 *
	 * @since 2.9.0
	 */
	private function __construct( int $form_id, array $words ) {
		$this->form_id = $form_id;
		$this->words   = $words;
	}

	/**
	 * Creates a condition that includes entries matching all given words.
	 *
	 * @since 2.9.0
	 *
	 * @param int      $form_id The form ID.
	 * @param string[] $words   The search words.
	 *
	 * @return self The condition.
	 */
	public static function include( int $form_id, array $words ): self {
		$condition               = new self( $form_id, $words );
		$condition->is_excluding = false;

		return $condition;
	}

	/**
	 * Creates a condition that excludes entries matching any of the given words.
	 *
	 * @since 2.9.0
	 *
	 * @param int      $form_id The form ID.
	 * @param string[] $words   The search words.
	 *
	 * @return self The condition.
	 */
	public static function exclude( int $form_id, array $words ): self {
		$condition               = new self( $form_id, $words );
		$condition->is_excluding = true;

		return $condition;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.9.0
	 */
	public function sql( $query ): string {
		global $wpdb;

		if ( empty( $this->words ) ) {
			return '';
		}

		$alias    = $query->_alias( null, $this->form_id );
		$function = $this->is_excluding ? 'NOT EXISTS' : 'EXISTS';
		$glue     = $this->is_excluding ? ' OR ' : ' AND ';

		$word_conditions = implode( $glue, array_map( static function ( string $word ) use ( $wpdb ) {
			return $wpdb->prepare( '`meta_value` LIKE %s', '%' . $wpdb->esc_like( $word ) . '%' );
		}, $this->words ) );

		return sprintf(
			'(%s (SELECT 1 FROM `%s` WHERE `form_id` = %d AND `entry_id` = `%s`.`id` AND (%s)))',
			$function,
			\GFFormsModel::get_entry_meta_table_name(),
			$this->form_id,
			$alias,
			$word_conditions
		);
	}
}
