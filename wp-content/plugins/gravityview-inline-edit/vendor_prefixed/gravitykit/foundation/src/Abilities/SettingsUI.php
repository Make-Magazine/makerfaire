<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare( strict_types=1 );

namespace GravityKit\GravityEdit\Foundation\Abilities;

/**
 * Settings page integration for managing abilities.
 *
 * @since 1.23.0
 */
final class SettingsUI {

	/**
	 * Ability registration manager.
	 *
	 * @since 1.23.0
	 *
	 * @var Manager
	 */
	private Manager $manager;

	/**
	 * Initializes the settings UI sub-component.
	 *
	 * @since 1.23.0
	 *
	 * @param Manager $manager Ability registration manager.
	 */
	public function __construct( Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Registers settings filters and AJAX handlers.
	 *
	 * @since 1.23.0
	 */
	public function init(): void {
		add_filter( 'gk/foundation/settings/data/plugins', [ $this, 'add_settings_section' ] );
		add_filter( 'gk/foundation/settings/' . Framework::ID . '/save/before', [ $this, 'sync_disabled_abilities' ], 10, 2 );
	}

	/**
	 * Adds abilities section to Foundation settings.
	 *
	 * @since 1.23.0
	 *
	 * @param array $plugins_data Existing settings data.
	 * @return array Modified settings data.
	 */
	public function add_settings_section( array $plugins_data ): array {
		// Only abilities explicitly marked user_visible are surfaced to admins;
		// abilities default to AI/MCP-only. An ability with no user_visible meta
		// stays registered and REST-accessible but does not render a row here.
		$abilities = array_values( array_filter( $this->manager->get_catalog(), [ $this, 'is_user_visible' ] ) );

		if ( empty( $abilities ) ) {
			return $plugins_data;
		}

		$plugins_data['abilities'] = [
			'id'       => Framework::ID,
			'title'    => esc_html__( 'Abilities', 'gk-foundation' ),
			'icon'     => 'dashicons-superhero-alt',
			'sections' => $this->build_sections( $abilities ),
		];

		return $plugins_data;
	}

	/**
	 * Builds settings sections grouped by product category.
	 *
	 * @since 1.23.0
	 *
	 * @param array[] $abilities Array of ability configs.
	 * @return array Settings sections.
	 */
	private function build_sections( array $abilities ): array {
		$grouped = [];

		foreach ( $abilities as $config ) {
			$categories = $this->get_categories_from_config( $config );
			$group      = $categories[0] ?? 'other';

			if ( ! isset( $grouped[ $group ] ) ) {
				$grouped[ $group ] = [];
			}

			$grouped[ $group ][] = $config;
		}

		$sections = [];

		foreach ( $grouped as $group => $group_abilities ) {
			$sections[] = [
				'id'       => "abilities_{$group}",
				'title'    => $this->get_group_label( $group ),
				'settings' => $this->build_fields( $group_abilities ),
			];
		}

		return $sections;
	}

	/**
	 * Builds field definitions for abilities.
	 *
	 * @since 1.23.0
	 *
	 * @param array[] $abilities Array of ability configs.
	 * @return array Field definitions.
	 */
	private function build_fields( array $abilities ): array {
		$fields = [];

		foreach ( $abilities as $config ) {
			$name        = $config['name'] ?? '';
			$label       = $config['label'] ?? $name;
			$meta        = is_array( $config['meta'] ?? null ) ? $config['meta'] : [];
			$annotations = $meta['annotations'] ?? [];

			// The settings UI is user-facing, so prefer a plain-language
			// user_description and fall back to the ability description, which is
			// authored for AI/MCP clients and is usually too technical for admins.
			$user_description = $meta['user_description'] ?? '';
			$description      = is_string( $user_description ) && '' !== $user_description
				? $user_description
				: ( $config['description'] ?? '' );

			$badge = '';
			if ( ! empty( $annotations['destructive'] ) ) {
				$badge = ' ⚠️';
			} elseif ( ! empty( $annotations['readonly'] ) ) {
				$badge = ' 📖';
			}

			$fields[] = [
				'id'          => self::setting_id( $name ),
				'type'        => 'checkbox',
				'title'       => $label . $badge,
				'description' => $description,
				'value'       => $this->manager->is_enabled( $name ) ? 1 : 0,
				'data'        => [
					'ability_name'    => $name,
					'command_palette' => (bool) ( $meta['command_palette'] ?? false ),
				],
			];
		}

		return $fields;
	}

	/**
	 * Builds the settings-field ID for an ability name.
	 *
	 * Ability names allow only `[a-z0-9-]` plus the `/` separator
	 * (Manager::NAME_PATTERN), so replacing `/` with `_` cannot collide.
	 *
	 * @since 1.23.0
	 *
	 * @param string $name Ability name.
	 * @return string
	 */
	private static function setting_id( string $name ): string {
		return 'ability_' . str_replace( '/', '_', $name );
	}

	/**
	 * Resolves a human-readable label for an ability group slug.
	 *
	 * Group slugs are ability category slugs, and products register their
	 * categories with labels (via the `gk/foundation/abilities/categories`
	 * filter) before any ability references them — so the registered
	 * category label is the authoritative display name.
	 *
	 * @since 1.23.0
	 *
	 * @param string $group Group slug.
	 *
	 * @return string
	 */
	private function get_group_label( string $group ): string {
		if ( 'other' === $group ) {
			return __( 'Other', 'gk-foundation' );
		}

		$registered = $this->manager->categories();
		$label      = $registered[ $group ]['label'] ?? '';

		$has_label = is_string( $label ) && '' !== $label;

		if ( $has_label ) {
			return $label;
		}

		return ucfirst( $group );
	}

	/**
	 * Syncs saved settings to the disabled abilities list.
	 *
	 * Converges on Manager::disable()'s option-only model: the WP-core
	 * registration is left intact, and Manager's wrapped permission_callback
	 * 403s any invocation of a disabled ability. Both code paths
	 * (Manager::disable and this filter) produce identical observable state.
	 *
	 * Detection of "unchecked" relies on the raw submitted payload, not the
	 * merged settings array, because Settings\Framework merges prior values
	 * before applying this filter — so a UI that omits an unchecked checkbox
	 * key would otherwise carry the previous enabled value forward.
	 *
	 * The AJAX save payload is shaped `[ 'plugin' => ..., 'settings' => [ setting_id => value ] ]`
	 * (see Settings\Framework::save_ui_settings()). It is only trusted when it
	 * targets this section and carries a settings array; otherwise — e.g. a
	 * programmatic save_plugin_settings() call outside the AJAX flow, where
	 * the payload is an empty array — the merged settings are used.
	 *
	 * @since 1.23.0
	 *
	 * @param array      $settings        Settings values keyed by setting ID (post-merge).
	 * @param array|null $request_payload Raw AJAX request payload, if any.
	 * @return array
	 */
	public function sync_disabled_abilities( array $settings, ?array $request_payload = null ): array {
		// The form only renders user_visible abilities, so only those are
		// reconciled here. Abilities hidden from the UI keep whatever disabled
		// state they had and are never disabled just because the form carries no
		// checkbox for them.
		$visible = array_filter( $this->manager->get_catalog(), [ $this, 'is_user_visible' ] );

		if ( empty( $visible ) ) {
			return $settings;
		}

		$is_abilities_payload = is_array( $request_payload )
			&& Framework::ID === ( $request_payload['plugin'] ?? null )
			&& is_array( $request_payload['settings'] ?? null );

		$raw_settings = $is_abilities_payload ? $request_payload['settings'] : [];

		$visible_names    = [];
		$disabled_visible = [];

		foreach ( $visible as $config ) {
			$name            = $config['name'] ?? '';
			$visible_names[] = $name;
			$setting_id      = self::setting_id( $name );

			// Prefer the raw payload so an omitted checkbox is treated as
			// unchecked; an empty settings array is a real submission with
			// everything unchecked. Fall back to the merged settings when the
			// payload doesn't target this section.
			if ( $is_abilities_payload ) {
				$value = $raw_settings[ $setting_id ] ?? null;
			} else {
				$value = $settings[ $setting_id ] ?? null;
			}

			if ( ! $this->is_enabled_value( $value ) ) {
				$disabled_visible[] = $name;
			}
		}

		// Preserve the disabled state of abilities not shown in the UI; the form
		// only governs the visible set.
		$preserved = array_diff( $this->manager->disabled(), $visible_names );

		$this->manager->set_disabled_abilities( array_values( array_merge( $preserved, $disabled_visible ) ) );

		return $settings;
	}

	/**
	 * Gets ordered categories for an ability.
	 *
	 * @since 1.23.0
	 *
	 * @param array $config Configuration array (expects meta categories under Manager::META_KEY_CATEGORIES).
	 * @return string[]
	 */
	private function get_categories_from_config( array $config ): array {
		$meta   = is_array( $config['meta'] ?? null ) ? $config['meta'] : [];
		$stored = $meta[ Manager::META_KEY_CATEGORIES ] ?? null;

		if ( is_array( $stored ) && ! empty( $stored ) ) {
			return array_values( $stored );
		}

		$category = $config['category'] ?? '';

		return $category ? [ $category ] : [];
	}

	/**
	 * Normalizes enabled values from settings payloads.
	 *
	 * @since 1.23.0
	 *
	 * @param mixed $value Setting value.
	 * @return bool
	 */
	private function is_enabled_value( $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			return 1 === (int) $value;
		}

		if ( is_string( $value ) ) {
			return in_array( strtolower( $value ), [ '1', 'true', 'yes', 'on' ], true );
		}

		return false;
	}

	/**
	 * Determines whether an ability config opts into the user-facing UI.
	 *
	 * Abilities are AI/MCP-facing by default; only those whose meta sets
	 * `user_visible` to a truthy value render in the settings tab and are
	 * eligible for other human surfaces. REST/Abilities API exposure is
	 * independent (see `show_in_rest`).
	 *
	 * @since 1.24.0
	 *
	 * @param array $config Ability config from the catalog.
	 *
	 * @return bool
	 */
	private function is_user_visible( array $config ): bool {
		$meta = is_array( $config['meta'] ?? null ) ? $config['meta'] : [];

		return ! empty( $meta['user_visible'] );
	}
}
