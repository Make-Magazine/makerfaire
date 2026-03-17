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

	var ImageChoice = function ( options ) {
		this.init( 'image-choice', options, ImageChoice.defaults );
	};
	$.fn.editableutils.inherit( ImageChoice, $.fn.editabletypes.checklist );

	$.extend( ImageChoice.prototype, {
		renderList: function () {
			this.$input = this.$tpl.find( 'input[type="radio"], input[type="checkbox"]' );
		},
		input2value: function () {
			var checked = [];
			this.$input.filter( ':checked' ).each( function ( i, el ) {
				checked.push( $( el ).val() );
			} );

			return checked;
		},
		str2value: function ( str ) {
			return str || null;
		},
		value2input: function ( value ) {
			var valueArray = [];
			
			// Handle both array and non-array values
			if ($.isArray(value)) {
				valueArray = $.map(value, function(val, index) {
					// Only add values that exist and can be used
					return val ? [val] : null;
				});
			} else if (value) {
				// Single value case
				valueArray = [value];
			}

			this.$input.prop('checked', false);
			if (valueArray.length) {
				this.$input.each(function(i, el) {
					var $el = $(el);
					// cannot use $.inArray as it performs strict comparison
					$.each(valueArray, function(j, val) {
						/*jslint eqeq: true*/
						if ($el.val() === val) {
							/*jslint eqeq: false*/
							$el.prop('checked', true);
						}
					});
				});
			}
		},
		value2str: function ( value ) {
			return value || '';
		},
		value2html: function ( value, element ) {
			var $el = $( element );
			var entryLink = $el.attr( 'data-entry-link' );

			if ( !value || !value.length ) {
				$el.empty();
				return;
			}

			var sourceData = $el.data( 'source' ),
				choiceDisplay = $el.data( 'choice_display' ),
				selected_choices = [];

			$.each( sourceData, function ( index, choice ) {
				// Skip choices with empty values - they match any string with indexOf('').
				if ( choice.hasOwnProperty( 'value' ) && choice.value !== '' && value.indexOf( choice.value ) !== -1 ) {
					selected_choices.push( choice );
				}
			} );


			if ( selected_choices.length > 0 ) {
				var html = '';

				$.each( selected_choices, function ( index, selected_choice ) {
					var choiceHtml = '';
					switch ( choiceDisplay ) {
						case 'label':
							choiceHtml = selected_choice.text;
							break;
						case 'image':
						default:
							choiceHtml = selected_choice.image_html;
							break;
					}

					if ( entryLink ) {
						choiceHtml = $( '<a />', { href: entryLink, html: choiceHtml } ).prop('outerHTML');
					}

					html += choiceHtml;
				} );

				$el.html( html );
			} else {
				$el.empty();
			}
		}
	} );

	ImageChoice.defaults = $.extend( {}, $.fn.editabletypes.list.defaults, {} );

	$.fn.editabletypes.image_choice = ImageChoice;

	/**
	 * Check if gformToggleRadioOther which is required for radio button field and it shows a warning if not found
	 * So create a placeholder function that returns null
	 */
	if ( typeof gformToggleRadioOther !== 'function' ) {
		window.gformToggleRadioOther = function ( ) {
			return null;
		};
	}

	// Toggle the radio or checkbox field when the image is clicked
	$( document ).on( 'click', '.gfield-choice-image-wrapper', function () {
		const $input = $( this ).parent().find( '.gfield-choice-input' );
	
		if ( $input.attr( 'type' ) === 'radio' ) {
			$input.prop( 'checked', true ); // radios should only be selected
		} else {
			$input.prop( 'checked', !$input.prop( 'checked' ) ); // checkboxes can toggle
		}
	} );
	
}( window.jQuery ));
