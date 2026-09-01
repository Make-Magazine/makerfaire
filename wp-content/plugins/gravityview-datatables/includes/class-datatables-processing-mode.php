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
	/**
	 * {@inheritdoc}
	 *
	 * `is_enabled()` is false whenever no `processing_mode` value is stored, which is every View
	 * saved before 3.3 — and those Views run server-side, so they are exactly the ones a Random
	 * sort has to be rescued from. Overridden so a random sort reaches `add_config()` on its own.
	 *
	 * @since 3.12.0
	 *
	 * @return array
	 */
	public function maybe_add_config( $dt_config, $view_id, $post, $object ) {
		if ( $this->is_enabled( $view_id ) || $this->has_random_sort( $view_id, $dt_config, $object ) ) {
			return $this->add_config( $dt_config, $view_id, $post, $object );
		}

		return $dt_config;
	}

	function add_config( $dt_config, $view_id, $post, $object ) {
		$processing_mode = $this->get_setting( $view_id, $this->settings_key, self::DEFAULT_MODE );
		$has_random_sort = $this->has_random_sort( $view_id, $dt_config, $object );

		if ( $post instanceof WP_Post && ( $processing_mode !== self::DEFAULT_MODE || $has_random_sort ) ) {
			$dt_config = $this->modify_config_for_client_side_processing( $dt_config, $view_id, $post, $object );

			gravityview()->log->debug(
				$has_random_sort
					// A server-side Random sort re-shuffles on every AJAX request, so paging
					// duplicates and drops rows; the single-fetch client-side path is the only
					// mode that can page it coherently (F-9).
					? '[processing_mode_add_config] Sort direction is Random: forcing client-side processing regardless of the saved processing_mode.'
					: '[processing_mode_add_config] Updating DataTables config to use client-side.'
			);
		}

		return $dt_config;
	}

	/**
	 * Whether this render's effective sort (saved meta, overlaid with any shortcode/block
	 * override) includes a Random direction.
	 *
	 * Reuses `GV_Extension_DataTables_Data::get_effective_sort()` (F-8) rather than reading
	 * `sort_direction` off the View directly, so a `sort_direction=RAND` shortcode/block
	 * override -- which survives core's sanitization -- routes through this check too.
	 *
	 * @since 3.12.0
	 *
	 * @param int                           $view_id  The View ID.
	 * @param array                         $dt_config The configuration built so far; already carries `columns` by the time `add_config()` runs.
	 * @param GV_Extension_DataTables_Data|null $object The current instance of the GV_Extension_DataTables_Data class.
	 *
	 * @return bool
	 */
	private function has_random_sort( $view_id, array $dt_config, $object ) {
		if ( ! $object instanceof GV_Extension_DataTables_Data ) {
			return false;
		}

		$view = \GV\View::by_id( $view_id );

		if ( ! $view instanceof \GV\View ) {
			return false;
		}

		$columns = \GV\Utils::get( $dt_config, 'columns', array() );
		$sort    = $object->get_effective_sort( $view, $columns );

		foreach ( (array) \GV\Utils::get( $sort, 'directions', array() ) as $direction ) {
			if ( 'rand' === strtolower( $direction ) ) {
				return true;
			}
		}

		return false;
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
	     * Filter the output returned from the AJAX request
	     *
	     * @since 2.3
	     *
	     * @param array                $dt_config The DataTables configuration array.
	     * @param \GV\View             $view      The View object.
	     * @param \GV\Entry_Collection $entries   The entries collection.
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

		// Get the actual column count from data array (includes hidden sort fields).
		$total_columns = ! empty( $dt_config['data'][0] ) ? count( $dt_config['data'][0] ) : count( $fields );

		$date_fields                         = [ 'date', 'date_created', 'date_updated', 'payment_date' ];
		$field_types_with_special_processing = array_merge( $date_fields, [ 'email' ] );
		$columns_to_process                  = [];
		$shadow_data                         = [];

		foreach ( $fields as $column_index => $field ) {
			$type = $field->type ?: ( $field->field->type ?? null );

			if ( ! $type || ! in_array( $type, $field_types_with_special_processing, true ) ) {
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
			$shadow_data_row = array_fill( 0, $total_columns, '' );

			foreach ( $columns_to_process as $column_index => $field ) {
				if ( ! array_key_exists( $field['id'], $entry ) ) {
					continue;
				}

				$entry_field_value    = $entry[ $field['id'] ];
				$dt_data_value        = $dt_config['data'][ $entry_index ][ $column_index ] ?? '';
				$dt_shadow_data_value = '';

				if ( in_array( $field['type'], $date_fields, true ) ) {
					// new DateTime( '' ) does not throw -- PHP treats an empty string as "now"
					// on both 7.4 and 8.x -- so a field submitted empty (the entry key exists
					// with value '') must be caught here, before construction, or every undated
					// entry gets today's timestamp and wrongly matches "today" in every
					// client-side date filter/sort. Leave the shadow cell at its '' default: the
					// JS falls back to the rendered value for it (empty for a blank date), which
					// correctly excludes the row rather than treating it as epoch (0 would be a
					// valid 1970 timestamp and match any range starting before then).
					$raw_date = is_string( $entry_field_value ) ? trim( $entry_field_value ) : '';

					if ( '' !== $raw_date ) {
						// Convert date fields to Unix timestamp using milliseconds.
						// We can use this value to filter date field columns in the UI.
						try {
							$dt_shadow_data_value = ( new DateTime( $raw_date ) )->setTime( 0, 0, 0 )->getTimestamp() * 1000;
						} catch ( Exception $e ) {
							gravityview()->log->debug( "Invalid date value for field ID {$field['id']}" );
						}
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
