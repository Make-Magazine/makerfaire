<?php
/**
 * One-time flash messages for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\BackgroundJobs\ResultNotice;
use GravityKit\GravityView\Foundation\Helpers\WP as WPHelper;
use GravityKit\GravityView\View\View;

/**
 * Stores and renders one-time flash messages after bulk action redirects.
 *
 * @since 3.0.0
 */
final class FlashMessages {
	/**
	 * @since 3.0.0
	 */
	public const TRANSIENT_PREFIX = 'gv_bulk_flash_';

	/**
	 * @since 3.0.0
	 */
	public const SYSTEM_TRANSIENT_PREFIX = 'gv_bulk_system_';

	/**
	 * @since 3.0.0
	 * @var array
	 */
	private $messages = [];

	/**
	 * Renders a result message after redirect.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return void
	 */
	public function render( View $view, $render_instance = '' ) {
		$flash = $this->get( $view, $render_instance );

		if ( ! $flash ) {
			return;
		}

		$status = $flash['status'];
		$class  = 'error' === $status ? 'gv-bulk-actions-message gv-error error' : 'gv-bulk-actions-message updated';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ResultNotice::render() sanitizes message HTML and $class is generated from an internal status enum.
		echo \GVCommon::generate_notice( ResultNotice::render( $flash['message'] ), $class );

		WPHelper::delete_transient( $this->get_transient_key( $flash['token'] ) );
		unset( $this->messages[ $flash['token'] . ':' . $flash['render_instance'] ] );
	}

	/**
	 * Renders a one-time system message that is scoped to the current user and View.
	 *
	 * System flashes are only for validation failures that happen before a
	 * render instance can be trusted, such as customized markup missing required
	 * hidden fields. Action result messages must use render().
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return void
	 */
	public function render_system( View $view ) {
		$flash = $this->get_system( $view );

		if ( ! $flash ) {
			return;
		}

		$status = $flash['status'];
		$class  = 'error' === $status ? 'gv-bulk-actions-message gv-error error' : 'gv-bulk-actions-message updated';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ResultNotice::render() sanitizes message HTML and $class is generated from an internal status enum.
		echo \GVCommon::generate_notice( ResultNotice::render( $flash['message'] ), $class );

		WPHelper::delete_transient( $this->get_system_transient_key( $flash['token'] ) );
		unset( $this->messages[ 'system:' . $flash['token'] ] );
	}

	/**
	 * Whether the current request should display a bulk action message.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public function should_display( View $view, $render_instance = '' ) {
		return (bool) $this->get( $view, $render_instance );
	}

	/**
	 * Redirects back to the View URL with a result message.
	 *
	 * @since 3.0.0
	 *
	 * @param int          $view_id View ID.
	 * @param string|array $message Message string, or notice array with a message.
	 * @param string       $status          success|error.
	 * @param string       $render_instance Optional render instance ID.
	 *
	 * @return void
	 */
	public function redirect( $view_id, $message, $status, $render_instance = '' ) {
		$token           = wp_generate_uuid4();
		$status          = 'error' === $status ? 'error' : 'success';
		$notice          = ResultNotice::normalize( $message );
		$render_instance = $this->normalize_render_instance( $render_instance );

		WPHelper::set_transient(
			$this->get_transient_key( $token ),
			[
				'view_id'         => absint( $view_id ),
				'message'         => $notice['message'],
				'status'          => $status,
				'render_instance' => $render_instance,
			],
			5 * MINUTE_IN_SECONDS
		);

		$this->redirect_to_token( $view_id, $message, $status, $token );
	}

	/**
	 * Redirects back to the View URL with a user-scoped system message.
	 *
	 * @since 3.0.0
	 *
	 * @param int          $view_id View ID.
	 * @param string|array $message Message string, or notice array with a message.
	 * @param string       $status  success|error.
	 *
	 * @return void
	 */
	public function redirect_system( $view_id, $message, $status ) {
		$token  = wp_generate_uuid4();
		$status = 'error' === $status ? 'error' : 'success';
		$notice = ResultNotice::normalize( $message );

		WPHelper::set_transient(
			$this->get_system_transient_key( $token ),
			[
				'view_id' => absint( $view_id ),
				'message' => $notice['message'],
				'status'  => $status,
			],
			5 * MINUTE_IN_SECONDS
		);

		$this->redirect_to_token( $view_id, $message, $status, $token );
	}

	/**
	 * Sends the redirect for a stored flash token.
	 *
	 * @since 3.0.0
	 *
	 * @param int          $view_id View ID.
	 * @param string|array $message Message string, or notice array with a message.
	 * @param string       $status  success|error.
	 * @param string       $token   Flash token.
	 *
	 * @return void
	 */
	private function redirect_to_token( $view_id, $message, $status, $token ) {
		$url = add_query_arg( Config::QUERY_TOKEN, rawurlencode( $token ), Config::get_form_action_url() );

		wp_safe_redirect( $url );

		/**
		 * Filters whether request processing should exit after redirecting.
		 *
		 * @since 3.0.0
		 *
		 * @param bool   $exit    Whether to exit after redirect.
		 * @param int    $view_id View ID.
		 * @param string $status  Message status, either success or error.
		 * @param string $url     Redirect URL.
		 * @param mixed  $message Redirect message.
		 */
		if ( apply_filters( 'gk/gravityview/bulk-actions/exit-after-redirect', true, absint( $view_id ), $status, $url, $message ) ) {
			exit;
		}
	}

	/**
	 * Returns a one-time flash message for the View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|null
	 */
	private function get( View $view, $render_instance = '' ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only flash token; token resolves to a server-side transient scoped to the current user.
		$token = empty( $_GET[ Config::QUERY_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_GET[ Config::QUERY_TOKEN ] ) );

		if ( ! $token ) {
			return null;
		}

		$render_instance = sanitize_key( (string) $render_instance );
		$cache_key       = $token . ':' . $render_instance;

		if ( isset( $this->messages[ $cache_key ] ) ) {
			return $this->messages[ $cache_key ];
		}

		$flash = WPHelper::get_transient( $this->get_transient_key( $token ) );

		if ( ! is_array( $flash ) || empty( $flash['message'] ) || (int) ( $flash['view_id'] ?? 0 ) !== (int) $view->ID ) {
			return null;
		}

		$stored_render_instance = sanitize_key( (string) ( $flash['render_instance'] ?? '' ) );

		if ( '' === $stored_render_instance || '' === $render_instance || $stored_render_instance !== $render_instance ) {
			return null;
		}

		$this->messages[ $cache_key ] = [
			'token'           => $token,
			'status'          => 'error' === ( $flash['status'] ?? '' ) ? 'error' : 'success',
			'message'         => ResultNotice::normalize_message( $flash['message'] ),
			'render_instance' => $stored_render_instance,
		];

		return $this->messages[ $cache_key ];
	}

	/**
	 * Returns a one-time system flash message for the View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return array|null
	 */
	private function get_system( View $view ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only flash token; token resolves to a server-side transient scoped to the current user.
		$token = empty( $_GET[ Config::QUERY_TOKEN ] ) ? '' : sanitize_key( wp_unslash( $_GET[ Config::QUERY_TOKEN ] ) );

		if ( ! $token ) {
			return null;
		}

		$cache_key = 'system:' . $token;

		if ( isset( $this->messages[ $cache_key ] ) ) {
			return $this->messages[ $cache_key ];
		}

		$flash = WPHelper::get_transient( $this->get_system_transient_key( $token ) );

		if ( ! is_array( $flash ) || empty( $flash['message'] ) || (int) ( $flash['view_id'] ?? 0 ) !== (int) $view->ID ) {
			return null;
		}

		$this->messages[ $cache_key ] = [
			'token'   => $token,
			'status'  => 'error' === ( $flash['status'] ?? '' ) ? 'error' : 'success',
			'message' => ResultNotice::normalize_message( $flash['message'] ),
		];

		return $this->messages[ $cache_key ];
	}

	/**
	 * Normalizes a render instance from an explicit value or the current request.
	 *
	 * @since 3.0.0
	 *
	 * @param string $render_instance Render instance ID.
	 *
	 * @return string
	 */
	private function normalize_render_instance( $render_instance ) {
		return sanitize_key( (string) $render_instance );
	}

	/**
	 * Returns the transient key for a flash token.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Flash token.
	 *
	 * @return string
	 */
	private function get_transient_key( $token ) {
		return self::TRANSIENT_PREFIX . get_current_user_id() . '_' . sanitize_key( $token );
	}

	/**
	 * Returns the transient key for a system flash token.
	 *
	 * @since 3.0.0
	 *
	 * @param string $token Flash token.
	 *
	 * @return string
	 */
	private function get_system_transient_key( $token ) {
		return self::SYSTEM_TRANSIENT_PREFIX . get_current_user_id() . '_' . sanitize_key( $token );
	}
}
