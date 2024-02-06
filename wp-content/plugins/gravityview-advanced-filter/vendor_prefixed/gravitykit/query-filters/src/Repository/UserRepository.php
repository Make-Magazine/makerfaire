<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Repository;

use WP_User;

/**
 * User repository.
 * @since 2.0.0
 */
interface UserRepository {
	/**
	 * Returns all user ID's that has ANY of the provided roles.
	 *
	 * @param array $roles The roles
	 *
	 * @return int[] The user ID's.
	 */
	public function get_user_ids_by_any_role( array $roles ): array;

	/**
	 * Returns the current user.
	 * @return WP_User
	 * @since 2.0.0
	 */
	public function get_current_user(): WP_User;

	/**
	 * Whether the provided user is considered an admin.
	 *
	 * @param WP_User|null $user The user to test. Current user if none is provided.
	 * @param array $form The form object.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	public function is_user_admin( ?WP_User $user = null, array $form = [] ): bool;
}
