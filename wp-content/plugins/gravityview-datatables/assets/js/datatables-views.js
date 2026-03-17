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
	$.fn.dataTable.ext.errMode = 'throw';

	var gvDataTables = {
		tables: {},

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

			// Filters are already initialized.
			if ( $( datatable.columns().header() ).find( '.gv-dt-field-filter' ).length ) {
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
					.attr( 'type', column.atts.field_type )
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

				var input_search_value = settings.oSavedState ? settings.oSavedState.columns[ index ].search.search : '';

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
		},

		init: function () {

			$( '.gv-datatables' ).each( function ( i, e ) {
				var options = window.gvDTglobals[ i ];
				var viewId = $( this ).attr( 'data-viewid' );

				gvDataTables.tables[ viewId ] = {
					emptyServerResponse: false,
					data: options.ajax.data,
				};

				// Wrap the processing text in HTML if it exists
				if ( options.language && options.language.processing ) {
					options.language.processing = "<div class='dataTables_processing_text'>" + options.language.processing + "</div>";
				}

				options.buttons = gvDataTables.setButtons( options );

				options.drawCallback = function ( data ) {
					if ( window.gvEntryNotes ) {
						window.gvEntryNotes.init();
					}

					if ( data.json && data.json.inlineEditTemplatesData ) {
						$( window ).trigger( 'gravityview-inline-edit/extend-template-data', data.json.inlineEditTemplatesData );
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

				// convert ajax data object to method that return values from the global object
				options.ajax.data = function ( e ) {
					return $.extend( {}, e, gvDataTables.tables[ viewId ].data );
				};

				// init FixedHeader and FixedColumns extensions
				if ( i < gvDTFixedHeaderColumns.length && gvDTFixedHeaderColumns.hasOwnProperty( i ) ) {

					if ( gvDTFixedHeaderColumns[ i ].fixedheader.toString() === '1' ) {
						options.fixedHeader = {
							headerOffset: $( '#wpadminbar' ).outerHeight()
						};
					}

					if ( gvDTFixedHeaderColumns[ i ].fixedcolumns.toString() === '1' ) {
						options.fixedColumns = true;
					}
				}

				// init Responsive extension
				if ( i < gvDTResponsive.length && gvDTResponsive.hasOwnProperty( i ) && gvDTResponsive[ i ].responsive.toString() === '1' ) {
					if ( '1' === gvDTResponsive[ i ].hide_empty.toString() ) {
						// use the modified row renderer to remove empty fields
						options.responsive = { details: { renderer: gvDataTables.customResponsiveRowRenderer } };
					} else {
						options.responsive = true;
						options.fixedColumns = false;
					}
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

						return gvDataTables.tables?.[viewId]?.shadowData?.[ settings.row ]?.[ settings.col ] ?? data;
					};
				} );

				// Implement state key management to invalidate cache when settings change.
				if ( options.stateSave && options.stateKey ) {
					const stateKey = options.stateKey;
					const baseStateKey = 'DataTables_' + $( this ).attr( 'data-viewid' );
					
					options.stateSaveCallback = function ( settings, data ) {
						try {
							localStorage.setItem( baseStateKey + '_' + stateKey, JSON.stringify( data ) );
						} catch ( ex ) {
							// Fail silently if localStorage is not available.
						}
					};

					options.stateLoadCallback = function ( settings ) {
						try {
							return JSON.parse( localStorage.getItem( baseStateKey + '_' + stateKey ) );
						} catch ( ex ) {
							return null;
						}
					};

					// Clean up old state keys to prevent localStorage bloat.
					try {
						const keysToRemove = [];

						for ( let i = 0; i < localStorage.length; i++ ) {
							const key = localStorage.key( i );
							if ( key && key.startsWith( baseStateKey + '_' ) && key !== baseStateKey + '_' + stateKey ) {
								keysToRemove.push( key );
							}
						}

						keysToRemove.forEach( key => localStorage.removeItem( key ) );
					} catch ( ex ) {
						// Fail silently.
					}
				}

				if ( options.ajax ) {
					// Handle empty server response.
					options.ajax = $.extend( {}, options.ajax, {
						dataFilter: function ( data, type ) {
							if ( data !== '' ) {
								return data;
							}

							gvDataTables.tables[ viewId ].emptyServerResponse = true;

							return JSON.stringify( {
								draw: options.ajax?.draw || 0,
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
							$( this ).attr( 'data-numeric-value', options.footerCalculation?.data?.[ columnIndex ]?.values[ rowIndex ] );
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
					gvDataTables.tables[ viewId ] = {
						...gvDataTables.tables[ viewId ],
						// Track whether the table was loaded with search in progress. This will be used to determine whether Ajax should be used when search criteria is modified.
						allRecordsLoaded: !$( `form.gv-widget-search[data-viewid="${ viewId }"]` ).hasClass( 'gv-is-search' ),
						shadowData: buildShadowDataObject( { data: options.data, shadowData: options.shadowData, columns: options.columns, } )
					};

					configureClientSideFilterAndSearch();
				}

				// Init Auto Update
				if ( options.updateInterval && options.updateInterval > 0 ) {
					setInterval( function () {
						const table = gvDataTables.tables[ viewId ].table;

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

				gvDataTables.tables[ viewId ].table = $( this ).DataTable( options );

				if ( options.hideSearchBar ) {
					$( gvDataTables.tables[ viewId ].table.settings()[ 0 ].nTableWrapper ).find( '.dataTables_filter' ).remove();
				}

				gvDataTables.tables[ viewId ].table
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
						var tableData = gvDataTables.tables[ viewId ].data ?? null;
						var getData = ( tableData && tableData.hasOwnProperty( 'getData' ) ) ? tableData.getData : null;
						var $viewContainer = $( e.target ).parents( 'div[id^=gv-view-]' );
						var noEntriesOption = tableData?.noEntriesOption * 1;
						var hideUntilSearched = tableData?.hideUntilSearched * 1;

						if (
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
									if ( redirectURL.length ) {
										window.location = redirectURL;
									}
									break;
								case 3: // Hide the View (should already be hidden, but just in case).
									$( e.target ).parents( '.gv-datatables-container' ).hide();
									break;
							}

						} else {
							// Entries found.
							if ( !hideUntilSearched && $( gvDataTables.tables[ viewId ].table.table().container() ).is( ':hidden' ) ) {
								$viewContainer
									.find( '.gv-widgets-header, .gv-widgets-footer, .gv-datatables-container' )
									.removeClass( 'gv-hidden' );

								// Unsetting width fixes the issue with the table not being displayed properly after being unhidden.
								// api.columns().adjust() doesn't work in this case.
								$viewContainer.find( 'table.dataTable' ).css( 'width', '' );
							}

							// No search results.
							const emptyTable = $('<div/>').html( gvDataTables.tables[ viewId ].emptyServerResponse ? options.language.emptyServerResponse : options.language.emptyTable ).text();

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
							gvDataTables.tables[ viewId ].shadowData = shadowData;
						}

						$( window ).trigger( 'gravityview-datatables/event/xhr', {
							e,
							settings,
							json,
							xhr,
						} );
					} )
					.on( 'responsive-resize', function ( e, datatable ) {
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
						if ( state ) {
							$( '.footer-calculation' ).find( 'td' ).eq( columnIdx ).show();
						} else {
							$( '.footer-calculation' ).find( 'td' ).eq( columnIdx ).hide();
						}
					} );
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

			$.fn.isInViewport = function () {
				var elementTop = $( this ).offset().top;
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

			$loader.css( {
				position: 'absolute',
				top: topPosition,
			} );
		},

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
					} else {
						buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, button );
					}
				} );
			}

			return buttons;
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

		Object.keys( gvDataTables.tables).forEach( function ( viewId ) {
			const $searchWidgetForm = $(`form.gv-widget-search[data-viewid="${viewId}"]`);

			// Reset search results.
			$( '.gv-search-clear', $searchWidgetForm ).off().on( 'click', function ( e ) {
				// prevent event from bubbling and firing
				e.preventDefault();
				e.stopImmediatePropagation();

				if ( !gvDataTables.tables[ viewId ] ) {
					return;
				}

				var tableData = gvDataTables.tables[ viewId ].data ?? null;
				var $container = $( '#gv-datatables-' + viewId );
				var $table;

				// Check if fixed columns is activated.
				if ( $container.find( '.DTFC_ScrollWrapper' ).length > 0 ) {
					$table = $container.find( '.dataTables_scrollBody .gv-datatables' );
				} else {
					$table = $container.find( '.gv-datatables' );
				}

				// clear form fields. because default input values are set, form.reset() does not work.
				// instead, a more comprehensive solution is required: https://stackoverflow.com/questions/680241/resetting-a-multi-stage-form-with-jquery/24496012#24496012

				$( 'input[type="search"], input:text, input:password, input:file, input[type="number"], select, textarea', $searchWidgetForm ).val( '' );
				$( 'input:checkbox, input:radio', $searchWidgetForm ).removeAttr( 'checked' ).removeAttr( 'selected' );

				// assign new data to the global object
				if ( tableData ) {
					tableData.getData = false;
					gvDataTables.tables[ viewId ].data = tableData;
				}

				// remove search query from URL
				const url = new URL( window.location.href );

				[ 'gv_search', 'mode' ].forEach( param => url.searchParams.delete( param ) );

				[ ...url.searchParams.keys() ].forEach( key => key.startsWith( 'filter_' ) && url.searchParams.delete( key ) );

				[ ...url.searchParams.keys() ].forEach( key => key.startsWith( 'gv_' ) && url.searchParams.delete( key ) );

				window.history.pushState( null, null, url.toString() );

				// update form state
				$searchWidgetForm.removeClass( 'gv-is-search' );
				$searchWidgetForm.attr( 'data-state', $searchWidgetForm.serialize() );

				const currentTableOptions = $table.DataTable().init();

				if ( currentTableOptions._ajax ) {
					// If Ajax was disabled before, re-enable it.
					$table.DataTable().settings()[ 0 ].ajax = currentTableOptions._ajax;
				}

				const shouldUseAjax = currentTableOptions.serverSide ||
					( !currentTableOptions.serverSide && !gvDataTables.tables[ viewId ].allRecordsLoaded );

				// Reload table.
				if ( shouldUseAjax ) {
					gvDataTables.tables[ viewId ].allRecordsLoaded = true;

					$table.DataTable().ajax.reload();
				} else {
					$table.DataTable().draw();
				}

				$( this ).hide( 100 );
			} );

			// Handle search.
			$searchWidgetForm.on( 'submit', function ( e ) {
				e.preventDefault();

				if ( !gvDataTables.tables[ viewId ] ) {
					return;
				}

				var getData = {};
				var $container = $( '#gv-datatables-' + viewId );
				var $table;

				// Check if fixed columns is activated.
				if ( $container.find( '.DTFC_ScrollWrapper' ).length > 0 ) {
					$table = $container.find( '.dataTables_scrollBody .gv-datatables' );
				} else {
					$table = $container.find( '.gv-datatables' );
				}
				var tableData = gvDataTables.tables[ viewId ].data ?? null;
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
					$container.parent().find( '.gv-widget-export-link a[data-nonce-url]' ).each( function() {

						let anchorUrl = new URL( $( this ).data( 'nonce-url' ) );

						// Merge existing query parameters from the anchor with the new ones
						url.searchParams.forEach( ( value, key ) => anchorUrl.searchParams.set( key, value ) );

						$( this ).attr( 'href', anchorUrl.toString() );
					} );
				}

				// assign new data to the global object
				gvDataTables.tables[ viewId ].data = tableData;

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
					( !currentTableOptions.serverSide && ( nonColumnSearch || !gvDataTables.tables[ viewId ].allRecordsLoaded ) );

				if ( shouldUseAjax ) {
					gvDataTables.tables[ viewId ].allRecordsLoaded = !tableData.getData;

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
			const shadowData = gvDataTables.tables[ viewId ].shadowData;
			const form = $( `form[data-viewid="${ viewId }"]` );
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
					const inputName = $( this ).attr( 'name' );

					// Skip date range inputs and date inputs - they'll be processed separately.
					if ( inputName === 'gv_start' || inputName === 'gv_end' || inputName.match( /\[start\]$|\[end\]$/ ) ) {
						return null;
					}

					// Skip date inputs that are in date search elements.
					const isInDateElement = $( this ).closest( '.gv-search-date' ).length > 0;

					if ( isInDateElement && inputName.match( /^filter_\d+$/ ) ) {
						return null;
					}

					return {
						value: $( this ).val().toLocaleLowerCase().trim(),
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
			if ( searchWidgetValues.length && gvDataTables.tables[ viewId ].allRecordsLoaded ) {
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
}( jQuery ) );
