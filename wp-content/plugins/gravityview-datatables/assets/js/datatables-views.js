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
 */

window.gvDTResponsive = window.gvDTResponsive || {};
window.gvDTFixedHeaderColumns = window.gvDTFixedHeaderColumns || {};
window.gvDTButtons = window.gvDTButtons || {};

(function( $ ) {

/**
 * Handle DataTables alert errors (possible values: alert, throw, none)
 * @link https://datatables.net/reference/option/%24.fn.dataTable.ext.errMode
 * @since 2.0
 */
$.fn.dataTable.ext.errMode = 'throw';

var gvDataTables = {

	init: function() {


		$('.gv-datatables').each( function( i, e ) {

			var options = gvDTglobals[ i ];

			options.buttons = gvDataTables.setButtons( options );

			var table = $(this).DataTable( options );

			// tweak the Responsive Extension
			if( i < gvDTResponsive.length && gvDTResponsive[ i ].responsive.toString() === '1' ) {

				var responsiveConfig = {};

				if( gvDTResponsive[ i ].hide_empty.toString() === '1' ) {
					// use the modified row renderer to remove empty fields
					responsiveConfig =  {
						details: {
							renderer: gvDataTables.customResponsiveRowRenderer
						}
					};
				}

				// init responsive
				new $.fn.dataTable.Responsive( table, responsiveConfig );

			}



			// init FixedHeader
			if( i < gvDTFixedHeaderColumns.length && gvDTFixedHeaderColumns[ i ].fixedheader.toString() === '1' ) {
				new $.fn.dataTable.FixedHeader( table );
			}
			// init FixedColumns
			if(  i < gvDTFixedHeaderColumns.length && gvDTFixedHeaderColumns[ i ].fixedcolumns.toString() === '1' ) {
				new $.fn.dataTable.FixedColumns( table );
			}


		});

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
		if( options && options.buttons && options.buttons.length > 0 ) {
			options.buttons.forEach( function( button, i ) {
				if( button.extend === 'print' ) {
					buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, gvDataTables.buttonCustomizePrint, button );
				} else {
					buttons[ i ] = $.extend( true, {}, gvDataTables.buttonCommon, button );
				}
			});

			$.fn.dataTable.Buttons.swfPath = gvDTButtons.swf || '';
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
					if( newValue.length === 0 ) {
						return newValue;
					}

					newValue = newValue.replace(/\n/g, " "); // Replace new lines with spaces

					/**
					 * Changed to jQuery in 1.2.2 to make it more consistent. Regex not always to be trusted!
					 */
					newValue = $('<span>'+newValue+'</span>') // Wrap in span to allow for $() closure
						.find('li').after('; ').end() // Separate <li></li> with ;
						.find('img').replaceWith(function() {
							return $(this).attr('alt'); // Replace <img> tags with the image's alt tag
						}).end()
						.find('br').replaceWith(' ').end() // Replace <br> with space
						.find('.map-it-link').remove().end() // Remove "Map It" link
						.text(); // Strip all tags

					return newValue;
				}
			}
		}
	},

	buttonCustomizePrint: {
		customize: function ( win ) {
			$(win.document.body).find( 'table' )
				.addClass( 'compact' )
				.css( 'font-size', 'inherit')
				.css( 'table-layout', 'auto' );
		}
	},


	/**
	 * Responsive Extension: Function that is called for display of the child row data, when view setting "Hide Empty" is enabled.
	 * @see assets/datatables-responsive/js/dataTables.responsive.js Responsive.defaults.details.renderer method
	 */
	customResponsiveRowRenderer: function ( api, rowIdx ) {
		var data = api.cells( rowIdx, ':hidden' ).eq(0).map( function ( cell ) {
			var header = $( api.column( cell.column ).header() );

			if ( header.hasClass( 'control' ) || header.hasClass( 'never' ) ) {
				return '';
			}

			var idx = api.cell( cell ).index();

			// GV custom part: if field value is empty
			if (  api.cell( cell ).data().length === 0 ) {
				return '';
			}

			// Use a non-public DT API method to render the data for display
			// This needs to be updated when DT adds a suitable method for
			// this type of data retrieval
			var dtPrivate = api.settings()[0];
			var cellData = dtPrivate.oApi._fnGetCellData(
				dtPrivate, idx.row, idx.column, 'display'
			);

			return '<li data-dtr-index="'+idx.column+'">'+
				'<span class="dtr-title">'+
				header.text()+':'+
				'</span> '+
				'<span class="dtr-data">'+
				cellData+
				'</span>'+
				'</li>';
		} ).toArray().join('');

		return data ?
			$('<ul data-dtr-index="'+rowIdx+'"/>').append( data ) :
			false;
	}

};



$(document).ready( function() {
	gvDataTables.init();
});


}(jQuery));
