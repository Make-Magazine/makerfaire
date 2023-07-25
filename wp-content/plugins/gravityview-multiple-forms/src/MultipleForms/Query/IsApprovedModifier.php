<?php

namespace GravityKit\MultipleForms\Query;

use GravityKit\MultipleForms\AbstractSingleton;
use GravityKit\MultipleForms\View;

/**
 * Class IsApprovedModifier
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms\Query
 */
class IsApprovedModifier extends AbstractSingleton {
	/**
	 * Stores teh current Join data for the modifier.
	 *
	 * @var array
	 */
	protected $current_join_data = [];

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		add_filter( 'gravityview_get_entries', [ $this, 'include_joins_to_is_approved_search_criteria' ], 15, 3 );
		add_filter( 'gravityview/view/get_entries/should_apply_legacy_join_is_approved_query_conditions', '__return_false' );
	}

	/**
	 * Include the filtering for the condition of is_approved to include this view join data.
	 *
	 * This is the only place that would work for this hook, but it doesn't change anything to $parameters
	 *
	 * @since 0.3
	 *
	 * @param $parameters
	 * @param $args
	 * @param $form_id
	 *
	 * @return array
	 */
	public function include_joins_to_is_approved_search_criteria( $parameters, $args, $form_id ) {
		if ( empty( $args['id'] ) ) {
			return $parameters;
		}

		$view_post = get_post( $args['id'] );

		if ( ! $view_post instanceof \WP_Post ) {
			return $parameters;
		}

		$join_data = View::get_join_data( $view_post );

		if ( empty( $join_data ) ) {
			return $parameters;
		}

		// Set join data and add callback.
		$this->current_join_data = $join_data;
		add_filter( 'gk/multiple-forms/query/search_criteria/is_approved_condition', [ $this, 'modify_approved_condition' ], 15 );

		return $parameters;
	}

	/**
	 * Modifies the approved condition to include the joined forms.
	 *
	 * @since 0.3
	 *
	 * @param \GF_Query_Condition $approved_condition
	 *
	 * @return \GF_Query_Condition
	 */
	public function modify_approved_condition( \GF_Query_Condition $approved_condition ): \GF_Query_Condition {
		if ( empty( $this->current_join_data ) ) {
			return $approved_condition;
		}

		$conditions = [ $approved_condition ];
		foreach ( $this->current_join_data as $join ) {
			$conditions[] = new \GF_Query_Condition(
				new \GF_Query_Column( \GravityView_Entry_Approval::meta_key, $join->get_join_form_id() ),
				\GF_Query_Condition::EQ,
				new \GF_Query_Literal( \GravityView_Entry_Approval_Status::APPROVED )
			);
		}

		$condition = call_user_func_array( [ \GF_Query_Condition::class, '_or' ], $conditions );

		// Remove filter and reset join data.
		remove_filter( 'gk/multiple-forms/query/search_criteria/is_approved_condition', [ $this, 'modify_approved_condition' ], 15 );
		$this->current_join_data = [];

		return $condition;
	}
}
