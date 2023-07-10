<?php

/**
 * Class GV_Extension_DataTables_Field_Filters
 *
 * @since 3.0
 */
class GV_Extension_DataTables_Field_Filters extends GV_DataTables_Extension {

	protected $settings_key = 'field_filters';

	/**
	 * Allows wp_add_inline_style to be used on FixedHeader/FixedColumn style
	 */
	protected $script_priority = 100;

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

		return $settings;
	}

	public function settings_row( $ds ) {

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
							'url' => 'https://docs.gravityview.co/article/710-datatables-buttons',
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
						'desc'    => esc_html__( 'Fix the first column in place while horizontally scrolling a table. The first column and its contents will remain visible at all times.', 'gv-datatables' ),
					), $ds['field_filter_location'] );
					?>
                </td>
            </tr>
        </table>
		<?php
	}

	/**
	 * Add Field Filters configuration to the DT configuration array
	 */
	public function add_config( $dt_config, $view_id, $post ) {

	    // Don't process unless Field Filters is enabled
		if ( ! $this->get_setting( $view_id, 'field_filters' ) ) {
		    return $dt_config;
		}

		$view = \GV\View::by_id( $view_id );

		foreach ( $view->fields->by_position( 'directory_table-columns' )->by_visible()->all() as $key => $field ) {
			$dt_config['columns'][ $key ] = $this->process_field( $dt_config['columns'][ $key ], $field, $view );
		}

		$field_filters = $this->get_setting( $view_id, 'field_filter_location', 'footer' );

		$dt_config['field_filters'] = $field_filters;

		return $dt_config;
	}

	/**
     * Generates field configuration to be passed to the script.
     *
     * Configuration includes whether a field is searchable and settings for each column's search inputs
     *
	 * @param array $passed_field_column
	 * @param \GV\GF_Field $field
	 * @param \GV\View $view
	 *
	 * @return mixed
	 */
	private function process_field( $passed_field_column, $field, \GV\View $view ) {

		$field_column = $passed_field_column;

		$field_id = preg_replace( '/^gv_/ism', '', $field_column['name'] );

		/** @var GF_Field $gf_field */
		$gf_field = $field->field;
		$type     = \GV\Utils::get( $gf_field, 'type', $field->ID );
		$gv_field = GravityView_Fields::get( $type );

		if ( isset( $field->formId ) ) {
			$form_id = $field->formId;
		} elseif ( $field instanceof \GV\Internal_Field ) {
			$form_id = $field->as_configuration()['form_id'];
		} else {
			$form_id = $view->form->ID;
		}

		$form = \GV\GF_Form::by_id( $form_id );

		$atts = array(
			'type'        => 'search',
			'class'       => 'gv-dt-field-filter',
			'uid'         => preg_replace( '/[^a-z\d]/i', '', $field->UID ),
            // translators: %s is replaced by the field label
			'placeholder' => esc_attr_x( 'Filter by %s', '%s is replaced by the field label', 'gv-datatables' ),
		);

		$field_column['searchable'] = false;

		if ( $gv_field && $gv_field->is_searchable ) {
			$field_column['searchable'] = true;
		}

		// For now, don't support complex field types that have inputs (Address, Name, etc).
		if ( ! empty( $field->field->inputs ) && floor( $field->ID ) === (float) $field->ID ) {
			$field_column['searchable'] = false;
		}

		if ( 'id' === $field_id ) {
			$field_column['searchable'] = true;
			$atts['type']               = 'number';
			$atts['min']                = 1;
			$atts['step']               = 1;
		}

		if ( $gf_field && 'number' === \GV\Utils::get( $gf_field, 'type' ) ) {
			$atts['type'] = 'number';
			$atts['min']  = $gf_field->rangeMin;
			$atts['max']  = $gf_field->rangeMax;
		}

		if (
			( $gf_field && 'date' === \GV\Utils::get( $gf_field, 'type' ) )
			|| 'date_created' === $field_id
			|| 'date_updated' === $field_id
		) {
			$atts['type']    = 'date';
			$atts['pattern'] = '\d{4}-\d{2}-\d{2}';
			$atts['min']     = strtr( '{year}-01-01', array(
				'{year}' => (int) apply_filters( 'gform_date_min_year', '1920', $form->form, $gf_field ),
			) );
		}

		if ( 'date_created' === $field_id || 'date_updated' === $field_id ) {
			// The maximum date is, unless spacetime goes wonky, today :-)
			$atts['max'] = wp_date( 'Y-m-d' );
		}

		if ( $gf_field && ! empty( $gf_field->choices ) ) {
			$atts['type']    = 'select';
			$atts['options'] = wp_json_encode( $gf_field->choices );
		}

		if ( 'is_approved' === $field_id ) {
			$atts['type']    = 'select';
			$atts['options'] = wp_json_encode( GravityView_Entry_Approval_Status::get_all() );
		}

		if ( 'is_starred' === $field_id ) {
			$atts['type']    = 'select';
			$atts['options'] = wp_json_encode( array(
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
		 * @param string $filter_placeholder
		 * @param string $field_label
		 * @param \GV\GF_Field $field
		 * @param \GV\GF_Form $form
		 * @param \GV\View $view
		 */
		$filter_placeholder = apply_filters( 'gravityview/datatables/field_filters/placeholder', $atts['placeholder'], $field_label, $field, $form, $view );

		$atts['placeholder'] = sprintf( $filter_placeholder, $field_label );

		/**
		 * Modifies the attributes passed to the field filtering JS.
		 *
		 * @since 3.0
		 *
		 * @param string $filter_placeholder
		 * @param string $field_label
		 * @param \GV\GF_Field|\GV\Internal_Field $field
		 * @param \GV\GF_Form $form
		 * @param \GV\View $view
		 */
		$filter_atts = apply_filters( 'gravityview/datatables/field_filters/atts', $atts, $field, $form, $view );

		$field_column['atts'] = $filter_atts;

		return $field_column;
	}

	/**
	 * Pass the search parameters through the cloned table that DataTables FixedColumns creates
	 *
	 * @uses wp_add_inline_script
	 *
	 * @return void
	 */
	public function add_scripts( $dt_configs, $views, $post ) {

		if ( ! parent::add_scripts( $dt_configs, $views, $post ) ) {
			return;
		}

		$comment = '';

		if ( current_user_can( 'manage_options' ) ) {
			$comment = '/** Inline script added by GravityView DataTables Field Filters when using FixedColumns setting */';
		}

		$script = <<<EOD
$comment
(function ( $ ) {

	var typingTimer;
	var searchDelay = 350;

	function set_first_input( that ) {
		return $( '.gv-datatables:not(".DTFC_Cloned")' )
			.find( '.gv-dt-field-filter[data-uid=' + that.data( 'uid' ) + ']' )
			.val( that.val() )
			.first();
	}

	$( document ).on( 'draw.dt', function ( draw ) {

		var table = $( draw.target ).dataTable();

		$( '.gv-dt-field-filter', '.DTFC_Cloned' ).val( function () {
			return table.api().state() ? table.api().state().columns[ 0 ].search.search : $( this ).val();
		} ).on( 'keyup', function ( e ) {
		
			clearTimeout( typingTimer );

			first_input = set_first_input( $( this ) );

			typingTimer = setTimeout( function () {
				first_input.trigger( 'change' );
			}, searchDelay );
		} );
	} );
})( jQuery );
EOD;

		wp_add_inline_script( 'gv-dt-fixedcolumns', normalize_whitespace( $script, true ) );
	}
}

new GV_Extension_DataTables_Field_Filters;
