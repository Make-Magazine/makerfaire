<?php
/**
 * Selected-entry display mode for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use GravityKit\GravityView\Pagination\PaginationKeys;
use GravityKit\GravityView\View\View;

/**
 * Stores selected entry IDs and filters a View to those entries.
 *
 * @since 3.0.0
 */
final class SelectionMode {
	/**
	 * @since 3.0.0
	 */
	public const TRANSIENT_PREFIX = 'gv_bulk_selected_';

	/**
	 * @since 3.0.0
	 * @var EntryResolver
	 */
	private $entry_resolver;

	/**
	 * @since 3.0.0
	 * @var ViewEligibility
	 */
	private $eligibility;

	/**
	 * @since 3.0.0
	 * @var FlashMessages
	 */
	private $flash_messages;

	/**
	 * @since 3.0.0
	 * @var array
	 */
	private $entry_ids_by_view = [];

	/**
	 * @since 3.0.0
	 *
	 * @param EntryResolver   $entry_resolver Entry resolver.
	 * @param ViewEligibility $eligibility    View eligibility checker.
	 * @param FlashMessages   $flash_messages Flash message service.
	 * @param bool            $register_hooks Whether query hooks should be registered.
	 */
	public function __construct( EntryResolver $entry_resolver, ViewEligibility $eligibility, FlashMessages $flash_messages, $register_hooks = true ) {
		$this->entry_resolver = $entry_resolver;
		$this->eligibility    = $eligibility;
		$this->flash_messages = $flash_messages;

		if ( $register_hooks ) {
			add_action( 'gravityview/view/query', [ $this, 'filter_query' ], 10, 2 );
		}
	}

	/**
	 * Processes a Show selected entries request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_request() {
		if ( empty( $_POST[ Config::POST_SHOW_SELECTED ] ) || empty( $_POST[ Config::POST_VIEW_ID ] ) ) {
			return;
		}

		$view_id = absint( wp_unslash( $_POST[ Config::POST_VIEW_ID ] ) );
		$view    = View::by_id( $view_id );

		if ( ! $view ) {
			return $this->flash_messages->redirect( $view_id, __( 'The View could not be found.', 'gk-gravityview' ), 'error' );
		}

		if ( ! $this->eligibility->is_enabled_for_view( $view ) ) {
			return $this->flash_messages->redirect( $view_id, __( 'Bulk actions are not enabled for this View.', 'gk-gravityview' ), 'error' );
		}

		if ( ! Config::is_show_selected_enabled( $view ) ) {
			return $this->flash_messages->redirect( $view_id, __( 'Show selected entries is not enabled for this View.', 'gk-gravityview' ), 'error' );
		}

		$nonce = empty( $_POST[ Config::POST_NONCE ] ) ? '' : sanitize_text_field( wp_unslash( $_POST[ Config::POST_NONCE ] ) );

		if ( ! wp_verify_nonce( $nonce, Config::get_nonce_action( $view ) ) ) {
			return $this->flash_messages->redirect( $view_id, __( 'The request was invalid. Refresh the page and try again.', 'gk-gravityview' ), 'error' );
		}

		$entries = $this->entry_resolver->resolve_from_request( $view );

		if ( is_wp_error( $entries ) ) {
			return $this->flash_messages->redirect( $view_id, $entries->get_error_message(), 'error' );
		}

		$this->redirect_to_selection( $view, array_keys( $entries ) );
	}

	/**
	 * Whether the current request is showing only selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public function is_active( View $view ) {
		return [] !== $this->get_entry_ids( $view );
	}

	/**
	 * Returns a URL that exits Show selected mode.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_show_all_url() {
		return remove_query_arg( [ Config::QUERY_SELECTION_MODE, Config::QUERY_SELECTION_TOKEN ] );
	}

	/**
	 * Filters a View query to the selected entries.
	 *
	 * @since 3.0.0
	 *
	 * @param \GF_Query $query        Gravity Forms query.
	 * @param View      $queried_view Queried View.
	 *
	 * @return void
	 */
	public function filter_query( &$query, $queried_view ) {
		if ( ! $queried_view instanceof View ) {
			return;
		}

		$entry_ids = $this->get_entry_ids( $queried_view );

		if ( [] === $entry_ids ) {
			return;
		}

		if ( empty( $entry_ids ) ) {
			$entry_ids = [ 0 ];
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
					new \GF_Query_Column( 'id', $queried_view->form->ID ),
					\GF_Query_Condition::IN,
					new \GF_Query_Series( $entry_literals )
				)
			)
		);
	}

	/**
	 * Redirects to Show selected mode after persisting entry IDs server-side.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view      View.
	 * @param int[] $entry_ids Entry IDs.
	 *
	 * @return void
	 */
	private function redirect_to_selection( View $view, array $entry_ids ) {
		$token     = wp_generate_uuid4();
		$entry_ids = array_values( array_unique( array_map( 'absint', $entry_ids ) ) );

		WPHelper::set_transient(
			$this->get_transient_key( $token ),
			[
				'view_id'   => (int) $view->ID,
				'entry_ids' => $entry_ids,
			],
			Config::get_show_selected_ttl( $view )
		);

		$url = add_query_arg(
			[
				Config::QUERY_SELECTION_MODE  => Config::SELECTION_MODE_SHOW_SELECTED,
				Config::QUERY_SELECTION_TOKEN => rawurlencode( $token ),
			],
			remove_query_arg( array_merge( PaginationKeys::keys_to_strip( $view ), [ Config::QUERY_TOKEN ] ), Config::get_form_action_url() )
		);

		wp_safe_redirect( $url );

		/**
		 * Filters whether request processing should exit after redirecting.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $exit    Whether to exit after redirect.
		 * @param int    $view_id View ID.
		 * @param string $status  Redirect status context.
		 * @param string $url     Redirect URL.
		 * @param string $message Redirect message.
		 */
		if ( apply_filters( 'gk/gravityview/bulk-actions/exit-after-redirect', true, (int) $view->ID, 'show_selected', $url, '' ) ) {
			exit;
		}
	}

	/**
	 * Returns entry IDs stored for the current selection token.
	 *
	 * An empty array means a valid token exists with no IDs. Null-equivalent
	 * state is represented by [] from the public call site using strict checks.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return int[]
	 */
	private function get_entry_ids( View $view ) {
		$view_id = (int) $view->ID;

		if ( array_key_exists( $view_id, $this->entry_ids_by_view ) ) {
			return $this->entry_ids_by_view[ $view_id ];
		}

		$this->entry_ids_by_view[ $view_id ] = [];

		if ( ! Config::is_show_selected_enabled( $view ) ) {
			return [];
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only token used to display a stored show-selected selection for the current user.
		$mode = empty( $_GET[ Config::QUERY_SELECTION_MODE ] ) ? '' : sanitize_key( wp_unslash( $_GET[ Config::QUERY_SELECTION_MODE ] ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only token used to display a stored show-selected selection for the current user.
		$token = empty( $_GET[ Config::QUERY_SELECTION_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_GET[ Config::QUERY_SELECTION_TOKEN ] ) );

		if ( Config::SELECTION_MODE_SHOW_SELECTED !== $mode || ! $token ) {
			return [];
		}

		$selection = WPHelper::get_transient( $this->get_transient_key( $token ) );

		if ( ! is_array( $selection ) || (int) ( $selection['view_id'] ?? 0 ) !== $view_id || empty( $selection['entry_ids'] ) || ! is_array( $selection['entry_ids'] ) ) {
			return [];
		}

		$this->entry_ids_by_view[ $view_id ] = array_values( array_unique( array_filter( array_map( 'absint', $selection['entry_ids'] ) ) ) );

		return $this->entry_ids_by_view[ $view_id ];
	}

	/**
	 * Returns the transient key for a selected-entry token.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Selection token.
	 *
	 * @return string
	 */
	private function get_transient_key( $token ) {
		return self::TRANSIENT_PREFIX . get_current_user_id() . '_' . sanitize_key( $token );
	}
}
