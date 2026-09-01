<?php
/**
 * Chained Select field type.
 *
 * PSR-4 migration of the legacy GravityView_Field_Chained_Select class.
 *
 * Adds a "Display style" setting toggling Gravity Forms' two native outputs:
 * "Side by side" (a `<table>`, what GF emits when `is_entry_detail()` is false)
 * and "Stacked" (`<div>`s, when true). The toggle registers a high-priority
 * `gform_is_entry_detail` filter that returns true only during our own scoped
 * re-render, so global GF context is left untouched.
 *
 * DataTables Multiple Entries always forces Stacked: responsive mode wraps cell
 * data in `<span class="dtr-data">`, and a `<table>` inside a `<span>` is invalid
 * HTML (the browser hoists it out and the cell fragments). Single Entry is exempt
 * — it isn't subject to responsive collapsing, so the chosen style is honored.
 *
 * @package GravityKit\GravityView\Field\Types
 * @license GPL2+
 * @since 3.0.0
 */

namespace GravityKit\GravityView\Field\Types;

use GravityKit\GravityView\GravityForms\Compat as GF_Compat;

class ChainedSelect extends \GravityView_Field {

	/**
	 * The field type slug.
	 *
	 * @since 2.14
	 *
	 * @var string
	 */
	public $name = 'chainedselect';

	/**
	 * Whether the field is searchable in the Search Bar.
	 *
	 * @since 2.14
	 *
	 * @var bool
	 */
	public $is_searchable = true;

	/**
	 * Search operators supported by this field.
	 *
	 * @since 2.14
	 *
	 * @var string[]
	 */
	public $search_operators = [ 'is', 'isnot' ];

	/**
	 * The Gravity Forms field class this field maps to.
	 *
	 * @since 2.14
	 *
	 * @var string
	 */
	public $_gf_field_class_name = 'GF_Field_ChainedSelect'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore -- Inherited GravityView_Field property name.

	/**
	 * The Field Picker group this field belongs to.
	 *
	 * @since 2.14
	 *
	 * @var string
	 */
	public $group = 'advanced';

	/**
	 * The dashicon shown for this field in the Field Picker.
	 *
	 * @since 2.14
	 *
	 * @var string
	 */
	public $icon = 'dashicons-admin-links';

	/**
	 * Internal gate for the conditional `gform_is_entry_detail` filter.
	 *
	 * Set to true ONLY while we are inside our own scoped re-render of
	 * a Chained Selects value (see `maybe_render_stacked()`). The
	 * `maybe_force_entry_detail()` callback reads this so GF flips to
	 * the `<div>` output path during our re-render — and stays
	 * untouched during every other render the page may perform.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private static $force_stacked_render = false;

	/**
	 * Sets the field label and registers its render-time hooks.
	 *
	 * @since 2.14
	 * @since 3.0.0 Added the display hooks for stacked rendering.
	 */
	public function __construct() {
		$this->label = esc_html__( 'Chained Select', 'gk-gravityview' );

		// Scoped GF behaviour toggle. Hooked at priority 9999 so any
		// other late callbacks that genuinely want to override entry-
		// detail context get to do so first.
		add_filter( 'gform_is_entry_detail', [ $this, 'maybe_force_entry_detail' ], 9999 );

		// Modify the rendering to get the stacked output.
		add_filter( 'gravityview/template/field/chainedselect/output', [ $this, 'maybe_render_stacked' ], 5, 2 );

		parent::__construct();
	}

	/**
	 * Register the "Display style" radio in the Field Options drawer.
	 *
	 * Auto-wired via the parent constructor's
	 * `gravityview_template_chainedselect_options` filter (see
	 * `GravityViewField::__construct()` for the auto-wire).
	 *
	 * The setting is shown in every non-Edit-Entry context. We do NOT
	 * hide it server-side when the active template is DataTables: the
	 * Studio doesn't re-fetch field options when the customer changes
	 * the "View type" picker, so a server-side hide would lag the
	 * actual template state. Instead the description spells out that
	 * DataTables always uses Stacked; the render-time logic enforces
	 * that regardless of the radio value.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $field_options Existing field options.
	 * @param string $template_id   Current View template id.
	 * @param string $field_id      Field id.
	 * @param string $context       Render context: `directory` | `single` | `edit`.
	 * @param string $input_type    GF input type slug.
	 * @param int    $form_id       Source form id.
	 *
	 * @return array Modified field options.
	 */
	public function field_options( $field_options, $template_id, $field_id, $context, $input_type, $form_id ) {

		// Edit Entry context — GF renders the field as form inputs;
		// the display style doesn't apply.
		if ( 'edit' === $context ) {
			return $field_options;
		}

		$field_options['chained_select_display_style'] = [
			'type'    => 'radio',
			'class'   => 'vertical',
			'label'   => esc_html__( 'Display style', 'gk-gravityview' ),
			'value'   => 'side-by-side',
			'choices' => [
				'side-by-side' => esc_html__( 'Table (label and value in columns)', 'gk-gravityview' ),
				'stacked'      => esc_html__( 'Stacked (label above value)', 'gk-gravityview' ),
			],
			'desc'    => esc_html__(
				'Each chained dropdown saves a label and a value (for example, "Category: Finance"). Choose how those pairs appear in your View.',
				'gk-gravityview'
			),
			'group'   => 'display',
		];

		return $field_options;
	}

	/**
	 * Re-render the value with the Stacked output when:
	 *
	 * 1. The customer selected "Stacked" in the field settings, OR
	 * 2. The View is a DataTables layout (forced regardless of setting).
	 *
	 * The re-render is scoped via a static flag so the global
	 * `gform_is_entry_detail` filter only flips for our re-render —
	 * no other GF field renders on the page are affected.
	 *
	 * @since 3.0.0
	 *
	 * @param string               $output  The current (side-by-side) output.
	 * @param \GV\Template_Context $context The render context.
	 *
	 * @return string The original output or the re-rendered stacked output.
	 */
	public function maybe_render_stacked( $output, $context ) {
		if ( ! $context || ! isset( $context->field, $context->entry ) ) {
			return $output;
		}

		// A specific input of a Chained Selects field (for example `7.1`) holds a
		// single value, and the field template already produced exactly that
		// value. The re-render below operates on the WHOLE Gravity Forms field, so
		// applying it to one input would replace that single value with every
		// input's label/value pair. Leave single-input output untouched.
		$input_id = gravityview_get_input_id_from_id( $context->field->ID );
		if ( $input_id ) {
			return $output;
		}

		$field_settings = method_exists( $context->field, 'as_configuration' )
			? $context->field->as_configuration()
			: [];

		$display_style = isset( $field_settings['chained_select_display_style'] )
			? (string) $field_settings['chained_select_display_style']
			: 'side-by-side';

		// DataTables override is scoped to Multiple Entries (directory)
		// context only — Single Entry pages on DataTables aren't
		// subject to responsive cell collapsing, so the customer's
		// chosen display style is honored there.
		$datatables_force = $this->is_datatables_render_context( $context )
			&& $this->is_directory_render_context();

		$should_stack = ( 'stacked' === $display_style ) || $datatables_force;

		if ( ! $should_stack ) {
			return $output;
		}

		$gf_field = isset( $context->field->field ) ? $context->field->field : null;
		if ( ! $gf_field instanceof \GF_Field ) {
			return $output;
		}

		$value = $context->field->get_value( $context->view, $context->source, $context->entry );

		self::$force_stacked_render = true;
		try {
			$stacked_output = GF_Compat::get_field_display( $gf_field, $value, $context->entry->as_entry() );
		} finally {
			// `finally` so a Throwable from GF doesn't leave the global
			// `gform_is_entry_detail` filter stuck in the forced state.
			self::$force_stacked_render = false;
		}

		// `get_field_display()` returns false on some GF versions when
		// the field has no displayable value. Fall back to the original
		// output rather than silently emptying the cell.
		return ( false === $stacked_output || '' === $stacked_output ) ? $output : $stacked_output;
	}

	/**
	 * Force `is_entry_detail()` to return true ONLY while
	 * `$force_stacked_render` is set.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $is_entry_detail Current value.
	 *
	 * @return bool Possibly overridden value.
	 */
	public function maybe_force_entry_detail( $is_entry_detail ) {
		return self::$force_stacked_render ? true : $is_entry_detail;
	}

	/**
	 * Whether the supplied template id is a DataTables layout.
	 *
	 * The DataTables extension registers its template as
	 * `datatables_table`. We match on `datatables` so any future
	 * variants register under the same family.
	 *
	 * @since 3.0.0
	 *
	 * @param string $template_id Template id from the field-options filter.
	 *
	 * @return bool
	 */
	private function is_datatables_template_id( $template_id ) {
		return false !== strpos( (string) $template_id, 'datatables' );
	}

	/**
	 * Whether the active render is a Multiple Entries (directory)
	 * screen rather than a Single Entry screen.
	 *
	 * The DataTables override only forces Stacked when we're rendering
	 * the directory listing — Single Entry pages don't use DataTables
	 * responsive cell collapsing, so the customer's display-style
	 * choice is honored there.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	private function is_directory_render_context() {
		$gv      = function_exists( 'gravityview' ) ? gravityview() : null;
		$request = $gv && property_exists( $gv, 'request' ) ? $gv->request : null;

		if ( ! $request || ! method_exists( $request, 'is_entry' ) ) {
			// No request resolver — assume directory. The render-time
			// path is invoked from inside the template loop either way.
			return true;
		}

		// `request->is_entry()` returns the GV entry when on a Single
		// Entry page, or `false` otherwise.
		return false === $request->is_entry();
	}

	/**
	 * Whether the active render context is a DataTables View.
	 *
	 * Reads the template id from the view's settings — same source the
	 * field-options filter checks.
	 *
	 * @since 3.0.0
	 *
	 * @param \GV\Template_Context $context Render context.
	 *
	 * @return bool
	 */
	private function is_datatables_render_context( $context ) {
		if ( ! $context || empty( $context->view ) ) {
			return false;
		}

		$template = '';
		if ( ! empty( $context->view->settings ) && method_exists( $context->view->settings, 'get' ) ) {
			$template = (string) $context->view->settings->get( 'template', '' );
		}

		// Fallback: peek at the post meta directly when settings hasn't
		// surfaced a template yet (legacy template-resolution paths).
		if ( '' === $template && ! empty( $context->view->ID ) ) {
			$template = (string) get_post_meta( (int) $context->view->ID, '_gravityview_directory_template', true );
		}

		return $this->is_datatables_template_id( $template );
	}
}
