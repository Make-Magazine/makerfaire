<?php
/**
 * Entry resolution for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Pagination\PaginationKeys;
use GravityKit\GravityView\View\View;
use WP_Error;

/**
 * Parses submitted IDs and verifies entries against the submitted View.
 *
 * @since 3.0.0
 */
final class EntryResolver {
	/**
	 * Returns whether the current request selected all entries.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_select_all_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Called only from request handlers after nonce verification.
		return ! empty( $_POST[ Config::POST_SELECT_ALL ] );
	}

	/**
	 * Resolves entries from the current POST request.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|WP_Error
	 */
	public function resolve_from_request( View $view ) {
		$select_all = $this->is_select_all_request();

		if ( $select_all ) {
			if ( ! Config::is_cross_page_selection_enabled( $view ) ) {
				return new WP_Error( 'gravityview_bulk_select_all_disabled', __( 'Selecting entries across pages is not enabled for this View.', 'gk-gravityview' ) );
			}

			return $this->resolve_all_from_request( $view );
		}

		$entry_ids = $this->get_requested_entry_ids( $view );

		if ( is_wp_error( $entry_ids ) ) {
			return $entry_ids;
		}

		if ( empty( $entry_ids ) ) {
			return new WP_Error( 'gravityview_bulk_no_entries', __( 'Select one or more entries before applying a bulk action.', 'gk-gravityview' ) );
		}

		if ( ! Config::is_cross_page_selection_enabled( $view ) ) {
			return $this->get_current_page_entries_for_view( $entry_ids, $view );
		}

		return $this->get_entries_for_view( $entry_ids, $view );
	}

	/**
	 * Returns explicit entry IDs from the current request.
	 *
	 * @since 3.0.0
	 *
	 * @param View     $view View.
	 * @param int|null $max Maximum entry IDs to parse. Defaults to the internal selected-entry cap.
	 *
	 * @return int[]|WP_Error
	 */
	public function get_requested_entry_ids( View $view, ?int $max = null ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Called only from request handlers after nonce verification.
		return $this->parse_entry_ids( isset( $_POST[ Config::POST_ENTRIES ] ) ? wp_unslash( $_POST[ Config::POST_ENTRIES ] ) : '', $view, $max );
	}

	/**
	 * Returns excluded entry IDs from the current select-all request.
	 *
	 * @since 3.0.0
	 *
	 * @param View     $view View.
	 * @param int|null $max Maximum entry IDs to parse. Defaults to the internal selected-entry cap.
	 *
	 * @return int[]|WP_Error
	 */
	public function get_excluded_entry_ids( View $view, ?int $max = null ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Called only from request handlers after nonce verification.
		return $this->parse_entry_ids( isset( $_POST[ Config::POST_EXCLUDED ] ) ? wp_unslash( $_POST[ Config::POST_EXCLUDED ] ) : '', $view, $max );
	}

	/**
	 * Returns the current selected entry count when it can be calculated cheaply.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view               View.
	 * @param int[] $excluded_entry_ids Excluded IDs for select-all requests.
	 *
	 * @return int|null
	 */
	public function get_selected_count_from_request( View $view, array $excluded_entry_ids = [] ) {
		if ( ! $this->is_select_all_request() ) {
			$entry_ids = $this->get_requested_entry_ids( $view );

			return is_wp_error( $entry_ids ) ? null : count( $entry_ids );
		}

		$total = $this->get_total_entries_for_view( $view );

		if ( null === $total ) {
			return null;
		}

		return max( 0, $total - count( $excluded_entry_ids ) );
	}

	/**
	 * Returns sanitized request arguments needed to rebuild the View query.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_request_args_for_background_selection() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Captures current query args for a nonce-verified bulk action so background select-all can replay the same View filters.
		$args = wp_unslash( $_GET );

		foreach ( [ 'pagenum', Config::QUERY_TOKEN, Config::QUERY_SELECTION_MODE, Config::QUERY_SELECTION_TOKEN, 'gv_bulk_status', 'gv_bulk_message', 'gv_bulk_view_id' ] as $key ) {
			unset( $args[ $key ] );
		}

		$args = is_array( $args ) ? $args : [];

		// Background select-all replays every page, so per-View pagination is dropped too.
		return PaginationKeys::strip_scoped( $args );
	}

	/**
	 * Parses entry IDs from POST data.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed     $raw  Raw value.
	 * @param View|null $view Optional View context.
	 * @param int|null  $max  Maximum entry IDs. Defaults to the internal selected-entry cap.
	 *
	 * @return int[]|WP_Error
	 */
	public function parse_entry_ids( $raw, ?View $view = null, ?int $max = null ) {
		$ids = [];
		$max = null === $max ? Config::get_max_entry_ids( $view ) : max( 1, $max );

		$add_value = function ( $value ) use ( &$add_value, &$ids, $max ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $nested_value ) {
					$result = $add_value( $nested_value );

					if ( is_wp_error( $result ) ) {
						return $result;
					}
				}

				return null;
			}

			if ( ! is_scalar( $value ) ) {
				return null;
			}

			$parts = preg_split( '/[,\s]+/', (string) $value, -1, PREG_SPLIT_NO_EMPTY );

			foreach ( $parts as $part ) {
				$entry_id = absint( $part );

				if ( ! $entry_id ) {
					continue;
				}

				$ids[ $entry_id ] = $entry_id;

				if ( count( $ids ) > $max ) {
					return $this->too_many_entries_error( $max );
				}
			}

			return null;
		};

		$result = $add_value( $raw );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array_values( $ids );
	}

	/**
	 * Retrieves and verifies entries against the submitted View.
	 *
	 * @since 3.0.0
	 *
	 * @param int[] $entry_ids Entry IDs.
	 * @param View  $view      View.
	 *
	 * @return array|WP_Error
	 */
	private function get_entries_for_view( array $entry_ids, View $view ) {
		$entry_ids       = array_values( array_unique( array_map( 'absint', $entry_ids ) ) );
		$entry_id_lookup = array_fill_keys( $entry_ids, true );

		add_action(
			'gravityview/view/query',
			$entry_subset_callback = function ( &$query, $queried_view ) use ( $entry_ids, $view ) {
				if ( (int) $queried_view->ID !== (int) $view->ID ) {
					return;
				}

				$entry_literals = array_map(
					static function ( $entry_id ) {
						return new \GF_Query_Literal( $entry_id );
					},
					$entry_ids
				);

				$query_parts = $query->_introspect();
				$query->where(
					\GF_Query_Condition::_and(
						$query_parts['where'],
						new \GF_Query_Condition(
							new \GF_Query_Column( 'id', $view->form->ID ),
							\GF_Query_Condition::IN,
							new \GF_Query_Series( $entry_literals )
						)
					)
				);

				$query->limit( count( $entry_ids ) )->offset( 0 );
			},
			10,
			2
		);

		add_filter(
			'gravityview_search_criteria',
			$remove_paging = static function ( $criteria ) use ( $entry_ids ) {
				$criteria['paging'] = [
					'current_page' => 1,
					'offset'       => 0,
					'page_size'    => max( 1, count( $entry_ids ) ),
				];

				return $criteria;
			}
		);

		try {
			$view_entries = $view->get_entries()->all();
		} finally {
			remove_action( 'gravityview/view/query', $entry_subset_callback );
			remove_filter( 'gravityview_search_criteria', $remove_paging );
		}

		$entries = [];

		foreach ( $view_entries as $entry ) {
			$entry_array = $entry->as_entry();
			$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

			if ( ! $entry_id || ! isset( $entry_id_lookup[ $entry_id ] ) ) {
				continue;
			}

			$entries[ $entry_id ] = $entry_array;
		}

		if ( count( $entries ) !== count( $entry_ids ) ) {
			return new WP_Error( 'gravityview_bulk_entry_forbidden', __( 'One or more selected entries are not available in this View.', 'gk-gravityview' ) );
		}

		$ordered_entries = [];

		foreach ( $entry_ids as $entry_id ) {
			$ordered_entries[ $entry_id ] = $entries[ $entry_id ];
		}

		return $ordered_entries;
	}

	/**
	 * Retrieves and verifies entries against the current page of the submitted View.
	 *
	 * @since 3.0.0
	 *
	 * @param int[] $entry_ids Entry IDs.
	 * @param View  $view      View.
	 *
	 * @return array|WP_Error
	 */
	private function get_current_page_entries_for_view( array $entry_ids, View $view ) {
		$entry_ids       = array_values( array_unique( array_map( 'absint', $entry_ids ) ) );
		$entry_id_lookup = array_fill_keys( $entry_ids, true );
		$view_entries    = $view->get_entries()->all();
		$entries         = [];

		foreach ( $view_entries as $entry ) {
			$entry_array = $entry->as_entry();
			$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

			if ( ! $entry_id || ! isset( $entry_id_lookup[ $entry_id ] ) ) {
				continue;
			}

			$entries[ $entry_id ] = $entry_array;
		}

		if ( count( $entries ) !== count( $entry_ids ) ) {
			return new WP_Error( 'gravityview_bulk_entry_forbidden', __( 'One or more selected entries are not available on this page.', 'gk-gravityview' ) );
		}

		$ordered_entries = [];

		foreach ( $entry_ids as $entry_id ) {
			$ordered_entries[ $entry_id ] = $entries[ $entry_id ];
		}

		return $ordered_entries;
	}

	/**
	 * Retrieves all entries available in the current View request.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|WP_Error
	 */
	private function get_all_entries_for_view( View $view ) {
		$max = Config::get_max_entry_ids( $view );

		add_filter(
			'gravityview_search_criteria',
			$remove_paging = static function ( $criteria ) use ( $max ) {
				$criteria['paging'] = [
					'current_page' => 1,
					'offset'       => 0,
					'page_size'    => $max + 1,
				];

				return $criteria;
			}
		);

		try {
			$view_entries = $view->get_entries()->all();
		} finally {
			remove_filter( 'gravityview_search_criteria', $remove_paging );
		}

		if ( count( $view_entries ) > $max ) {
			return $this->too_many_entries_error( $max );
		}

		$entries = [];

		foreach ( $view_entries as $entry ) {
			$entry_array = $entry->as_entry();
			$entry_id    = empty( $entry_array['id'] ) ? 0 : (int) $entry_array['id'];

			if ( ! $entry_id ) {
				continue;
			}

			$entries[ $entry_id ] = $entry_array;
		}

		if ( empty( $entries ) ) {
			return new WP_Error( 'gravityview_bulk_entry_forbidden', __( 'One or more selected entries are not available in this View.', 'gk-gravityview' ) );
		}

		return $entries;
	}

	/**
	 * Resolves all request entries, minus submitted exclusions.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|WP_Error
	 */
	private function resolve_all_from_request( View $view ) {
		$entries = $this->get_all_entries_for_view( $view );

		if ( is_wp_error( $entries ) ) {
			return $entries;
		}

		$excluded_entry_ids = $this->get_excluded_entry_ids( $view );

		if ( is_wp_error( $excluded_entry_ids ) ) {
			return $excluded_entry_ids;
		}

		foreach ( $excluded_entry_ids as $excluded_entry_id ) {
			unset( $entries[ $excluded_entry_id ] );
		}

		if ( empty( $entries ) ) {
			return new WP_Error( 'gravityview_bulk_no_entries', __( 'Select one or more entries before applying a bulk action.', 'gk-gravityview' ) );
		}

		return $entries;
	}

	/**
	 * Returns the total number of entries available in the current View request.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return int|null
	 */
	private function get_total_entries_for_view( View $view ) {
		try {
			return max( 0, (int) $view->get_entries()->total() );
		} catch ( \Throwable $e ) {
			return null;
		}
	}

	/**
	 * Returns a too-many-entries error.
	 *
	 * @since 3.0.0
	 *
	 * @param int $max Maximum entry count.
	 *
	 * @return WP_Error
	 */
	private function too_many_entries_error( $max ) {
		return new WP_Error(
			'gravityview_bulk_too_many_entries',
			strtr(
				/* translators: [count] is the maximum number of entries allowed in one bulk action request. */
				_n( 'Select [count] entry or fewer at a time.', 'Select [count] entries or fewer at a time.', $max, 'gk-gravityview' ),
				[
					'[count]' => $max,
				]
			)
		);
	}
}
