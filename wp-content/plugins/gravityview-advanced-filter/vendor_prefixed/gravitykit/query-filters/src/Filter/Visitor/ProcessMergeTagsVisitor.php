<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor;

use GFCommon;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Repository\FormRepository;
use GravityView_API;

/**
 * Replaces merge tag on filter values.
 * @since 2.0.0
 */
final class ProcessMergeTagsVisitor implements FilterVisitor {
	/**
	 * The form repository.
	 * @since 2.0.0
	 * @var FormRepository
	 */
	private $form_repository;

	/**
	 * The form object.
	 * @since 2.0.0
	 * @var array
	 */
	private $form;

	/**
	 * Creates the visitor.
	 * @since 2.0.0
	 */
	public function __construct( FormRepository $form_repository, array $form = [] ) {
		$this->form_repository = $form_repository;
		$this->form            = $form;
	}

	/**
	 * @inheritDoc
	 * @since 2.0.0
	 */
	public function visit_filter( Filter $filter, string $level = '0' ) {
		if ( $filter->is_logic() ) {
			return;
		}

		$form = $this->getForm( $filter );

		$filter->set_value( self::process_merge_tags( $filter->value(), $form ) );
	}

	/**
	 * Returns the proper form object.
	 *
	 * @param Filter $filter The filter.
	 *
	 * @return array
	 * @since $ver4
	 */
	private function getForm( Filter $filter ): array {
		$form = $this->form;

		// Todo: can this be removed?

//		if ( isset( $filter['form_id'] ) ) {
//			$form = GFAPI::get_form( $filter['form_id'] );
//		}

		if ( ! $form ) {
			$form = $this->form_repository->get_form();
		}

		return $form;
	}

	/**
	 * Process merge tags in filter values
	 *
	 * @since 2.0.0
	 *
	 * @param array       $form         GF Form array
	 * @param array       $entry        GF Entry array
	 * @param string|null $filter_value Filter value text
	 *
	 * @return string|null
	 */
	public static function process_merge_tags( $filter_value, $form = [], $entry = [] ) {
		preg_match_all( "/{get:(.*?)}/ism", $filter_value ?? '', $get_merge_tags, PREG_SET_ORDER );

		$urldecode_get_merge_tag_value = function ( $value ) {
			return urldecode( $value );
		};

		foreach ( $get_merge_tags as $merge_tag ) {
			add_filter( 'gravityview/merge_tags/get/value/' . $merge_tag[1], $urldecode_get_merge_tag_value );
		}

		return class_exists( 'GravityView_API' )
			? GravityView_API::replace_variables( $filter_value, $form, $entry )
			: GFCommon::replace_variables( $filter_value, $form, $entry );
	}
}
