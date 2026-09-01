/**
 * Custom js script loaded on Views frontend to set DataTables
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      https://www.gravitykit.com
 * @copyright Copyright 2014, Katz Web Services, Inc.
 *
 * @since 1.0.0
 *
 * globals jQuery, gvGlobals
 */

window.gvDTResponsive = window.gvDTResponsive || {};
window.gvDTFixedHeaderColumns = window.gvDTFixedHeaderColumns || {};

( function ( $ ) {
	/**
	 * Handle DataTables alert errors (possible values: alert, throw, none)
	 * @link https://datatables.net/reference/option/%24.fn.dataTable.ext.errMode
	 * @since 2.0
	 */
	$.fn.dataTable.ext.errMode = function ( settings, techNote, message ) {
		// The first Ajax request fires inside DataTable(), before any .on( 'error.dt' ) binding
		// exists, so that binding misses exactly the failure that matters. This hook is in
		// place before initialization. `oInit.instanceKey` is this table's own per-embed state
		// key (set on `options` at init), keyed by table instance rather than by View ID so two
		// embeds of the same View don't share one failure/retry state.
		var instanceKey = settings && settings.oInit ? settings.oInit.instanceKey : null;

		if ( ! instanceKey || ! gvDataTables.tables[ instanceKey ] ) {
			// Not a table this View owns: another plugin's DataTable, or ours before
			// gvDataTables.tables[ instanceKey ] is registered. Overwriting ext.errMode replaced
			// DataTables' own string-mode dispatch entirely, so silently returning here would
			// swallow that table's error instead of leaving it reported the way DataTables
			// itself would by default.
			alert( message );

			return;
		}

		gvDataTables.handleTableFailure( instanceKey, settings, message );
	};

	var gvDataTables = {
		tables: {},

		// Whether the shared client-side filter predicate has been registered (see ensureClientSideFilterAndSearch).
		clientSideSearchRegistered: false,

		// Absorbs cell borders and sub-pixel rounding when deciding whether a table really
		// overflows its container.
		TABLE_OVERFLOW_EPSILON_PX: 2,

		// Per-column allowance for DataTables' whole-pixel column sizing; see
		// ensureHorizontalScroll(), which must not read that rounding as a column to scroll to.
		// Measured at about half a pixel per column, so a whole one leaves headroom.
		COLUMN_ROUNDING_SLACK_PX: 1,

		/**
		 * Gives a table wider than the space it sits in somewhere to scroll.
		 *
		 * A table can outgrow its container without anything here asking it to. Under the
		 * browser's auto layout a column is never narrower than its own content, so a value with
		 * no break opportunity — an untruncated URL, a long reference code — sets a floor for its
		 * column, and enough such columns push the table past the container whatever `width` says.
		 *
		 * Nothing then contains it. The table is laid out inside `.dataTables_wrapper`, which
		 * neither scrolls nor clips, so what the visitor gets is decided by whichever ancestor
		 * happens to have an `overflow` of its own: GravityView's themed container sets
		 * `overflow: hidden`, which cuts the far columns off with no way to reach them, and
		 * without that rule the table pushes the whole page sideways instead. Both are the same
		 * defect — a table with no scroll surface of its own.
		 *
		 * So the surface is given here rather than left to the page. Wrapping the table alone,
		 * rather than the whole DataTables wrapper, keeps the search box, pagination and export
		 * buttons in place while the table scrolls under them.
		 *
		 * Applied only when the table actually overflows, so a View that fits is untouched and
		 * gains no scrollbar. Once wrapped the table keeps its wrapper: the wrapper shows a
		 * scrollbar only while there is something to scroll, so a later draw that fits needs no
		 * undoing, and removing it would move the table under a visitor mid-session.
		 *
		 * @since 3.12.0
		 * @param {jQuery} $table The initialized <table>.
		 * @param {object} options The options object passed to .DataTable().
		 */
		ensureHorizontalScroll: function ( $table, options ) {
			// Responsive collapses columns to fit the container instead of scrolling past it.
			if ( options.responsive ) {
				return;
			}

			var table = $table.get( 0 );

			if ( ! table || ! table.parentNode ) {
				return;
			}

			// Already has a surface: either its own wrapper from an earlier pass, or
			// `.dataTables_scrollBody`, which scrollX gives it and which scrolls already. A
			// second one inside either would nest scrollbars.
			if ( table.parentNode.classList.contains( 'gv-dt-overflow' ) || table.closest( '.dataTables_scrollBody' ) ) {
				return;
			}

			var available = table.parentNode.clientWidth;

			if ( ! available ) {
				return;
			}

			// DataTables assigns each column a whole number of pixels and then writes their sum as
			// the table's width, so a table sized to fit still lands fractionally over: measured
			// at 1px for two columns and 3px for six, i.e. about half a pixel per column. Treating
			// that as overflow would put a permanent scrollbar under every ordinary View, so the
			// budget grows with the column count and only a table past it has a column to reach.
			var columnCount = table.querySelectorAll( 'thead tr:last-child th' ).length;
			var slack = Math.max( gvDataTables.TABLE_OVERFLOW_EPSILON_PX, columnCount * gvDataTables.COLUMN_ROUNDING_SLACK_PX );

			if ( table.getBoundingClientRect().width <= available + slack ) {
				return;
			}

			var wrapper = document.createElement( 'div' );

			wrapper.className = 'gv-dt-overflow';
			table.parentNode.insertBefore( wrapper, table );
			wrapper.appendChild( table );
		},

		/**
		 * A number for a "num" column's cell, which was written to be read rather than compared.
		 *
		 * DataTables preformats a "num" cell with `d * 1` and then compares the results with `<`
		 * and `>` (jquery.dataTables.js, `__numericReplace` and `string-asc`). `"$3,053.00" * 1`
		 * is NaN, every NaN comparison answers false, and Array.sort is stable, so the column
		 * takes its arrow and not one row moves. Three kinds of value arrive that way -- a clock
		 * reading, a Number field carrying its display format, and a date whose shadow timestamp
		 * the server could not resolve -- and each is read here instead.
		 *
		 * Which reading applies is decided by the column, never by the look of the value: the same
		 * "1,234" is one and a bit in one Number Format and one thousand in another, and
		 * Date.parse() answers "$1,011.00" with a day in 2000.
		 *
		 * @since 3.13.1
		 *
		 * @param {*}      value  The shadow value, or the rendered cell text where there is none.
		 * @param {object} column The column definition.
		 *
		 * @return {number|*} A number, or the value unchanged where it already is one.
		 */
		numericSortValue: function ( value, column ) {
			// A column that fixes its decimal separator outranks Number(): under Decimal
			// Comma "3.053" is three thousand and change, while Number() reads it as three.
			const separatorIsFixed = 'number' === column.field_type &&
				( ',' === column.decimal_separator || '.' === column.decimal_separator );

			// Number() (not parseFloat) is what decides this: parseFloat("2024-05-01") reads as
			// 2024, so a date would never reach the readings below.
			if ( ! separatorIsFixed && ! isNaN( Number( value ) ) ) {
				return value;
			}

			let reading;

			if ( 'time' === column.field_type ) {
				reading = gvDataTables.parseClockTime( value );
			} else if ( 'number' === column.field_type ) {
				reading = gvDataTables.parseFormattedNumber( value, column.decimal_separator );
			} else {
				// A date column, or one a filter gave the "num" type. Only a date reading is
				// tried: a day-first "31/01/2024" has none, and its digits taken as a number
				// would be ordered against the timestamps of the cells beside it that do read.
				reading = Date.parse( value );
			}

			// A cell with no reading joins the empty ones at the bottom rather than staying put.
			// DataTables answers an empty "num" cell with -Infinity, and one NaN among the
			// numbers answers false to every comparison, which unsettles the whole column.
			return isNaN( reading ) ? -Infinity : reading;
		},

		/**
		 * The number a formatted value writes, or the value unchanged where it writes none.
		 *
		 * A Number field carries its format into the cell -- "$3,053.00" under Currency, "0,125"
		 * under Decimal Comma -- and neither is a number to Number(). Decimal Comma sends its
		 * separator with the column, since those digits cannot say on their own whether the comma
		 * divides or groups. Currency sends none and is read here instead: the last separator is
		 * the decimal point unless exactly three digits follow it, which holds because every
		 * currency Gravity Forms carries groups in threes and writes at most two decimals.
		 * Accounting notation writes a negative in parentheses.
		 *
		 * @since 3.13.1
		 *
		 * @param {*}      value     The rendered value.
		 * @param {string} [decimal] The column's decimal separator, ',' where one is fixed.
		 *
		 * @return {number} The number it writes, or NaN.
		 */
		parseFormattedNumber: function ( value, decimal ) {
			const text = String( value ).trim();
			// Everything a currency puts around the digits comes off, including the four
			// thousands separators Gravity Forms' own currencies use: "," "." " " and "'".
			const digits = text.replace( /[^\d.,]/g, '' );

			if ( ! /\d/.test( digits ) ) {
				return NaN;
			}

			let normalized;

			if ( '.' === decimal || ',' === decimal ) {
				const grouping = '.' === decimal ? ',' : '.';

				normalized = digits.split( grouping ).join( '' ).split( decimal ).join( '.' );
			} else {
				const trailing = digits.match( /[.,](\d+)$/ );

				if ( trailing && 3 !== trailing[ 1 ].length ) {
					const separatorAt = digits.length - trailing[ 1 ].length - 1;

					normalized = digits.slice( 0, separatorAt ).replace( /[.,]/g, '' ) + '.' + trailing[ 1 ];
				} else {
					normalized = digits.replace( /[.,]/g, '' );
				}
			}

			const number = Number( normalized );

			if ( isNaN( number ) ) {
				return NaN;
			}

			// Gravity Forms writes every negative amount with a leading minus, whatever the
			// currency ("-$50.00", "-50,00 €"); the parentheses are accounting notation a
			// template can produce instead.
			const isNegative = /^\(.*\)$/.test( text ) || 0 === text.indexOf( '-' );

			return isNegative ? -number : number;
		},

		/**
		 * Seconds since midnight for a clock reading, or NaN where the value is not one.
		 *
		 * A Time field renders "01:30 PM", which compares as text ahead of "08:05 AM" -- the
		 * afternoon before the morning. Both the 12- and 24-hour formats are read, since the
		 * field's own setting decides which one the cell holds. Anything else declines: a
		 * meridiem alongside an hour outside 1-12, or a cell carrying more than the reading, and
		 * guessing at either would order the column by a time nothing holds.
		 *
		 * @since 3.13.1
		 *
		 * @param {*} value The rendered value.
		 *
		 * @return {number} Seconds since midnight, or NaN.
		 */
		parseClockTime: function ( value ) {
			// Anchored at both ends. Left open, the optional meridiem quietly matches nothing
			// where it cannot match all of itself -- "01:30 p. m." and "9:30 PMX" then read as
			// half past one in the MORNING rather than declining.
			const parts = String( value ).trim().match( /^(\d{1,2}):(\d{2})(?::(\d{2}))?(?:\s*([AaPp])\.?\s?[Mm]\.?)?$/ );

			if ( ! parts ) {
				return NaN;
			}

			let hours = parseInt( parts[ 1 ], 10 );
			const minutes = parseInt( parts[ 2 ], 10 );
			const seconds = parts[ 3 ] ? parseInt( parts[ 3 ], 10 ) : 0;
			const meridiem = parts[ 4 ] ? parts[ 4 ].toLowerCase() : '';

			if ( minutes > 59 || seconds > 59 ) {
				return NaN;
			}

			if ( ! meridiem ) {
				return hours > 23 ? NaN : ( hours * 3600 ) + ( minutes * 60 ) + seconds;
			}

			if ( hours < 1 || hours > 12 ) {
				return NaN;
			}

			if ( 'p' === meridiem && 12 !== hours ) {
				hours += 12;
			}

			if ( 'a' === meridiem && 12 === hours ) {
				hours = 0;
			}

			return ( hours * 3600 ) + ( minutes * 60 ) + seconds;
		},

		/**
		 * Initialize DataTables field filters.
		 *
		 * @since {2.7}
		 * @param {DataTables.Api} datatable {@see https://datatables.net/reference/api/}
		 * @param {object} settings {@see https://datatables.net/reference/type/DataTables.Settings}
		 */
		setUpFieldFilters: function ( datatable, settings ) {

			var field_filters_location = datatable.init().field_filters;

			if ( !field_filters_location ) {
				return;
			}

			// Filters are already initialized. Check the whole table container, not just the
			// header: field_filters_location can be 'footer', where a header-only check never
			// matches and re-runs the whole builder on every 'responsive-resize'.
			if ( $( datatable.table().container() ).find( '.gv-dt-field-filter' ).length ) {
				gvDataTables.setUpClearFiltersButton( datatable );
				gvDataTables.setUpSearchBarClear( datatable );

				return;
			}

			const throttledSearch = $.fn.dataTable.util.throttle( function ( table, val ) {
				if ( settings.oInit.serverSide ) {
					table.search( val ).draw();
				} else {
					table.draw();
				}
			}, 200 );

			datatable.columns().every( function ( index ) {
				var column = settings.aoColumns[ index ];
				var that = this;
				var input;

				if ( !column.searchable ) {
					return;
				}

				input = $( '<input/>' )
					.attr( 'type', column.atts.type )
					.attr( 'placeholder', column.atts.placeholder )
					.attr( 'min', ( column.atts.min || null ) )
					.attr( 'max', ( column.atts.max || null ) )
					.attr( 'step', ( column.atts.step || null ) );

				if ( [ 'select', 'chainedselect', 'checkbox', 'multiselect' ].includes( column.atts.field_type ) && column.atts.options ) {
					input = $( '<select></select>' )
						.append( $( '<option>' )
							.val( '' )
							.text( column.atts.placeholder || '' )
						);

					var options;

					try {
						options = JSON.parse( column.atts.options );
					} catch ( e ) {
						console.log( e );
						return;
					}

					$.each( options, function ( d, j ) {
						input.append( $( '<option>' )
							.val( $( '<div />' ).html( j.value ).text() )
							.text( $( '<div />' ).html( j.text || j.label ).text() )
						);
					} );
				} else if ( 'date_range' === column.atts.field_type ) {
					input = $('<div/>').addClass('date-input-wrapper');

					const type = 'date';

					const fromDateInput = $( '<input/>' )
						.attr( 'type', type )
						.attr( 'title', column.atts.from_date_title )
						.attr( 'min', ( column.atts.min || null ) )
						.attr( 'max', ( column.atts.max || null ) )
						.attr( 'step', ( column.atts.step || null ) );

					const toDateInput = $( '<input/>' )
						.attr( 'type', type )
						.attr( 'title', column.atts.to_date_title )
						.attr( 'min', ( column.atts.min || null ) )
						.attr( 'max', ( column.atts.max || null ) )
						.attr( 'step', ( column.atts.step || null ) );

					input.append(fromDateInput).append(toDateInput);
				} else if ( 'date' === column.atts.field_type ) {
					input.removeAttr( 'placeholder' ).attr( 'title', column.atts.title );
				}

				// A filter is only as wide as its column, so a label like "Filter by Start Date"
				// is truncated on sight, and a <select> shows only as much of its placeholder
				// option as fits. Mirroring the full text into `title` keeps it reachable by
				// pointer and by assistive tech. Date filters set their own title above, and the
				// date-range wrapper is a <div> whose two inputs carry theirs.
				var is_date_range_wrapper = input.hasClass( 'date-input-wrapper' );

				// `gravityview/datatables/field_filters/atts` can put anything here, and a
				// non-string would throw on .replace() and abort the whole filter build.
				var placeholder  = 'string' === typeof column.atts.placeholder ? column.atts.placeholder : '';
				var filter_title = placeholder.replace( /^—\s*/, '' ).replace( /\s*—$/, '' );

				if ( filter_title && ! is_date_range_wrapper && ! input.attr( 'title' ) ) {
					input.attr( 'title', filter_title );
				}

				// The column's live search wins over init-time saved state: a rebuild that runs
				// after the visitor already typed something (a legitimate Responsive re-attach)
				// must not regress to whatever the table started with.
				var input_search_value = that.search() || ( settings.oSavedState?.columns?.[ index ]?.search?.search ?? '' );

				input.val( input_search_value );

				$( input )
					.addClass( column.atts.class )
					.attr( 'data-uid', column.atts.uid ) // used to sync header and footer values

					// Prevent clicks inside header inputs from sorting the column
					.on( 'click', function ( e ) {
						e.stopPropagation();
					} )
					/*.on('keypress.DT keyup.DT input.DT paste.DT cut.DT change.DT clear.DT', function ( e ) {

					})*/
					.on( 'keydown', function ( e ) {
						if ( e.metaKey || e.ctrlKey ) {
							gvDataTables.cmdOrCtrlPressed = 'keydown';
						}
					} )
					.on( 'keyup', function ( e ) {
						gvDataTables.cmdOrCtrlPressed = false;
					} )
					.on( 'keydown keyup', function ( e ) {
						var keyCode = e.keyCode || e.which;

						// Don't submit the form if the user presses Enter (this will sort the column and the filters are submitted per-keypress already)
						if ( 13 === keyCode ) {
							e.preventDefault();
							return false;
						}

						// Manually select the text in the input field if the user presses Command+A (Mac) or Ctrl+A (Windows/Linux)
						if ( 'a' === e.key && gvDataTables.cmdOrCtrlPressed ) {
							$( this )[ 0 ].select();
						}

						return true;
					} )
					.on( 'keyup.DT input.DT paste.DT cut.DT change.DT clear', function ( e ) {
						var keyCode = e.keyCode || e.which;

						// Control, command, arrows, page up/down
						var ignore_keys = [
							13, // Return
							16, // Shift
							17, // Ctrl
							18, // Alt
							33, // Page up
							34, // Page down
							35, // End
							36, // Home
							37, // Left
							38, // Up
							39, // Right
							40, // Down
							91, // Command (Left)
							93, // Command (Right)
						];

						// Function keys
						var is_function_keys = ( keyCode < 130 && keyCode > 112 );

						if ( -1 !== ignore_keys.indexOf( keyCode ) || is_function_keys ) {
							return true;
						}

						if ( $( this ).hasClass( 'date-input-wrapper' ) ) {
							var inputPosition = $( e.target ).closest( '.date-input-wrapper' ).find( 'input' ).index( e.target );

							$( this )
								.parents( 'table.gv-datatables' )
								.find( '.gv-dt-field-filter[data-uid=' + $( this ).data( 'uid' ) + ']' )
								.find( 'input:eq(' + inputPosition + ')' )
								.val( e.target.value );
						} else {
							$( this )
								.parents( 'table.gv-datatables' )
								.find( '.gv-dt-field-filter[data-uid=' + $( this ).data( 'uid' ) + ']' )
								.val( this.value );
						}

						if ( !settings.oInit.serverSide ) {
							throttledSearch( that, this.value );

							return;
						}

						if ( that.search() !== this.value ) {
							throttledSearch( that, this.value );
						}
					} );

				if ( 'both' === field_filters_location ) {
					$( input )
						.appendTo( $( that.footer() ).empty() )
						.clone( true )
						.appendTo( $( that.header() ) );
				} else if ( 'header' === field_filters_location ) {
					$( input ).appendTo( $( that.header() ) );
				} else {
					$( input ).appendTo( $( that.footer() ).empty() );
				}
			} );

			gvDataTables.setUpClearFiltersButton( datatable );
			gvDataTables.setUpSearchBarClear( datatable );
			gvDataTables.syncFooterColumnWidths( datatable );
			gvDataTables.applyFieldFilterScrollMargins( datatable );
			gvDataTables.setUpFooterScrollMirror( datatable );

			// FixedColumns re-measures pin widths on 'column-sizing' (e.g. Responsive/window
			// resize), and DataTables redraws move data but not layout; either can change how
		},

		/**
		 * Sizes `scroll-padding` on the scroll containers (head/body/foot) so a scroll-into-view
		 * (click-actionability, `scrollIntoViewIfNeeded()`) or a focus-driven auto-scroll both
		 * decides a scroll IS needed and lands clear of FixedColumns' sticky pin cells.
		 *
		 * `scroll-margin` on the target element only, was tried first and measured insufficient:
		 * it shapes where a scroll LANDS, but the browser decides WHETHER to scroll at all from
		 * the target's plain (un-inflated) rect against the container's client rect, ignoring
		 * scroll-margin entirely. A footer input whose plain rect already sits inside the
		 * container — true for any input a sticky pin merely paints over rather than pushes out
		 * of the client rect, which is exactly this defect's shape — was never judged as needing
		 * a scroll, so the margin never took effect. `scroll-padding` on the container is the
		 * property the necessity decision itself reads (the container's "optimal viewing
		 * region"), which is why it has to live here and not on the filter elements. Confirmed
		 * against `scrollIntoViewIfNeeded()`, standard `scrollIntoView()`, and `focus()` alike.
		 *
		 * Static CSS cannot do this either: the padding has to equal the pinned cells' rendered
		 * width, which depends on their content.
		 *
		 * @since 3.11.1
		 *
		 * @param {DataTables.Api} datatable
		 *
		 * @return {void}
		 */
		/**
		 * Keeps the scroll head and foot sized to the body, for the life of the table.
		 *
		 * Scrolling gives each of the three a table of its own, and only the body's is sized by
		 * the data, so the other two are given its widths (see syncScrollColumnWidths) and put on
		 * fixed layout to make them obey. Every event that can change a column's width has to
		 * reach this, or the head keeps a width the body no longer has.
		 *
		 * @since 3.13.0
		 *
		 * @param {object} datatable The DataTables API instance.
		 *
		 * @return {void}
		 */
		setUpScrollColumnSync: function ( datatable ) {
			var $container = $( datatable.table().container() );

			// Nothing to keep together on a table DataTables did not split.
			if ( ! $container.find( '.dataTables_scrollHead' ).length ) {
				return;
			}

			var sync = function () {
				gvDataTables.syncFooterColumnWidths( datatable );
				gvDataTables.applyFieldFilterScrollMargins( datatable );
			};

			// A visibility toggle changes how many columns share the width, Responsive collapses
			// them with CSS, and FixedColumns re-measures its pin strips on 'column-sizing'.
			var layoutEvents = [ 'draw', 'column-sizing', 'column-visibility', 'responsive-resize' ]
				.map( function ( name ) {
					return name + '.dt.gvScrollColumnSync';
				} )
				.join( ' ' );

			datatable.off( layoutEvents ).on( layoutEvents, sync );

			// A resize re-lays the body but reaches none of those: DataTables binds its own
			// resize handler only for a table with `scrollX` or a width attribute, and
			// 'responsive-resize' only exists when Responsive is on. Without this, a phone turned
			// on its side leaves the head and foot at the width they had before, permanently.
			// Per table, since two Views on one page each need their own.
			var resizeTimer = null;
			var resizeEvent = 'resize.gvColumnWidths' + ( datatable.settings()[ 0 ].sTableId || '' ).replace( /[^\w]/g, '' );

			$( window ).off( resizeEvent ).on( resizeEvent, function () {
				clearTimeout( resizeTimer );

				// Long enough for a drag to settle: the widths come from the body, and reading
				// them mid-drag only produces an answer the next frame throws away.
				resizeTimer = setTimeout( sync, 150 );
			} );

			// The body settles after the draw for reasons no DataTables event reports: a web font
			// arriving re-measures every value, landing the table ~200ms later at a width the head
			// was never told about. Its own box is the one thing any such cause has to move.
			var bodyTable = $container.find( '.dataTables_scrollBody table' )[ 0 ];
			var settings = datatable.settings()[ 0 ];
			var queuedFrame = null;
			var destroyed = false;

			// Everything bound above outlives the table otherwise: the resize handler is on
			// window, and its timer and the queued frame both reach a table that is gone.
			datatable.off( 'destroy.dt.gvScrollColumnSync' ).on( 'destroy.dt.gvScrollColumnSync', function () {
				destroyed = true;

				$( window ).off( resizeEvent );

				clearTimeout( resizeTimer );

				if ( queuedFrame ) {
					window.cancelAnimationFrame( queuedFrame );

					queuedFrame = null;
				}

				if ( settings.gvBodyResizeObserver ) {
					settings.gvBodyResizeObserver.disconnect();

					settings.gvBodyResizeObserver = null;
				}
			} );

			if ( bodyTable && window.ResizeObserver ) {
				if ( settings.gvBodyResizeObserver ) {
					settings.gvBodyResizeObserver.disconnect();
				}

				// A write here can reach the body, through the head and foot inner wrappers where
				// an ancestor sizes itself to its contents. It settles rather than looping: every
				// write is made only where the value differs, and the width being written is the
				// body's own.
				settings.gvBodyResizeObserver = new ResizeObserver( function () {
					// Deliberately not gated on the table's own width: a table held at
					// `width: 100%` keeps that width while its columns redistribute underneath,
					// and those are the widths the head has to follow. The sync writes only where
					// a value actually changed, so an uneventful pass costs a measurement.
					if ( queuedFrame ) {
						return;
					}

					// Measuring inside the callback reads the layout that produced it, which is
					// the one the browser is still in the middle of.
					queuedFrame = window.requestAnimationFrame( function () {
						queuedFrame = null;

						sync();
					} );
				} );

				settings.gvBodyResizeObserver.observe( bodyTable );
			}

			// The observer sees a font swap only through the box it moves, and a swap that
			// changes glyph widths without changing any row's height moves none. Fonts settling
			// is the reported trigger, so it is waited on by name rather than through its effects.
			if ( document.fonts && document.fonts.ready && document.fonts.ready.then ) {
				document.fonts.ready.then( function () {
					if ( destroyed ) {
						return;
					}

					sync();
				} );
			}

			sync();
		},

		/**
		 * Gives the scroll head and foot the column widths the body resolved.
		 *
		 * Scrolling lays head, body and foot out as three separate tables, and a View with
		 * configured widths turns DataTables' own sizing off, so each of the three is left to
		 * size itself. They do not agree: a header cell reserves room for its sort arrows and
		 * its filter control, a footer cell does not, and a heading is not the value beneath it.
		 *
		 * The body is the reference because it is the only one of the three sized purely by the
		 * data. Handing its widths to the other two removes the second layout pass from the
		 * answer.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} datatable The DataTables API instance.
		 *
		 * @return {void}
		 */
		syncFooterColumnWidths: function ( datatable ) {
			// Taken from DataTables' own per-column cell references rather than the footer's
			// first row: footer calculations can prepend a row above the filters, and a
			// row-position guess would then size the wrong cells or bail on the count.
			var headers = datatable.columns().header().toArray();
			var footers = datatable.columns().footer().toArray();

			if ( ! headers.length || headers.length !== footers.length ) {
				return;
			}

			var $container = $( datatable.table().container() );
			// Resolved from the container, not from the header cells: FixedHeader moves the real
			// thead into a floating table on the body, so while the header is stuck to the top of
			// the screen those cells lead out of the scroll surface and the sync found no table
			// to write to at all.
			var headTable = $container.find( '.dataTables_scrollHead table' )[ 0 ] || null;
			var footTable = $container.find( '.dataTables_scrollFoot table' )[ 0 ] || null;

			// Nothing to align on a table DataTables did not split.
			if ( ! headTable || ! footTable ) {
				return;
			}

			var widths = [];
			var rendered = 0;
			// The first row that actually spans one cell per column. A RowGroup heading, and the
			// "no matching records" row a zero-result search leaves behind, are both a single
			// colspan cell and would hand column one the width of the whole table. Cells with no
			// box are Responsive's collapsed columns, which stay in the DOM.
			var bodyCells = [];

			// How many columns actually occupy the scroll surface. A body row is the reference
			// only if it has one visible cell for each of them: matching against its own child
			// count instead would reject every row on a Responsive table, where a collapsed
			// column keeps its cells in the DOM with no box, and fall back to header widths.
			var occupied = headers.filter( function ( head, index ) {
				return head && footers[ index ] && head.getClientRects().length;
			} ).length;

			$container.find( '.dataTables_scrollBody tbody tr' ).each( function () {
				var cells = $( this ).children().filter( function () {
					return ! this.hasAttribute( 'colspan' ) && this.getClientRects().length;
				} ).toArray();

				if ( cells.length && cells.length === occupied ) {
					bodyCells = cells;

					return false;
				}
			} );

			headers.forEach( function ( head, index ) {
				var foot = footers[ index ];

				if ( ! head || ! foot ) {
					return;
				}

				// A column with no box is one Responsive has collapsed, or one DataTables has
				// hidden outright: it holds no width worth copying, and counting it would keep a
				// <col> the grid no longer has.
				if ( ! head.getClientRects().length ) {
					return;
				}

				// Read from the rendered row rather than DataTables' cached node references: a
				// server-side draw replaces the row, leaving the cached node detached and
				// measuring the width it had before. An empty table has no row at all, and the
				// header is then the only resolved width there is.
				var bodyCell = bodyCells[ rendered ];
				var reference = bodyCell && bodyCell.getClientRects().length ? bodyCell : head;

				++rendered;

				// Every width is read before any is written: writing into the same layout being
				// measured would make each column's answer depend on the ones before it.
				widths.push( reference.getBoundingClientRect().width );
			} );

			if ( ! widths.length || ! footTable ) {
				return;
			}

			// DataTables writes a pixel width on the head/foot tables and their inner wrappers at
			// init and never revises it, so after a resize fixed layout shares a stale surplus
			// across the columns however right the colgroup is. Both are written because either
			// can govern: a `table.dataTable { width: 100% !important }` rule, which GravityView's
			// own default style carries, outranks the inline width and leaves the wrapper in
			// charge; without one the inline width wins.
			var bodyTable = $container.find( '.dataTables_scrollBody table' )[ 0 ];
			var bodyWidth = bodyTable ? bodyTable.getBoundingClientRect().width + 'px' : '';

			[ headTable, footTable ].forEach( function ( table ) {
				if ( ! table || ! bodyWidth ) {
					return;
				}

				var inner = table.parentElement;

				if ( inner && inner.style.width !== bodyWidth ) {
					inner.style.width = bodyWidth;
				}

				if ( table.style.width !== bodyWidth ) {
					table.style.width = bodyWidth;
				}
			} );

			gvDataTables.applyColumnWidthGrid( headTable, widths );
			gvDataTables.applyColumnWidthGrid( footTable, widths );
		},

		/**
		 * Sizes one scroll section's columns from a <colgroup>.
		 *
		 * Carried on a <colgroup> rather than on the cells: a section's first row is not
		 * dependable, since footer calculations can sit above the filters and either row is
		 * customizable enough to merge cells, and a merged width says nothing about the columns
		 * it spans.
		 *
		 * @since 3.13.0
		 *
		 * @param {HTMLTableElement} table  The scroll section's table.
		 * @param {Array<number>}    widths Resolved width per rendered column, in DOM order.
		 *
		 * @return {void}
		 */
		applyColumnWidthGrid: function ( table, widths ) {
			if ( ! table ) {
				return;
			}

			var colgroup = table.querySelector( ':scope > colgroup.gv-dt-footer-widths' );

			if ( ! colgroup ) {
				colgroup = document.createElement( 'colgroup' );
				colgroup.className = 'gv-dt-footer-widths';
			}

			// Kept ahead of the sections on every pass, not only when created: something moves it
			// behind the thead, and a trailing colgroup is honoured by Blink and Gecko but is not
			// guaranteed to be.
			if ( colgroup !== table.firstChild ) {
				table.insertBefore( colgroup, table.firstChild );
			}

			while ( colgroup.children.length > widths.length ) {
				colgroup.removeChild( colgroup.lastChild );
			}

			while ( colgroup.children.length < widths.length ) {
				colgroup.appendChild( document.createElement( 'col' ) );
			}

			widths.forEach( function ( width, index ) {
				if ( ! width ) {
					return;
				}

				var next = width.toFixed( 3 ) + 'px';
				var col = colgroup.children[ index ];

				// Skipped when unchanged, so a redraw does not dirty the layout for nothing.
				if ( col.style.width !== next ) {
					col.style.width = next;
				}
			} );
		},

		/**
		 * Keeps an empty split header readable without changing the loaded table's layout.
		 *
		 * The scroll head normally needs fixed layout to obey the body's pixel grid. Before a
		 * server-side response arrives, however, there is no body grid and fixed layout ignores
		 * the header cells' 60px minimums. Use automatic layout only for that empty interval (and
		 * for a genuinely empty result), then restore the fixed rule as soon as a data row exists.
		 *
		 * @since 3.13.0
		 *
		 * @param {DataTables.Api} datatable
		 *
		 * @return {void}
		 */
		setUpEmptyScrollHeadLayout: function ( datatable ) {
			var syncLayout = function () {
				var $container = $( datatable.table().container() );
				var headTable = $container.find( '.dataTables_scrollHead table' )[ 0 ];

				if ( ! headTable ) {
					return;
				}

				var hasDataRow = $container.find( '.dataTables_scrollBody tbody tr' ).first()
					.children().not( '.dataTables_empty' ).length > 0;

				headTable.style.tableLayout = hasDataRow ? '' : 'auto';
			};

			datatable.off( 'draw.dt.gvEmptyScrollHeadLayout' )
				.on( 'draw.dt.gvEmptyScrollHeadLayout', syncLayout );

			syncLayout();
		},

		applyFieldFilterScrollMargins: function ( datatable ) {
			var $container = $( datatable.table().container() );

			$container.find( '.dataTables_scrollFoot, .dataTables_scrollHead, .dataTables_scrollBody' ).each( function () {
				var $scroller = $( this );

				// The padding wanted is the width of the pin strip, which is a property of the
				// columns, not of the table's length. Summing every pinned cell in the scroller
				// multiplies it by the row count -- harmless for the single-row head and foot,
				// but the body reached 2229px for an 89px strip at 25 rows, so anything scrolled
				// or focused into view there overshot far past its column.
				var $pinnedRow = $scroller.find( 'tr' ).filter( function () {
					return $( this ).children( '.dtfc-fixed-left, .dtfc-fixed-right' ).length > 0;
				} ).first();

				var pinLeftWidth = 0;
				var pinRightWidth = 0;

				$pinnedRow.children( 'th.dtfc-fixed-left, td.dtfc-fixed-left' ).each( function () {
					pinLeftWidth += this.getBoundingClientRect().width;
				} );

				$pinnedRow.children( 'th.dtfc-fixed-right, td.dtfc-fixed-right' ).each( function () {
					pinRightWidth += this.getBoundingClientRect().width;
				} );

				this.style.scrollPaddingLeft  = pinLeftWidth ? pinLeftWidth + 'px' : '';
				this.style.scrollPaddingRight = pinRightWidth ? pinRightWidth + 'px' : '';
			} );
		},

		/**
		 * Drags the scroll head and foot along when the body scrolls sideways.
		 *
		 * DataTables splits the table into head/body/foot scroll containers whenever `scrollY`
		 * is set, which Scroller does, but binds the handler that keeps them together only when
		 * `scrollX` is set too (`_fnScrollingContainer`). A View using Scroller without pinned
		 * columns therefore gets a body wide enough to scroll sideways past a head and foot that
		 * never move, leaving every heading over the wrong column.
		 *
		 * @since 3.13.0
		 *
		 * @param {DataTables.Api} datatable
		 *
		 * @return {void}
		 */
		setUpHorizontalScrollSync: function ( datatable ) {
			const settings = datatable.settings()[ 0 ];

			// DataTables binds this itself when scrollX is on, and a second handler writing the
			// same value would only cost a layout read per scroll event. `sX` is '' when off and
			// '100%' when on, so falsy is exactly "DataTables did not bind".
			if ( ! settings || ! settings.oScroll || settings.oScroll.sX ) {
				return;
			}

			// The nodes DataTables recorded when it built the scroll layout, rather than a class
			// lookup that another plugin's markup could answer.
			const scrollBody = settings.nScrollBody;
			const scrollHead = settings.nScrollHead;
			const scrollFoot = settings.nScrollFoot;

			if ( ! scrollBody || ! scrollHead ) {
				return;
			}

			$( scrollBody ).off( 'scroll.gvHeadSync' ).on( 'scroll.gvHeadSync', function () {
				scrollHead.scrollLeft = this.scrollLeft;

				if ( scrollFoot ) {
					scrollFoot.scrollLeft = this.scrollLeft;
				}
			} );
		},

		/**
		 * Mirrors the footer scroll container's position onto the body.
		 *
		 * DataTables already mirrors the scroll body's position onto the head and foot, but
		 * nothing mirrors the other direction. A footer input's own focus/scroll-into-view
		 * scroll moves only its nearest scrolling ancestor, the foot, not the body, which
		 * otherwise leaves the filter row visibly desynced from the columns it filters until
		 * the visitor scrolls the body again.
		 *
		 * @since 3.11.1
		 *
		 * @param {DataTables.Api} datatable
		 *
		 * @return {void}
		 */
		setUpFooterScrollMirror: function ( datatable ) {
			var $container  = $( datatable.table().container() );
			var scrollBody  = $container.find( '.dataTables_scrollBody' )[ 0 ];
			var scrollFoot  = $container.find( '.dataTables_scrollFoot' )[ 0 ];

			if ( ! scrollBody || ! scrollFoot ) {
				return;
			}

			$( scrollFoot ).off( 'scroll.gvFieldFilterSync' ).on( 'scroll.gvFieldFilterSync', function () {
				if ( scrollBody.scrollLeft !== scrollFoot.scrollLeft ) {
					scrollBody.scrollLeft = scrollFoot.scrollLeft;
				}
			} );
		},

		/**
		 * Decides whether a failed request is worth retrying, or is the visitor's problem now.
		 *
		 * @since 3.11.0
		 *
		 * @param {string} instanceKey This table's own per-embed state key (see gvDataTables.init).
		 * @param {object} settings    The DataTables settings object for the failing table.
		 *
		 * @return {void}
		 */
		handleTableFailure: function ( instanceKey, settings, message ) {
			var state = gvDataTables.tables[ instanceKey ];

			// An error envelope was already handled for this response, so this warning is a
			// duplicate: DataTables logs one for the payload's own `error` key.
			if ( state.envelopeHandled ) {
				state.envelopeHandled = false;

				return;
			}

			var attempts = state.transportRetries || 0;

			if ( attempts < 2 ) {
				state.transportRetries = attempts + 1;

				window.setTimeout( function () {
					if ( state.table ) {
						state.table.ajax.reload( null, false );
					}
				}, 1 === state.transportRetries ? 500 : 1500 );

				return;
			}

			state.transportRetries = 0;

			var options = settings ? settings.oInit : {};

			// The request never reached the server, so there is no envelope and no server-built
			// diagnostics. DataTables' own tech note is all the detail that exists, and the
			// capability flag is the server's word on whether this visitor may see it.
			var payload = null;

			if ( options.canSeeDiagnostics && message ) {
				payload = { diagnostics: { message: String( message ) } };
			}

			gvDataTables.renderFailureState( instanceKey, options, payload );
		},

		/**
		 * Replaces the eternal "Loading data…" spinner with a message a visitor can act on.
		 *
		 * Diagnostics are appended only when the server sent them, and the server sends them
		 * only to administrators, so this cannot leak them to a visitor.
		 *
		 * @since 3.11.0
		 *
		 * @param {string} instanceKey This table's own per-embed state key (see gvDataTables.init).
		 * @param {object} options     The DataTables init options, for its language strings.
		 * @param {object} payload     The decoded error envelope, when there was one.
		 */
		renderFailureState: function ( instanceKey, options, payload ) {
			var state = gvDataTables.tables[ instanceKey ];

			// Resolves the container from the table that actually failed, not by id: two embeds of
			// one View share the same "#gv-datatables-<id>" id (a template-level constraint this
			// file can't fix), so an id lookup always resolves to whichever embed rendered first.
			var $container = state && state.table ? $( state.table.table().container() ).closest( '.gv-datatables-container' ) : $();

			if ( ! $container.length ) {
				// No table reference yet (state not initialized) — fall back to the bare View id
				// from the instance key ("<viewId>:<index>"), same as before this table was keyed
				// per embed.
				$container = $( '#gv-datatables-' + instanceKey.split( ':' )[ 0 ] );
			}

			if ( ! $container.length ) {
				$container = $( '.gv-datatables-container' ).first();
			}

			var language = ( options && options.language ) || {};

			// Nothing to say means nothing to show: an empty role="alert" that steals focus is
			// strictly worse than the spinner it replaces.
			if ( ! language.loadError ) {
				return;
			}

			// The spinner would otherwise sit there forever behind the message.
			$container.find( '.dataTables_processing' ).hide();

			// "Showing 0 to 0 of 0 entries" and a pager both state a count the server never
			// returned. Restored by the response filter once a request succeeds.
			$container.find( '.dataTables_info, .dataTables_paginate' ).hide();

			var $existing = $container.find( '.gv-dt-error' );

			if ( $existing.length ) {
				$existing.remove();
			}

			var $error = $( '<div/>', {
				'class': 'gv-dt-error',
				role: 'alert',
				tabindex: '-1',
			} );

			$( '<p/>', { text: language.loadError } ).appendTo( $error );

			var $retry = $( '<button/>', {
				type: 'button',
				'class': 'gv-dt-retry',
				text: language.retry || language.loadError,
			} ).appendTo( $error );

			var diagnostics = payload && payload.diagnostics;
			var gvError     = payload && payload.gv_error;

			// Gated on diagnostics, which the server sends to administrators only. A visitor's
			// envelope carries a correlation id too, but it belongs in the console, not on screen.
			if ( diagnostics ) {
				var $details = $( '<details/>', { 'class': 'gv-dt-error-detail' } );

				$( '<summary/>', { text: language.errorDetails || '' } ).appendTo( $details );

				if ( gvError && gvError.correlation_id ) {
					$( '<p/>', { text: gvError.correlation_id } ).appendTo( $details );
				}

				// Which fields are present depends on how the response failed: a fatal carries
				// its message and origin, a polluted response carries the bytes it wrote, and a
				// refusal carries neither. Listing whatever arrived beats a fixed template that
				// renders "unknown: " for everything else.
				var detailLines = [];

				if ( diagnostics.message ) {
					detailLines.push( diagnostics.message );
				}

				if ( diagnostics.file ) {
					detailLines.push( diagnostics.file + ( diagnostics.line ? ':' + diagnostics.line : '' ) );
				}

				if ( diagnostics.suspected_source && 'unknown' !== diagnostics.suspected_source ) {
					detailLines.push( diagnostics.suspected_source );
				}

				if ( diagnostics.polluted_output ) {
					detailLines.push( diagnostics.polluted_output );
				}

				if ( diagnostics.code ) {
					detailLines.push( diagnostics.code );
				}

				if ( detailLines.length ) {
					// .text(), never .html(): this excerpt is page output an attacker may influence.
					$( '<pre/>' ).text( detailLines.join( '\n' ) ).appendTo( $details );
				}

				$details.appendTo( $error );

			}

			if ( ( gvError || diagnostics ) && window.console && window.console.warn ) {
				window.console.warn( 'GravityView DataTables could not load the table.', payload );
			}

			$retry.on( 'click', function () {
				$error.remove();

				var table = state && state.table;

				// A stale column signature is a property of this page, not of the request:
				// asking again sends the same signature and is refused again. Only a
				// re-rendered page can carry a current one.
				var needsRerender = payload && payload.gv_error && 'stale_config' === payload.gv_error.code;

				if ( table && table.ajax && ! needsRerender ) {
					table.ajax.reload();
				} else {
					window.location.reload();
				}
			} );

			$container.prepend( $error );

			// Only when the visitor is not already somewhere. A table can fail while someone is
			// mid-word in a filter, and taking the caret out of their input loses their place
			// for no gain: role="alert" is announced without focus having to move at all.
			var active      = document.activeElement;
			var isSomewhere = active && active !== document.body && active !== document.documentElement;

			if ( ! isSomewhere ) {
				$error.trigger( 'focus' );
			}
		},

		/**
		 * Trades a stale nonce for a fresh one, so a page cached past the nonce tick can recover
		 * instead of leaving its tables permanently stuck.
		 *
		 * @since 3.11.0
		 *
		 * @param {string}   instanceKey This table's own per-embed state key (see gvDataTables.init).
		 * @param {Function} done        Called with true when a fresh nonce was stored.
		 */
		refreshNonce: function ( instanceKey, done ) {
			var state = gvDataTables.tables[ instanceKey ];

            if ( ! state || ! state.data || state.nonceRefreshed ) {
				done( false );

				return;
			}

			// Once only: a second failure is not a stale nonce.
			state.nonceRefreshed = true;

			// The nonce endpoint lives at the same admin-ajax the table already posts to, so use
			// that URL rather than guessing one.
			var url = state.table && state.table.ajax ? state.table.ajax.url() : gvDataTables.getAjaxUrl();

			// A hung admin-ajax.php request (slow host, dropped connection) must still resolve
			// this, or a stale-nonce table waits on `done()` forever behind the spinner.
			$.ajax( { url: url, type: 'POST', data: { action: 'gv_datatables_nonce' }, timeout: 10000 } )
				.done( function ( response ) {
					var fresh = response && response.nonce;

					if ( ! fresh ) {
						done( false );

						return;
					}

					state.data.nonce = fresh;

					done( true );
				} )
				.fail( function () {
					done( false );
				} );
		},

		/**
		 * The admin-ajax endpoint the tables already post to.
		 *
		 * @since 3.11.0
		 *
		 * @return {string}
		 */
		getAjaxUrl: function () {
			if ( window.gvGlobals && window.gvGlobals.ajaxurl ) {
				return window.gvGlobals.ajaxurl;
			}

			return ( window.ajaxurl || '/wp-admin/admin-ajax.php' );
		},

		/**
		 * The DataTable belonging to a Search Bar.
		 *
		 * A View on the page twice renders two Search Bars carrying the same data-viewid and
		 * two containers carrying the same id, and `#id` resolves to the first of them, so
		 * neither attribute distinguishes one embed from the other. GravityView wraps each
		 * embed in its own "gv-view-<id>-<n>", which does.
		 *
		 * Markup is only read here, never changed: the ids and classes a site may be targeting
		 * with CSS stay exactly as they are, duplicate container id included.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} $form The Search Bar form.
		 *
		 * @return {object} The table element, empty when the View rendered none.
		 */
		resolveSearchBarTable: function ( $form ) {
			var viewId   = String( $form.attr( 'data-viewid' ) || '' );
			var selector = '[id^="gv-view-' + viewId + '-"]';
			var form     = $form.get( 0 );
			var embed    = null;

			if ( /^\d+$/.test( viewId ) && form && form.closest ) {
				embed = form.closest( selector );
			}

			if ( embed ) {
				// Ownership, not containment: a View embedded inside another embed of the same
				// View sits under this wrapper too, and belongs to its own Search Bar. An embed
				// that owns no table resolves to nothing rather than borrowing a sibling's.
				var $owned = $( embed ).find( '.gv-datatables' ).filter( function () {
					return this.closest( selector ) === embed;
				} );

				// Under horizontal scrolling the header is a second table, so prefer the body one.
				var $inScrollBody = $owned.filter( function () {
					return !! this.closest( '.dataTables_scrollBody' );
				} );

				return ( $inScrollBody.length ? $inScrollBody : $owned ).first();
			}

			// No wrapper of this View's own: the container-id lookup this used before, which is
			// still right for the single embed it was written for.
			var $scope = $( '#gv-datatables-' + viewId );
			var $table = $scope.find( '.dataTables_scrollBody .gv-datatables' ).first();

			if ( 0 === $table.length ) {
				$table = $scope.find( '.gv-datatables' ).first();
			}

			return $table;
		},

		/**
		 * The Search Bar belonging to a table's own embed.
		 *
		 * A View on the page twice renders two Bars carrying the same data-viewid, so reading
		 * one by id alone gives the first embed's values to both tables.
		 *
		 * @since 3.11.0
		 *
		 * @param {Element} tableNode The table element.
		 * @param {string}  viewId    The View the table belongs to.
		 *
		 * @return {object} The form(s); the whole set when there is nothing to disambiguate.
		 */
		resolveEmbedSearchForm: function ( tableNode, viewId ) {
			var $forms = $( 'form.gv-widget-search[data-viewid="' + viewId + '"]' );

			// Nothing to walk: unchanged from the View-wide lookup.
			if ( ! tableNode || ! tableNode.closest || ! /^\d+$/.test( String( viewId ) ) ) {
				return $forms;
			}

			var selector = '[id^="gv-view-' + viewId + '-"]';
			var embed    = tableNode.closest( selector );

			// The wrapper decides before the count does: a table whose embed has no Search Bar
			// must read no Bar at all, rather than a sibling's, even when only one exists.
			if ( embed ) {
				return $forms.filter( function () {
					return this.closest( selector ) === embed;
				} );
			}

			return $forms;
		},

		/**
		 * The per-embed state entry behind a table.
		 *
		 * `gvDataTables.tables` is keyed per embed, with a bare View id aliased to the first of
		 * them. Reading through the alias hands back the first embed's data whichever table you
		 * meant; the table's own init carries the key that does not.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} $table The table element.
		 * @param {string} viewId The View, for the legacy fallback.
		 *
		 * @return {object|null}
		 */
		resolveTableState: function ( $table, viewId ) {
			var instanceKey = null;

			try {
				instanceKey = $table.length ? $table.DataTable().init().instanceKey : null;
			} catch ( ex ) {
				instanceKey = null;
			}

			if ( instanceKey && gvDataTables.tables[ instanceKey ] ) {
				return gvDataTables.tables[ instanceKey ];
			}

			// The bare View id is aliased to the FIRST embed, so falling back to it while a
			// specific table is in hand would read and mutate a different embed's state. Only
			// safe where there is nothing to confuse: a View rendered once on the page.
			//
			// Horizontal scrolling clones the table into the scroll head and foot, and the
			// clones keep the class and the data-viewid. Counting them makes one scrolling
			// embed look like three, which would disable this fallback for the very single-embed
			// case it exists to serve.
			var isOnlyEmbed = $( '.gv-datatables[data-viewid="' + viewId + '"]' ).filter( function () {
				return ! this.closest( '.dataTables_scrollHead, .dataTables_scrollFoot' );
			} ).length < 2;

			return isOnlyEmbed ? ( gvDataTables.tables[ viewId ] || null ) : null;
		},

		/**
		 * The value-carrying controls inside a field filter.
		 *
		 * A date range carries the `.gv-dt-field-filter` class on the flex wrapper around its
		 * two inputs, so `.val()` on the filter itself answers undefined. Every caller that
		 * reads or writes a filter's value must go through here, or it silently treats a
		 * date range as empty.
		 *
		 * @since 3.12.0
		 *
		 * @param {object} $filter A single `.gv-dt-field-filter` element, wrapped.
		 *
		 * @return {object} The filter itself when it is a control, otherwise its child controls.
		 */
		filterControls: function ( $filter ) {
			return $filter.is( 'input, select, textarea' ) ? $filter : $filter.find( 'input, select, textarea' );
		},

		/**
		 * Empties every field filter and redraws from page one.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} datatable The DataTables API instance.
		 *
		 * @return {void}
		 */
		clearFieldFilters: function ( datatable ) {
			$( datatable.table().container() ).find( '.gv-dt-field-filter' ).each( function () {
				// An attached date picker keeps its own display value, so clearing the input
				// alone leaves the old date on screen.
				gvDataTables.filterControls( $( this ) ).val( '' ).trigger( 'change' );
			} );

			datatable.columns().search( '' );

			// A full draw returns to page one; reloading without it can strand the visitor on a
			// page that no longer exists once the filters are gone.
			datatable.draw();
		},

		/**
		 * Makes the Search Bar widget's own "Clear" clear this table's column filters too.
		 *
		 * Clear only ever cleared the search, so the column filters outlived it: with state
		 * saving on they were restored by the reload, and when core resets the form in place
		 * they were never touched at all. Either way the search box emptied while the table
		 * stayed filtered, with nothing on screen to explain the missing rows.
		 *
		 * Bound in the capture phase because core's own handler returns false for a form it
		 * considers changed, which stops the click from ever bubbling this far.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} datatable The DataTables API instance.
		 * @param {string} viewId    The View whose Search Bar this is.
		 *
		 * @return {void}
		 */
		setUpSearchBarClear: function ( datatable ) {
			var table = datatable.table().node();

			// The template always writes this attribute, which makes it a surer source than an
			// init key that only exists because jQuery folded the same attribute into the
			// options object.
			var viewId = String( $( table ).attr( 'data-viewid' ) || '' );

			if ( ! viewId ) {
				return;
			}

			// A re-initialized View leaves its old table detached, so pruning comes first:
			// holding that entry both leaks the dead API object and lets the guard below keep
			// the stale one in place of the live table.
			gvDataTables.searchClearTables = ( gvDataTables.searchClearTables || [] ).filter( function ( entry ) {
				return document.contains( entry.table );
			} );

			var existing = null;

			gvDataTables.searchClearTables.forEach( function ( entry ) {
				if ( entry.table === table ) {
					existing = entry;
				}
			} );

			if ( existing ) {
				// Same element, new DataTables instance: keep the registry pointing at the live one.
				existing.viewId    = viewId;
				existing.datatable = datatable;
				existing.stateKey  = datatable.init().embedStateKey || null;
			} else {
				gvDataTables.searchClearTables.push( {
					table: table,
					viewId: viewId,
					datatable: datatable,
					// Removed by this View's Clear, so a sibling embed keeps its own saved state.
					stateKey: datatable.init().embedStateKey || null,
				} );
			}

			// One listener for the page, however many tables register: a per-table listener
			// would do O(tables) work on every click and pin a destroyed table's closure.
			if ( gvDataTables.searchClearBound ) {
				return;
			}

			gvDataTables.searchClearBound = true;

			// Capture phase, because core's own handler returns false for a form it considers
			// changed, which stops the click from bubbling this far.
			document.addEventListener( 'click', function ( event ) {
				// A click target is normally an element, but normalize rather than bail: a silent
				// return here would leave the clear unapplied while the rest of the page acts
				// as though it happened.
				var target = event.target;

				if ( target && 1 !== target.nodeType ) {
					target = target.parentElement;
				}

				var link = target && target.closest ? target.closest( '.gv-search-clear' ) : null;

				if ( ! link ) {
					return;
				}

				var form = link.closest( 'form.gv-widget-search' );

				if ( ! form ) {
					return;
				}

				var formViewId = String( form.getAttribute( 'data-viewid' ) || '' );

				if ( ! formViewId ) {
					return;
				}

				// Two Views on one page each own their own Search Bar; neither may clear the other.
				var sameView = gvDataTables.searchClearTables.filter( function ( entry ) {
					return entry.viewId === formViewId && document.contains( entry.table );
				} );

				// A View embedded twice gives both Search Bars the same data-viewid, so the id
				// alone cannot say which table this Clear belongs to. GravityView wraps each
				// embed in its own "gv-view-<id>-<n>", and the clicked link sits inside the one
				// it belongs to.
				//
				// The selector is pinned to THIS View's wrapper, not any "gv-view-" ancestor: a
				// Search Bar rendered inside some other View's wrapper would otherwise scope to
				// that one, match none of its own tables, and silently clear nothing. A View id
				// is a post id, so anything else cannot be trusted in a selector.
				var isNumericViewId = /^\d+$/.test( formViewId );
				var wrapperSelector = '[id^="gv-view-' + formViewId + '-"]';
				var embed           = isNumericViewId ? link.closest( wrapperSelector ) : null;

				var targets;

				if ( embed ) {
					// Compared against each table's OWN nearest wrapper rather than asking
					// whether this one contains it: a View embedded inside another embed of the
					// same View is contained by both, and the outer Clear must not reach the
					// inner table. An embed owning no table clears nothing, rather than falling
					// back to every copy of the View.
					targets = sameView.filter( function ( entry ) {
						return entry.table.closest( wrapperSelector ) === embed;
					} );
				} else {
					// No wrapper of this View's own. Rather than clearing every copy, resolve the
					// single table the Search Bar's own handlers drive, so this path and those
					// never disagree about which embed a Clear belongs to.
					var $owned = gvDataTables.resolveSearchBarTable( $( form ) );
					var owned  = $owned.length ? $owned.get( 0 ) : null;

					targets = owned ?
						sameView.filter( function ( entry ) {
							return entry.table === owned;
						} ) :
						sameView;
				}

				// Saved state is the only thing that can survive core's navigation back to the
				// unfiltered URL, so it has to go before the page reloads.
				//
				// Only the entries of the tables actually being cleared: each embed of a repeated
				// View owns its own now, so removing them all would reset a sibling the visitor
				// never touched, and it would show up on its next load rather than on this click.
				var stateKeysToRemove = targets.map( function ( entry ) {
					return entry.stateKey;
				} ).filter( Boolean );

				try {
					[ sessionStorage, localStorage ].forEach( function ( store ) {
						stateKeysToRemove.forEach( function ( key ) {
							store.removeItem( key );
						} );
					} );
				} catch ( ex ) {
					// Storage is unavailable, so there is no state to survive the navigation.
				}

				// Covers the branch where core resets the form in place and never navigates.
				targets.forEach( function ( entry ) {
					gvDataTables.clearFieldFilters( entry.datatable );
				} );
			}, true );
		},

		/**
		 * Adds a button that clears every field filter at once.
		 *
		 * It only appears while a visitor has a non-empty filter, so a View's initial rendering
		 * is unchanged.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} datatable The DataTables API instance.
		 */
		setUpClearFiltersButton: function ( datatable ) {
			var config = datatable.init().clearFilters;

			if ( ! config || ! config.enabled ) {
				return;
			}

			var $container = $( datatable.table().container() );

			// One button per table, even though this runs on every re-init.
			if ( $container.find( '.gv-dt-clear-filters' ).length ) {
				return;
			}

			var $button = $( '<button/>', {
				type: 'button',
				'class': 'gv-dt-clear-filters',
				text: config.label,
			} ).hide();

			var $live = $container.find( '.gv-dt-live-region' );

			if ( ! $live.length ) {
				$live = $( '<div/>', {
					'class': 'gv-dt-live-region screen-reader-text',
					'aria-live': 'polite',
				} ).appendTo( $container );
			}

			var $info = $container.find( '.dataTables_info' ).first();

			if ( $info.length ) {
				$button.insertBefore( $info );
			} else {
				$button.appendTo( $container );
			}

			var filterInputs = function () {
				return $container.find( '.gv-dt-field-filter' );
			};

			var hasActiveFilter = function () {
				var active = false;

				filterInputs().each( function () {
					gvDataTables.filterControls( $( this ) ).each( function () {
						if ( '' !== $.trim( $( this ).val() || '' ) ) {
							active = true;

							return false;
						}
					} );

					return ! active;
				} );

				return active;
			};

			var syncVisibility = function () {
				$button.toggle( hasActiveFilter() );
			};

			$button.on( 'click', function () {
				gvDataTables.clearFieldFilters( datatable );

				$live.text( config.cleared );

				// Focus must not stay on a button that is about to hide.
				$container.attr( 'tabindex', '-1' ).trigger( 'focus' );

				syncVisibility();
			} );

			$container.on( 'input.gvClearFilters change.gvClearFilters', '.gv-dt-field-filter', syncVisibility );
			datatable.on( 'draw.dt', syncVisibility );

			syncVisibility();
		},

		/**
		 * Returns the DataTables configuration for a table element.
		 *
		 * Configs are matched by View ID because window.gvDTglobals is
		 * de-duplicated server-side: the same View embedded twice on a page
		 * produces two table elements but a single config entry, so positional
		 * lookup breaks. Configs without an ajax.data.view_id (third-party
		 * filtered shapes) fall back to DOM-order lookup.
		 *
		 * @since 3.10.0
		 *
		 * @param {string} viewId The table's data-viewid attribute value.
		 * @param {number} domIndex The table's index among .gv-datatables elements.
		 * @returns {object|null} The matching config, or null when none exists.
		 */
		getViewConfig: function ( viewId, domIndex ) {
			var globals = window.gvDTglobals || [];

			for ( var j = 0; j < globals.length; j++ ) {
				var cfg = globals[ j ];
				var cfgViewId = cfg && cfg.ajax && cfg.ajax.data ? cfg.ajax.data.view_id : undefined;

				if ( cfgViewId !== undefined && String( cfgViewId ) === String( viewId ) ) {
					return cfg;
				}
			}

			var indexed = domIndex < globals.length ? globals[ domIndex ] : null;
			var indexedHasViewId = indexed && indexed.ajax && indexed.ajax.data && indexed.ajax.data.view_id !== undefined;

			return indexed && ! indexedHasViewId ? indexed : null;
		},

		/**
		 * Finds an extension config (gvDTResponsive / gvDTFixedHeaderColumns)
		 * for a View.
		 *
		 * Prefers view_id matching; entries without a view_id (inline scripts
		 * from cached pages generated before view_id was added) fall back to
		 * DOM-order lookup.
		 *
		 * @since 3.10.0
		 *
		 * @param {Array} configs The pushed config objects.
		 * @param {string} viewId The table's data-viewid attribute value.
		 * @param {number} domIndex The table's index among .gv-datatables elements.
		 * @returns {object|null} The matching config, or null.
		 */
		getExtensionConfig: function ( configs, viewId, domIndex ) {
			if ( ! configs || 'number' !== typeof configs.length ) {
				return null;
			}

			for ( var j = 0; j < configs.length; j++ ) {
				var cfg = configs[ j ];

				if ( cfg && cfg.view_id !== undefined && String( cfg.view_id ) === String( viewId ) ) {
					return cfg;
				}
			}

			var indexed = domIndex < configs.length ? configs[ domIndex ] : null;

			return indexed && indexed.view_id === undefined ? indexed : null;
		},

		/**
		 * Keeps the RowGroup lock from shadowing a click on the grouping column itself.
		 *
		 * RowGroup locks ordering with `orderFixed`, which DataTables applies ahead of the
		 * visitor's order. Clicking the grouping column therefore lands as a dead tiebreaker
		 * on a column already ordered by the lock: the header arrow flips but the rows never
		 * move. Point the lock at the visitor's direction instead, and restore the configured
		 * one once they order by something else.
		 *
		 * Server-side processing is exempt: there the order travels to GF_Query, which collapses
		 * the repeated column and keeps the last direction, so the click already wins and a
		 * redraw here would only cost a second identical request.
		 *
		 * @since 3.11.0
		 *
		 * @param {object} table The DataTables API instance.
		 * @param {object} options The configuration the table was initialized with.
		 * @returns {void}
		 */
		followVisitorOrderOnGroupColumn: function ( table, options ) {
			if ( options.serverSide || ! options.rowGroupSettings ) {
				return;
			}

			const configuredOrder = options.orderFixed;

			if ( ! Array.isArray( configuredOrder ) || ! Array.isArray( configuredOrder[ 0 ] ) ) {
				return;
			}

			const groupColumn = configuredOrder[ 0 ][ 0 ];
			const configuredDirection = configuredOrder[ 0 ][ 1 ];

			table.on( 'order.dt', function () {
				const settings = table.settings()[ 0 ];
				const lockedOrder = settings.aaSortingFixed;

				if ( ! Array.isArray( lockedOrder ) || ! Array.isArray( lockedOrder[ 0 ] ) ) {
					return;
				}

				const visitorOrder = settings.aaSorting.filter( function ( order ) {
					return order[ 0 ] === groupColumn;
				} )[ 0 ];

				const wantedDirection = visitorOrder ? visitorOrder[ 1 ] : configuredDirection;

				if ( wantedDirection === lockedOrder[ 0 ][ 1 ] ) {
					return;
				}

				lockedOrder[ 0 ][ 1 ] = wantedDirection;

				// This draw already sorted with the stale lock, so it takes one more pass to
				// land. The check above makes that pass a no-op, bounding this to one redraw.
				setTimeout( function () {
					if ( ! $.fn.dataTable.isDataTable( settings.nTable ) ) {
						return;
					}

					table.draw( false );
				}, 0 );
			} );
		},

		/**
		 * Registers the client-side filter predicate exactly once.
		 *
		 * $.fn.dataTable.ext.search is global to every DataTable on the page;
		 * one registration per table would run duplicate predicates against
		 * every row of every table on each draw.
		 *
		 * @since 3.10.0
		 */
		ensureClientSideFilterAndSearch: function () {
			if ( gvDataTables.clientSideSearchRegistered ) {
				return;
			}

			gvDataTables.clientSideSearchRegistered = true;

			configureClientSideFilterAndSearch();
		},

		init: function () {

			// How many embeds of each View have been seen, so a repeated one can be told apart.
			// Counted per View rather than across the page, so adding or removing an unrelated
			// View does not renumber this one's embeds.
			var embedsSeen = {};

			$( '.gv-datatables' ).each( function ( i, e ) {
				var viewId = $( this ).attr( 'data-viewid' );

				// Two embeds of the same View on one page must not share one state entry: without
				// this, the second embed's init silently replaces the first's in gvDataTables.tables,
				// so Auto-Update, exports, and error recovery only ever see the last table.
				var instanceKey = viewId + ':' + i;
				var embedIndex  = embedsSeen[ viewId ] = ( undefined === embedsSeen[ viewId ] ? 0 : embedsSeen[ viewId ] + 1 );
				var config = gvDataTables.getViewConfig( viewId, i );

				if ( ! config ) {
					return;
				}

				// Each table needs its own copy: init mutates the config
				// (options.ajax is deleted for client-side processing and its
				// data replaced with a function for server-side), which would
				// corrupt a second table sharing the same object.
				var options = $.extend( true, {}, config );

				options.instanceKey = instanceKey;
				options.embedIndex  = embedIndex;

				gvDataTables.tables[ instanceKey ] = {
					emptyServerResponse: false,
					data: ( options.ajax || {} ).data,
				};

				if ( ! gvDataTables.tables[ viewId ] ) {
					gvDataTables.tables[ viewId ] = gvDataTables.tables[ instanceKey ];
				}

				// Wrap the processing text in HTML if it exists
				if ( options.language && options.language.processing ) {
					options.language.processing = "<div class='dataTables_processing_text'>" + options.language.processing + "</div>";
				}

				options.buttons = gvDataTables.setButtons( options );

				options.drawCallback = function ( data ) {
					if ( window.gvEntryNotes ) {
						window.gvEntryNotes.init();
					}

					// In server-side mode, templates come from the AJAX response (data.json).
					// In preloaded (client-side) mode, there is no AJAX response, so templates are in the config object.
					var templates = ( data.json && data.json.inlineEditTemplatesData ) || options.inlineEditTemplatesData;

					if ( templates ) {
						$( window ).trigger( 'gravityview-inline-edit/extend-template-data', templates );
					}

					$( window ).trigger( 'gravityview-inline-edit/init' );
				};

				/**
				 * Add per-field search inputs
				 *
				 * @since 2.5
				 *
				 * @param {DataTables.Settings} settings
				 */
				options.initComplete = function ( settings ) {
					gvDataTables.setUpFieldFilters( this.api(), settings );
				};

				// A config without an ajax block (a purely client-side table) has nothing for
				// this to wrap; the data assignment above already tolerates the same absence.
				if ( options.ajax ) {
					// convert ajax data object to method that return values from the global object
					options.ajax.data = function ( e ) {
						// A joined form's field can share its bare ID with a primary-form field
						// (both "gv_2"); the server can't tell them apart from that alone.
						disambiguateOrderColumnNames( e, options.columns, options.primaryFormId );

						const state = gvDataTables.tables[ instanceKey ];

						// A `sort` parameter in the address outranks the View's sort settings server-side,
						// so it must not also outrank the visitor. The request order alone cannot tell
						// the two apart — paging resends the same order — hence the comparison against
						// the order the table started with.
						const visitorReordered = state.initialOrder !== undefined &&
							JSON.stringify( state.table.order() ) !== state.initialOrder;

						// Send the live URL query so server-side Filter & Sort conditions still resolve
						// when the render-time snapshot (getData) is stale, e.g. behind a full-page cache.
						return $.extend( {}, e, state.data, {
							pageQuery: window.location.search,
							visitorReordered: visitorReordered ? 1 : 0,
						} );
					};
				}

				// init FixedHeader and FixedColumns extensions
				var fixedConfig = gvDataTables.getExtensionConfig( window.gvDTFixedHeaderColumns, viewId, i );

				// Per-field pins arrive from PHP as fixedColumns edge counts. The View-level
				// checkbox (first column pinned) is the fallback for Views without field pins.
				var hasServerPins = options.fixedColumns !== undefined && options.fixedColumns !== null;

				if ( fixedConfig ) {
					if ( String( fixedConfig.fixedheader ) === '1' ) {
						options.fixedHeader = {
							headerOffset: $( '#wpadminbar' ).outerHeight()
						};
					}

					if ( ! hasServerPins && String( fixedConfig.fixedcolumns ) === '1' ) {
						options.fixedColumns = true;
					}
				}

				// init Responsive extension
				var responsiveConfig = gvDataTables.getExtensionConfig( window.gvDTResponsive, viewId, i );

				if ( responsiveConfig && String( responsiveConfig.responsive ) === '1' ) {
					if ( '1' === String( responsiveConfig.hide_empty ) ) {
						// use the modified row renderer to remove empty fields
						options.responsive = { details: { renderer: gvDataTables.customResponsiveRowRenderer } };
					} else {
						options.responsive = true;
					}

					// Responsive and FixedColumns both reposition columns and conflict, on both
					// Responsive paths. PHP already converts pins to collapse protection here.
					options.fixedColumns = false;
				}

				// init rowGroup extension.
				if ( options.rowGroupSettings && options.rowGroupSettings.status && options.rowGroupSettings.status * 1 === 1 ) {

					// Disable incompatible extensions.
					options.fixedColumns = false;

					var rowGroup = {
						dataSrc: function ( row ) {
							const row_field = row[ options.rowGroupSettings.index * 1 ];
							let $row_field;

							try {
								$row_field = $( row_field );
							} catch ( e ) {
								$row_field = {};
							}

							if ( $row_field.length && $row_field.attr( 'href' ) !== undefined ) {
								return $( row_field ).text();
							}

							// Check if row_field contains inline-editable spans (from GravityEdit)
							// and extract just the text content for grouping
							if ( $row_field.length && $row_field.hasClass && $row_field.attr( 'class' ) && $row_field.attr( 'class' ).includes( 'gv-inline-editable-field' ) ) {
								return $row_field.text();
							}

							return row_field;
						},
						startRender: null,
						endRender: null
					};

					if ( options.rowGroupSettings.startRender === true ) {
						rowGroup.startRender = function ( rows, group ) {
							return group;
						};
					}

					if ( options.rowGroupSettings.endRender === true ) {
						rowGroup.endRender = function ( rows, group ) {
							return group;
						};
					}

					options.rowGroup = rowGroup;
				}

				options.createdRow = function ( row, dt, rowIndex ) {
					$( row ).find( '> td' ).each( function ( columnIndex ) {
						$( this ).attr( 'data-row-index', rowIndex );
						$( this ).attr( 'data-column-index', columnIndex );
					} );
				};

				// Configure custom render logic for columns.
				options.columns.forEach( column => {
					// Use shadow data object to sort columns.
					column.render = ( data, type, row, settings ) => {
						if ( type !== 'sort' ) {
							return data;
						}

						const sortValue = gvDataTables.tables?.[instanceKey]?.shadowData?.[ settings.row ]?.[ settings.col ] ?? data;

						if ( 'num' !== column.type ) {
							return sortValue;
						}

						return gvDataTables.numericSortValue( sortValue, column );
					};
				} );

				// Implement state key management to invalidate cache when settings change.
				if ( options.stateSave && options.stateKey ) {
					const stateKey = options.stateKey;
					const baseStateKey = 'DataTables_' + $( this ).attr( 'data-viewid' );

					// Everything belonging to this View and this configuration. Sibling embeds
					// differ only by what follows.
					const configStateKey = baseStateKey + '_' + stateKey;

					// The same View embedded twice produces the same key, so the two tables read
					// and write one entry: a filter set on one came back on the other after a
					// reload. Only the repeats are suffixed, so a page with a single embed (nearly
					// all of them) keeps the exact key it had and needs no migration.
					const embedStateKey = options.embedIndex ? configStateKey + '_' + options.embedIndex : configStateKey;

					// The Search Bar clear reads this back, so it removes this embed's entry
					// rather than every entry belonging to the View.
					options.embedStateKey = embedStateKey;

					options.stateSaveCallback = function ( settings, data ) {
						try {
							// stateDuration -1 means "this session only", so sessionStorage is what
							// honors it. localStorage kept table state forever.
							sessionStorage.setItem( embedStateKey, JSON.stringify( data ) );
						} catch ( ex ) {
							// Fail silently if localStorage is not available.
						}
					};

					options.stateLoadCallback = function ( settings ) {
						try {
							return JSON.parse( sessionStorage.getItem( embedStateKey ) );
						} catch ( ex ) {
							return null;
						}
					};

					// An entry belonging to this configuration: either the first embed's key or one
					// of the numbered siblings.
					//
					// Deliberately not narrowed to the embeds THIS page renders. The key space is
					// the View's, not the page's, so a View shown once here and three times
					// elsewhere shares it; reclaiming the higher numbers would delete the other
					// page's tables' state every time this one loaded. Entries for embeds that no
					// longer exist anywhere are the price, and they expire with the session.
					const belongsToThisConfig = function ( key ) {
						if ( key === configStateKey ) {
							return true;
						}

						if ( 0 !== key.indexOf( configStateKey + '_' ) ) {
							return false;
						}

						return /^\d+$/.test( key.slice( configStateKey.length + 1 ) );
					};

					// Sweep superseded state keys, including any left in localStorage by versions
					// that stored table state there.
					//
					// Shape-matched rather than "everything but my own key": sibling embeds would
					// otherwise delete each other's state on every load.
					try {
						[ sessionStorage, localStorage ].forEach( function ( store ) {
							const keysToRemove = [];

							for ( let i = 0; i < store.length; i++ ) {
								const key = store.key( i );
								if ( key && key.startsWith( baseStateKey + '_' ) && ! belongsToThisConfig( key ) ) {
									keysToRemove.push( key );
								}
							}

							keysToRemove.forEach( key => store.removeItem( key ) );
						} );
					} catch ( ex ) {
						// Fail silently.
					}
				}

				if ( options.ajax ) {
					// An error envelope arrives as a valid 200 body, so it is caught here rather
					// than through DataTables' error path.
					options.ajax = $.extend( {}, options.ajax, {
						dataFilter: function ( data, type ) {
							// Server-side draws once before the first response arrives, with an empty
							// table. Without this the no-entries action fires on that draw and can
							// redirect the visitor before the server has answered at all.
							if ( gvDataTables.tables[ instanceKey ] ) {
								gvDataTables.tables[ instanceKey ].hasResponse = true;
							}

							if ( data !== '' ) {
								var envelope = null;

								try {
									envelope = JSON.parse( data );
								} catch ( ex ) {
									envelope = null;
								}

								var failure = envelope && envelope.gv_error;

								if ( ! failure ) {
									// This table's own state carries its own `.table` reference by now (the
									// constructor assigns it synchronously, before any response reaches this
									// callback), so use it rather than the id: two embeds of the same View
									// share one "#gv-datatables-<id>" id.
									// Must match renderFailureState()'s resolution: it appends to the outer
									// .gv-datatables-container, so searching only the inner DataTables
									// wrapper would leave the error box on screen after a recovery.
									var $successContainer = gvDataTables.tables[ instanceKey ] && gvDataTables.tables[ instanceKey ].table ?
										$( gvDataTables.tables[ instanceKey ].table.table().container() ).closest( '.gv-datatables-container' ) :
										$( '#gv-datatables-' + viewId );

									$successContainer.find( '.gv-dt-error' ).remove();
									$successContainer.find( '.dataTables_info, .dataTables_paginate' ).show();

									if ( gvDataTables.tables[ instanceKey ] ) {
										gvDataTables.tables[ instanceKey ].serverError = null;
									}

									return data;
								}

								// DataTables logs a warning for the payload's `error` key, which fires
								// the same event the transport retry listens to. This response is
								// already accounted for, so that retry must stand down.
								if ( gvDataTables.tables[ instanceKey ] ) {
									gvDataTables.tables[ instanceKey ].envelopeHandled = true;

									// A refusal carries no entries, but it is not an answer about how many
									// entries exist. The draw handler consumes this so the configured
									// no-entries action does not fire on a transport failure.
									gvDataTables.tables[ instanceKey ].serverError = true;
								}

								// Deferred so DataTables finishes this draw cycle before the DOM changes.
								window.setTimeout( function () {
									var isStaleNonce = 'invalid_nonce' === envelope.gv_error.code;

									if ( ! isStaleNonce ) {
										gvDataTables.renderFailureState( instanceKey, options, envelope );

										return;
									}

									gvDataTables.refreshNonce( instanceKey, function ( refreshed ) {
										var table = gvDataTables.tables[ instanceKey ] && gvDataTables.tables[ instanceKey ].table;

										if ( refreshed && table ) {
											table.ajax.reload( null, false );

											return;
										}

										gvDataTables.renderFailureState( instanceKey, options, envelope );
									} );
								}, 0 );

								return data;
							}

							gvDataTables.tables[ instanceKey ].emptyServerResponse = true;

							// The draw counter has to echo the request, or DataTables drops this
							// response (draw < iDraw) and the visitor never sees the message.
							var currentDraw = 0;

							try {
								currentDraw = new $.fn.dataTable.Api( gvDataTables.tables[ instanceKey ].table )
									.settings()[ 0 ].iDraw;
							} catch ( ex ) {
								currentDraw = 0;
							}

							return JSON.stringify( {
								draw: currentDraw,
								recordsTotal: 0,
								recordsFiltered: 0,
								data: []
							} );
						}
					} );
				}

				// Client-side processing is on.
				if ( !options.serverSide && options.ajax ) {
					// DT will use Ajax if initialized with the .ajax property.
					// Let's save a copy in case we need it later, and remove it from options.
					options._ajax = options.ajax;
					options.processing = true;

					delete options.ajax;
				}

				// Enable footer calculations.
				if ( !options.serverSide && options.footerCalculation ) {
					const existingCreatedRowFn = options.createdRow;

					options.createdRow = function ( row, dt, rowIndex ) {
						if ( typeof existingCreatedRowFn === 'function' ) {
							existingCreatedRowFn( row, dt, rowIndex );
						}

						$( row ).find( '> td' ).each( function ( columnIndex ) {
							// A 'form' scope column legitimately has no `values` array (footerCallback
						// reads its result from server-rendered markup instead), so this has to
						// stay optional-chained all the way through.
						$( this ).attr( 'data-numeric-value', options.footerCalculation?.data?.[ columnIndex ]?.values?.[ rowIndex ] );
						} );
					};

					options.footerCallback = function ( tfoot ) {
						const api = this.api();

						const footerCalculationRow = () => $( tfoot ).parent().find( '.footer-calculation' );

						if ( footerCalculationRow().length === 0 ) {
							const columnCount = api.columns().nodes().length;
							const cellContent = '<td></td>'.repeat( columnCount );
							let footerCalculationRowContent = $( `<tr class="footer-calculation" style="background-color: ${ options.footerCalculation?.row_background_color || 'white' };">${ cellContent }</tr>` );

							if ( window?.wp?.hooks ) {
								footerCalculationRowContent = window.wp.hooks.applyFilters( 'gk.datatables.footer-calculation.row-content', footerCalculationRowContent, {
									columnCount,
									api,
								} );
							}

							$( tfoot ).parent()[ options.footerCalculation?.row_position === 'above' ? 'prepend' : 'append' ]( footerCalculationRowContent );
						}

						api.columns().every( function ( columnIndex ) {
							const {
								scope,
								operation,
								label,
								decimals,
								field_type: fieldType,
								format_as_duration: formatAsDuration,
								format_as_currency: formatAsCurrency,
							} = options.footerCalculation?.data?.[ columnIndex ] || {};

							const locale = ( options.footerCalculation?.locale ?? 'en-US' ).replace( '_', '-' );
							let calculationResult;
							let calculationResultFormatted;

							if ( scope === 'form' ) {
								// When scope is form, the calculation result is already provided in the markup generated in the backend.
								calculationResult = $( options.footerCalculation?.server_side_footer_markup ).find( 'th' ).eq( columnIndex ).data( 'numeric-value' );
								calculationResultFormatted = $( options.footerCalculation?.server_side_footer_markup ).find( 'th' ).eq( columnIndex ).html();
							} else {
								let totalValues = 0;
								let minValue = Infinity;
								let maxValue = -Infinity;

								// Get pre-collected values array from PHP (includes all entries).
								const allValues = options.footerCalculation?.data?.[ columnIndex ]?.values || [];

								// For "visible" scope, filter to current page indices. For "view" scope, use all values.
								let valuesToProcess = allValues;
								if ( scope === 'visible' ) {
									const pageInfo = api.page.info();
									valuesToProcess = allValues.filter( ( _, idx ) => idx >= pageInfo.start && idx < pageInfo.end );
								}

								// Handle empty array edge case before reduce to avoid incorrect defaults.
								if ( valuesToProcess.length === 0 ) {
									// For avg/min/max operations, return null; for count operations, return 0.
									switch ( operation ) {
										case 'avg':
										case 'min':
										case 'min-fastest':
										case 'max':
										case 'max-slowest':
											calculationResult = null;
											break;
										default:
											calculationResult = 0; // count operations return 0 for empty arrays.
									}
								} else {
									calculationResult = valuesToProcess.reduce( ( accumulator, value, index, array ) => {
										let numericValue;

										switch ( operation ) {
											case 'sum':
											case 'avg':
												numericValue = 0;

												if ( value ) {
													numericValue = parseFloat( value );

													if ( isNaN( numericValue ) ) {
														numericValue = 0; // Treat non-numeric as 0 for avg and sum.
													}
												}

												if ( operation === 'avg' ) {
													totalValues += numericValue;

													return ( index === array.length - 1 ) ? totalValues / array.length : accumulator;
												}

												return accumulator + numericValue;
											case 'min-fastest':
											case 'min':
												numericValue = parseFloat( value );

												if ( !isNaN( numericValue ) ) {
													minValue = Math.min( minValue, numericValue );
												}

												return ( index === array.length - 1 ) ? ( minValue === Infinity ? null : minValue ) : accumulator;
											case 'max-slowest':
											case 'max':
												numericValue = parseFloat( value );

												if ( !isNaN( numericValue ) ) {
													maxValue = Math.max( maxValue, numericValue );
												}

												return ( index === array.length - 1 ) ? ( maxValue === -Infinity ? null : maxValue ) : accumulator;
											case 'count':
											case 'count-nonempty-consented':
											case 'count-nonempty-checked':
											case 'count-nonempty-selected':
											case 'quiz-passed':
											case 'quiz-passed-percent':
												return value ? accumulator + 1 : accumulator;
											case 'count-empty-unconsented':
											case 'count-empty-unchecked':
											case 'count-empty-unselected':
											case 'quiz-failed':
											case 'quiz-failed-percent':
												return !value ? accumulator + 1 : accumulator;
											default:
												return accumulator;
										}
									}, 0 );
								}

								// Handle edge case: if min/max operations had no valid numeric values, replace Infinity/-Infinity with null.
								if ( calculationResult === Infinity || calculationResult === -Infinity ) {
									calculationResult = null;
								}

								if ( /quiz-.*-percent/.test( operation ) ) {
									// Use the length of valuesToProcess instead of api.rows().count() to handle all entries correctly.
									// Guard against division by zero for empty arrays.
									if ( valuesToProcess.length > 0 ) {
										calculationResult = calculationResult / valuesToProcess.length * 100;
									} else {
										calculationResult = 0;
									}
								}

								// Format calculation result.
								if ( calculationResult === null ) {
									// No valid numeric values for min/max operation - display empty string.
									calculationResultFormatted = '';
								} else if ( formatAsDuration ) {
									calculationResultFormatted = formatAsDuration === 'human_readable' ? convertSecondsToHumanReadableHMS( calculationResult ) : convertSecondsToHMS( calculationResult );

									calculationResultFormatted = calculationResultFormatted
										.replace( 'hours', options.translations?.hours || 'hours' )
										.replace( 'hour', options.translations?.hour || 'hour' )
										.replace( 'minutes', options.translations?.minutes || 'minutes' )
										.replace( 'minute', options.translations?.minute || 'minute' )
										.replace( 'seconds', options.translations?.seconds || 'seconds' )
										.replace( 'second', options.translations?.second || 'second' );
								} else if ( formatAsCurrency ) {
									calculationResultFormatted = formatCurrency( calculationResult, formatAsCurrency, locale, decimals );
								} else {
									calculationResultFormatted = new Intl.NumberFormat( locale, {
										minimumFractionDigits: decimals,
										maximumFractionDigits: decimals
									} ).format( calculationResult );
								}
							}

							const footerCalculationRowCell = footerCalculationRow().find( 'td' ).eq( columnIndex );

							footerCalculationRowCell.attr( 'data-numeric-value', calculationResult );
							footerCalculationRowCell.attr( 'data-operation', operation );
							footerCalculationRowCell.attr( 'data-decimals', decimals );
							footerCalculationRowCell.attr( 'data-field-type', fieldType );
							footerCalculationRowCell.attr( 'data-format-as-duration', formatAsDuration );
							footerCalculationRowCell.attr( 'data-format-as-currency', formatAsCurrency );
							footerCalculationRowCell.attr( 'data-scope', scope );

							let footerCalculationRowCellContent = ( label || '' ).replace( '{result}', calculationResultFormatted || '' );

							if ( window?.wp?.hooks ) {
								calculationResultFormatted = window.wp.hooks.applyFilters( 'gk.datatables.footer-calculation.calculation-result',
									calculationResultFormatted,
									calculationResult,
									{
										scope,
										operation,
										decimals,
										fieldType,
										columnIndex,
										api,
									}
								);

								footerCalculationRowCellContent = window.wp.hooks.applyFilters(
									'gk.datatables.footer-calculation.cell-content',
									footerCalculationRowCellContent, {
										calculationResultFormatted,
										calculationResult,
										fieldType,
										scope,
										operation,
										decimals,
										label,
										columnIndex,
										api,
									}
								);
							}

							footerCalculationRowCell.html( footerCalculationRowCellContent );
						} );
					};
				}

				if ( window?.wp?.hooks ) {
					options = wp.hooks.applyFilters( 'gk.datatables.options', options );
				}

				if ( !options.serverSide ) {
					Object.assign( gvDataTables.tables[ instanceKey ], {
						// False when the first render was already filtered, so the client holds a
						// subset rather than the full set.
						allRecordsLoaded: !gvDataTables.resolveEmbedSearchForm( this, viewId ).hasClass( 'gv-is-search' ),
						shadowData: buildShadowDataObject( { data: options.data, shadowData: options.shadowData, columns: options.columns, } )
					} );

					gvDataTables.ensureClientSideFilterAndSearch();
				}

				// Init Auto Update
				if ( options.updateInterval && options.updateInterval > 0 ) {
					setInterval( function () {
						const table = gvDataTables.tables[ instanceKey ].table;

						if ( options._ajax ) {
							// If Ajax was disabled before, re-enable it.
							table.settings()[ 0 ].ajax = options._ajax;
						}

						table.ajax.reload( null, false );
					}, ( options.updateInterval * 1 ) );
				}

				// Setting "options.searching = false" to hide DT's search input will completely disable the search (filtering) functionality.
				// The workaround is to remove the search bar after the table is initialized.
				if ( !options.searching ) {
					options.searching = true;
					options.hideSearchBar = true;
				}

				gvDataTables.tables[ instanceKey ].table = $( this ).DataTable( options );

				// Captured before the visitor can touch the table, so a later request can say
				// whether the ordering is still the one the View shipped with.
				gvDataTables.tables[ instanceKey ].initialOrder = JSON.stringify( gvDataTables.tables[ instanceKey ].table.order() );

				gvDataTables.followVisitorOrderOnGroupColumn( gvDataTables.tables[ instanceKey ].table, options );

				gvDataTables.ensureHorizontalScroll( $( this ), options );
				gvDataTables.setUpEmptyScrollHeadLayout( gvDataTables.tables[ instanceKey ].table );
				gvDataTables.setUpHorizontalScrollSync( gvDataTables.tables[ instanceKey ].table );
				gvDataTables.setUpScrollColumnSync( gvDataTables.tables[ instanceKey ].table );

				// Later draws bring rows the first solve never saw: paging, searching, and every
				// server-side response. The width only ever grows, so a draw whose rows need less
				// room leaves the table where the visitor last saw it.
				//
				// 'column-sizing' carries the window resize DataTables throttles for us, and a
				// visibility toggle changes how many columns share the width: either can put a
				// table that fitted a moment ago past its container.
				gvDataTables.tables[ instanceKey ].table.on( 'draw.dt column-sizing.dt column-visibility.dt', function () {
					gvDataTables.ensureHorizontalScroll( $( this ), options );
				} );

				if ( options.hideSearchBar ) {
					$( gvDataTables.tables[ instanceKey ].table.settings()[ 0 ].nTableWrapper ).find( '.dataTables_filter' ).remove();
				}

				// The DataTable() constructor above shows the loader and fires 'processing.dt'
				// synchronously for an Ajax-sourced table, before the '.on( "processing.dt" )'
				// binding below can attach, so that first event never reaches repositionLoader()
				// and the loader is left at its default (potentially header-overlapping) position.
				// Catch that missed first show directly.
				if ( $( 'div.dataTables_processing', $( this ).parents( '.gv-datatables-container' ) ).is( ':visible' ) ) {
					gvDataTables.repositionLoader( $( this ) );
				}

				gvDataTables.tables[ instanceKey ].table
					.on( 'draw.dt', function ( e, settings ) {
						var api = new $.fn.dataTable.Api( settings );

						if ( api.column( 0 ).data().length ) {
							$( e.target )
								.parents( '.gv-container-no-results' )
								.removeClass( 'gv-container-no-results' )
								.siblings( '.gv-widgets-no-results' )
								.removeClass( 'gv-widgets-no-results' );
						}

						var viewId = $( e.target ).data( 'viewid' );
						var tableData = gvDataTables.tables[ instanceKey ].data ?? null;
						var getData = ( tableData && tableData.hasOwnProperty( 'getData' ) ) ? tableData.getData : null;
						var $viewContainer = $( e.target ).parents( 'div[id^=gv-view-]' );
						var noEntriesOption = tableData?.noEntriesOption * 1;
						var hideUntilSearched = tableData?.hideUntilSearched * 1;

						// The response filter sets this and clears it only when a response
						// actually succeeds: the failure state redraws, and resetting per draw
						// would let the second draw fire the no-entries action the first one
						// suppressed.
						var serverError = gvDataTables.tables[ instanceKey ].serverError;

						// Server-side draws once before the first response lands; an empty table at
						// that point says nothing about how many entries exist.
						var awaitingFirstResponse = options.serverSide && ! gvDataTables.tables[ instanceKey ].hasResponse;

						if (
							! serverError && // The server answered, rather than refusing.
							! awaitingFirstResponse &&
							api.data().length === 0 && // No entries.
							0 === api.search().length && // No global search.
							0 === api.columns().search().filter( function ( string ) {
								return string !== '';
							} ).length && // No field filters per-column search.
							!getData // Search Bar is not being used to search.
						) {
							// No entries.
							const zeroRecords = $('<div/>').html( options.language.zeroRecords ).text();

							$( e.target ).find( '.dataTables_empty' ).text( zeroRecords );

							switch ( noEntriesOption ) {
								case 1: // Show a form.
									$viewContainer
										.find( '[id^=gv-datatables-],.gv-widgets-header,.gv-powered-by' ).hide().end()
										.find( '.gv-datatables-form-container' ).removeClass( 'gv-hidden' );
									break;
								case 2: // Redirect to the URL.
									var redirectURL = tableData && tableData.hasOwnProperty( 'redirectURL' ) ? tableData.redirectURL : null;
									if ( redirectURL && redirectURL.length ) {
										window.location = redirectURL;
									}
									break;
								case 3: // Hide the View (should already be hidden, but just in case).
									$( e.target ).parents( '.gv-datatables-container' ).hide();
									break;
							}

						} else {
							// Entries found.
							if ( !hideUntilSearched && $( gvDataTables.tables[ instanceKey ].table.table().container() ).is( ':hidden' ) ) {
								$viewContainer
									.find( '.gv-widgets-header, .gv-widgets-footer, .gv-datatables-container' )
									.removeClass( 'gv-hidden' );

								// Unsetting width fixes the issue with the table not being displayed properly after being unhidden.
								// api.columns().adjust() doesn't work in this case.
								$viewContainer.find( 'table.dataTable' ).css( 'width', '' );

								if ( options.gvHasWidths ) {
									// Clearing the table width drops the computed column widths with it.
									gvDataTables.tables[ instanceKey ].table.columns.adjust();
								}
							}

							// No search results.
							const emptyTable = $('<div/>').html( gvDataTables.tables[ instanceKey ].emptyServerResponse ? options.language.emptyServerResponse : options.language.emptyTable ).text();

							$( e.target ).find( '.dataTables_empty' ).text( emptyTable );
						}

						$( window ).trigger( 'gravityview-datatables/event/draw', { e, settings } );
					} )
					.on( 'preXhr.dt', function ( e, settings, data ) {
						$( window ).trigger( 'gravityview-datatables/event/preXhr', {
							e,
							settings,
							data,
						} );
					} )
					.on( 'processing.dt', function ( e, settings, processing ) {
						if ( !processing ) {
							return;
						}

						gvDataTables.repositionLoader( $( e.target ) );
					} )
					.on( 'xhr.dt', function ( e, settings, json, xhr ) {
						if ( json?.shadowData ) {
							const shadowData = buildShadowDataObject( { data: json.data, shadowData: json.shadowData, columns: options.columns } );

							json.shadowData = shadowData;
							gvDataTables.tables[ instanceKey ].shadowData = shadowData;
						}

						$( window ).trigger( 'gravityview-datatables/event/xhr', {
							e,
							settings,
							json,
							xhr,
						} );
					} )
					.on( 'responsive-resize', function ( e, datatable ) {
						if ( options.gvHasWidths ) {
							// Re-apply percentage widths after Responsive changes visibility.
							datatable.columns.adjust();
						}
						// Re-initialize field filters, if enabled.
						gvDataTables.setUpFieldFilters( datatable, datatable.settings()[ 0 ] );
					} )
					.on( 'responsive-display', function () {
						$( window ).trigger( 'gravityview-datatables/event/responsive' );
						var visible_divs, div_attr;

						// Fix duplicate images in Fancybox in datatables on mobile.
						visible_divs = $( this ).find( 'td:visible .gravityview-fancybox' );

						if ( visible_divs.length > 0 ) {
							visible_divs.each( function ( i, e ) {
								div_attr = $( this ).attr( 'data-fancybox' );
								if ( div_attr && div_attr.indexOf( 'mobile' ) === -1 ) {
									div_attr += '-mobile';
									$( this ).attr( 'data-fancybox', div_attr );
								}
							} );
						}
					} )
					.on( 'column-visibility.dt', function ( e, settings, columnIdx, state ) {
						// Scopes to the table that fired this event: a document-wide selector
						// would match every GravityView DataTables View on the page and hide
						// the wrong table's footer-calculation cell.
						var $footer = $( new $.fn.dataTable.Api( settings ).table().container() ).find( '.footer-calculation' );

						if ( state ) {
							$footer.find( 'td' ).eq( columnIdx ).show();
						} else {
							$footer.find( 'td' ).eq( columnIdx ).hide();
						}
					} )
					;
			} );

		}, // end of init

		/**
		 * Reposition the loader based on what parts of the table is visible.
		 * @since 2.7
		 * @param {jQuery} $table The current DataTables table DOM element.
		 */
		repositionLoader: function ( $table ) {
			var $container = $table.parents( '.gv-datatables-container' );
			var $thead = $table.find( 'thead' );
			var $tbody = $table.find( 'tbody' );
			var $tfoot = $table.find( 'tfoot' );
			var $loader = $( 'div.dataTables_processing', $container );

			// A table with no container/thead/tbody/loader has nowhere sane to reposition the
			// overlay relative to; `.position()`/`.offset()`/`.outerHeight()` on an empty set
			// return `undefined`, and every pixel computed below from a missing thead goes NaN.
			if ( ! $container.length || ! $thead.length || ! $tbody.length || ! $loader.length ) {
				return;
			}

			$.fn.isInViewport = function () {
				// `scrollX` splits one table into a header clone, the body, and a footer clone.
				// The clones carry only their own section, so `thead`/`tbody`/`tfoot` lookups
				// against them return empty sets, whose `offset()` is undefined.
				var offset = this.length ? $( this ).offset() : null;

				if ( ! offset ) {
					return false;
				}

				var elementTop = offset.top;
				var elementBottom = elementTop + $( this ).outerHeight();

				var viewportTop = $( window ).scrollTop();
				var viewportBottom = viewportTop + $( window ).height();

				return elementTop >= viewportTop && elementBottom <= viewportBottom;
			};

			var tbodyTop = $tbody.position().top;
			var theadHeight = $thead.outerHeight();
			var scrollTop = $( window ).scrollTop();
			var containerTop = $container.offset().top;
			var windowHeight = ( window.innerHeight || document.documentElement.clientHeight );
			var loaderHeight = $loader.outerHeight();
			var adjustedViewportTop = scrollTop - containerTop + theadHeight;
			var adjustedViewportBottom = scrollTop + windowHeight - containerTop - loaderHeight;
			var viewportTop = Math.max( 0, scrollTop - containerTop );
			var viewportBottom = Math.min( $container.outerHeight(), scrollTop + windowHeight - containerTop );
			var visibleTbodyTop = Math.min( viewportBottom - loaderHeight, Math.max( viewportTop, tbodyTop + theadHeight ) );

			// $table.position() is wrapper-relative, matching the loader's own offset parent below.
			// $thead.position() is NOT: browsers make a <table> the offsetParent of its own thead/
			// tbody/tfoot regardless of its own `position`, so thead.position() is table-relative and
			// would silently omit the height of the length/filter controls rendered above the table.
			var theadBottom = $table.position().top + theadHeight;

			var tableIsInViewport = $table.isInViewport();
			var topPosition;

			if ( tableIsInViewport && $tbody.height() > $loader.height() ) {
				// The full table is visible and the loader fits in the tbody. The default loader position works.
				topPosition = '50%';
			} else if ( tableIsInViewport ) {
				// If the full table is visible, but the loader is too big. Place it at the top of the tbody so it doesn't overlap the header.
				topPosition = visibleTbodyTop;
			} else if ( $tfoot.isInViewport() ) {
				// If the table is not in the viewport, but the footer is, place the loader near the footer.
				topPosition = ( ( $tfoot.position().top - adjustedViewportTop ) / 2 ) + adjustedViewportTop;
			} else if ( $thead.isInViewport() ) {
				topPosition = ( ( adjustedViewportBottom - visibleTbodyTop ) / 2 ) + visibleTbodyTop;
			}

			// No branch matched (e.g. table and thead both outside the viewport): fall through to
			// just below the header rather than leaving `top` undefined, which jQuery silently
			// ignores and strands the loader at the stock top:50%/margin-top:-26px.
			if ( 'undefined' === typeof topPosition ) {
				topPosition = theadBottom + 4;
			}

			// Pixel placements are absolute from the wrapper's top edge, so the header can never
			// be skipped, however the branches above resolved. The '50%' case is left alone: it is
			// only chosen when the tbody already fits below the header (guard above), so centering
			// within the table can't reach up into it.
			if ( '50%' !== topPosition ) {
				topPosition = Math.max( topPosition, theadBottom + 4 );
			}

			$loader.css( {
				position: 'absolute',
				top: topPosition,
				// Stock DataTables CSS ships margin-top:-26px, calibrated only for the top:50% centering
				// case. Every pixel placement above must cancel it or it lands 26px higher than computed.
				'margin-top': '50%' === topPosition ? '' : 0,
			} );
		},

		/**
		 * Export button `extend` values whose built-in action only sees the current page in
		 * server-side mode. Not `colvis`, and not any button that already carries its own
		 * `action` (the third-party escape hatch).
		 */
		EXPORT_EXTENDS: [ 'copy', 'copyHtml5', 'csv', 'csvHtml5', 'excel', 'excelHtml5', 'pdf', 'pdfHtml5', 'print' ],

		/**
		 * Set button options for DataTables
		 *
		 * @param {object} options Options for the DT instance
		 * @returns {Array} button settings
		 */
		setButtons: function ( options ) {

			var buttons = [];

			// extend the buttons export format
			if ( options && options.buttons && options.buttons.length > 0 ) {
				options.buttons.forEach( function ( button, i ) {
					if ( button.extend === 'print' ) {
						buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, gvDataTables.buttonCustomizePrint, button );
					} else if ( button.extend === 'csvHtml5' || button.extend === 'csv' ) {
						buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, gvDataTables.buttonCustomizeCsv, button );
					} else if ( button.extend === 'pdfHtml5' || button.extend === 'pdf' ) {
						buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, gvDataTables.buttonCustomizePdf, button );
					} else {
						buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, button );
					}

					if ( gvDataTables.EXPORT_EXTENDS.indexOf( buttons[ i ].extend ) !== -1 && ! buttons[ i ].action ) {
						// Buttons' own alias resolution (`copy` -> `copyHtml5`, etc.) clears `extend`
						// on the conf object the action eventually receives, so the originally
						// requested extend is captured here, at setup time, instead of read from
						// that conf at click time.
						buttons[ i ].action = ( function ( requestedExtend ) {
							return function ( e, dt, node, config ) {
								// Buttons calls this wrapper with `this` bound to the button's own Buttons
							// API instance (`dt.button(node)`); the built-in action needs that same
							// `this` (it calls `this.processing(...)` on it), so it is forwarded here
							// rather than losing it to a plain `gvDataTables.` method call.
							gvDataTables.fullDatasetExportAction.call( this, requestedExtend, e, dt, node, config );
							};
						}( buttons[ i ].extend ) );
					}
				} );
			}

			return buttons;
		},

		/**
		 * Maps an export button's `extend` to the stock action Buttons 2.3.6 registers for it.
		 * Buttons itself resolves the short names ('copy', 'csv', ...) through a redirect chain
		 * (each is a function that returns the *Html5 name to actually use), so this reproduces
		 * that resolution rather than re-implementing the chain walk.
		 *
		 * @param {string} extend The button's `extend` value.
		 * @returns {Function|null} The built-in action, or null if it can't be resolved.
		 */
		resolveBuiltinExportAction: function ( extend ) {
			var ALIASES = { copy: 'copyHtml5', csv: 'csvHtml5', excel: 'excelHtml5', pdf: 'pdfHtml5' };
			var name = ALIASES[ extend ] || extend;
			var buttonDef = $.fn.dataTable.ext.buttons[ name ];

			return ( buttonDef && 'function' === typeof buttonDef.action ) ? buttonDef.action : null;
		},

		/**
		 * Finds or creates the container's polite live region (shared with the Clear Filters
		 * button) and announces a message through it.
		 *
		 * @param {jQuery} $container The `.gv-datatables-container` element.
		 * @param {string} message
		 */
		announceLive: function ( $container, message ) {
			var $live = $container.find( '.gv-dt-live-region' );

			if ( ! $live.length ) {
				$live = $( '<div/>', {
					'class': 'gv-dt-live-region screen-reader-text',
					'aria-live': 'polite',
				} ).appendTo( $container );
			}

			$live.text( message || '' );
		},

		/**
		 * Refuses an export whose full row count exceeds the configured cap. `role="alert"`
		 * announces on its own, so this does not also go through the polite live region.
		 *
		 * @param {jQuery} $container The `.gv-datatables-container` element.
		 * @param {string} message
		 */
		renderExportRefusalNotice: function ( $container, message ) {
			var $existing = $container.find( '.gv-dt-export-notice' );

			if ( $existing.length ) {
				$existing.remove();
			}

			var $notice = $( '<div/>', {
				'class': 'gv-dt-export-notice',
				role: 'alert',
				text: message || '',
			} );

			var $table = $container.find( 'table.gv-datatables' ).first();

			if ( $table.length ) {
				$notice.insertBefore( $table );
			} else {
				$notice.prependTo( $container );
			}
		},

		/**
		 * Wraps an export button's built-in action so server-side mode exports the full filtered
		 * set instead of only the rows currently held client-side, bounded by a row cap.
		 *
		 * @since 3.11.0
		 *
		 * @param {string}              requestedExtend The button's `extend` as configured, captured
		 *                                               at setup time (see setButtons(): Buttons'
		 *                                               own alias resolution clears `extend` on the
		 *                                               `config` this action receives at click time).
		 * @param {jQuery.Event}        e      Click event.
		 * @param {DataTables.Api}      dt     The table's API instance.
		 * @param {node}                node   The button's DOM node.
		 * @param {object}              config The merged button config.
		 */
		fullDatasetExportAction: function ( requestedExtend, e, dt, node, config ) {
			var builtinAction = gvDataTables.resolveBuiltinExportAction( requestedExtend );

			if ( ! builtinAction ) {
				if ( window.console && window.console.warn ) {
					window.console.warn( 'GravityView DataTables: could not resolve the built-in action for export button "' + requestedExtend + '"' );
				}

				return;
			}

			var info     = dt.page.info();
			var settings = dt.settings()[ 0 ];

			// Already complete client-side: nothing to fetch, run the export as-is.
			if ( ! settings.oFeatures.bServerSide || -1 === info.length || info.recordsDisplay <= info.length ) {
				builtinAction.call( this, e, dt, node, config );

				return;
			}

			var gvExport   = dt.init().gvExport || {};
			var $container = $( dt.table().container() ).closest( '.gv-datatables-container' );

			if ( gvExport.maxRows > 0 && info.recordsDisplay > gvExport.maxRows ) {
				gvDataTables.renderExportRefusalNotice( $container, gvExport.tooLarge );

				return;
			}

			// Keyed by table instance, not View ID: two embeds of the same View exporting at the
			// same time must not share one "already in flight" flag.
			var instanceKey = dt.init().instanceKey;
			var state       = gvDataTables.tables[ instanceKey ];

			if ( state && state.exportInFlight ) {
				return;
			}

			if ( state ) {
				state.exportInFlight = true;
			}

			var buttonApi = dt.button( node );

			$( node ).attr( 'aria-busy', 'true' );

			if ( buttonApi && buttonApi.processing ) {
				buttonApi.processing( node, true );
			}

			gvDataTables.announceLive( $container, gvExport.exporting );

			// `emptyServerResponse` is never cleared once set, so only a value that flips during
			// THIS fetch says anything about it; reading the flag itself would refuse every
			// export on a table that came back empty once, however long ago.
			var wasEmptyBeforeExport = !! ( state && state.emptyServerResponse );

			var prevLength = info.length;
			var prevPage   = info.page;
			var actionThis = this;

			var clearBusyState = function () {
				window.clearTimeout( busyTimeout );

				if ( state ) {
					state.exportInFlight = false;
				}

				$( node ).removeAttr( 'aria-busy' );

				if ( buttonApi && buttonApi.processing ) {
					buttonApi.processing( node, false );
				}

				dt.off( '.gvExport' );

				// Every path into this function (success, error.dt, and the busy-timeout fallback)
				// left `length(-1)` in place on the visitor's table until this restored it, so it
				// belongs here rather than duplicated after each caller.
				dt.page.len( prevLength ).page( prevPage ).draw( 'page' );
			};

			// A response that never lands at all fires neither listener below, and the button
			// would then refuse every later export.
			var busyTimeout = window.setTimeout( clearBusyState, 60000 );

			// A resort, a filter change, or the auto-update timer can all issue their own draw
			// against this same table while the `length(-1)` fetch is still in flight, and once
			// it is in flight `page.info().length` already reads -1 for every draw regardless of
			// which request produced it, so only the request's own draw number tells them apart.
			// `on()`, not `one()`: an intervening draw must be ignored, not consume the listener
			// that our own draw still needs.
			var expectedDraw;

			// Whichever of these two fires first unbinds the other. That is what keeps a refused
			// fetch from exporting an empty file: DataTables fires `error` from _fnLog() for the
			// payload's own `error` key BEFORE it updates the table, so error.dt lands first and
			// clearBusyState() unbinds the draw handler below while the envelope's zero rows are
			// still on their way in.
			dt.on( 'draw.dt.gvExport', function () {
				if ( settings.iDraw !== expectedDraw ) {
					return;
				}

				// Two refusals reach here looking like a clean draw of zero rows, because
				// neither carries the `error` key that would have fired error.dt first: an
				// administrator whose site filters `.../response/error-payload` into a payload
				// without it (admins receive the filter's array verbatim, so only they can see
				// this shape), and an empty response body, which the dataFilter answers with a
				// fabricated success envelope. Exporting either writes a file silently missing
				// every row.
				var cameBackEmpty = state && state.emptyServerResponse && ! wasEmptyBeforeExport;

				if ( state && ( state.serverError || cameBackEmpty ) ) {
					gvDataTables.announceLive( $container, '' );

					clearBusyState();

					return;
				}

				// Runs while the table is still at `length(-1)`: clearBusyState() below is what
				// restores the visitor's page length, and must come after, not before.
				builtinAction.call( actionThis, e, dt, node, config );

				clearBusyState();
			} );

			dt.one( 'error.dt.gvExport', clearBusyState );

			dt.page.len( -1 ).draw();

			expectedDraw = settings.iDraw;
		},

		/**
		 * Whether a link's destination only repeats the text it sits behind.
		 *
		 * An export appends the destination after the label, which is the only way a File Upload or
		 * Website column carries its value. On a `mailto:` or `tel:` link the address is the label
		 * with a scheme in front, so appending it says the same thing twice.
		 *
		 * @since 3.13.0
		 *
		 * @param {string} href  The link destination.
		 * @param {string} label The link text.
		 *
		 * @return {boolean} Whether the destination adds nothing to the label.
		 */
		exportHrefRepeatsLabel: function ( href, label ) {
			if ( href === label ) {
				return true;
			}

			var scheme = href.slice( 0, href.indexOf( ':' ) + 1 ).toLowerCase();

			if ( 'mailto:' !== scheme && 'tel:' !== scheme ) {
				return false;
			}

			// Everything past a `?` is a subject and body, which no label carries.
			var target  = href.slice( scheme.length ).split( '?' )[ 0 ];
			var decoded = target;

			try {
				// A number's spaces and brackets reach the href as escapes, and `%20` would
				// otherwise survive the separator strip below.
				decoded = decodeURIComponent( target );
			} catch ( e ) {
				// A malformed escape leaves the address exactly as it arrived.
				decoded = target;
			}

			if ( 'mailto:' === scheme ) {
				return decoded === label;
			}

			// Only the characters that space a number out for reading, so a destination carrying
			// anything the label does not -- letters, an `;ext=` -- still counts as its own value.
			var separators = /[\s().\-–—]/g;

			return decoded.replace( separators, '' ) === label.replace( separators, '' );
		},

		/**
		 * Extend the buttons exportData format
		 * @since 2.0
		 * @link http://datatables.net/extensions/buttons/examples/html5/outputFormat-function.html
		 */
		buttonCommon: {
			exportOptions: {
				columns: function ( idx, data, node ) {
					var $wrapperEl = $( node ).closest( 'div.dataTables_wrapper' );

					if ( !$wrapperEl.length ) {
						return $( node ).is( ':visible' );
					}

					var $tableEl = $wrapperEl.find( 'table.gv-datatables' );

					if ( !$.fn.DataTable.isDataTable( $tableEl ) ) {
						return $( node ).is( ':visible' );
					}

					return $tableEl.dataTable().api().columns().visible()[ idx ];
				},
				format: {
					header: function ( data, columnIdx, row ) {
						return $( row ).find( '.gv-field-label' ).text();
					},
					body: function ( data, column, row ) {

						var newValue = data;

						// Don't process if empty
						if ( newValue.length === 0 ) {
							return newValue;
						}

						newValue = newValue.replace( /\n/g, ' ' ); // Replace new lines with spaces

						/**
						 * Changed to jQuery in 1.2.2 to make it more consistent. Regex not always to be trusted!
						 */
						newValue = $( '<span>' + newValue + '</span>' ) // Wrap in span to allow for $() closure
							.find( 'a' ).replaceWith( function () {
								// .text() below would keep the label and drop the destination, which
								// empties File Upload, Website and Entry Link columns of their value.
								var $link = $( this );
								var label = $link.text().trim();
								var href  = ( $link.attr( 'href' ) || '' ).trim();

								// "Link to single entry" points at a page, not at the value the column
								// is showing, so the address belongs in the browser and not in a cell.
								// Handing back the link's own nodes keeps whatever it wrapped, including
								// an image the pass below still has to turn into its alt text.
								if ( $link.is( '[data-gv-export-label]' ) ) {
									var $held = $link.contents();

									return $held.length ? $held : document.createTextNode( '' );
								}

								// A text node, not a string: label/href came out of .text(), which decodes
								// entities, so a field value that merely looks like markup (e.g. an
								// entity-encoded "<img ...>") would otherwise get re-parsed as real markup
								// by replaceWith(), and rendered.
								if ( '' === href || '' === label ) {
									return document.createTextNode( label || href );
								}

								if ( gvDataTables.exportHrefRepeatsLabel( href, label ) ) {
									return document.createTextNode( label );
								}

								return document.createTextNode( label + ' (' + href + ')' );
							} ).end()
							.find( 'li' ).after( '; ' ).end() // Separate <li></li> with ;
							.find( 'img' ).replaceWith( function () {
								return $( this ).attr( 'alt' ); // Replace <img> tags with the image's alt tag
							} ).end()
							.find( '.dashicons.dashicons-yes' ).replaceWith( function () {
								return '&#10004;'; // Replace Dashicons with checkmark emoji
							} ).end()
							.find( 'br' ).replaceWith( ' ' ).end() // Replace <br> with space
							.find( '.map-it-link' ).remove().end() // Remove "Map It" link
							.text(); // Strip all tags

						return newValue;
					},
				},
			},
		},

		/**
		 * Wide tables lose columns in PDF: pdfmake does not split horizontally, so anything past
		 * the page edge is in no page of the file. Landscape A3 fits far more, and the sizes are
		 * filterable for tables wider still.
		 */
		/**
		 * Excel on Windows reads a CSV without a BOM as the system codepage, which mangles
		 * accented and non-Latin values. Entry data can also start with a spreadsheet formula
		 * prefix, which spreadsheet apps execute on open, so those cells are prefixed with a
		 * tab to keep them inert while staying readable.
		 */
		buttonCustomizeCsv: {
			charset: 'UTF-8',
			bom: true,
			exportOptions: {
				format: {
					body: function ( data, column, row ) {
						var value = gvDataTables.buttonCommon.exportOptions.format.body( data, column, row );
						var opensAsFormula = /^[=+\-@\t\r]/.test( String( value ) );

						return opensAsFormula ? '\t' + value : value;
					},
				},
			},
		},

		buttonCustomizePdf: {
			orientation: 'landscape',
			pageSize: 'A3',
		},

		buttonCustomizePrint: {
			customize: function ( win ) {
				$( win.document.body ).find( 'table' )
					.addClass( 'compact' )
					.css( 'font-size', 'inherit' )
					.css( 'table-layout', 'auto' );
			},
		},

		/**
		 * Responsive Extension: Function that is called for display of the child row data, when view setting "Hide Empty" is enabled.
		 * @see assets/datatables-responsive/js/dataTables.responsive.js Responsive.defaults.details.renderer method
		 */
		customResponsiveRowRenderer: function ( api, rowIdx ) {
			var data = api.cells( rowIdx, ':hidden' ).eq( 0 ).map( function ( cell ) {
				var header = $( api.column( cell.column ).header() );

				if ( header.hasClass( 'control' ) || header.hasClass( 'never' ) ) {
					return '';
				}

				var idx = api.cell( cell ).index();

				// GV custom part: if field value is empty
				if ( api.cell( cell ).data().length === 0 ) {
					return '';
				}

				// Use a non-public DT API method to render the data for display
				// This needs to be updated when DT adds a suitable method for
				// this type of data retrieval
				var dtPrivate = api.settings()[ 0 ];
				var cellData = dtPrivate.oApi._fnGetCellData( dtPrivate, idx.row, idx.column, 'display' );

				return '<li data-dtr-index="' + idx.column + '">' + '<span class="dtr-title">' + header.find( '.gv-dt-field-filter' ).remove().end().text() + ':' + '</span> ' + '<span class="dtr-data">' + cellData + '</span>' + '</li>';
			} ).toArray().join( '' );

			return data ? $( '<ul data-dtr-index="' + rowIdx + '"/>' ).append( data ) : false;
		},
	};

	$( document ).ready( function () {
		gvDataTables.init();

		// No tables were initialized.
		if ( !Object.keys( gvDataTables.tables ).length ) {
			return;
		}

		// Iterated per Search Bar rather than per entry in the tables registry. That registry
		// holds a bare View id aliased to the first embed, so keying off it selected EVERY form
		// of that View at once: one Bar's Clear reset its siblings' inputs as well as its own.
		$( 'form.gv-widget-search' ).each( function () {
			const $searchWidgetForm = $( this );
			const viewId = String( $searchWidgetForm.attr( 'data-viewid' ) || '' );

			// Views without a DataTable on this page keep core's own behaviour.
			if ( ! viewId || ! gvDataTables.tables[ viewId ] ) {
				return;
			}

			// Reset search results.
			$( '.gv-search-clear', $searchWidgetForm ).off().on( 'click', function ( e ) {
				var $table = gvDataTables.resolveSearchBarTable( $searchWidgetForm );
				var tableState = gvDataTables.resolveTableState( $table, viewId );

				// Resolved BEFORE the default is prevented. This Bar's embed may own no table,
				// and preventing first would leave the visitor a Clear button that does nothing
				// at all; letting the link follow its href returns them to the unfiltered URL,
				// which is what clearing means.
				if ( ! $table.length || ! tableState ) {
					return;
				}

				// prevent event from bubbling and firing
				e.preventDefault();
				e.stopImmediatePropagation();

				var tableData = tableState.data ?? null;

				// clear form fields. because default input values are set, form.reset() does not work.
				// instead, a more comprehensive solution is required: https://stackoverflow.com/questions/680241/resetting-a-multi-stage-form-with-jquery/24496012#24496012

				$( 'input[type="search"], input:text, input:password, input:file, input[type="number"], select, textarea', $searchWidgetForm ).val( '' );
				$( 'input:checkbox, input:radio', $searchWidgetForm ).removeAttr( 'checked' ).removeAttr( 'selected' );

				// The date pickers keep their values in hidden inputs and a widget the
				// selectors above never reach; clear them through the picker
				// instances. Instance data is the discriminator (not `data-gv-config`)
				// so legacy `.gv-datepicker` mounts are covered too.
				$( '*', $searchWidgetForm ).filter( function () {
					return !!$( this ).data( 'gv-picker-instance' );
				} ).each( function () {
					const instance = $( this ).data( 'gv-picker-instance' );

					if ( typeof instance.updateRange === 'function' ) {
						instance.updateRange( { start: null, end: null } );
					} else if ( typeof instance.updateDate === 'function' ) {
						instance.updateDate( { date: null } );
					}
				} );

				// assign new data to the global object
				if ( tableData ) {
					tableData.getData = false;
					tableState.data = tableData;
				}

				// remove search query from URL
				const url = new URL( window.location.href );

				[ 'gv_search', 'mode' ].forEach( param => url.searchParams.delete( param ) );

				[ ...url.searchParams.keys() ].forEach( key => key.startsWith( 'filter_' ) && url.searchParams.delete( key ) );

				[ ...url.searchParams.keys() ].forEach( key => key.startsWith( 'gv_' ) && url.searchParams.delete( key ) );

				window.history.pushState( null, null, url.toString() );

				// update form state
				$searchWidgetForm.removeClass( 'gv-is-search' );

				// The picker updates flush their hidden inputs asynchronously and their
				// change events re-show the button against the stale form state, so the
				// pristine snapshot and the hide both run after that flush.
				const $clearButton = $( this );

				window.setTimeout( function () {
					$searchWidgetForm.attr( 'data-state', $searchWidgetForm.serialize() );

					if ( window.gvGlobals && gvGlobals.clear ) {
						$clearButton.text( gvGlobals.clear );
					}

					$clearButton.stop( true, true ).hide();
				}, 50 );

				const currentTableOptions = $table.DataTable().init();

				if ( currentTableOptions._ajax ) {
					// If Ajax was disabled before, re-enable it.
					$table.DataTable().settings()[ 0 ].ajax = currentTableOptions._ajax;
				}

				const shouldUseAjax = currentTableOptions.serverSide ||
					( !currentTableOptions.serverSide && !tableState.allRecordsLoaded );

				// Reload table.
				if ( shouldUseAjax ) {
					tableState.allRecordsLoaded = true;

					$table.DataTable().ajax.reload();
				} else {
					$table.DataTable().draw();
				}

			} );

			// Handle search.
			$searchWidgetForm.on( 'submit', function ( e ) {
				var getData = {};
				var $table = gvDataTables.resolveSearchBarTable( $searchWidgetForm );
				var tableState = gvDataTables.resolveTableState( $table, viewId );

				// Resolved BEFORE the default is prevented, so a Bar whose embed owns no table
				// still submits normally rather than becoming inert.
				if ( ! $table.length || ! tableState ) {
					return;
				}

				e.preventDefault();

				var tableData = tableState.data ?? null;
				var inputs = $( this ).serializeArray().filter( function ( k ) {
					return $.trim( k.value ) !== '';
				} );

				// For date range searches, ensure both start and end are included even if empty.
				$( this ).find( '.gv-search-date-range' ).each( function () {
					// Check for entry date inputs (gv_start/gv_end).
					let startInput = $( this ).find( '[name="gv_start"]' );
					let endInput = $( this ).find( '[name="gv_end"]' );
					let startName, endName;

					if ( startInput.length ) {
						// Entry date field
						startName = 'gv_start';
						endName = 'gv_end';
					} else {
						// Regular date field - find filter_X[start] and filter_X[end]
						startInput = $( this ).find( 'input[name*="[start]"]' );
						endInput = $( this ).find( 'input[name*="[end]"]' );

						if ( startInput.length ) {
							startName = startInput.attr( 'name' );
							endName = endInput.attr( 'name' );
						}
					}

					if ( startName ) {
						const hasStart = inputs.some( input => input.name === startName );
						const hasEnd = inputs.some( input => input.name === endName );

						// Only add empty values if at least one of them already has a value (user is searching with this date range)
						if ( hasStart || hasEnd ) {
							if ( !hasStart ) {
								inputs.push( { name: startName, value: '' } );
							}
							if ( !hasEnd ) {
								inputs.push( { name: endName, value: '' } );
							}
						}
					}
				} );

				// handle form state
				if ( $( this ).serialize() === $( this ).attr( 'data-state' ) ) {
					return;
				} else {
					$( this ).attr( 'data-state', $( this ).serialize() );
				}

				// submit form if table data is not set
				if ( !tableData ) {
					this.submit();
					return;
				}

				if ( tableData.hideUntilSearched * 1 ) {
					delete ( tableData.hideUntilSearched );
				}

				getData = convertFormValuesToJSON( inputs );

				// reset cached search values
				tableData.search = { 'value': '' };
				tableData.getData = ( Object.keys( getData ).length > 1 ) ? JSON.stringify( getData ) : false;

				// set or clear URL with search query
				if ( tableData.setUrlOnSearch ) {
					const baseUrl = window.location.origin + window.location.pathname;
					const queryString = $( this ).serialize();
					const url = new URL( baseUrl );

					if ( queryString ) {
						const formParams = new URLSearchParams( queryString );

						formParams.forEach( ( value, key ) => {
							if ( value !== '' ) {
								url.searchParams.append( key, value );
							}
						} );
					}

					if ( !tableData.getData ) {
						url.searchParams.delete( 'mode' );
					}

					window.history.pushState( null, null, url.toString() );

					// Additionally, update the href of each Export Link widget, if they exist.
					// Scoped to this embed: the View's container id returns the first embed's
					// container for every Search Bar of a repeated View, and a nested embed's
					// links sit inside the outer one's subtree.
					const embedSelector = '[id^="gv-view-' + viewId + '-"]';
					const tableNode     = $table.get( 0 );
					const embedWrapper  = tableNode && tableNode.closest ? tableNode.closest( embedSelector ) : null;

					// No wrapper (older template): the container's parent, as before.
					const $exportScope = embedWrapper ? $( embedWrapper ) : $table.closest( '.gv-datatables-container' ).parent();

					$exportScope.find( '.gv-widget-export-link a[data-nonce-url]' ).filter( function () {
						return ! embedWrapper || this.closest( embedSelector ) === embedWrapper;
					} ).each( function() {

						let anchorUrl = new URL( $( this ).data( 'nonce-url' ) );

						// Merge existing query parameters from the anchor with the new ones
						url.searchParams.forEach( ( value, key ) => anchorUrl.searchParams.set( key, value ) );

						$( this ).attr( 'href', anchorUrl.toString() );
					} );
				}

				// assign new data to the global object
				tableState.data = tableData;

				const currentTableOptions = $table.DataTable().init();

				if ( currentTableOptions._ajax ) {
					// If Ajax was disabled before, re-enable it.
					$table.DataTable().settings()[ 0 ].ajax = currentTableOptions._ajax;
				}

				// update form state
				$( this ).addClass( 'gv-is-search' ).attr( 'data-state', $( this ).serialize() ).trigger( 'keyup' );
				$( '.gv-search-clear', $( this ) ).text( gvGlobals.clear );

				// Check if search bar uses inputs that don't map to columns (e.g., "search everything").
				// If so, we will use Ajax to search when client-side processing is enabled.
				const filters = Object.keys( JSON.parse( tableData.getData ) );
				let filterIndex = 0;
				let nonColumnSearch = false;

				while ( filterIndex < filters.length && !nonColumnSearch ) {
					const filterKey = filters[ filterIndex ];
					let searchBarInputName = filterKey.replace( 'filter_', 'gv_' );

					// Map entry date inputs to date_created column.
					searchBarInputName = [ 'gv_start', 'gv_end' ].includes( searchBarInputName ) ? 'gv_date_created' : searchBarInputName;

					if ( searchBarInputName === 'mode' ) {
						filterIndex++;
						continue;
					}

					/*jshint -W083 */
					nonColumnSearch = !currentTableOptions.columns.some( tableColumn => tableColumn.name === searchBarInputName );
					/*jshint +W083 */

					filterIndex++;
				}

				const shouldUseAjax = currentTableOptions.serverSide ||
					( !currentTableOptions.serverSide && ( nonColumnSearch || !tableState.allRecordsLoaded ) );

				if ( shouldUseAjax ) {
					tableState.allRecordsLoaded = !tableData.getData;

					$table.DataTable().ajax.reload();
				} else {
					$table.DataTable().draw();
				}
			} );
		});
	} );

	// Converts seconds to HH:MM:SS format. Used to format duration in footer calculations.
	function convertSecondsToHMS( seconds, padding = 2 ) {
		const pad = ( num ) => num.toString().padStart( padding, '0' );

		const hours = Math.floor( seconds / 3600 );
		const minutes = Math.floor( ( seconds % 3600 ) / 60 );
		const secs = seconds % 60;

		return [ hours, minutes, secs ].map( pad ).join( ':' );
	}

	// Converts seconds to "X hours, X minutes, X seconds" format. Used to format duration in footer calculations.
	function convertSecondsToHumanReadableHMS( seconds ) {
		const hours = Math.floor( seconds / 3600 );
		seconds %= 3600;
		const minutes = Math.floor( seconds / 60 );
		seconds %= 60;

		const parts = [];
		if ( hours > 0 ) parts.push( hours + ' ' + ( hours === 1 ? 'hour' : 'hours' ) );
		if ( minutes > 0 ) parts.push( minutes + ' ' + ( minutes === 1 ? 'minute' : 'minutes' ) );
		if ( seconds > 0 ) parts.push( seconds + ' ' + ( seconds === 1 ? 'second' : 'seconds' ) );

		return parts.length > 0 ? parts.join( ', ' ) : '0 seconds';
	}

	// Formats currency values in footer calculations.
	function formatCurrency( amount, currency, locale = 'en-US', decimals = 2 ) {
		const formatter = new Intl.NumberFormat( locale, {
			style: 'currency',
			currency: currency,
			minimumFractionDigits: decimals,
			maximumFractionDigits: decimals,
			currencyDisplay: 'narrowSymbol' // You can also use 'code' to display the currency code (e.g., EUR)
		} );

		return formatter.format( amount );
	}

	// Parse date string based on format (mdy, dmy, ymd) and convert to timestamp.
	function parseDateString( dateStr, format ) {
		if (!dateStr) {
			return 0;
		}

		const parts = dateStr.split(/[\/\-\.]/);

		if ( parts.length !== 3 ) {
			return 0;
		}

			let year, month, day;

		if ( format === 'mdy' ) {
			[ month, day, year ] = parts;
		} else if ( format === 'dmy' ) {
			[ day, month, year ] = parts;
		} else if ( format === 'ymd' ) {
			[ year, month, day ] = parts;
		}

		if ( !year || !month || !day ) {
			return 0;
		}

		// Convert to ISO format (YYYY-MM-DD) and create timestamp.
		const isoDate = `${ year }-${ month.padStart( 2, '0' ) }-${ day.padStart( 2, '0' ) }`;

		return ( new Date( isoDate + 'T00:00:00Z' ) ).getTime();
	}

	// Custom filter and search function for client-side processing.
	// Search widget values are first used to filter data, followed by column filters.
	function configureClientSideFilterAndSearch() {
		$.fn.dataTable.ext.search.push( function ( settings, searchData, dataIndex ) {
			const viewId = settings.oInit.viewid;
			// This table's own per-embed state key: shadowData is per-INSTANCE (it mirrors this
			// table's own displayed rows), so two embeds of the same View must not read each
			// other's shadowData through a shared View-ID-keyed lookup here.
			const instanceKey = settings.oInit.instanceKey;

			// Not a GravityView table (e.g. a third-party DataTable on the
			// same page) — leave its rows alone.
			const tableEntry = undefined === instanceKey ? null : gvDataTables.tables[ instanceKey ];

			if ( ! tableEntry ) {
				return true;
			}

			const shadowData = tableEntry.shadowData;
			// A View embedded twice renders one Search Bar per embed, both carrying the same
			// View ID, so a bare-ID lookup hands the first embed's values to every table.
			const form = gvDataTables.resolveEmbedSearchForm( settings.nTable, viewId );
			const searchWidgetMode = $( 'input[type="hidden"][name="mode"]', form ).val();
			const columns = settings.aoColumns;

			let searchWidgetValues = [];

			if ( form.hasClass( 'gv-is-search' ) ) {
				// Get search values from search widget fields.
				const supportedInputTypes = [
					'input[type="search"]',
					'input[type="text"]',
					'input[type="password"]',
					'input[type="file"]',
					'input[type="radio"]:checked',
					'input[type="checkbox"]:checked',
					'textarea',
					'select',
				].join( ',' );

				searchWidgetValues = form.find( supportedInputTypes ).map( function () {
					const inputName = $( this ).attr( 'name' ) || '';

					if ( ! inputName ) {
						return null;
					}

					// Skip date range inputs and date inputs - they'll be processed separately.
					if ( inputName === 'gv_start' || inputName === 'gv_end' || inputName.match( /\[start\]$|\[end\]$/ ) ) {
						return null;
					}

					// Skip date inputs that are in date search elements.
					const isInDateElement = $( this ).closest( '.gv-search-date' ).length > 0;

					if ( isInDateElement && inputName.match( /^filter_\d+$/ ) ) {
						return null;
					}

					// `<select multiple>` returns an array from .val(), which has no
					// .toLocaleLowerCase(); join its selected values the same way the
					// contains-test below already treats a multi-value selection.
					const rawValue = $( this ).val();
					const value = ( Array.isArray( rawValue ) ? rawValue.join( ' ' ) : ( rawValue ?? '' ) )
						.toString()
						.toLocaleLowerCase()
						.trim();

					return {
						value: value,
						filterName: inputName
					};
				} ).get().filter( sv => sv !== null && sv.value !== '' );

				// Date searches (both single and range).
				form.find( '.gv-search-date' ).each( function () {
					// Check for entry date inputs (gv_start/gv_end) or regular date field inputs (filter_X[start]/filter_X[end])
					let startInput = $( this ).find( '[name="gv_start"]' );
					let endInput = $( this ).find( '[name="gv_end"]' );
					let isEntryDate = startInput.length > 0;

					if ( !isEntryDate ) {
						// Regular date field - find filter_X[start] and filter_X[end].
						startInput = $( this ).find( 'input[name*="[start]"]' );
						endInput = $( this ).find( 'input[name*="[end]"]' );

						// If no range inputs found, look for single date input.
						if ( startInput.length === 0 && endInput.length === 0 ) {
							startInput = $( this ).find( 'input' );
						}
					}

					const start = ( startInput.val() || '' ).trim();
					const end = ( endInput.val() || '' ).trim();

					if ( start || end ) {
						let columnName;

						if ( isEntryDate ) {
							// Entry date maps to gv_date_created column.
							columnName = 'gv_date_created';
						} else {
							// Regular date field - extract field ID from input name like "filter_3[start]" or "filter_3".
							const nameMatch = startInput.attr( 'name' ).match( /filter_(\d+)/ );

							columnName = nameMatch ? 'gv_' + nameMatch[1] : '';
						}

						// Detect date format from input class (mdy, dmy, ymd).
						const inputClass = startInput.attr( 'class' ) || '';
						let dateFormat = 'mdy'; // default.

						if ( inputClass.includes( 'dmy' ) ) {
							dateFormat = 'dmy';
						} else if ( inputClass.includes( 'ymd' ) ) {
							dateFormat = 'ymd';
						}

						// Check if this is a range input by looking for [start] or [end] in the input name, or if it's entry date.
						const isRangeInput = startInput.attr( 'name' ).includes( '[start]' ) || startInput.attr( 'name' ).includes( '[end]' ) || isEntryDate;

						if ( isRangeInput ) {
							// Date range input.
							searchWidgetValues.push( {
								filterName: columnName,
								value: { start: start, end: end },
								inputType: 'date_range',
								dateFormat: dateFormat
							} );
						} else {
							// Single date input.
							searchWidgetValues.push( {
								filterName: columnName,
								value: start,
								inputType: 'date_single',
								dateFormat: dateFormat
							} );
						}
					}
				} );

				// Number range searches.
				form.find( '.gv-search-number-range' ).each( function () {
					const inputs = $( this ).find( 'input[type="number"]' );
					const minInput = inputs.filter( '[name*="[min]"]' );
					const maxInput = inputs.filter( '[name*="[max]"]' );
					const min = ( minInput.val() || '' ).trim();
					const max = ( maxInput.val() || '' ).trim();

					if ( min || max ) {
						const filterName = minInput.attr( 'name' ).replace( '[min]', '' );

						searchWidgetValues.push( {
							filterName: filterName,
							value: { min: min, max: max },
							inputType: 'number_range'
						} );
					}
				} );
			}

			// Get columns with filter search values.
			const filterValues = columns.map( ( column ) => {
				const fieldFiltersColumn = settings.oInit.field_filters === 'footer' ? $( column.nTf ) : $( column.nTh );

				let value = '';
				const columnFilterValue = fieldFiltersColumn.find( 'input, select' ).val() ?? '';

				if ( [ 'date', 'date_range' ].includes( column.atts?.field_type ) ) {
					const dateInputs = fieldFiltersColumn.find( 'input' );

					value = `${ dateInputs.eq( 0 ).val() }--${ dateInputs.eq( 1 ).val() }`.replace( /--$/, '' ); // Format: `start--end`.
				}

				return {
					value: ( value || columnFilterValue ).toString().toLocaleLowerCase().trim(),
					columnIndex: column.idx,
					column
				};
			} ).filter( sv => sv.value !== undefined && sv.value !== '' );

			// If search is not performed, return true to include the row.
			if ( searchWidgetValues.length === 0 && filterValues.length === 0 ) {
				return true;
			}

			// First check if the row matches search widget values, if applicable.
			// This should be checked only if all records are loaded since otherwise the DT already contains filtered data returned by the server.
			if ( searchWidgetValues.length && tableEntry.allRecordsLoaded ) {
				const searchFn = ( { value: searchWidgetValue, filterName, inputType, dateFormat } ) => {
					// Date range search requires additional processing.
					if ( inputType === 'date_range' ) {
						// Find the date column by filterName.
						const column = columns.find( column => column.name === filterName );

						if ( !column ) {
							return true;
						}

						const cellValue = shadowData?.[ dataIndex ]?.[ column.idx ] ?? '';
						const cellTimestamp = parseInt( cellValue, 10 );
						const start = searchWidgetValue.start;
						const end = searchWidgetValue.end;

						if ( isNaN( cellTimestamp ) ) {
							return false;
						}

						const startTimestamp = parseDateString( start, dateFormat );
						const endTimestamp = parseDateString( end, dateFormat );

						if ( startTimestamp && endTimestamp ) {
							return cellTimestamp >= startTimestamp && cellTimestamp <= endTimestamp;
						}

						if ( startTimestamp ) {
							return cellTimestamp >= startTimestamp;
						}

						if ( endTimestamp ) {
							return cellTimestamp <= endTimestamp;
						}

						return true;
					}

					// Single date search requires additional processing.
					if ( inputType === 'date_single' ) {
						const column = columns.find( column => column.name === filterName );

						if ( !column ) {
							return true;
						}

						const cellValue = shadowData?.[ dataIndex ]?.[ column.idx ] ?? '';
						const cellTimestamp = parseInt( cellValue, 10 );

						if ( isNaN( cellTimestamp ) ) {
							return false;
						}

						// Parse search date.
						const searchTimestamp = parseDateString( searchWidgetValue, dateFormat );

						if ( !searchTimestamp ) {
							return true;
						}

						// Compare dates (ignore time).
						const cellDate = new Date( cellTimestamp );
						const searchDate = new Date( searchTimestamp );
						const cellDateOnly = new Date( cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate() );
						const searchDateOnly = new Date( searchDate.getFullYear(), searchDate.getMonth(), searchDate.getDate() );

						return cellDateOnly.getTime() === searchDateOnly.getTime();
					}

					// Number range search requires additional processing.
					if ( inputType === 'number_range' ) {
						const column = columns.find( column => filterName === column.name.replace( 'gv_', 'filter_' ).replace( /\./g, '_' ) );

						if ( !column ) {
							return true;
						}

						const cellValue = shadowData?.[ dataIndex ]?.[ column.idx ] ?? '';
						const num = parseFloat( cellValue.toString().trim() );
						const min = searchWidgetValue.min;
						const max = searchWidgetValue.max;

						if ( isNaN(num) ) {
							return false;
						}

						if ( min && max ) {
							return num >= parseFloat(min) && num <= parseFloat(max);
						}

						if ( min ) {
							return num >= parseFloat(min);
						}

						if ( max ) {
							return num <= parseFloat(max);
						}

						return true;
					}

					const column = columns.find( column => filterName === column.name.replace( 'gv_', 'filter_' ).replace( '.', '_' ) );
					const exactMatch = ( searchWidgetValue.startsWith( '"' ) && searchWidgetValue.endsWith( '"' ) ) || column?.atts?.field_type === 'select';

					// Is "search everything" used or the mode is "any"? Match any values in the row.
					if ( filterName === 'gv_search' || searchWidgetMode !== 'all' ) {
						return shadowData[ dataIndex ].some( cellValue => {
							cellValue = ( cellValue ?? '' ).toString().toLocaleLowerCase().trim();

							return exactMatch ? cellValue === searchWidgetValue.replace( /"/g, '' ) : cellValue.includes( searchWidgetValue );
						} );
					}

					// If the search bar input is not mapped to a DT column, return true to include the row.
					if ( !column ) {
						return true;
					}

					let cellValue = shadowData?.[ dataIndex ]?.[ column.idx ] ?? '';

					cellValue = cellValue.toString().toLocaleLowerCase().trim();

					return exactMatch ? cellValue === searchWidgetValue.replace( /"/g, '' ) : cellValue.includes( searchWidgetValue );
				};

				const searchWidgetResult = searchWidgetMode === 'all' ? searchWidgetValues.every( searchFn ) : searchWidgetValues.some( searchFn );

				if ( !searchWidgetResult ) {
					return false;
				}
			}

			// Then check if row cells match column filter values.
			return filterValues.every( ( { value: filterValue, columnIndex, column } ) => {
				let cellValue = shadowData?.[ dataIndex ]?.[ columnIndex ] ?? ( searchData[ column.idx ] ?? '' );

				cellValue = cellValue.toString().toLocaleLowerCase().trim();

				filterValue = filterValue ?? '';

				const exactMatch = ( filterValue.startsWith( '"' ) && filterValue.endsWith( '"' ) ) || settings.oInit.aoColumns[ columnIndex ]?.atts?.field_type === 'select';

				if ( [ 'date', 'date_range' ].includes( column.atts.field_type ) ) {
					const [ fromDate, toDate ] = filterValue.split( '--' );

					const fromDateStamp = ( new Date( fromDate + 'T00:00:00Z' ) ).getTime() || 0;
					const toDateStamp = ( new Date( toDate + 'T00:00:00Z' ) ).getTime() || 0;

					cellValue = parseInt( cellValue, 10 );

					return ( fromDateStamp === 0 && toDateStamp === 0 ) ||
						( fromDateStamp === 0 && cellValue === toDateStamp ) ||
						( toDateStamp === 0 && cellValue === fromDateStamp ) ||
						( cellValue >= fromDateStamp && cellValue <= toDateStamp );
				}

				return exactMatch ? cellValue === filterValue.replace( /"/g, '' ) : cellValue.includes( filterValue );
			} );
		} );
	}

	// Convert multidimensional form values (e.g., [ { name: 'foo[bar][bar1]', value: 'xyz' }, { name: 'baz', value: 'bax' } ]) to JSON object (e.g., {"foo":{"bar":{"bar1":"xyz"}},"baz":"bax"}).
	function convertFormValuesToJSON( formValues ) {
		const result = {};

		formValues.forEach( item => {
			const { name, value } = item;

			if ( !name.includes( '[' ) ) {
				result[ name ] = value;

				return;
			}

			const keys = name.split( /\[|\]\[|\]/ ).filter( Boolean );

			let current = result;

			for ( let i = 0; i < keys.length; i++ ) {
				if ( i === keys.length - 1 ) {
					current[ keys[ i ] ] = value;
				} else {
					if ( !current[ keys[ i ] ] ) {
						current[ keys[ i ] ] = {};
					}

					current = current[ keys[ i ] ];
				}
			}
		} );

		return result;
	}

	// A joined form's field can share its bare Gravity Forms field ID with a primary-form field,
	// and the server-side order request identifies the sorted column only by that bare ID (e.g.
	// "gv_2"), so it can't tell which form was meant and silently sorts by the primary form's
	// field instead. It does understand a "<form_id>_<field_id>" name (a View's own saved
	// joined-field sort setting is stored that way), so rewrite only the ordered column's name to
	// that form. Every other column's name is left alone, since column search also reads it.
	function disambiguateOrderColumnNames( e, columns, configPrimaryFormId ) {
		if ( ! e.order?.length || ! columns?.length ) {
			return;
		}

		// The config's own primaryFormId is authoritative. The columns[0].form_id fallback
		// (for inline configs cached from renders before the key existed) guesses wrong
		// exactly when a joined form's column is the first visible column.
		const primaryFormId = undefined !== configPrimaryFormId && null !== configPrimaryFormId ?
			configPrimaryFormId :
			columns[ 0 ]?.form_id;

		e.order.forEach( ( orderEntry ) => {
			const columnDef = columns[ orderEntry.column ];
			const requestColumn = e.columns?.[ orderEntry.column ];

			if ( ! columnDef?.form_id || ! requestColumn?.name ) {
				return;
			}

			if ( String( columnDef.form_id ) === String( primaryFormId ) ) {
				return;
			}

			const fieldId = requestColumn.name.replace( /^gv_/, '' );

			// Only a bare numeric Gravity Forms field ID is ambiguous across forms. Entry meta
			// ("id", "date_created", …) sorts the same regardless of form and must stay as-is.
			if ( /^\d+$/.test( fieldId ) ) {
				requestColumn.name = `gv_${ columnDef.form_id }_${ fieldId }`;
			}
		} );
	}

	// Build shadow data object that's used for filtering and searching when client-side processing is enabled.
	// Shadow data is passed by the server and contains values for only certain columns that required processing in the backend.
	// If the value is empty, it's replaced with the original value from the data object with HTML markup stripped.
	// We also decode ROT13-encoded string (e.g. email addresses) to make them searchable.
	function buildShadowDataObject( { data, shadowData, columns } ) {
		return shadowData.map( ( row, rowIndex ) => row.map( ( cellValue, cellIndex ) => {
			if ( cellValue && columns[ cellIndex ].atts?.field_type === 'email' ) {
				return decodeROT13String( cellValue );
			} else if ( cellValue !== '' ) {
				return cellValue;
			}
			if(data[ rowIndex ]){
				return $( '<div>' ).html( data[ rowIndex ][ cellIndex ] ).text();
			}
		} ) );
	}

	// Decode ROT13-encoded string.
	function decodeROT13String( str ) {
		return str.replace( /[a-zA-Z]/g, function ( char ) {
			let charCode = char.charCodeAt( 0 );

			if ( charCode >= 65 && charCode <= 90 ) {  // Uppercase.
				charCode = ( ( charCode - 65 + 13 ) % 26 ) + 65;
			} else if ( charCode >= 97 && charCode <= 122 ) { // Lowercase.
				charCode = ( ( charCode - 97 + 13 ) % 26 ) + 97;
			}

			return String.fromCharCode( charCode );
		} );
	}

	// Exposed so tests/JS/specs/loading-overlay-position.spec.js can drive it directly,
	// mirroring the window.loadRowGroupWithFieldsForTest export in datatables-admin-views.js.
	window.repositionLoaderForTest = gvDataTables.repositionLoader;
}( jQuery ) );
