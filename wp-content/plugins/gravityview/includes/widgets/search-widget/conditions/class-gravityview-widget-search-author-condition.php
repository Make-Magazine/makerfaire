<?php

use GravityKit\GravityView\QueryFilters\Condition\Created_By_Condition;
use GV\View;

/**
 * A GF_Query condition that allows user data searches.
 *
 * @since      2.2.2
 *
 * @deprecated 2.55.0 Use Query Filters instead.
 */
class GravityView_Widget_Search_Author_GF_Query_Condition extends Created_By_Condition {
	/**
	 * The View object.
	 *
	 * @since 2.2.2
	 *
	 * @var null|View
	 */
	private $view;

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.2.2
	 */
	public function __construct( $filter, $view ) {
		$this->view = $view;
		$form_id    = $this->view->form ? $this->view->form->ID ?? null : null;

		parent::__construct( $filter['value'], $form_id );
	}

	/**
	 * Serializes the object.
	 *
	 * @since 2.42
	 *
	 * @return array The serialized data.
	 */
	public function __serialize(): array {
		return [
			'view_id' => $this->view ? $this->view->ID : 0,
			'value'   => $this->value,
			'form_id' => $this->form_id,
		];
	}

	/**
	 * Deserializes the object.
	 *
	 * @since 2.42
	 */
	public function __unserialize( array $data ): void {
		parent::__construct( $data['value'] ?? '', $data['form_id'] ?? 0 );

		$view       = View::by_id( $data['view_id'] ?? 0 );
		$this->view = $view instanceof View ? $view : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	protected function user_meta_fields(): array {
		$user_meta_fields = parent::user_meta_fields();

		/** Filter the user meta fields to search.
		 *
		 * @param array    $user_meta_fields The user meta fields.
		 * @param \GV\View $view             The View.
		 */
		return apply_filters(
			'gravityview/widgets/search/created_by/user_meta_fields',
			$user_meta_fields,
			$this->view
		);
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 2.55.0
	 */
	protected function user_fields(): array {
		$user_fields = parent::user_fields();

		/**
		 * Filter the user fields to search.
		 *
		 * @param array    $user_fields The user fields.
		 * @param \GV\View $view        The View.
		 */
		return apply_filters( 'gravityview/widgets/search/created_by/user_fields', $user_fields, $this->view );
	}
}
