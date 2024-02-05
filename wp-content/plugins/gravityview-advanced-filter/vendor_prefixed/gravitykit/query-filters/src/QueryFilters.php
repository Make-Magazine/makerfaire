<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 01-February-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\AdvancedFilter\QueryFilters;

use Exception;
use GF_Query_Condition;
use GravityKit\AdvancedFilter\QueryFilters\Condition\ConditionFactory;
use GravityKit\AdvancedFilter\QueryFilters\Filter\EntryFilterService;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Filter\FilterFactory;
use GravityKit\AdvancedFilter\QueryFilters\Filter\RandomFilterIdGenerator;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\DisableAdminVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\DisableFiltersVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\FilterVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessDateVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessFieldTypeVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\ProcessMergeTagsVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\UserIdVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Repository\DefaultRepository;
use GravityKit\AdvancedFilter\QueryFilters\Sql\SqlAdjustmentCallbacks;
use GravityView_Entry_Approval_Status;
use RuntimeException;

class QueryFilters {
	/**
	 * @since 1.0
	 * @var array Assets handle.
	 */
	public const ASSETS_HANDLE = 'gk-query-filters';

	/**
	 * @since 1.0
	 * @var Filter Filters.
	 */
	private $filters;

	/**
	 * @since 1.0
	 * @var array GF Form.
	 */
	private $form = [];

	/**
	 * @since 2.0.0
	 * @var FilterFactory
	 */
	private $filter_factory;

	/**
	 * @since 2.0.0
	 * @var ConditionFactory
	 */
	private $condition_factory;

	/**
	 * @since 2.0.0
	 * @var DefaultRepository
	 */
	private $repository;

	/**
	 * @since 2.0.0
	 * @var EntryFilterService
	 */
	private $entry_filter_service;

	/**
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->filter_factory       = new FilterFactory( new RandomFilterIdGenerator() );
		$this->condition_factory    = new ConditionFactory();
		$this->repository           = new DefaultRepository();
		$this->entry_filter_service = new EntryFilterService( $this->repository );
	}

	/**
	 * Convenience create method.
	 * @return QueryFilters
	 * @since 2.0.0
	 */
	public static function create(): QueryFilters {
		return new QueryFilters();
	}

	/**
	 * Sets form on class instance.
	 *
	 * @param array $form GF Form.
	 *
	 * @return void
	 * @throws \Exception
	 * @internal
	 *
	 * @since 1.0
	 */
	public function set_form( array $form ) {
		if ( ! isset( $form['id'], $form['fields'] ) ) {
			throw new Exception( 'Invalid form object provided.' );
		}

		$this->form = $form;
	}

	/**
	 * Creates immutable instance with form data.
	 *
	 * @param array $form The form object.
	 *
	 * @return QueryFilters
	 * @throws \Exception
	 * @since 2.0.0
	 */
	public function with_form( array $form ): QueryFilters {
		$clone = clone $this;
		$clone->set_form( $form );

		return $clone;
	}

	/**
	 * Sets filters on class instance.
	 *
	 * @param array $filters Field filters.
	 *
	 * @return void
	 * @throws \Exception
	 * @internal
	 *
	 * @since 1.0
	 */
	public function set_filters( array $filters ) {
		$this->filters = $this->filter_factory->from_array( $filters );
	}

	/**
	 * Creates immutable instance with different filters.
	 *
	 * @param array $filters Field filters.
	 *
	 * @return QueryFilters
	 * @throws \Exception
	 * @since 2.0.0
	 */
	public function with_filters( array $filters ): QueryFilters {
		$clone = clone $this;
		$clone->set_filters( $filters );

		return $clone;
	}

	/**
	 * Converts filters and returns GF Query conditions.
	 *
	 * @return GF_Query_Condition|null
	 * @throws RuntimeException
	 * @since 1.0
	 */
	public function get_query_conditions() {
		if ( empty( $this->form ) ) {
			throw new RuntimeException( 'Missing form object.' );
		}

		if ( ! $this->filters instanceof Filter ) {
			return null;
		}

		add_filter( 'gform_gf_query_sql', function ( array $query ): array {
			return SqlAdjustmentCallbacks::sql_empty_date_adjustment( $query );
		} );

		return $this->condition_factory->from_filter( $this->get_filters(), $this->form['id'] );
	}

	/**
	 * The filter visitors that finalize abstract filters.
	 * @return FilterVisitor[] The visitors.
	 * @filter `gk/query-filters/filter/visitors` The filters to be applied to the query.
	 * @since 2.0.0
	 */
	private function get_filter_visitors(): array {
		$visitors = [
			new DisableFiltersVisitor(),
			new DisableAdminVisitor( $this->repository, $this->form ),
			new ProcessMergeTagsVisitor( $this->repository, $this->form ),
			new UserIdVisitor( $this->repository, $this->form ),
			new ProcessDateVisitor( $this->repository, $this->form ),
			new ProcessFieldTypeVisitor( $this->repository, $this->form ),
		];

		$visitors = apply_filters( 'gk/query-filters/filter/visitors', $visitors, $this->form );

		return array_filter( $visitors, function ( $visitor ): bool {
			return $visitor instanceof FilterVisitor;
		} );
	}

	/**
	 * Gets field filter options from Gravity Forms and modify them
	 *
	 * @return array|void
	 * @see \GFCommon::get_field_filter_settings()
	 */
	public function get_field_filters() {
		return $this->repository->get_field_filters( $this->form['id'] );
	}

	/**
	 * Adds Entry Approval Status filter option.
	 *
	 * @param array $filters
	 *
	 * @return array
	 * @since 1.4
	 *
	 */
	private static function add_approval_status_filter( array $filters ): array {
		if ( ! class_exists( 'GravityView_Entry_Approval_Status' ) ) {
			return $filters;
		}

		$approval_choices = GravityView_Entry_Approval_Status::get_all();

		$approval_values = [];

		foreach ( $approval_choices as $choice ) {
			$approval_values[] = [
				'text'  => $choice['label'],
				'value' => $choice['value'],
			];
		}

		$filters[] = [
			'text'      => __( 'Entry Approval Status', 'gravityview-advanced-filter' ),
			'key'       => 'is_approved',
			'operators' => [ 'is', 'isnot' ],
			'values'    => $approval_values,
		];

		return $filters;
	}

	/**
	 * Creates a filter that should return zero results.
	 *
	 * @return array
	 * @since 1.0
	 *
	 */
	public static function get_zero_results_filter(): array {
		return Filter::locked()->to_array();
	}

	/**
	 * Returns translation strings used in the UI.
	 *
	 * @return array $translations Translation strings.
	 * @since 1.0
	 *
	 */
	private function get_translations(): array {
		/**
		 * @filter `gk/query-filters/translations` Modify default translation strings.
		 *
		 * @param array $translations Translation strings.
		 *
		 * @since  1.0
		 *
		 */
		$translations = apply_filters( 'gk/query-filters/translations', [
			'internet_explorer_notice' => esc_html__(
				'Internet Explorer is not supported. Please upgrade to another browser.',
				'gravityview-advanced-filter'
			),
			'fields_not_available'     => esc_html__(
				'Form fields are not available. Please try refreshing the page.',
				'gravityview-advanced-filter'
			),
			'add_condition'            => esc_html__( 'Add Condition', 'gravityview-advanced-filter' ),
			'join_and'                 => esc_html_x( 'and', 'Join using "and" operator', 'gravityview-advanced-filter' ),
			'join_or'                  => esc_html_x( 'or', 'Join using "or" operator', 'gravityview-advanced-filter' ),
			'is'                       => esc_html_x( 'is', 'Filter operator (e.g., A is TRUE)', 'gravityview-advanced-filter' ),
			'isnot'                    => esc_html_x( 'is not', 'Filter operator (e.g., A is not TRUE)', 'gravityview-advanced-filter' ),
			'>'                        => esc_html_x( 'greater than', 'Filter operator (e.g., A is greater than B)', 'gravityview-advanced-filter' ),
			'<'                        => esc_html_x( 'less than', 'Filter operator (e.g., A is less than B)', 'gravityview-advanced-filter' ),
			'contains'                 => esc_html_x( 'contains', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
			'ncontains'                => esc_html_x( 'does not contain', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
			'starts_with'              => esc_html_x( 'starts with', 'Filter operator (e.g., AB starts with A)', 'gravityview-advanced-filter' ),
			'ends_with'                => esc_html_x( 'ends with', 'Filter operator (e.g., AB ends with B)', 'gravityview-advanced-filter' ),
			'isbefore'                 => esc_html_x( 'is before', 'Filter operator (e.g., A is before date B)', 'gravityview-advanced-filter' ),
			'isafter'                  => esc_html_x( 'is after', 'Filter operator (e.g., A is after date B)', 'gravityview-advanced-filter' ),
			'ison'                     => esc_html_x( 'is on', 'Filter operator (e.g., A is on date B)', 'gravityview-advanced-filter' ),
			'isnoton'                  => esc_html_x( 'is not on', 'Filter operator (e.g., A is not on date B)', 'gravityview-advanced-filter' ),
			'isempty'                  => esc_html_x( 'is empty', 'Filter operator (e.g., A is empty)', 'gravityview-advanced-filter' ),
			'isnotempty'               => esc_html_x( 'is not empty', 'Filter operator (e.g., A is not empty)', 'gravityview-advanced-filter' ),
			'remove_field'             => esc_html__( 'Remove Field', 'gravityview-advanced-filter' ),
			'available_choices'        => esc_html__( 'Return to Field Choices', 'gravityview-advanced-filter' ),
			'available_choices_label'  => esc_html__(
				'Return to the list of choices defined by the field.',
				'gravityview-advanced-filter'
			),
			'custom_is_operator_input' => esc_html__( 'Custom Choice', 'gravityview-advanced-filter' ),
			'untitled'                 => esc_html__( 'Untitled', 'gravityview-advanced-filter' ),
			'field_not_available'      => esc_html__(
				'Form field ID #%d is no longer available. Please remove this condition.',
				'gravityview-advanced-filter'
			),
		] );

		return $translations;
	}

	/**
	 * Enqueues UI scripts.
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 * @since 1.0
	 *
	 */
	public function enqueue_scripts( array $meta = [] ) {
		$script = 'assets/js/query-filters.js';
		$handle = $meta['handle'] ?? self::ASSETS_HANDLE;
		$ver    = $meta['ver'] ?? filemtime( plugin_dir_path( __DIR__ ) . $script );
		$src    = $meta['src'] ?? plugins_url( $script, __DIR__ );
		$deps   = $meta['deps'] ?? [ 'jquery' ];

		wp_enqueue_script( $handle, $src, $deps, $ver );

		$variable_name = $meta['variable_name'] ?? sprintf( 'gkQueryFilters_%s', uniqid() );
		wp_localize_script(
			$handle,
			$variable_name,
			[
				'fields'                    => $meta['fields'] ??  $this->get_field_filters() ,
				'conditions'                => $meta['conditions'] ??  [] ,
				'targetElementSelector'     => $meta['target_element_selector'] ??  '#gk-query-filters' ,
				'autoscrollElementSelector' => $meta['autoscroll_element_selector'] ??  '' ,
				'inputElementName'          => $meta['input_element_name'] ??  'gk-query-filters' ,
				'translations'              => $meta['translations'] ??  $this->get_translations() ,
			]
		);
	}

	/**
	 * Enqueues UI styles.
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 * @since 1.0
	 *
	 */
	public static function enqueue_styles( array $meta = [] ) {
		$style  = 'assets/css/query-filters.css';
		$handle = $meta['handle'] ??  self::ASSETS_HANDLE ;
		$ver    = $meta['ver'] ??  filemtime( plugin_dir_path( __DIR__ ) . $style ) ;
		$src    = $meta['src'] ??  plugins_url( $style, __DIR__ ) ;
		$deps   = $meta['deps'] ??  [] ;

		wp_enqueue_style( $handle, $src, $deps, $ver );
	}

	/**
	 * Converts GF conditional logic rules to the object used by Query Filters.
	 *
	 * @param array $gf_conditional_logic GF conditional logic object.
	 *
	 * @return array Original or converted object.
	 * @since 1.0
	 *
	 */
	public function convert_gf_conditional_logic( array $gf_conditional_logic ) {
		if ( ! isset( $gf_conditional_logic['actionType'], $gf_conditional_logic['logicType'], $gf_conditional_logic['rules'] ) ) {
			return $gf_conditional_logic;
		}

		$conditions = [];

		foreach ( $gf_conditional_logic['rules'] as $rule ) {
			$conditions[] = [
				'_id'      => wp_generate_password( 4, false ),
				'key'      => $rule['fieldId'] ?? null,
				'operator' => $rule['operator'] ?? null,
				'value'    => $rule['value'] ?? null,
			];
		}

		$query_filters_conditional_logic = [
			'_id'        => wp_generate_password( 4, false ),
			'mode'       => Filter::MODE_AND,
			'conditions' => [],
		];

		if ( 'all' === $gf_conditional_logic['logicType'] ) {
			foreach ( $conditions as $condition ) {
				$query_filters_conditional_logic['conditions'][] = [
					'_id'        => wp_generate_password( 4, false ),
					'mode'       => Filter::MODE_OR,
					'conditions' => [
						$condition,
					],
				];
			}
		} else {
			$query_filters_conditional_logic['conditions'] = [
				[
					'_id'        => wp_generate_password( 4, false ),
					'mode'       => Filter::MODE_OR,
					'conditions' => $conditions,
				],
			];
		}

		return $query_filters_conditional_logic;
	}

	/**
	 * Whether the provided entry meets the filters.
	 *
	 * @param array $entry The entry object.
	 *
	 * @return bool
	 */
	final public function meets_filters( array $entry ): bool {
		if ( ! $this->filters instanceof Filter ) {
			return false;
		}

		return $this->entry_filter_service->meets_filter( $entry, $this->get_filters() );
	}

	/**
	 * The filter factory.
	 * @return FilterFactory
	 * @since 2.0.0
	 */
	final public function get_filter_factory(): FilterFactory {
		return $this->filter_factory;
	}

	/**
	 * Retrieves the finalized filters.
	 *
	 * @param bool $as_unprocessed Whether to return the filters unprocessed.
	 *
	 * @return Filter
	 * @since 2.0.0
	 */
	final public function get_filters( bool $as_unprocessed = false ): Filter {
		$clone = clone $this->filters;

		if ( ! $as_unprocessed ) {
			foreach ( $this->get_filter_visitors() as $visitor ) {
				$clone->accept( $visitor );
			}
		}

		return $clone;
	}
}
