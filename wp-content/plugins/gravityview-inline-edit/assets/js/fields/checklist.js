/**
 List of checkboxes.
 Internally value stored as javascript array of values.

 @class checklist
 @extends list
 @final
 @example https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address
 **/
(function ( $ ) {
	"use strict";

	var Checklist = function ( options ) {
		this.init( 'checklist', options, Checklist.defaults );
	};

	$.fn.editableutils.inherit( Checklist, $.fn.editabletypes.list );

	$.extend( Checklist.prototype, {
		renderList: function () {

			if ( !$.isArray( this.sourceData ) ) {
				return;
			}

			this.$input = this.$tpl.find( 'input[type="checkbox"]' );

			this.setClass();
		},

		value2str: function ( value ) {
			return $.isArray( value ) ? value.sort().join( $.trim( this.options.separator ) ) : '';
		},

		//parse separated string
		str2value: function ( str ) {
			var reg, value = null;
			if ( typeof str === 'string' && str.length ) {
				reg = new RegExp( '\\s*' + $.trim( this.options.separator ) + '\\s*' );
				value = str.split( reg );
			} else if ( $.isArray( str ) ) {
				value = str;
			} else {
				value = [ str ];
			}
			return value;
		},

		//set checked on required checkboxes
		value2input: function ( value ) {

			var valueArray = $.map( value, function ( val, index ) {
				if ( val && val.length > 0 ) {
					return [ val ];
				}
			} );

			this.$input.prop( 'checked', false );
			if ( $.isArray( valueArray ) && valueArray.length ) {
				this.$input.each( function ( i, el ) {
					var $el = $( el );
					// cannot use $.inArray as it performs strict comparison
					$.each( valueArray, function ( j, val ) {
						/*jslint eqeq: true*/
						if ( $el.val() === val ) {
							/*jslint eqeq: false*/
							$el.prop( 'checked', true );
						}
					} );
				} );
			}
		},

		input2value: function () {
			var checked = [];
			this.$input.filter( ':checked' ).each( function ( i, el ) {
				checked.push( $( el ).val() );
			} );

			return checked;
		},

		//collect text of checked boxes
		value2htmlFinal: function ( value, element ) {
			var html = [];
			var checked = $.fn.editableutils.itemsByValue( value, this.sourceData );
			var escape = this.options.escape, sourceData = $( element ).data( 'source' );
			var inputID = $( element ).data( 'inputid' );
			var choiceDisplay = $( element ).data( 'choice_display' );
			var singleColumnField = false;
			var $el = $( element );
			var isEntryView = $el.parents( 'table.gf_entries' ).length;

			if ( inputID ) {
				//sourceData indexes start at 0 so we need to decrement inputID
				singleColumnField = sourceData[ ( inputID - 1 ) ];

				if ( value.length > 0 && value.indexOf( singleColumnField.value ) >= 0 ) {

					// `tick` is default
					switch ( choiceDisplay ) {
						case 'label':
							$el.text( singleColumnField.text );
							break;
						case 'value':
							$el.text( singleColumnField.value );
							break;
						default:
							$el.html( '<span class="dashicons dashicons-yes"></span>' );
							break;
					}
				} else {
					$el.empty();
				}

			} else if ( checked.length ) {
				$.each( checked, function ( i, v ) {
					var text = escape ? $.fn.editableutils.escape( v.value ) : v.value;
					if ( ! isEntryView ) {
						html.push( '<li>' + text + '</li>' );
					} else {
						html.push( text );
					}
				} );

				if ( isEntryView ) {
					$el.html( html.join( ', ' ) );
					return;
				}

				$el.html( '<ul class="bulleted">' + html.join( '' ) + '</ul>' );
			} else {
				$el.empty();
			}
		},

		activate: function () {
			//this.$input.first().focus();
		},

		autosubmit: function () {
			this.$input.on( 'keydown', function ( e ) {
				if ( e.which === 13 ) {
					$( this ).closest( 'form' ).submit();
				}
			} );
		}
	} );

	Checklist.defaults = $.extend( {}, $.fn.editabletypes.list.defaults, {
		/**
		 Separator of values when reading from `data-value` attribute

		 @todo Test what happens when value has comma in it

		 @property separator
		 @type string
		 @default ','
		 **/
		separator: ','
	} );

	$.fn.editabletypes.checklist = Checklist;

}( window.jQuery ));
