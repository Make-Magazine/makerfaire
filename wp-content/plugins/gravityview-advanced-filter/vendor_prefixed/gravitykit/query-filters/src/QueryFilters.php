<?php
/**
 * @license MIT
 *
 * Modified by gravitykit on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\AdvancedFilter\QueryFilters;

use DateTimeInterface;
use Exception;
use GF_Query_Condition;
use GravityKit\AdvancedFilter\QueryFilters\Clock\Clock;
use GravityKit\AdvancedFilter\QueryFilters\Clock\SystemClock;
use GravityKit\AdvancedFilter\QueryFilters\Condition\ConditionFactory;
use GravityKit\AdvancedFilter\QueryFilters\Filter\EntryFilterService;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Filter;
use GravityKit\AdvancedFilter\QueryFilters\Filter\FilterFactory;
use GravityKit\AdvancedFilter\QueryFilters\Filter\FilterIdGenerator;
use GravityKit\AdvancedFilter\QueryFilters\Filter\RandomFilterIdGenerator;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\CurrentUserVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\DisableAdminVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\DisableFiltersVisitor;
use GravityKit\AdvancedFilter\QueryFilters\Filter\Visitor\EntryAwareFilterVisitor;
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
	 * An optional date range filter.
	 *
	 * @since 2.9.0
	 *
	 * @var Filter|null
	 */
	private $date_range_filter;

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
	 *
	 * @since 2.0.0
	 *
	 * @return QueryFilters
	 */
	public static function create(): QueryFilters {
		return new QueryFilters();
	}

	/**
	 * Sets form on class instance.
	 *
	 * @since 1.0
	 *
	 * @param array $form GF Form.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @internal
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
	 * @since 2.0.0
	 *
	 * @param array $form The form object.
	 *
	 * @return QueryFilters
	 *
	 * @throws \Exception
	 */
	public function with_form( array $form ): QueryFilters {
		$clone = clone $this;
		$clone->set_form( $form );

		return $clone;
	}

	/**
	 * Sets filters on class instance.
	 *
	 * @since 1.0
	 *
	 * @param array $filters Field filters.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @internal
	 */
	public function set_filters( array $filters ) {
		$this->filters = $this->filter_factory->from_array( $filters );
	}

	/**
	 * Creates immutable instance with different filters.
	 *
	 * @since 2.0.0
	 *
	 * @param array $filters Field filters.
	 *
	 * @return QueryFilters
	 *
	 * @throws \Exception
	 */
	public function with_filters( array $filters ): QueryFilters {
		$clone = clone $this;
		$clone->set_filters( $filters );

		return $clone;
	}

	/**
	 * Creates an immutable instance with a date range filter.
	 *
	 * @since 2.9.0
	 *
	 * @param DateTimeInterface|null $start          The start of the date range.
	 * @param DateTimeInterface|null $end            The end of the date range.
	 * @param bool                   $use_exact_time Whether to preserve the exact time from the provided objects.
	 *
	 * @return self The new instance.
	 * @throws \InvalidArgumentException When no date was provided.
	 */
	public function with_date_range( ?DateTimeInterface $start = null, ?DateTimeInterface $end = null, bool $use_exact_time = false ): self {
		$clone                    = clone $this;
		$clone->date_range_filter = $this->filter_factory->date_range( $start, $end, 'date_created', $use_exact_time );

		return $clone;
	}

	/**
	 * Converts filters and returns GF Query conditions.
	 *
	 * @since 1.0
	 *
	 * @return GF_Query_Condition|null
	 *
	 * @throws RuntimeException
	 */
	public function get_query_conditions() {
		if ( empty( $this->form ) ) {
			throw new RuntimeException( 'Missing form object.' );
		}

		if ( ! $this->filters instanceof Filter && ! $this->date_range_filter ) {
			return null;
		}

		add_filter( 'gform_gf_query_sql', function ( array $query ): array {
			return SqlAdjustmentCallbacks::sql_empty_date_adjustment( $query );
		} );

		return $this->condition_factory->from_filter( $this->get_filters(), $this->form['id'] );
	}

	/**
	 * The filter visitors that finalize abstract filters.
	 *
	 * @since  2.0.0
	 *
	 * @return FilterVisitor[]|EntryAwareFilterVisitor[]|callable[] The visitors.
	 */
	private function get_filter_visitors(): array {
		$visitors = [
			new DisableFiltersVisitor(),
			new DisableAdminVisitor( $this->repository, $this->form ),
			new ProcessMergeTagsVisitor( $this->repository, $this->form ),
			new CurrentUserVisitor( $this->repository ),
			new UserIdVisitor( $this->repository, $this->form ),
			new ProcessDateVisitor( $this->repository, $this->form ),
			new ProcessFieldTypeVisitor( $this->repository, $this->form ),
		];

		/**
		 * Modifies the filters to be applied to the query.
		 *
		 * @since 2.0.0
		 *
		 * @param FilterVisitor[]|EntryAwareFilterVisitor[]|callable[] $visitors The visitors.
		 * @param array                                                $form     The GF form object.
		 */
		$visitors = apply_filters( 'gk/query-filters/filter/visitors', $visitors, $this->form );

		return array_filter(
			$visitors,
			static fn( $visitor ): bool => Filter::is_valid_visitor( $visitor ),
		);
	}

	/**
	 * Gets field filter options from Gravity Forms and modify them
	 *
	 * @see \GFCommon::get_field_filter_settings()
	 *
	 * @param int|null $form_id The form ID.
	 *
	 * @return array
	 */
	public function get_field_filters( ?int $form_id = null ): array {
		$form_id = $form_id ?? $this->form['id'] ?? null;

		if ( ! $form_id ) {
			return [];
		}

		return $this->repository->get_field_filters( $form_id );
	}

	/**
	 * Returns the forms for the Query filters.
	 *
	 * @since  2.7.0
	 *
	 * @param int|null $form_id The form ID.
	 *
	 * @return array{id:string, title:string}[] The forms.
	 *
	 * @internal
	 */
	public function get_forms( ?int $form_id = null ): array {
		$form = $this->repository->get_form( $form_id ?? $this->form['id'] ?? null );

		$forms = $form ? [ [ 'id' => $form['id'], 'title' => $form['title'] ] ] : [];

		/**
		 * Modifies the list of forms available for the Query Filters UI.
		 *
		 * @since 2.7.0
		 *
		 * @param array{id:string, title:string}[] $forms   The available forms.
		 * @param int                              $form_id The current form ID.
		 */
		return apply_filters( 'gk/query-filters/forms', $forms, $this->form['id'] ?? 0 );
	}

	/**
	 * Creates a filter that should return zero results.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public static function get_zero_results_filter(): array {
		return Filter::locked()->to_array();
	}

	/**
	 * Returns translation strings used in the UI.
	 *
	 * @since 1.0
	 *
	 * @return array $translations Translation strings.
	 */
	private function get_translations(): array {
		/**
		 * Modify default translation strings.
		 *
		 * @since 1.0
		 *
		 * @param array $translations Translation strings.
		 */
		$translations = apply_filters( 'gk/query-filters/translations', [
			'internet_explorer_notice'      => esc_html__( 'Internet Explorer is not supported. Please upgrade to another browser.', 'gravityview-advanced-filter' ),
			'fields_not_available'          => esc_html__( 'Form fields are not available. Please try refreshing the page.', 'gravityview-advanced-filter' ),
			'confirm_remove_group'          => esc_html__( 'This action will delete the entire group of conditions. Do you want to continue?', 'gravityview-advanced-filter' ),
			'toggle_group_mode'             => esc_html__( 'Click to Toggle the Group Mode', 'gravityview-advanced-filter' ),
			'add_group_label'               => esc_html__( 'Add a New Condition Group', 'gravityview-advanced-filter' ),
			'add_condition_label'           => esc_html__( 'Add a New Condition', 'gravityview-advanced-filter' ),
			'has_any'                       => esc_html__( 'has ANY of', 'gravityview-advanced-filter' ),
			'has_all'                       => esc_html__( 'has ALL of', 'gravityview-advanced-filter' ),
			'select_option'                 => esc_html__( 'Select option', 'gravityview-advanced-filter' ),
			'create_option'                 => esc_html__( 'Create this option', 'gravityview-advanced-filter' ),
			'duplicate_option'              => esc_html__( 'This option is already selected', 'gravityview-advanced-filter' ),
			'add_condition'                 => esc_html__( 'Add Condition', 'gravityview-advanced-filter' ),
			'add_created_by_user_condition' => esc_html__( 'Current User Condition', 'gravityview-advanced-filter' ),
			'condition'                     => esc_html__( 'Condition', 'gravityview-advanced-filter' ),
			'group'                         => esc_html__( 'Group ', 'gravityview-advanced-filter' ),
			'condition_join_operator'       => esc_html__( 'Condition Join Operator', 'gravityview-advanced-filter' ),
			'join_and'                      => esc_html_x( 'and', 'Join using "and" operator', 'gravityview-advanced-filter' ),
			'join_or'                       => esc_html_x( 'or', 'Join using "or" operator', 'gravityview-advanced-filter' ),
			'is'                            => esc_html_x( 'is', 'Filter operator (e.g., A is TRUE)', 'gravityview-advanced-filter' ),
			'isnot'                         => esc_html_x( 'is not', 'Filter operator (e.g., A is not TRUE)', 'gravityview-advanced-filter' ),
			'>'                             => esc_html_x( 'greater than', 'Filter operator (e.g., A is greater than B)', 'gravityview-advanced-filter' ),
			'<'                             => esc_html_x( 'less than', 'Filter operator (e.g., A is less than B)', 'gravityview-advanced-filter' ),
			'contains'                      => esc_html_x( 'contains', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
			'ncontains'                     => esc_html_x( 'does not contain', 'Filter operator (e.g., AB contains B)', 'gravityview-advanced-filter' ),
			'starts_with'                   => esc_html_x( 'starts with', 'Filter operator (e.g., AB starts with A)', 'gravityview-advanced-filter' ),
			'ends_with'                     => esc_html_x( 'ends with', 'Filter operator (e.g., AB ends with B)', 'gravityview-advanced-filter' ),
			'isbefore'                      => esc_html_x( 'is before', 'Filter operator (e.g., A is before date B)', 'gravityview-advanced-filter' ),
			'isafter'                       => esc_html_x( 'is after', 'Filter operator (e.g., A is after date B)', 'gravityview-advanced-filter' ),
			'ison'                          => esc_html_x( 'is on', 'Filter operator (e.g., A is on date B)', 'gravityview-advanced-filter' ),
			'isnoton'                       => esc_html_x( 'is not on', 'Filter operator (e.g., A is not on date B)', 'gravityview-advanced-filter' ),
			'isempty'                       => esc_html_x( 'is empty', 'Filter operator (e.g., A is empty)', 'gravityview-advanced-filter' ),
			'isnotempty'                    => esc_html_x( 'is not empty', 'Filter operator (e.g., A is not empty)', 'gravityview-advanced-filter' ),
			'remove_condition'              => esc_html__( 'Remove Condition', 'gravityview-advanced-filter' ),
			'remove_group'                  => esc_html__( 'Remove Group', 'gravityview-advanced-filter' ),
			'available_choices'             => esc_html__( 'Return to Field Choices', 'gravityview-advanced-filter' ),
			'available_choices_label'       => esc_html__( 'Return to the list of choices defined by the field.', 'gravityview-advanced-filter' ),
			'custom_is_operator_input'      => esc_html__( 'Custom Choice', 'gravityview-advanced-filter' ),
			'untitled'                      => esc_html__( 'Untitled', 'gravityview-advanced-filter' ),
			'form_fields'                   => esc_html__( 'Form Fields', 'gravityview-advanced-filter' ),
			'entry_properties'              => esc_html__( 'Entry Properties', 'gravityview-advanced-filter' ),
			'non_field_data'                => esc_html__( 'Non-Field Data', 'gravityview-advanced-filter' ),
			'select_field'                  => esc_html__( 'Select Field', 'gravityview-advanced-filter' ),
			'select_operator'               => esc_html__( 'Select Operator', 'gravityview-advanced-filter' ),
			'select_form'                   => esc_html__( 'Select Form', 'gravityview-advanced-filter' ),
			'field_not_available'           => esc_html__( 'Form field ID #%d is no longer available. Please remove this condition.', 'gravityview-advanced-filter' ),
		] );

		return $translations;
	}

	/**
	 * Enqueues UI scripts.
	 *
	 * @since 1.0
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 */
	public function enqueue_scripts( array $meta = [] ) {
		$script = 'assets/js/query-filters.js';
		$handle = $meta['handle'] ?? self::ASSETS_HANDLE;
		$ver    = $meta['ver'] ?? filemtime( plugin_dir_path( __DIR__ ) . $script );
		$src    = $meta['src'] ?? plugins_url( $script, __DIR__ );
		$deps   = $meta['deps'] ?? [ 'jquery' ];

		wp_enqueue_script( $handle, $src, $deps, $ver );

		$variable_name = $meta['variable_name'] ?? sprintf( 'gkQueryFilters_%s', bin2hex( random_bytes( 8 ) ) );
		wp_localize_script(
			$handle,
			$variable_name,
			[
				'forms'                     => $meta['forms'] ?? $this->get_forms(),
				'fields'                    => $meta['fields'] ?? $this->get_field_filters(),
				'conditions'                => $meta['conditions'] ?? [],
				'targetElementSelector'     => $meta['target_element_selector'] ?? '#gk-query-filters',
				'autoscrollElementSelector' => $meta['autoscroll_element_selector'] ?? '',
				'inputElementName'          => $meta['input_element_name'] ?? 'gk-query-filters',
				'translations'              => $meta['translations'] ?? $this->get_translations(),
				'maxNestingLevel'           => (int) ( $meta['max_nesting_level'] ?? 2 ),
			]
		);

		// Add a temporary metatag to force merge tag support for this page.
		add_action( 'admin_head',
			$cb = static function () use ( &$cb ) {
				remove_action( 'admin_head', $cb );
				echo '<meta class="merge-tag-support mt-initialized" style="display:none" />';
			} );
	}

	/**
	 * Enqueues date range picker scripts.
	 *
	 * @since 2.9.0
	 *
	 * @param array      $meta  Meta data.
	 * @param Clock|null $clock Optional clock instance.
	 *
	 * @return void
	 */
	public static function enqueue_date_range_picker( array $meta = [], ?Clock $clock = null ) {
		$variable_name = $meta['variable_name'] ?? null;
		if ( ! $variable_name ) {
			return;
		}

		$clock = $clock ?? new SystemClock();
		$now   = $clock->now();

		$script = 'assets/js/date-range-picker.js';
		$style  = 'assets/css/date-range-picker.css';

		$handle     = $meta['handle'] ?? 'gk-date-range-picker';
		$ver        = $meta['ver'] ?? filemtime( plugin_dir_path( __DIR__ ) . $script );
		$script_src = $meta['src'] ?? plugins_url( $script, __DIR__ );
		$style_src  = $meta['src'] ?? plugins_url( $style, __DIR__ );
		$deps       = $meta['deps'] ?? [];

		$label      = trim( $meta['label'] ?? '' );
		$value      = [ 'start' => $meta['start'] ?? null, 'end' => $meta['end'] ?? null ];
		$input_name = $meta['input_element_name'] ?? null;

		wp_enqueue_script( $handle, $script_src, $deps, $ver );
		wp_enqueue_style( $handle, $style_src, [], $ver );

		/**
		 * Modifies the date format for the date range picker.
		 *
		 * @since 2.9.0
		 *
		 * @param string $date_format The date format.
		 *                            Any combination of `mdy`, and optionally `_dot` or `_dash`. Slash by default.
		 *                            eg. `mdy`, `dmy_dash`, `ymd_dot`, etc.
		 */
		$date_format = apply_filters(
			'gk/query-filters/date-range-picker/date-format',
			$meta['date_format'] ?? 'mdy'
		);

		/**
		 * Modifies the date range picker value.
		 *
		 * @since 2.9.0
		 *
		 * @param array{start: ?string, end: ?string} $value  The date range picker value.
		 *                                                    Values should be in YYYY-mm-dd.
		 */
		$value = apply_filters( 'gk/query-filters/date-range-picker/value', $value );

		/**
		 * Modifies the date range picker translation strings.
		 *
		 * @since 2.9.0
		 *
		 * @param array $translations Translation strings.
		 */
		$translations = apply_filters(
			'gk/query-filters/date-range-picker/translations',
			[
				'presets' => esc_html__( 'Presets', 'gravityview-advanced-filter' ),
			]
		);

		/**
		 * Modifies the date range picker presets.
		 *
		 * @since 2.9.0
		 *
		 * @param array              $presets Preset configurations with label and range keys.
		 * @param \DateTimeInterface $now     The current date and time, used to compute preset ranges.
		 */
		$presets = apply_filters(
			'gk/query-filters/date-range-picker/presets',
			[
				[
					'label' => esc_html__( 'Today', 'gravityview-advanced-filter' ),
					'range' => [
						'start' => $now->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last 7 Days', 'gravityview-advanced-filter' ),
					'range' => [
						'start' => $now->modify( '-6 days' )->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last 30 Days', 'gravityview-advanced-filter' ),
					'range' => [
						'start' => $now->modify( '-29 days' )->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'This Month', 'gravityview-advanced-filter' ),
					'range' => [
						'start' => $now->modify( 'first day of this month' )->format( 'Y-m-d' ),
						'end'   => $now->modify( 'last day of this month' )->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last Month', 'gravityview-advanced-filter' ),
					'range' => [
						'start' => $now->modify( 'first day of last month' )->format( 'Y-m-d' ),
						'end'   => $now->modify( 'last day of last month' )->format( 'Y-m-d' ),
					],
				],
			],
			$now
		);

		wp_localize_script(
			$handle,
			$variable_name,
			array_filter(
				[
					'label'            => $label,
					'dateFormat'       => $date_format,
					'value'            => $value,
					'translations'     => $translations,
					'presets'          => $presets,
					'inputElementName' => $input_name,
				],
				static function ( $value ): bool {
					return ! is_null( $value ) && $value !== [] && $value !== '';
				}
			)
		);
	}

	/**
	 * Enqueues UI styles.
	 *
	 * @since 1.0
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 */
	public static function enqueue_styles( array $meta = [] ) {
		$style  = 'assets/css/query-filters.css';
		$handle = $meta['handle'] ?? self::ASSETS_HANDLE;
		$ver    = $meta['ver'] ?? filemtime( plugin_dir_path( __DIR__ ) . $style );
		$src    = $meta['src'] ?? plugins_url( $style, __DIR__ );
		$deps   = $meta['deps'] ?? [];

		wp_enqueue_style( $handle, $src, $deps, $ver );
	}

	/**
	 * Converts GF conditional logic rules to the object used by Query Filters.
	 *
	 * @since 1.0
	 *
	 * @param array $gf_conditional_logic GF conditional logic object.
	 *
	 * @return array Original or converted object.
	 */
	public static function convert_gf_conditional_logic(
		array $gf_conditional_logic,
		?FilterIdGenerator $id_generator = null
	) {
		if ( ! isset( $gf_conditional_logic['actionType'], $gf_conditional_logic['logicType'], $gf_conditional_logic['rules'] ) ) {
			return $gf_conditional_logic;
		}

		if ( ! isset( $id_generator ) ) {
			$id_generator = new RandomFilterIdGenerator();
		}

		$conditions = [];

		foreach ( $gf_conditional_logic['rules'] as $rule ) {
			$conditions[] = [
				'_id'      => $id_generator->get_id(),
				'key'      => $rule['fieldId'] ?? null,
				'operator' => $rule['operator'] ?? null,
				'value'    => $rule['value'] ?? null,
			];
		}

		// Outer group.
		$query_filters_conditional_logic = [
			'_id'        => $id_generator->get_id(),
			// Mode is the inverse of the actual condition group mode.
			'mode'       => 'all' === $gf_conditional_logic['logicType'] ? Filter::MODE_OR : Filter::MODE_AND,
			'conditions' => [],
		];

		$query_filters_conditional_logic['conditions'][] = [
			'_id'        => $id_generator->get_id(),
			'mode'       => 'all' === $gf_conditional_logic['logicType'] ? Filter::MODE_AND : Filter::MODE_OR,
			'conditions' => $conditions,
		];

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

		return $this->entry_filter_service->meets_filter( $entry, $this->get_filters( false, $entry ) );
	}

	/**
	 * The filter factory.
	 *
	 * @since 2.0.0
	 *
	 * @return FilterFactory
	 */
	final public function get_filter_factory(): FilterFactory {
		return $this->filter_factory;
	}

	/**
	 * Retrieves the finalized filters.
	 *
	 * @since 2.0.0
	 *
	 * @param array $entry          An optional entry object used as context.
	 *
	 * @param bool  $as_unprocessed Whether to return the filters unprocessed.
	 *
	 * @return Filter
	 */
	final public function get_filters( bool $as_unprocessed = false, array $entry = [] ): Filter {
		// Check if we have any filters at all.
		if ( ! $this->filters instanceof Filter && ! $this->date_range_filter instanceof Filter ) {
			throw new RuntimeException( 'Missing filter object.' );
		}

		// Clone existing filters if present.
		$filter = $this->filters instanceof Filter ? $this->filters : null;

		// Combine with the date range filter if both exist.
		if ( $filter instanceof Filter && $this->date_range_filter instanceof Filter ) {
			$filter = $filter->and( $this->date_range_filter );
		} elseif ( ! $filter instanceof Filter ) {
			// Only the date range filter exists.
			$filter = $this->date_range_filter;
		}

		$clone = clone $filter;

		if ( ! $as_unprocessed ) {
			foreach ( $this->get_filter_visitors() as $visitor ) {
				if ( $entry && Filter::is_entry_aware_visitor( $visitor ) ) {
					$visitor->set_entry( $entry );
				}

				$clone->accept( $visitor );
			}
		}

		return $clone;
	}
}
