/* eslint-disable linebreak-style */
/* eslint-disable camelcase */
/* global gform, jQuery, tinymce */
window.GWPAdvancedMergeTags = null;

const GWPAdvancedMergeTags = [];

( function() {
	window.GWPAdvancedMergeTags = function() {
		const self = GWPAdvancedMergeTags;

		/**
		 * Initialize the Advanced Merge Tags frontend functionality.
		 */
		self.init = function() {
			// Convert the gwp modifier that are supported for calculations. This should match the backend function.
			gform.addFilter( 'gform_merge_tag_value_pre_calculation', function( value, mergeTagArr, isVisible, formulaField, formId ) {
				// gwp_word_count modifier.
				if ( typeof ( mergeTagArr[ 4 ] ) === 'string' && mergeTagArr[ 4 ].startsWith( 'gwp_word_count' ) ) {
					const inputId = mergeTagArr[ 1 ];
					let regex = /[^\s]+/g;
					regex = gform.applyFilters( 'gravitywp_advancedmergetags_wordcount_regex', regex, value, inputId, formulaField, formId );

					const words = value.match( regex );
					let wordCount = words === null ? 0 : words.length;
					wordCount = gform.applyFilters( 'gravitywp_advancedmergetags_wordcount_result', wordCount, words, value, inputId, formulaField, formId );

					return wordCount;
				}

				// gwp_substring modifier.
				if ( typeof ( mergeTagArr[ 4 ] ) === 'string' && mergeTagArr[ 4 ].startsWith( 'gwp_substring' ) ) {
					const atts = self.parseMergetagsAtts( mergeTagArr[ 4 ] );
					if ( ! self.hasRequiredAtts( atts.named, [ 'start' ] ) ) {
						return value;
					}

					return self.substr( value, atts.named.start, atts.named.length );
				}

				// return unmodified value.
				return value;
			} );

			// Tinymce editor changes are not bound to trigger calculation events. When a tinymce is found for this input we bind it to the calculation event.
			gform.addAction( 'gform_post_calculation_events', function( match, formulaField, formId, calcObj ) {
				if ( typeof ( match[ 4 ] ) === 'string' && match[ 4 ].startsWith( 'gwp_word_count' ) ) {
					const inputId = match[ 1 ];
					const fieldId = parseInt( inputId, 10 );
					const input = jQuery( '#field_' + formId + '_' + fieldId ).find( 'textarea[name="input_' + inputId + '"]' );
					if ( input ) {
						jQuery( input ).keydown( function() {
							calcObj.bindCalcEvent( inputId, formulaField, formId );
						} ).change( function() {
							calcObj.bindCalcEvent( inputId, formulaField, formId, 0 );
						} );
					}
					// The editors are initialized on the gform_post_render event, to prevent this from executing before that we hook into the ready event.
					jQuery( document ).on( 'tinymce-editor-init', function() {
						if ( tinymce ) {
							const editor = tinymce.get( 'input_' + formId + '_' + fieldId );
							if ( editor ) {
								editor.on( 'keyup change', function() {
									calcObj.bindCalcEvent( inputId, formulaField, formId, 0 );
								} );
							}
						}
					} );
				}
			} );
		};

		/**
		 * Parses a string of merge tags and their associated attributes into an object with two properties: `named` and `numeric`.
		 * `named` is an object containing attribute names as keys and attribute values as values.
		 * `numeric` is an array containing the values of attributes that were identified as numeric.
		 *
		 * Based on WordPress Gutenberg JS Shortcode attribute parser. https://github.com/WordPress/gutenberg/blob/22ca90c1a6e88ad3ae72d49635b4c358a47f672f/packages/shortcode/src/index.js
		 *
		 * @param {string} text The string of merge tags and their associated attributes to parse.
		 * @return {{named: Object, numeric: Array}} An object with two properties: `named` and `numeric`.
		 */
		self.parseMergetagsAtts = function( text ) {
			const named = {};
			const numeric = [];

			// This regular expression is reused from `shortcode_parse_atts()` in
			// `wp-includes/shortcodes.php`.
			//
			// Capture groups:
			//
			// 1. An attribute name, that corresponds to...
			// 2. a value in double quotes.
			// 3. An attribute name, that corresponds to...
			// 4. a value in single quotes.
			// 5. An attribute name, that corresponds to...
			// 6. an unquoted value.
			// 7. A numeric attribute in double quotes.
			// 8. A numeric attribute in single quotes.
			// 9. An unquoted numeric attribute.
			const pattern =
				/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*'([^']*)'(?:\s|$)|([\w-]+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|'([^']*)'(?:\s|$)|(\S+)(?:\s|$)/g;

			// Map zero-width spaces to actual spaces.
			text = text.replace( /[\u00a0\u200b]/g, ' ' );

			let match;

			// Match and normalize attributes.
			while ( ( match = pattern.exec( text ) ) ) {
				if ( match[ 1 ] ) {
					named[ match[ 1 ].toLowerCase() ] = match[ 2 ];
				} else if ( match[ 3 ] ) {
					named[ match[ 3 ].toLowerCase() ] = match[ 4 ];
				} else if ( match[ 5 ] ) {
					named[ match[ 5 ].toLowerCase() ] = match[ 6 ];
				} else if ( match[ 7 ] ) {
					numeric.push( match[ 7 ] );
				} else if ( match[ 8 ] ) {
					numeric.push( match[ 8 ] );
				} else if ( match[ 9 ] ) {
					numeric.push( match[ 9 ] );
				}
			}

			return { named, numeric };
		};

		/**
		 * Check if an object contains all the required non-empty attributes.
		 *
		 * @param {Object} atts         The object containing the attributes to check.
		 * @param {Array}  requiredAtts The array of required attribute names.
		 *
		 * @return {boolean} True if all required attributes are present and non-empty, false otherwise.
		 */
		self.hasRequiredAtts = function( atts, requiredAtts ) {
			for ( let i = 0; i < requiredAtts.length; i++ ) {
				const key = requiredAtts[ i ];
				if ( ! atts.hasOwnProperty( key ) || atts[ key ].trim() === '' ) {
					return false;
				}
			}
			return true;
		};

		/**
		 * Returns the portion of string specified by the start and length parameters.
		 *
		 * @param {string}      str    - The input string to be processed.
		 * @param {number}      start  - If start is non-negative, the returned string will start at the start'th position in str, counting from zero. If start is negative, the returned string will start at the start'th position from the end of str.
		 * @param {number|null} length - If length is given and is positive, the string returned will contain at most length characters beginning from start (depending on the length of str). If length is given and is negative, then that many characters will be omitted from the end of str after the start position has been calculated. If length is null or not provided, the function returns the remaining part of the string starting from the start position.
		 *
		 * @return {string} The extracted substring from the input string.
		 */
		self.substr = function( str, start, length = null ) {
			// Cast the input string to a string.
			str = String( str );

			// Get the length of the string.
			const len = str.length;

			// Normalize the start parameter.
			start = parseInt( start, 10 );
			if ( isNaN( start ) ) {
				start = 0;
			} else if ( start < 0 ) {
				start = Math.max( len + start, 0 );
			} else {
				start = Math.min( start, len );
			}

			// Normalize the length parameter.
			if ( length === null ) {
				length = len - start;
			} else {
				length = parseInt( length, 10 );
				if ( isNaN( length ) ) {
					length = 0;
				}
			}

			// Calculate the end position.
			let end = start + length;
			if ( length < 0 ) {
				end = len + length;
			} else if ( end > len ) {
				end = len;
			}
			// Extract the substring from the input string and return it.
			return str.substring( start, end );
		};
		self.init();
	};
}() );

new window.GWPAdvancedMergeTags();

