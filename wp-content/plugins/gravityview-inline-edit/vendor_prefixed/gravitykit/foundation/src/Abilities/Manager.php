<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityEdit\Foundation\Abilities;

use GravityKit\GravityEdit\Foundation\Logger\Framework as Logger;
use WP_Error;

/**
 * Manages ability registration and enable/disable state.
 *
 * This is a thin wrapper around wp_register_ability() that adds:
 * - Enable/disable functionality via wrapped permission_callback
 * - GravityKit metadata for discovery
 * - Logging
 *
 * @since 1.23.0
 */
final class Manager {

	public const DISABLED_OPTION           = 'gk_abilities_disabled';
	public const META_KEY_SOURCE           = 'gk_registered_by';
	public const META_KEY_CATEGORIES       = 'gk_categories';
	public const META_KEY_PRODUCT          = 'gk_product';
	public const META_KEY_SCOPE            = 'gk_scope';
	public const META_KEY_CONTRACT_VERSION = 'gk_contract_version';
	public const DEFAULT_CONTRACT_VERSION  = '1.0.0';

	/**
	 * Config fields required by register(). `name` is validated separately
	 * before normalization.
	 *
	 * @since 1.23.0
	 */
	public const REQUIRED_CONFIG_FIELDS = [ 'label', 'description', 'category', 'execute_callback', 'permission_callback' ];

	/**
	 * Prefix shared by GravityKit ability names and category slugs.
	 *
	 * @since 1.23.0
	 */
	public const SLUG_PREFIX = 'gk-';

	/**
	 * Regex fragment (no anchors or delimiters) matching a GravityKit ability name.
	 *
	 * @since 1.23.0
	 */
	public const NAME_PATTERN = self::SLUG_PREFIX . '[a-z0-9-]+/[a-z0-9-]+';

	/**
	 * Logger used to record registration events.
	 *
	 * @since 1.23.0
	 *
	 * @var Logger|null
	 */
	private ?Logger $logger = null;

	/**
	 * Catalog of registered ability configurations, keyed by name.
	 *
	 * @since 1.23.0
	 *
	 * @var array
	 */
	private array $catalog = [];

	/**
	 * Registered ability categories keyed by slug.
	 *
	 * @since 1.23.0
	 *
	 * @var array
	 */
	private array $categories = [];

	/**
	 * Lazily creates the ability logger.
	 *
	 * Deferred out of the constructor: Manager is instantiated on `plugins_loaded` (before
	 * `after_setup_theme`), and translating the logger label there trips WordPress 6.7's
	 * just-in-time translation notice. The label is only needed once a log entry is rendered.
	 *
	 * @since 1.23.0
	 *
	 * @return Logger
	 */
	private function logger(): Logger {
		if ( null === $this->logger ) {
			$this->logger = Logger::get_instance( 'abilities', __( 'Abilities', 'gk-foundation' ) );
		}

		return $this->logger;
	}

	/**
	 * Gets product declarations gathered from consuming plugins.
	 *
	 * @since 1.23.0
	 *
	 * @return array Product declarations keyed by product slug.
	 */
	public function products(): array {
		/**
		 * Filters the GravityKit product declarations.
		 *
		 * Each product declares its product-level ability data in one
		 * payload: the MCP tool-name prefix and the ability categories
		 * (product category and scope subcategories) to register. Foundation
		 * contributes no declarations of its own.
		 *
		 * @since 1.23.0
		 *
		 * @param array $products {
		 *     Product declarations keyed by product slug (e.g. `gravityview`).
		 *     Each declaration is an array:
		 *
		 *     @type string $mcp_prefix Short MCP tool-name prefix (e.g. `gv`). Required —
		 *                              a missing prefix triggers _doing_it_wrong() and the
		 *                              full product slug is used as a fallback.
		 *     @type array  $categories Optional. Ability categories to register, keyed
		 *                              by slug; each entry has `label` and `description`.
		 * }
		 */
		$products = apply_filters( 'gk/foundation/abilities/products', [] );

		/** @phpstan-ignore-next-line -- Defensive: a filter callback may return a non-array. */
		return is_array( $products ) ? $products : [];
	}

	/**
	 * Returns the canonical empty-object input schema.
	 *
	 * Every registered ability exposes an input schema so downstream consumers
	 * (REST input validation, the MCP catalog, schema sweeps) can rely on a
	 * uniform shape. Abilities that take no input omit `input_schema` at
	 * registration and {@see register()} fills in this default. The strict
	 * `additionalProperties => false` declares "this ability accepts no input"
	 * while still validating the empty `{}` payload such calls send.
	 *
	 * @since 1.23.1
	 *
	 * @return array{type: string, properties: object, additionalProperties: bool}
	 */
	public static function empty_input_schema(): array {
		return [
			'type'                 => 'object',
			'properties'           => (object) [],
			'additionalProperties' => false,
		];
	}

	/**
	 * Registers an ability with WordPress.
	 *
	 * Legacy top-level `annotations` and `show_in_rest` keys are accepted
	 * and merged into `meta` during normalization.
	 *
	 * @since 1.23.0
	 *
	 * @param array $config {
	 *     Ability configuration.
	 *
	 *     @type string   $name                Ability name as `namespace/ability-name`.
	 *     @type string   $label               Human-readable label.
	 *     @type string   $description         What the ability does.
	 *     @type string   $category            Primary category slug.
	 *     @type callable $execute_callback    Executes the ability.
	 *     @type callable $permission_callback Authorizes execution.
	 *     @type array    $input_schema        Optional. JSON Schema describing input.
	 *     @type array    $output_schema       Optional. JSON Schema describing output.
	 *     @type string[] $categories          Optional. Category slugs; the first is used as primary.
	 *     @type array    $meta                Optional. Ability meta. Set `command_palette` to true to
	 *                                         surface the ability in the Command Palette.
	 * }
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function register( array $config ) {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return $this->error(
				'gk_abilities_not_supported',
				__( 'The WordPress Abilities API is not available.', 'gk-foundation' ),
				[ 'name' => $config['name'] ?? 'unknown' ]
			);
		}

		$name = $config['name'] ?? '';

		if ( ! is_string( $name ) || '' === $name ) {
			$this->logger()->error(
				'Missing required field \'name\' in ability config',
				[
					'name' => 'unknown',
				]
			);

			$message = __( 'Ability config must include a non-empty string name.', 'gk-foundation' );
			$this->notify_invalid_registration( 'gk_abilities_invalid_name', $message, [ 'name' => $name ] );

			return $this->error(
				'gk_abilities_invalid_name',
				$message,
				[
					'name'   => $name,
					'reason' => 'empty_name',
				]
			);
		}

		if ( ! $this->is_valid_name( $name ) ) {
			$this->logger()->error(
				'Invalid ability name format',
				[
					'name'     => $name,
					'expected' => 'gk-product/ability-name',
				]
			);

			$message = __( 'Ability names must follow gk-{product}/{ability-name}.', 'gk-foundation' );
			$this->notify_invalid_registration( 'gk_abilities_invalid_name', $message, [ 'name' => $name ] );

			return $this->error(
				'gk_abilities_invalid_name',
				$message,
				[
					'name'   => $name,
					'reason' => 'invalid_format',
				]
			);
		}

		// Normalize legacy keys to current Abilities API arguments.
		$config = $this->normalize_config( $config );

		// Every ability exposes at least the canonical empty-object input schema
		// so downstream consumers (REST validation, the MCP catalog, schema
		// sweeps) can rely on a uniform shape. No-input abilities omit
		// input_schema at registration; the framework supplies the default here
		// rather than making each ability repeat the boilerplate. Capture the
		// original presence first so the permission wrapper still forwards input
		// only to abilities that genuinely declared a schema.
		$declares_input = ! empty( $config['input_schema'] );
		if ( ! $declares_input ) {
			$config['input_schema'] = self::empty_input_schema();
		}

		// Validate required fields.
		foreach ( self::REQUIRED_CONFIG_FIELDS as $field ) {
			if ( empty( $config[ $field ] ) ) {
				$this->logger()->error(
					"Missing required field '{$field}' in ability config",
					[
						'name' => $name,
					]
				);

				$message = sprintf(
					/* translators: %s: missing ability config field */
					__( 'Missing required ability config field: %s.', 'gk-foundation' ),
					$field
				);
				$this->notify_invalid_registration(
					'gk_abilities_missing_field',
					$message,
					[
						'name'  => $name,
						'field' => $field,
					]
				);

				return $this->error(
					'gk_abilities_missing_field',
					$message,
					[
						'name'  => $name,
						'field' => $field,
					]
				);
			}
		}

		if ( ! is_string( $config['label'] ) || ! is_string( $config['description'] ) || ! is_string( $config['category'] ) ) {
			$this->logger()->error(
				'Invalid ability config types',
				[
					'name' => $name,
				]
			);

			$message = __( 'Ability label, description, and category must be strings.', 'gk-foundation' );
			$this->notify_invalid_registration( 'gk_abilities_invalid_type', $message, [ 'name' => $name ] );

			return $this->error(
				'gk_abilities_invalid_type',
				$message,
				[ 'name' => $name ]
			);
		}

		if ( ! is_callable( $config['execute_callback'] ) || ! is_callable( $config['permission_callback'] ) ) {
			$this->logger()->error(
				'Ability callbacks must be callable',
				[
					'name' => $name,
				]
			);

			$message = __( 'Ability execute_callback and permission_callback must be callable.', 'gk-foundation' );
			$this->notify_invalid_registration( 'gk_abilities_invalid_callback', $message, [ 'name' => $name ] );

			return $this->error(
				'gk_abilities_invalid_callback',
				$message,
				[ 'name' => $name ]
			);
		}

		// Snapshot before the invocation-wrapping and key-stripping below; the
		// catalog stores the config as supplied, and only once registration
		// succeeds so failed registrations never surface as phantom abilities.
		$catalog_config = $config;

		// Always register with WP core regardless of disabled state. The
		// wrapped permission_callback's is_disabled() gate denies invocation
		// with HTTP 403 'ability_disabled', so the ability remains visible
		// in catalogs and discovery surfaces but cannot be executed. This is
		// the option-backed permission-denial model: state changes never
		// require re-registration outside the wp_abilities_api_init window.
		$config['permission_callback'] = $this->wrap_permission_callback(
			$name,
			$config['permission_callback'],
			$declares_input
		);

		unset( $config['name'], $config['categories'], $config['callback'], $config['annotations'], $config['show_in_rest'] );

		$result = wp_register_ability( $name, $config );

		/** @phpstan-ignore-next-line -- Defensive check for future API compatibility. */
		if ( is_wp_error( $result ) ) {
			$this->logger()->error(
				"Failed to register ability '{$name}'",
				[
					'error' => $result->get_error_message(),
				]
			);

			return $this->error(
				'gk_abilities_registration_failed',
				$result->get_error_message(),
				[
					'name'  => $name,
					'error' => $result,
				]
			);
		}

		if ( null === $result ) {
			$this->logger()->error(
				"Failed to register ability '{$name}'",
				[
					'error' => 'Abilities API did not register the ability.',
				]
			);

			return $this->error(
				'gk_abilities_registration_failed',
				__( 'Abilities API did not register the ability.', 'gk-foundation' ),
				[ 'name' => $name ]
			);
		}

		$this->catalog[ $name ] = $catalog_config;

		$this->logger()->debug(
			"Registered ability '{$name}'",
			[
				'categories' => $config['meta'][ self::META_KEY_CATEGORIES ] ?? [],
			]
		);

		return true;
	}

	/**
	 * Creates a fluent ability builder.
	 *
	 * Intentional alias of {@see Manager::builder()}; both are documented
	 * public API in the Abilities README so neither should be removed.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return AbilityBuilder
	 */
	public function ability( string $name ): AbilityBuilder {
		return new AbilityBuilder( $name, $this );
	}

	/**
	 * Creates a fluent ability builder.
	 *
	 * Intentional alias of {@see Manager::ability()}; both are documented
	 * public API in the Abilities README so neither should be removed.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return AbilityBuilder
	 */
	public function builder( string $name ): AbilityBuilder {
		return $this->ability( $name );
	}

	/**
	 * Registers an ability category.
	 *
	 * @since 1.23.0
	 *
	 * @param string $slug Category slug.
	 * @param array  $args Category arguments.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function register_category( string $slug, array $args ) {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return $this->error(
				'gk_abilities_not_supported',
				__( 'The WordPress Abilities API category registry is not available.', 'gk-foundation' ),
				[ 'category' => $slug ]
			);
		}

		$result = wp_register_ability_category( $slug, $args );

		/** @phpstan-ignore-next-line -- Defensive check for future API compatibility. */
		if ( is_wp_error( $result ) ) {
			return $this->error(
				'gk_abilities_registration_failed',
				$result->get_error_message(),
				[
					'category' => $slug,
					'error'    => $result,
				]
			);
		}

		if ( null === $result ) {
			return $this->error(
				'gk_abilities_registration_failed',
				__( 'Abilities API did not register the category.', 'gk-foundation' ),
				[ 'category' => $slug ]
			);
		}

		$this->categories[ $slug ] = array_merge(
			[
				'slug' => $slug,
			],
			$args
		);

		return true;
	}

	/**
	 * Gets registered categories.
	 *
	 * @since 1.23.0
	 *
	 * @param array $filters Optional filters.
	 * @return array
	 */
	public function categories( array $filters = [] ): array {
		$categories = $this->categories;

		if ( ! empty( $filters['product'] ) ) {
			$product    = sanitize_key( (string) $filters['product'] );
			$categories = array_filter(
				$categories,
				function ( array $category ) use ( $product ): bool {
					$slug = (string) ( $category['slug'] ?? '' );
					return $this->is_product_category( $slug, $product );
				}
			);
		}

		return $categories;
	}

	/**
	 * Whether a category slug belongs to a product.
	 *
	 * Matches the product's own category (`gk-{product}`) and its scope
	 * subcategories (`gk-{product}-*`).
	 *
	 * @since 1.23.0
	 *
	 * @param string $slug    Category slug.
	 * @param string $product Product slug.
	 * @return bool
	 */
	private function is_product_category( string $slug, string $product ): bool {
		$is_product_slug = ( self::SLUG_PREFIX . $product ) === $slug;
		$is_scope_slug   = 0 === strpos( $slug, $this->scope_slug_prefix( $product ) );

		return $is_product_slug || $is_scope_slug;
	}

	/**
	 * Gets GravityKit abilities with optional filters.
	 *
	 * @since 1.23.0
	 *
	 * @param array $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function all( array $filters = [] ): array {
		$enabled_only = array_key_exists( 'enabled_only', $filters ) ? (bool) $filters['enabled_only'] : true;
		$abilities    = $this->get_gravitykit_abilities( $enabled_only );

		if ( ! empty( $filters['product'] ) ) {
			$product   = sanitize_key( (string) $filters['product'] );
			$abilities = array_filter(
				$abilities,
				function ( $ability ) use ( $product ): bool {
					return $this->get_ability_product( $ability ) === $product;
				}
			);
		}

		if ( ! empty( $filters['scope'] ) ) {
			$scope     = sanitize_key( (string) $filters['scope'] );
			$abilities = array_filter(
				$abilities,
				function ( $ability ) use ( $scope ): bool {
					return $this->get_ability_scope( $ability ) === $scope;
				}
			);
		}

		return $abilities;
	}

	/**
	 * Gets GravityKit abilities for one product.
	 *
	 * @since 1.23.0
	 *
	 * @param string $product Product slug.
	 * @param array  $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function by_product( string $product, array $filters = [] ): array {
		$filters['product'] = $product;
		return $this->all( $filters );
	}

	/**
	 * Gets GravityKit abilities for one scope.
	 *
	 * @since 1.23.0
	 *
	 * @param string $scope Scope slug.
	 * @param array  $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function by_scope( string $scope, array $filters = [] ): array {
		$filters['scope'] = $scope;
		return $this->all( $filters );
	}

	/**
	 * Converts an ability to the Foundation REST catalog shape.
	 *
	 * The $context value is echoed into the response so clients can branch
	 * on it. Today there is one effective context ('catalog' and 'single'
	 * produce the same body). The parameter is a forward-looking projection
	 * lever — a future 'minimal' context could omit the input/output
	 * schemas for fast list rendering.
	 *
	 * @since 1.23.0
	 *
	 * @param \WP_Ability $ability Ability object.
	 * @param string      $context Output context: 'catalog' (default) or 'single'.
	 * @return array
	 */
	public function to_rest_item( \WP_Ability $ability, string $context = 'catalog' ): array {
		$name        = $ability->get_name();
		$meta        = $ability->get_meta();
		$annotations = $meta['annotations'] ?? [];
		$product     = $this->get_ability_product( $ability );
		$scope       = $this->get_ability_scope( $ability );

		return [
			'name'                          => $name,
			'label'                         => $ability->get_label(),
			'description'                   => $ability->get_description(),
			'category'                      => $ability->get_category(),
			'categories'                    => $this->get_label_resolved_categories( $ability ),
			'input_schema'                  => $ability->get_input_schema(),
			'output_schema'                 => $ability->get_output_schema(),
			'annotations'                   => is_array( $annotations ) ? $annotations : [],
			'enabled'                       => $this->is_enabled( $name ),
			self::META_KEY_PRODUCT          => $product,
			self::META_KEY_SCOPE            => $scope,
			self::META_KEY_CONTRACT_VERSION => $ability->get_meta_item( self::META_KEY_CONTRACT_VERSION ) ?: self::DEFAULT_CONTRACT_VERSION,
			'rest_run_url'                  => $this->get_rest_run_url( $name ),
			'mcp_tool_name'                 => $this->get_mcp_tool_name( $name ),
			'context'                       => $context,
		];
	}

	/**
	 * Wraps permission callback to add rate-limit and enable/disable checks.
	 *
	 * @since 1.23.0
	 *
	 * @filter gk/foundation/abilities/rate-limit Return WP_Error to short-circuit execution.
	 *
	 * @param string   $name          Ability name.
	 * @param callable $callback      Original permission callback.
	 * @param bool     $expects_input Whether the original callback expects an input argument.
	 * @return callable Wrapped callback.
	 */
	private function wrap_permission_callback( string $name, callable $callback, bool $expects_input ): callable {
		return function ( $input = null ) use ( $name, $callback, $expects_input ) {
			/**
			 * Filters whether an ability invocation should be rate-limited.
			 *
			 * Return null to continue. Return WP_Error to fail closed and reject
			 * the invocation with that error. Runs for every wrapped permission
			 * callback before the disabled-state check.
			 *
			 * @since 1.23.0
			 *
			 * @param null|\WP_Error $rate_limit_decision Initial value or denial error.
			 * @param string         $name                Ability name.
			 * @param array          $input               Validated input, or an empty array for input-less abilities.
			 */
			$rate_limit_decision = apply_filters( 'gk/foundation/abilities/rate-limit', null, $name, $input ?? [] );
			if ( is_wp_error( $rate_limit_decision ) ) {
				return $rate_limit_decision;
			}

			if ( $this->is_disabled( $name ) ) {
				return new WP_Error(
					'ability_disabled',
					sprintf(
						/* translators: %s: ability name */
						__( 'The ability "%s" is currently disabled.', 'gk-foundation' ),
						$name
					),
					[ 'status' => 403 ]
				);
			}

			if ( $expects_input ) {
				return call_user_func( $callback, $input );
			}

			return call_user_func( $callback );
		};
	}

	/**
	 * Normalizes ability configuration for the WordPress Abilities API.
	 *
	 * @since 1.23.0
	 *
	 * @param array $config Ability configuration.
	 * @return array Normalized configuration.
	 */
	private function normalize_config( array $config ): array {
		if ( isset( $config['callback'] ) && empty( $config['execute_callback'] ) ) {
			$config['execute_callback'] = $config['callback'];
		}

		$categories = [];

		if ( ! empty( $config['categories'] ) && is_array( $config['categories'] ) ) {
			$categories = $config['categories'];
		}

		if ( ! empty( $config['category'] ) && is_string( $config['category'] ) ) {
			$primary = $config['category'];
			if ( empty( $categories ) ) {
				$categories = [ $primary ];
			} elseif ( ! in_array( $primary, $categories, true ) ) {
				array_unshift( $categories, $primary );
			} else {
				$categories = array_merge( [ $primary ], array_values( array_diff( $categories, [ $primary ] ) ) );
			}
		}

		if ( ! empty( $categories ) ) {
			$categories         = array_values( array_unique( $categories ) );
			$config['category'] = $categories[0];
		}

		$meta = $config['meta'] ?? [];
		if ( ! is_array( $meta ) ) {
			$meta = [];
		}

		if ( isset( $config['annotations'] ) && is_array( $config['annotations'] ) ) {
			$meta['annotations'] = array_merge( $meta['annotations'] ?? [], $config['annotations'] );
		}

		if ( array_key_exists( 'show_in_rest', $config ) ) {
			$meta['show_in_rest'] = (bool) $config['show_in_rest'];
		}

		if ( ! empty( $categories ) ) {
			$meta[ self::META_KEY_CATEGORIES ] = array_values( $categories );
		}

		$name = isset( $config['name'] ) && is_string( $config['name'] ) ? $config['name'] : '';

		$meta[ self::META_KEY_SOURCE ]           = 'gravitykit';
		$meta[ self::META_KEY_PRODUCT ]          = $this->derive_product_from_name( $name );
		$meta[ self::META_KEY_CONTRACT_VERSION ] = isset( $meta[ self::META_KEY_CONTRACT_VERSION ] ) && is_string( $meta[ self::META_KEY_CONTRACT_VERSION ] )
			? $meta[ self::META_KEY_CONTRACT_VERSION ]
			: self::DEFAULT_CONTRACT_VERSION;

		$scope = $this->derive_scope_from_category( $meta[ self::META_KEY_PRODUCT ], $config['category'] ?? '' );
		if ( '' !== $scope ) {
			$meta[ self::META_KEY_SCOPE ] = $scope;
		} else {
			unset( $meta[ self::META_KEY_SCOPE ] );
		}

		// Stamp the MCP tool name into meta so WP core's own catalog
		// (/wp-abilities/v1/) carries it too — MCP clients that fall back to
		// the core catalog read meta.mcp_tool_name (the server owns naming;
		// clients never derive).
		if ( '' !== $name ) {
			$meta['mcp_tool_name'] = $this->get_mcp_tool_name( $name );
		}

		$config['meta'] = $meta;

		return $config;
	}

	/**
	 * Checks if an ability is disabled.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return bool True if disabled.
	 */
	public function is_disabled( string $name ): bool {
		return in_array( $name, $this->disabled(), true );
	}

	/**
	 * Checks if an ability is enabled.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return bool True if enabled.
	 */
	public function is_enabled( string $name ): bool {
		return ! $this->is_disabled( $name );
	}

	/**
	 * Disables an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function disable( string $name ) {
		$disabled = $this->disabled();

		if ( in_array( $name, $disabled, true ) ) {
			return true;
		}

		$disabled[] = $name;
		$result     = update_option( self::DISABLED_OPTION, $disabled );

		if ( ! $result ) {
			return $this->error(
				'gk_abilities_option_update_failed',
				__( 'Failed to persist disabled abilities.', 'gk-foundation' ),
				[ 'name' => $name ]
			);
		}

		$this->logger()->info( "Disabled ability '{$name}'" );

		// Intentionally NOT calling wp_unregister_ability(): the wrapped
		// permission_callback short-circuits with `ability_disabled` (see
		// wrap_permission_callback below), and Manager::all() already filters
		// disabled entries out of catalog listings when enabled_only=true.
		// Unregistering would also force enable() to re-register, but
		// wp_register_ability() is gated by `doing_action('wp_abilities_api_init')`
		// in wp-includes/abilities-api.php, and the registry rejects already-
		// registered names (WP_Abilities_Registry::register, registry.php:92-99).

		do_action( 'gk/foundation/abilities/changed', $name, 'disabled' );

		return true;
	}

	/**
	 * Enables an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function enable( string $name ) {
		$disabled = $this->disabled();

		if ( ! in_array( $name, $disabled, true ) ) {
			return true;
		}

		$disabled = array_values( array_diff( $disabled, [ $name ] ) );
		$result   = update_option( self::DISABLED_OPTION, $disabled );

		if ( ! $result ) {
			return $this->error(
				'gk_abilities_option_update_failed',
				__( 'Failed to persist disabled abilities.', 'gk-foundation' ),
				[ 'name' => $name ]
			);
		}

		$this->logger()->info( "Enabled ability '{$name}'" );

		// Re-registration not needed: disable() never unregisters (see the
		// note there). The wrapped permission_callback's is_disabled() check
		// flips back to pass-through automatically once the option no longer
		// lists this name.

		do_action( 'gk/foundation/abilities/changed', $name, 'enabled' );

		return true;
	}

	/**
	 * Gets disabled ability names.
	 *
	 * @since 1.23.0
	 *
	 * @return string[]
	 */
	public function disabled(): array {
		$disabled = get_option( self::DISABLED_OPTION, [] );

		return is_array( $disabled ) ? array_values( array_filter( $disabled, 'is_string' ) ) : [];
	}

	/**
	 * Replaces the disabled abilities list.
	 *
	 * @since 1.23.0
	 *
	 * @param string[] $disabled Ability names.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	public function set_disabled_abilities( array $disabled ) {
		$disabled = array_values( array_unique( array_filter( $disabled, 'is_string' ) ) );
		$result   = update_option( self::DISABLED_OPTION, $disabled );

		if ( $result || get_option( self::DISABLED_OPTION, [] ) === $disabled ) {
			$this->logger()->info(
				'Updated disabled abilities list',
				[
					'count' => count( $disabled ),
				]
			);
			return true;
		}

		return $this->error(
			'gk_abilities_option_update_failed',
			__( 'Failed to persist disabled abilities.', 'gk-foundation' ),
			[ 'count' => count( $disabled ) ]
		);
	}

	/**
	 * Gets GravityKit-registered abilities.
	 *
	 * @since 1.23.0
	 *
	 * @param bool $enabled_only Whether to return only enabled abilities. Default true.
	 * @return \WP_Ability[] Array of WP_Ability objects.
	 */
	public function get_gravitykit_abilities( bool $enabled_only = true ): array {
		if ( ! function_exists( 'wp_get_abilities' ) ) {
			return [];
		}

		$all_abilities = wp_get_abilities();

		return array_filter(
			$all_abilities,
			function ( $ability ) use ( $enabled_only ): bool {
				if ( ! method_exists( $ability, 'get_meta_item' ) ) {
					return false;
				}

				if ( $ability->get_meta_item( self::META_KEY_SOURCE ) !== 'gravitykit' ) {
					return false;
				}

				if ( $enabled_only && $this->is_disabled( $ability->get_name() ) ) {
					return false;
				}

				return true;
			}
		);
	}

	/**
	 * Gets all known GravityKit ability configs.
	 *
	 * @since 1.23.0
	 *
	 * @return array[] Ability configurations keyed by name.
	 */
	public function get_catalog(): array {
		return $this->catalog;
	}

	/**
	 * Gets a specific ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return \WP_Ability|null
	 */
	public function get( string $name ): ?\WP_Ability {
		if ( ! function_exists( 'wp_get_ability' ) ) {
			return null;
		}

		return wp_get_ability( $name );
	}

	/**
	 * Checks if an ability exists.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return bool
	 */
	public function exists( string $name ): bool {
		if ( ! function_exists( 'wp_has_ability' ) ) {
			return false;
		}

		return wp_has_ability( $name );
	}

	/**
	 * Validates GravityKit ability name format.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return bool
	 */
	public function is_valid_name( string $name ): bool {
		return 1 === preg_match( '#^' . self::NAME_PATTERN . '$#', $name );
	}

	/**
	 * Derives product slug from ability name.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return string
	 */
	private function derive_product_from_name( string $name ): string {
		if ( 1 !== preg_match( '#^' . self::SLUG_PREFIX . '([a-z0-9-]+)/#', $name, $matches ) ) {
			return '';
		}

		return $matches[1];
	}

	/**
	 * Derives scope from category.
	 *
	 * @since 1.23.0
	 *
	 * @param string $product Product slug.
	 * @param mixed  $category Category slug.
	 * @return string
	 */
	private function derive_scope_from_category( string $product, $category ): string {
		if ( '' === $product || ! is_string( $category ) ) {
			return '';
		}

		$prefix = $this->scope_slug_prefix( $product );
		if ( 0 !== strpos( $category, $prefix ) ) {
			return '';
		}

		return substr( $category, strlen( $prefix ) );
	}

	/**
	 * Gets the category-slug prefix for a product's scope subcategories.
	 *
	 * @since 1.23.0
	 *
	 * @param string $product Product slug.
	 * @return string
	 */
	private function scope_slug_prefix( string $product ): string {
		return self::SLUG_PREFIX . $product . '-';
	}

	/**
	 * Gets ability product metadata.
	 *
	 * @since 1.23.0
	 *
	 * @param \WP_Ability $ability Ability object.
	 * @return string
	 */
	private function get_ability_product( \WP_Ability $ability ): string {
		$product = $ability->get_meta_item( self::META_KEY_PRODUCT );

		return is_string( $product ) && '' !== $product ? $product : $this->derive_product_from_name( $ability->get_name() );
	}

	/**
	 * Gets ability scope metadata.
	 *
	 * @since 1.23.0
	 *
	 * @param \WP_Ability $ability Ability object.
	 * @return string
	 */
	private function get_ability_scope( \WP_Ability $ability ): string {
		$scope = $ability->get_meta_item( self::META_KEY_SCOPE );

		return is_string( $scope ) ? $scope : '';
	}

	/**
	 * Resolves category labels for an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param \WP_Ability $ability Ability object.
	 * @return array[]
	 */
	private function get_label_resolved_categories( \WP_Ability $ability ): array {
		$stored = $ability->get_meta_item( self::META_KEY_CATEGORIES );

		if ( ! is_array( $stored ) || empty( $stored ) ) {
			$stored = [ $ability->get_category() ];
		}

		$categories = [];
		foreach ( array_filter( array_map( 'strval', $stored ) ) as $slug ) {
			$registered   = $this->categories[ $slug ] ?? [];
			$categories[] = [
				'slug'        => $slug,
				'label'       => (string) ( $registered['label'] ?? $slug ),
				'description' => (string) ( $registered['description'] ?? '' ),
			];
		}

		return $categories;
	}

	/**
	 * Builds the native Abilities API run URL.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return string
	 */
	private function get_rest_run_url( string $name ): string {
		$path = "wp-abilities/v1/abilities/{$name}/run";

		return function_exists( 'rest_url' ) ? rest_url( $path ) : "/wp-json/{$path}";
	}

	/**
	 * Builds the MCP tool name for an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return string
	 */
	private function get_mcp_tool_name( string $name ): string {
		$product  = $this->derive_product_from_name( $name );
		$slug     = substr( $name, strpos( $name, '/' ) + 1 );
		$products = $this->products();

		// mcp_prefix is required in a product declaration (validated with a
		// _doing_it_wrong() notice at category registration). The full
		// product slug is a collision-free runtime fallback so tool names
		// stay functional and unique even for undeclared products.
		$declared = $products[ $product ]['mcp_prefix'] ?? null;
		$prefix   = is_string( $declared ) && '' !== $declared ? $declared : $product;

		if ( '' === $prefix ) {
			$prefix = 'gk';
		}

		return str_replace( '-', '_', $prefix . '_' . $slug );
	}

	/**
	 * Creates a standard abilities error.
	 *
	 * @since 1.23.0
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param array  $context Error context.
	 * @return WP_Error
	 */
	private function error( string $code, string $message, array $context = [] ): WP_Error {
		return new WP_Error( $code, $message, $context );
	}

	/**
	 * Surface an invalid-registration failure to developers in WP_DEBUG.
	 *
	 * Most callers of register() drop the return value (50+ in-tree callers),
	 * so a silent WP_Error means a typo'd ability name or missing field
	 * just no-ops with no visible feedback. _doing_it_wrong logs a notice in
	 * WP_DEBUG and is a no-op in production.
	 *
	 * @since 1.23.0
	 *
	 * @param string $code    Error code.
	 * @param string $message Error message.
	 * @param array  $context Error context (typically includes 'name').
	 */
	private function notify_invalid_registration( string $code, string $message, array $context ): void {
		if ( ! function_exists( '_doing_it_wrong' ) ) {
			return;
		}

		$name = isset( $context['name'] ) ? (string) $context['name'] : 'unknown';

		_doing_it_wrong(
			'GravityKitFoundation::abilities()->register()',
			esc_html( sprintf( '[%s] %s (ability: %s)', $code, $message, $name ) ),
			'1.23.0'
		);
	}
}
