/**
 * GravityView glue for the Query Filters date and date-range pickers.
 *
 * Enqueued once per request. Iterates every `.gv-date-range-picker[data-gv-config]`,
 * `.gv-date-picker[data-gv-config]`, and legacy `.gv-datepicker` element on the page
 * and mounts the matching Query Filters picker plugin.
 */
( function ( $ ) {
	'use strict';

	/**
	 * Detects the date format key from CSS classes on an element.
	 *
	 * @param {jQuery} $el The element to inspect.
	 * @returns {string} The format key (e.g. 'mdy', 'dmy_dash').
	 */
	function detectFormat( $el ) {
		var formats = [ 'dmy_dash', 'dmy_dot', 'dmy', 'ymd_slash', 'ymd_dash', 'ymd_dot', 'mdy' ];
		for ( var i = 0; i < formats.length; i++ ) {
			if ( $el.hasClass( formats[ i ] ) ) {
				return formats[ i ];
			}
		}
		return 'mdy';
	}

	/**
	 * Parses a display-formatted date string to Y-m-d.
	 *
	 * @param {string} displayDate The formatted date string.
	 * @param {string} formatKey   The format key (e.g. 'mdy', 'dmy_dash').
	 * @returns {string} Date in YYYY-MM-DD format, or empty string.
	 */
	function parseDate( displayDate, formatKey ) {
		if ( ! displayDate ) {
			return '';
		}

		var sep = '/';
		if ( formatKey.indexOf( '_dash' ) !== -1 ) {
			sep = '-';
		} else if ( formatKey.indexOf( '_dot' ) !== -1 ) {
			sep = '.';
		}

		var parts = displayDate.split( sep );
		if ( parts.length !== 3 ) {
			return '';
		}

		var y, m, d;
		var order = formatKey.replace( /_dash|_dot|_slash/, '' );

		switch ( order ) {
			case 'dmy':
				d = parts[ 0 ]; m = parts[ 1 ]; y = parts[ 2 ];
				break;
			case 'ymd':
				y = parts[ 0 ]; m = parts[ 1 ]; d = parts[ 2 ];
				break;
			case 'mdy':
			default:
				m = parts[ 0 ]; d = parts[ 1 ]; y = parts[ 2 ];
				break;
		}

		return y + '-' + m + '-' + d;
	}

	/**
	 * Mounts QF pickers on elements with a data-gv-config attribute.
	 */
	function mount( $root, selector, pluginMethod, defaults ) {
		if ( typeof $.fn[ pluginMethod ] !== 'function' ) {
			return;
		}

		const isRange = pluginMethod === 'dateRangePicker';

		$root.find( selector ).addBack( selector ).each( function () {
			const $el = $( this );

			if ( $el.data( 'gv-picker-mounted' ) ) {
				return;
			}

			let config;

			try {
				config = JSON.parse( $el.attr( 'data-gv-config' ) || '{}' );
			} catch ( e ) {
				window.console && console.warn( e );
				return;
			}

			const $form = $el.closest( 'form' );
			const props = $.extend( {}, defaults, config, {
				onChange: notifyFormOnChange( $form ),
			} );
			const instance = $el[ pluginMethod ]( props );
			$el.data( 'gv-picker-mounted', true );
			$el.data( 'gv-picker-instance', instance );

			attachFormReset( $el, instance, isRange, config.value );
		} );
	}

	/**
	 * Bubbles picker changes as a `change` event on the form.
	 */
	function notifyFormOnChange( $form ) {
		if ( ! $form || ! $form.length ) {
			return undefined;
		}
		// setTimeout defers past the Svelte flush so the hidden input is up to date.
		return function () {
			window.setTimeout( function () {
				$form.trigger( 'change' );
			}, 0 );
		};
	}

	/**
	 * Restores the picker to its initial value on form reset.
	 */
	function attachFormReset( $el, instance, isRange, initialValue ) {
		if ( ! instance ) {
			return;
		}

		const update = isRange ? instance.updateRange : instance.updateDate;

		if ( typeof update !== 'function' ) {
			return;
		}

		const $form = $el.closest( 'form' );

		if ( ! $form.length ) {
			return;
		}

		const resetValue = initialValue && typeof initialValue === 'object'
			? initialValue
			: ( isRange ? { start: null, end: null } : { date: null } );

		$form.on( 'reset', function () {
			window.setTimeout( function () {
				update.call( instance, resetValue );
			}, 0 );
		} );
	}

	/**
	 * Replaces legacy `.gv-datepicker` inputs with QF single-date pickers.
	 *
	 * Reads the date format from CSS classes on the original input and
	 * converts existing values from display format to Y-m-d for the picker.
	 */
	function mountLegacy( $root, defaults ) {
		if ( typeof $.fn.datePicker !== 'function' ) {
			return;
		}

		$root.find( '.gv-datepicker' ).each( function () {
			const $input = $( this );

			if ( $input.data( 'gv-picker-mounted' ) ) {
				return;
			}

			var dateFormat = detectFormat( $input );
			var rawValue   = $input.val() || '';
			var isoValue   = parseDate( rawValue, dateFormat );

			var $mount = $( '<div>' );
			$input.after( $mount ).remove();

			const initialValue = { date: isoValue || null };
			const $form = $mount.closest( 'form' );
			const instance = $mount.datePicker( $.extend( {}, defaults, {
				inputElementName: $input.attr( 'name' ),
				value:            initialValue,
				dateFormat:       dateFormat,
				showTodayButton:  true,
				onChange:         notifyFormOnChange( $form ),
			} ) );

			$mount.data( 'gv-picker-mounted', true );
			$mount.data( 'gv-picker-instance', instance );

			attachFormReset( $mount, instance, false, initialValue );
		} );
	}

	$( function () {
		const $body    = $( document.body );
		const defaults = window.gvQueryFiltersDatePicker || {};

		mount( $body, '.gv-date-range-picker[data-gv-config]', 'dateRangePicker', window.gvQueryFiltersDateRangePicker || {} );
		mount( $body, '.gv-date-picker[data-gv-config]', 'datePicker', defaults );
		mountLegacy( $body, defaults );

		// Recapture data-state once pickers have injected their hidden inputs.
		$body.find( '.gv-widget-search' ).each( function () {
			const $form = $( this );
			$form.attr( 'data-state', $form.serialize() );
		} );
	} );
} )( jQuery );
