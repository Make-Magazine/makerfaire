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
			var $el = $( element );
			var entryLink = $el.attr( 'data-entry-link' );

			if ( !value ) {
				$el.empty();
				return;
			}

			if ( value === 'gf_other_choice' ) {
				var html = $el.attr( 'data-other-choice' );

				if ( entryLink ) {
					html = $( '<a />', { href: entryLink, html: html } );
				}

				$el.html( html );

				return;
			}
			var sourceData = $el.data( 'source' ),
				choiceDisplay = $el.data( 'choice_display' ), selected_choice = false;

			$.each( sourceData, function ( index, choice ) {
				if ( choice.hasOwnProperty( 'value' ) && choice.value === value ) {
					selected_choice = choice;
					return false; // break;
				}
			} );

			if ( value.length > 0 && selected_choice ) {
				var html = '';

				// `value` is default
				switch ( choiceDisplay ) {
					case 'label':
						html = selected_choice.text;
						break;
					case 'value':
					default:
						html = selected_choice.value;
						break;
				}

				if ( entryLink ) {
					html = $( '<a />', { href: entryLink, html: html } );
				}

				$el.html( html );

			} else {
				$el.empty();
			}
		}
	} );

	Radiolist.defaults = $.extend( {}, $.fn.editabletypes.list.defaults, {} );

	$.fn.editabletypes.radiolist = Radiolist;
}( window.jQuery ));
