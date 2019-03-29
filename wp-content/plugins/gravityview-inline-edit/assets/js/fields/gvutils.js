/**
 * GV inline edit utility logic used across different fields
 **/
(function ( $ ) {
	"use strict";

	$.fn.editableutils.gvutils = {

		/**
		 Sets value of input.

		 @method value2input(value)
		 @param {mixed} value
		 @return boolean Based on whether the value was updated or not
		 **/
		value2input: function ( inputField, value ) {

			if ( inputField.length ) {
				this.gvutilsInputNumber = inputField.data( 'inputnumber' );
				inputField.val( value[ this.gvutilsInputNumber ] );
				return true;
			}
			return false;


		},

		/**
		 Returns value of input.

		 @method input2value()
		 @return Object|boolean Returns object of the new value if in single user mode; otherwise, returns false
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
