<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified by gravityview on 10-October-2025 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

/**
 * Base contract for all notice types.
 *
 * @since 1.3.0
 */
interface NoticeInterface {
	/**
	 * Returns notice slug (unique identifier within its owning namespace).
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_slug(): string;

	/**
	 * Returns the namespace used to register the notice.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_namespace(): string;

	/**
	 * Returns a unique notice slug, which is a composite namespace/slug key. Used for storage, Ajax, etc.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Returns the human-readable message to display.
	 *
	 * @since 1.3.0
	 *
	 * @return string Message string.
	 */
	public function get_message(): string;

	/**
	 * Returns the severity (info|success|warning|error).
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_severity(): string;

	/**
	 * Returns display order; lower numbers appear first.
	 *
	 * @since 1.3.0
	 *
	 * @return int
	 */
	public function get_order(): int;

	/**
	 * Returns positioning guards for admin screens.
	 * – string: WP_Screen ID to match (e.g., 'plugins').
	 * – callable: custom logic returning true/false. Receives (NoticeInterface $notice, ?WP_Screen $screen).
	 * Provides an array to mix and match. Empty array means the notice is global.
	 *
	 * @since 1.3.0
	 *
	 * @return array<int, string|callable>
	 */
	public function get_screens(): array;

	/**
	 * Returns a list of capabilities that grant access to this notice.
	 * When the array is empty, the notice is visible to everyone.
	 *
	 * @since 1.3.0
	 *
	 * @return string[] List of capability slugs.
	 */
	public function get_capabilities(): array;

	/**
	 * Whether the notice can be dismissed by the user.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_dismissible(): bool;

	/**
	 * Whether the notice can be snoozed by the user.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_snoozable(): bool;

	/**
	 * Returns snooze options (label => seconds).
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,int>
	 */
	public function get_snooze_options(): array;

	/**
	 * Whether the notice is sticky (always visible, never collapsed).
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_sticky(): bool;

	/**
	 * Returns associative array of product metadata (currently name and icon).
	 *
	 * @since 1.3.0
	 *
	 * @return array{name:string,icon:string}
	 */
	public function get_product(): array;

	/**
	 * Serializes the notice into a lightweight structure optimised for UI usage, etc.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function as_payload(): array;

	/**
	 * Returns the full notice definition exactly as provided when the notice
	 * was created. This is the canonical representation persisted in the
	 * database and used for re-hydration.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function as_definition(): array;

	/**
	 * Indicates whether the notice should be displayed only once (flash).
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_flash(): bool;

	/**
	 * Returns a callable condition or null. When provided, must return bool.
	 *
	 * @since 1.3.0
	 *
	 * @return callable|string|null
	 */
	public function get_condition();

	/**
	 * Returns the Unix timestamp after which the notice becomes active. 0 = immediately active.
	 *
	 * @since 1.3.0
	 *
	 * @return int
	 */
	public function get_start_time(): int;

	/**
	 * Returns true when the notice start time is in the future.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public function is_scheduled(): bool;

	/**
	 * Returns the associative array of extra data attached to the notice.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>
	 */
	public function get_extra(): array;

	/**
	 * Returns the contexts where this notice should be displayed.
	 *
	 * @since 1.3.0
	 *
	 * @return string[] Array of contexts: 'network', 'user', 'site', or empty for all contexts.
	 */
	public function get_context(): array;
}
