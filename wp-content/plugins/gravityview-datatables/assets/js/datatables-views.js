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

				// convert ajax data object to method that return values from the global object
				options.ajax.data = function ( e ) {
					return $.extend( {}, e, gvDataTables.tablesData[ viewId ] );
				};

				// init FixedHeader and FixedColumns extensions
				if ( i < gvDTFixedHeaderColumns.length && gvDTFixedHeaderColumns.hasOwnProperty( i ) ) {

					if ( gvDTFixedHeaderColumns[ i ].fixedheader.toString() === '1' ) {
						options.fixedHeader = true;
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
					}
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
					$( window ).trigger( 'gravityview-datatables/event/draw', { e: e, settings: settings } );
				} )
				.on( 'preXhr.dt', function ( e, settings, data ) {
					$( window ).trigger( 'gravityview-datatables/event/preXhr', {
						e: e,
						settings: settings,
						data: data
					} );
				} )
				.on( 'xhr.dt', function ( e, settings, json, xhr ) {
					$( window ).trigger( 'gravityview-datatables/event/xhr', {
						e: e,
						settings: settings,
						json: json,
						xhr: xhr
					} );
				} ).on( 'responsive-display', function ( e, datatable, row, showHide, update ) {
					$( window ).trigger( 'gravityview-datatables/event/responsive');
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

				return '<li data-dtr-index="' + idx.column + '">' + '<span class="dtr-title">' + header.text() + ':' + '</span> ' + '<span class="dtr-data">' + cellData + '</span>' + '</li>';
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
				$table.on( 'draw.dt', function () {
					$container.toggleClass( 'hidden', inputs.length <= 1 );
				} );
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

