<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\UserRepository;

/**
 * Visitor that possibly disables filters that are suffixed with `:disabled_admin` when the user is an admin.
 * @since 2.0.0
 */
final class DisableAdminVisitor implements FilterVisitor {
	/**
	 * @var UserRepository
	 */
	private $user_repository;
	/**
	 * @var array
	 */
	private $form;

	/**
	 * Creates the step.
	 * @since 2.0.0
	 */
	public function __construct( UserRepository $user_repository, array $form = [] ) {
		$this->user_repository = $user_repository;
		$this->form            = $form;
	}

	/**
	 * @inheritDoc
	 * @return void
	 * @since 2.0.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ) {
		if ( $filter->is_logic() || $filter->value() === null ) {
			return;
		}

		$remove_admin_fields = 0;

		// Check if filter should be disabled for admins, and remove that modifier.
		$filter->set_value( str_replace( ':disabled_admin', '', $filter->value(), $remove_admin_fields ) );

		if ( $remove_admin_fields && $this->user_repository->is_user_admin( null, $this->form ) ) {
			// User is admin and this filter should be ignored.
			$filter->disable();
		}
	}
}
