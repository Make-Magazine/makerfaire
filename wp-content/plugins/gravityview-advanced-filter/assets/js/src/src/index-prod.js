import App from './App.svelte';

jQuery( document ).ready( function( $ ) {

	$( '#entry_filters' ).removeClass( 'hide-if-js' );

	const { conditions, translations, fetchFields } = window.gvAdvancedFilter;
	let { fields_complete, fields_default } = window.gvAdvancedFilter;
	const { ajaxurl } = window;

	let initialFormValue = $( '#gravityview_form_id' ).val();

	const AdvancedFilter = new App( {
		target: $( '#entry_filters' )[ 0 ],
		props: {
			conditions,
			fields: fields_complete,
			translations,
		},
	} );

	const onFormChange = () => {
		const { action, nonce } = fetchFields;

		$.ajax( {
			type: 'post',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action,
				nonce,
				form_id: $( '#gravityview_form_id' ).val(),
			},
			success: ( response ) => {
				if ( response.success ) {
					( { fields_default, fields_complete } = response.data );
				}
			},
			complete: () => {
				AdvancedFilter.updateFields( fields_complete );
			},
		} );
	};

	// Form fields are not available when the View is created with a "form_id" URL query parameter, in which case we need to fetch them from the server
	if ( ! fields_complete.length ) {
		onFormChange();
	}

	$( '#gravityview_form_id' ).on( 'change', initialFormValue === '' ? onFormChange : null );

	$( 'body' ).on( 'gravityview_form_change', onFormChange );

	$( 'body' ).on( 'dialogopen', '.gv-fields', function( e ) {

		const $conditionalLogicElement = $( e.target ).find( '.gv-setting-container-conditional_logic_container .gv-field-conditional-logic' );
		const $conditionsExportElement = $( e.target ).find( '.gv-setting-container-conditional_logic input' );
		const $conditionsFailOutputElement = $( e.target ).find( '.gv-setting-container-conditional_logic_fail_output' );
		let _conditions = null;

		if ( $conditionalLogicElement.hasClass( 'initialized' ) ) {
			return;
		}

		$conditionalLogicElement.addClass( 'initialized' );

		try {
			_conditions = JSON.parse( $conditionsExportElement.val() );
		} catch ( e ) {}

		new App( {
			target: $conditionalLogicElement[ 0 ],
			props: {
				conditions: _conditions,
				fields: fields_default,
				translations,
				onConditionsUpdate: _updatedConditions => {
					$conditionsExportElement.val( JSON.stringify( _updatedConditions ) );
					$conditionsFailOutputElement.toggleClass( 'hidden', ! _updatedConditions );
				},
			},
		} );
	} );

} );
