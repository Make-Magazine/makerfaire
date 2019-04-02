/**
 MultiSelect editable input.
 Internally value stored as `select one,select two`

 @class multiselect
 @extends abstractinput
 @final
 **/
(function ( $ ) {
	"use strict";

	var MultiSelect = function ( options ) {
		this.init( 'multiselect', options, MultiSelect.defaults );
		this.sourceData = null;
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit( MultiSelect, $.fn.editabletypes.abstractinput );

	$.extend( MultiSelect.prototype, {

		/**
		 * Renders input from tpl
		 */
		render: function () {
			this.$input = this.$tpl.find( 'select' );
			this.sourceData = this.options.source;
		},

		/**
		 * Default method to show value in element. Can be overwritten by display option.
		 *
		 **/
		value2html: function ( value, element ) {

			if ( ! value ) {
				$( element ).empty();
				return;
			}

			// Turn CSV string into array
			if( 'string' === typeof value ) {
				value = value.split(',');
			}

			var value_array = $.map( value, function ( val, index ) {
				return [ val ];
			} );

			var value_html = value_array.join( '</li><li>' );

			$( element ).html( '<ul><li>' + value_html + '</li></ul>' );
		},

		/**
		 * set the inputs
		 */
		value2input: function ( value ) {
			var valueArray = [];
			var $multiselectField = this.$input;

			if ( value && value.indexOf( ',' ) > -1 ) {
				valueArray = value.split( ',' );
			}

			if ( $.isArray( valueArray ) && valueArray.length ) {
				$multiselectField.val( valueArray );
			} else {
				$multiselectField.val( value );
			}
		}
	} );

	MultiSelect.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults, {
		inputclass: ''
	} );

	$.fn.editabletypes.multiselect = MultiSelect;

}( window.jQuery ));
