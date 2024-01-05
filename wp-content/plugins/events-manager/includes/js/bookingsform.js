document.querySelectorAll('#em-booking-form').forEach( el => el.classList.add('em-booking-form') ); //backward compatability


// Add event listeners
var em_booking_form_observer;
document.addEventListener("DOMContentLoaded", function() {
	document.querySelectorAll('form.em-booking-form').forEach( function( booking_form ){
		// backwards compatibility tweaks
		if( !('id' in booking_form.dataset) ){
			// find event id and give some essential ids
			let event_id_el = booking_form.querySelector('input[name="event_id"]');
			if( event_id_el ){
				let event_id = event_id_el.value;
				booking_form.setAttribute('data-id', event_id);
				booking_form.setAttribute('id', 'em-booking-form-' + event_id);
				booking_form.parentElement.setAttribute('data-id', event_id);
				booking_form.parentElement.setAttribute('id', 'event-booking-form-' + event_id);
				booking_form.querySelectorAll('.em-booking-submit, input[type="submit"]').forEach( button => button.classList.add('em-form-submit') );
			}
		}
		em_booking_form_init( booking_form );
	});
	// if you have an AJAX-powered site, set EM.bookings_form_observer = true before DOMContentLoaded and EM will detect dynamically added booking forms
	if( true || 'bookings_form_observer' in EM && EM.bookings_form_observer ) {
		em_booking_form_observer = new MutationObserver( function( mutationList ) {
			mutationList.forEach( function( mutation ) {
				if ( mutation.type === 'childList' ){
					mutation.addedNodes.forEach( function( node ){
						if ( node instanceof HTMLDivElement && node.classList.contains('em-event-booking-form') ) {
							em_booking_form_init( node.querySelector('form.em-booking-form') );
						}
					});
				}
			});
		});
		em_booking_form_observer.observe( document, { childList: true, attributes: false, subtree: true, } );
	}
});

var em_booking_form_count_spaces = function( booking_form ){
	// count spaces booked, if greater than 0 show booking form
	let tickets_selected = 0;
	let booking_data = new FormData(booking_form);
	for ( const pair of booking_data.entries() ) {
		if( pair[0].match(/^em_tickets\[[0-9]+\]\[spaces\]/) && parseInt(pair[1]) > 0 ){
			tickets_selected++;
		}
	}
	booking_form.setAttribute('data-spaces', tickets_selected);
	return tickets_selected;
};

var em_booking_form_init = function( booking_form ){
	booking_form.dispatchEvent( new CustomEvent('em_booking_form_init', {
		bubbles : true,
	}) );

	/**
	 * When ticket selection changes, trigger booking form update event
	 */
	booking_form.addEventListener("change", function( e ){
		if ( e.target.matches('.em-ticket-select') || (EM.bookings.update_listener && e.target.matches(EM.bookings.update_listener)) ){
			// trigger spaces refresh
			em_booking_form_count_spaces( booking_form );
			// let others do similar stuff
			booking_form.dispatchEvent( new CustomEvent('em_booking_form_updated') );
		}
	});

	let em_booking_form_updated_listener; // prevents double-check due to jQuery listener
	/**
	 * When booking form is updated, get a booking intent
	 */
	booking_form.addEventListener("em_booking_form_updated", function( e ){
		em_booking_form_updated_listener = true;
		em_booking_summary_ajax( booking_form ).finally( function(){
			em_booking_form_updated_listener = false;
		});
	});
	if( jQuery ) {
		// check for jQuery-fired legacy JS, but check above isn't already in progress due to new JS elements
		jQuery(booking_form).on('em_booking_form_updated', function () {
			if( !em_booking_form_updated_listener ){
				em_booking_summary_ajax(booking_form);
			}
		})
	}


	/**
	 * When booking summary is updated, get a booking intent if supplied and trigger the updated intent option
	 */
	booking_form.addEventListener("em_booking_summary_updated", function( e ){
		let booking_intent = e.detail.response.querySelector('input.em-booking-intent');
		em_booking_form_update_booking_intent( booking_form, booking_intent );
	});

	/**
	 * When booking is submitted
	 */
	booking_form.addEventListener("submit", function( e ){
		e.preventDefault();
		em_booking_form_submit( e.target );
	});

	// trigger an intent update
	let booking_intent = booking_form.querySelector('input.em-booking-intent');
	em_booking_form_update_booking_intent( booking_form, booking_intent );

	booking_form.dispatchEvent( new CustomEvent('em_booking_form_loaded', {
		bubbles : true,
	}) );
}

var em_booking_form_scroll_to_message = function ( booking_form ) {
	let messages = booking_form.parentElement.querySelectorAll('.em-booking-message');
	if( messages.length > 0 ) {
		let message = messages[0];
		window.scroll({
			top: message.getBoundingClientRect().top +  window.scrollY - EM.booking_offset,
			behavior : 'smooth',
		});
	}
}

var em_booking_form_add_message = function( booking_form, content = null, opts = {} ){
	let options = Object.assign({
		type : 'success', // or error
		replace : true,
		scroll : content !== null,
	}, opts);

	// replace
	if( options.replace ) {
		booking_form.parentElement.querySelectorAll('.em-booking-message').forEach( message => message.remove() );
	}

	// add message
	if( content !== null ) {
		let div = document.createElement('div');
		div.classList.add('em-booking-message', 'em-booking-message-' + options.type );
		div.innerHTML = content;
		booking_form.parentElement.insertBefore( div, booking_form );
	}

	// scroll if needed
	if( options.scroll ){
		em_booking_form_scroll_to_message( booking_form );
	}
}

var em_booking_form_add_error = function ( booking_form, error, opts ) {
	let options = Object.assign({
		type : 'error',
	}, opts);
	if( error != null ){
		if( (Array.isArray(error) || typeof error === 'object') ){
			let error_msg = '';
			if( typeof error === 'object' ){
				Object.entries(error).forEach( function( entry ){
					let [id, err] = entry;
					error_msg += '<p data-field-id="'+ id + '">' + err + '</p>';
				});
			}else{
				error.forEach(function( err ){
					error_msg += '<p>' + err + '</p>';
				});
			}
			if( error_msg ) {
				em_booking_form_add_message( booking_form, error_msg, options );
			}
			console.log( error );
		}else{
			em_booking_form_add_message( booking_form, error, options );
		}
	}
}

var em_booking_form_add_confirm = function ( booking_form, message, opts = {}) {
	let options = Object.assign({
		hide : false,
	}, opts);
	em_booking_form_add_message( booking_form, message, options );
	if( options.hide ){
		em_booking_form_hide_success( booking_form );
	}
}

var em_booking_form_hide_success = function( booking_form, opts = {} ){
	let options = Object.assign({
		hideLogin : true,
	}, opts);
	let booking_summary_sections = booking_form.querySelectorAll('.em-booking-form-summary-title, .em-booking-form-summary-title');
	if ( booking_summary_sections.length > 0 ) {
		booking_form.querySelectorAll('section:not(.em-booking-form-section-summary)').forEach( section => section.classList.add('hidden') );
		booking_form.parentElement.querySelectorAll('.em-booking-form > h3.em-booking-section-title').forEach( section => section.classList.add('hidden') ); // backcompat
	} else {
		booking_form.classList.add('hidden');
	}
	booking_form.dispatchEvent( new CustomEvent( 'em_booking_form_hide_success', {
		detail : {
			options : options,
		},
	}));
	// hide login
	if ( options.hideLogin ) {
		document.querySelectorAll('.em-booking-login').forEach( login => login.classList.add('hidden') );
	}
}

var em_booking_form_unhide_success = function( booking_form, opts = {} ){
	let options = Object.assign({
		showLogin : true,
	}, opts);
	let booking_summary_sections = booking_form.querySelectorAll('.em-booking-form-summary-title, .em-booking-form-summary-title');
	if ( booking_summary_sections.length > 0 ) {
		booking_form.querySelectorAll('section:not(.em-booking-form-section-summary)').forEach( section => section.classList.remove('hidden') );
		booking_form.parentElement.querySelectorAll('.em-booking-form > h3.em-booking-section-title').forEach( section => section.classList.remove('hidden') ); // backcompat
	} else {
		booking_form.classList.remove('hidden');
	}
	booking_form.dispatchEvent( new CustomEvent( 'em_booking_form_unhide_success', {
		detail : {
			options : options,
		},
	}));
	// hide login
	if ( options.showLogin ) {
		document.querySelectorAll('.em-booking-login').forEach( login => login.classList.add('hidden') );
	}
};

var em_booking_form_hide_spinner = function( booking_form ){
	booking_form.parentElement.querySelectorAll('.em-loading').forEach( spinner => spinner.remove() );
}

var em_booking_form_show_spinner = function( booking_form ){
	let spinner = document.createElement('div');
	spinner.classList.add('em-loading');
	booking_form.parentElement.append(spinner);
}

var em_booking_form_enable_button = function( booking_form, show = false ){
	let button = booking_form.querySelector('input.em-form-submit');
	button.disabled = false;
	button.classList.remove('disabled');
	if( show ){
		button.classList.remove('hidden');
	}
	return button;
}

var em_booking_form_disable_button = function( booking_form, hide = false ){
	let button = booking_form.querySelector('input.em-form-submit');
	button.disabled = true;
	button.classList.add('disabled');
	if( hide ){
		button.classList.add('hidden');
	}
	return button;
}

var em_booking_form_update_booking_intent = function( booking_form, booking_intent = null ){
	// remove current booking intent (if not the same as booking_intent and replace
	booking_form.querySelectorAll('input.em-booking-intent').forEach( function( intent ){
		if( booking_intent !== intent ) {
			intent.remove();
		}
	});
	// append to booking form
	if ( booking_intent ) {
		booking_form.append( booking_intent );
	}
	// handle the button and other elements on the booking form
	let button = booking_form.querySelector('input.em-form-submit');
	if( button ){
		if( booking_intent && booking_intent.dataset.spaces > 0 ){
			em_booking_form_enable_button( booking_form )
			if ( booking_intent.dataset.amount > 0 ) {
				// we have a paid booking, show paid booking button text
				if ( button.getAttribute('data-text-payment') ) {
					button.value = EM.bookings.submit_button.text.payment.replace('%s', booking_intent.dataset.amount_formatted);
				}
			} else {
				// we have a free booking, show free booking button
				button.value = EM.bookings.submit_button.text.default;
			}
		} else if ( !booking_intent && em_booking_form_count_spaces( booking_form ) > 0 ){
			// this is in the event that the booking form has minimum spaces selected, but no booking_intent was ever output by booking form
			// fallback / backcompat mainly for sites overriding templates and possibly not incluing the right actions/filters in their template
			button.value = EM.bookings.submit_button.text.default;
			em_booking_form_enable_button( booking_form );
		} else {
			// no booking_intent means no valid booking params yet
			button.value = EM.bookings.submit_button.text.default;
			em_booking_form_disable_button( booking_form );
		}
	}
	// if event is free or paid, show right heading (if avialable)
	booking_form.querySelectorAll('.em-booking-form-confirm-title').forEach( title => title.classList.add('hidden') );
	if( booking_intent && booking_intent.dataset.spaces > 0 ) {
		if (booking_intent.dataset.amount > 0) {
			booking_form.querySelectorAll('.em-booking-form-confirm-title-paid').forEach(title => title.classList.remove('hidden'));
		} else {
			booking_form.querySelectorAll('.em-booking-form-confirm-title-free').forEach(title => title.classList.remove('hidden'));
		}
	}
	// wrap intent into an object
	let intent = {
		uuid : 0,
		event_id : null,
		spaces : 0,
		amount : 0,
		amount_base : 0,
		amount_formatted : '$0',
		taxes : 0,
		currency : '$'
	};
	if( booking_intent ){
		intent = Object.assign(intent, booking_intent.dataset);
		intent.id = booking_intent.id; // the actual element id
	}
	// trigger booking_intent update for others to hook in
	booking_form.dispatchEvent( new CustomEvent('em_booking_intent_updated', {
		detail : {
			intent : intent,
			booking_intent : booking_intent,
		},
		cancellable : true,
	}) );
}

var em_booking_summary_ajax_promise;
var em_booking_summary_ajax = async function ( booking_form ){
	let summary_section = document.getElementById('em-booking-form-section-summary-' + booking_form.dataset.id);
	let summary;
	if( summary_section ) {
		summary = summary_section.querySelector('.em-booking-form-summary');
	}
	let booking_data = new FormData(booking_form);
	booking_data.set('action', 'booking_form_summary');

	if( em_booking_summary_ajax_promise ){
		em_booking_summary_ajax_promise.abort();
	}
	if( summary ){
		booking_form.dispatchEvent( new CustomEvent('em_booking_summary_updating', {
			detail : {
				summary : summary,
				booking_data : booking_data,
			},
			cancellable : true,
		}) );
		let template = booking_form.querySelector('.em-booking-summary-skeleton');
		if ( template ) {
			let skeleton = template.content.cloneNode(true);
			// count tickets, duplicate ticket rows if more than 1
			if ( booking_form.dataset.spaces > 1 ) {
				let tickets = skeleton.querySelector('.em-bs-section-items')
				let ticket_row = tickets.querySelector('.em-bs-row.em-bs-row-item');
				for ( let i = 1; i < booking_form.dataset.spaces; i++ ) {
					tickets.append( ticket_row.cloneNode(true) );
				}
			}
			booking_form.dispatchEvent( new CustomEvent('em_booking_summary_skeleton', {
				detail: { skeleton: skeleton },
			}) );
			summary.replaceChildren(skeleton);
		}
	}
	em_booking_summary_ajax_promise = fetch( EM.bookingajaxurl, {
		method: "POST",
		body: booking_data,
	}).then( function( response ){
		if( response.ok ) {
			return response.text();
		}
		return Promise.reject( response );
	}).then( function( html ){
		let parser = new DOMParser();
		let response = parser.parseFromString( html, 'text/html' );
		let summary_html = response.querySelector('.em-booking-summary');
		if( summary && summary_html ){
			summary.querySelectorAll('.em-loading').forEach( spinner => spinner.remove() );
			// show summary and reset up tippy etc.
			if ( typeof summary.replaceChildren === "function") { // 92% coverage, eventually use exclusively
				summary.replaceChildren(summary_html);
			} else {
				summary.innerHTML = '';
				summary.append(summary_html);
			}
			em_setup_tippy(summary);
			em_booking_summary_ajax_promise = false;
		}
		// dispatch booking summary updated event, which should also be caught and retriggered for a booking_intent update
		booking_form.dispatchEvent( new CustomEvent('em_booking_summary_updated', {
			detail : {
				response : response,
				summary : summary,
			},
			cancellable : true,
		}) );
	}).catch( function( error ){
		// remove all booking inent data - invalid state
		booking_form.querySelectorAll('input.em-booking-intent').forEach( intent => intent.remove() );
		booking_form.dispatchEvent( new CustomEvent('em_booking_summary_ajax_error', {
			detail : {
				error : error,
				summary : summary,
			},
			cancellable : true,
		}) );
	}).finally( function(){
		em_booking_summary_ajax_promise = false;
		if( summary ) {
			summary.querySelectorAll('.em-loading').forEach( spinner => spinner.remove() );
		}
		booking_form.dispatchEvent( new CustomEvent('em_booking_summary_ajax_complete', {
			detail : {
				summary : summary,
			},
			cancellable : true,
		}) );
	});
	return em_booking_summary_ajax_promise;
};

var em_booking_form_doing_ajax = false;
var em_booking_form_submit = function( booking_form, opts = {} ){
	let options = em_booking_form_submit_options( opts );

	// before sending
	if ( em_booking_form_doing_ajax ) {
		alert( EM.bookingInProgress );
		return false;
	}
	em_booking_form_doing_ajax = true;

	if ( options.doStart ) {
		em_booking_form_submit_start( booking_form, options );
	}

	let $response = null;

	let data = new FormData( booking_form );

	if( 'data' in opts && typeof opts.data === 'object') {
		for ( const [key, value] of Object.entries(opts.data) ) {
			data.set(key, value);
		}
	}

	return fetch( EM.bookingajaxurl, {
		method: "POST",
		body: data,
	}).then( function( response ){
		if( response.ok ) {
			return response.json();
		}
		return Promise.reject( response );
	}).then( function( response ){
		// backwards compatibility response
		if ( !('success' in response) && 'result' in response ){
			response.success = response.result;
		}
		// do success logic if set/requested
		if ( options.doSuccess ) {
			$response = response
			em_booking_form_submit_success( booking_form, response, options );
		}
		return response;
	}).catch( function( error ){
		// only interested in network errors, if response was processed, we may be catching a thrown error
		if ( $response ){
			// response was given
			if( options.showErrorMessages === true ){
				let $error = 'errors' in $response && $response.errors ? $response.errors : $response.message;
				em_booking_form_add_error( booking_form,  $error );
			}
		} else {
			if( options.doCatch ){
				em_booking_form_submit_error( booking_form, error );
			}
		}
	}).finally( function(){
		$response = null;
		if( options.doFinally ) {
			em_booking_form_submit_finally( booking_form, options );
		}
	});
}

var em_booking_form_submit_start = function( booking_form ){
	document.querySelectorAll('.em-booking-message').forEach( message => message.remove() );
	em_booking_form_show_spinner( booking_form );
	let booking_intent = booking_form.querySelector('input.em-booking-intent');
	let button = booking_form.querySelector( 'input.em-form-submit' );
	if( button ) {
		button.setAttribute('data-current-text', button.value);
		if ( booking_intent && 'dataset' in booking_intent ) {
			button.value = EM.bookings.submit_button.text.processing.replace('%s', booking_intent.dataset.amount_formatted);
		} else {
			// fallback
			button.value = EM.bookings.submit_button.text.processing;
		}
	}
}

var em_booking_form_submit_success = function( booking_form, response, opts = {} ){
	let options = em_booking_form_submit_options( opts );
	// hide the spinner
	if ( options.hideSpinner === true ) {
		em_booking_form_hide_spinner( booking_form );
	}
	// backcompat
	if( 'result' in response && !('success' in response) ){
		response.success = response.result;
	}
	// show error or success message
	if ( response.success ) {
		// show message
		if( options.showSuccessMessages === true ){
			em_booking_form_add_confirm( booking_form, response.message );
		}
		// hide form elements
		if ( options.hideForm === true ) {
			em_booking_form_hide_success( booking_form, options );
		}
		// trigger success event
		if( options.triggerEvents === true ){
			if( jQuery ) { // backcompat jQuery events, use regular JS events instaed
				jQuery(document).trigger('em_booking_success', [response, booking_form]);
				if( response.gateway !== null ){
					jQuery(document).trigger('em_booking_gateway_add_'+response.gateway, [response]);
				}
			}
			booking_form.dispatchEvent( new CustomEvent('em_booking_success', {
				detail: {
					response : response,
				},
				cancellable : true,
			}));
		}
		if( (options.redirect === true) && response.redirect ){ //custom redirect hook
			window.location.href = response.redirect;
		}
	}else{
		// output error message
		if( options.showErrorMessages === true ){
			if( response.errors != null ){
				em_booking_form_add_error( booking_form,  response.errors );
			}else{
				em_booking_form_add_error( booking_form,  response.message );
			}
		}
		// trigger error event
		if( options.triggerEvents === true ) {
			if( jQuery ) { // backcompat jQuery events, use regular JS events instaed
				jQuery(document).trigger('em_booking_error', [response, booking_form]);
			}
			booking_form.dispatchEvent( new CustomEvent('em_booking_error', {
				detail: {
					response : response,
				},
				cancellable : true,
			}));
		}
	}
	// reload recaptcha if available (shoud move this out)
	if ( !response.success && typeof Recaptcha != 'undefined' && typeof RecaptchaState != 'undefined') {
		try {
			Recaptcha.reload();
		} catch (error) {
			// do nothing
		}
	}else if ( !response.success && typeof grecaptcha != 'undefined' ) {
		try {
			grecaptcha.reset();
		} catch (error) {
			// do nothing
		}
	}
	// trigger final success event
	if ( options.triggerEvents === true ) {
		if( jQuery ) { // backcompat jQuery events, use regular JS events instaed
			jQuery(document).trigger('em_booking_complete', [response, booking_form]);
		}
		booking_form.dispatchEvent( new CustomEvent('em_booking_complete', {
			detail: {
				response : response,
			},
			cancellable : true,
		}));
	}
}

var em_booking_form_submit_error = function( booking_form, error ){
	if( jQuery ) { // backcompat jQuery events, use regular JS events instaed
		jQuery(document).trigger('em_booking_ajax_error', [null, null, null, booking_form]);
	}
	booking_form.dispatchEvent( new CustomEvent('em_booking_ajax_error', {
		detail: {
			error : error,
		},
		cancellable : true
	}));
	em_booking_form_add_error( booking_form,  'There was an unexpected network error, please try again or contact a site administrator.' );
	console.log( error );
};

var em_booking_form_submit_finally = function( booking_form, opts = {} ){
	let options = em_booking_form_submit_options( opts );
	em_booking_form_doing_ajax = false;

	let button = booking_form.querySelector( 'input.em-form-submit' );
	if ( button ) {
		if ( button.getAttribute( 'data-current-text' ) ) {
			button.value = button.getAttribute('data-current-text');
			button.setAttribute('data-current-text', null);
		} else {
			button.value = EM.bookings.submit_button.text.default;
		}
	}
	if( options.hideSpinner === true ) {
		em_booking_form_hide_spinner( booking_form );
	}
	if( options.showForm === true ) {
		em_booking_form_unhide_success( booking_form, opts );
	}

	if( jQuery ) { // backcompat jQuery events, use regular JS events instaed
		jQuery(document).trigger('em_booking_ajax_complete', [null, null, booking_form]);
	}
	booking_form.dispatchEvent( new CustomEvent('em_booking_complete', {
		cancellable : true,
	}));
};

var em_booking_form_submit_options = function( opts ){
	return Object.assign({
		doStart : true,
		doSuccess : true,
		doCatch : true,
		doFinally : true,
		showErrorMessages : true,
		showSuccessMessages : true,
		hideForm : true,
		hideLogin : true,
		showForm : false,
		hideSpinner : true,
		redirect : true, // can be redirected, not always
		triggerEvents : true
	}, opts);
};