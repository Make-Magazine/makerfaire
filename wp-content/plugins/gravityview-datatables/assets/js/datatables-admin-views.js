/**
 * Custom js script loaded on Views edit screen (admin)
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    GravityKit <hello@gravitykit.com>
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @global {object} GV_DataTables_Admin
 * @since 1.0.0
 */

/**
 * JS port of GV_Extension_DataTables_Data::normalize_width_values(), plus the per-field
 * clamp that get_normalized_column_widths() applies before handing off to it.
 *
 * This predicts, in the View editor, exactly what the server will render, so a case added
 * to tests/fixtures/width-normalization-cases.json fails on whichever side (PHP or here)
 * has drifted from the other. Exposed on window (not inside the jQuery IIFE) so
 * tests/JS/specs/width-budget-normalization.spec.js can load this file and call it directly
 * without needing DOM fixtures or jQuery.
 *
 * @global window.gvDataTablesWidthBudget
 */
( function ( global ) {

	/**
	 * Per-field clamp mirroring get_normalized_column_widths(): a PHP-"empty" value (blank,
	 * 0, "0") means no width at all, not a width clamped up to 1. Anything else clamps to
	 * the field's declared range, [1, 100].
	 *
	 * @param {Array<number|string|null>} rawWidths Widths as configured, in column order.
	 * @return {Array<number|null>}
	 */
	function clampWidths( rawWidths ) {
		return rawWidths.map( function ( raw ) {
			var isPhpEmpty = null === raw || undefined === raw || '' === raw || 0 === Number( raw );

			if ( isPhpEmpty ) {
				return null;
			}

			// parseInt on a non-numeric string is NaN; `|| 0` mirrors PHP's absint(),
			// which resolves a non-numeric value to 0 rather than rejecting it.
			var whole = Math.abs( parseInt( raw, 10 ) ) || 0;

			return Math.min( 100, Math.max( 1, whole ) );
		} );
	}

	/**
	 * Direct port of GV_Extension_DataTables_Data::normalize_width_values(). Given widths
	 * already clamped to [1, 100] or null, applies the same three rules the renderer does:
	 * scale an over-100 sum down to ratios, share the remainder across blank columns, or
	 * drop every width to content sizing when the remainder cannot give each blank column
	 * at least 1%.
	 *
	 * @param {Array<number|null>} clamped Clamped widths; null for unset.
	 * @return {Array<number|null>}
	 */
	function normalizeClamped( clamped ) {
		var result = clamped.slice();
		var setIndexes = [];
		var unsetIndexes = [];

		clamped.forEach( function ( width, index ) {
			( null === width ? unsetIndexes : setIndexes ).push( index );
		} );

		if ( 0 === setIndexes.length ) {
			return result;
		}

		var total = setIndexes.reduce( function ( sum, index ) { return sum + result[ index ]; }, 0 );

		if ( total > 100 ) {
			var remaining = 100;
			var scaled = {};

			setIndexes.forEach( function ( index ) {
				scaled[ index ] = Math.max( 1, Math.floor( ( result[ index ] / total ) * 100 ) );
				remaining -= scaled[ index ];
			} );

			if ( remaining > 0 ) {
				// array_search( max(...), ..., true ) returns the FIRST key holding the
				// max value; only advance on a strictly greater value to match that.
				var widestIndex = setIndexes[ 0 ];

				setIndexes.forEach( function ( index ) {
					if ( scaled[ index ] > scaled[ widestIndex ] ) {
						widestIndex = index;
					}
				} );

				scaled[ widestIndex ] += remaining;
			}

			setIndexes.forEach( function ( index ) { result[ index ] = scaled[ index ]; } );

			total = setIndexes.reduce( function ( sum, index ) { return sum + result[ index ]; }, 0 );
		}

		if ( 0 === unsetIndexes.length ) {
			return result;
		}

		var remainingForBlanks = 100 - total;

		if ( remainingForBlanks < unsetIndexes.length ) {
			return clamped.map( function () { return null; } );
		}

		var share = Math.floor( remainingForBlanks / unsetIndexes.length );
		var leftover = remainingForBlanks - ( share * unsetIndexes.length );
		var lastUnsetIndex = unsetIndexes[ unsetIndexes.length - 1 ];

		unsetIndexes.forEach( function ( index ) {
			result[ index ] = index === lastUnsetIndex ? share + leftover : share;
		} );

		return result;
	}

	/**
	 * Full pipeline: raw, as-typed values in, final normalized percentages (or null) out.
	 *
	 * @param {Array<number|string|null>} rawWidths Widths as configured, in column order.
	 * @return {Array<number|null>}
	 */
	function normalizeWidths( rawWidths ) {
		return normalizeClamped( clampWidths( rawWidths ) );
	}

	/**
	 * Classifies a set of raw widths into the three notice states the plan specifies:
	 *
	 * - "over": every column has a width and the sum exceeds 100; widths scale down to fit.
	 *   (A sum over 100 WITH a blank column always resolves to "starved" instead, because
	 *   scaling first pins the total at exactly 100, leaving the blank no remainder.)
	 * - "starved": at least one width is configured and at least one column is blank, but
	 *   the remainder cannot give every blank column at least 1%, so every width is dropped.
	 * - "none": nothing to warn about, notice stays hidden. Covers both "no widths at all"
	 *   and "widths fit within budget".
	 *
	 * @param {Array<number|string|null>} rawWidths Widths as configured, in column order.
	 * @return {{state: string, sum: number, normalized: Array<number|null>, blankCount: number}}
	 */
	function computeBudget( rawWidths ) {
		var clamped = clampWidths( rawWidths );
		var blankCount = clamped.filter( function ( width ) { return null === width; } ).length;
		var hasAnyWidth = blankCount < clamped.length;

		if ( ! hasAnyWidth ) {
			return { state: 'none', sum: 0, normalized: clamped, blankCount: blankCount };
		}

		var normalized = normalizeClamped( clamped );
		var configuredSum = clamped.reduce( function ( sum, width ) { return sum + ( width || 0 ); }, 0 );
		var isStarved = blankCount > 0 && normalized.every( function ( width ) { return null === width; } );

		if ( isStarved ) {
			return { state: 'starved', sum: configuredSum, normalized: normalized, blankCount: blankCount };
		}

		if ( 0 === blankCount && configuredSum > 100 ) {
			return { state: 'over', sum: configuredSum, normalized: normalized, blankCount: blankCount };
		}

		return { state: 'none', sum: configuredSum, normalized: normalized, blankCount: blankCount };
	}

	/**
	 * Fills a localized string's sprintf-style placeholders (`%1$d`, `%2$s`, `%d`, ...) and
	 * unescapes `%%` to a literal `%`, matching PHP's sprintf() convention even though no
	 * sprintf() call runs here. The strings PHP hands to JS are written for PHP's own
	 * sprintf() (the source has `100%%` so PHP's sprintf() itself does not misparse it), so a
	 * naive string replace leaves the escape unresolved and prints "100%%" verbatim.
	 *
	 * @param {string}                          template     The localized string.
	 * @param {Object<string,string|number>}     replacements Placeholder token to value.
	 * @return {string}
	 */
	function formatBudgetString( template, replacements ) {
		// A private-use-area sentinel: guaranteed absent from translated text or a
		// replacement value, so it round-trips through the placeholder substitution below
		// without a real "%" in a field label colliding with it.
		var PERCENT_PLACEHOLDER = '';
		var result = template.split( '%%' ).join( PERCENT_PLACEHOLDER );

		Object.keys( replacements ).forEach( function ( token ) {
			result = result.split( token ).join( replacements[ token ] );
		} );

		return result.split( PERCENT_PLACEHOLDER ).join( '%' );
	}

	global.gvDataTablesWidthBudget = {
		clampWidths: clampWidths,
		normalizeWidths: normalizeWidths,
		computeBudget: computeBudget,
	};

	global.gvDataTablesFormatBudgetString = formatBudgetString;

	/**
	 * Mirrors GV_DataTables_Column_Control::pin_counts(): a pin is only effective when it is
	 * part of a contiguous run starting at the left edge or ending at the right edge. Any
	 * other pin (a "middle pin", or a `left` pin after an unpinned column) is ignored at
	 * render, which the editor needs to flag per field rather than let render silently.
	 *
	 * @param {Array<string>} pins Pin values ('', 'left', or 'right'), in column order.
	 * @return {Array<boolean>} Parallel array: true where that pin has no effect.
	 */
	function computeIneffectivePins( pins ) {
		var n = pins.length;
		var leftRun = 0;

		while ( leftRun < n && 'left' === pins[ leftRun ] ) {
			leftRun++;
		}

		var rightRun = 0;

		while ( rightRun < n && 'right' === pins[ n - 1 - rightRun ] ) {
			rightRun++;
		}

		return pins.map( function ( pin, index ) {
			if ( 'left' === pin ) {
				return index >= leftRun;
			}

			if ( 'right' === pin ) {
				return index < n - rightRun;
			}

			return false;
		} );
	}

	global.gvDataTablesPinWarning = {
		computeIneffectivePins: computeIneffectivePins,
	};

}( window ) );

(function( $ ) {

	var gvDataTablesExt = {

		has_tabs: null,

		init: function() {

            gvDataTablesExt.has_tabs = $( '#gravityview_settings' ).data("ui-tabs");

			$('#gravityview_directory_template')
				.on( 'change', gvDataTablesExt.toggleMetaboxAndRowGroup );

			$('#datatables_settingsbuttons, #datatables_settingsscroller, #datatables_settingsauto_update, #datatables_settingsrowgroup')
				.on( 'change', gvDataTablesExt.showGroupOptions )
				.trigger('change');

			$( 'body' )
				.on( 'gravityview/settings/tab/enable', gvDataTablesExt.showMetabox )
				.on( 'gravityview/settings/tab/disable', gvDataTablesExt.hideMetabox )
				.on( 'gravityview/field-added', load_row_group_with_fields )
				.on( 'sortupdate', '#directory-active-fields .active-drop', load_row_group_with_fields )
				.on( 'gravityview/field-added', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/field-removed', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/all-fields-removed', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/view-config-updated', gvDataTablesExt.setFilterFields )
				.on( 'gravityview/dialog-closed', gvDataTablesExt.setFilterFields );

			widthBudgetNotice.init();
			pinWarningNotice.init();
		},

		// Automagically manage the list of fields with filters by updating the select element under DataTables settings.
		setFilterFields: function () {
			const fieldUIDtoLabelMap = {};

			$( '#directory-active-fields .active-drop > div.gv-fields' ).each( function () {
				const $fieldLabelEl = $( this ).find( '.field-label' );

				if ( !$fieldLabelEl.length ) {
					return;
				}
				const nameAttr = $fieldLabelEl.attr( 'name' ) || '';
				const nameAttrValue = nameAttr.match( /\[directory_table-columns\]\[(.*?)\]/ );

				if ( !nameAttrValue || !nameAttrValue[ 1 ] ) {
					return;
				}

				fieldUIDtoLabelMap[ nameAttrValue[ 1 ].replace( /[^a-z\d]/, '' ) ] = $( this ).find( '.gv-field-label-text-container' ).text();
			} );

			const $fieldsWithFilterSettingsEl = $( '#datatables_settingsfields_with_filter' );

			const previouslySelectedFields = $fieldsWithFilterSettingsEl.val();

			if ( $.isEmptyObject( fieldUIDtoLabelMap ) ) {
				$fieldsWithFilterSettingsEl.empty();

				$fieldsWithFilterSettingsEl.parents( 'tr' ).hide();
			} else {
				// Retrieve all option values that are available in the select element.
				const existingOptions = $fieldsWithFilterSettingsEl.find( 'option' ).map( function () { return this.value; } ).get();

				// Convert to a set to simplify lookup later.
				const previouslySelectedFieldsSet = new Set( previouslySelectedFields );

				$fieldsWithFilterSettingsEl.empty();

				$.each( fieldUIDtoLabelMap, function ( key, value ) {
					// If the field was previously selected or didn't exist, then it should be selected.
					const isSelected = previouslySelectedFieldsSet.has( key ) || !existingOptions.includes( key );

					$fieldsWithFilterSettingsEl.append( $( '<option>', { value: key, text: value } ).prop( 'selected', isSelected ) );
				} );

				$fieldsWithFilterSettingsEl.parents( 'tr' ).show();
			}
		},

		toggleMetaboxAndRowGroup: function() {

			var template = $('#gravityview_directory_template').val();
			var $setting = $('#gravityview_datatables_settings');

			if( 'datatables_table' === template ) {

				$('body').trigger('gravityview/settings/tab/enable', $setting );

				load_row_group_with_fields();

			} else {

				$('body').trigger('gravityview/settings/tab/disable', $setting );

			}
		},

		showMetabox: function( event, tab ) {

			if( ! gvDataTablesExt.has_tabs ) {
				$( tab ).slideDown( 'fast' );
			}
		},

		hideMetabox: function( event, tab ) {

			if( ! gvDataTablesExt.has_tabs ) {
				$( tab ).slideUp( 'fast' );
			}
		},

		/**
		 * Show the sub-settings for each DataTables extension checkbox
		 */
		showGroupOptions: function() {
			var _this = $(this);
			if( _this.is(':checked') ) {
				_this.parents('tr').siblings().fadeIn();
			} else {
				_this.parents('tr').siblings().fadeOut( 100 );
			}
		},
	};

	/**
	 * Adds all active fields to a row group select, keyed by each field's own UID so a
	 * reorder or delete cannot silently repoint a saved selection at a different field.
	 */
	function load_row_group_with_fields() {
		var active_fields = $( '#directory-active-fields .active-drop > div.gv-fields' );
		var settings_field = $( '#datatables_settingsrowgroup_field' );
		var selected_value = settings_field.val();

		// The View editor localizes this before enqueue; guard anyway so a missing global
		// degrades to "don't skip internal fields" instead of throwing and aborting the
		// rest of this ready handler.
		var internal_fields = ( 'undefined' !== typeof GV_DataTables_Admin && GV_DataTables_Admin.internal_fields ) || {};
		var options = [];

		active_fields.each(function (i, elem) {
			var $elem = $( elem );
			var field_id = $elem.find('.field-key').val();

			// Don't group by internal GravityView fields; they're not unique.
			if ( internal_fields.hasOwnProperty( field_id ) ) {
				return;
			}

			var $fieldLabelEl = $elem.find( '.field-label' );
			var nameAttr      = $fieldLabelEl.attr( 'name' ) || '';
			var nameAttrValue = nameAttr.match( /\[directory_table-columns\]\[(.*?)\]/ );
			var uid = nameAttrValue && nameAttrValue[ 1 ] ? nameAttrValue[ 1 ] : null;

			if ( ! uid ) {
				return;
			}

			var label = $elem.find( '.gv-field-label-text-container' ).text();
			var backup_label = $elem.find( '.gv-field-label' ).data( 'original-title' );

			// If `label` is falsey (e.g., '', null, undefined), use `backup_label`
			options.push( { uid: uid, label: label || backup_label } );
		} );

		settings_field.empty();

		// Return early if there are no options
		if ( options.length === 0 ) {
			return;
		}

		$.each( options, function ( i, option ) {
			settings_field.append( $( '<option>', {
				value: option.uid,
				text: option.label,
				selected: option.uid === selected_value
			} ) );
		} );
	}

	// Exposed so tests/JS/specs/row-group-admin-fields.spec.js can drive it directly,
	// mirroring the window.gvDataTablesWidthBudget export above.
	window.loadRowGroupWithFieldsForTest = load_row_group_with_fields;

	// Exposed the same way for tests/JS/specs/set-filter-fields-missing-name.spec.js.
	window.setFilterFieldsForTest = gvDataTablesExt.setFilterFields;

	/**
	 * Live budget notice above the directory-table-columns fields zone. On load and on every
	 * width change, field add/remove, or field reorder, predicts what
	 * GV_Extension_DataTables_Data::get_normalized_column_widths() will render, using the
	 * shared normalizeWidths() port defined above this IIFE. Role-gated visibility cannot be
	 * evaluated in the editor (the render-time column set depends on who is viewing), so this
	 * computes over every configured column; the copy is worded as the default outcome.
	 */
	var widthBudgetNotice = {

		NOTICE_ID: 'gv-dt-width-budget-notice',
		WIDTH_SELECTOR: '#directory-active-fields input[name^="fields[directory_table-columns]"][name$="[width]"]',
		debounceTimer: null,

		init: function () {
			if ( 'undefined' === typeof GV_DataTables_Admin || ! GV_DataTables_Admin.width_budget ) {
				return; // Localized strings missing; nothing safe to render.
			}

			$( 'body' )
				.on( 'input change', widthBudgetNotice.WIDTH_SELECTOR, widthBudgetNotice.scheduleRecompute )
				.on(
					'gravityview/field-added gravityview/field-removed gravityview/all-fields-removed',
					widthBudgetNotice.scheduleRecompute
				)
				.on( 'sortupdate', '#directory-active-fields .active-drop', widthBudgetNotice.scheduleRecompute )
				.on( 'change', '#gravityview_directory_template', widthBudgetNotice.scheduleRecompute );

			widthBudgetNotice.recompute();
		},

		// 150ms so typing "1", "15", "150" announces once instead of three times.
		scheduleRecompute: function () {
			clearTimeout( widthBudgetNotice.debounceTimer );
			widthBudgetNotice.debounceTimer = setTimeout( widthBudgetNotice.recompute, 150 );
		},

		// The "width" field option is shared with the Table layout; this notice is a
		// DataTables-only concern.
		isDataTablesTemplate: function () {
			return 'datatables_table' === $( '#gravityview_directory_template' ).val();
		},

		getNoticeEl: function () {
			var $existing = $( '#' + widthBudgetNotice.NOTICE_ID );

			if ( $existing.length ) {
				return $existing;
			}

			var $notice = $(
				'<div id="' + widthBudgetNotice.NOTICE_ID + '" class="gv-dt-width-budget" role="status" aria-live="polite" hidden>' +
					'<span class="dashicons dashicons-warning" aria-hidden="true"></span> ' +
					'<span class="gv-dt-width-budget-text"></span>' +
				'</div>'
			);

			var $zone = $( '#directory-active-fields' );

			if ( $zone.length ) {
				$zone.before( $notice );
			}

			return $notice;
		},

		// Widths and field labels, in field order, for the over-budget list copy.
		readColumns: function () {
			var widths = [];
			var labels = [];

			$( widthBudgetNotice.WIDTH_SELECTOR ).each( function () {
				var $input = $( this );
				var $field = $input.closest( '.gv-fields' );
				var label = $field.find( '.gv-field-label-text-container' ).text() ||
					$field.find( '.gv-field-label' ).data( 'original-title' ) ||
					'';

				widths.push( $input.val() );
				labels.push( label );
			} );

			return { widths: widths, labels: labels };
		},

		recompute: function () {
			var $notice = widthBudgetNotice.getNoticeEl();
			var $inputs = $( widthBudgetNotice.WIDTH_SELECTOR );

			if ( ! widthBudgetNotice.isDataTablesTemplate() ) {
				widthBudgetNotice.clear( $notice, $inputs );

				return;
			}

			var columns = widthBudgetNotice.readColumns();
			var budget = window.gvDataTablesWidthBudget.computeBudget( columns.widths );
			var strings = GV_DataTables_Admin.width_budget;

			if ( 'over' === budget.state ) {
				var items = [];

				budget.normalized.forEach( function ( width, index ) {
					if ( null === width ) {
						return;
					}

					items.push(
						window.gvDataTablesFormatBudgetString( strings.overBudgetItem, {
							'%1$s': columns.labels[ index ],
							'%2$d': width,
						} )
					);
				} );

				var text = window.gvDataTablesFormatBudgetString( strings.overBudget, {
					'%1$d': budget.sum,
					'%2$s': items.join( strings.listSeparator ),
				} );

				widthBudgetNotice.show( $notice, $inputs, text );
			} else if ( 'starved' === budget.state ) {
				var template = 1 === budget.blankCount ? strings.starvedSingular : strings.starvedPlural;
				var starvedText = window.gvDataTablesFormatBudgetString( template, { '%d': budget.blankCount } );

				widthBudgetNotice.show( $notice, $inputs, starvedText );
			} else {
				widthBudgetNotice.clear( $notice, $inputs );
			}
		},

		show: function ( $notice, $inputs, text ) {
			$notice.find( '.gv-dt-width-budget-text' ).text( text );
			$notice.removeAttr( 'hidden' );

			$inputs.each( function () {
				widthBudgetNotice.addDescribedBy( $( this ) );
			} );
		},

		clear: function ( $notice, $inputs ) {
			$notice.attr( 'hidden', 'hidden' );
			$notice.find( '.gv-dt-width-budget-text' ).text( '' );

			$inputs.each( function () {
				widthBudgetNotice.removeDescribedBy( $( this ) );
			} );
		},

		// Adds/removes only this notice's id from aria-describedby, so an input's existing
		// description (if any) is never clobbered.
		addDescribedBy: function ( $el ) {
			var tokens = ( $el.attr( 'aria-describedby' ) || '' ).split( /\s+/ ).filter( Boolean );

			if ( -1 === tokens.indexOf( widthBudgetNotice.NOTICE_ID ) ) {
				tokens.push( widthBudgetNotice.NOTICE_ID );
			}

			$el.attr( 'aria-describedby', tokens.join( ' ' ) );
		},

		removeDescribedBy: function ( $el ) {
			var tokens = ( $el.attr( 'aria-describedby' ) || '' )
				.split( /\s+/ )
				.filter( Boolean )
				.filter( function ( token ) { return token !== widthBudgetNotice.NOTICE_ID; } );

			if ( tokens.length ) {
				$el.attr( 'aria-describedby', tokens.join( ' ' ) );
			} else {
				$el.removeAttr( 'aria-describedby' );
			}
		},
	};

	/**
	 * F-20: a per-field pin that is not adjacent to a table edge is silently ignored at
	 * render, with no editor feedback (spec U7's `gv-dt-pin-warning`). Flags each such select
	 * inline, on field reorder or pin change, using the shared window.gvDataTablesPinWarning
	 * port defined above. Also toggles the note under the legacy "Enable FixedColumns"
	 * checkbox: once any field carries a pin, field pins win and the checkbox is ignored.
	 */
	var pinWarningNotice = {

		PIN_SELECTOR: '#directory-active-fields select[name^="fields[directory_table-columns]"][name$="[dt_pin_column]"]',
		debounceTimer: null,
		uid: 0,

		init: function () {
			if ( 'undefined' === typeof GV_DataTables_Admin || ! GV_DataTables_Admin.pin_warning ) {
				return; // Localized string missing; nothing safe to render.
			}

			$( 'body' )
				.on( 'change', pinWarningNotice.PIN_SELECTOR, pinWarningNotice.scheduleRecompute )
				.on(
					'gravityview/field-added gravityview/field-removed gravityview/all-fields-removed',
					pinWarningNotice.scheduleRecompute
				)
				.on( 'sortupdate', '#directory-active-fields .active-drop', pinWarningNotice.scheduleRecompute );

			pinWarningNotice.recompute();
		},

		scheduleRecompute: function () {
			clearTimeout( pinWarningNotice.debounceTimer );
			pinWarningNotice.debounceTimer = setTimeout( pinWarningNotice.recompute, 150 );
		},

		recompute: function () {
			var $selects = $( pinWarningNotice.PIN_SELECTOR );
			var pins     = $selects.map( function () { return $( this ).val() || ''; } ).get();
			var ineffective = window.gvDataTablesPinWarning.computeIneffectivePins( pins );

			$selects.each( function ( index ) {
				var $select = $( this );

				if ( ineffective[ index ] ) {
					pinWarningNotice.show( $select );
				} else {
					pinWarningNotice.clear( $select );
				}
			} );

			pinWarningNotice.toggleLegacyNote( pins.some( function ( pin ) { return '' !== pin; } ) );
		},

		getNoticeEl: function ( $select ) {
			var $existing = $select.next( '.gv-dt-pin-warning' );

			if ( $existing.length ) {
				return $existing;
			}

			var $notice = $(
				'<p class="gv-dt-pin-warning" id="gv-dt-pin-warning-' + ( pinWarningNotice.uid++ ) + '" role="status" aria-live="polite" hidden></p>'
			);

			$select.after( $notice );

			return $notice;
		},

		show: function ( $select ) {
			var $notice = pinWarningNotice.getNoticeEl( $select );

			$notice.text( GV_DataTables_Admin.pin_warning ).removeAttr( 'hidden' );

			var tokens = ( $select.attr( 'aria-describedby' ) || '' ).split( /\s+/ ).filter( Boolean );

			if ( -1 === tokens.indexOf( $notice.attr( 'id' ) ) ) {
				tokens.push( $notice.attr( 'id' ) );
			}

			$select.attr( 'aria-describedby', tokens.join( ' ' ) );
		},

		clear: function ( $select ) {
			var $notice = $select.next( '.gv-dt-pin-warning' );

			if ( ! $notice.length ) {
				return;
			}

			$notice.attr( 'hidden', 'hidden' ).text( '' );

			var noticeId = $notice.attr( 'id' );
			var tokens   = ( $select.attr( 'aria-describedby' ) || '' )
				.split( /\s+/ )
				.filter( Boolean )
				.filter( function ( token ) { return token !== noticeId; } );

			if ( tokens.length ) {
				$select.attr( 'aria-describedby', tokens.join( ' ' ) );
			} else {
				$select.removeAttr( 'aria-describedby' );
			}
		},

		toggleLegacyNote: function ( anyPinExists ) {
			var $note = $( '#gv-dt-legacy-fixedcolumns-note' );

			if ( ! $note.length ) {
				return;
			}

			if ( anyPinExists ) {
				$note.text( GV_DataTables_Admin.legacy_fixedcolumns_overridden ).removeAttr( 'hidden' );
			} else {
				$note.attr( 'hidden', 'hidden' ).text( '' );
			}
		},
	};

	// Changing to .on( 'ready' ) breaks for now; the checkbox toggling doesn't work.
	$(document).ready( function() {
		gvDataTablesExt.init();
	});
}(jQuery));

