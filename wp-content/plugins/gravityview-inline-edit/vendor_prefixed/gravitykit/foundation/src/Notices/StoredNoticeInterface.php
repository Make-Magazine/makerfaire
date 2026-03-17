<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityEdit\Foundation\Notices;

/**
 * Contract for notices that are persisted in the database for later display.
 *
 * @since 1.3.0
 */
interface StoredNoticeInterface extends NoticeInterface {
	/**
	 * Returns the absolute expiration timestamp (UTC).
	 *
	 * @since 1.3.0
	 *
	 * @return int Expiration timestamp (0 when the notice does not expire).
	 */
	public function get_expiration();

	/**
	 * Returns the persistence scope for this notice.
	 * Currently supported values:
	 *   – "global" (default): definition is stored once site-wide.
	 *   – "user": definition is stored in user meta of one or more users.
	 *
	 * @since 1.3.0
	 */
	public function get_scope(): string;

	/**
	 * For user-scoped notices, returns the list of target user IDs. Ignored for global scope.
	 * May include strings with 'not:' prefix for exclusions (e.g., 'not:1').
	 *
	 * @since 1.3.0
	 *
	 * @return array<int|string> List of user IDs or exclusion strings.
	 */
	public function get_users(): array;

	/**
	 * Returns the live configuration for this notice, if any.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string,mixed>|null Live configuration or null if not configured.
	 */
	public function get_live_config(): ?array;

	/**
	 * Applies live updates by invoking the notice's live callback, if configured.
	 *
	 * @since 1.3.0
	 *
	 * @param NoticeRepository $repository Repository instance for persistence operations.
	 *
	 * @return void
	 */
	public function apply_live_updates( NoticeRepository $repository ): void;

	/**
	 * Returns whether the notice can be globally dismissed (removed from database).
	 *
	 * @since 1.4.0
	 *
	 * @return bool True if the notice can be globally dismissed.
	 */
	public function is_globally_dismissible(): bool;

	/**
	 * Returns the capability required for global dismissal.
	 *
	 * @since 1.4.0
	 *
	 * @return string|array The capability required for global dismissal.
	 */
	public function get_global_dismiss_capability();
}
