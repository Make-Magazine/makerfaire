/**
 Name editable input.
 Internally value stored as {prefix: "Sir", first: "John", middle: "Millenium", last: "Doe", suffix: "II" }

 @class name
 @extends abstractinput
 @final
 @example https://github.com/vitalets/x-editable/tree/develop/dist/inputs-ext/address
 **/
(function ( $ ) {
	"use strict";

	var Name = function ( options ) {
		this.init( 'name', options, Name.defaults );
		this.selectField = null;
		this.singleFieldMode = false;
		this.nameInputNumber = 0;//Only used in single-field mode
	};

	//inherit from Abstract input
	$.fn.editableutils.inherit( Name, $.fn.editabletypes.abstractinput );

	$.extend( Name.prototype, {
		/**
		 Renders input from tpl

		 @method render()
		 **/
		render: function () {
			this.$input = this.$tpl.find( 'input' );
			this.selectField = this.$tpl.find( 'select' );
			if ( this.$input.filter( '.gv-inline-edit-single-field-mode' ).length ) {
				this.singleFieldMode = true;
			}

			this.$input.on('keydown.editable', function (e) {
				if (e.which === 13) {
					$(this).closest('form').submit();
				}
			});
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

			var input_id = $( element ).attr( 'data-inputid' );

			// Single input, but key not set
			if ( '' !== input_id && !value.hasOwnProperty( input_id ) ) {
				$( element ).empty();
				return;
			}

			var value_array = $.map( value, function ( val, index ) {
				return [ val ];
			} );

			if ( 0 === value_array.length ) {
				$( element ).empty();
				return;
			}
			var html = value_array.join( ' ' );

			$( element ).text( html );
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

			if ( this.singleFieldMode ) {
				var inputField = this.$input.filter( '.gv-inline-edit-single-field-mode' );
				this.nameInputNumber = inputField.data( 'inputnumber' );
				inputField.val( value[ this.nameInputNumber ] );
				return;
			}

			if ( this.$tpl.filter( '.gv-inline-edit-single-field-mode' ).length ) {//single-field mode using a dropdown
				this.selectField.val( value[ this.selectField.parent().data( 'inputnumber' ) ] ).trigger( 'change', true );
				return;
			}

			this.selectField.filter( '[name$=".2"]' ).val( value[ 2 ] ).trigger( 'change', true );//second argument needed to separate initial change from user's click (for autosubmit)
			this.$input.filter( '[name$=".3"]' ).val( value[ 3 ] );
			this.$input.filter( '[name$=".4"]' ).val( value[ 4 ] );
			this.$input.filter( '[name$=".6"]' ).val( value[ 6 ] );
			this.$input.filter( '[name$=".8"]' ).val( value[ 8 ] );

		},

		/**
		 Returns value of input.

		 @method input2value()
		 **/
		input2value: function () {
			var $inputField = this.$input.filter( '.gv-inline-edit-single-field-mode' );
			if ( this.singleFieldMode ) {
				var value = {};
				value[ $inputField.data( 'inputnumber' ) ] = $inputField.val();
				return value;
			}


			if ( this.$tpl.filter( '.gv-inline-edit-single-field-mode' ).length ) {//single-field mode using a dropdown
				var selectValue = {};
				selectValue[ this.selectField.parent().data( 'inputnumber' ) ] = this.selectField.val();
				return selectValue;
			}

			return {
				2: this.selectField.filter( '[name$=".2"]' ).val(),
				3: this.$input.filter( '[name$=".3"]' ).val(),
				4: this.$input.filter( '[name$=".4"]' ).val(),
				6: this.$input.filter( '[name$=".6"]' ).val(),
				8: this.$input.filter( '[name$=".8"]' ).val()
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

	Name.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults );

	$.fn.editabletypes.name = Name;

}( window.jQuery ));
