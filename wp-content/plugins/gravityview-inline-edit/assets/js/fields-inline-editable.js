/**
 * Initialize inline edit and handle all operations
 * related to it
 *
 * @license   GPL2+
 * @link      http://gravityview.co
 *
 * @since 1.0.0
 */

/* global gv_inline_x */ // PhpStorm helper
( function ( jQuery ) {
	"use strict";

	jQuery( document ).ready( function ( $ ) {

		var self = {};

		/**
		 * Set inline edit defaults and initialize everything
		 */
		self.init = function () {
			$.fn.editable.defaults.mode = gv_inline_x.mode;
			$.fn.editableform.buttons = gv_inline_x.buttons;
			$.fn.editableContainer.Inline.prototype.containerClass = "gv-editable-container gform_wrapper editable-container editable-inline";
			$.fn.editableContainer.Popup.prototype.containerClass = "gv-editable-container gform_wrapper editable-container editable-popup";
			self.setInlineEditableFields();
			self.setInlineEditableInitialState();
			self.toggleInlineEdit();
			self.initColumnToggle();
			self.gfMoveToggleButton();
			self.preventClicks();
		};

		/**
		 * From an instance of WP_Error, get the first error message
		 * Like the equivalent WP_Error method, get the first item from the first error
		 *
		 * @returns {string} WP_Error message
		 */
		self.getWPErrorMessage = function ( $wpError ) {
			for ( var error in $wpError ) {
				if ( $wpError.hasOwnProperty( error ) ) {
					return $wpError[ error ][ 0 ];
				}
			}
		};

		/**
		 * After editing a field via inline edit, update fields based on the response.
		 * Used primarily for updating number calculation fields
		 *
		 * @param {array} updateResponse  AJAX response
		 */
		self.updateFieldsOnEdit = function ( updateResponse ) {

			if ( !$.isArray( updateResponse ) ) {
				return;
			}

			$.each( updateResponse, function ( i, response ) {
				if ( response.value && response.selector ) {

					var maybe_json = $.parseJSON( response.value );

					if ( typeof maybe_json === 'object' ) {
						$( response.selector ).editable( 'setValue', maybe_json );
					} else {
						$( response.selector ).text( maybe_json );
					}
				}
			} );

		};

		/**
		 * Convert the GF date format into a friendly X-editable format
		 *
		 * @param  {string} $GFdateFormat The GF date format
		 * @returns {string} X-editable date format
		 */
		self.convertDateFormat = function ( $GFdateFormat ) {
			var $formatName;

			switch ( $GFdateFormat ) {
				case 'mdy' :
					$formatName = 'mm/dd/yyyy';
					break;
				case 'dmy' :
					$formatName = 'dd/mm/yyyy';
					break;
				case 'dmy_dash' :
					$formatName = 'dd-mm-yyyy';
					break;
				case 'dmy_dot' :
					$formatName = 'dd.mm.yyyy';
					break;
				case 'ymd_slash' :
					$formatName = 'yyyy/mm/dd';
					break;
				case 'ymd_dash' :
					$formatName = 'yyyy-mm-dd';
					break;
				case 'ymd_dot' :
					$formatName = 'yyyy.mm.dd';
					break;
			}
			return $formatName;
		};


		/**
		 * Iterate through all fields with class `gv-inline-editable-view` and
		 * ID `gv-inline-editable-*` and convert them into inline-editable fields
		 *
		 */
		self.setInlineEditableFields = function () {
			var editableOptions, tplName;

			$( '.gv-inline-editable-view [id^=gv-inline-editable-]' ).each( function ( i, val ) {

				var form_id = $( this ).data( 'formid' ), field_id = $( this ).data( 'fieldid' ),
					view_id = $( this ).data( 'viewid' );

				editableOptions = {
					pk: $( this ).data( 'entryid' ),
					url: gv_inline_x.url,
					container: gv_inline_x.container,
					showbuttons: gv_inline_x.showbuttons,
					onblur: gv_inline_x.onblur,
					params: {
						gv_inline_edit_field: 'true',
						nonce: gv_inline_x.nonce,
						type: $( this ).data( 'type' ),
						form_id: form_id,
						field_id: field_id,
						view_id: view_id
					},
					success: function ( response, newValue ) {
						if ( response.errors ) {
							return self.getWPErrorMessage( response.errors );
						}
						self.updateFieldsOnEdit( response );
					}
				};

				//This is a datepicker field
				if ( $( this ).data( 'dateformat' ) ) {
					editableOptions.format = 'yyyy-mm-dd';
					editableOptions.viewformat = self.convertDateFormat( $( this ).data( 'dateformat' ) );
					editableOptions.datepicker = {
						firstDay: 1,
						showOn: "focus"
					};
				}

				//Set templates
				tplName = editableOptions.params.type;
				if ( $( this ).data( 'tplmode' ) && 'address' !== editableOptions.params.type ) {
					tplName += $( this ).data( 'tplmode' );
				}

				if ( gv_inline_x.templates.hasOwnProperty( tplName + '_' + form_id + '_' + field_id ) ) {
					editableOptions.tpl = gv_inline_x.templates[ tplName + '_' + form_id + '_' + field_id ];
				} else if ( gv_inline_x.templates.hasOwnProperty( tplName ) ) {
					editableOptions.tpl = gv_inline_x.templates[ tplName ];
				}

				//Switch template to single-field equivalanes where applicable
				var singleModeDetails = self.getSingleFieldModeTemplate( $( this ), editableOptions.params.type, editableOptions.tpl );

				if ( singleModeDetails ) {
					if ( singleModeDetails.disableField ) {
						$( this ).parent().html( '' );
						return true;
					}
					editableOptions.tpl = singleModeDetails.tpl;
					editableOptions.params.inputNumber = singleModeDetails.inputNumber;
				}

				$( this ).editable( editableOptions );

				//For textarea fields
				if ( $( this ).data( 'maxLength' ) ) {
					editableOptions.params.maxlength = $( this ).data( 'maxLength' );
				}

				//For gvlist, multicolumn support
				if ( 'gvlist' === editableOptions.params.type && 'multi' === $( this ).data( 'tplmode' ) ) {
					$( this ).editable( 'option', 'multicolumn', true );
					$( this ).editable( 'option', 'colcount', $( this ).data( 'colcount' ) );
					self.setGvListEmpty( $( this ) );
				}

				$( this ).on( 'shown', self.inlineEditFieldsOnShown );
			} );

		};

		/**
		 * Runs after all inline edit fields are shown
		 *
		 */
		self.inlineEditFieldsOnShown = function ( e, editable ) {

			// We're in Entries. The first column has a link, so we need to move the form up one level.
			if ( 'inline' === editable.options.mode ) {
				if ( editable.container.$element.parents( '.column-primary' ).length ) {
					editable.container.$tip.insertBefore( editable.container.$element.parent() );
				}
			}

			if ( 'textarea' === editable.input.type ) {
				editable.input.$input.attr( 'maxlength', editable.options.maxlength );
			}
			if ( 'number' === editable.input.type ) {
				editable.input.$input.attr( 'step', 'any' );
			}
		};

		/**
		 * For a multi-column `gvlist` item, if the value is
		 * empty, set the field to empty instead of showing column headers
		 *
		 * @param {Object} $gvList DOM object of the gvlist entry to be examined
		 */
		self.setGvListEmpty = function ( $gvList ) {
			var gvListSourceData = $gvList.data( 'source' );

			if ( null === gvListSourceData[ 0 ] || 'object' !== typeof gvListSourceData[ 0 ] ) {
				return;
			}

			var valueArray = $.map( gvListSourceData[ 0 ], function ( val, index ) {
				if ( val.length > 0 ) {
					return [ val ];
				}
			} );

			if ( 0 === valueArray.length ) {
				$gvList.editable( 'setValue', '' );
			}
		};


		/**
		 * For some advanced fields, it's possible to use one of their input fields as
		 * a stand-alone field in a view e.g. For an address, you can use the 'Country' field
		 * as an entry in a view. This method returns the appropriate template for each of
		 * these fields and the corresponding input number.
		 * Note that each field in an advanced field has a corresponding unique input number.
		 *
		 * @param {Object} $field    The field being evaluated
		 * @param {string} fieldType The field type
		 * @returns {Object|boolean} false if the field isn't eligible for single-field mode or an object containing the template and input number for the single field
		 */
		self.getSingleFieldModeTemplate = function ( $field, fieldType, template ) {
			var eligibleFieldsSingleFieldMode = [ 'name', 'address', 'gvtime', 'checklist' ];//Which fields do we enable this mode on

			var inputNumber = $field.data( 'inputid' );

			/*
			 We activate this mode only for fields in eligibleFieldsSingleFieldMode; even then, we need to be able to retrieve the
			 input number for the field in question. All advanced fields have input numbers for the individual fields they store;
			 we get that input number by traversing the DOM to get the wrapping td
			 */
			if ( -1 === $.inArray( fieldType, eligibleFieldsSingleFieldMode ) || '' === inputNumber ) {
				return false;
			}


			inputNumber = inputNumber * 1;
			var singleFieldModeTpl = '';
			var disableField = false;

			switch ( fieldType ) {
				case 'name':
					if ( 2 === inputNumber ) {//prefix input
						singleFieldModeTpl += '<span class="gv-inline-edit-single-field-mode" data-inputnumber="' + inputNumber + '">' + gv_inline_x.templates[ fieldType + 'prefixes' ] + '</span>';
					}
					break;
				case 'checklist':
					// template contains all checkboxes in a <div><ul></div>
					var single_checkbox_html = $( template )
					// Remove all checkboxes that aren't the one we want to show
						.find( 'li' ).not( ':has(input[name$="\.' + inputNumber + '"])' ).remove().end()
						// And grab the <div class="ginput_container"> wrapper
						.parents( 'div' )[ 0 ].outerHTML;

					singleFieldModeTpl += '<div data-inputnumber="' + inputNumber + '">' + single_checkbox_html + '</div>';
					break;
				case 'address':
					//Hide disabled inputs
					var hiddenFields = $field.data( 'hidden' );
					if ( $.isArray( hiddenFields ) ) {
						$.each( hiddenFields, function ( i, val ) {//$.inArray does strict comparison so it'll fail
							if ( inputNumber === val ) {
								disableField = true;
							}
						} );
					}
					if ( 4 === inputNumber && 'international' !== $field.data( 'tplmode' ) && gv_inline_x.templates.hasOwnProperty( fieldType + $field.data( 'tplmode' ) ) ) {//canadian provinces, US states and any custom states added via filters. Differentiated using $field.data( 'tplmode' )
						singleFieldModeTpl += '<select class="gv-inline-edit-single-field-mode" data-inputnumber="' + inputNumber + '">' + gv_inline_x.templates[ fieldType + $field.data( 'tplmode' ) ] + '</select>';
					}
					if ( 6 === inputNumber ) {//country input
						singleFieldModeTpl += '<select class="gv-inline-edit-single-field-mode" data-inputnumber="' + inputNumber + '">' + gv_inline_x.templates[ fieldType + 'international' ] + '</select>';
					}
					break;
				case 'gvtime':
					if ( 3 === inputNumber ) {//am/pm selector
						singleFieldModeTpl += '<select class="gv-inline-edit-single-field-mode" data-inputnumber="' + inputNumber + '">' + '<option value="am">AM</option>' + '<option value="pm">PM</option>' + '</select>';
					}
					break;
			}

			if ( !singleFieldModeTpl ) {
				singleFieldModeTpl += '<input type="text" class="gv-inline-edit-single-field-mode" data-inputnumber="' + inputNumber + '"/>';
			}
			return {
				tpl: '<div>' + singleFieldModeTpl + '</div>',
				inputNumber: inputNumber,
				disableField: disableField
			};
		};

		/**
		 * Override the default inline-edit error method to support
		 * HTML in the error message
		 */
		$.fn.editableform.Constructor.prototype.error = function ( msg ) {
			var $group = this.$form.find( '.control-group' ),
				$block = this.$form.find( '.editable-error-block' );

			if ( msg === false ) {
				$group.removeClass( $.fn.editableform.errorGroupClass );
				$block.removeClass( $.fn.editableform.errorBlockClass ).empty().hide();
			} else {
				$group.addClass( $.fn.editableform.errorGroupClass );
				$block.addClass( $.fn.editableform.errorBlockClass ).html( msg ).show();
			}
		};

		/**
		 * Set the editable state for a View on load
		 *
		 * @returns {void}
		 */
		self.setInlineEditableInitialState = function () {

			$( '.gv-inline-editable-view' ).each( function () {

				if ( !$.cookie ) {
					self.setEditableState( $( this ), 'enabled', false );
					return;
				}

				var view_id = $( this ).find( '.gravityview-inline-edit-id' ).val();

				if ( 'undefined' === typeof view_id ) {
					return;
				}

				// On load
				var stored_state = $.cookie( 'gv-inline-edit-' + view_id );

				self.setEditableState( $( this ), stored_state, false );

			} );

		};

		/**
		 * Set the state, update button text, set cookie & and add classes associated with editable enabled/disabled states
		 *
		 * @param {jQuery[]} $views
		 * @param {string} state
		 * @param {bool} set_cookie
		 *
		 * @returns {void}
		 */
		self.setEditableState = function ( $views, state, set_cookie ) {

			$views.find( '.gv-inline-editable-disabled' ).toggleClass( 'editable-disabled', ( state === 'enabled' ) );

			if ( state && state === 'enabled' ) {

				$views.removeClass( 'gv-inline-edit-off' ).addClass( 'gv-inline-edit-on' );

				$views.find( '[id^=gv-inline-editable-]' ).editable( 'enable' );

				if ( gv_inline_x.showinputs ) {
					if ( '1' === gv_inline_x.showinputs ) {
						gv_inline_x.showinputs = '[id^=gv-inline-editable-]';
					}
					$views.find( gv_inline_x.showinputs ).editable( 'show' );
				}

			} else {
				$views.removeClass( 'gv-inline-edit-on' ).addClass( 'gv-inline-edit-off' );

				$views.find( '[id^=gv-inline-editable-]' ).editable( 'disable' );
			}

			$views.find( '.inline-edit-enable' ).text( function () {
				return $( this ).attr( 'data-label-' + state );
			} );

			if ( set_cookie && $.cookie ) {
				var view_id = $views.find( '.gravityview-inline-edit-id' ).val();
				$.cookie( 'gv-inline-edit-' + view_id, state, {
					domain: gv_inline_x.cookie_domain,
					path: gv_inline_x.cookiepath
				} );
			}

			/**
			 * todo: jsDoc
			 */
			$( document ).trigger( 'gravityview-inline-edit/set-state', {
				'state': state,
				'views': $views
			} );
		};

		/**
		 * In the front-end, turn inline edit functionality on/off
		 *
		 * // TODO: Toggle body class to allow better Bootstrap popover namespacing
		 *
		 * @returns {void}
		 */
		self.toggleInlineEdit = function () {

			$( '.inline-edit-enable' ).click( function ( e ) {

				e.preventDefault();

				// What View?
				var $view = $( this ).parents( '.gv-inline-editable-view' );
				var $editable = $view.find( '[id^=gv-inline-editable-]' );

				// Toggle
				$editable.editable( 'toggleDisabled' );

				// What's the current state?
				var enabled_state = $view.find( '.gv-inline-editable-value' ).hasClass( 'editable-disabled' ) ? 'disabled' : 'enabled';

				self.setEditableState( $view, enabled_state, true );
			} );
		};

		/**
		 * When Inline Edit is active, click on a column header in GF or GravityView, and you can edit the whole column directly
		 *
		 * @since 1.0
		 *
		 * @returns {void}
		 */
		self.initColumnToggle = function () {

			$( '.wp-admin .gf_entries, .gv-table-view' ).find( 'thead th, tfoot th' ).on( 'click', function ( e ) {

				// If Inline Edit isn't enabled, act normal
				if ( !$( this ).parents( '.gv-inline-edit-on' ).length ) {
					return true;
				}

				e.preventDefault();

				$( this ).toggleClass( 'gv-inline-edit-column-on' );

				// If in the Entries screen, columns have `column-field_id-` class structure.
				// In GravityView frontend, columns have `gv-field-` structure
				var regex_search = $( 'body' ).hasClass( 'wp-admin' ) ? /column-field_id-/ : /gv-field-/;

				// Get an array of all classes in the `class` attribute
				var class_list = $( this ).attr( 'class' ).split( /\s+/ );

				// We only want the one matching CSS name
				var css_class_name = false;
				$.each( class_list, function ( index, column_class_name ) {
					if ( column_class_name.match( regex_search ) ) {
						css_class_name = column_class_name;
						return false;
					}
				} );

				// Something's gone wrong, the class wasn't found
				if ( !css_class_name ) {
					return false;
				}

				// If column already enabled, turn off and restore options setting
				var is_column_on = $( this ).hasClass( 'gv-inline-edit-column-on' );

				var $editables = $( this )
					.parents( 'table' )
					.find( 'td[class~="' + css_class_name + '"]' )
					.find( '[id^=gv-inline-editable-]' );

				$editables
					.editable( 'hide' ) // Prevent weird toggle behavior
					.editable( 'option', 'showbuttons', is_column_on ? false : gv_inline_x.showbuttons )
					.editable( 'option', 'mode', is_column_on ? 'inline' : gv_inline_x.mode )
					.editable( 'option', 'onblur', is_column_on ? 'ignore' : gv_inline_x.onblur );

				if ( is_column_on ) {
					$editables.editable( 'show' );
				}

				return false;
			} );
		};

		/**
		 * When in the WP Admin "Entries" screen, allow our script to disable links by adding `disabled="disabled` to them
		 * @returns {void}
		 */
		self.gfMoveToggleButton = function () {
			$( '.wp-admin .inline-edit-enable' ).insertAfter( '.tablenav.top .bulkactions' );
		};

		/**
		 * When Inline Editing is active and a field isn't editable, prevent link clicking
		 * @returns {void}
		 */
		self.preventClicks = function () {
			$( '.gv-inline-editable-disabled a' ).on( 'click', function ( e ) {
				if ( $( this ).parents( '.editable-disabled' ).length ) {
					e.preventDefault();
					return false;
				}
				return true;
			} );
		};

		/**
		 * Trigger to extend global gv_inline_x object's templates data
		 */
		$( window ).on( 'gravityview-inline-edit/extend-template-data', function ( e, data ) {
			window.gv_inline_x.templates = $.extend( {}, window.gv_inline_x.templates, data );
		} );

		/**
		 * Trigger to initialize plugin
		 */
		$( window ).on( 'gravityview-inline-edit/init', function () {
			$( self.init );
		} );

		/**
		 * Initialize plugin on load if disableInitOnLoad parameter is not set
		 */
		if ( !!!+gv_inline_x.disableInitOnLoad ) {
			$( self.init );
		}

	} );

}( jQuery ) );
