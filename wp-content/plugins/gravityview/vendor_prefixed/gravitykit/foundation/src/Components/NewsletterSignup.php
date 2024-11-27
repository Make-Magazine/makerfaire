<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 26-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Components;

use GravityKit\GravityView\Foundation\Licenses\Framework;
use GravityKit\GravityView\Foundation\State\StateManager;
use GravityKit\GravityView\Foundation\State\UserStateManager;
use InvalidArgumentException;
use RuntimeException;
use WP_Error;

/**
 * Component that handles the newsletter signup form.
 *
 * @since 1.2.14
 */
final class NewsletterSignup {
	/**
	 * The state manager.
	 *
	 * @since 1.2.14
	 *
	 * @var null|StateManager
	 */
	private $state_manager;

	/**
	 * The singleton.
	 *
	 * @since 1.2.14
	 *
	 * @var self
	 */
	private static $_instance;

	/**
	 * The key used for the visibility state.
	 *
	 * @since 1.2.14
	 *
	 * @var string
	 */
	private const STATE_HIDE_SIGNUP_FORM = 'newsletter_hide_signup_form';

	/**
	 * The key used for the signed up state.
	 *
	 * @since 1.2.15
	 *
	 * @var string
	 */
	private const STATE_SIGNED_UP = 'newsletter_signed_up';

	/**
	 * The header to add to the email for signing purposes.
	 *
	 * @since 1.2.14
	 *
	 * @var string
	 */
	private const SIGNING_HEADER = 'X-Foundation-Signed';

	/**
	 * The URL to post the newsletter signup to.
	 *
	 * @since 1.2.14
	 *
	 * @var string
	 */
	private $form_endpoint = 'https://gravitykit.com/wp-json/gk-foundation/v1/newsletter-signup';

	/**
	 * Class instance.
	 *
	 * @since 1.2.14
	 *
	 * @param StateManager $state_manager The state manager.
	 * @param string       $form_endpoint The URL to post the newsletter signup to.
	 */
	private function __construct( StateManager $state_manager, string $form_endpoint = '' ) {
		$this->state_manager = $state_manager;

		if ( ! empty( $form_endpoint ) ) {
			$this->form_endpoint = $form_endpoint;
		}

		add_filter( 'gk/foundation/ajax/' . Framework::AJAX_ROUTER . '/routes', [ $this, 'configure_ajax_routes' ] );
		add_filter( 'gk/foundation/ajax/' . Framework::AJAX_ROUTER . '/params', [ $this, 'configure_ajax_params' ] );
	}

	/**
	 * Gets the single instance of this component.
	 *
	 * @since 1.2.14
	 *
	 * @param StateManager|null $state_manager The state manager.
	 *
	 * @return self
	 */
	public static function get_instance( StateManager $state_manager = null ): self {
		if ( null === self::$_instance ) {
			$endpoint = defined( 'GK_FOUNDATION_NEWSLETTER_SIGNUP_FORM_ENDPOINT' )
				? GK_FOUNDATION_NEWSLETTER_SIGNUP_FORM_ENDPOINT
				: '';

			self::$_instance = new self(
				$state_manager ?? new UserStateManager(),
				$endpoint
			);
		}

		return self::$_instance;
	}

	/**
	 * Configures Ajax routes handled by this class.
	 *
	 * @since 1.2.14
	 *
	 * @see   FoundationCore::process_ajax_request()
	 *
	 * @param array $routes Ajax route to class method map.
	 *
	 * @return array The updated routes.
	 */
	public function configure_ajax_routes( array $routes ): array {
		return array_merge(
			$routes,
			[
				'newsletter_hide_signup_form' => [ $this, 'ajax_hide_signup_form' ],
				'newsletter_signup'           => [ $this, 'ajax_newsletter_signup' ],
			]
		);
	}

	/**
	 * Configures Ajax params handled by this class.
	 *
	 * @since 1.2.14
	 *
	 * @see   FoundationCore::process_ajax_request()
	 *
	 * @param array $params The available ajax params.
	 *
	 * @return array The updated ajax params.
	 */
	public function configure_ajax_params( array $params ): array {
		return array_merge(
			$params,
			[
				'newsletterSignup' => [
					'hide' => $this->is_hidden(),
				],
			]
		);
	}

	/**
	 * Returns whether the signup form is hidden.
	 *
	 * @since 1.2.15
	 *
	 * @return bool Whether the signup form is hidden.
	 */
	private function is_hidden(): bool {
		if ( $this->state_manager->has( self::STATE_SIGNED_UP ) ) {
			return true;
		}

		if ( ! $this->state_manager->has( self::STATE_HIDE_SIGNUP_FORM ) ) {
			return false;
		}

		$time = $this->state_manager->get( self::STATE_HIDE_SIGNUP_FORM );

		if ( null === $time ) {
			// Update to include the current date.
			$this->ajax_hide_signup_form();

			return false;
		}

		return ( time() - $time ) < YEAR_IN_SECONDS;
	}

	/**
	 * Stores the hidden state for the newsletter sign up form.
	 *
	 * @since 1.2.14
	 *
	 * @param array|null $payload The ajax payload.
	 *
	 * @return void
	 */
	public function ajax_hide_signup_form( ?array $payload = null ): void {
		$this->state_manager->add( self::STATE_HIDE_SIGNUP_FORM, time() );
	}

	/**
	 * Processes the newsletter signup.
	 *
	 * @since 1.2.14
	 *
	 * @param array $payload The payload.
	 *
	 * @throws InvalidArgumentException When the provided email address is incorrect.
	 * @throws RuntimeException When anything went wrong during the processing.
	 *
	 * @return array The response object.
	 */
	public function ajax_newsletter_signup( array $payload ): array {
		$email = (string) ( $payload['email'] ?? '' );

		if ( ! is_email( $email ) ) {
			throw new InvalidArgumentException(
				esc_html__( 'The provided email address is not valid.', 'gk-gravityview' )
			);
		}

		$result = wp_remote_post(
			$this->form_endpoint,
			[
				'sslverify' => false,
				'headers'   => [
					self::SIGNING_HEADER => '1',
				],
				'body'      => [
					'email' => $email,
				],
			]
		);

		if ( $result instanceof WP_Error ) {
			throw new RuntimeException( $result->get_error_message() );
		}

		$message = json_decode( wp_remote_retrieve_body( $result ), true );

		if ( is_array( $message ) ) {
			$message = $message['message'] ?? '';
		}

		if ( 401 === wp_remote_retrieve_response_code( $result ) ) {
			throw new RuntimeException( __( 'Something went wrong on our end. Please try again later.', 'gk-gravityview' ) );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $result ) ) {
			throw new RuntimeException( $message );
		}

		// Hide the signup form for this user in the future.
		$this->state_manager->add( self::STATE_SIGNED_UP, time() );

		return [
			'message' => $message,
		];
	}
}
