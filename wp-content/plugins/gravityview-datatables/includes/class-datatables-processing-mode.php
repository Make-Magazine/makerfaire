<?php
/**
 * Adds a View editor setting to enable client-side processing mode for DataTables.
 *
 * @since 2.6
 */

class GV_Extension_DataTables_Processing_Mode extends GV_DataTables_Extension {
	protected $settings_key = 'processing_mode';

	const DEFAULT_MODE = 'serverSide';

	function __construct() {
		add_filter( 'gravityview/datatables/output', [ $this, 'update_config_with_shadow_data' ], 10, 3 );

		parent::__construct();
	}

	function defaults( $settings ) {

		$settings['processing_mode'] = self::DEFAULT_MODE;

		return $settings;
	}

	/**
	 * Prints the setting.
	 *
	 * @since 3.3
	 *
	 * @param array $ds DataTables extension settings
	 *
	 * @return void
	 */
	function settings_row( $ds ) {
		$processing_mode = rgar( $ds, 'processing_mode', self::DEFAULT_MODE );

		?>
        <table class="form-table">
            <caption><?php esc_html_e( 'Data Processing Mode', 'gv-datatables' ); ?></caption>
            <tr valign="top">
                <td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option(
						'datatables_settings[' . $this->settings_key . ']',
						array(
							'label'   => esc_html__( 'Processing Mode', 'gv-datatables' ),
							'type'    => 'radio',
							'value'   => $processing_mode,
                            'options' => [
                                'serverSide' => esc_html__( 'Ajax (Server-side)', 'gv-datatables' ),
                                'clientSide' => esc_html__( 'Preloaded (Client-side)', 'gv-datatables' ),
                            ],
							'tooltip' => true,
							'desc'    => esc_html__( 'Server-side processing calls the website every time there is a change in search, sorting, or paging. Client-side pre-loads all the data so the View will take longer to load initially, but then navigating data will be instantaneous.', 'gv-datatables' ),
							'article' => array(
								'id'  => '64fa00a0dda35f3fc4a17dd0',
								'url' => 'https://docs.gravitykit.com/article/957-client-side-processing',
							),
						),
						rgar( $ds, $this->settings_key, 0 )
					);
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * {inheritdoc}
	 *
	 * @since 3.3
	 *
	 * @return array
	 */
	function add_config( $dt_config, $view_id, $post, $object ) {
		$processing_mode = $this->get_setting( $view_id, $this->settings_key, self::DEFAULT_MODE );

		if ( $post instanceof WP_Post && $processing_mode !== self::DEFAULT_MODE ) {
			$dt_config = $this->modify_config_for_client_side_processing( $dt_config, $view_id, $post, $object );

			gravityview()->log->debug( '[processing_mode_add_config] Updating DataTables config to use client-side.' );
		}

		return $dt_config;
	}

    /**
     * Modifies the DataTables configuration to use client-side processing mode.
     *
     * @since 3.3
     *
     * @param array   $dt_config The configuration for the current View.
     * @param int     $view_id   The ID of the View being configured.
     * @param WP_Post $post      Current View or post/page where View is embedded.
     * @param GV_Extension_DataTables_Data $object The current instance of the GV_Extension_DataTables_Data class.
     */
    private function modify_config_for_client_side_processing( array $dt_config, int $view_id, WP_Post $post, GV_Extension_DataTables_Data $object ) {
        // Don't preload all the data if we're on a single-entry page.
	    if ( gravityview()->request->is_entry() ) {
            return $dt_config;
	    }

        $view = \GV\View::by_id( $view_id );

        // View not found...weird! Bail.
        if ( is_null( $view ) ) {
            return $dt_config;
        }

        $view->settings->set( 'page_size', PHP_INT_MAX );

	    $entries = $view->get_entries( gravityview()->request );

	    $dt_config = array_merge( $dt_config, [
		    'data'       => $object->get_output_data( $entries, $view, $post ),
		    'serverSide' => false,
		    'processing' => false,
	    ] );

	    /**
	     * @filter `gravityview/datatables/output` Filter the output returned from the AJAX request
	     * @since  2.3
	     *
	     * @param array                $output
	     * @param \GV\View             $view
	     * @param \GV\Entry_Collection $entries
	     */
	    $dt_config = apply_filters( 'gravityview/datatables/output', $dt_config, $view, $entries );

        return $dt_config;
    }

	/**
	 * Creates a shadow data object containing values only for fields that require special handling in the UI and adds it to the DataTables config.
	 * For example, we need raw values (as stored in the DB) for date fields to enable column filtering.
	 *
	 * @since 3.3
	 *
	 * @param array               $dt_config
	 * @param GV\View             $view
	 * @param GV\Entry_Collection $entries
	 *
	 * @return array
	 */
	public function update_config_with_shadow_data( $dt_config, $view, $entries ) {
		$fields = $view->fields->by_position( 'directory_table-columns' )->by_visible()->all();

		$date_fields                         = [ 'date', 'date_created', 'date_updated', 'payment_date' ];
		$field_types_with_special_processing = array_merge( $date_fields, [ 'email' ] );
		$columns_to_process                  = [];
		$shadow_data                         = [];

		foreach ( $fields as $column_index => $field ) {
			$type = $field->type ?: $field->field->type;

			if ( ! in_array( $type, $field_types_with_special_processing, true ) ) {
				continue;
			}

			$columns_to_process[ $column_index ] = [
				'id'   => $field->ID,
				'type' => $type,
			];
		}

		foreach ( $entries->all() as $entry_index => $entry ) {
			$entry = $entry->as_entry();

			// Default shadow data value is an empty string. It will get overwritten in the UI
			// with the original data value that has HTML markup stripped. This is done to reduce
			// the size of the shadow object by including only the necessary data that requires
			// special handling in the backend as done below.
			$shadow_data_row = array_fill( 0, count( $fields ), '' );

			foreach ( $columns_to_process as $column_index => $field ) {
				if ( ! array_key_exists( $field['id'], $entry ) ) {
					continue;
				}

				$entry_field_value    = $entry[ $field['id'] ];
				$dt_data_value        = $dt_config['data'][ $entry_index ][ $column_index ] ?? '';
				$dt_shadow_data_value = '';

				if ( in_array( $field['type'], $date_fields, true ) ) {
					// Convert date fields to Unix timestamp using milliseconds.
					// We can use this value to filter date field columns in the UI.
					try {
						$dt_shadow_data_value = ( new DateTime( $entry_field_value ) )->setTime( 0, 0, 0 )->getTimestamp() * 1000;
					} catch ( Exception $e ) {
						gravityview()->log->debug( "Invalid date value for field ID {$field['id']}" );
					}
				} else if ( 'email' === $field['type'] && preg_match( '/function hivelogic/', $dt_data_value ) ) {
					// Obfuscate email addresses if the original value is encoded using GravityView's "enkoder".
					// This uses a simple ROT13 algorithm and avoids deobfuscation overhead in the UI.
					$dt_shadow_data_value = str_rot13( $entry_field_value );
				}

				$shadow_data_row[ $column_index ] = $dt_shadow_data_value;
			}

			$shadow_data[] = $shadow_data_row;
		}

		$dt_config['shadowData'] = $shadow_data;

		return $dt_config;
	}
}

new GV_Extension_DataTables_Processing_Mode;
