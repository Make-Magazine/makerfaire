/*
 * Extend the default Inline Edit number type
 *
 * @class Number
 * @extends number
 */
( function ( $ ) {
	"use strict";

	var Number = function ( options ) {
		this.init( 'number', options, Number.defaults );
		this.selectField = null;
	};

	$.fn.editableutils.inherit( Number, $.fn.editabletypes.abstractinput );

	$.extend( Number.prototype, {
		value2html: function ( value, element ) {
			var $el = $( element );
			var html = $el.attr('data-display');

			$el.html( html );
		}
	} );

	Number.defaults = $.extend( {}, $.fn.editabletypes.number.defaults );

	$.fn.editabletypes.number = Number;
}( window.jQuery ) );
