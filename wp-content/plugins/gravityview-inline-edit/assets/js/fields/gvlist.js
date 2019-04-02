/**
 Gvlist editable input. We cannot call this class `List` because X-editable already has an abstract class
 which gvlist,radiolist, etc. extend
 Internally, list values are stored as a serialized array e.g. a:3:{i:0;s:20:"Gvlist item one";i:1;s:16:"Gvlist item two";i:2;s:9:"Gvlist item 3";}
 or as an array of arrays (for the multiple-column lists)

 @class gvlist
 @extends abstractinput
 @final
 @example https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address
 **/
(function ( $ ) {
	"use strict";

	var Gvlist = function ( options ) {
		this.init( 'gvlist', options, Gvlist.defaults );
		this.sourceData = null;
	};

	$.fn.editableutils.inherit( Gvlist, $.fn.editabletypes.list );

	$.extend( Gvlist.prototype, {
		render: function () {
			var $label, $div, $listValue;
			this.sourceData = this.options.source;

			if ( !$.isArray( this.sourceData ) ) {
				return;
			}

			this.$input = this.$tpl.find( "input[name^='input_']" );
		},

		value2str: function ( value ) {
			return value;
		},

		str2value: function ( str ) {
			return str;
		},

		//set the inputs
		value2input: function ( value ) {
			var sourceData = this.sourceData;
			var isMultiColumn = this.options.multicolumn;
			var columnCount = this.options.colcount;
			var counter = 0;
			var inputValue, propertyName, $addButton = null;

			if ( value ) {//After the field has been updated using inline-edit
				sourceData = value;
			}

			if ( isMultiColumn && 'undefined' === typeof columnCount ) {//Multicolumn field that was originally empty
				columnCount = this.$tpl.find( "input[name^='input_']" ).length;
			}

			if ( ( $.isArray( sourceData ) && sourceData.length ) ) {
				//Create empty fields for all the list items.
				$addButton = this.$tpl.find( 'img.add_list_item' );
				for ( var i = 1; i < sourceData.length; i++ ) {
					gformAddListItem( $addButton, 0 );
				}
				//Populate the fields
				this.$tpl.find( "input[name^='input_']" ).each( function ( i, el ) {
					if ( isMultiColumn ) {
						propertyName = $( el ).parents( 'td' ).data( 'label' );
						counter = Math.floor( i / columnCount );
						inputValue = sourceData[ counter ][ propertyName ];
					} else {
						inputValue = sourceData[ i ];
					}
					$( el ).val( inputValue );
				} );
			}
		},

		input2value: function () {
			var listValues = [];
			var propertyName = null;
			var rowNumber = 0;
			var listRow = {};
			var isMultiColumn = this.options.multicolumn;
			var columnCount = this.options.colcount;

			if ( isMultiColumn && 'undefined' === typeof columnCount ) {//Multicolumn field that was originally empty
				columnCount = this.$tpl.find( "input[name^='input_']" ).length;
			}

			//Rather than use this.$input which was set during `render`, we use
			//`this.$tpl.find( "input[name^='input_']" )` to grab all inputs, including
			//those the user might have added after render
			this.$tpl.find( "input[name^='input_']" ).each( function ( i, el ) {
				if ( isMultiColumn ) {
					propertyName = $( el ).parents( 'td' ).data( 'label' );
					rowNumber = Math.floor( i / columnCount );//which row should this value be stored in
					if ( listValues[ rowNumber ] ) {
						listValues[ rowNumber ][ propertyName ] = $( el ).val();
					} else {
						listRow = {};
						listRow[ propertyName ] = $( el ).val();
						listValues.push( listRow );
					}
				} else {
					listValues.push( $( el ).val() );
				}
			} );
			return listValues;
		},

		/**
		 * Default method to show value in element. Can be overwritten by display option.
		 *
		 * @method value2html(value, element)
		 **/
		value2html: function ( value, element ) {

			if ( !value ) {
				$( element ).empty();
				return;
			}

			var valueHtml = null;

			var valueArray = $.map( value, function ( val, index ) {
				if ( 'object' === typeof val ) {
					return [ val ];
				}
				if ( val.length > 0 ) {
					return [ val ];
				}
			} );

			if ( 0 === valueArray.length ) {
				$( element ).empty();
				return;
			}
			var isMultiColumnEmpty = true;
			if ( null !== valueArray[ 0 ] && 'object' === typeof valueArray[ 0 ] ) {
				valueHtml = '<table><thead><tr>';

				$.each( valueArray[ 0 ], function ( colName, colValue ) {
					valueHtml += '<th>' + colName + '</th>';
				} );

				valueHtml += '</tr></thead><tbody>';

				$.each( valueArray, function ( i, val ) {
					valueHtml += '<tr>';
					$.each( val, function ( key, cellData ) {
						if ( cellData ) {
							isMultiColumnEmpty = false;
						}
						valueHtml += '<td>' + cellData + '</td>';
					} );
					valueHtml += '</tr>';
				} );

				valueHtml += '</tbody></table>';

			} else {
				valueHtml = '<ul><li>' + valueArray.join( '</li><li>' ) + '</li></ul>';
			}

			if ( this.options.multicolumn && isMultiColumnEmpty ) {
				$( element ).empty();
				return;
			}

			$( element ).html( valueHtml );
		},

		activate: function () {
			this.$input.first().focus();
		},

		autosubmit: function () {
			this.$input.on( 'keydown', function ( e ) {
				if ( e.which === 13 ) {
					$( this ).closest( 'form' ).submit();
				}
			} );
		}
	} );

	Gvlist.defaults = $.extend( {}, $.fn.editabletypes.list.defaults, {
		/**
		 @property tpl
		 @default <div></div>
		 **/
		//tpl:'<div class="editable-gvlist"></div>',

		/**
		 @property inputclass
		 @type string
		 @default null
		 **/
		inputclass: null,

		/**
		 Separator of values when reading from `data-value` attribute

		 @property separator
		 @type string
		 @default ','
		 **/
		separator: ','
	} );

	$.fn.editabletypes.gvlist = Gvlist;

}( window.jQuery ));
