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

( function ( $ ) {

  /**
   * Handle DataTables alert errors (possible values: alert, throw, none)
   * @link https://datatables.net/reference/option/%24.fn.dataTable.ext.errMode
   * @since 2.0
   */
  $.fn.dataTable.ext.errMode = 'throw';

  var gvDataTables = {

    tablesData: {} ,

    init: function () {

      $( '.gv-datatables' ).each( function ( i , e ) {

        var options = window.gvDTglobals[ i ];
        var viewId = $( this ).attr( 'data-viewid' );

        // assign ajax data to the global object
        gvDataTables.tablesData[ viewId ] = options.ajax.data;

        options.buttons = gvDataTables.setButtons( options );

        options.drawCallback = function( data ) {

          if ( window.gvEntryNotes ) {
            window.gvEntryNotes.init();
          }

          if ( data.json.inlineEditTemplatesData ) {
            $( window ).trigger( 'gravityview-inline-edit/extend-template-data' , data.json.inlineEditTemplatesData );
          }
          $( window ).trigger( 'gravityview-inline-edit/init' );
        };

        // convert ajax data object to method that return values from the global object
        options.ajax.data = function ( e ) {
          return $.extend( {} , e , gvDataTables.tablesData[ viewId ] );
        };

        var table = $( this ).DataTable( options );

        table.on( 'draw.dt' , function ( e , settings ) {
          var api = new $.fn.dataTable.Api( settings );
          if ( api.column( 0 ).data().length ) {
            $( e.target )
              .parents( '.gv-container-no-results' )
              .removeClass( 'gv-container-no-results' )
              .siblings( '.gv-widgets-no-results' )
              .removeClass( 'gv-widgets-no-results' );
          }
        } );

        // tweak the Responsive Extension
        if ( i < gvDTResponsive.length && gvDTResponsive.hasOwnProperty( i ) && gvDTResponsive[ i ].responsive.toString() === '1' ) {

          var responsiveConfig = {};

          if ( gvDTResponsive[ i ].hide_empty.toString() === '1' ) {
            // use the modified row renderer to remove empty fields
            responsiveConfig = {
              details: {
                renderer: gvDataTables.customResponsiveRowRenderer
              }
            };
          }

          // init responsive
          new $.fn.dataTable.Responsive( table , responsiveConfig );

        }

        // init FixedHeader
        if ( i < gvDTFixedHeaderColumns.length && gvDTFixedHeaderColumns.hasOwnProperty( i ) ) {

          if ( gvDTFixedHeaderColumns[ i ].fixedheader.toString() === '1' ) {
            new $.fn.dataTable.FixedHeader( table );
          }

          // init FixedColumns
          if ( gvDTFixedHeaderColumns[ i ].fixedcolumns.toString() === '1' ) {
            new $.fn.dataTable.FixedColumns( table );
          }
        }

      } );

    } , // end of init

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
        options.buttons.forEach( function ( button , i ) {
          if ( button.extend === 'print' ) {
            buttons[ i ] = $.extend( true , {} , gvDataTables.buttonCommon , gvDataTables.buttonCustomizePrint , button );
          } else {
            buttons[ i ] = $.extend( true , {} , gvDataTables.buttonCommon , button );
          }
        } );

        $.fn.dataTable.Buttons.swfPath = gvDTButtons.swf || '';
      }

      return buttons;
    } ,

    /**
     * Extend the buttons exportData format
     * @since 2.0
     * @link http://datatables.net/extensions/buttons/examples/html5/outputFormat-function.html
     */
    buttonCommon: {
      exportOptions: {
        format: {
          body: function ( data , column , row ) {

            var newValue = data;

            // Don't process if empty
            if ( newValue.length === 0 ) {
              return newValue;
            }

            newValue = newValue.replace( /\n/g , " " ); // Replace new lines with spaces

            /**
             * Changed to jQuery in 1.2.2 to make it more consistent. Regex not always to be trusted!
             */
            newValue = $( '<span>' + newValue + '</span>' ) // Wrap in span to allow for $() closure
              .find( 'li' ).after( '; ' ).end() // Separate <li></li> with ;
              .find( 'img' ).replaceWith( function () {
                return $( this ).attr( 'alt' ); // Replace <img> tags with the image's alt tag
              } ).end()
              .find( 'br' ).replaceWith( ' ' ).end() // Replace <br> with space
              .find( '.map-it-link' ).remove().end() // Remove "Map It" link
              .text(); // Strip all tags

            return newValue;
          }
        }
      }
    } ,

    buttonCustomizePrint: {
      customize: function ( win ) {
        $( win.document.body ).find( 'table' )
          .addClass( 'compact' )
          .css( 'font-size' , 'inherit' )
          .css( 'table-layout' , 'auto' );
      }
    } ,


    /**
     * Responsive Extension: Function that is called for display of the child row data, when view setting "Hide Empty" is enabled.
     * @see assets/datatables-responsive/js/dataTables.responsive.js Responsive.defaults.details.renderer method
     */
    customResponsiveRowRenderer: function ( api , rowIdx ) {
      var data = api.cells( rowIdx , ':hidden' ).eq( 0 ).map( function ( cell ) {
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
        var cellData = dtPrivate.oApi._fnGetCellData(
          dtPrivate , idx.row , idx.column , 'display'
        );

        return '<li data-dtr-index="' + idx.column + '">' +
          '<span class="dtr-title">' +
          header.text() + ':' +
          '</span> ' +
          '<span class="dtr-data">' +
          cellData +
          '</span>' +
          '</li>';
      } ).toArray().join( '' );

      return data ?
        $( '<ul data-dtr-index="' + rowIdx + '"/>' ).append( data ) :
        false;
    }
  };

  $( document ).ready( function () {

    gvDataTables.init();

    // prevent search submit
    $( '.gv-widget-search' ).on( 'submit' , function ( e ) {
      e.preventDefault();

      var getData    = {} ,
          viewId     = $( this ).attr( 'data-viewid' ) ,
          $container = $( '#gv-datatables-' + viewId ) ,
          $table     = $container.find( '.gv-datatables' ).DataTable() ,
          tableData  = ( gvDataTables.tablesData ) ? gvDataTables.tablesData[ viewId ] : null ,
          inputs     = $( this ).serializeArray().filter( function ( k ) {
            return $.trim( k.value ) !== '';
          } );

      // submit form if table data is not set
      if ( !tableData ) {
        this.submit();
        return;
      }

      if ( tableData.hideUntilSearched * 1 ) {
        $table.on( 'draw.dt' , function () {
          $container.toggleClass( 'hidden' , inputs.length <= 1 );
        } );
      }

      // assemble getData object with filter name/value pairs
      inputs.forEach( function ( input ) {
        getData[ input.name ] = input.value;
      } );

      // reset cached search values
      tableData.search = { 'value': '' };
      tableData.getData = ( Object.keys( getData ).length > 1 ) ? JSON.stringify( getData ) : false;

      // set or clear URL with search query
      if ( tableData.setUrlOnSearch ) {
        window.history.pushState( null , null , ( tableData.getData ) ?
          '?' + $( this ).serialize() :
          window.location.pathname
        );
      }

      // assign new data to the global object
      gvDataTables.tablesData[ viewId ] = tableData;

      // reload table
      $table.ajax.reload();

      return;
    } );
  } );

}( jQuery ) );
