<?php
/**
 * @license MIT
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\QueryFilters\Rest\Choice;

use GFCommon;
use GravityKit\GravityView\QueryFilters\Querying\Form\Form;
use GravityKit\GravityView\QueryFilters\Querying\Form\FormCriteria;
use GravityKit\GravityView\QueryFilters\Querying\Form\GravityFormsFormRepository;
use GravityKit\GravityView\QueryFilters\Querying\User\User;
use GravityKit\GravityView\QueryFilters\Querying\User\UserCriteria;
use GravityKit\GravityView\QueryFilters\Querying\User\WordPressUserRepository;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST controller that backs the `ChoicesField` endpoint contract.
 *
 * @since 2.12.0
 *
 * @phpstan-type Choice array{value: string, label: string, disabled?: bool}
 * @phpstan-type ChoiceGroup array{label: string, choices: array<Choice|ChoiceGroup>}
 * @phpstan-type ChoiceItem Choice|ChoiceGroup
 * @phpstan-type ChoiceResponse array{choices: ChoiceItem[], resolved: Choice[], hasMore: bool}
 */
final class ChoiceController {
	/**
	 * Capabilities that are allowed to consume the form / field choice routes.
	 *
	 * Read-only metadata (form titles/IDs and field choice lists) so the entry-viewing cap is
	 * the right gate — anyone who can view entries can know which form they're filtering against.
	 *
	 * @since 2.12.0
	 */
	private const CAPABILITIES = [ 'gravityforms_view_entries' ];

	/**
	 * Capabilities required to consume the user lookup route.
	 *
	 * @since 2.12.0
	 */
	private const USER_CAPABILITIES = [ 'list_users' ];

	/**
	 * Default page size when the caller doesn't supply a `limit`.
	 *
	 * @since 2.12.0
	 */
	private const RESULT_LIMIT = 50;

	/**
	 * Hard ceiling on `limit` — protects the server from runaway pagination requests.
	 *
	 * @since 2.12.0
	 */
	private const MAX_LIMIT = 200;

	/**
	 * The REST namespace prefix to register routes under.
	 *
	 * @since 2.12.0
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * @since 2.12.0
	 *
	 * @param string $prefix REST namespace prefix (e.g. `gravitycharts/v1`).
	 */
	public function __construct( string $prefix ) {
		$this->prefix = $prefix . '/query-filters';
	}

	/**
	 * Convenience: register both routes under the resolved prefix.
	 *
	 * @since 2.12.0
	 *
	 * @param string $prefix REST namespace prefix.
	 */
	public static function register( string $prefix ): void {
		( new self( $prefix ) )->register_routes();
	}

	/**
	 * Register the routes on `rest_api_init`.
	 *
	 * @since 2.12.0
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->prefix,
			'/choice/forms',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'find_forms' ],
				'permission_callback' => [ self::class, 'check_permission' ],
				'args'                => self::common_args(),
			]
		);

		register_rest_route(
			$this->prefix,
			'/choice/users',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'find_users' ],
				'permission_callback' => [ self::class, 'check_user_permission' ],
				'args'                => self::common_args(),
			]
		);
	}

	/**
	 * Permission gate for form / field choice routes — entry-viewer cap.
	 *
	 * @since 2.12.0
	 */
	public static function check_permission(): bool {
		return GFCommon::current_user_can_any( self::CAPABILITIES );
	}

	/**
	 * Permission gate for the user lookup route.
	 *
	 * @since 2.12.0
	 */
	public static function check_user_permission(): bool {
		return current_user_can( self::USER_CAPABILITIES[0] );
	}

	/**
	 * Find forms matching the query.
	 *
	 * @since 2.12.0
	 *
	 * @param WP_REST_Request $request The REST request.
	 *
	 * @return WP_REST_Response Response with shape {@see ChoiceResponse}.
	 */
	public function find_forms( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$query   = trim( (string) $request->get_param( 'q' ) );
		$resolve = self::sanitize_values( $request->get_param( 'values' ) );
		$offset  = max( 0, (int) $request->get_param( 'offset' ) );
		$limit   = self::clamp_limit( $request->get_param( 'limit' ) );

		$repository = new GravityFormsFormRepository( $wpdb );

		// Ask the repository for one extra row so we can detect overflow without a separate COUNT.
		$probe_limit = min( $limit + 1, FormCriteria::MAX_LIMIT );
		$forms       = $repository->search(
			FormCriteria::search( $query )
				->with_pagination( $probe_limit, $offset )
		);

		$has_more = count( $forms ) > $limit;
		if ( $has_more ) {
			$forms = array_slice( $forms, 0, $limit );
		}

		$resolved = $resolve === []
			? []
			: $repository->search( FormCriteria::for_ids( array_map( 'intval', $resolve ) ) );

		$to_choice = static fn( Form $form ): array => [
			'value' => (string) $form->id(),
			'label' => sprintf( '%s (#%d)', $form->title(), $form->id() ),
		];

		return new WP_REST_Response( self::build_response(
			array_map( $to_choice, $forms ),
			array_map( $to_choice, $resolved ),
			$has_more
		) );
	}

	/**
	 * Find users matching the query across login, email, nicename, display_name, first_name,
	 * last_name, and ID. An empty query returns the first page of users.
	 *
	 * @since 2.12.0
	 *
	 * @param WP_REST_Request $request The REST request.
	 *
	 * @return WP_REST_Response Response with shape {@see ChoiceResponse}.
	 */
	public function find_users( WP_REST_Request $request ): WP_REST_Response {
		$query   = trim( (string) $request->get_param( 'q' ) );
		$resolve = self::sanitize_values( $request->get_param( 'values' ) );
		$offset  = max( 0, (int) $request->get_param( 'offset' ) );
		$limit   = self::clamp_limit( $request->get_param( 'limit' ) );

		$repository = new WordPressUserRepository();

		$probe_limit = min( $limit + 1, UserCriteria::MAX_LIMIT );
		$users       = $repository->search(
			UserCriteria::search( $query )
				->with_pagination( $probe_limit, $offset )
		);

		$has_more = count( $users ) > $limit;
		if ( $has_more ) {
			$users = array_slice( $users, 0, $limit );
		}

		$resolved = $resolve === []
			? []
			: $repository->search( UserCriteria::for_ids( array_map( 'intval', $resolve ) ) );

		$to_choice = static fn( User $user ): array => $user->to_choice();

		return new WP_REST_Response( self::build_response(
			array_map( $to_choice, $users ),
			array_map( $to_choice, $resolved ),
			$has_more
		) );
	}

	/**
	 * Standard query string parameters shared by both routes.
	 *
	 * @since 2.12.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function common_args(): array {
		return [
			'q'      => [
				'type'              => 'string',
				'required'          => false,
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'values' => [
				'type'     => 'array',
				'required' => false,
				'default'  => [],
				'items'    => [ 'type' => 'string' ],
			],
			'offset' => [
				'type'              => 'integer',
				'required'          => false,
				'default'           => 0,
				'minimum'           => 0,
				'sanitize_callback' => 'absint',
			],
			'limit'  => [
				'type'              => 'integer',
				'required'          => false,
				'default'           => self::RESULT_LIMIT,
				'minimum'           => 1,
				'maximum'           => self::MAX_LIMIT,
				'sanitize_callback' => 'absint',
			],
		];
	}

	/**
	 * Clamp the requested limit to the allowed range.
	 *
	 * @since 2.12.0
	 *
	 * @param mixed $raw Raw param.
	 *
	 * @return int The clamped limit.
	 */
	private static function clamp_limit( $raw ): int {
		$limit = (int) $raw;
		if ( $limit <= 0 ) {
			return self::RESULT_LIMIT;
		}

		return min( $limit, self::MAX_LIMIT );
	}

	/**
	 * Normalise the raw `values` query param into a list of trimmed strings.
	 *
	 * @since 2.12.0
	 *
	 * @param mixed $raw Raw param value.
	 *
	 * @return string[] The sanitized values.
	 */
	private static function sanitize_values( $raw ): array {
		if ( ! is_array( $raw ) ) {
			return [];
		}

		$out = [];
		foreach ( $raw as $value ) {
			if ( ! is_scalar( $value ) ) {
				continue;
			}
			$trimmed = trim( (string) $value );
			if ( $trimmed !== '' ) {
				$out[] = $trimmed;
			}
		}

		return array_values( array_unique( $out ) );
	}

	/**
	 * Build the response envelope shared by both routes.
	 *
	 * @since 2.12.0
	 *
	 * @param Choice[] $choices  The suggestion list.
	 * @param Choice[] $resolved The hydrated label list for `values[]`.
	 * @param bool     $has_more Whether more results exist beyond what was returned.
	 *
	 * @return ChoiceResponse
	 */
	private static function build_response( array $choices, array $resolved, bool $has_more ): array {
		return [
			'choices'  => $choices,
			'resolved' => $resolved,
			'hasMore'  => $has_more,
		];
	}
}
