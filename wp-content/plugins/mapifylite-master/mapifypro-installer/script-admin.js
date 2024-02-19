jQuery( function( $ ) {
	$( '#install-mapifypro' ).click( function() {
		let api_key = $('input[name=mapifypro_api_key]').val();
		let $output = $('#mapifypro-installer-output');
		let $container = $( '#mapifypro-installer' );

		if ( ! $container.hasClass('loading') ) {
			if ( api_key.length < 40 ) {
				$output.html( mapifypro_installer.invalid_api_message );
			} else {
				send_ajax_request( 'verify_api_key', 'Verifying API Key...', null, true );
			}
		}

	} );

	$( '#mapifypro-installer-output' ).on( 'click', '.license-button', function() {
		let product_id = $( this ).data( 'id' );
		let label = mapifypro_installer.product_ids[ product_id ];
		let $output = $('#mapifypro-installer-output');

		// remove all license buttons
		$output.find( '.license-button' ).remove();

		// set message
		$output.append( mapifypro_installer.verified_message.replace( '%s', label ) );

		// send ajax request
		send_ajax_request( 'activate_api_key', 'Checking API key...', product_id, false );
	} );

	$( '#mapifypro-installer-output' ).on( 'click', '.activate-mapifypro', function() {
		$('#mapifypro-installer').addClass( 'loading-once' );
		$('#install-mapifypro').addClass( 'disabled' );
		$('#mapifypro_api_key').attr( 'disabled', 'disabled' );
	} );

	function send_ajax_request( request, progress_message, variable, flush_output ) {
		let data = {
			action: 'mapifypro_installer_action',
			api_key: $('input[name=mapifypro_api_key]').val(),
			action_request: request,
			request_variable: variable,
			nonce: $('input[name=nonce]').val()
		}

		let $output = $('#mapifypro-installer-output');

		if ( 'undefined' !== typeof flush_output && flush_output ) {
			$output.html( '' );
		} else {
			$output.append( '<br>' );
		}

		$output.append( progress_message );

		// start_loading
		setTimeout( function() {
			start_loading();
		}, 10 );

		$.post( ajaxurl, data, function( response ){
			$output.append( '<br>' + response.message );
			
			switch ( response.status ) {
				case 'api_key_verified':
					if ( 1 === response.product_ids.length ) {
						send_ajax_request( 'activate_api_key', 'Checking API key...', response.product_ids[0], false );
					} else {
						$output.append( '<br>' );		
						response.product_ids.forEach( ( product_id, index ) => {
							let label = mapifypro_installer.product_ids[ product_id ];
							let remaining = response.product_ids_data[ product_id ].data.activations_remaining;
							$output.append( '<a href="javascript:;" class="button license-button" data-id="' + product_id + '">' + label + ' (' + remaining + ' remaining)</a>' );
						} );
					}

					break;

				case 'api_key_activated':
					send_ajax_request( 'install_plugin', 'Installing plugin file...', response.download_url, false );
					break;
			
				default:
					break;
			}
			
			stop_loading();
		});
	}

	function start_loading() {
		$('#mapifypro-installer').addClass( 'loading' );
		$('#install-mapifypro').addClass( 'disabled' );
		$('#mapifypro_api_key').attr( 'disabled', 'disabled' );
	}

	function stop_loading() {
		$('#mapifypro-installer').removeClass( 'loading' );
		$('#install-mapifypro').removeClass( 'disabled' );
		$('#mapifypro_api_key').removeAttr( 'disabled' );
	}

	window.addEventListener( 'beforeunload', function (e) {
		if ( $('#mapifypro-installer').hasClass( 'loading' ) ) {
			e.preventDefault();
			e.returnValue = '';
		} else {
			delete e['returnValue'];
		}
	} );

} );