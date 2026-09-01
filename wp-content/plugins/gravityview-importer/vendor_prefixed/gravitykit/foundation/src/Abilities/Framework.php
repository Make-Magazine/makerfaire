<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityImport\Foundation\Abilities;

/**
 * Abilities component entry point.
 *
 * Initializes the WordPress Abilities API integration for GravityKit.
 * Foundation is also the cross-product discovery contract: registered
 * abilities are stamped with `gk_registered_by`, `gk_product`, `gk_scope`, and
 * `gk_contract_version` metadata, and product/MCP catalog filters should use
 * those keys instead of parsing ability names or product-specific classes.
 *
 * @since 1.23.0
 */
final class Framework {

	public const ID = 'gk_abilities';

	/**
	 * Singleton instance.
	 *
	 * @since 1.23.0
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Ability registration manager.
	 *
	 * @since 1.23.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Settings UI sub-component.
	 *
	 * @since 1.23.0
	 *
	 * @var SettingsUI
	 */
	private SettingsUI $settings;

	/**
	 * REST discovery sub-component.
	 *
	 * @since 1.23.0
	 *
	 * @var REST
	 */
	private REST $rest;

	/**
	 * Gets the singleton instance.
	 *
	 * @since 1.23.0
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initializes sub-components.
	 *
	 * @since 1.23.0
	 */
	private function __construct() {
		$this->manager  = new Manager();
		$this->settings = new SettingsUI( $this->manager );
		$this->rest     = new REST( $this->manager );
	}

	/**
	 * Initializes the Abilities component.
	 *
	 * Called from Core.php during initialization (on plugins_loaded).
	 *
	 * @since 1.23.0
	 */
	public function init(): void {
		if ( ! $this->is_supported() ) {
			return;
		}

		// Register GravityKit categories first.
		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_categories' ] );

		// Register abilities after categories exist.
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );

		// Initialize sub-components. No Command Palette bridge is enqueued:
		// WP 7.0's @wordpress/core-abilities consumes ability metadata
		// directly from /wp-abilities/v1/.

		/**
		 * Filters whether the Abilities settings tab (Settings → GravityKit →
		 * Abilities) is rendered.
		 *
		 * Abilities are still registered with WordPress and served over REST
		 * regardless of this filter; it only controls the management UI.
		 * Defaults to false so the tab stays hidden until the UI is ready for
		 * end users. Return true to surface the tab and its per-ability
		 * enable/disable toggles.
		 *
		 * @since 1.24.0
		 *
		 * @param bool $render Whether to render the Abilities settings tab. Default false.
		 */
		if ( apply_filters( 'gk/foundation/abilities/render-ui', false ) ) {
			$this->settings->init();
		}

		$this->rest->init();

		// Trigger the WordPress Abilities API registry to fire the init hooks.
		// The registry requires WordPress 'init' to have fired first.
		add_action( 'init', [ $this, 'trigger_abilities_registry' ], 99 );
	}

	/**
	 * Triggers the WordPress Abilities API registry.
	 *
	 * This fires wp_abilities_api_categories_init and wp_abilities_api_init hooks,
	 * which causes our registered callbacks to execute and register abilities.
	 *
	 * @since 1.23.0
	 *
	 * @hooked init (priority 99)
	 */
	public function trigger_abilities_registry(): void {
		// Calling wp_get_abilities() triggers WP_Abilities_Registry::get_instance(),
		// which fires the wp_abilities_api_init action hook.
		if ( function_exists( 'wp_get_abilities' ) ) {
			wp_get_abilities();
		}
	}

	/**
	 * Registers the ability categories declared by products.
	 *
	 * Foundation contributes no categories of its own — each product
	 * declares its product category and scope subcategories via the
	 * `gk/foundation/abilities/products` filter (see Manager::products()).
	 *
	 * @since 1.23.0
	 *
	 * @hooked wp_abilities_api_categories_init
	 */
	public function register_categories(): void {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		foreach ( $this->manager->products() as $product_slug => $product ) {
			if ( ! is_array( $product ) ) {
				_doing_it_wrong(
					'gk/foundation/abilities/products',
					esc_html( sprintf( 'Product declarations must be arrays; "%s" is not.', (string) $product_slug ) ),
					'1.23.0'
				);

				continue;
			}

			$prefix           = $product['mcp_prefix'] ?? null;
			$has_valid_prefix = is_string( $prefix ) && '' !== $prefix;

			if ( ! $has_valid_prefix ) {
				_doing_it_wrong(
					'gk/foundation/abilities/products',
					esc_html( sprintf( 'The "%s" declaration must include a non-empty mcp_prefix string.', (string) $product_slug ) ),
					'1.23.0'
				);
			}

			// `categories` is optional in shape; products with abilities will
			// always declare their product category and scope subcategories.
			$categories = $product['categories'] ?? [];

			if ( ! is_array( $categories ) ) {
				_doing_it_wrong(
					'gk/foundation/abilities/products',
					esc_html( sprintf( 'The "categories" key of "%s" must be an array keyed by category slug.', (string) $product_slug ) ),
					'1.23.0'
				);

				continue;
			}

			foreach ( $categories as $slug => $args ) {
				$is_valid_definition = is_string( $slug ) && '' !== $slug && is_array( $args );

				if ( ! $is_valid_definition ) {
					_doing_it_wrong(
						'gk/foundation/abilities/products',
						esc_html( sprintf( 'Category definitions for "%s" must be arrays keyed by a non-empty slug.', (string) $product_slug ) ),
						'1.23.0'
					);

					continue;
				}

				$this->manager->register_category( $slug, $args );
			}
		}
	}

	/**
	 * Registers abilities from GravityKit plugins.
	 *
	 * @since 1.23.0
	 *
	 * @hooked wp_abilities_api_init
	 */
	public function register_abilities(): void {
		/**
		 * Fires before the `gk/foundation/abilities/register` filter runs so
		 * products can register via the Foundation facade.
		 *
		 * @since 1.23.0
		 *
		 * @param Manager $manager Manager instance.
		 */
		do_action( 'gk/foundation/abilities/register/before', $this->manager );

		/**
		 * Filters the abilities to register with WordPress.
		 *
		 * @since 1.23.0
		 *
		 * @param array[] $abilities {
		 *     Array of ability configurations. Each configuration is an array:
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
		 * @param Manager $manager Manager instance for fluent registration.
		 */
		$abilities = apply_filters( 'gk/foundation/abilities/register', [], $this->manager );

		if ( ! is_array( $abilities ) ) {
			_doing_it_wrong(
				'gk/foundation/abilities/register',
				esc_html__( 'The abilities register filter must return an array of ability configs.', 'gk-foundation' ),
				'1.23.0'
			);

			$abilities = [];
		}

		foreach ( $abilities as $index => $config ) {
			if ( ! is_array( $config ) ) {
				_doing_it_wrong(
					'gk/foundation/abilities/register',
					esc_html( sprintf( 'Ability config at index %s must be an array.', (string) $index ) ),
					'1.23.0'
				);

				continue;
			}

			$this->manager->register( $config );
		}

		/**
		 * Fires after all GravityKit abilities are registered.
		 *
		 * @since 1.23.0
		 *
		 * @param Manager $manager Manager instance.
		 */
		do_action( 'gk/foundation/abilities/registered', $this->manager );
	}

	/**
	 * Whether the WordPress Abilities API is available in this environment.
	 *
	 * @since 1.23.0
	 *
	 * @return bool
	 */
	public function is_supported(): bool {
		return function_exists( 'wp_register_ability' ) && function_exists( 'wp_register_ability_category' );
	}

	/**
	 * Registers an ability.
	 *
	 * @since 1.23.0
	 *
	 * @see Manager::register() For the configuration shape.
	 *
	 * @param array $config Ability configuration.
	 * @return true|\WP_Error
	 */
	public function register( array $config ) {
		return $this->manager->register( $config );
	}

	/**
	 * Creates an ability builder.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return AbilityBuilder
	 */
	public function builder( string $name ): AbilityBuilder {
		return $this->manager->builder( $name );
	}

	/**
	 * Registers an ability category.
	 *
	 * @since 1.23.0
	 *
	 * @param string $slug Category slug.
	 * @param array  $args Category args.
	 * @return true|\WP_Error
	 */
	public function register_category( string $slug, array $args ) {
		return $this->manager->register_category( $slug, $args );
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
		return $this->manager->categories( $filters );
	}

	/**
	 * Gets GravityKit abilities.
	 *
	 * @since 1.23.0
	 *
	 * @param array $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function all( array $filters = [] ): array {
		return $this->manager->all( $filters );
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
		return $this->manager->get( $name );
	}

	/**
	 * Gets abilities for one product.
	 *
	 * @since 1.23.0
	 *
	 * @param string $product Product slug.
	 * @param array  $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function by_product( string $product, array $filters = [] ): array {
		return $this->manager->by_product( $product, $filters );
	}

	/**
	 * Gets abilities for one scope.
	 *
	 * @since 1.23.0
	 *
	 * @param string $scope Scope slug.
	 * @param array  $filters Optional filters.
	 * @return \WP_Ability[]
	 */
	public function by_scope( string $scope, array $filters = [] ): array {
		return $this->manager->by_scope( $scope, $filters );
	}

	/**
	 * Checks if an ability is enabled.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return bool
	 */
	public function is_enabled( string $name ): bool {
		return $this->manager->is_enabled( $name );
	}

	/**
	 * Enables an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return true|\WP_Error
	 */
	public function enable( string $name ) {
		return $this->manager->enable( $name );
	}

	/**
	 * Disables an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return true|\WP_Error
	 */
	public function disable( string $name ) {
		return $this->manager->disable( $name );
	}

	/**
	 * Gets disabled ability names.
	 *
	 * @since 1.23.0
	 *
	 * @return string[]
	 */
	public function disabled(): array {
		return $this->manager->disabled();
	}

	/**
	 * Replaces disabled ability names.
	 *
	 * @since 1.23.0
	 *
	 * @param array $names Ability names.
	 * @return true|\WP_Error
	 */
	public function set_disabled( array $names ) {
		return $this->manager->set_disabled_abilities( $names );
	}

	/**
	 * Converts an ability to a REST catalog item.
	 *
	 * @since 1.23.0
	 *
	 * @param \WP_Ability $ability Ability object.
	 * @param string      $context Output context.
	 * @return array
	 */
	public function to_rest_item( \WP_Ability $ability, string $context = 'catalog' ): array {
		return $this->manager->to_rest_item( $ability, $context );
	}

	/**
	 * Gets the ability Manager instance.
	 *
	 * @since 1.23.0
	 *
	 * @internal
	 *
	 * @return Manager
	 */
	public function get_manager(): Manager {
		return $this->manager;
	}
}
