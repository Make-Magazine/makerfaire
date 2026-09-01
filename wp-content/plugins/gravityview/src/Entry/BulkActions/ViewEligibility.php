<?php
/**
 * View eligibility checks for frontend bulk actions.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GravityKit\GravityView\Template\TemplateContext;
use GravityKit\GravityView\Template\View\Table as TableTemplate;
use GravityKit\GravityView\View\View;

/**
 * Determines whether bulk actions can run in a View context.
 *
 * @since 3.0.0
 */
final class ViewEligibility {
	/**
	 * @since 3.0.0
	 * @var Registry
	 */
	private $registry;

	/**
	 * @since 3.0.0
	 *
	 * @param Registry|null $registry Action registry.
	 */
	public function __construct( ?Registry $registry = null ) {
		$this->registry = $registry ? $registry : new Registry();
	}

	/**
	 * Whether the current context is the frontend table View template.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return bool
	 */
	public function is_table_context( $context ) {
		return $context instanceof TemplateContext && $context->template instanceof TableTemplate && $context->view instanceof View;
	}

	/**
	 * Whether bulk actions are enabled for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public function is_enabled_for_view( View $view ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! in_array( $view->settings->get( 'template' ), Config::get_supported_template_ids( $view ), true ) ) {
			return false;
		}

		if ( ! $this->registry->has_widget( $view ) ) {
			return false;
		}

		$selected_action_keys = $this->registry->get_selected_action_keys( $view );

		if ( [] === $selected_action_keys ) {
			return false;
		}

		if ( $this->is_multi_form_view( $view ) && ! $this->has_selected_multi_form_action( $view, $selected_action_keys ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Whether the View should be treated as multi-form for bulk action gating.
	 *
	 * The filter keeps the existing escape hatch for disabling the multi-form block.
	 *
	 * @since 3.0.0
	 *
	 * @param View $view View.
	 *
	 * @return bool
	 */
	public function is_multi_form_view( View $view ) {
		$is_multi_form = ! empty( $view->joins ) || ! empty( $view->unions );

		/**
		 * Filters whether frontend bulk actions are disabled for multi-form Views.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $disabled Whether bulk actions are disabled. Default true for Views with joins or unions.
		 * @param int  $view_id  View ID.
		 * @param View $view     View.
		 */
		return (bool) apply_filters( 'gk/gravityview/bulk-actions/disable-multi-form-views', $is_multi_form, (int) $view->ID, $view );
	}

	/**
	 * Whether any selected action supports multi-form Views.
	 *
	 * @since 3.0.0-beta.3
	 *
	 * @param View     $view                 View.
	 * @param string[] $selected_action_keys Selected action keys.
	 *
	 * @return bool
	 */
	private function has_selected_multi_form_action( View $view, array $selected_action_keys ) {
		$actions = Registry::get_actions( $view );

		foreach ( $selected_action_keys as $action_key ) {
			if ( empty( $actions[ $action_key ] ) ) {
				continue;
			}

			if ( Registry::action_supports_multi_form_view( $actions[ $action_key ] ) ) {
				return true;
			}
		}

		return false;
	}
}
