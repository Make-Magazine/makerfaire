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
 * Fluent builder for ability registration.
 *
 * Provides a readable API for defining abilities:
 *
 *     $manager->ability( 'gk-gravityview/views-list' )
 *         ->label( 'List Views' )
 *         ->description( 'Enumerate Views the current user can edit.' )
 *         ->categories( [ 'gk-gravityview-views', 'gk-gravityview' ] )
 *         ->capability( 'edit_gravityviews' )
 *         ->input( [ 'type' => 'object' ] )
 *         ->output( [ 'type' => 'array' ] )
 *         ->callback( [ $this, 'list_views' ] )
 *         ->command_palette()
 *         ->register();
 *
 * @since 1.23.0
 */
final class AbilityBuilder {

	/**
	 * Manager instance receiving the registration.
	 *
	 * @since 1.23.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Accumulated ability configuration.
	 *
	 * @since 1.23.0
	 *
	 * @var array
	 */
	private array $config;

	/**
	 * Initializes a builder for the given ability name.
	 *
	 * @since 1.23.0
	 *
	 * @param string  $name    Fully-qualified ability name (namespace/action).
	 * @param Manager $manager Manager that will receive the final registration.
	 */
	public function __construct( string $name, Manager $manager ) {
		$this->manager = $manager;
		$this->config  = [
			'name'       => $name,
			'categories' => [],
			'meta'       => [
				'annotations' => [],
			],
		];
	}

	/**
	 * Sets the ability label.
	 *
	 * @since 1.23.0
	 *
	 * @param string $label Human-readable label.
	 *
	 * @return self
	 */
	public function label( string $label ): self {
		$this->config['label'] = $label;
		return $this;
	}

	/**
	 * Sets the ability description.
	 *
	 * @since 1.23.0
	 *
	 * @param string $description Human-readable description.
	 *
	 * @return self
	 */
	public function description( string $description ): self {
		$this->config['description'] = $description;
		return $this;
	}

	/**
	 * Sets the callback function.
	 *
	 * @since 1.23.0
	 *
	 * @param callable $callback Ability execution callback.
	 *
	 * @return self
	 */
	public function callback( callable $callback ): self {
		$this->config['execute_callback'] = $callback;
		return $this;
	}

	/**
	 * Sets input JSON Schema.
	 *
	 * @since 1.23.0
	 *
	 * @param array $schema JSON Schema describing the ability input.
	 *
	 * @return self
	 */
	public function input( array $schema ): self {
		$this->config['input_schema'] = $schema;
		return $this;
	}

	/**
	 * Sets output JSON Schema.
	 *
	 * @since 1.23.0
	 *
	 * @param array $schema JSON Schema describing the ability output.
	 *
	 * @return self
	 */
	public function output( array $schema ): self {
		$this->config['output_schema'] = $schema;
		return $this;
	}

	/**
	 * Adds to a category.
	 *
	 * @since 1.23.0
	 *
	 * @param string $category Category slug.
	 *
	 * @return self
	 */
	public function category( string $category ): self {
		if ( ! in_array( $category, $this->config['categories'], true ) ) {
			$this->config['categories'][] = $category;
		}
		return $this;
	}

	/**
	 * Adds to multiple categories.
	 *
	 * @since 1.23.0
	 *
	 * @param array $categories Category slugs.
	 *
	 * @return self
	 */
	public function categories( array $categories ): self {
		foreach ( $categories as $category ) {
			$this->category( $category );
		}
		return $this;
	}

	/**
	 * Sets required capability.
	 *
	 * Creates a permission_callback that checks current_user_can().
	 *
	 * @since 1.23.0
	 *
	 * @param string $capability Capability required to execute the ability.
	 *
	 * @return self
	 */
	public function capability( string $capability ): self {
		$this->config['permission_callback'] = function ( $input = null ) use ( $capability ) {
			return current_user_can( $capability );
		};
		return $this;
	}

	/**
	 * Sets custom permission callback.
	 *
	 * @since 1.23.0
	 *
	 * @param callable $callback Permission callback.
	 *
	 * @return self
	 */
	public function permission( callable $callback ): self {
		$this->config['permission_callback'] = $callback;
		return $this;
	}

	/**
	 * Enables Command Palette integration.
	 *
	 * @since 1.23.0
	 *
	 * @param bool $enabled Whether the ability should appear in the Command Palette.
	 *
	 * @return self
	 */
	public function command_palette( bool $enabled = true ): self {
		$this->config['meta']['command_palette'] = $enabled;
		return $this;
	}

	/**
	 * Sets a user-facing description for human surfaces.
	 *
	 * The ability `description()` stays the machine/AI-facing contract; this is
	 * the plain-language version shown in human surfaces (the Settings UI, and a
	 * Command Palette hint where supported). Surfaces fall back to `description`
	 * when this is not set.
	 *
	 * @since 1.24.0
	 *
	 * @param string $description Plain-language description shown to site admins.
	 *
	 * @return self
	 */
	public function user_description( string $description ): self {
		$this->config['meta']['user_description'] = $description;
		return $this;
	}

	/**
	 * Marks the ability as user-facing.
	 *
	 * Abilities are AI/MCP-facing by default; this opts an ability into the
	 * human surfaces (the Settings UI, and Command Palette eligibility). It does
	 * not affect REST/Abilities API exposure, which is controlled by
	 * `show_in_rest()`.
	 *
	 * @since 1.24.0
	 *
	 * @param bool $visible Whether the ability is shown to users.
	 *
	 * @return self
	 */
	public function user_visible( bool $visible = true ): self {
		$this->config['meta']['user_visible'] = $visible;
		return $this;
	}

	/**
	 * Marks as read-only (uses WordPress annotation).
	 *
	 * @since 1.23.0
	 *
	 * @param bool $readonly Whether the ability is read-only.
	 *
	 * @return self
	 */
	public function readonly( bool $readonly = true ): self {
		$this->config['meta']['annotations']['readonly'] = $readonly;
		return $this;
	}

	/**
	 * Marks as destructive (uses WordPress annotation).
	 *
	 * @since 1.23.0
	 *
	 * @param bool $destructive Whether the ability is destructive.
	 *
	 * @return self
	 */
	public function destructive( bool $destructive = true ): self {
		$this->config['meta']['annotations']['destructive'] = $destructive;
		return $this;
	}

	/**
	 * Marks as idempotent (uses WordPress annotation).
	 *
	 * @since 1.23.0
	 *
	 * @param bool $idempotent Whether the ability is idempotent.
	 *
	 * @return self
	 */
	public function idempotent( bool $idempotent = true ): self {
		$this->config['meta']['annotations']['idempotent'] = $idempotent;
		return $this;
	}

	/**
	 * Adds custom metadata.
	 *
	 * @since 1.23.0
	 *
	 * @param string $key   Metadata key.
	 * @param mixed  $value Metadata value.
	 *
	 * @return self
	 */
	public function meta( string $key, $value ): self {
		$this->config['meta'][ $key ] = $value;
		return $this;
	}

	/**
	 * Enables REST API exposure (uses WordPress native).
	 *
	 * @since 1.23.0
	 *
	 * @param bool $show Whether to expose the ability via REST.
	 *
	 * @return self
	 */
	public function show_in_rest( bool $show = true ): self {
		$this->config['meta']['show_in_rest'] = $show;
		return $this;
	}

	/**
	 * Registers the ability.
	 *
	 * @since 1.23.0
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function register() {
		return $this->manager->register( $this->config );
	}

	/**
	 * Gets configuration without registering.
	 *
	 * Useful for testing or inspection.
	 *
	 * @since 1.23.0
	 *
	 * @return array
	 */
	public function to_array(): array {
		return $this->config;
	}
}
