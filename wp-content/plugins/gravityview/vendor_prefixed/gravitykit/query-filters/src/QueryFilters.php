<?php

namespace GravityKit\GravityView\QueryFilters;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GF_Query_Condition;
use GravityKit\GravityView\QueryFilters\Clock\Clock;
use GravityKit\GravityView\QueryFilters\Clock\SystemClock;
use GravityKit\GravityView\QueryFilters\Condition\ConditionFactory;
use GravityKit\GravityView\QueryFilters\Filter\EntryFilterService;
use GravityKit\GravityView\QueryFilters\Filter\Filter;
use GravityKit\GravityView\QueryFilters\Filter\FilterFactory;
use GravityKit\GravityView\QueryFilters\Filter\FilterIdGenerator;
use GravityKit\GravityView\QueryFilters\Filter\RandomFilterIdGenerator;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\CurrentUserVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\DisableAdminVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\DisableFiltersVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\EntryAwareFilterVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\FilterVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\ProcessDateVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\ProcessFieldTypeVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\ProcessMergeTagsVisitor;
use GravityKit\GravityView\QueryFilters\Filter\Visitor\UserIdVisitor;
use GravityKit\GravityView\QueryFilters\MergeTag\FormMergeModifier;
use GravityKit\GravityView\QueryFilters\Querying\Field\Source\ChoiceSourceManager;
use GravityKit\GravityView\QueryFilters\Querying\Field\Source\FieldChoicesSource;
use GravityKit\GravityView\QueryFilters\Querying\Field\Source\Integrations;
use GravityKit\GravityView\QueryFilters\Repository\DefaultRepository;
use GravityKit\GravityView\QueryFilters\Rest\Choice\ChoiceController;
use GravityKit\GravityView\QueryFilters\Rest\Endpoint\EndpointRegistry;
use GravityKit\GravityView\QueryFilters\Sql\SqlAdjustmentCallbacks;
use RuntimeException;

class QueryFilters {
	/**
	 * @since 1.0
	 * @var array Assets handle.
	 */
	/**
	 * The version of this copy of the library.
	 *
	 * Several plugins can vendor their own copy and load them on one screen. The version decides
	 * which copy owns the shared browser globals, so it must be bumped with every release.
	 *
	 * @since $ver$
	 */
	public const VERSION = '2.16.0';

	public const ASSETS_HANDLE = 'gk-query-filters';

	/**
	 * @since 2.11.0
	 * @var string Internal handle for the shared date-picker bundle.
	 */
	private const PICKERS_HANDLE = 'gk-query-filters-pickers';

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

		FormMergeModifier::register();
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
	 * Registers the ChoicesField REST routes.
	 *
	 * @since 2.12.0
	 *
	 * @param string|null $prefix Optional explicit prefix. Defaults to {@see self::resolve_prefix()}.
	 *
	 * @return string The resolved REST namespace prefix.
	 */
	public static function register_rest_routes( ?string $prefix = null ): string {
		$resolved_prefix = self::resolve_prefix( $prefix );
		$manager         = self::choice_source_manager();

		add_action( 'rest_api_init', static function () use ( $resolved_prefix, $manager ): void {
			ChoiceController::register( $resolved_prefix, $manager );
		} );

		EndpointRegistry::register_defaults( $resolved_prefix, $manager );

		self::register_field_filter_hooks();

		return $resolved_prefix;
	}

	/**
	 * The memoized choice source manager, sources registered ahead of the field-choices fallback.
	 *
	 * @since 2.14.0
	 *
	 * @var ChoiceSourceManager|null
	 */
	private static ?ChoiceSourceManager $choice_source_manager = null;

	/**
	 * Returns the choice source manager, building it on first use.
	 *
	 * @since 2.14.0
	 *
	 * @return ChoiceSourceManager The manager.
	 */
	private static function choice_source_manager(): ChoiceSourceManager {
		if ( null === self::$choice_source_manager ) {
			$manager = new ChoiceSourceManager( new FieldChoicesSource() );

			Integrations::register( $manager );

			self::$choice_source_manager = $manager;
		}

		return self::$choice_source_manager;
	}

	/**
	 * Tags choice-bearing field filters with the `field` auto-endpoint hint so they can be
	 * auto-switched to the on-demand endpoint, and grants them the `has_*` multi-value operators.
	 * Fields that already declare an endpoint, or that no source claims, are left untouched.
	 *
	 * @since 2.14.0
	 *
	 * @param array $fields The field filters, each carrying `form_id` and `key`.
	 *
	 * @return array The tagged field filters.
	 */
	private static function tag_choice_fields( array $fields ): array {
		$manager = self::choice_source_manager();

		foreach ( $fields as $index => $field ) {
			if ( ! is_array( $field ) || isset( $field['endpoint'] ) || isset( $field['auto_endpoint'] ) ) {
				continue;
			}

			$form_id  = (int) ( $field['form_id'] ?? 0 );
			$field_id = (string) ( $field['key'] ?? '' );
			if ( $form_id < 1 || '' === $field_id ) {
				continue;
			}

			if ( ! $manager->supports( $form_id, $field_id ) ) {
				continue;
			}

			$fields[ $index ]['auto_endpoint'] = 'field';
			$fields[ $index ]['operators']     = array_values(
				array_unique(
					array_merge(
						$field['operators'] ?? [],
						[ 'has_any', 'has_all', 'has_none' ]
					)
				)
			);
		}

		return $fields;
	}

	/**
	 * Enriches field filters with their choice endpoints: tags choice-bearing fields and resolves
	 * each `endpoint`/`auto_endpoint` declaration into the descriptor the UI consumes.
	 *
	 * @since 2.14.0
	 *
	 * @param array $fields The raw field filters, each carrying `form_id` and `key`.
	 *
	 * @return array The enriched field filters.
	 */
	public static function prepare_field_filters( array $fields ): array {
		return EndpointRegistry::apply_to_field_filters( self::tag_choice_fields( $fields ) );
	}

	/**
	 * Builds and enriches the field-filter list for a form, so QF owns the list a consumer would
	 * otherwise assemble itself. A field list already supplied by an earlier caller is returned as is.
	 *
	 * @since 2.14.0
	 *
	 * @param mixed $fields  A field list from an earlier filter, or null to have QF build one.
	 * @param int   $form_id The form to build field filters for.
	 *
	 * @return array The enriched field filters.
	 */
	public static function provide_field_filters( $fields, int $form_id ): array {
		if ( is_array( $fields ) ) {
			return $fields;
		}

		return self::prepare_field_filters( self::create()->get_field_filters( $form_id ) );
	}

	/**
	 * Registers the field-filter hooks consumers use to delegate field-list building to QF.
	 *
	 * Enrich a list you built yourself:
	 * `$fields = apply_filters( 'gk/query-filters/prepare-field-filters', $fields );`
	 *
	 * Let QF build and enrich the whole list for a form:
	 * `$fields = apply_filters( 'gk/query-filters/fields-for-form', null, $form_id );`
	 *
	 * @since 2.14.0
	 */
	private static function register_field_filter_hooks(): void {
		add_filter( 'gk/query-filters/prepare-field-filters', [ self::class, 'prepare_field_filters' ] );
		add_filter( 'gk/query-filters/fields-for-form', [ self::class, 'provide_field_filters' ], 10, 2 );
	}

	/**
	 * Derive a default REST prefix from the current PHP namespace.
	 *
	 * `GravityKit\GravityCharts\QueryFilters\…` → `gravitycharts/v1`.
	 * `GravityKit\QueryFilters\…`               → `gk-queryfilters/v1`.
	 *
	 * @since 2.12.0
	 *
	 * @param string|null $prefix A predefined prefix.
	 *
	 * @return string The derived prefix.
	 */
	private static function resolve_prefix( ?string $prefix = null ): string {
		if ( $prefix ) {
			return $prefix;
		}

		$without_vendor = preg_replace( '#^GravityKit\\\\#', '', __NAMESPACE__ );
		$first_segment  = strstr( $without_vendor, '\\', true );
		$first_segment  = $first_segment !== false ? $first_segment : $without_vendor;

		if ( $first_segment === 'QueryFilters' ) {
			return 'gk-queryfilters/v1';
		}

		return strtolower( $first_segment ) . '/v1';
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
	public function with_date_range(
		?DateTimeInterface $start = null,
		?DateTimeInterface $end = null,
		bool $use_exact_time = false
	): self {
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
			'internet_explorer_notice'      => esc_html__( 'Internet Explorer is not supported. Please upgrade to another browser.',
				'gk-query-filters', 'gk-gravityview' ),
			'fields_not_available'          => esc_html__( 'Form fields are not available. Please try refreshing the page.',
				'gk-query-filters', 'gk-gravityview' ),
			'confirm_remove_group'          => esc_html__( 'This action will delete the entire group of conditions. Do you want to continue?',
				'gk-query-filters', 'gk-gravityview' ),
			'toggle_group_mode'             => esc_html__( 'Click to Toggle the Group Mode', 'gk-query-filters', 'gk-gravityview' ),
			'add_group_label'               => esc_html__( 'Add a New Condition Group', 'gk-query-filters', 'gk-gravityview' ),
			'add_condition_label'           => esc_html__( 'Add a New Condition', 'gk-query-filters', 'gk-gravityview' ),
			'has_any'                       => esc_html__( 'has ANY of', 'gk-query-filters', 'gk-gravityview' ),
			'has_all'                       => esc_html__( 'has ALL of', 'gk-query-filters', 'gk-gravityview' ),
			'has_none'                      => esc_html__( 'has NONE of', 'gk-query-filters', 'gk-gravityview' ),
			'select_option'                 => esc_html__( 'Select option', 'gk-query-filters', 'gk-gravityview' ),
			'create_option'                 => esc_html__( 'Create this option', 'gk-query-filters', 'gk-gravityview' ),
			'duplicate_option'              => esc_html__( 'This option is already selected', 'gk-query-filters', 'gk-gravityview' ),
			'add_condition'                 => esc_html__( 'Add Condition', 'gk-query-filters', 'gk-gravityview' ),
			'add_created_by_user_condition' => esc_html__( 'Current User Condition', 'gk-query-filters', 'gk-gravityview' ),
			'condition'                     => esc_html__( 'Condition', 'gk-query-filters', 'gk-gravityview' ),
			'group'                         => esc_html__( 'Group ', 'gk-query-filters', 'gk-gravityview' ),
			'condition_join_operator'       => esc_html__( 'Condition Join Operator', 'gk-query-filters', 'gk-gravityview' ),
			'join_and'                      => esc_html_x( 'and', 'Join using "and" operator', 'gk-query-filters', 'gk-gravityview' ),
			'join_or'                       => esc_html_x( 'or', 'Join using "or" operator', 'gk-query-filters', 'gk-gravityview' ),
			'is'                            => esc_html_x( 'is',
				'Filter operator (e.g., A is TRUE)',
				'gk-query-filters', 'gk-gravityview' ),
			'isnot'                         => esc_html_x( 'is not',
				'Filter operator (e.g., A is not TRUE)',
				'gk-query-filters', 'gk-gravityview' ),
			'>'                             => esc_html_x( 'greater than',
				'Filter operator (e.g., A is greater than B)',
				'gk-query-filters', 'gk-gravityview' ),
			'<'                             => esc_html_x( 'less than',
				'Filter operator (e.g., A is less than B)',
				'gk-query-filters', 'gk-gravityview' ),
			'contains'                      => esc_html_x( 'contains',
				'Filter operator (e.g., AB contains B)',
				'gk-query-filters', 'gk-gravityview' ),
			'ncontains'                     => esc_html_x( 'does not contain',
				'Filter operator (e.g., AB contains B)',
				'gk-query-filters', 'gk-gravityview' ),
			'starts_with'                   => esc_html_x( 'starts with',
				'Filter operator (e.g., AB starts with A)',
				'gk-query-filters', 'gk-gravityview' ),
			'ends_with'                     => esc_html_x( 'ends with',
				'Filter operator (e.g., AB ends with B)',
				'gk-query-filters', 'gk-gravityview' ),
			'isbefore'                      => esc_html_x( 'is before',
				'Filter operator (e.g., A is before date B)',
				'gk-query-filters', 'gk-gravityview' ),
			'isafter'                       => esc_html_x( 'is after',
				'Filter operator (e.g., A is after date B)',
				'gk-query-filters', 'gk-gravityview' ),
			'ison'                          => esc_html_x( 'is on',
				'Filter operator (e.g., A is on date B)',
				'gk-query-filters', 'gk-gravityview' ),
			'isnoton'                       => esc_html_x( 'is not on',
				'Filter operator (e.g., A is not on date B)',
				'gk-query-filters', 'gk-gravityview' ),
			'isempty'                       => esc_html_x( 'is empty',
				'Filter operator (e.g., A is empty)',
				'gk-query-filters', 'gk-gravityview' ),
			'isnotempty'                    => esc_html_x( 'is not empty',
				'Filter operator (e.g., A is not empty)',
				'gk-query-filters', 'gk-gravityview' ),
			'remove_condition'              => esc_html__( 'Remove Condition', 'gk-query-filters', 'gk-gravityview' ),
			'remove_group'                  => esc_html__( 'Remove Group', 'gk-query-filters', 'gk-gravityview' ),
			'available_choices'             => esc_html__( 'Return to Field Choices', 'gk-query-filters', 'gk-gravityview' ),
			'available_choices_label'       => esc_html__( 'Return to the list of choices defined by the field.',
				'gk-query-filters', 'gk-gravityview' ),
			'custom_is_operator_input'      => esc_html__( 'Custom Choice', 'gk-query-filters', 'gk-gravityview' ),
			'custom_date'                   => esc_html__( 'Custom Date', 'gk-query-filters', 'gk-gravityview' ),
			'custom_value'                  => esc_html__( 'Custom Value', 'gk-query-filters', 'gk-gravityview' ),
			'custom_value_hint'             => esc_html__( 'Type to add a custom value', 'gk-query-filters', 'gk-gravityview' ),
			'open_calendar'                 => esc_html__( 'Open calendar', 'gk-query-filters', 'gk-gravityview' ),
			'reset_date'                    => esc_html__( 'Clear date', 'gk-query-filters', 'gk-gravityview' ),
			'reset_date_range'              => esc_html__( 'Clear date range', 'gk-query-filters', 'gk-gravityview' ),
			'untitled'                      => esc_html__( 'Untitled', 'gk-query-filters', 'gk-gravityview' ),
			'form_fields'                   => esc_html__( 'Form Fields', 'gk-query-filters', 'gk-gravityview' ),
			'entry_properties'              => esc_html__( 'Entry Properties', 'gk-query-filters', 'gk-gravityview' ),
			'non_field_data'                => esc_html__( 'Non-Field Data', 'gk-query-filters', 'gk-gravityview' ),
			'select_field'                  => esc_html__( 'Select Field', 'gk-query-filters', 'gk-gravityview' ),
			'select_operator'               => esc_html__( 'Select Operator', 'gk-query-filters', 'gk-gravityview' ),
			'select_form'                   => esc_html__( 'Select Form', 'gk-query-filters', 'gk-gravityview' ),
			'field_not_available'           => esc_html__( 'Form field ID #%d is no longer available. Please remove this condition.',
				'gk-query-filters', 'gk-gravityview' ),
			'no_results'                    => esc_html__( 'No results', 'gk-query-filters', 'gk-gravityview' ),
			'loading'                       => esc_html__( 'Loading…', 'gk-query-filters', 'gk-gravityview' ),
			'loading_more'                  => esc_html__( 'Loading more…', 'gk-query-filters', 'gk-gravityview' ),
			'clear'                         => esc_html__( 'Clear all', 'gk-query-filters', 'gk-gravityview' ),
			'remove'                        => esc_html__( 'Remove', 'gk-query-filters', 'gk-gravityview' ),
			'open_choices'                  => esc_html__( 'Show choices', 'gk-query-filters', 'gk-gravityview' ),
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
		// Register the shared date-picker bundle so the main app can depend on it instead of
		// duplicating bits-ui and @internationalized/date into the query-filters.js bundle.
		$pickers_handle = self::register_pickers_bundle();

		$script = 'assets/js/query-filters.js';
		$handle = $meta['handle'] ?? self::ASSETS_HANDLE;
		$ver    = $meta['ver'] ?? self::VERSION;
		$src    = $meta['src'] ?? plugins_url( $script, __DIR__ );
		$deps   = $meta['deps'] ?? [ 'jquery', $pickers_handle ];

		if ( self::claims_handle( $handle ) ) {
			wp_enqueue_script( $handle, $src, $deps, $ver );
		} else {
			wp_enqueue_script( $handle );
		}
		wp_enqueue_style( $pickers_handle );

		$fields = self::prepare_field_filters( $meta['fields'] ?? $this->get_field_filters() );

		$variable_name = $meta['variable_name'] ?? sprintf( 'gkQueryFilters_%s', bin2hex( random_bytes( 8 ) ) );
		wp_localize_script(
			$handle,
			$variable_name,
			[
				'forms'                     => $meta['forms'] ?? $this->get_forms(),
				'fields'                    => $fields,
				'conditions'                => $meta['conditions'] ?? [],
				'nonce'                     => $meta['nonce'] ?? wp_create_nonce( 'wp_rest' ),
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
	 * The current WordPress locale formatted as a BCP-47 tag for the picker's `Intl`-based formatters.
	 *
	 * @since 2.11.0
	 *
	 * @return string The locale tag (e.g. `nl-NL`).
	 */
	private static function get_picker_locale(): string {
		/**
		 * Modifies the BCP-47 locale tag passed to the date picker and date range picker.
		 *
		 * @since 2.11.0
		 *
		 * @param string $locale The BCP-47 locale tag (e.g. `nl-NL`).
		 */
		return (string) apply_filters(
			'gk/query-filters/date-picker/locale',
			str_replace( '_', '-', get_locale() )
		);
	}

	/**
	 * Default translation strings shared between the date picker and date range picker.
	 *
	 * @since 2.11.0
	 *
	 * @return array<string, string> Default translation strings keyed by translation key.
	 */
	private static function get_picker_translations(): array {
		return [
			'presets'          => esc_html__( 'Presets', 'gk-query-filters', 'gk-gravityview' ),
			'open_calendar'    => esc_html__( 'Open calendar', 'gk-query-filters', 'gk-gravityview' ),
			'reset_date'       => esc_html__( 'Clear date', 'gk-query-filters', 'gk-gravityview' ),
			'reset_date_range' => esc_html__( 'Clear date range', 'gk-query-filters', 'gk-gravityview' ),
			'prev_month'       => esc_html__( 'Previous month', 'gk-query-filters', 'gk-gravityview' ),
			'next_month'       => esc_html__( 'Next month', 'gk-query-filters', 'gk-gravityview' ),
			'today'            => esc_html__( 'Today', 'gk-query-filters', 'gk-gravityview' ),
			'today_aria_label' => esc_html__( "Jump to today's month", 'gk-query-filters', 'gk-gravityview' ),
		];
	}

	/**
	 * Returns whether this copy of the library should register the handle.
	 *
	 * Several plugins can vendor their own copy, and they all register under the same handle:
	 * Strauss prefixes PHP symbols, not the strings WordPress keys scripts on. The handle cannot be
	 * scoped per copy either, since hosts declare it as a dependency by name. So the copies compete
	 * for it, and the newest wins -- a version that does not read as one belongs to a copy from
	 * before this was decided, and loses to any that does.
	 *
	 * @since $ver$
	 *
	 * @param string $handle The handle to claim.
	 *
	 * @return bool Whether to go on and register.
	 */
	private static function claims_handle( string $handle ): bool {
		$registered = wp_scripts()->query( $handle, 'registered' );

		if ( ! $registered ) {
			return true;
		}

		$version = (string) ( $registered->ver ?? '' );

		if ( preg_match( '/^\d+\.\d+/', $version ) && version_compare( self::VERSION, $version, '<=' ) ) {
			return false;
		}

		wp_deregister_script( $handle );
		wp_deregister_style( $handle );

		return true;
	}

	/**
	 * Registers the shared date-picker bundle under a single handle.
	 *
	 * Consumers can override handle, version, source URLs, and additional dependencies via `$meta`
	 * for prefixed/vendored plugin setups. Returns the handle actually registered so callers can
	 * attach `wp_localize_script` / `wp_enqueue_script` to it without duplicating script tags.
	 *
	 * @since 2.11.0
	 *
	 * @param array $meta Optional overrides: `handle`, `ver`, `src`, `style_src`, `deps`.
	 *
	 * @return string The handle the bundle is registered under.
	 */
	private static function register_pickers_bundle( array $meta = [] ): string {
		$handle = $meta['handle'] ?? self::PICKERS_HANDLE;

		if ( ! self::claims_handle( $handle ) ) {
			return $handle;
		}

		$script     = 'assets/js/date-range-picker.js';
		$style      = 'assets/css/date-range-picker.css';
		$ver        = $meta['ver'] ?? self::VERSION;
		$script_src = $meta['src'] ?? plugins_url( $script, __DIR__ );
		$style_src  = $meta['style_src'] ?? plugins_url( $style, __DIR__ );
		// jQuery is always required — the bundle registers `$.fn.dateRangePicker` and `$.fn.datePicker`.
		$deps = array_values( array_unique( array_merge( [ 'jquery' ], $meta['deps'] ?? [] ) ) );

		wp_register_script( $handle, $script_src, $deps, $ver );
		wp_register_style( $handle, $style_src, [], $ver );

		return $handle;
	}

	/**
	 * Resolves the minimum and maximum selectable date for a picker.
	 *
	 * Reads `min_date` / `max_date` from `$meta` and passes each through its kind-specific filter
	 * (`gk/query-filters/date-picker/{min,max}-date` or `gk/query-filters/date-range-picker/{min,max}-date`)
	 * so PHP consumers can constrain the selectable range without writing JS. Returns null on
	 * either bound to leave it unset; the caller's `array_filter` strips those before localizing.
	 *
	 * @since 2.12.0
	 *
	 * @param array  $meta        Enqueue meta array. Reads `min_date` / `max_date` (YYYY-mm-dd).
	 * @param string $picker_kind Hook-name segment for the picker (`date-picker` or `date-range-picker`).
	 *
	 * @return array{minDate: string|null, maxDate: string|null} Resolved bounds.
	 */
	private static function apply_date_bound_filters( array $meta, string $picker_kind ): array {
		/**
		 * Modifies the minimum selectable date for the picker.
		 *
		 * Fires under `gk/query-filters/date-picker/min-date` and
		 * `gk/query-filters/date-range-picker/min-date` depending on the active picker.
		 *
		 * @since 2.12.0
		 *
		 * @param string|null $min_date The minimum date in YYYY-mm-dd, or null for no lower bound.
		 */
		$min_date = self::normalize_picker_date(
			apply_filters( "gk/query-filters/{$picker_kind}/min-date", $meta['min_date'] ?? null )
		);

		/**
		 * Modifies the maximum selectable date for the picker.
		 *
		 * Fires under `gk/query-filters/date-picker/max-date` and
		 * `gk/query-filters/date-range-picker/max-date` depending on the active picker.
		 *
		 * @since 2.12.0
		 *
		 * @param string|null $max_date The maximum date in YYYY-mm-dd, or null for no upper bound.
		 */
		$max_date = self::normalize_picker_date(
			apply_filters( "gk/query-filters/{$picker_kind}/max-date", $meta['max_date'] ?? null )
		);

		// An inverted range is a configuration bug — drop both bounds so the picker stays usable
		// rather than rendering an impossible window.
		if ( null !== $min_date && null !== $max_date && $min_date > $max_date ) {
			$min_date = null;
			$max_date = null;
		}

		return [
			'minDate' => $min_date,
			'maxDate' => $max_date,
		];
	}

	/**
	 * Coerce a raw picker date bound into a strict YYYY-mm-dd string, or null when invalid.
	 *
	 * Non-string input, blank strings, ill-formatted strings, and calendar-overflow dates
	 * (e.g. `2026-02-30`) all collapse to null so the picker treats the bound as unset.
	 *
	 * @since 2.12.0
	 *
	 * @param mixed $value Raw input (from filter, meta, or both).
	 *
	 * @return string|null A canonical YYYY-mm-dd string, or null when the input doesn't parse.
	 */
	private static function normalize_picker_date( $value ): ?string {
		if ( ! is_string( $value ) || '' === $value ) {
			return null;
		}

		$date = DateTimeImmutable::createFromFormat( '!Y-m-d', $value );
		if ( ! $date instanceof DateTimeImmutable ) {
			return null;
		}

		// Round-trip catches overflow values that PHP's lenient parser silently rolls forward.
		return $date->format( 'Y-m-d' ) === $value ? $value : null;
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

		$handle = self::register_pickers_bundle( $meta );

		$label      = trim( $meta['label'] ?? '' );
		$value      = [ 'start' => $meta['start'] ?? null, 'end' => $meta['end'] ?? null ];
		$input_name = $meta['input_element_name'] ?? null;

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle );

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
			self::get_picker_translations()
		);

		$bounds = self::apply_date_bound_filters( $meta, 'date-range-picker' );

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
					'label' => esc_html__( 'Today', 'gk-query-filters', 'gk-gravityview' ),
					'range' => [
						'start' => $now->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last 7 Days', 'gk-query-filters', 'gk-gravityview' ),
					'range' => [
						'start' => $now->modify( '-6 days' )->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last 30 Days', 'gk-query-filters', 'gk-gravityview' ),
					'range' => [
						'start' => $now->modify( '-29 days' )->format( 'Y-m-d' ),
						'end'   => $now->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'This Month', 'gk-query-filters', 'gk-gravityview' ),
					'range' => [
						'start' => $now->modify( 'first day of this month' )->format( 'Y-m-d' ),
						'end'   => $now->modify( 'last day of this month' )->format( 'Y-m-d' ),
					],
				],
				[
					'label' => esc_html__( 'Last Month', 'gk-query-filters', 'gk-gravityview' ),
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
					'locale'           => self::get_picker_locale(),
					'minDate'          => $bounds['minDate'],
					'maxDate'          => $bounds['maxDate'],
				],
				static function ( $value ): bool {
					return ! is_null( $value ) && $value !== [] && $value !== '';
				}
			)
		);
	}

	/**
	 * Enqueues the single date picker scripts.
	 *
	 * Registers the bundle that exposes both `$.fn.dateRangePicker` and
	 * `$.fn.datePicker`, localized for a single-date selection.
	 *
	 * @since 2.11.0
	 *
	 * @param array $meta Meta data.
	 *
	 * @return void
	 */
	public static function enqueue_date_picker( array $meta = [] ) {
		$variable_name = $meta['variable_name'] ?? null;
		if ( ! $variable_name ) {
			return;
		}

		$handle = self::register_pickers_bundle( $meta );

		$label      = trim( $meta['label'] ?? '' );
		$value      = [ 'date' => $meta['date'] ?? null ];
		$input_name = $meta['input_element_name'] ?? null;

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle );

		/**
		 * Modifies the date format for the single date picker.
		 *
		 * @since 2.11.0
		 *
		 * @param string $date_format The date format.
		 *                            Any combination of `mdy`, and optionally `_dot` or `_dash`. Slash by default.
		 *                            eg. `mdy`, `dmy_dash`, `ymd_dot`, etc.
		 */
		$date_format = apply_filters(
			'gk/query-filters/date-picker/date-format',
			$meta['date_format'] ?? 'mdy'
		);

		/**
		 * Modifies the single date picker value.
		 *
		 * @since 2.11.0
		 *
		 * @param array{date: ?string} $value The date picker value.
		 *                                    Value should be in YYYY-mm-dd.
		 */
		$value = apply_filters( 'gk/query-filters/date-picker/value', $value );

		/**
		 * Modifies the single date picker translation strings.
		 *
		 * @since 2.11.0
		 *
		 * @param array $translations Translation strings.
		 */
		$translations = apply_filters(
			'gk/query-filters/date-picker/translations',
			self::get_picker_translations()
		);

		$bounds = self::apply_date_bound_filters( $meta, 'date-picker' );

		wp_localize_script(
			$handle,
			$variable_name,
			array_filter(
				[
					'label'            => $label,
					'dateFormat'       => $date_format,
					'value'            => $value,
					'translations'     => $translations,
					'inputElementName' => $input_name,
					'locale'           => self::get_picker_locale(),
					'minDate'          => $bounds['minDate'],
					'maxDate'          => $bounds['maxDate'],
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
		$ver    = $meta['ver'] ?? self::VERSION;
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
