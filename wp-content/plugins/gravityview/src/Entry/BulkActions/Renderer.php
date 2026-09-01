<?php
/**
 * Frontend bulk action table rendering.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

use GV\View;
use GravityKit\GravityView\Template\TemplateContext;

/**
 * Renders the frontend table controls for bulk actions.
 *
 * @since 3.0.0
 */
final class Renderer {
	/**
	 * @since 3.0.0
	 * @var Registry
	 */
	private $registry;

	/**
	 * @since 3.0.0
	 * @var ViewEligibility
	 */
	private $eligibility;

	/**
	 * @since 3.0.0
	 * @var FlashMessages
	 */
	private $flash_messages;

	/**
	 * @since 3.0.0
	 * @var SelectionMode
	 */
	private $selection_mode;

	/**
	 * @since 3.0.0
	 * @var BackgroundStatus
	 */
	private $background_status;

	/**
	 * @since 3.0.0
	 * @var array
	 */
	private $available_actions = [];

	/**
	 * Shared template data keyed by render instance.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	private $template_data_cache = [];

	/**
	 * @since 3.0.0
	 * @var string
	 */
	private const RENDER_INSTANCE_PROPERTY = 'gv_bulk_render_instance_id';

	/**
	 * @since 3.0.0
	 * @var array
	 */
	private static $render_instance_counts = [];

	/**
	 * @since 3.0.0
	 *
	 * @param Registry         $registry       Action registry.
	 * @param ViewEligibility  $eligibility    View eligibility checker.
	 * @param FlashMessages    $flash_messages Flash message service.
	 * @param SelectionMode    $selection_mode     Selected-results mode service.
	 * @param BackgroundStatus $background_status  Background job status service.
	 */
	public function __construct(
		Registry $registry,
		ViewEligibility $eligibility,
		FlashMessages $flash_messages,
		SelectionMode $selection_mode,
		BackgroundStatus $background_status
	) {
		$this->registry          = $registry;
		$this->eligibility       = $eligibility;
		$this->flash_messages    = $flash_messages;
		$this->selection_mode    = $selection_mode;
		$this->background_status = $background_status;
	}

	/**
	 * Renders the bulk action toolbar.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_toolbar( $context ) {
		if ( ! $this->is_bulk_ui_available( $context ) ) {
			return;
		}

		$data = $this->get_template_data( $context, 'table/bulk-actions/toolbar' );

		$this->flash_messages->render_system( $context->view );
		$this->flash_messages->render( $context->view, $data['render_instance_id'] );
		$this->background_status->render( $context->view, $data['render_instance_id'] );

		$this->render_template(
			$context,
			'table/bulk-actions/toolbar',
			$data
		);
	}

	/**
	 * Renders the selection summary as a table row.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_table_selection_summary( $context ) {
		if ( ! $this->is_bulk_ui_available( $context ) || 'table_body_before' !== $this->get_selection_summary_position( $context ) ) {
			return;
		}

		$this->render_template(
			$context,
			'table/bulk-actions/table-selection-row',
			$this->get_template_data(
				$context,
				'table/bulk-actions/table-selection-row',
				[
					'column_count' => $this->get_table_column_count( $context ),
				]
			)
		);
	}

	/**
	 * Renders the select-all column header at the beginning of the table.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_header_cell( $context ) {
		$this->render_header_cell_for_position( $context, 'first' );
	}

	/**
	 * Renders the select-all column header at the end of the table.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_header_cell_after( $context ) {
		$this->render_header_cell_for_position( $context, 'last' );
	}

	/**
	 * Renders an entry row checkbox at the beginning of the table.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_entry_cell( $context ) {
		$this->render_entry_cell_for_position( $context, 'first' );
	}

	/**
	 * Renders an entry row checkbox at the end of the table.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return void
	 */
	public function render_entry_cell_after( $context ) {
		$this->render_entry_cell_for_position( $context, 'last' );
	}

	/**
	 * Adds the checkbox column to no-results colspan calculations.
	 *
	 * @since 3.0.0
	 *
	 * @param int             $count   Column count.
	 * @param TemplateContext $context Template context.
	 *
	 * @return int
	 */
	public function filter_column_count( $count, $context ) {
		if ( $this->is_bulk_ui_available( $context ) ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Whether bulk action controls should be rendered for the context.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return bool
	 */
	private function is_bulk_ui_available( $context ) {
		if ( ! $this->eligibility->is_table_context( $context ) || ! $this->eligibility->is_enabled_for_view( $context->view ) ) {
			return false;
		}

		return ! empty( $this->get_available_actions( $context->view ) );
	}

	/**
	 * Renders a template overrideable partial.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context       Template context.
	 * @param string          $template_slug Template slug.
	 * @param array           $data          Template data.
	 *
	 * @return void
	 */
	private function render_template( TemplateContext $context, $template_slug, array $data ) {
		$view    = $context->view;
		$view_id = (int) $view->ID;

		/**
		 * Filters whether a frontend bulk action template partial should render.
		 *
		 * @since 3.0.0
		 *
		 * @param bool             $render        Whether to render the template partial.
		 * @param int              $view_id       View ID.
		 * @param \GV\View         $view          View object.
		 * @param TemplateContext  $context       Template context.
		 * @param string           $template_slug Template slug.
		 * @param array            $data          Template data.
		 */
		if ( ! apply_filters( 'gk/gravityview/bulk-actions/render-template', true, $view_id, $view, $context, $template_slug, $data ) ) {
			return;
		}

		$template = $context->template->get_template_part( $template_slug, null, false );

		if ( ! $template ) {
			return;
		}

		$gravityview  = $context;
		$bulk_actions = (object) $data;

		include $template;
	}

	/**
	 * Renders the select-all column header for the requested position.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context  Template context.
	 * @param string          $position Column position.
	 *
	 * @return void
	 */
	private function render_header_cell_for_position( $context, $position ) {
		if ( ! $this->is_bulk_ui_available( $context ) || $position !== $this->get_checkbox_column_position( $context ) ) {
			return;
		}

		$this->render_template(
			$context,
			'table/bulk-actions/header-checkbox',
			$this->get_template_data( $context, 'table/bulk-actions/header-checkbox' )
		);
	}

	/**
	 * Renders an entry row checkbox for the requested position.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context  Template context.
	 * @param string          $position Column position.
	 *
	 * @return void
	 */
	private function render_entry_cell_for_position( $context, $position ) {
		if ( ! $this->is_bulk_ui_available( $context ) || $position !== $this->get_checkbox_column_position( $context ) || empty( $context->entry->ID ) ) {
			return;
		}

		$this->render_template(
			$context,
			'table/bulk-actions/entry-checkbox',
			$this->get_template_data(
				$context,
				'table/bulk-actions/entry-checkbox',
				[
					'entry_id' => absint( $context->entry->ID ),
				]
			)
		);
	}

	/**
	 * Returns data made available to bulk action templates.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context       Template context.
	 * @param string          $template_slug Template slug.
	 * @param array           $extra         Additional data.
	 *
	 * @return array
	 */
	private function get_template_data( TemplateContext $context, $template_slug, array $extra = [] ) {
		$view               = $context->view;
		$view_id            = (int) $view->ID;
		$render_instance_id = $this->get_render_instance_id( $context );
		$cache_key          = $view_id . ':' . $render_instance_id;

		if ( ! isset( $this->template_data_cache[ $cache_key ] ) ) {
			$page_count           = $this->get_page_entry_count( $context );
			$total_count          = $this->get_total_entry_count( $context );
			$actions              = $this->get_template_actions( $view );
			$background_available = Config::is_background_processing_enabled( $view ) && $this->has_background_enabled_action( $view, $actions );
			$select_all           = Config::is_cross_page_selection_enabled( $view )
				&& $total_count > $page_count
				&& ( $total_count <= Config::get_max_entry_ids( $view ) || $background_available );
				$job_active           = $this->background_status->is_active( $view, $render_instance_id );
			$clear_selection      = $this->flash_messages->should_display( $view, $render_instance_id )
				|| $this->background_status->should_clear_selection( $view, $render_instance_id );

			$this->template_data_cache[ $cache_key ] = [
				'actions'                    => $actions,
				'action_select_id'           => 'gv-bulk-action-' . $render_instance_id,
				'background_job_active'      => $job_active,
				'can_select_all'             => $select_all,
				'clear_selection'            => $clear_selection,
				'form_action'                => Config::get_form_action_url(),
				'form_id'                    => 'gv-bulk-actions-form-' . $render_instance_id,
				'nonce'                      => wp_create_nonce( Config::get_nonce_action( $view ) ),
				'page_count'                 => $page_count,
				'post_action'                => Config::POST_ACTION,
				'post_entries'               => Config::POST_ENTRIES,
				'post_excluded'              => Config::POST_EXCLUDED,
				'post_nonce'                 => Config::POST_NONCE,
				'post_select_all'            => Config::POST_SELECT_ALL,
				'post_show_selected'         => Config::POST_SHOW_SELECTED,
				'post_render_instance'       => Config::POST_RENDER_INSTANCE,
				'is_selection_mode'          => $this->selection_mode->is_active( $view ),
				'post_view_id'               => Config::POST_VIEW_ID,
				'selection_behavior'         => Config::get_selection_behavior( $view ),
				'selection_summary_position' => $this->get_selection_summary_position( $context ),
				'selection_ttl'              => Config::get_selection_ttl( $view ),
				'render_instance_id'         => $render_instance_id,
				'show_all_url'               => $this->selection_mode->get_show_all_url(),
				'show_selected_enabled'      => Config::is_show_selected_enabled( $view ),
					'storage_key'                => Config::get_storage_key( $view, $render_instance_id ),
				'total_count'                => $total_count,
				'view'                       => $view,
				'view_id'                    => $view_id,
			];
		}

		$data = $this->template_data_cache[ $cache_key ];

		$data = array_merge( $data, $extra );

		/**
		 * Filters frontend bulk action template data.
		 *
		 * @since 3.0.0
		 *
		 * @param array            $data          Template data.
		 * @param int              $view_id       View ID.
		 * @param \GV\View         $view          View object.
		 * @param TemplateContext  $context       Template context.
		 * @param string           $template_slug Template slug.
		 */
		return apply_filters( 'gk/gravityview/bulk-actions/template-data', $data, $view_id, $view, $context, $template_slug );
	}

	/**
	 * Returns a per-render identifier for DOM IDs.
	 *
	 * The same View can be embedded more than once on a page, so IDs cannot be
	 * based on the View ID alone. The table template declares a bulk-action
	 * render instance property so the toolbar, header, body, and summary contexts
	 * for one render share an ID without a static object registry retaining the
	 * template after the render completes.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return string
	 */
	private function get_render_instance_id( TemplateContext $context ) {
		$view_id  = (int) $context->view->ID;
		$template = is_object( $context->template ) ? $context->template : null;
		$property = self::RENDER_INSTANCE_PROPERTY;

		if ( $template && property_exists( $template, $property ) ) {
			if ( empty( $template->{$property} ) ) {
				$template->{$property} = $this->create_render_instance_id( $view_id );
			}

			return sanitize_key( (string) $template->{$property} );
		}

		// Bulk actions are gated to table templates, which declare the property
		// above. This defensive branch avoids retaining template objects if an
		// unexpected context reaches the renderer.
		return $this->create_render_instance_id( $view_id );
	}

	/**
	 * Creates a unique render instance ID for a View in the current request.
	 *
	 * @since 3.0.0
	 *
	 * @param int $view_id View ID.
	 *
	 * @return string
	 */
	private function create_render_instance_id( $view_id ) {
		self::$render_instance_counts[ $view_id ] = 1 + (int) ( self::$render_instance_counts[ $view_id ] ?? 0 );

		return $view_id . '-' . self::$render_instance_counts[ $view_id ];
	}

	/**
	 * Returns the selection summary placement.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return string
	 */
	private function get_selection_summary_position( TemplateContext $context ) {
		$view             = $context->view;
		$settings         = $this->registry->get_widget_settings( $view );
		$default_position = 'table_body_before';

		if ( $settings ) {
			$default_position = $settings->get( 'bulk_actions_summary_position', $default_position );
		}

		if ( ! in_array( $default_position, [ 'toolbar', 'table_body_before', 'none' ], true ) ) {
			$default_position = 'table_body_before';
		}

		/**
		 * Filters where selected-entry status and selection links render.
		 *
		 * Supported values: toolbar, table_body_before, none.
		 *
		 * @since 3.0.0
		 *
		 * @param string          $position Selection summary position.
		 * @param int             $view_id  View ID.
		 * @param \GV\View        $view     View object.
		 * @param TemplateContext $context  Template context.
		 */
		$position = apply_filters( 'gk/gravityview/bulk-actions/selection-summary-position', $default_position, (int) $view->ID, $view, $context );

		return in_array( $position, [ 'toolbar', 'table_body_before', 'none' ], true ) ? $position : $default_position;
	}

	/**
	 * Returns the checkbox column placement.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return string
	 */
	private function get_checkbox_column_position( TemplateContext $context ) {
		$view             = $context->view;
		$settings         = $this->registry->get_widget_settings( $view );
		$default_position = 'first';

		if ( $settings ) {
			$default_position = $settings->get( 'bulk_actions_checkbox_position', $default_position );
		}

		if ( ! in_array( $default_position, [ 'first', 'last' ], true ) ) {
			$default_position = 'first';
		}

		/**
		 * Filters where the selection checkbox column renders.
		 *
		 * Supported values: first, last.
		 *
		 * @since 3.0.0
		 *
		 * @param string          $position Checkbox column position.
		 * @param int             $view_id  View ID.
		 * @param \GV\View        $view     View object.
		 * @param TemplateContext $context  Template context.
		 */
		$position = apply_filters( 'gk/gravityview/bulk-actions/checkbox-column-position', $default_position, (int) $view->ID, $view, $context );

		return in_array( $position, [ 'first', 'last' ], true ) ? $position : $default_position;
	}

	/**
	 * Returns the table column count including hooked columns.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return int
	 */
	private function get_table_column_count( TemplateContext $context ) {
		if ( ! $context->fields ) {
			return 1;
		}

		$count = $context->fields->by_position( 'directory_table-columns' )->by_visible( $context->view )->count();

		/**
		 * Filters the number of table columns used for colspan calculations.
		 *
		 * @since 3.0.0
		 *
		 * @param int             $count   Number of visible table columns.
		 * @param TemplateContext $context The template context.
		 */
		return (int) apply_filters( 'gravityview/template/table/columns/count', $count, $context );
	}

	/**
	 * Returns memoized available actions for a View.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view View.
	 *
	 * @return array
	 */
	private function get_available_actions( $view ) {
		$key = (int) $view->ID;

		if ( ! isset( $this->available_actions[ $key ] ) ) {
			$this->available_actions[ $key ] = $this->registry->get_available_actions( $view );
		}

		return $this->available_actions[ $key ];
	}

	/**
	 * Returns actions prepared for template output.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\View $view View.
	 *
	 * @return array
	 */
	private function get_template_actions( $view ) {
		$actions = $this->get_available_actions( $view );

		foreach ( $actions as $key => $action ) {
			$actions[ $key ]['_confirmation']                 = $this->get_confirmation_config( $action );
			$actions[ $key ]['_typed_confirmation_enabled']   = Config::action_uses_typed_confirmation( $key, $action, $view );
			$actions[ $key ]['_typed_confirmation_threshold'] = Config::get_typed_confirmation_threshold( $view, $key );
			$actions[ $key ]['_frontend_data']                = $this->get_action_frontend_data( $key, $action, $view );
		}

		return $actions;
	}

	/**
	 * Returns action-specific frontend data.
	 *
	 * @since 3.0.0
	 *
	 * @param string $action_key Action key.
	 * @param array  $action     Action config.
	 * @param View   $view       View.
	 *
	 * @return array
	 */
	private function get_action_frontend_data( $action_key, array $action, View $view ) {
		if ( empty( $action['frontend_data_callback'] ) || ! is_callable( $action['frontend_data_callback'] ) ) {
			return [];
		}

		$data = call_user_func( $action['frontend_data_callback'], $view, $action, $action_key );

		return is_array( $data ) ? $data : [];
	}

	/**
	 * Returns whether any rendered action is enabled for background processing.
	 *
	 * @since 3.0.0
	 *
	 * @param View  $view    View.
	 * @param array $actions Action configs.
	 *
	 * @return bool
	 */
	private function has_background_enabled_action( View $view, array $actions ) {
		foreach ( $actions as $action_key => $action ) {
			if ( Config::is_action_background_processing_enabled( $action_key, $action, $view ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns normalized action confirmation configuration.
	 *
	 * @since 3.0.0
	 *
	 * @param array $action Action configuration.
	 *
	 * @return array
	 */
	private function get_confirmation_config( array $action ) {
		$confirmation = $action['confirmation'] ?? [];

		if ( is_string( $confirmation ) ) {
			$confirmation = [
				'enabled' => '' !== $confirmation,
				'message' => $confirmation,
			];
		} elseif ( is_bool( $confirmation ) ) {
			$confirmation = [
				'enabled' => $confirmation,
			];
		} elseif ( ! is_array( $confirmation ) ) {
			$confirmation = [];
		}

		$confirmation = wp_parse_args(
			$confirmation,
			[
				'enabled'               => false,
				'title'                 => '',
				'title_singular'        => '',
				'message'               => '',
				'message_singular'      => '',
				'action_label'          => '',
				'action_label_singular' => '',
			]
		);

		$confirmation['enabled'] = (bool) $confirmation['enabled'];

		return $confirmation;
	}

	/**
	 * Returns the number of entries on the current page.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return int
	 */
	private function get_page_entry_count( TemplateContext $context ) {
		return $context->entries ? (int) $context->entries->count() : 0;
	}

	/**
	 * Returns the total number of entries available in the current View request.
	 *
	 * @since 3.0.0
	 *
	 * @param TemplateContext $context Template context.
	 *
	 * @return int
	 */
	private function get_total_entry_count( TemplateContext $context ) {
		if ( ! $context->entries ) {
			return 0;
		}

		return (int) max( $context->entries->count(), $context->entries->total() );
	}
}
