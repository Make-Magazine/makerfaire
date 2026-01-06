/**
 * Survey editable input for Gravity Forms Survey Add-On.
 * Supports both single-row and multi-row survey fields.
 *
 * @class survey
 * @extends abstractinput
 * @final
 * @since 2.5.2
 */
(function ( $ ) {
	"use strict";

	var Survey = function ( options ) {
		this.init( 'survey', options, Survey.defaults );
		this.multipleRows = false;
		this.subtype = 'likert'; // Default subtype
		this.choicesMap = {}; // Map choice values to text for various field types

		// Determine if this is a multi-row survey, rank field, rating field, or other survey type from options
		if ( options && options.scope ) {
			var $element = $( options.scope );
			this.multipleRows = $element.attr( 'data-multiple-rows' ) === '1';
			this.subtype = $element.attr( 'data-subtype' ) || 'likert';
			
			// For fields with data-source (rank, rating, radio, checkbox, select), build a map
			var sourceData = $element.attr( 'data-source' );
			if ( sourceData ) {
				var choices = [];
				try {
					choices = JSON.parse( sourceData );
				} catch ( e ) {
					// Silently fail if JSON parsing fails
				}

				if ( choices && choices.length ) {
					for ( var i = 0; i < choices.length; i++ ) {
						this.choicesMap[ choices[ i ].value ] = choices[ i ].text;
					}
				}
			}
		}
	};

	// Inherit from Abstract input
	$.fn.editableutils.inherit( Survey, $.fn.editabletypes.abstractinput );

	$.extend( Survey.prototype, {
		/**
		 * Renders input from template
		 *
		 * @method render()
		 */
		render: function () {
			// Determine the appropriate selector based on the field subtype
			var selector;
			switch ( this.subtype ) {
				case 'rank':
					selector = 'ul.gsurvey-rank';
					break;
				case 'checkbox':
					selector = 'input[type="checkbox"]';
					break;
				case 'select':
					selector = 'select';
					break;
				case 'text':
					selector = 'input[type="text"]';
					break;
				case 'textarea':
					selector = 'textarea';
					break;
				case 'likert':
				case 'radio':
				case 'rating':
				default:
					selector = 'input[type="radio"]';
					break;
			}
			
			// Find and store the input element(s)
			this.$input = this.$tpl.find( selector );

			// Fix scroll jump issue for Likert scale tables
			// The gravityformssurvey plugin calls .focus() on radio inputs which causes scroll jumps
			if ( this.subtype === 'likert' ) {
				this.preventLikertScrollJump();
			}

			// Important to trigger survey plugin editable functions
			jQuery( document ).trigger( 'gform_post_render' );

		},

		/**
		 * Prevents scroll jump when clicking radio buttons in Likert scale tables.
		 * The gravityformssurvey plugin's gsurveySetUpLikertFields function calls .focus()
		 * on radio inputs, which causes the browser to scroll the element into view.
		 *
		 * @method preventLikertScrollJump()
		 */
		preventLikertScrollJump: function () {
			// Override the focus method on radio inputs to prevent scroll
			this.$input.each( function () {
				var originalFocus = this.focus;
				this.focus = function () {
					originalFocus.call( this, { preventScroll: true } );
				};
			} );
		},

		/**
		 * Default method to show value in element. Can be overwritten by display option.
		 *
		 * @method value2html(value, element)
		 */
		value2html: function ( value, element ) {
			var $element = $( element );
			
			if ( this.subtype === 'rank' ) {
				// Check if we have an OL (readonly display) or UL (editable form)
				var $ol = $element.find( 'ol.gsurvey-rank-entry' );
				var $ul = $element.find( 'ul.gsurvey-rank' );
				
				if ( $ol.length ) {
					// Readonly display - reorder the OL
					this.setRankDisplayOrder( value, $ol );
				} else if ( $ul.length ) {
					// Editable form - reorder the UL
					this.setRankOrder( value, $ul );
				} else if ( value ) {
					// No OL/UL found (empty field) - create OL from value
					this.createRankDisplayFromValue( value, $element );
				}
			} else if ( this.subtype === 'likert' ) {
				// For likert fields with their special table structure
				this.setLinkertSelectedValues( value, $element );
			} else if ( this.subtype === 'checkbox' ) {
				// For checkbox survey fields
				if ( value ) {
					this.setCheckboxDisplay( value, $element );
				}
			} else if ( this.subtype === 'text' || this.subtype === 'textarea' ) {
				// For text and textarea survey fields, just display the text value
				$element.text( value || '' );
			} else {
				// For rating, radio, select and other simple choice fields
				if ( value ) {
					this.setSimpleChoiceDisplay( value, $element );
				}
			}
		},

		/**
		 * Gets value from element's html
		 *
		 * @method html2value(html)
		 */
		html2value: function ( html ) {
			return null;
		},

		/**
		 * Converts value to string for internal comparing (not for sending to server).
		 *
		 * @method value2str(value)
		 */
		value2str: function ( value ) {
			if ( typeof value === 'object' ) {
				return JSON.stringify( value );
			}
			return value || '';
		},

		/**
		 * Converts string to value. Used for reading value from 'data-value' attribute.
		 *
		 * @method str2value(str)
		 */
		str2value: function ( str ) {
			// If it's already an object, return it as-is
			if ( typeof str === 'object' ) {
				return str;
			}
			
			if ( !str || str === '' ) {
				return this.multipleRows ? {} : '';
			}
			
			try {
				return JSON.parse( str );
			} catch ( e ) {
				return this.multipleRows ? {} : str;
			}
		},

		/**
		 * Sets selected values on radio buttons and adds/removes appropriate classes.
		 * Shared function used by both value2input and value2html.
		 *
		 * @method setLinkertSelectedValues(value, $radios)
		 * @param {mixed} value - The value to set (string for single-row, object for multi-row)
		 * @param {jQuery} $element - jQuery object of the field container element
		 */
		setLinkertSelectedValues: function ( value, $element = null ) {
			var self = this;
			var $radios = $element ? $element.find( 'input[type="radio"]' ) : this.$input;

			// First, clear all selections
			$radios.prop( 'checked', false );
			$radios.closest( 'td' ).removeClass( 'gsurvey-likert-selected' );
			
			if ( !value ) {
				return;
			}

			// Helper function to update admin text display
			var updateAdminText = function ( inputId, radioValue ) {
				if ( !$('body').hasClass('wp-admin') || ! $element ) {
					return;
				}

				var realValue = radioValue.split( ':' )[1] || radioValue;
				var textValue = self.choicesMap[realValue] || realValue;
				if ( self.multipleRows && typeof value === 'object' && !Array.isArray( value ) ) {
					$element.parents( 'tr' ).find( '.field_id-' + inputId.replace( '.', '\\.' ) + ' span' ).text( textValue );
				}else{
					$element.text( textValue );
				}
			};

			
			// Auto-detect if this is multi-row based on value structure
			if ( this.multipleRows && typeof value === 'object' && !Array.isArray( value ) ) {
				$.each( value, function ( inputId, radioValue ) {
					var $radio = $radios.filter( '[name="input_' + inputId + '"][value="' + radioValue + '"]' );
					if ( $radio.length ) {
						$radio.prop( 'checked', true ).closest( 'td' ).addClass( 'gsurvey-likert-selected' );
					}
					updateAdminText( inputId, radioValue );
				} );
			} else {
				// Single-row: value is a simple string (the column value)
				var $radio = $radios.filter( '[value="' + value + '"]' );
				if ( $radio.length ) {
					$radio.prop( 'checked', true ).closest( 'td' ).addClass( 'gsurvey-likert-selected' );
				}
				updateAdminText( null, value );
			}
		},

		/**
		 * Sets value of input (radio buttons in the survey table, rating field, or rank list items).
		 *
		 * @method value2input(value)
		 * @param {mixed} value
		 */
		value2input: function ( value ) {
			if ( this.subtype === 'rank' ) {
				this.setRankOrder( value, this.$input );
			} else if ( this.subtype === 'likert' ) {
				// For likert fields with their special table structure
				this.setLinkertSelectedValues( value );
			} else if ( this.subtype === 'checkbox' ) {
				// For checkbox survey fields (multiple selection)
				this.setCheckboxValues( value, this.$input );
			} else if ( this.subtype === 'text' || this.subtype === 'textarea' ) {
				// For text and textarea survey fields
				this.$input.val( value );
			} else {
				// For rating, radio, select and other simple choice fields
				this.setSimpleChoiceValue( value, this.$input );
			}
		},

		/**
		 * Returns value of input.
		 *
		 * @method input2value()
		 */
		input2value: function () {
			if ( this.subtype === 'rank' ) {
				// Rank field: return comma-separated list of choice IDs in order
				return this.getRankValue( this.$input );
			} else if ( this.subtype === 'select' ) {
				// Select field: return the selected option value
				return this.$input.val() || '';
			} else if ( this.subtype === 'text' || this.subtype === 'textarea' ) {
				// Text and textarea survey fields: return the input value
				return this.$input.val() || '';
			} else if ( this.subtype === 'checkbox' ) {
				// Checkbox survey field: return array of checked values
				var values = [];
				this.$input.filter( ':checked' ).each( function () {
					values.push( $( this ).val() );
				} );
				return values;
			} else if ( this.multipleRows ) {
				// Multi-row likert: return object with all row values
				var rowValues = {};
				
				this.$input.filter( ':checked' ).each( function () {
					var $radio = $( this );
					var name = $radio.attr( 'name' );
					var value = $radio.val();
					
					// Extract input ID from name (e.g., "input_10.1" -> "10.1")
					var inputId = name.replace( /^input_/, '' );
					rowValues[ inputId ] = value;
				} );
				
				return rowValues;
			} else {
				// Single-row likert, rating, and radio: return the selected value
				var checked = this.$input.filter( ':checked' );
				return checked.length ? checked.val() : '';
			}
		},

		/**
		 * Sets the order of rank field items based on the value.
		 * 
		 * @method setRankOrder(value, $rankList)
		 * @param {string} value - Comma-separated values representing the order
		 * @param {jQuery} $rankList - jQuery object of the rank UL element or its container
		 */
		setRankOrder: function ( value, $rankList ) {
			if ( ! value ) {
				return;
			}

			// If $rankList is a container, find the actual UL
			var $ul = $rankList.is( 'ul.gsurvey-rank' ) ? $rankList : $rankList.find( 'ul.gsurvey-rank' );

			if ( ! $ul.length ) {
				return;
			}

			// Split the comma-separated values
			var orderedValues = value.split( ',' );

			// Reorder the list items based on the ordered values
			var $items = $ul.children( 'li.gsurvey-rank-choice' );

			// Create a map of items by their ID (which contains the choice value)
			var itemMap = {};
			$items.each( function () {
				var $item = $( this );
				var itemId = $item.attr( 'id' );
				if ( itemId ) {
					itemMap[ itemId ] = $item;
				}
			} );

			// Re-append items in the correct order
			$.each( orderedValues, function ( index, choiceValue ) {
				var trimmedValue = $.trim( choiceValue );
				if ( itemMap[ trimmedValue ] ) {
					$ul.append( itemMap[ trimmedValue ] );
				}
			} );
	
		},

		/**
		 * Gets the current rank order as a comma-separated string.
		 * 
		 * @method getRankValue($rankList)
		 * @param {jQuery} $rankList - jQuery object of the rank UL element
		 * @return {string} Comma-separated values representing the current order
		 */
		getRankValue: function ( $rankList ) {
			var values = [];
			
			$rankList.children( 'li' ).each( function () {
				var $item = $( this );
				var itemId = $item.attr( 'id' );
				if ( itemId ) {
					values.push( itemId );
				}
			} );
			
			return values.join( ',' );
		},

		/**
		 * Sets the order of rank field items in the readonly display (OL element).
		 * This is called by value2html to update the display after save.
		 * 
		 * The saved value contains choice values (e.g., "grank12c1bb841,grank18bf0eec2,...")
		 * but the OL contains text (e.g., "First Choice", "Second Choice", ...).
		 * We use this.choicesMap to convert values to text.
		 * 
		 * @method setRankDisplayOrder(value, $ol)
		 * @param {string} value - Comma-separated choice values representing the order
		 * @param {jQuery} $ol - jQuery object of the readonly OL element
		 */
		setRankDisplayOrder: function ( value, $ol ) {
			if ( ! value || ! $ol.length ) {
				return;
			}

			// Split the comma-separated values (these are the choice values in order)
			var orderedValues = value.split( ',' );

			// Get all list items from the OL
			var $items = $ol.children( 'li' );
			
			// Create a map of items by their text content
			var itemsByText = {};
			$items.each( function () {
				var $item = $( this );
				var text = $item.text().trim();
				itemsByText[ text ] = $item;
			} );

			// Detach all items
			$items.detach();

			var self = this;
			
			// Re-append items in the correct order based on orderedValues
			$.each( orderedValues, function ( index, choiceValue ) {
				var trimmedValue = $.trim( choiceValue );
				
				// Convert choice value to text using the choices map
				var choiceText = self.choicesMap[ trimmedValue ] || trimmedValue;
				
				// Find the item by its text
				if ( itemsByText[ choiceText ] ) {
					$ol.append( itemsByText[ choiceText ] );
					delete itemsByText[ choiceText ];
				}
			} );
			
			// Append any remaining unmatched items
			$.each( itemsByText, function ( text, $item ) {
				$ol.append( $item );
			} );
		},

		/**
		 * Creates a rank field display (OL) from a saved value when no display exists yet.
		 * This happens when a previously empty field gets a value through inline edit.
		 * 
		 * @method createRankDisplayFromValue(value, $element)
		 * @param {string} value - Comma-separated choice values representing the order
		 * @param {jQuery} $element - jQuery object of the field container element
		 */
		createRankDisplayFromValue: function ( value, $element ) {
			if ( ! value ) {
				return;
			}

			// Split the comma-separated values
			var orderedValues = value.split( ',' );
			
			// Create the OL element
			var $ol = $( '<ol class="gsurvey-rank-entry"></ol>' );
			
			var self = this;
			
			// Add each choice as an LI in order
			$.each( orderedValues, function ( index, choiceValue ) {
				var trimmedValue = $.trim( choiceValue );
				
				// Convert choice value to text using the choices map
				var choiceText = self.choicesMap[ trimmedValue ] || trimmedValue;
				
				if ( choiceText ) {
					$ol.append( $( '<li></li>' ).text( choiceText ) );
				}
			} );
			
			// Replace the element's content with the new OL
			$element.html( $ol );
		},

		/**
		 * Sets the selected value for simple choice fields (rating, radio, checkbox, select).
		 * 
		 * @method setSimpleChoiceValue(value, $inputs)
		 * @param {string} value - The choice value to select
		 * @param {jQuery} $inputs - jQuery collection of input elements or select element
		 */
		setSimpleChoiceValue: function ( value, $inputs ) {
			if ( ! value ) {
				// Clear all selections
				if ( $inputs.is( 'select' ) ) {
					$inputs.val( '' );
				} else {
					$inputs.prop( 'checked', false );
				}
				return;
			}
			
			// Handle select elements
			if ( $inputs.is( 'select' ) ) {
				$inputs.val( value );
			} else {
				// Handle radio and checkbox inputs
				$inputs.each( function () {
					var $input = $( this );
					if ( $input.val() === value ) {
						$input.prop( 'checked', true );
					} else {
						$input.prop( 'checked', false );
					}
				} );
			}
		},

		/**
		 * Sets the display text for simple choice fields based on the selected value.
		 * This is called after saving to update the readonly display.
		 * Used for rating, radio, select and other simple single-choice fields.
		 * 
		 * @method setSimpleChoiceDisplay(value, $element)
		 * @param {string} value - The choice value
		 * @param {jQuery} $element - jQuery object of the field container element
		 */
		setSimpleChoiceDisplay: function ( value, $element ) {
			if ( ! value ) {
				// Clear the display
				$element.empty();
				return;
			}
			
			// Get the text for this value from the choices map
			var choiceText = this.choicesMap[ value ] || value;
			
			// Update or create the display text
			$element.text( choiceText );
		},

		/**
		 * Sets the selected values for checkbox survey fields (multiple selection).
		 * 
		 * @method setCheckboxValues(values, $checkboxes)
		 * @param {array|string} values - Array of checkbox values to select (or JSON string)
		 * @param {jQuery} $checkboxes - jQuery collection of checkbox inputs
		 */
		setCheckboxValues: function ( values, $checkboxes ) {
			// Parse JSON string if needed
			if ( typeof values === 'string' && values ) {
				try {
					values = JSON.parse( values );
				} catch ( e ) {
					values = [];
				}
			}
			
			if ( ! Array.isArray( values ) ) {
				values = [];
			}
			
			// First, uncheck all checkboxes
			$checkboxes.prop( 'checked', false );
			
			// Then check the ones in the values array
			$checkboxes.each( function () {
				var $checkbox = $( this );
				if ( values.indexOf( $checkbox.val() ) !== -1 ) {
					$checkbox.prop( 'checked', true );
				}
			} );
		},

		/**
		 * Sets the display text for checkbox survey fields based on selected values.
		 * This is called after saving to update the readonly display.
		 * Displays values as a bulleted list (UL).
		 * 
		 * @method setCheckboxDisplay(values, $element)
		 * @param {array|string} values - Array of checkbox values (or JSON string)
		 * @param {jQuery} $element - jQuery object of the field container element
		 */
		setCheckboxDisplay: function ( values, $element ) {
			// Parse JSON string if needed
			if ( typeof values === 'string' && values ) {
				try {
					values = JSON.parse( values );
				} catch ( e ) {
					values = [];
				}
			}
			
			if ( ! Array.isArray( values ) || values.length === 0 ) {
				$element.empty();
				return;
			}
			
			var self = this;
			
			// Create a UL element with class "bulleted"
			var $ul = $( '<ul class="bulleted"></ul>' );
			
			// Add each value as an LI
			$.each( values, function ( index, value ) {
				var choiceText = self.choicesMap[ value ] || value;
				$ul.append( $( '<li></li>' ).text( choiceText ) );
			} );
			
			// Replace the element's content with the UL
			$element.html( $ul );
		},

	} );

	Survey.defaults = $.extend( {}, $.fn.editabletypes.abstractinput.defaults );

	$.fn.editabletypes.survey = Survey;

}( window.jQuery ));

