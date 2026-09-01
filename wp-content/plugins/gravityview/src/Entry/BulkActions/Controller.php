<?php
/**
 * Frontend bulk action controller.
 *
 * @package GravityKit\GravityView\Entry\BulkActions
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Entry\BulkActions;

/**
 * Wires frontend bulk action services into WordPress hooks.
 *
 * @since 3.0.0
 */
final class Controller {
	/**
	 * @since 3.0.0
	 * @var RequestHandler
	 */
	private $request_handler;

	/**
	 * @since 3.0.0
	 * @var Renderer
	 */
	private $renderer;

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
	 */
	public function __construct() {
		$registry          = new Registry();
		$eligibility       = new ViewEligibility( $registry );
		$flash_messages    = new FlashMessages();
		$entry_resolver    = new EntryResolver();
		$view_lock         = new ViewActionLock();
		$background_status = new BackgroundStatus( null, $view_lock );

		$this->selection_mode    = new SelectionMode( $entry_resolver, $eligibility, $flash_messages );
		$this->background_status = $background_status;
		$this->request_handler   = new RequestHandler(
			$registry,
			$eligibility,
			$entry_resolver,
			$flash_messages,
			$this->selection_mode,
			$background_status,
			$view_lock
		);
		$this->renderer          = new Renderer( $registry, $eligibility, $flash_messages, $this->selection_mode, $background_status );

		add_action( 'template_redirect', [ $this, 'process_background_job_request' ], 7 );
		add_action( 'template_redirect', [ $this, 'process_selection_mode_request' ], 8 );
		add_action( 'template_redirect', [ $this, 'process_request' ], 9 );
		add_action( 'wp_ajax_' . Config::AJAX_VALIDATE_ACTION, [ $this, 'process_action_validation_request' ] );
		add_action( 'wp_ajax_' . Config::AJAX_STATUS_ACTION, [ $this, 'process_background_status_request' ] );
		add_action( 'wp_ajax_' . Config::AJAX_SETTING_OPTIONS_ACTION, [ $this, 'process_setting_options_request' ] );
		add_action( 'gravityview/template/table/columns/before', [ $this, 'render_header_cell' ] );
		add_action( 'gravityview/template/table/columns/after', [ $this, 'render_header_cell_after' ] );
		add_action( 'gravityview/template/table/cells/before', [ $this, 'render_entry_cell' ] );
		add_action( 'gravityview/template/table/cells/after', [ $this, 'render_entry_cell_after' ] );
		add_action( 'gravityview/template/table/body/before', [ $this, 'render_table_selection_summary' ] );
		add_filter( 'gravityview/template/table/columns/count', [ $this, 'filter_column_count' ], 10, 2 );
	}

	/**
	 * Processes a submitted bulk action.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_request() {
		$this->request_handler->process_request();
	}

	/**
	 * Processes a Show selected entries request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_selection_mode_request() {
		$this->selection_mode->process_request();
	}

	/**
	 * Processes a background job status action.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_background_job_request() {
		$this->background_status->process_cancel_request();
		$this->background_status->process_dismiss_request();
	}

	/**
	 * Processes an AJAX background job status request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_background_status_request() {
		$this->background_status->process_ajax_status_request();
	}

	/**
	 * Processes an AJAX action input validation request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_action_validation_request() {
		$this->request_handler->process_ajax_validation_request();
	}

	/**
	 * Processes an AJAX action setting options request.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function process_setting_options_request() {
		$this->request_handler->process_ajax_setting_options_request();
	}

	/**
	 * Renders the bulk action toolbar.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_toolbar( $context ) {
		$this->renderer->render_toolbar( $context );
	}

	/**
	 * Renders the selection summary inside the table body.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_table_selection_summary( $context ) {
		$this->renderer->render_table_selection_summary( $context );
	}

	/**
	 * Renders the select-all column header.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_header_cell( $context ) {
		$this->renderer->render_header_cell( $context );
	}

	/**
	 * Renders the select-all column header after table columns.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_header_cell_after( $context ) {
		$this->renderer->render_header_cell_after( $context );
	}

	/**
	 * Renders an entry row checkbox.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_entry_cell( $context ) {
		$this->renderer->render_entry_cell( $context );
	}

	/**
	 * Renders an entry row checkbox after table cells.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $context Template context.
	 *
	 * @return void
	 */
	public function render_entry_cell_after( $context ) {
		$this->renderer->render_entry_cell_after( $context );
	}

	/**
	 * Adds the checkbox column to no-results colspan calculations.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $count   Column count.
	 * @param mixed $context Template context.
	 *
	 * @return int
	 */
	public function filter_column_count( $count, $context ) {
		return $this->renderer->filter_column_count( $count, $context );
	}
}
