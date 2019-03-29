/**
 * Created by zackkatz on 2/8/17.
 */
/**
 Address editable input.
 See this as an example: https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address

 @class address
 @extends abstractinput
 @final
 **/
(function ( $ ) {
	"use strict";

	var Address = function ( options ) {
		this.init( 'address', options, Address.defaults );
		this.selectField = null;
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit( Address, $.fn.editabletypes.abstractinput );

	$.extend( Address.prototype, {

		/**
		 Renders input from tpl

		 @method render()
		 **/
		render: function () {
			this.$input = this.$tpl.find( 'input' );
			this.selectField = this.$tpl.find( 'select' );
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
			var value_array = $.map( value, function ( val, index ) {
				return [ val ];
			} );

			var html = value_array.join( ' ' );

			$( element ).html( '<div>' + html + '</div>' );
		},

		/**
		 Gets value from element's html

		 @method html2value(html)
		 **/
		html2value: function ( html ) {

			return null;
		},

		/**
		 Converts value to string.
		 It is used in internal comparing (not for sending to server).

		 @method value2str(value)
		 **/
		value2str: function ( value ) {
			var str = '';
			if ( value ) {
				for ( var k in value ) {
					str = str + k + ':' + value[ k ] + ';';
				}
			}
			return str;
		},

		/*
		 Converts string to value. Used for reading value from 'data-value' attribute.

		 @method str2value(str)
		 */
		str2value: function ( str ) {
			/*
			 this is mainly for parsing value defined in data-value attribute.
			 If you will always set value by javascript, no need to overwrite it
			 */
			return str;
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

			var inputUpdated = $.fn.editableutils.gvutils.value2input( this.$input.filter( '.gv-inline-edit-single-field-mode' ), value );
			if ( inputUpdated ) {
				return;
			}
			if ( this.selectField.filter( '.gv-inline-edit-single-field-mode' ).length ) {//single-field mode using a dropdown
				this.selectField.val( value[ this.selectField.data( 'inputnumber' ) ] ).trigger( 'change', true );
				return;
			}

			var stateField = this.$input.filter( '[name$=".4"]' );

			this.$input.filter( '[name$=".1"]' ).val( value[ 1 ] );
			this.$input.filter( '[name$=".2"]' ).val( value[ 2 ] );
			this.$input.filter( '[name$=".3"]' ).val( value[ 3 ] );

			//Support state fields from custom address types
			stateField.length ? stateField.val( value[ 4 ] ) : this.selectField.filter( '[name$=".4"]' ).val( value[ 4 ] ).trigger( 'change', true );

			this.$input.filter( '[name$=".4"]' ).val( value[ 4 ] );
			this.$input.filter( '[name$=".5"]' ).val( value[ 5 ] );

			this.selectField.filter( '[name$=".6"]' ).val( value[ 6 ] ).trigger( 'change', true );	//second argument needed to
			//separate initial change from user's click (for autosubmit)

		},

		/**
		 Returns value of input.

		 @method input2value()
		 **/
		input2value: function () {

			var returnValue = $.fn.editableutils.gvutils.input2value( this.$input.filter( '.gv-inline-edit-single-field-mode' ) );

			if ( returnValue ) {
				return returnValue;
			}

			if ( this.selectField.filter( '.gv-inline-edit-single-field-mode' ).length ) {//single-field mode using a dropdown
				var selectValue = {};
				selectValue[ this.selectField.data( 'inputnumber' ) ] = this.selectField.val();
				return selectValue;
			}
			var state = this.$input.filter( '[name$=".4"]' ).val();
			return {
				1: this.$input.filter( '[name$=".1"]' ).val(),
				2: this.$input.filter( '[name$=".2"]' ).val(),
				3: this.$input.filter( '[name$=".3"]' ).val(),
				4: 'undefined' !== typeof state ? state : this.selectField.filter( '[name$=".4"]' ).val(),
				5: this.$input.filter( '[name$=".5"]' ).val(),
				6: this.selectField.filter( '[name$=".6"]' ).val(),
			};
		},

		/**
		 Activates input: sets focus on the first field.

		 @method activate()
		 **/
		activate: function () {
			this.$input.filter( ':first-child' ).focus();
		},

		/**
		 Attaches handler to submit form in case of 'showbuttons=false' mode

		 @method autosubmit()
		 **/
		autosubmit: function () {
			this.$tpl.find( ':input' ).keydown( function ( e ) {
				if ( e.which === 13 ) {
					$( this ).closest( 'form' ).submit();
				}
			} );
		}
	} );

	Address.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults );

	$.fn.editabletypes.address = Address;

}( window.jQuery ));
