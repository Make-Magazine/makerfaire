<?php
/**
 * GravityView Extension -- DataTables -- Per-field column control
 *
 * @since     3.11.0
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 *
 * @package   GravityView
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Per-field control over pinned columns and Responsive collapsing.
 *
 * Both settings are per field, stored in the field's own configuration. FixedColumns,
 * however, counts pinned columns inward from the table edges, so per-field pins have to
 * collapse into a left count and a right count.
 *
 * @since 3.12.0
 */
class GV_DataTables_Column_Control {

	const PIN_SETTING = 'dt_pin_column';

	const RESPONSIVE_SETTING = 'dt_responsive_visibility';

	/**
	 * DataTables Responsive priorities. Lower collapses last.
	 */
	const PRIORITY_HIGH = 1;
	const PRIORITY_LOW  = 20000;

	/**
	 * Marks a column visible at every Responsive breakpoint.
	 *
	 * The `never` class does the opposite: Responsive groups it with `none`, which keeps
	 * the column out of the table entirely.
	 */
	const CLASS_ALWAYS_VISIBLE = 'all';

	/**
	 * Appended to columns the config adds for sorting, which have no field behind them.
	 */
	const HIDDEN_SORT_CLASS = 'gv-hidden-sort-column';

	/**
	 * The field position that becomes the table's columns.
	 */
	const FIELD_POSITION = 'directory_table-columns';

	public function __construct() {
		add_filter( 'gk/gravityview/template/options', array( $this, 'apply_field_options' ), 10, 8 );
		add_filter( 'gravityview_datatables_js_options', array( $this, 'apply_to_config' ), 20, 4 );
	}

	/**
	 * Adapter for `gk/gravityview/template/options`, which replaces the deprecated
	 * `gravityview_template_field_options` this class targeted before core 2.55. The two
	 * hooks are not a drop-in swap: this one carries every field AND widget through the same
	 * filter (distinguished by `$field_type`) and its parameters are in a different order.
	 *
	 * @since 3.12.0
	 *
	 * @param array  $options     Existing field/widget options.
	 * @param string $field_type  `field` or `widget`; only `field` carries directory columns.
	 * @param string $template_id The View template.
	 * @param string $context     Where the field is rendered.
	 * @param bool   $grouped     Unused here; passed through untouched.
	 * @param int    $form_id     The form ID.
	 * @param string $field_id    The field ID.
	 * @param string $input_type  The field input type.
	 *
	 * @return array
	 */
	public function apply_field_options( $options, $field_type, $template_id, $context, $grouped, $form_id, $field_id, $input_type ) {
		if ( 'field' !== $field_type ) {
			return $options;
		}

		return $this->field_options( $options, $template_id, $field_id, $context, $input_type, $form_id );
	}

	/**
	 * Loads the FixedColumns assets for a View whose pins come from field settings.
	 *
	 * The FixedHeader/FixedColumns extension enqueues them only when the View-level
	 * checkbox is on, so without this a pinned config asks for a library that is not on the
	 * page and nothing is pinned. The `gravityview_datatables_scripts_styles` action cannot
	 * carry this decision: its only call site passes empty config and View arrays.
	 *
	 * @since 3.12.0
	 *
	 * @return void
	 */
	private function enqueue_fixed_columns() {
		$script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$script_path  = plugins_url( 'assets/js/third-party/datatables/', GV_DT_FILE );
		$style_path   = plugins_url( 'assets/css/third-party/datatables/', GV_DT_FILE );

		wp_enqueue_script(
			'gv-dt-fixedcolumns',
			apply_filters(
				'gravityview_dt_fixedcolumns_script_src',
				$script_path . 'dataTables.fixedColumns' . $script_debug . '.js'
			),
			array( 'jquery', 'gv-datatables' ),
			GV_Extension_DataTables::version,
			true
		);

		wp_enqueue_style(
			'gv-dt_fixedcolumns_style',
			apply_filters(
				'gravityview_dt_fixedcolumns_style_src',
				$style_path . 'fixedColumns.dataTables' . $script_debug . '.css'
			),
			array( 'gravityview_style_datatables_table' ),
			GV_Extension_DataTables::version
		);
	}

	/**
	 * Adds the two field settings to DataTables table columns.
	 *
	 * @since 3.12.0
	 *
	 * @param array  $options     Existing field options.
	 * @param string $template_id The View template.
	 * @param string $field_id    The field ID.
	 * @param string $context     Where the field is rendered.
	 * @param string $input_type  The field input type.
	 * @param int    $form_id     The form ID.
	 *
	 * @return array
	 */
	public function field_options( $options, $template_id, $field_id, $context, $input_type = '', $form_id = 0 ) {
		$is_datatables_directory = 'datatables_table' === $template_id && 'directory' === $context;

		if ( ! $is_datatables_directory ) {
			return $options;
		}

		$options[ self::PIN_SETTING ] = array(
			'type'     => 'select',
			'label'    => __( 'Pin Column', 'gv-datatables' ),
			'desc'     => __( 'Pinned columns stay visible while scrolling the table horizontally. Pinned columns must sit next to the left or right edge of the table.', 'gv-datatables' ),
			'value'    => '',
			'group'    => 'display',
			'priority' => 210,
			'options'  => array(
				''      => __( 'Not pinned', 'gv-datatables' ),
				'left'  => __( 'Pin to left edge', 'gv-datatables' ),
				'right' => __( 'Pin to right edge', 'gv-datatables' ),
			),
		);

		$options[ self::RESPONSIVE_SETTING ] = array(
			'type'     => 'select',
			'label'    => __( 'Responsive Visibility', 'gv-datatables' ),
			'desc'     => __( 'Controls which columns are hidden first when Responsive mode shrinks the table.', 'gv-datatables' ),
			'value'    => '',
			'group'    => 'display',
			'priority' => 220,
			'options'  => array(
				''      => __( 'Automatic', 'gv-datatables' ),
				'high'  => __( 'Collapse last', 'gv-datatables' ),
				'low'   => __( 'Collapse first', 'gv-datatables' ),
				'never' => __( 'Never collapse', 'gv-datatables' ),
			),
		);

		return $options;
	}

	/**
	 * Applies the stored field settings to a built DataTables config.
	 *
	 * @since 3.12.0
	 *
	 * @param array   $dt_config The DataTables configuration.
	 * @param int     $view_id   The View ID.
	 * @param WP_Post $post      The post the View renders in.
	 * @param object  $object    The data class instance.
	 *
	 * @return array
	 */
	public function apply_to_config( $dt_config, $view_id, $post = null, $object = null ) {
		$view = \GV\View::by_id( $view_id );

		if ( ! $view || empty( $dt_config['columns'] ) ) {
			return $dt_config;
		}

		$dt_settings          = get_post_meta( $view_id, '_gravityview_datatables_settings', true );
		$responsive_enabled   = ! empty( $dt_settings['responsive'] );
		$legacy_fixed_columns = ! empty( $dt_settings['fixedcolumns'] );

		$dt_config = self::build(
			$dt_config,
			self::settings_from_view( $view ),
			$responsive_enabled,
			$legacy_fixed_columns
		);

		$counts       = \GV\Utils::get( $dt_config, 'fixedColumns', array() );
		$has_pins     = is_array( $counts ) && ( ! empty( $counts['left'] ) || ! empty( $counts['right'] ) );
		$checkbox_off = ! $legacy_fixed_columns;

		// Only field-driven pins need this; the checkbox path enqueues through the
		// FixedHeader/FixedColumns extension already.
		if ( $has_pins && $checkbox_off ) {
			$this->enqueue_fixed_columns();
		}

		return $dt_config;
	}

	/**
	 * Reads the per-field settings in visible column order.
	 *
	 * @since 3.12.0
	 *
	 * @param \GV\View $view The View.
	 *
	 * @return array<int,array{pin:string,responsive:string}>
	 */
	private static function settings_from_view( $view ) {
		$configuration = $view->fields->by_position( self::FIELD_POSITION )->by_visible()->as_configuration();

		// as_configuration() keys by position, so the field configs are one level in.
		$fields   = \GV\Utils::get( $configuration, self::FIELD_POSITION, array() );
		$settings = array();

		foreach ( (array) $fields as $field_config ) {
			$settings[] = array(
				'pin'        => (string) \GV\Utils::get( $field_config, self::PIN_SETTING, '' ),
				'responsive' => (string) \GV\Utils::get( $field_config, self::RESPONSIVE_SETTING, '' ),
			);
		}

		return $settings;
	}

	/**
	 * Maps per-field settings onto the DataTables configuration.
	 *
	 * Kept static and free of View lookups so the mapping rules are testable on their own.
	 *
	 * @since 3.12.0
	 *
	 * @param array $dt_config            The DataTables configuration.
	 * @param array $field_settings       Per-column settings, in visible column order.
	 * @param bool  $responsive_enabled   Whether the View enables Responsive mode.
	 * @param bool  $legacy_fixed_columns Whether the View-level FixedColumns checkbox is on.
	 *
	 * @return array
	 */
	public static function build( array $dt_config, array $field_settings, $responsive_enabled, $legacy_fixed_columns ) {
		$field_column_indexes = self::field_column_indexes( $dt_config['columns'] );
		$pins                 = array();
		$responsive           = array();

		foreach ( $field_column_indexes as $position => $column_index ) {
			$setting = isset( $field_settings[ $position ] ) ? $field_settings[ $position ] : array();

			$pins[ $column_index ]       = (string) \GV\Utils::get( $setting, 'pin', '' );
			$responsive[ $column_index ] = (string) \GV\Utils::get( $setting, 'responsive', '' );
		}

		// Responsive and FixedColumns both reposition columns and conflict in DataTables 1.x.
		// A pin then means "do not collapse this field" instead of freezing it.
		if ( $responsive_enabled ) {
			foreach ( $pins as $column_index => $pin ) {
				$is_pinned         = '' !== $pin;
				$has_own_setting   = '' !== $responsive[ $column_index ];

				if ( $is_pinned && ! $has_own_setting ) {
					$responsive[ $column_index ] = 'high';
				}
			}

			$dt_config = self::apply_responsive( $dt_config, $responsive );

			return $dt_config;
		}

		$dt_config = self::apply_responsive( $dt_config, $responsive );
		$counts    = self::pin_counts( $pins, count( $dt_config['columns'] ) );
		$has_pins  = $counts['left'] > 0 || $counts['right'] > 0;

		if ( ! $has_pins && $legacy_fixed_columns ) {
			$counts   = array( 'left' => 1, 'right' => 0 );
			$has_pins = true;
		}

		if ( $has_pins ) {
			$dt_config['fixedColumns'] = $counts;
			$dt_config['scrollX']      = true;
		}

		return $dt_config;
	}

	/**
	 * Column indexes that correspond to configured fields, in order.
	 *
	 * Shared with GV_Extension_DataTables_RowGroup, which resolves its own per-field
	 * setting (the grouping field) to a column index the same way pins and Responsive
	 * Visibility do.
	 *
	 * @param array $columns The built column configs.
	 *
	 * @return int[]
	 */
	public static function field_column_indexes( array $columns ) {
		$indexes = array();

		foreach ( $columns as $index => $column ) {
			$class_names   = (string) \GV\Utils::get( $column, 'className', '' );
			$is_sort_only  = false !== strpos( $class_names, self::HIDDEN_SORT_CLASS );

			if ( $is_sort_only ) {
				continue;
			}

			$indexes[] = $index;
		}

		return $indexes;
	}

	/**
	 * Collapses per-field pins into the edge counts FixedColumns expects.
	 *
	 * A pin that is not part of a contiguous run touching an edge is ignored: honoring it
	 * would mean freezing the columns between it and the edge, which the author never chose.
	 *
	 * @since 3.12.0
	 *
	 * @param array<int,string> $pins           Pin values keyed by column index, in column order.
	 * @param int|null          $total_columns  Total DataTables column count, including any
	 *                                          hidden-sort columns that carry no pin setting
	 *                                          of their own. Defaults to `count( $pins )` for
	 *                                          callers (and tests) that pin every column that
	 *                                          exists.
	 *
	 * @return array{left:int,right:int}
	 */
	public static function pin_counts( array $pins, $total_columns = null ) {
		// Collapsed to visible-column space on purpose, which is the space FixedColumns
		// counts in: _addStyles() skips an invisible column and compensates
		// (`i + rightInvisibles >= numCols - this.c.right`), so a hidden-sort column past
		// the last field column does not consume a right slot. Verified in a browser --
		// {@see tests/JS/specs/fixedcolumns-hidden-column-space.spec.js}.
		$ordered = array_values( $pins );
		$left    = 0;
		$right   = 0;

		foreach ( $ordered as $pin ) {
			if ( 'left' !== $pin ) {
				break;
			}

			++$left;
		}

		foreach ( array_reverse( $ordered ) as $pin ) {
			if ( 'right' !== $pin ) {
				break;
			}

			++$right;
		}

		// Pinning every column leaves nothing to scroll under the sticky columns; the
		// bundled FixedColumns 4.2.2 applies `left`/`right` with no range validation of its
		// own (validation arrived in 4.3), so keep at least one column scrollable. The cap is
		// against the *total* column count, not just the pinnable ones: a hidden-sort column
		// (no pin setting, see field_column_indexes()) still leaves room to scroll even when
		// every real field column is pinned.
		$max_pinned = max( 0, ( null === $total_columns ? count( $ordered ) : $total_columns ) - 1 );

		if ( $left + $right > $max_pinned ) {
			$left  = min( $left, $max_pinned );
			$right = max( 0, min( $right, $max_pinned - $left ) );
		}

		return array( 'left' => $left, 'right' => $right );
	}

	/**
	 * Writes Responsive priorities and classes onto the columns.
	 *
	 * @param array             $dt_config  The DataTables configuration.
	 * @param array<int,string> $responsive Responsive values keyed by column index.
	 *
	 * @return array
	 */
	private static function apply_responsive( array $dt_config, array $responsive ) {
		foreach ( $responsive as $column_index => $value ) {
			if ( ! isset( $dt_config['columns'][ $column_index ] ) ) {
				continue;
			}

			if ( 'high' === $value ) {
				$dt_config['columns'][ $column_index ]['responsivePriority'] = self::PRIORITY_HIGH;
			}

			if ( 'low' === $value ) {
				$dt_config['columns'][ $column_index ]['responsivePriority'] = self::PRIORITY_LOW;
			}

			if ( 'never' === $value ) {
				$existing_classes = (string) \GV\Utils::get( $dt_config['columns'][ $column_index ], 'className', '' );

				$dt_config['columns'][ $column_index ]['className'] = trim( $existing_classes . ' ' . self::CLASS_ALWAYS_VISIBLE );
			}
		}

		return $dt_config;
	}
}

new GV_DataTables_Column_Control();
