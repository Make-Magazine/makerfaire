<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 11-September-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\UserRepository;
use WP_User;

/**
 * Visitor that tests if the current user has the required role(s).
 * @since 2.1.0
 */
final class CurrentUserVisitor implements FilterVisitor {
	/**
	 * @since 2.1.0
	 * @var UserRepository
	 */
	private $user_repository;

	/**
	 * Creates the step.
	 * @since 2.1.0
	 */
	public function __construct( UserRepository $user_repository ) {
		$this->user_repository = $user_repository;
	}

	/**
	 * @inheritDoc
	 * @since 2.1.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ): void {
		if ( $filter->is_logic() || 'current_user_role' !== $filter->key() ) {
			return;
		}

		$current_user = $this->user_repository->get_current_user();
		if ( ! $current_user->exists() ) {
			$filter->lock();

			return;
		}

		$this->has_required_roles( $current_user, $filter )
			? $filter->disable()
			: $filter->lock();
	}

	/**
	 * Normalize the filter's role(s) to an array of roles.
	 * @since 2.1.0
	 *
	 * @param Filter $filter The filter.
	 *
	 * @return string[] The roles for this filter.
	 */
	private function get_filter_roles( Filter $filter ): array {
		$value = $filter->value();
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$value = [ trim( $value ) ];
		}

		if ( is_array( $value ) ) {
			return $value;
		}

		return [];
	}

	/**
	 * Returns whether the provided user has the required roles according to the filter.
	 * @since 2.1.0
	 *
	 * @param WP_User $user   The user.
	 * @param Filter  $filter The filter.
	 *
	 * @return bool Whether the user has the required roles.
	 */
	private function has_required_roles( WP_User $user, Filter $filter ): bool {
		$filter_roles = $this->get_filter_roles( $filter );
		if ( [] === $filter_roles ) {
			return true;
		}

		$result = array_intersect( $user->roles, $filter_roles );

		if ( 'has_any' === $filter->operator() ) {
			return count( $result ) > 0;
		}

		// All filters need to match.
		return count( $result ) === count( $filter_roles );
	}
}
