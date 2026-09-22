/**
 * Faire signs admin JS.
 *
/* global ajaxurl, mfSigns */
 
(function () {
	'use strict';
 
	// Gap between polls. Each poll itself does MF_SIGN_POLL_BUDGET seconds of generation,
	// so this is just breathing room, not the work interval.
	var POLL_GAP_MS = 1500;
 
	// Must exceed the server's poll budget comfortably.
	var POLL_TIMEOUT_MS = 90000;
 
	var running = {};
 
	function nonce() {
		return ( typeof mfSigns !== 'undefined' && mfSigns.nonce ) ? mfSigns.nonce : '';
	}
 
	/**
	 * The status <span>s live inside #tabs<faire>, not #collapse<faire>.
	 * Classes on the page: "signs pdfEntList", "presenter pdfEntList", "tabletags pdfEntList",
	 *                      "maker updateMsg",  "presenter updateMsg",  "tabletags updateMsg"
	 */
	function statusEl( faire, type, which ) {
		return jQuery( '#tabs' + faire ).find( '.' + type + '.' + which );
	}
 
	function say( faire, type, which, html, isError ) {
		var $el = statusEl( faire, type, which );
		if ( ! $el.length ) {
			console.warn( 'mf_fairesigns: no status element for', faire, type, which );
			return;
		}
		$el.html( html ).css( 'color', isError ? '#b32d2e' : '' );
	}
 
	function post( data ) {
		return jQuery.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: jQuery.extend( { nonce: nonce() }, data ),
			timeout: POLL_TIMEOUT_MS
		} );
	}
 
	function errMsg( response, fallback ) {
		return ( response && response.data && response.data.msg ) ? response.data.msg : fallback;
	}
 
	/* ---------------------------------------------------------------------
	 * Generate all signs
	 * ------------------------------------------------------------------ */
 
	window.createPDF = function ( faire, type ) {
		var key = faire + '|' + type;
 
		if ( running[ key ] ) {
			return; // already polling in this tab
		}
 
		say( faire, type, 'pdfEntList', 'Checking&hellip;' );
 
		// If a run is already in progress, pick it up where it stopped rather than
		// rebuilding the queue from scratch.
		post( { action: 'mf_signStatus', faire: faire, type: type } )
			.done( function ( response ) {
				if ( response && response.success && response.data && response.data.state === 'running' ) {
					say( faire, type, 'pdfEntList', 'Resuming the run already in progress&hellip;' );
					render( faire, type, response.data );
					startPolling( faire, type );
					return;
				}
				start( faire, type );
			} )
			.fail( function () {
				start( faire, type );
			} );
	};
 
	function start( faire, type ) {
		say( faire, type, 'pdfEntList', 'Building the entry list&hellip;' );
 
		post( { action: 'createEntList', faire: faire, type: type } )
			.done( function ( response ) {
				if ( response && response.success ) {
					say( faire, type, 'pdfEntList', response.data.msg );
					startPolling( faire, type );
				} else {
					say( faire, type, 'pdfEntList', errMsg( response, 'The server returned an unexpected response. Check the PHP error log.' ), true );
				}
			} )
			.fail( function ( xhr ) {
				say(
					faire,
					type,
					'pdfEntList',
					'Request failed (HTTP ' + xhr.status + '). ' +
						( xhr.status === 403 ? 'Permission or nonce problem — try reloading the page.' : 'Check the PHP error log.' ),
					true
				);
			} );
	}
 
	/* ---------------------------------------------------------------------
	 * Polling — each poll also does the work
	 * ------------------------------------------------------------------ */
 
	function startPolling( faire, type ) {
		running[ faire + '|' + type ] = true;
		poll( faire, type );
	}
 
	function stopPolling( faire, type ) {
		delete running[ faire + '|' + type ];
	}
 
	function poll( faire, type ) {
		var key = faire + '|' + type;
 
		if ( ! running[ key ] ) {
			return;
		}
 
		post( { action: 'mf_signStatus', faire: faire, type: type } )
			.done( function ( response ) {
				if ( ! response || ! response.success ) {
					stopPolling( faire, type );
					say( faire, type, 'pdfEntList', errMsg( response, 'Lost contact with the server.' ), true );
					return;
				}
 
				var d = response.data;
				render( faire, type, d );
 
				if ( d.state === 'running' ) {
					setTimeout( function () {
						poll( faire, type );
					}, POLL_GAP_MS );
				} else {
					stopPolling( faire, type );
				}
			} )
			.fail( function ( xhr ) {
				stopPolling( faire, type );
				say(
					faire,
					type,
					'pdfEntList',
					'Polling failed (HTTP ' + ( xhr.status || 'timeout' ) + '). The run has paused — click Generate again to resume from where it stopped.',
					true
				);
			} );
	}
 
	function render( faire, type, d ) {
		var failNote = d.fail > 0
			? ' &mdash; <span style="color:#b32d2e">' + d.fail + ' failed</span>'
			: '';
 
		var firstError = d.firstError
			? '<br><small style="color:#b32d2e">First failure: ' + jQuery( '<div>' ).text( d.firstError ).html() + '</small>'
			: '';
 
		if ( d.state === 'running' ) {
			var pct = d.total ? Math.round( ( d.done / d.total ) * 100 ) : 0;
			say(
				faire,
				type,
				'pdfEntList',
				'Generating: <strong>' + d.done + ' / ' + d.total + '</strong> (' + pct + '%)' +
					failNote +
					( d.locked ? ' &mdash; <em>another tab is running this</em>' : '' ) +
					firstError
			);
			return;
		}
 
		if ( d.state === 'done' ) {
			say(
				faire,
				type,
				'pdfEntList',
				'Finished: <strong>' + d.ok + ' of ' + d.total + '</strong> generated' +
					( d.fail > 0
						? ', <span style="color:#b32d2e">' + d.fail + ' failed — see the PHP error log</span>' + firstError
						: '. Reload the page to update "Last created on".' ),
				d.fail > 0
			);
			return;
		}
 
		if ( d.state === 'error' ) {
			say( faire, type, 'pdfEntList', d.msg || 'The run stopped with an error.', true );
			return;
		}
 
		if ( d.state === 'idle' ) {
			say( faire, type, 'pdfEntList', '' );
		}
	}
 
	/* ---------------------------------------------------------------------
	 * Zip creation
	 * ------------------------------------------------------------------ */
 
	window.createZip = function ( faire, type ) {
		say( faire, type, 'updateMsg', 'Building zip files&hellip;' );
 
		post( {
			action: 'createSignZip',
			faire: faire,
			type: type,
			seltype: jQuery( 'input[name=' + faire + type + 'seltype]:checked' ).val(),
			selstatus: jQuery( 'input[name=' + faire + type + 'selstatus]:checked' ).val(),
			error: jQuery( 'input[name=' + faire + type + 'filtererror]:checked' ).val(),
			filform: jQuery( 'select[name=' + faire + type + 'filterform]' ).val()
		} )
			.done( function ( response ) {
				if ( response && response.success ) {
					var html = response.data.msg + ' Reload the page to see them listed.';
					if ( response.data.files && response.data.files.length ) {
						html += '<br><small>' + response.data.files.join( '<br>' ) + '</small>';
					}
					say( faire, type, 'updateMsg', html );
				} else {
					say( faire, type, 'updateMsg', errMsg( response, 'Zip creation failed. Check the PHP error log.' ), true );
				}
			} )
			.fail( function ( xhr ) {
				say( faire, type, 'updateMsg', 'Request failed (HTTP ' + ( xhr.status || 'timeout' ) + ').', true );
			} );
	};
 
	/* ---------------------------------------------------------------------
	 * Legacy per-entry generation (only where genTableTags() renders a.fairsign links)
	 * ------------------------------------------------------------------ */
 
	window.printSigns = function ( type, faire ) {
		var pdfLink = type === 'signs' ? 'makersigns' : ( type === 'presenter' ? 'presenterSigns' : 'tabletag' );
		var folder = type === 'signs' ? 'maker' : type;
 
		jQuery( 'a.fairsign' ).each( function () {
			var $a = jQuery( this );
			var eid = $a.attr( 'id' );
 
			$a.html( 'Creating' ).attr( 'disabled', 'disabled' );
 
			jQuery.ajax( {
				type: 'GET',
				url: mfSigns.themeUri + '/generate_pdf/' + pdfLink + '.php',
				data: { eid: eid, type: 'save', faire: faire }
			} ).done( function () {
				$a.html( eid ).attr( 'href', mfSigns.themeUri + '/signs/' + faire + '/' + folder + '/' + eid + '.pdf' );
			} ).fail( function ( xhr ) {
				$a.html( eid + ' (failed: ' + xhr.status + ')' ).css( 'color', '#b32d2e' );
			} );
		} );
	};
 
	window.fireEvent = function ( obj, evt ) {
		if ( document.createEvent ) {
			var evObj = document.createEvent( 'MouseEvents' );
			evObj.initEvent( evt, true, false );
			obj.dispatchEvent( evObj );
		} else if ( document.createEventObject ) {
			obj.fireEvent( 'on' + evt );
		}
	};
 
	// Warn before navigating away mid-run — closing the tab pauses generation.
	jQuery( window ).on( 'beforeunload', function () {
		for ( var k in running ) {
			if ( running.hasOwnProperty( k ) ) {
				return 'Sign generation is still running. Leaving this page will pause it.';
			}
		}
	} );
}());
