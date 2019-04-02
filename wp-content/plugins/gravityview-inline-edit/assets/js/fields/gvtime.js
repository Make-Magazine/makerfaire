/*
 * Gvtime. Extend the default inline edit time format to support
 * GF's custom time template better
 *
 * @class gvtime
 * @extends time
 * @xample https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address
 *
 */
(function ( $ ) {
	"use strict";

	var Gvtime = function ( options ) {
		this.init( 'gvtime', options, Gvtime.defaults );
		this.selectField = null;
	};
	//inherit from time
	$.fn.editableutils.inherit( Gvtime, $.fn.editabletypes.time );

	$.extend( Gvtime.prototype, {
		render: function () {
			this.setClass();
			this.$input = this.$tpl.find( 'input' );
			this.selectField = this.$tpl.find( 'select' );
		},

		/**
		 Sets value of input.

		 @method value2input(value)
		 @param {mixed} value
		 **/
		value2input: function ( value ) {
			if ( !value ) {
				return;
			}

			if ( this.$input.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				var inputField = this.$input.filter( '.gv-inline-edit-single-field-mode' );
				this.nameInputNumber = inputField.data( 'inputnumber' );
				inputField.val( value[ this.nameInputNumber ] );
				return;
			}
			if ( this.selectField.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				this.selectField.val( value[ this.selectField.data( 'inputnumber' ) ] ).trigger( 'change', true );
				return;
			}
			this.$input.filter( '[id$="_1"]' ).val( value[ 1 ] );
			this.$input.filter( '[id$="_2"]' ).val( value[ 2 ] );
			//second argument needed to separate initial change from user's click (for autosubmit)
			this.selectField.filter( '[id$="_3"]' ).val( value[ 3 ] ).trigger( 'change', true );
		},

		/**
		 Returns value of input.

		 @method input2value()
		 **/
		input2value: function () {
			if ( this.$input.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				var value = {};
				value[ this.$input.data( 'inputnumber' ) ] = this.$input.filter( '.gv-inline-edit-single-field-mode' ).val();
				return value;
			}
			if ( this.selectField.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				var selectFieldValue = {};
				selectFieldValue[ this.selectField.data( 'inputnumber' ) ] = this.selectField.filter( '.gv-inline-edit-single-field-mode' ).val();
				return selectFieldValue;
			}
			return {
				1: this.$input.filter( '[id$="_1"]' ).val(),
				2: this.$input.filter( '[id$="_2"]' ).val(),
				3: this.selectField.filter( '[id$="_3"]' ).val(),
			};
		},

		/**
		 Default method to show value in element. Can be overwritten by display option.

		 @method value2html(value, element)
		 **/
		value2html: function ( value, element ) {

			if ( !value ) {
				$( element ).empty();
				return;
			}

			// Use .attr() because .data returns undefined instead of empty string, which is annoying
			var input_id = $( element ).attr( 'data-inputid' );

			var return_value = '';

			// Single hour/minute field
			if ( this.$input && this.$input.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				return_value = value[ this.$input.data( 'inputnumber' ) ];
			}

			// Single AM/PM field
			else if ( this.selectField && this.selectField.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				return_value = value[ this.selectField.data( 'inputnumber' ) ];
			}

			// Single field, when live-updating from full field
			else if ( '' !== input_id && value.hasOwnProperty( input_id ) ) {
				return_value = value[ input_id ];
			}

			// Full field
			else {
				return_value = value[ 1 ] + ':' + value[ 2 ] + ' ' + value[ 3 ];
			}

			$( element ).html( '<div>' + return_value.toUpperCase() + '</div>' );
		}
	} );

	Gvtime.defaults = $.extend( {}, $.fn.editabletypes.time.defaults );
	$.fn.editabletypes.gvtime = Gvtime;

}( window.jQuery ));
