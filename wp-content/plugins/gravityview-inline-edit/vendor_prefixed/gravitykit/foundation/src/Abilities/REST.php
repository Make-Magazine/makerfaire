<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityEdit\Foundation\Abilities;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * REST API endpoints for ability discovery and documentation.
 *
 * @since 1.23.0
 */
final class REST {

	private const NAMESPACE = 'gravitykit/v1';

	/**
	 * Ability registration manager.
	 *
	 * @since 1.23.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Initializes the REST sub-component.
	 *
	 * @since 1.23.0
	 *
	 * @param Manager $manager Ability registration manager.
	 */
	public function __construct( Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Registers REST API hooks.
	 *
	 * @since 1.23.0
	 */
	public function init(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Registers REST routes.
	 *
	 * @since 1.23.0
	 */
	public function register_routes(): void {
		// List all GravityKit abilities.
		register_rest_route(
			self::NAMESPACE,
			'/abilities',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_abilities' ],
				'permission_callback' => [ $this, 'can_view_abilities' ],
				'args'                => [
					'include_disabled' => [
						'type'              => 'boolean',
						'default'           => false,
						'description'       => __( 'Include abilities currently disabled via the Settings UI. Default false.', 'gk-foundation' ),
						'sanitize_callback' => 'rest_sanitize_boolean',
					],
					'product'          => [
						'type'              => 'string',
						'description'       => __( 'Filter by product slug, e.g. "gravityview" or "gravityimport". Each catalog item exposes its product as gk_product; use those values.', 'gk-foundation' ),
						'sanitize_callback' => 'sanitize_key',
					],
					'scope'            => [
						'type'              => 'string',
						'description'       => __( 'Filter by functional scope, e.g. "discovery" or "fields". Scopes group similar operations across products; each catalog item exposes its scope as gk_scope, derived from its primary category ("gk-gravityview-discovery" yields "discovery").', 'gk-foundation' ),
						'sanitize_callback' => 'sanitize_key',
					],
					'category'         => [
						'type'              => 'string',
						'description'       => __( 'Filter by full category slug, e.g. "gk-gravityview-discovery" or "gk-gravityview-views". Matches the ability\'s primary category exactly.', 'gk-foundation' ),
						'sanitize_callback' => 'sanitize_key',
					],
					'page'             => [
						'type'              => 'integer',
						'default'           => 1,
						'minimum'           => 1,
						'description'       => __( 'Page number for paginated results.', 'gk-foundation' ),
						'sanitize_callback' => 'absint',
					],
					'per_page'         => [
						'type'              => 'integer',
						'default'           => 50,
						'minimum'           => 1,
						'maximum'           => 100,
						'description'       => __( 'Items per page (1-100). Default 50.', 'gk-foundation' ),
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// Get single ability details.
		register_rest_route(
			self::NAMESPACE,
			'/abilities/(?P<name>' . Manager::NAME_PATTERN . ')',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_ability' ],
				'permission_callback' => [ $this, 'can_view_abilities' ],
				'args'                => [
					'name' => [
						'type'              => 'string',
						'required'          => true,
						'description'       => __( 'Ability name in gk-{product}/{ability} format, e.g. "gk-gravityview/views-list". Use the listing endpoint to discover available names.', 'gk-foundation' ),
						'validate_callback' => function ( $param ) {
							return is_string( $param ) && $this->manager->is_valid_name( $param );
						},
					],
				],
			]
		);
	}

	/**
	 * Permission check for viewing abilities.
	 *
	 * Restrict-only: the manage_options gate is enforced unconditionally; the
	 * filter can only narrow access, never widen it.
	 *
	 * @since 1.23.0
	 */
	public function can_view_abilities(): bool {
		$allowed = current_user_can( 'manage_options' );

		if ( ! $allowed ) {
			return false;
		}

		/**
		 * Filters whether the current request can view the GravityKit abilities catalog.
		 *
		 * Restrict-only: this filter runs only after the default manage_options
		 * gate has already passed. Return false to deny access; truthy returns
		 * cannot widen access for users who failed the cap check.
		 *
		 * @since 1.23.0
		 *
		 * @param bool $allowed Whether catalog access is allowed (always true at this point).
		 */
		return (bool) apply_filters( 'gk/foundation/abilities/rest/catalog/permission', $allowed );
	}

	/**
	 * Gets all GravityKit abilities.
	 *
	 * Supports filters (product, scope, category) and pagination (page,
	 * per_page). Disabled abilities are omitted by default; pass
	 * include_disabled=1 to include them.
	 *
	 * @since 1.23.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_abilities( WP_REST_Request $request ): WP_REST_Response {
		$include_disabled = (bool) $request->get_param( 'include_disabled' );
		$product          = (string) $request->get_param( 'product' );
		$scope            = (string) $request->get_param( 'scope' );
		$category         = (string) $request->get_param( 'category' );
		$page             = max( 1, (int) $request->get_param( 'page' ) );
		$per_page         = max( 1, min( 100, (int) $request->get_param( 'per_page' ) ) );

		$abilities = $this->manager->get_gravitykit_abilities( ! $include_disabled );

		if ( '' !== $product ) {
			$abilities = array_filter(
				$abilities,
				function ( $ability ) use ( $product ): bool {
					return $ability->get_meta_item( Manager::META_KEY_PRODUCT ) === $product;
				}
			);
		}

		if ( '' !== $scope ) {
			$abilities = array_filter(
				$abilities,
				function ( $ability ) use ( $scope ): bool {
					return $ability->get_meta_item( Manager::META_KEY_SCOPE ) === $scope;
				}
			);
		}

		if ( '' !== $category ) {
			$abilities = array_filter(
				$abilities,
				static function ( $ability ) use ( $category ): bool {
					return $ability->get_category() === $category;
				}
			);
		}

		$abilities = array_values( $abilities );
		$total     = count( $abilities );
		$pages     = (int) ceil( $total / $per_page );
		$offset    = ( $page - 1 ) * $per_page;
		$page_rows = array_slice( $abilities, $offset, $per_page );

		$items = [];
		foreach ( $page_rows as $ability ) {
			$items[] = $this->manager->to_rest_item( $ability );
		}

		$response = new WP_REST_Response( $items );
		$response->header( 'X-WP-Total', (string) $total );
		$response->header( 'X-WP-TotalPages', (string) $pages );

		return $response;
	}

	/**
	 * Gets single ability details.
	 *
	 * @since 1.23.0
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_ability( WP_REST_Request $request ) {
		$name    = $request->get_param( 'name' );
		$ability = $this->manager->get( $name );

		if ( ! $ability ) {
			return new WP_Error(
				'ability_not_found',
				__( 'Ability not found.', 'gk-foundation' ),
				[ 'status' => 404 ]
			);
		}

		// Check if it's a GravityKit ability.
		if ( $ability->get_meta_item( Manager::META_KEY_SOURCE ) !== 'gravitykit' ) {
			return new WP_Error(
				'ability_not_found',
				__( 'Ability not found.', 'gk-foundation' ),
				[ 'status' => 404 ]
			);
		}

		return new WP_REST_Response( $this->manager->to_rest_item( $ability, 'single' ) );
	}
}
