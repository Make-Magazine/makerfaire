<?php
/**
 * Adds a View editor setting controlling how strictly Percent Widths are applied.
 *
 * @since 3.13.0
 */

class GV_Extension_DataTables_Column_Widths extends GV_DataTables_Extension {
	protected $settings_key = 'percent_width_mode';

	/**
	 * Percentages give way to the values in the column when the two cannot both be had.
	 *
	 * @since 3.13.0
	 */
	const MODE_ADAPT = 'adapt';

	/**
	 * Percentages are kept whatever they do to the values in the column.
	 *
	 * @since 3.13.0
	 */
	const MODE_EXACT = 'exact';

	/**
	 * Table class that switches a View to exact percentages.
	 *
	 * @since 3.13.0
	 */
	const EXACT_CLASS = 'gv-dt-exact-widths';

	/**
	 * @inheritDoc
	 *
	 * @since 3.13.0
	 */
	function defaults( $settings ) {
		$settings[ $this->settings_key ] = self::MODE_ADAPT;

		return $settings;
	}

	/**
	 * Whether the View keeps its percentages even where they starve a column.
	 *
	 * @since 3.13.0
	 *
	 * @param int $view_id The View being rendered.
	 *
	 * @return bool
	 */
	public static function is_exact( $view_id ) {
		$settings = get_post_meta( $view_id, '_gravityview_datatables_settings', true );

		return self::MODE_EXACT === \GV\Utils::get( (array) $settings, 'percent_width_mode', self::MODE_ADAPT );
	}

	/**
	 * Prints the setting.
	 *
	 * @since 3.13.0
	 *
	 * @param array $ds DataTables extension settings.
	 *
	 * @return void
	 */
	function settings_row( $ds ) {
		$mode = rgar( $ds, $this->settings_key, self::MODE_ADAPT );

		?>
		<table class="form-table">
			<caption><?php esc_html_e( 'Column Widths', 'gv-datatables' ); ?></caption>
			<tr valign="top">
				<td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[' . $this->settings_key . ']',
						array(
							'label'   => esc_html__( 'When a column is too narrow for its content', 'gv-datatables' ),
							'type'    => 'radio',
							'value'   => $mode,
							'options' => array(
								self::MODE_ADAPT => esc_html__( 'Widen the table and scroll sideways', 'gv-datatables' ),
								self::MODE_EXACT => esc_html__( 'Keep the column at its set width', 'gv-datatables' ),
							),
							'tooltip' => true,
							'desc'    => esc_html__( 'Only applies to columns given a width: open a field\'s settings in Multiple Entries and set "Percent Width" under Display. On a phone that width can work out narrower than the values in the column — widening the table keeps them readable, keeping the width makes them overlap the next column.', 'gv-datatables' ),
						),
						$mode
					);
					?>
				</td>
			</tr>
		</table>
		<?php
	}
}

new GV_Extension_DataTables_Column_Widths();
