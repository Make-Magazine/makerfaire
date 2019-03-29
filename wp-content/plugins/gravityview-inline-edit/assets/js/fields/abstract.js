/**
 GvAbstract editable input. Extends Inline edit abstract type and implements
 custom GV functionality so that all other fields extend this rather than $.fn.editabletypes.abstractinput

 @class GvAbstract
 @extends abstractinput
 @final

 </script>
 **/
(function ( $ ) {
	"use strict";

	$.fn.editableutils.gvabstract = {

		/**
		 Sets value of input.

		 @method value2input(value)
		 @param {mixed} value
		 **/
		value2input: function ( inputField, value ) {

			if ( inputField.length ) {
				this.gvabstractInputNumber = inputField.data( 'inputnumber' );
				inputField.val( value[ this.gvabstractInputNumber ] );
				return;
			}


		},

		/**
		 Returns value of input.

		 @method input2value()
		 **/
		input2value: function ( inputField ) {

			if ( inputField.length ) {
				var value = {};
				value[ inputField.data( 'inputnumber' ) ] = inputField.val();
				return value;
			}
			return false;

		}

	};


}( window.jQuery ));