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
 * Set the correct User IDs for the filter.
 * @since 2.0.0
 */
final class UserIdVisitor implements FilterVisitor
{
    /**
     * The user repository.
     * @since 2.0.0
     * @var UserRepository
     */
    private $user_repository;

    /**
     * The form object.
     * @since 2.0.0
     * @var array
     */
    private $form;

    /**
     * Creates the visitor.
     *
     * @since 2.0.0
     * @param UserRepository $user_repository The user repository.
     * @param array $form The form object.
     */
    public function __construct(UserRepository $user_repository, array $form = [])
    {
        $this->user_repository = $user_repository;
        $this->form = $form;
    }

    /**
     * @inheritDoc
     * @since 2.0.0
     */
    public function visit_filter(Filter $filter, string $level = '0')
    {
        if (
            $filter->is_logic()
            || !in_array($filter->key(), ['created_by', 'created_by_user_role'], true)
        ) {
            return;
        }

        if ($filter->key() === 'created_by') {
            $this->handleById($filter);

            return;
        }

        $this->handleByRole($filter);
    }

    /**
     * Handles the filter for `created_by` filter.
     *
     * @since 2.0.0
     * @param Filter $filter The filter.
     *
     * @return void
     */
    private function handleById(Filter $filter)
    {
        if ($filter->value() === 'created_by_or_admin') {
            if ($this->user_repository->is_user_admin(null, $this->form)) {
                // User has admin caps, so we can ignore this filter on the query.
                $filter->disable();

                return;
            }

            // No admin; we'll handle this as a regular user.
            $filter->set_value('created_by');
        }

        if ($filter->value() === 'created_by') {
            if (!$this->isLoggedIn()) {
                $filter->lock();

                return;
            }

            $filter->set_value($this->user_repository->get_current_user()->ID);
        }

        if (empty($filter->value())) {
            $filter->lock();
        }
    }

    /**
     * Handles the filter for `created_by_user_role` filter.
     *
     * @since 2.0.0
     * @param Filter $filter The filter.
     *
     * @return void
     */
    private function handleByRole(Filter $filter)
    {
        $filter->set_key('created_by');

        $roles = [$filter->value()];
        if ($filter->value() === 'current_user') {
            // Match entries that are created by a user that has ANY of the current users roles.
            $roles = $this->user_repository->get_current_user()->roles;
        }

        $user_ids = $this->user_repository->get_user_ids_by_any_role($roles);

        if (!$user_ids) {
            $filter->operator() === 'is'
                ? $filter->lock() // User is not IN empty set, so we lock the filter.
                : $filter->disable(); // User cannot be in an empty array, so the filter can be disabled.

            return;
        }

        if (count($user_ids) === 1) {
            $filter->set_value(reset($user_ids));

            return;
        }

        // Multiple user ID's
        $filter->set_value($user_ids);
        $filter->set_operator(
            $filter->operator() === 'is' ? 'in' : 'not in'
        );
    }

    /**
     * Whether the current user is logged in.
     * @since 2.0.0
     * @return bool
     */
    private function isLoggedIn() : bool
    {
        return $this->user_repository->get_current_user()->exists();
    }
}
