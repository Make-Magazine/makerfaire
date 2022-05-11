<?php
/**
 * Auto-refresh DataTables results based on View settings
 *
 * @since 2.6
 */

class GV_Extension_DataTables_Auto_Update extends GV_DataTables_Extension {

	protected $settings_key = 'auto_update';

	const DEFAULT_INTERVAL_MINUTES = 5;

	function __construct() {
		parent::__construct();

	}

	function defaults( $settings ) {

		$settings['auto_update']     = false;
		$settings['update_interval'] = self::DEFAULT_INTERVAL_MINUTES;

		return $settings;
	}

	/**
	 * Prints the setting for Auto Update
	 *
	 * @param array $ds DataTables extension settings
	 *
	 * @return void
	 */
	function settings_row( $ds ) {

		$update_interval = rgar( $ds, 'update_interval', self::DEFAULT_INTERVAL_MINUTES );
		$update_interval = floatval( $update_interval );

		?>
        <table class="form-table">
            <caption><?php esc_html_e( 'Auto-Update', 'gv-datatables' ); ?></caption>
            <tr valign="top">
                <td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[auto_update]',
						array(
							'label'   => __( 'Enable Auto-Update', 'gv-datatables' ),
							'type'    => 'checkbox',
							'value'   => 1,
							'tooltip' => true,
							'desc'    => esc_html__( 'Automatically refresh the table every number of minutes without refreshing the page.', 'gv-datatables' ),
							'article' => array(
								'id'  => '61c2875e28e2785c351f4eae',
								'url' => 'https://docs.gravityview.co/article/821-enable-auto-update-datatables-setting',
							),
						),
						rgar( $ds, 'auto_update', 0 )
					);
					?>
                </td>
            </tr>
            <tr valign="top">
                <td scope="row">
                    <label for="gravityview_dt_update_interval"><?php esc_html_e( 'Auto-Update Interval (In Minutes)', 'gv-datatables' ); ?></label>
                </td>
                <td>
                    <input name="datatables_settings[update_interval]"
                           placeholder="<?php echo esc_attr( self::DEFAULT_INTERVAL_MINUTES ); ?>"
                           id="gravityview_dt_update_interval" type="number" min="0" step="any"
                           value="<?php echo esc_attr( $update_interval ); ?>" class="small-text"/>
                </td>
            </tr>
        </table>
		<?php
	}


	/**
	 * Add config of update interval to js settings. Only runs when `auto_update` is enabled.
	 *
	 * @inheritDoc
	 * @return array DataTables configuration array with `updateInterval` key set to value as microseconds.
	 */
	function add_config( $dt_config, $view_id, $post ) {

		$update_interval = $this->get_setting( $view_id, 'update_interval', self::DEFAULT_INTERVAL_MINUTES );

		// If it's already been overridden upstream, use the value. Otherwise, use the View setting.
		$update_interval = empty( $dt_config['update_interval'] ) ? $update_interval : $dt_config['update_interval'];

		if ( empty( $update_interval ) ) {
			return $dt_config;
		}

		// Convert minutes to microseconds.
		$dt_config['updateInterval'] = floatval( $update_interval ) * 60 * 1000;

		gravityview()->log->debug( '[update_interval_add_config] Inserting Update Interval config. Data:', array( 'data' => $dt_config ) );

		return $dt_config;
	}


}

new GV_Extension_DataTables_Auto_Update();
