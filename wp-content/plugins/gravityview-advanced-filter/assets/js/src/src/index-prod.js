import App from './App.svelte';

jQuery( document ).ready( function( $ ) {
	$( '#entry_filters' ).removeClass( 'hide-if-js' );

	const { conditions, fields, translations, fetchFields } = window.gvAdvancedFilter;
	const { ajaxurl } = window;

	let initialFormValue = $( '#gravityview_form_id' ).val();

	const AdvancedFilter = new App( {
		target: $( '#entry_filters' )[ 0 ],
		props: {
			conditions,
			fields,
			translations,
		},
	} );

	const onFormChange = () => {
		const { action, nonce } = fetchFields;

		let fields = null;
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
					fields = response.data.fields;
				}
			},
			complete: () => {
				AdvancedFilter.updateFields( fields );
			},
		} );
	};

	$( 'body' ).on( 'gravityview_form_change', onFormChange );
	$( '#gravityview_form_id' ).on( 'change', initialFormValue === '' ? onFormChange : null );
} );
