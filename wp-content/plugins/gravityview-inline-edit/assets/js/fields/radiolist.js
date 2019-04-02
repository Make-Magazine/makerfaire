/**
 List of radio buttons. Unlike checklist, value is stored internally as
 scalar variable instead of array. Extends Checklist to reuse some code.

 @class radiolist
 @extends checklist
 @final
 @example https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address
 **/
( function ( $ ) {
	"use strict";

	var Radiolist = function ( options ) {
		this.init( 'radiolist', options, Radiolist.defaults );
	};
	$.fn.editableutils.inherit( Radiolist, $.fn.editabletypes.checklist );

	$.extend( Radiolist.prototype, {
		renderList: function () {
			this.$input = this.$tpl.find( 'input[type="radio"]' );
		},
		input2value: function () {
			return this.$input.filter( ':checked' ).val();
		},
		str2value: function ( str ) {
			return str || null;
		},
		value2input: function ( value ) {
			this.$input.val( [ value ] );
		},
		value2str: function ( value ) {
			return value || '';
		},
		value2html: function ( value, element ) {

			if ( !value ) {
				$( element ).empty();
				return;
			}

			var sourceData = $( element ).data( 'source' ),
				choiceDisplay = $( element ).data( 'choice_display' ), selected_choice = false;

			$.each( sourceData, function ( index, choice ) {
				if ( choice.hasOwnProperty( 'value' ) && choice.value === value ) {
					selected_choice = choice;
					return false; // break;
				}
			} );

			if ( value.length > 0 && selected_choice ) {
				// `value` is default
				switch ( choiceDisplay ) {
					case 'label':
						$( element ).text( selected_choice.text );
						break;
					case 'value':
					default:
						$( element ).text( selected_choice.value );
						break;
				}
			} else {
				$( element ).empty();
			}
		}
	} );

	Radiolist.defaults = $.extend( {}, $.fn.editabletypes.list.defaults, {} );

	$.fn.editabletypes.radiolist = Radiolist;
}( window.jQuery ));
