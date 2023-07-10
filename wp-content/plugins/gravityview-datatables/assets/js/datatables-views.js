/**
 * Custom js script loaded on Views frontend to set DataTables
 *
 * @package   GravityView
 * @license   GPL2+
 * @author    Katz Web Services, Inc.
 * @link      http://gravityview.co
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

		tablesData: {},

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

			var delayed_search = $.fn.dataTable.util.throttle( function ( table, val ) {
				table
					.search( val, false, false )
					.draw();
			}, 200 );

			datatable.columns().every( function ( index ) {

				var column = settings.aoColumns[ index ];
				var that = this;
				var input;

				if ( !column.searchable ) {
					return;
				}

				if ( 'select' === column.atts.type && column.atts.options ) {

					input = $( '<select></select>' )
						.append( $( '<option>' )
							.val( '' )
							.text( column.atts.placeholder || '' ) );

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
							.text( (j.text || j.label) ) );
					} );

				} else {
					input = $( '<input/>' )
						.attr( 'type', column.atts.type )
						.attr( 'placeholder', column.atts.placeholder )
						.attr( 'min', (column.atts.min || null) )
						.attr( 'max', (column.atts.max || null) )
						.attr( 'step', (column.atts.step || null) );
				}

				var input_search_value = settings.oSavedState ? settings.oSavedState.columns[ index ].search.search : '';

				input.val( input_search_value );

				$( input )
					.attr( 'class', column.atts.class )
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
							$(this)[0].select();
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
						var is_function_keys = (keyCode < 130 && keyCode > 112);

						if ( -1 !== ignore_keys.indexOf( keyCode ) || is_function_keys ) {
							return true;
						}

						$( this )
							.parents( 'table.gv-datatables' )
							.find( '.gv-dt-field-filter[data-uid=' + $( this ).data( 'uid' ) + ']' )
							.val( this.value );

						if ( that.search() !== this.value ) {
							delayed_search( that, this.value );
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

				// assign ajax data to the global object
				gvDataTables.tablesData[ viewId ] = options.ajax.data;

				options.buttons = gvDataTables.setButtons( options );

				options.drawCallback = function ( data ) {

					if ( window.gvEntryNotes ) {
						window.gvEntryNotes.init();
					}

					if ( data.json.inlineEditTemplatesData ) {
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
				options.initComplete = function( settings ) {
					gvDataTables.setUpFieldFilters( this.api(), settings );
				};

				// convert ajax data object to method that return values from the global object
				options.ajax.data = function ( e ) {
					return $.extend( {}, e, gvDataTables.tablesData[ viewId ] );
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
				if( options.rowGroupSettings && options.rowGroupSettings.status && options.rowGroupSettings.status * 1 === 1 ) {

					// Disable incompatible extensions.
					options.fixedColumns = false;

					var rowGroup = {
						dataSrc: function ( row ) {
							var row_field = row[ options.rowGroupSettings.index * 1 ];
							if ( $( row_field ).attr( 'href' ) !== undefined ) {
								return $( row_field ).text();
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


				var table = $( this ).DataTable( options );

				// Init Auto Update
				if( options.updateInterval && options.updateInterval > 0 ){
					setInterval(function() {
						table.ajax.reload();
					}, ( options.updateInterval * 1 ) );
				}

				table
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
					var tableData = ( gvDataTables.tablesData && viewId ) ? gvDataTables.tablesData[ viewId ] : null;
					var getData = ( tableData && tableData.hasOwnProperty('getData') ) ? tableData.getData : null;

					if (
						api.data().length === 0 && // No entries.
						0 === api.search().length && // No global search.
						0 === api.columns().search().filter( function( string ) {
							  return string !== "";
						  } ).length && // No field filters per-column search.
						! getData // Search Bar is not being used to search.
					) {
						// No entries.
						$( e.target ).find( '.dataTables_empty' ).text( options.language.zeroRecords );

						var noEntriesOption = tableData && tableData.hasOwnProperty('noEntriesOption') ? tableData.noEntriesOption * 1 : null;

						switch ( noEntriesOption ) {
							case 1: // Show a form.
								$container = $( e.target ).parents( 'div[id^=gv-view-]' );
								$container
									.find('[id^=gv-datatables-],.gv-widgets-header,.gv-powered-by').hide().end()
									.find( '.gv-datatables-form-container' ).removeClass( 'gv-hidden' );
								break;
							case 2: // Redirect to the URL.
								var redirectURL = tableData && tableData.hasOwnProperty('redirectURL') ? tableData.redirectURL : null;
								if ( redirectURL.length ) {
									window.location = redirectURL;
								}
								break;
							case 3: // Hide the View (should already be hidden, but just in case).
								$( e.target ).parents( '.gv-datatables-container' ).hide();
								break;
						}

					} else {
						// No search results.
						$( e.target ).find( '.dataTables_empty' ).text( options.language.emptyTable );
					}

					$( window ).trigger( 'gravityview-datatables/event/draw', { e: e, settings: settings } );
				} )
				.on( 'preXhr.dt', function ( e, settings, data ) {
					$( window ).trigger( 'gravityview-datatables/event/preXhr', {
						e: e,
						settings: settings,
						data: data
					} );
				} )
				.on( 'processing.dt', function ( e, settings, processing ) {
					if ( ! processing ) {
						return;
					}

					gvDataTables.repositionLoader( $( e.target ) );
				} )
				.on( 'xhr.dt', function ( e, settings, json, xhr ) {
					$( window ).trigger( 'gravityview-datatables/event/xhr', {
						e: e,
						settings: settings,
						json: json,
						xhr: xhr
					} );
				} )
				.on( 'responsive-resize', function ( e, datatable, columns ) {

					// Re-initialize field filters, if enabled.
					gvDataTables.setUpFieldFilters( datatable, datatable.settings()[0] );
				} )
				.on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
					$( window ).trigger( 'gravityview-datatables/event/responsive' );
					var visible_divs, div_attr;

					// Fix duplicate images in Fancybox in datatables on mobile.
					visible_divs = $( this ).find( 'td:visible .gravityview-fancybox' );

					if( visible_divs.length > 0 ){
						visible_divs.each( function( i, e ) {
							div_attr = $( this ).attr( 'data-fancybox' );
							if ( div_attr && div_attr.indexOf( 'mobile' ) === -1 ) {
								div_attr += '-mobile';
								$( this ).attr( 'data-fancybox', div_attr );
							}
						} );
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
				format: {
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

				return '<li data-dtr-index="' + idx.column + '">' + '<span class="dtr-title">' + header.find('.gv-dt-field-filter').remove().end().text() + ':' + '</span> ' + '<span class="dtr-data">' + cellData + '</span>' + '</li>';
			} ).toArray().join( '' );

			return data ? $( '<ul data-dtr-index="' + rowIdx + '"/>' ).append( data ) : false;
		},
	};

	$( document ).ready( function () {
		gvDataTables.init();

		// reset search results
		$( '.gv-search-clear' ).off().on( 'click', function ( e ) {
			var $form = $( this ).parents( 'form' );
			var viewId = $form.attr( 'data-viewid' );
			var tableId = $( '#gv-datatables-' + viewId ).find( '.dataTable' ).attr( 'id' );
			var tableData = ( gvDataTables.tablesData ) ? gvDataTables.tablesData[ viewId ] : null;
			var isSearch = $form.hasClass( 'gv-is-search' );

			if ( !tableId || !$.fn.DataTable.isDataTable( '#' + tableId ) ) {
				return;
			}

			// prevent event from bubbling and firing
			e.preventDefault();
			e.stopImmediatePropagation();

			var $table = $( '#' + tableId ).DataTable();

			if ( isSearch && $form.serialize() !== $form.attr( 'data-state' ) ) {
				var formData = {};
				var serializedData = $form.attr( 'data-state' ).split( '&' );
				for ( var i = 0; i < serializedData.length; i++ ) {
					var item = serializedData[ i ].split( '=' );
					formData[ decodeURIComponent( item[ 0 ] ) ] = decodeURIComponent( item[ 1 ] );
				}

				$.each( formData, function ( name, value ) {
					var $el = $form.find( '[name="' + name + '"]' );
					$el.val( value );
				} );

				$( '.gv-search-clear', $form ).text( gvGlobals.clear );

				return;
			}

			// clear form fields. because default input values are set, form.reset() does not work.
			// instead, a more comprehensive solution is required: https://stackoverflow.com/questions/680241/resetting-a-multi-stage-form-with-jquery/24496012#24496012

			$( 'input[type="search"], input:text, input:password, input:file, select, textarea', $form ).val( '' );
			$( 'input:checkbox, input:radio', $form ).removeAttr( 'checked' ).removeAttr( 'selected' );

			if ( $form.serialize() !== $form.attr( 'data-state' ) ) {
				// assign new data to the global object
				tableData.getData = false;
				gvDataTables.tablesData[ viewId ] = tableData;
				window.history.pushState( null, null, window.location.pathname );

				// update form state
				$form.removeClass( 'gv-is-search' );
				$form.attr( 'data-state', $form.serialize() );

				// reload table
				$table.ajax.reload();
			}

			$( this ).hide( 100 );
		} );

		// prevent search submit
		$( '.gv-widget-search' ).on( 'submit', function ( e ) {
			e.preventDefault();

			var getData = {};
			var viewId = $( this ).attr( 'data-viewid' );
			var $container = $( '#gv-datatables-' + viewId );
			var $table;

			// Check if fixed columns is activated.
			if ( $container.find( '.DTFC_ScrollWrapper' ).length > 0 ) {
				$table = $container.find( '.dataTables_scrollBody .gv-datatables' ).DataTable();
			} else {
				$table = $container.find( '.gv-datatables' ).DataTable();
			}
			var tableData = ( gvDataTables.tablesData ) ? gvDataTables.tablesData[ viewId ] : null;
			var inputs = $( this ).serializeArray().filter( function ( k ) {
				return $.trim( k.value ) !== '';
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
				$container.toggleClass( 'hidden gv-hidden', inputs.length <= 1 );
			}

			// assemble getData object with filter name/value pairs
			for ( var i = 0; i < inputs.length; i++ ) {
				var name = inputs[ i ].name;
				var value = inputs[ i ].value;

				// convert multidimensional form values (e.g., {"foo[bar]": "xyz"}) to JSON object (e.g., {"foo":{"bar": "xyz"}})
				var matches = name.match( /(.*?)\[(.*)\]/ );
				if ( matches ) {
					if ( !getData[ matches[ 1 ] ] ) {
						if ( matches[ 2 ] ) {
							getData[ matches[ 1 ] ] = {};
						} else {
							getData[ matches[ 1 ] ] = [];
						}
					}

					if ( matches[ 2 ] ) {
						getData[ matches[ 1 ] ][ matches[ 2 ] ] = value;
					} else {
						getData[ matches[ 1 ] ].push( value );
					}
				} else {
					getData[ name ] = value;
				}
			}

			// reset cached search values
			tableData.search = { 'value': '' };
			tableData.getData = ( Object.keys( getData ).length > 1 ) ? JSON.stringify( getData ) : false;

			// set or clear URL with search query
			if ( tableData.setUrlOnSearch ) {
				window.history.pushState( null, null, ( tableData.getData ) ? '?' + $( this ).serialize() : window.location.pathname );
			}

			// assign new data to the global object
			gvDataTables.tablesData[ viewId ] = tableData;

			// reload table
			$table.ajax.reload();

			// update form state
			$( this ).addClass( 'gv-is-search' ).attr( 'data-state', $( this ).serialize() ).trigger( 'keyup' );
			$( '.gv-search-clear', $( this ) ).text( gvGlobals.clear );
		} );
	} );
}( jQuery ) );

