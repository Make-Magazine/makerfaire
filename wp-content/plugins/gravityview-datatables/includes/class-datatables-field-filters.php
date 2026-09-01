<?php

use GV\GF_Form;
use GV\Utils;
use GV\View;

/**
 * Class GV_Extension_DataTables_Field_Filters
 *
 * @since 3.0
 */
class GV_Extension_DataTables_Field_Filters extends GV_DataTables_Extension {

	protected $settings_key = 'field_filters';

	const FIELD_UID_REGEX = '/[^a-z\d]/i';

	public function __construct() {
		parent::__construct();

		add_filter( 'gk/foundation/inline-scripts', [ $this, 'modify_date_filter_type_selection' ] );
	}

	/**
	 * Set default setting for extension
	 *
	 * @param array $settings
	 *
	 * @return array field_filters default is false.
	 */
	public function defaults( $settings ) {
		$settings['field_filters']         = false;
		$settings['field_filter_location'] = 'footer';
		$settings['date_filter_type']      = 'date';
		$settings['fields_with_filter']    = '';
		$settings['clear_filters_button']  = 1;

		return $settings;
	}

	public function settings_row( $ds, $post ) {
		$fields = View::by_id( $post->ID )->fields->by_position( 'directory_table-columns' )->by_visible()->all();

		// Set "All Fields" default option since an empty array triggers an error when rendering the multiselect option.
		// This option is overwritten in the UI (see setFilterFields() in datatables-admin-views.js).
		$fields_with_filter = [ '' => esc_html__( 'All Fields', 'gv-datatables' ) ];

		if ( ! empty( $fields ) ) {
			$fields_with_filter = [];

			foreach ( $fields as $field ) {
				$uid = preg_replace( self::FIELD_UID_REGEX, '', $field->UID );

				$fields_with_filter[ $uid ] = $field->label;
			}
		}

		?>
        <table class="form-table">
            <caption><?php esc_html_e( 'Field Filters', 'gv-datatables' ); ?></caption>
            <tr valign="top">
                <td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option( 'datatables_settings[field_filters]', array(
						'label'   => __( 'Enable Field Filters', 'gv-datatables' ),
						'desc'    => esc_html__( 'Display search fields in the table footer to filter results by each field.', 'gv-datatables' ),
						'type'    => 'checkbox',
						'value'   => 1,
						'article' => array(
							'id'  => '5ea73bab04286364bc9914ba',
							'url' => 'https://docs.gravitykit.com/article/710-datatables-buttons',
						),
					), $ds['field_filters'] );
					?>
                </td>
            </tr>
            <tr valign="top" data-requires="field_filters">
                <td colspan="2">
					<?php
					echo GravityView_Render_Settings::render_field_option( 'datatables_settings[field_filter_location]', array(
						'label'   => __( 'Input Location', 'gv-datatables' ),
						'type'    => 'radio',
						'value'   => 'footer',
						'choices' => array(
							'footer' => esc_html_x( 'Footer', 'The footer of an HTML table', 'gv-datatables' ),
							'header' => esc_html_x( 'Header', 'The header of an HTML table', 'gv-datatables' ),
							'both'   => esc_html_x( 'Both', 'Both options', 'gv-datatables' ),
						),
						'desc'    => esc_html__( 'Choose where the filter inputs appear on the table.', 'gv-datatables' ),
					), $ds['field_filter_location'] );
					?>
                </td>
            </tr>
	        <tr valign="top">
		        <td colspan="2" data-requires="field_filters">
			        <?php
			        echo GravityView_Render_Settings::render_field_option( 'datatables_settings[fields_with_filter]', array(
				        'label'   => __( 'Fields With Filter', 'gv-datatables' ),
				        'type'    => 'multiselect',
				        'choices' => $fields_with_filter,
				        'value'   => '',
				        'desc'    => esc_html__( 'Select one or more fields for which filtering will be enabled.', 'gv-datatables' ),
			        ), $ds['fields_with_filter'] );
			        ?>
		        </td>
	        </tr>
	        <tr valign="top" data-requires="field_filters">
		        <td colspan="2">
			        <?php
			        echo GravityView_Render_Settings::render_field_option( 'datatables_settings[clear_filters_button]', array(
				        'label' => __( 'Show "Clear Filters" button', 'gv-datatables' ),
				        'type'  => 'checkbox',
				        'value' => 1,
				        'desc'  => esc_html__( 'Display a button that clears all field filters at once when any filter is active.', 'gv-datatables' ),
			        ), \GV\Utils::get( $ds, 'clear_filters_button', 1 ) );
			        ?>
		        </td>
	        </tr>
	        <tr valign="top" id="date_filter_type" data-requires="field_filters">
		        <td colspan="2">
			        <?php
			        echo GravityView_Render_Settings::render_field_option( 'datatables_settings[date_filter_type]', array(
				        'label'   => __( 'Date Filter Type', 'gv-datatables' ),
				        'type'    => 'radio',
				        'value'   => 'date',
				        'choices' => array(
					        'date'       => esc_html__( 'Single Date Input', 'gv-datatables' ),
					        'date_range' => esc_html__( 'Date Range', 'gv-datatables' ),
				        ),
				        'desc'    => esc_html__( 'Select how to apply date filters for column values. Choose Single Date Input to specify a filter for a specific day, or select Date Range to define a "From" and "To" date range for filtering values within a specific period. Date Range is only available with client-side processing.', 'gv-datatables' ),
			        ), $ds['date_filter_type'] );
			        ?>
		        </td>
	        </tr>
        </table>

		<?php
		echo GravityView_Render_Settings::render_field_option( 'datatables_settings[version]', array(
			// Stamps the DataTables version the settings were last saved with, so upgrade paths
			// can tell a pre-3.2 silo from a current one. Never shown to the author.
			'label'   => __( 'DataTables version', 'gv-datatables' ),
			'type'    => 'hidden',
			'value'   => GV_DT_VERSION,
		), GV_DT_VERSION );
		?>

		<?php
	}

	/**
	 * Add Field Filters configuration to the DT configuration array
	 */
	public function add_config( $dt_config, $view_id, $post, $object ) {

	    // Don't process unless Field Filters is enabled
		if ( ! $this->get_setting( $view_id, 'field_filters' ) ) {
		    return $dt_config;
		}

		$view = View::by_id( $view_id );

		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $key => $field ) {
			$dt_config['columns'][ $key ] = $this->process_field( $dt_config['columns'][ $key ], $field, $view );
		}

		$field_filters = $this->get_setting( $view_id, 'field_filter_location', 'footer' );

		$dt_config['field_filters'] = $field_filters;

		$show_clear_button = (bool) $this->get_setting( $view_id, 'clear_filters_button', 1 );

		if ( $show_clear_button ) {
			$dt_config['clearFilters'] = array(
				'enabled' => true,
				'label'   => __( 'Clear Filters', 'gv-datatables' ),
				'cleared' => __( 'All filters cleared.', 'gv-datatables' ),
			);
		}

		return $dt_config;
	}

	/**
     * Generates field configuration to be passed to the script.
     *
     * Configuration includes whether a field is searchable and settings for each column's search inputs
     *
	 * @param array $passed_field_column
	 * @param \GV\GF_Field $field
	 * @param View $view
	 *
	 * @return mixed
	 */
	private function process_field( $passed_field_column, $field, View $view ) {

		$field_column = $passed_field_column;

		$field_id = preg_replace( '/^gv_/ism', '', $field_column['name'] );

		/** @var GF_Field $gf_field */
		$gf_field = $field->field;
		$type     = Utils::get( $gf_field, 'type', $field->ID );
		$gv_field = GravityView_Fields::get( $type );

		if ( isset( $field->formId ) ) {
			$form_id = $field->formId;
		} elseif ( $field instanceof \GV\Internal_Field ) {
			$form_id = $field->as_configuration()['form_id'];
		} else {
			$form_id = $view->form->ID;
		}

		$form = GF_Form::by_id( $form_id );

		$dt_settings = get_post_meta( $view->ID, '_gravityview_datatables_settings', true );

		// An absent `processing_mode` key (any View saved before 3.3) must default to server-side,
		// matching class-datatables-processing-mode.php and class-datatables-data.php.
		$is_server_side = 'serverSide' === Utils::get( $dt_settings, 'processing_mode', GV_Extension_DataTables_Processing_Mode::DEFAULT_MODE );

		$atts = [
			'type'        => 'search',
			'field_type'  => $gf_field->type ?? '',
			'class'       => 'gv-dt-field-filter',
			'uid'         => preg_replace( self::FIELD_UID_REGEX, '', $field->UID ),
			// translators: %s is replaced by the field label
			'placeholder' => esc_attr_x( 'Filter by %s', '%s is replaced by the field label', 'gv-datatables' ),
		];

		$field_column['searchable'] = false;

		if ( $gv_field && $gv_field->is_searchable ) {
			$field_column['searchable'] = true;
		}

		if ( 'id' === $field_id ) {
			$field_column['searchable'] = true;
			$atts['type']               = 'number';
			$atts['min']                = 1;
			$atts['step']               = 1;
		}

		if ( $gf_field && 'number' === Utils::get( $gf_field, 'type' ) ) {
			$atts['type'] = 'number';
			$atts['min']  = $gf_field->rangeMin;
			$atts['max']  = $gf_field->rangeMax;
		}

		if (
			( $gf_field && 'date' === Utils::get( $gf_field, 'type' ) )
			|| 'date_created' === $field_id
			|| 'date_updated' === $field_id
			|| 'payment_date' === $field_id
		) {
			$is_server_side = 'serverSide' === Utils::get( $dt_settings, 'processing_mode', GV_Extension_DataTables_Processing_Mode::DEFAULT_MODE );

			$atts['field_type'] = Utils::get( $dt_settings, 'date_filter_type', self::defaults( [] )['date_filter_type'] );

			if ( $is_server_side ) {
				// Server-side only supports a single date input, so override the UI setting in
				// case client-side rendering was enabled before and Date Range was selected.
				// Both keys matter: `field_type` is what datatables-views.js reads to choose the
				// single-input vs. two-input renderer, and `type` sets the rendered input's HTML
				// `type` attribute.
				$atts['field_type'] = 'date';
				$atts['type']       = 'date';
			}

			$atts['pattern'] = '\d{4}-\d{2}-\d{2}';
			$atts['min']     = strtr( '{year}-01-01', array(
				'{year}' => (int) apply_filters( 'gform_date_min_year', '1920', $form->form, $gf_field ),
			) );

			$atts['from_date_title'] = strtr(
				esc_html_x( 'Start date to filter the [field name] field', 'Placeholders inside [] are not to be translated.', 'gv-datatables' ),
				[ '[field name]' => $field->get_label( $view, $form ) ]
			);

			$atts['to_date_title'] = strtr(
				esc_html_x( 'End date to filter the [field name] field', 'Placeholders inside [] are not to be translated.', 'gv-datatables' ),
				[ '[field name]' => $field->get_label( $view, $form ) ]
			);

			$atts['title'] = strtr(
				esc_html_x( 'Date to filter the [field name] field', 'Placeholders inside [] are not to be translated.', 'gv-datatables' ),
				[ '[field name]' => $field->get_label( $view, $form ) ]
			);
		}

		if ( 'date_created' === $field_id || 'date_updated' === $field_id ) {
			// The maximum date is, unless spacetime goes wonky, today :-)
			$atts['max'] = wp_date( 'Y-m-d' );
		}

		if ( $gf_field && ! empty( $gf_field->choices ) ) {
			$choices = $gf_field->choices;

			if ( 'radio' === $gf_field->type ) {
				$atts['field_type'] = 'select';
			} elseif ( 'chainedselect' === $gf_field->type ) {
				$choices = array_map( function ( $entry ) use ( $field ) {
					$value = $entry->as_entry()[ $field->ID ] ?? '';

					return '' === $value ? [] : [
						'text'  => $value,
						'value' => $value,  // text = value in Chained Selects.
					];
				}, $view->get_entries()->all() );

				$choices = array_filter( $choices );

				array_multisort( array_column( $choices, 'text' ), SORT_ASC, $choices );
			} elseif ( class_exists( 'GP_Populate_Anything' ) && ( $gf_field->{'gppa-choices-enabled'} || $gf_field->{'gppa-values-enabled'} ) ) {
				GP_Populate_Anything::get_instance()->populate_field( $gf_field, $form, [] );

				$choices = $gf_field->choices;

				foreach ( $choices as &$choice ) {
					$choice['value'] = ! $is_server_side ? $choice['text'] : $choice['value']; // If client-side, use text as the value.

					$choice = array_intersect_key( $choice, array_flip( [ 'value', 'text' ] ) );
				}
			}

			$atts['type']    = 'select';
			$atts['options'] = wp_json_encode( $choices );
		}

		if ( in_array( $field_id, [ 'is_approved', 'entry_approval' ] ) ) {
			$atts['type']       = 'select';
			$atts['field_type'] = 'select';

			$options = GravityView_Entry_Approval_Status::get_all();

			foreach ( $options as &$option ) {
				$option['text']  = $option['label'];
				$option['value'] = ! $is_server_side ? $option['label'] : $option['value']; // If client-side, use label as the value.

				$option = array_intersect_key( $option, array_flip( [ 'value', 'text' ] ) );
			}

			$atts['options'] = wp_json_encode( $options );
		}

		if ( 'is_starred' === $field_id ) {
			$atts['type']       = 'select';
			$atts['field_type'] = 'select';
			$atts['options']    = wp_json_encode( array(
				array(
					'value' => 1,
					'label' => __( 'Is Starred', 'gv-datatables' ),
				),
				array(
					'value' => 0,
					'label' => __( 'Not Starred', 'gv-datatables' ),
				),
			) );
		}

		if ( ! $is_server_side && preg_match( '/^custom_/', $field_id ) ) {
			$field_column['searchable'] = true;
		}

		if ( ! empty( $atts['options'] ) ) {
			$atts['placeholder'] = sprintf( '— %s —', $atts['placeholder'] );
		}

		$field_label = $field->get_label( $view, $form );

		/**
		 * Modifies the placeholder text used in the per-field filters.
		 *
		 * The `%s` placeholder is replaced by the $field label, fetched using {@see \GV\GF_Field::get_label()}.
		 * HTML tags are ignored; the value grabbed by `jQuery.text()` will be used
		 *
		 * @since 3.0
		 *
		 * @param string        $filter_placeholder The placeholder text.
		 * @param string        $field_label        The field label.
		 * @param \GV\GF_Field  $field              The field object.
		 * @param GF_Form       $form               The form object.
		 * @param View          $view               The view object.
		 */
		$filter_placeholder = apply_filters( 'gravityview/datatables/field_filters/placeholder', $atts['placeholder'], $field_label, $field, $form, $view );

		$atts['placeholder'] = sprintf( $filter_placeholder, $field_label );

		/**
		 * Modifies the attributes passed to the field filtering JS.
		 *
		 * @since 3.0
		 *
		 * @param array                              $atts  The filter attributes.
		 * @param \GV\GF_Field|\GV\Internal_Field    $field The field object.
		 * @param GF_Form                            $form  The form object.
		 * @param View                               $view  The view object.
		 */
		$filter_atts = apply_filters( 'gravityview/datatables/field_filters/atts', $atts, $field, $form, $view );

		$field_column['atts'] = $filter_atts;

		// Backward compatibility with <=v3.2 to ensure that all fields are searchable.
		if ( version_compare( $dt_settings['version'] ?? '3.2', '3.2', '<=' ) ) {
			return $field_column;
		}

		// An empty (untouched) "Fields With Filter" selection means no restriction, matching the
		// settings row's own fallback label for it ("All Fields"), not zero filters. Only an
		// explicit, non-empty selection narrows the set. The setting is a multiselect in the UI
		// ($fieldsWithFilterSettingsEl.val() returns an array), but the inspector schema declares
		// it `text`; normalize to an array here so a scalar from that path, or from older stored
		// data, doesn't fail in_array()'s type expectation.
		if ( ! empty( $dt_settings['fields_with_filter'] ) ) {
			$fields_with_filter = (array) $dt_settings['fields_with_filter'];

			$field_column['searchable'] = $field_column['searchable'] && in_array( $field_column['atts']['uid'], $fields_with_filter );
		}

		return $field_column;
	}

	/**
	 * Modifies the date filter type selection when data processing mode is changed.
	 *
	 * @since 3.3
	 *
	 * @param array $scripts
	 *
	 * @return array
	 */
	public function modify_date_filter_type_selection( $scripts ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		// #date_filter_type only exists in the View editor's DataTables metabox. Foundation
		// fires `gk/foundation/inline-scripts` on every admin page, so without this guard the
		// script shipped everywhere else too (F-21).
		if ( ! $screen || 'gravityview' !== $screen->post_type || 'post' !== $screen->base ) {
			return $scripts;
		}

		$scripts[] = [
			'script' => <<<JS
(function ( $ ) {
	$(window).on('load', function() {
		const _processingMode = $('input[name="datatables_settings[processing_mode]"]');

		_processingMode.on('change', function() {
			// A `disabled` input is dropped from the form entirely, so locking the Date
			// Range radios this way silently wiped a saved choice the moment an author
			// switched to server-side and saved (F-21). Lock the whole row visually
			// (pointer-events, aria-disabled) instead of disabling the inputs, so the
			// checked radio -- and therefore what gets posted -- never changes; switching
			// back to client-side later restores exactly what was saved.
			var isServerSide = $('#datatables_settingsprocessing_mode-serverSide').is(':checked');

			$('#date_filter_type')
				.css({
					pointerEvents: isServerSide ? 'none' : '',
					opacity: isServerSide ? 0.5 : '',
				} )
				.attr('aria-disabled', isServerSide ? 'true' : null);
		});

		if (_processingMode.length) {
			_processingMode.trigger('change');
		}
	});
})( jQuery );
JS
		];

		return $scripts;
	}
}

new GV_Extension_DataTables_Field_Filters;
