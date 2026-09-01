/**
 * Frontend table bulk actions.
 *
 * @since 3.0.0
 */
( function () {
	'use strict';

	var strings = window.gvGlobals || {};
	var initializedForms = [];
	var globalFormListenersInitialized = false;
	var modalIdCounter = 0;
	var bodyScrollLockCount = 0;
	var previousBodyOverflow = '';
	/**
	 * DOM event dispatched after one Bulk Actions form writes persistent selection
	 * state. Same-page controls with the same storage key and render instance use
	 * it to mirror the localStorage-backed selection immediately; cross-tab sync
	 * still uses the native storage event.
	 *
	 * Event detail: { form, renderInstance, storageKey, viewId }.
	 */
	var selectionChangedEvent = 'gk.gravityview.bulkActions.selectionChanged';
	strings.bulk_actions_entry_selected = strings.bulk_actions_entry_selected || 'entry selected';
	strings.bulk_actions_entries_selected = strings.bulk_actions_entries_selected || 'entries selected';
	strings.bulk_actions_entry_selected_sentence = strings.bulk_actions_entry_selected_sentence || '[count] selected.';
	strings.bulk_actions_entries_selected_sentence = strings.bulk_actions_entries_selected_sentence || '[count] selected.';
	strings.bulk_actions_select_entries = strings.bulk_actions_select_entries || 'Select one or more entries before applying a bulk action.';
	strings.bulk_actions_select_action = strings.bulk_actions_select_action || 'Select a bulk action to apply.';
	strings.bulk_actions_confirm_title = strings.bulk_actions_confirm_title || 'Apply bulk action?';
	strings.bulk_actions_confirm_button = strings.bulk_actions_confirm_button || 'Apply';
	strings.bulk_actions_delete_confirm_title = strings.bulk_actions_delete_confirm_title || 'Delete selected entries?';
	strings.bulk_actions_delete_confirm_message = strings.bulk_actions_delete_confirm_message || 'This will delete [count] entries. This cannot be undone. Type [word] to confirm.';
	strings.bulk_actions_delete_confirm_message_singular = strings.bulk_actions_delete_confirm_message_singular || 'This will delete [count] entry. This cannot be undone. Type [word] to confirm.';
	strings.bulk_actions_delete_confirm_label = strings.bulk_actions_delete_confirm_label || 'Type [word] to confirm';
	strings.bulk_actions_delete_confirm_word = strings.bulk_actions_delete_confirm_word || 'DELETE';
	strings.bulk_actions_delete_confirm_button = strings.bulk_actions_delete_confirm_button || 'Delete entries';
	strings.bulk_actions_typed_confirm_message = strings.bulk_actions_typed_confirm_message || 'This action will affect [count] entries. Type [word] to confirm.';
	strings.bulk_actions_typed_confirm_label = strings.bulk_actions_typed_confirm_label || 'Type [word] to confirm';
	strings.bulk_actions_typed_confirm_word = strings.bulk_actions_typed_confirm_word || 'CONFIRM';
	strings.bulk_actions_typed_confirm_button = strings.bulk_actions_typed_confirm_button || 'Apply';
	strings.bulk_actions_edit_title = strings.bulk_actions_edit_title || 'Edit selected entries';
	strings.bulk_actions_edit_message = strings.bulk_actions_edit_message || 'Choose fields to update for [count] selected entries.';
	strings.bulk_actions_edit_message_singular = strings.bulk_actions_edit_message_singular || 'Choose fields to update for [count] selected entry.';
	strings.bulk_actions_edit_field_label = strings.bulk_actions_edit_field_label || 'Field';
	strings.bulk_actions_edit_operation_label = strings.bulk_actions_edit_operation_label || 'Action';
	strings.bulk_actions_edit_value_label = strings.bulk_actions_edit_value_label || 'Value';
	strings.bulk_actions_edit_set_value = strings.bulk_actions_edit_set_value || 'Set value';
	strings.bulk_actions_edit_clear_value = strings.bulk_actions_edit_clear_value || 'Clear field value';
	strings.bulk_actions_edit_choose_field = strings.bulk_actions_edit_choose_field || 'Choose a field';
	strings.bulk_actions_edit_choose_value = strings.bulk_actions_edit_choose_value || 'Choose a value';
	strings.bulk_actions_edit_already_chosen = strings.bulk_actions_edit_already_chosen || 'already chosen';
	strings.bulk_actions_edit_add_field = strings.bulk_actions_edit_add_field || 'Add another field';
	strings.bulk_actions_edit_update_entries = strings.bulk_actions_edit_update_entries || 'Update entries';
	strings.bulk_actions_edit_remove_field = strings.bulk_actions_edit_remove_field || 'Remove field';
	strings.bulk_actions_edit_no_fields = strings.bulk_actions_edit_no_fields || 'No editable fields or entry properties are configured for this View.';
	strings.bulk_actions_edit_duplicate_field = strings.bulk_actions_edit_duplicate_field || 'Choose each field only once.';
	strings.bulk_actions_edit_value_required = strings.bulk_actions_edit_value_required || 'Enter a value or choose Clear field value.';
	strings.bulk_actions_edit_required_clear = strings.bulk_actions_edit_required_clear || 'Required fields cannot be cleared.';
	strings.bulk_actions_edit_review_required = strings.bulk_actions_edit_review_required || 'Confirm that you reviewed these bulk edits.';
	strings.bulk_actions_edit_review_acknowledge = strings.bulk_actions_edit_review_acknowledge || 'I understand these changes will be applied to the selected entries.';
	strings.bulk_actions_edit_clear_warning = strings.bulk_actions_edit_clear_warning || 'One or more fields will be cleared.';
	strings.bulk_actions_edit_large_warning = strings.bulk_actions_edit_large_warning || 'This will update [count] entries.';
	strings.bulk_actions_resend_title = strings.bulk_actions_resend_title || 'Resend notifications';
	strings.bulk_actions_resend_message = strings.bulk_actions_resend_message || 'Choose notifications to send for [count] selected entries.';
	strings.bulk_actions_resend_message_singular = strings.bulk_actions_resend_message_singular || 'Choose notifications to send for [count] selected entry.';
	strings.bulk_actions_resend_notification_label = strings.bulk_actions_resend_notification_label || 'Notifications';
	strings.bulk_actions_resend_send_to_label = strings.bulk_actions_resend_send_to_label || 'Send to';
	strings.bulk_actions_resend_send_to_help = strings.bulk_actions_resend_send_to_help || 'Leave blank to use each notification recipient.';
	strings.bulk_actions_resend_send_to_invalid = strings.bulk_actions_resend_send_to_invalid || 'Enter a valid Send to email address.';
	strings.bulk_actions_resend_choose_notification = strings.bulk_actions_resend_choose_notification || 'Choose at least one notification.';
	strings.bulk_actions_resend_no_notifications = strings.bulk_actions_resend_no_notifications || 'No notifications are available for this View.';
	strings.bulk_actions_resend_confirm_button = strings.bulk_actions_resend_confirm_button || 'Send notifications';
	strings.bulk_actions_cancel = strings.bulk_actions_cancel || 'Cancel';
	strings.bulk_actions_validate_action = strings.bulk_actions_validate_action || 'gv_bulk_action_validate';
	strings.bulk_actions_validate_error = strings.bulk_actions_validate_error || 'The bulk action data could not be validated.';
	strings.bulk_actions_validate_network_error = strings.bulk_actions_validate_network_error || 'The validation request could not be completed. Check your connection and try again.';
	strings.bulk_actions_validating = strings.bulk_actions_validating || 'Validating...';
	strings.bulk_actions_background_active = strings.bulk_actions_background_active || 'A background action is already running. Wait for it to finish before starting another bulk action.';
	strings.bulk_actions_ajax_url = strings.bulk_actions_ajax_url || window.ajaxurl || '';
	strings.bulk_actions_ajax_error = strings.bulk_actions_ajax_error || 'Background progress could not be updated.';

	function ready( callback ) {
		if ( 'loading' === document.readyState ) {
			document.addEventListener( 'DOMContentLoaded', callback );
			return;
		}

		callback();
	}

	function toArray( collection ) {
		return Array.prototype.slice.call( collection || [] );
	}

	function toInt( value ) {
		return parseInt( value, 10 ) || 0;
	}

	function toBool( value ) {
		return true === value || '1' === value || 'true' === value;
	}

	function uniqueId( prefix ) {
		modalIdCounter++;

		return prefix + modalIdCounter;
	}

	function lockBodyScroll() {
		if ( !bodyScrollLockCount ) {
			previousBodyOverflow = document.body.style.overflow || '';
			document.body.style.overflow = 'hidden';
		}

		bodyScrollLockCount++;
	}

	function unlockBodyScroll() {
		bodyScrollLockCount = Math.max( 0, bodyScrollLockCount - 1 );

		if ( !bodyScrollLockCount ) {
			document.body.style.overflow = previousBodyOverflow;
			previousBodyOverflow = '';
		}
	}

	function pruneInitializedForms() {
		initializedForms = initializedForms.filter( function ( api ) {
			return api && api.isConnected();
		} );

		return initializedForms;
	}

	function initGlobalFormListeners() {
		if ( globalFormListenersInitialized ) {
			return;
		}

		globalFormListenersInitialized = true;

		document.addEventListener( 'gk.gravityview.bulkActions.backgroundStatus', function ( event ) {
			pruneInitializedForms().forEach( function ( api ) {
				api.onBackgroundStatus( event );
			} );
		} );

		window.addEventListener( 'pageshow', function ( event ) {
			pruneInitializedForms().forEach( function ( api ) {
				api.onPageShow( event );
			} );
		} );

		window.addEventListener( 'storage', function ( event ) {
			pruneInitializedForms().forEach( function ( api ) {
				api.onStorage( event );
			} );
		} );

		document.addEventListener( selectionChangedEvent, function ( event ) {
			pruneInitializedForms().forEach( function ( api ) {
				if ( api.onSelectionChanged ) {
					api.onSelectionChanged( event );
				}
			} );
		} );
	}

	function registerInitializedForm( api ) {
		initializedForms.push( api );
		initGlobalFormListeners();
	}

	function formatSelectedCount( count ) {
		var template = 1 === count ? strings.bulk_actions_entry_selected_sentence : strings.bulk_actions_entries_selected_sentence;

		if ( -1 === template.indexOf( '[count]' ) ) {
			return count + ' ' + ( 1 === count ? strings.bulk_actions_entry_selected : strings.bulk_actions_entries_selected ) + '.';
		}

		return template.replace( '[count]', count );
	}

	/**
	 * Developer hooks use WordPress' wp.hooks API.
	 *
	 * Filters:
	 * - gk.gravityview.bulkActions.elements
	 * - gk.gravityview.bulkActions.actions
	 * - gk.gravityview.bulkActions.action
	 * - gk.gravityview.bulkActions.confirmation
	 * - gk.gravityview.bulkActions.confirmationHandler
	 *
	 * Actions:
	 * - gk.gravityview.bulkActions.ready
	 * - gk.gravityview.bulkActions.beforeConfirm
	 * - gk.gravityview.bulkActions.cancelled
	 * - gk.gravityview.bulkActions.beforeSubmit
	 * - gk.gravityview.bulkActions.beforeDismiss
	 */
	function getHooks() {
		return window.wp && window.wp.hooks ? window.wp.hooks : null;
	}

	function applyFilters( name, value ) {
		var wpHooks = getHooks();

		if ( wpHooks && wpHooks.applyFilters ) {
			return wpHooks.applyFilters.apply( wpHooks, arguments );
		}

		return value;
	}

	function doAction( name ) {
		var wpHooks = getHooks();

		if ( wpHooks && wpHooks.doAction ) {
			wpHooks.doAction.apply( wpHooks, arguments );
		}
	}

	function normalizeIds( ids ) {
		if ( !Array.isArray( ids ) ) {
			return [];
		}

		return ids.map( toInt ).filter( function ( entryId, index, selected ) {
			return entryId && selected.indexOf( entryId ) === index;
		} );
	}

	function matchesRenderInstance( element, renderInstance ) {
		return !renderInstance || element.getAttribute( 'data-render-instance' ) === renderInstance;
	}

	function findByViewId( scope, selector, viewId, renderInstance ) {
		return toArray( scope.querySelectorAll( selector ) ).filter( function ( element ) {
			return element.getAttribute( 'data-view-id' ) === viewId && matchesRenderInstance( element, renderInstance );
		} );
	}

	function uniqueElements( elements ) {
		return elements.filter( function ( element, index ) {
			return element && elements.indexOf( element ) === index;
		} );
	}

	function findElements( scope, role, fallbackSelector, viewId, renderInstance ) {
		var elements = findByViewId( scope, '[data-gv-bulk-role="' + role + '"]', viewId, renderInstance );
		var fallbacks = fallbackSelector ? findByViewId( scope, fallbackSelector, viewId, renderInstance ) : [];

		return uniqueElements( elements.concat( fallbacks ) );
	}

	function findFirstElement( form, scope, role, fallbackSelector, viewId, renderInstance ) {
		var elements = findElements( form, role, fallbackSelector, viewId, renderInstance );

		if ( elements.length ) {
			return elements[0];
		}

		elements = findElements( scope, role, fallbackSelector, viewId, renderInstance );

		return elements.length ? elements[0] : null;
	}

	function isViewElement( element, role, fallbackSelector, viewId, renderInstance ) {
		if ( !element || element.getAttribute( 'data-view-id' ) !== viewId || !matchesRenderInstance( element, renderInstance ) ) {
			return false;
		}

		if ( element.getAttribute( 'data-gv-bulk-role' ) === role ) {
			return true;
		}

		return fallbackSelector && element.matches && element.matches( fallbackSelector );
	}

	function closestViewElement( element, role, fallbackSelector, viewId, renderInstance ) {
		while ( element && element !== document ) {
			if ( isViewElement( element, role, fallbackSelector, viewId, renderInstance ) ) {
				return element;
			}

			element = element.parentElement;
		}

		return null;
	}

	function matchesViewElement( element, role, fallbackSelector, viewId, renderInstance ) {
		return !!closestViewElement( element, role, fallbackSelector, viewId, renderInstance );
	}

	function storageFor( storageKey ) {
		return {
			getItem: function () {
				try {
					return window.localStorage.getItem( storageKey );
				} catch ( e ) {
					return '';
				}
			},
			setItem: function ( value ) {
				try {
					window.localStorage.setItem( storageKey, value );
				} catch ( e ) {}
			},
			removeItem: function () {
				try {
					window.localStorage.removeItem( storageKey );
				} catch ( e ) {}
			}
		};
	}

	function normalizeConfirmation( confirmation ) {
		if ( 'string' === typeof confirmation ) {
			confirmation = {
				enabled: '' !== confirmation,
				message: confirmation
			};
		} else if ( 'boolean' === typeof confirmation ) {
			confirmation = {
				enabled: confirmation
			};
		} else if ( !confirmation || 'object' !== typeof confirmation ) {
			confirmation = {};
		}

		return {
			enabled: !!confirmation.enabled,
			title: confirmation.title || strings.bulk_actions_confirm_title,
			titleSingular: confirmation.titleSingular || confirmation.title_singular || '',
			message: confirmation.message || '',
			messageSingular: confirmation.messageSingular || confirmation.message_singular || '',
			actionLabel: confirmation.actionLabel || confirmation.action_label || strings.bulk_actions_confirm_button,
			actionLabelSingular: confirmation.actionLabelSingular || confirmation.action_label_singular || ''
		};
	}

	function resolveConfirmationForCount( confirmation, selectedCount ) {
		confirmation = normalizeConfirmation( confirmation );

		if ( 1 !== selectedCount ) {
			return confirmation;
		}

		return {
			enabled: confirmation.enabled,
			title: confirmation.titleSingular || confirmation.title,
			titleSingular: confirmation.titleSingular,
			message: confirmation.messageSingular || confirmation.message,
			messageSingular: confirmation.messageSingular,
			actionLabel: confirmation.actionLabelSingular || confirmation.actionLabel,
			actionLabelSingular: confirmation.actionLabelSingular
		};
	}

	function findOption( select, value ) {
		var matches = toArray( select.options ).filter( function ( option ) {
			return option.value === value;
		} );

		return matches.length ? matches[0] : null;
	}

	function updateOptionConfirmation( option, confirmation ) {
		option.setAttribute( 'data-confirm-enabled', confirmation.enabled ? '1' : '0' );
		option.setAttribute( 'data-confirm-title', confirmation.title || '' );
		option.setAttribute( 'data-confirm-title-singular', confirmation.titleSingular || '' );
		option.setAttribute( 'data-confirm-message', confirmation.message || '' );
		option.setAttribute( 'data-confirm-message-singular', confirmation.messageSingular || '' );
		option.setAttribute( 'data-confirm-action-label', confirmation.actionLabel || '' );
		option.setAttribute( 'data-confirm-action-label-singular', confirmation.actionLabelSingular || '' );
		option.setAttribute( 'data-confirm', confirmation.message || '' );
	}

	function normalizeTypedConfirmation( typedConfirmation ) {
		typedConfirmation = typedConfirmation && 'object' === typeof typedConfirmation ? typedConfirmation : {};

		return {
			enabled: !!typedConfirmation.enabled,
			threshold: Math.max( 1, toInt( typedConfirmation.threshold ) )
		};
	}

	function parseJsonAttribute( element, attributeName ) {
		var value = element ? element.getAttribute( attributeName ) : '';

		if ( !value ) {
			return {};
		}

		try {
			return JSON.parse( value );
		} catch ( e ) {
			return {};
		}
	}

	function updateOptionTypedConfirmation( option, typedConfirmation ) {
		typedConfirmation = normalizeTypedConfirmation( typedConfirmation );

		option.setAttribute( 'data-typed-confirm-enabled', typedConfirmation.enabled ? '1' : '0' );
		option.setAttribute( 'data-typed-confirm-threshold', typedConfirmation.threshold ? String( typedConfirmation.threshold ) : '' );
	}

	function getOptionAction( option ) {
		var message = option.getAttribute( 'data-confirm-message' ) || option.getAttribute( 'data-confirm' ) || '';
		var enabled = option.getAttribute( 'data-confirm-enabled' );

		return {
			key: option.value,
			label: option.textContent,
			confirmation: normalizeConfirmation( {
				enabled: null === enabled ? '' !== message : toBool( enabled ),
				title: option.getAttribute( 'data-confirm-title' ) || '',
				titleSingular: option.getAttribute( 'data-confirm-title-singular' ) || '',
				message: message,
				messageSingular: option.getAttribute( 'data-confirm-message-singular' ) || '',
				actionLabel: option.getAttribute( 'data-confirm-action-label' ) || '',
				actionLabelSingular: option.getAttribute( 'data-confirm-action-label-singular' ) || ''
			} ),
			typedConfirmation: normalizeTypedConfirmation( {
				enabled: toBool( option.getAttribute( 'data-typed-confirm-enabled' ) ),
				threshold: option.getAttribute( 'data-typed-confirm-threshold' ) || ''
			} ),
			frontendData: parseJsonAttribute( option, 'data-action-data' )
		};
	}

	function syncActionOptions( select, actions ) {
		var actionKeys = Object.keys( actions );
		var selectionRemoved = false;

		toArray( select.options ).forEach( function ( option ) {
			if ( option.value && -1 === actionKeys.indexOf( option.value ) ) {
				selectionRemoved = selectionRemoved || option.selected;
				option.remove();
			}
		} );

		Object.keys( actions ).forEach( function ( key ) {
			var action = actions[ key ] || {};
			var option = findOption( select, key );
			var confirmation = normalizeConfirmation( action.confirmation || {} );
			var typedConfirmation = normalizeTypedConfirmation( action.typedConfirmation || {} );

			if ( !option ) {
				option = document.createElement( 'option' );
				option.value = key;
				select.appendChild( option );
			}

			option.textContent = action.label || key;
			updateOptionConfirmation( option, confirmation );
			updateOptionTypedConfirmation( option, typedConfirmation );
			if ( action.frontendData ) {
				option.setAttribute( 'data-action-data', JSON.stringify( action.frontendData ) );
			}
		} );

		if ( selectionRemoved ) {
			select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		}
	}

	function getActions( select, form ) {
		var actions = {};

		toArray( select.options ).forEach( function ( option ) {
			if ( !option.value ) {
				return;
			}

			actions[ option.value ] = getOptionAction( option );
		} );

		actions = applyFilters( 'gk.gravityview.bulkActions.actions', actions, form ) || actions;
		syncActionOptions( select, actions );

		return actions;
	}

	function showTypedConfirmation( confirmation, context ) {
		var isDelete = 'delete' === context.actionKey;
		var expected = isDelete ? strings.bulk_actions_delete_confirm_word : strings.bulk_actions_typed_confirm_word;
		var modal = document.createElement( 'div' );
		var dialog = document.createElement( 'div' );
		var title = document.createElement( 'h2' );
		var message = document.createElement( 'p' );
		var label = document.createElement( 'label' );
		var input = document.createElement( 'input' );
		var actions = document.createElement( 'div' );
		var confirmButton = document.createElement( 'button' );
		var cancelButton = document.createElement( 'button' );
		var previousFocus = document.activeElement;
		var titleId = uniqueId( 'gv-bulk-actions-confirm-title-' );
		var messageId = uniqueId( 'gv-bulk-actions-confirm-message-' );

		modal.className = 'gv-bulk-actions-confirmation-modal';
		dialog.className = 'gv-bulk-actions-confirmation-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-labelledby', titleId );
		dialog.setAttribute( 'aria-describedby', messageId );
		title.id = titleId;
		title.textContent = confirmation.title || ( isDelete ? strings.bulk_actions_delete_confirm_title : strings.bulk_actions_confirm_title );
		message.id = messageId;
		message.textContent = ( isDelete ? ( 1 === context.selectedCount ? strings.bulk_actions_delete_confirm_message_singular : strings.bulk_actions_delete_confirm_message ) : strings.bulk_actions_typed_confirm_message )
			.replace( '[count]', context.selectedCount )
			.replace( '[word]', expected );
		label.className = 'gv-bulk-actions-confirmation-label';
		label.textContent = ( isDelete ? strings.bulk_actions_delete_confirm_label : strings.bulk_actions_typed_confirm_label ).replace( '[word]', expected );
		input.type = 'text';
		input.autocomplete = 'off';
		input.spellcheck = false;
		actions.className = 'gv-bulk-actions-confirmation-actions';
		confirmButton.type = 'button';
		confirmButton.className = 'gv-bulk-actions-confirmation-confirm';
		confirmButton.disabled = true;
		confirmButton.textContent = confirmation.actionLabel || ( isDelete ? strings.bulk_actions_delete_confirm_button : strings.bulk_actions_typed_confirm_button );
		cancelButton.type = 'button';
		cancelButton.className = 'gv-bulk-actions-confirmation-cancel';
		cancelButton.textContent = strings.bulk_actions_cancel;
		label.appendChild( input );
		actions.appendChild( cancelButton );
		actions.appendChild( confirmButton );
		dialog.appendChild( title );
		dialog.appendChild( message );
		dialog.appendChild( label );
		dialog.appendChild( actions );
		modal.appendChild( dialog );

		return new Promise( function ( resolve ) {
			function cleanup( confirmed ) {
				document.removeEventListener( 'keydown', onKeydown, true );
				modal.remove();
				unlockBodyScroll();

				if ( previousFocus && 'function' === typeof previousFocus.focus ) {
					previousFocus.focus();
				}

				resolve( confirmed );
			}

			function onKeydown( event ) {
				var focusable;
				var first;
				var last;

				if ( 'Escape' === event.key ) {
					event.preventDefault();
					cleanup( false );
					return;
				}

				if ( 'Tab' !== event.key ) {
					return;
				}

				focusable = toArray( dialog.querySelectorAll( 'button, input' ) ).filter( function ( element ) {
					return !element.disabled;
				} );
				first = focusable[0];
				last = focusable[ focusable.length - 1 ];

				if ( first && !dialog.contains( document.activeElement ) ) {
					event.preventDefault();
					first.focus();
					return;
				}

				if ( event.shiftKey && document.activeElement === first ) {
					event.preventDefault();
					last.focus();
				} else if ( !event.shiftKey && document.activeElement === last ) {
					event.preventDefault();
					first.focus();
				}
			}

			input.addEventListener( 'input', function () {
				confirmButton.disabled = expected !== input.value;
			} );
			confirmButton.addEventListener( 'click', function () {
				cleanup( true );
			} );
			cancelButton.addEventListener( 'click', function () {
				cleanup( false );
			} );
			modal.addEventListener( 'click', function ( event ) {
				if ( event.target === modal ) {
					cleanup( false );
				}
			} );
			document.addEventListener( 'keydown', onKeydown, true );
			lockBodyScroll();
			document.body.appendChild( modal );
			input.focus();
		} );
	}

	function removeActionInputs( form, actionKey ) {
		toArray( form.querySelectorAll( '[data-gv-bulk-action-input="' + actionKey + '"]' ) ).forEach( function ( input ) {
			input.remove();
		} );
	}

	function appendActionInput( form, actionKey, name, value ) {
		var input = document.createElement( 'input' );

		if ( Array.isArray( value ) ) {
			value.forEach( function ( item ) {
				appendActionInput( form, actionKey, name + '[]', item );
			} );
			return;
		}

		input.type = 'hidden';
		input.name = 'gv_bulk_action_input[' + actionKey + ']' + name;
		input.value = value;
		input.setAttribute( 'data-gv-bulk-action-input', actionKey );
		form.appendChild( input );
	}

	function getBulkEditFields( context ) {
		var data = context.action && context.action.frontendData ? context.action.frontendData : {};

		return Array.isArray( data.fields ) ? data.fields : [];
	}

	function getResendNotificationsData( context ) {
		var data = context.action && context.action.frontendData ? context.action.frontendData : {};
		var notifications = Array.isArray( data.notifications ) ? data.notifications : [];
		var groups = Array.isArray( data.groups ) ? data.groups : [];

		return {
			notifications: notifications,
			groups: groups,
			sendToOverrideAllowed: toBool( data.sendToOverrideAllowed )
		};
	}

	function shouldSkipResendNotificationsPicker( context ) {
		var data = getResendNotificationsData( context );

		return 1 === data.notifications.length && !data.sendToOverrideAllowed;
	}

	function writeResendNotificationsInput( context, notifications, sendTo ) {
		removeActionInputs( context.form, context.actionKey );
		appendActionInput( context.form, context.actionKey, '[notifications]', notifications );
		appendActionInput( context.form, context.actionKey, '[send_to]', sendTo || '' );
	}

	function showResendNotificationsDialog( context ) {
		var data = getResendNotificationsData( context );
		var actionKey = context.actionKey;
		var modal = document.createElement( 'div' );
		var dialog = document.createElement( 'div' );
		var title = document.createElement( 'h2' );
		var message = document.createElement( 'p' );
		var error = document.createElement( 'div' );
		var groups = document.createElement( 'div' );
		var sendToLabel = document.createElement( 'label' );
		var sendToInput = document.createElement( 'input' );
		var sendToHelp = document.createElement( 'span' );
		var actions = document.createElement( 'div' );
		var confirmButton = document.createElement( 'button' );
		var cancelButton = document.createElement( 'button' );
		var previousFocus = document.activeElement;
		var settled = false;
		var titleId = uniqueId( 'gv-bulk-actions-resend-title-' );
		var messageId = uniqueId( 'gv-bulk-actions-resend-message-' );
		var errorId = uniqueId( 'gv-bulk-actions-resend-error-' );
		var sendToHelpId = uniqueId( 'gv-bulk-actions-resend-send-to-help-' );
		var resolveDialog = function () {};

		removeActionInputs( context.form, actionKey );

		function setError( text, target ) {
			error.textContent = text || '';
			error.hidden = !text;

			toArray( dialog.querySelectorAll( '[aria-invalid="true"]' ) ).forEach( function ( control ) {
				control.removeAttribute( 'aria-invalid' );
				control.removeAttribute( 'aria-errormessage' );
			} );

			if ( text ) {
				if ( target ) {
					target.setAttribute( 'aria-invalid', 'true' );
					target.setAttribute( 'aria-errormessage', errorId );
					target.focus();
				} else {
					error.focus();
				}
			}
		}

		function getCheckedTokens() {
			return toArray( groups.querySelectorAll( 'input[type="checkbox"]:checked' ) ).map( function ( input ) {
				return input.value;
			} );
		}

		function collectInput() {
			var tokens = getCheckedTokens();
			var firstCheckbox = groups.querySelector( 'input[type="checkbox"]' );
			var sendTo = data.sendToOverrideAllowed ? sendToInput.value.trim() : '';

			if ( !tokens.length ) {
				setError( strings.bulk_actions_resend_choose_notification, firstCheckbox );
				return null;
			}

			if ( sendTo && 'function' === typeof sendToInput.checkValidity && !sendToInput.checkValidity() ) {
				setError( sendToInput.validationMessage || strings.bulk_actions_resend_send_to_invalid, sendToInput );
				return null;
			}

			return {
				notifications: tokens,
				sendTo: sendTo
			};
		}

		function applyFieldErrors( fieldErrors ) {
			if ( !fieldErrors || 'object' !== typeof fieldErrors ) {
				return false;
			}

			if ( fieldErrors.send_to ) {
				setError( fieldErrors.send_to, sendToInput );
				return true;
			}

			if ( fieldErrors.notifications ) {
				setError( fieldErrors.notifications, groups.querySelector( 'input[type="checkbox"]' ) );
				return true;
			}

			return false;
		}

		function cleanup( confirmed ) {
			document.removeEventListener( 'keydown', onKeydown, true );
			modal.remove();
			unlockBodyScroll();

			if ( previousFocus && 'function' === typeof previousFocus.focus ) {
				previousFocus.focus();
			}

			return confirmed;
		}

		function finishDialog( confirmed ) {
			if ( settled ) {
				return;
			}

			settled = true;
			resolveDialog( cleanup( confirmed ) );
		}

		function onKeydown( event ) {
			var focusable;
			var first;
			var last;

			if ( 'Escape' === event.key ) {
				event.preventDefault();
				finishDialog( false );
				return;
			}

			if ( 'Tab' !== event.key ) {
				return;
			}

			focusable = toArray( dialog.querySelectorAll( 'button, input, select, textarea, [tabindex]' ) ).filter( function ( element ) {
				return !element.disabled && !element.hidden && -1 !== element.tabIndex;
			} );
			first = focusable[0];
			last = focusable[ focusable.length - 1 ];

			if ( first && !dialog.contains( document.activeElement ) ) {
				event.preventDefault();
				first.focus();
				return;
			}

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( !event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}

		function renderGroups() {
			var showFormHeadings = data.groups.length > 1;

			groups.className = 'gv-bulk-actions-resend-groups';
			groups.setAttribute( 'aria-label', strings.bulk_actions_resend_notification_label );

			data.groups.forEach( function ( group ) {
				var groupWrap = document.createElement( 'div' );
				var heading = document.createElement( 'h3' );
				var options = document.createElement( 'div' );

				groupWrap.className = 'gv-bulk-actions-resend-group';
				options.className = 'gv-bulk-actions-resend-options';

				if ( showFormHeadings ) {
					heading.className = 'gv-bulk-actions-resend-group-title';
					heading.textContent = group.label || '';
					groupWrap.appendChild( heading );
				}

				( group.notifications || [] ).forEach( function ( notification ) {
					var label = document.createElement( 'label' );
					var checkbox = document.createElement( 'input' );
					var text = document.createElement( 'span' );

					checkbox.type = 'checkbox';
					checkbox.value = notification.token || '';
					checkbox.addEventListener( 'change', function () {
						setError( '' );
					} );
					text.textContent = notification.label || notification.id || notification.token || '';
					label.className = 'gv-bulk-actions-resend-option';
					label.appendChild( checkbox );
					label.appendChild( text );
					options.appendChild( label );
				} );

				groupWrap.appendChild( options );
				groups.appendChild( groupWrap );
			} );
		}

		modal.className = 'gv-bulk-actions-confirmation-modal gv-bulk-actions-resend-modal';
		dialog.className = 'gv-bulk-actions-confirmation-dialog gv-bulk-actions-resend-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-labelledby', titleId );
		dialog.setAttribute( 'aria-describedby', messageId + ' ' + errorId );
		title.id = titleId;
		title.textContent = strings.bulk_actions_resend_title;
		message.id = messageId;
		message.textContent = ( 1 === context.selectedCount ? strings.bulk_actions_resend_message_singular : strings.bulk_actions_resend_message ).replace( '[count]', context.selectedCount );
		error.id = errorId;
		error.className = 'gv-bulk-actions-edit-error';
		error.setAttribute( 'role', 'alert' );
		error.tabIndex = -1;
		error.hidden = true;
		renderGroups();

		sendToLabel.className = 'gv-bulk-actions-confirmation-label gv-bulk-actions-resend-send-to';
		sendToLabel.textContent = strings.bulk_actions_resend_send_to_label;
		sendToInput.type = 'email';
		sendToInput.autocomplete = 'email';
		sendToInput.setAttribute( 'aria-describedby', sendToHelpId );
		sendToInput.addEventListener( 'input', function () {
			sendToInput.setCustomValidity( '' );
			setError( '' );
		} );
		sendToHelp.id = sendToHelpId;
		sendToHelp.className = 'gv-bulk-actions-resend-help';
		sendToHelp.textContent = strings.bulk_actions_resend_send_to_help;
		sendToLabel.appendChild( sendToInput );
		sendToLabel.appendChild( sendToHelp );

		actions.className = 'gv-bulk-actions-confirmation-actions';
		confirmButton.type = 'button';
		confirmButton.className = 'gv-bulk-actions-confirmation-confirm';
		confirmButton.textContent = strings.bulk_actions_resend_confirm_button;
		cancelButton.type = 'button';
		cancelButton.className = 'gv-bulk-actions-confirmation-cancel';
		cancelButton.textContent = strings.bulk_actions_cancel;

		confirmButton.addEventListener( 'click', function () {
			var input = collectInput();
			var originalText = confirmButton.textContent;

			if ( !input ) {
				return;
			}

			writeResendNotificationsInput( context, input.notifications, input.sendTo );
			confirmButton.disabled = true;
			setButtonBusy( confirmButton, true, strings.bulk_actions_validating );
			dialog.setAttribute( 'aria-busy', 'true' );

			validateActionInput( context ).then( function () {
				finishDialog( true );
			} ).catch( function ( error ) {
				removeActionInputs( context.form, actionKey );
				confirmButton.disabled = false;
				setButtonBusy( confirmButton, false, originalText );
				dialog.removeAttribute( 'aria-busy' );

				if ( error && applyFieldErrors( error.fieldErrors ) ) {
					return;
				}

				setError( error && error.message ? error.message : strings.bulk_actions_validate_error );
			} );
		} );
		cancelButton.addEventListener( 'click', function () {
			finishDialog( false );
		} );
		modal.addEventListener( 'click', function ( event ) {
			if ( event.target === modal ) {
				finishDialog( false );
			}
		} );

		dialog.appendChild( title );
		dialog.appendChild( message );
		dialog.appendChild( error );
		dialog.appendChild( groups );

		if ( data.sendToOverrideAllowed ) {
			dialog.appendChild( sendToLabel );
		}

		actions.appendChild( cancelButton );
		actions.appendChild( confirmButton );
		dialog.appendChild( actions );
		modal.appendChild( dialog );

		return new Promise( function ( resolve ) {
			resolveDialog = resolve;

			if ( !data.notifications.length ) {
				setError( strings.bulk_actions_resend_no_notifications );
				confirmButton.disabled = true;
			}

			document.addEventListener( 'keydown', onKeydown, true );
			lockBodyScroll();
			document.body.appendChild( modal );

			( dialog.querySelector( 'input[type="checkbox"], input[type="email"], button' ) || dialog ).focus();
		} );
	}

	function validateActionInput( context ) {
		var formData;
		var request;
		var timeout;
		var controller;

		if ( !strings.bulk_actions_ajax_url || !window.fetch || !context || !context.form ) {
			return Promise.reject( new Error( strings.bulk_actions_validate_error ) );
		}

		formData = new window.FormData( context.form );
		formData.set( 'action', strings.bulk_actions_validate_action );
		formData.set( 'gv_bulk_action', context.actionKey || '' );
		formData.set( 'gv_bulk_view_id', context.form.getAttribute( 'data-view-id' ) || '' );
		formData.set( 'gv_bulk_render_instance', context.form.getAttribute( 'data-render-instance' ) || '' );

		request = {
			method: 'POST',
			credentials: 'same-origin',
			body: formData
		};

		if ( window.AbortController ) {
			controller = new window.AbortController();
			request.signal = controller.signal;
			timeout = window.setTimeout( function () {
				controller.abort();
			}, 15000 );
		}

		return window.fetch( strings.bulk_actions_ajax_url, request ).then( function ( response ) {
			return response.json().then( function ( data ) {
				return {
					data: data,
					status: response.status
				};
			} );
		} ).then( function ( response ) {
			var payload;
			var message;
			var error;

			if ( response.data && response.data.success ) {
				return true;
			}

			payload = response.data && response.data.data ? response.data.data : {};
			message = payload.message ? payload.message : strings.bulk_actions_validate_error;
			error = new Error( message );
			error.status = response.status;
			error.fieldErrors = payload.field_errors && 'object' === typeof payload.field_errors ? payload.field_errors : {};

			throw error;
		} ).catch( function ( error ) {
			if ( error && 'AbortError' === error.name ) {
				throw new Error( strings.bulk_actions_validate_network_error );
			}

			if ( error && error.status ) {
				throw error;
			}

			throw new Error( strings.bulk_actions_validate_network_error );
		} ).then( function ( result ) {
			if ( timeout ) {
				window.clearTimeout( timeout );
			}

			return result;
		}, function ( error ) {
			if ( timeout ) {
				window.clearTimeout( timeout );
			}

			throw error;
		} );
	}

	function setButtonBusy( button, busy, label ) {
		var spinner;

		if ( !button ) {
			return;
		}

		button.textContent = '';
		button.setAttribute( 'aria-busy', busy ? 'true' : 'false' );

		if ( busy ) {
			spinner = document.createElement( 'span' );
			spinner.className = 'gv-bulk-actions-spinner';
			spinner.setAttribute( 'aria-hidden', 'true' );
			button.appendChild( spinner );
			button.appendChild( document.createTextNode( ' ' ) );
		}

		button.appendChild( document.createTextNode( label ) );
	}

	function createBulkEditValueControl( field ) {
		var type = field.type || 'text';
		var control;

		function getValidation() {
			return field && field.validation && 'object' === typeof field.validation ? field.validation : {};
		}

		function hasValidationValue( validation, key ) {
			return Object.prototype.hasOwnProperty.call( validation, key ) && null !== validation[ key ] && undefined !== validation[ key ] && '' !== String( validation[ key ] );
		}

		function setValidationAttribute( target, validation, key, attribute ) {
			if ( hasValidationValue( validation, key ) ) {
				target.setAttribute( attribute || key, validation[ key ] );
			}
		}

		function applyValidationAttributes( target ) {
			var validation = getValidation();

			setValidationAttribute( target, validation, 'maxlength' );
			setValidationAttribute( target, validation, 'min' );
			setValidationAttribute( target, validation, 'max' );

			if ( hasValidationValue( validation, 'step' ) ) {
				target.setAttribute( 'step', validation.step );
			} else if ( 'datetime-local' === type ) {
				target.setAttribute( 'step', '1' );
			}

			if ( 'INPUT' === target.tagName && 'text' === target.type ) {
				setValidationAttribute( target, validation, 'pattern' );
			}
		}

		if ( 'textarea' === type ) {
			control = document.createElement( 'textarea' );
			control.rows = 4;
			applyValidationAttributes( control );
			return control;
		}

		if ( 'select' === type || 'radio' === type ) {
			control = document.createElement( 'select' );
			control.appendChild( new Option( strings.bulk_actions_edit_choose_value, '' ) );
			( field.choices || [] ).forEach( function ( choice ) {
				control.appendChild( new Option( choice.label || choice.value, choice.value ) );
			} );
			return control;
		}

		if ( 'multiselect' === type ) {
			control = document.createElement( 'select' );
			control.multiple = true;
			control.size = Math.min( Math.max( ( field.choices || [] ).length, 2 ), 6 );
			( field.choices || [] ).forEach( function ( choice ) {
				control.appendChild( new Option( choice.label || choice.value, choice.value ) );
			} );
			return control;
		}

		control = document.createElement( 'input' );
		control.type = 'number' === type ? 'number' : ( 'email' === type ? 'email' : ( 'website' === type ? 'url' : ( 'datetime-local' === type ? 'datetime-local' : 'text' ) ) );

		applyValidationAttributes( control );

		return control;
	}

	function showBulkEditDialog( context ) {
		var fields = getBulkEditFields( context );
		var actionKey = context.actionKey;
		var modal = document.createElement( 'div' );
		var dialog = document.createElement( 'div' );
		var title = document.createElement( 'h2' );
		var message = document.createElement( 'p' );
		var error = document.createElement( 'div' );
		var rows = document.createElement( 'div' );
		var addButton = document.createElement( 'button' );
		var review = document.createElement( 'div' );
		var reviewWarning = document.createElement( 'p' );
		var reviewList = document.createElement( 'ul' );
		var reviewLabel = document.createElement( 'label' );
		var reviewCheckbox = document.createElement( 'input' );
		var reviewError = document.createElement( 'p' );
		var actions = document.createElement( 'div' );
		var confirmButton = document.createElement( 'button' );
		var cancelButton = document.createElement( 'button' );
		var previousFocus = document.activeElement;
		var settled = false;
		var titleId = uniqueId( 'gv-bulk-actions-edit-title-' );
		var messageId = uniqueId( 'gv-bulk-actions-edit-message-' );
		var errorId = uniqueId( 'gv-bulk-actions-edit-error-' );
		var reviewWarningId = uniqueId( 'gv-bulk-actions-edit-review-warning-' );
		var reviewListId = uniqueId( 'gv-bulk-actions-edit-review-list-' );
		var reviewErrorId = uniqueId( 'gv-bulk-actions-edit-review-error-' );
		var acknowledgedSignature = '';

		removeActionInputs( context.form, actionKey );

		function setError( text ) {
			error.textContent = text || '';
			error.hidden = !text;
			if ( text ) {
				error.focus();
			}
		}

		function getRowError( row ) {
			return row.querySelector( '[data-bulk-edit-row-error]' );
		}

		function getRowErrorTarget( row ) {
			return row.querySelector( '[data-bulk-edit-value]' ) || row.querySelector( '[data-bulk-edit-operation]' ) || row.querySelector( '[data-bulk-edit-field]' );
		}

		function setRowError( row, text, target ) {
			var rowError = getRowError( row );
			var controls = row.querySelectorAll( 'select, input, textarea' );

			if ( !rowError ) {
				return;
			}

			rowError.textContent = text || '';
			rowError.hidden = !text;
			row.classList.toggle( 'gv-bulk-actions-edit-row-has-error', !!text );

			toArray( controls ).forEach( function ( control ) {
				control.removeAttribute( 'aria-invalid' );
				control.removeAttribute( 'aria-errormessage' );
			} );

			if ( text ) {
				target = target || getRowErrorTarget( row );

				if ( target ) {
					target.setAttribute( 'aria-invalid', 'true' );
					target.setAttribute( 'aria-errormessage', rowError.id );
				}
			}
		}

		function clearRowErrors() {
			toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).forEach( function ( row ) {
				setRowError( row, '' );
			} );
		}

		function focusRow( row ) {
			var control = row ? row.querySelector( '[aria-invalid="true"], [data-bulk-edit-value], [data-bulk-edit-field]' ) : null;

			if ( control && 'function' === typeof control.focus ) {
				control.focus();
			}
		}

		function fieldById( fieldId ) {
			var matches = fields.filter( function ( field ) {
				return String( field.id ) === String( fieldId );
			} );

			return matches.length ? matches[0] : null;
		}

		function getChangesSignature( changes ) {
			return JSON.stringify( changes.map( function ( change ) {
				return {
					field_id: String( change.field_id ),
					operation: String( change.operation ),
					value: change.value
				};
			} ) );
		}

		function resetReview() {
			var changes;
			var signature;

			if ( review.hidden ) {
				return;
			}

			changes = collectChanges( true );
			signature = changes ? getChangesSignature( changes ) : '';

			if ( acknowledgedSignature === signature ) {
				return;
			}

			acknowledgedSignature = '';
			reviewCheckbox.checked = false;
			setReviewError( '' );
			reviewCheckbox.removeAttribute( 'aria-describedby' );
			reviewCheckbox.removeAttribute( 'aria-required' );
			review.hidden = true;
		}

		function getSelectedFieldIds( excludeRow ) {
			var selected = {};

			toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).forEach( function ( row ) {
				var select = row.querySelector( '[data-bulk-edit-field]' );
				var value = select ? select.value : '';

				if ( row !== excludeRow && value ) {
					selected[ value ] = true;
				}
			} );

			return selected;
		}

		function updateFieldOptions() {
			toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).forEach( function ( row ) {
				var fieldSelect = row.querySelector( '[data-bulk-edit-field]' );
				var selected = getSelectedFieldIds( row );

				if ( !fieldSelect ) {
					return;
				}

				toArray( fieldSelect.options ).forEach( function ( option ) {
					var field = option.value ? fieldById( option.value ) : null;
					var label = field && field.label ? field.label : option.textContent.replace( ' (' + strings.bulk_actions_edit_already_chosen + ')', '' );
					var disabled = !!( option.value && selected[ option.value ] );

					option.disabled = disabled;
					option.textContent = disabled ? label + ' (' + strings.bulk_actions_edit_already_chosen + ')' : label;
				} );
			} );

			addButton.disabled = toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).length >= fields.length;
		}

		function getChangeSummary( change ) {
			var field = fieldById( change.field_id );
			var label = field && field.label ? field.label : change.field_id;

			if ( 'clear' === change.operation ) {
				return label + ': ' + strings.bulk_actions_edit_clear_value;
			}

			return label + ': ' + getValueSummary( field, change.value );
		}

		function getValueSummary( field, value ) {
			var choiceLabels = {};

			if ( !field || !Array.isArray( field.choices ) ) {
				return Array.isArray( value ) ? value.join( ', ' ) : value;
			}

			field.choices.forEach( function ( choice ) {
				choiceLabels[ String( choice.value ) ] = choice.label || choice.value;
			} );

			if ( Array.isArray( value ) ) {
				return value.map( function ( item ) {
					return choiceLabels[ String( item ) ] || item;
				} ).join( ', ' );
			}

			return choiceLabels[ String( value ) ] || value;
		}

		function getControlValue( control ) {
			if ( control && control.multiple && control.options ) {
				return toArray( control.options ).filter( function ( option ) {
					return option.selected;
				} ).map( function ( option ) {
					return option.value;
				} );
			}

			return control ? control.value : '';
		}

		function isEmptyValue( value ) {
			if ( Array.isArray( value ) ) {
				return !value.length;
			}

			return !value;
		}

		function getValidationMessage( field, control ) {
			var validation = field && field.validation && 'object' === typeof field.validation ? field.validation : {};
			var customMessage = '';

			if ( validation.validation_message && 'string' === typeof validation.validation_message ) {
				customMessage = validation.validation_message;
			} else if ( field && field.validation_message && 'string' === typeof field.validation_message ) {
				customMessage = field.validation_message;
			}

			return customMessage || ( control && control.validationMessage ? control.validationMessage : strings.bulk_actions_validate_error );
		}

		function updateOperationOptions( row ) {
			var field = fieldById( row.querySelector( '[data-bulk-edit-field]' ).value );
			var operationSelect = row.querySelector( '[data-bulk-edit-operation]' );
			var clearOption = toArray( operationSelect.options ).filter( function ( option ) {
				return 'clear' === option.value;
			} )[0];

			if ( clearOption ) {
				clearOption.disabled = !!( field && field.required );
			}

			if ( field && field.required && 'clear' === operationSelect.value ) {
				operationSelect.value = 'set';
			}
		}

		function updateRowValueControl( row ) {
			updateOperationOptions( row );

			var field = fieldById( row.querySelector( '[data-bulk-edit-field]' ).value );
			var operation = row.querySelector( '[data-bulk-edit-operation]' ).value;
			var valueWrap = row.querySelector( '[data-bulk-edit-value-wrap]' );
			var oldControl = row.querySelector( '[data-bulk-edit-value]' );
			var control;

			if ( oldControl ) {
				oldControl.remove();
			}

			if ( !field ) {
				valueWrap.hidden = true;
				return;
			}

			control = createBulkEditValueControl( field );
			control.setAttribute( 'data-bulk-edit-value', '1' );
			control.setAttribute( 'aria-describedby', getRowError( row ).id );
			control.disabled = 'clear' === operation;
			control.addEventListener( 'input', function () {
				setError( '' );
				control.setCustomValidity( '' );
				setRowError( row, '' );
				resetReview();
			} );
			control.addEventListener( 'change', function () {
				setError( '' );
				control.setCustomValidity( '' );
				setRowError( row, '' );
				resetReview();
			} );
			control.addEventListener( 'invalid', function ( event ) {
				event.preventDefault();
			} );
			valueWrap.hidden = 'clear' === operation;
			valueWrap.appendChild( control );
		}

		function addRow() {
			var row = document.createElement( 'div' );
			var rowId = uniqueId( 'gv-bulk-actions-edit-row-error-' );
			var fieldLabel = document.createElement( 'label' );
			var fieldSelect = document.createElement( 'select' );
			var operationLabel = document.createElement( 'label' );
			var operationSelect = document.createElement( 'select' );
			var valueLabel = document.createElement( 'label' );
			var rowError = document.createElement( 'p' );
			var removeButton = document.createElement( 'button' );

			row.className = 'gv-bulk-actions-edit-row';
			fieldSelect.setAttribute( 'data-bulk-edit-field', '1' );
			fieldSelect.setAttribute( 'aria-describedby', rowId );
			fieldSelect.appendChild( new Option( strings.bulk_actions_edit_choose_field, '' ) );
			fields.forEach( function ( field ) {
				fieldSelect.appendChild( new Option( field.label, field.id ) );
			} );
			fieldLabel.textContent = strings.bulk_actions_edit_field_label;
			fieldLabel.appendChild( fieldSelect );

			operationSelect.setAttribute( 'data-bulk-edit-operation', '1' );
			operationSelect.setAttribute( 'aria-describedby', rowId );
			operationSelect.appendChild( new Option( strings.bulk_actions_edit_set_value, 'set' ) );
			operationSelect.appendChild( new Option( strings.bulk_actions_edit_clear_value, 'clear' ) );
			operationLabel.textContent = strings.bulk_actions_edit_operation_label;
			operationLabel.appendChild( operationSelect );

			valueLabel.setAttribute( 'data-bulk-edit-value-wrap', '1' );
			valueLabel.textContent = strings.bulk_actions_edit_value_label;
			rowError.id = rowId;
			rowError.className = 'gv-bulk-actions-edit-row-error';
			rowError.setAttribute( 'data-bulk-edit-row-error', '1' );
			rowError.setAttribute( 'role', 'alert' );
			rowError.hidden = true;

			removeButton.type = 'button';
			removeButton.className = 'gv-bulk-actions-edit-remove';
			removeButton.textContent = strings.bulk_actions_edit_remove_field;
			removeButton.addEventListener( 'click', function () {
				row.remove();
				updateFieldOptions();
				resetReview();
			} );

			fieldSelect.addEventListener( 'change', function () {
				setError( '' );
				setRowError( row, '' );
				resetReview();
				updateRowValueControl( row );
				updateFieldOptions();
			} );
			operationSelect.addEventListener( 'change', function () {
				setError( '' );
				setRowError( row, '' );
				resetReview();
				updateRowValueControl( row );
			} );

			row.appendChild( fieldLabel );
			row.appendChild( operationLabel );
			row.appendChild( valueLabel );
			row.appendChild( rowError );
			row.appendChild( removeButton );
			rows.appendChild( row );
			updateRowValueControl( row );
			updateFieldOptions();
			fieldSelect.focus();
		}

		function collectChanges( skipErrors ) {
			var used = {};
			var changes = [];
			var firstErrorRow = null;
			var fieldControl;
			var operationControl;
			var valueControl;

			if ( !skipErrors ) {
				setError( '' );
				clearRowErrors();
			}
			toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).forEach( function ( row ) {
				fieldControl = row.querySelector( '[data-bulk-edit-field]' );
				operationControl = row.querySelector( '[data-bulk-edit-operation]' );
				valueControl = row.querySelector( '[data-bulk-edit-value]' );

				var fieldId = fieldControl.value;
				var operation = operationControl.value;
				var value = getControlValue( valueControl );
				var field = fieldById( fieldId );

				if ( !fieldId ) {
					if ( !skipErrors ) {
						setRowError( row, strings.bulk_actions_edit_choose_field, fieldControl );
					}
					firstErrorRow = firstErrorRow || row;
					return;
				}

				if ( used[ fieldId ] ) {
					if ( !skipErrors ) {
						setRowError( row, strings.bulk_actions_edit_duplicate_field, fieldControl );
					}
					firstErrorRow = firstErrorRow || row;
					return;
				}

				if ( 'clear' === operation && field && field.required ) {
					if ( !skipErrors ) {
						setRowError( row, strings.bulk_actions_edit_required_clear, operationControl );
					}
					firstErrorRow = firstErrorRow || row;
					return;
				}

				if ( 'set' === operation && isEmptyValue( value ) ) {
					if ( !skipErrors ) {
						setRowError( row, strings.bulk_actions_edit_value_required, valueControl );
					}
					firstErrorRow = firstErrorRow || row;
					return;
				}

				if ( 'set' === operation && valueControl && 'function' === typeof valueControl.checkValidity ) {
					valueControl.setCustomValidity( '' );

					if ( !valueControl.checkValidity() ) {
						if ( !skipErrors ) {
							setRowError( row, getValidationMessage( field, valueControl ), valueControl );
						}
						firstErrorRow = firstErrorRow || row;
						return;
					}
				}

				used[ fieldId ] = true;
				changes.push( {
					field_id: fieldId,
					operation: operation,
					value: 'clear' === operation ? '' : value
				} );
			} );

			if ( firstErrorRow && !skipErrors ) {
				focusRow( firstErrorRow );
				return null;
			}

			return changes;
		}

		function applyFieldErrors( fieldErrors ) {
			var firstErrorRow = null;
			var unmatched = [];

			if ( !fieldErrors || 'object' !== typeof fieldErrors ) {
				return false;
			}

			clearRowErrors();

			Object.keys( fieldErrors ).forEach( function ( fieldId ) {
				var message = fieldErrors[ fieldId ];
				var matched = false;

				toArray( rows.querySelectorAll( '.gv-bulk-actions-edit-row' ) ).forEach( function ( row ) {
					var fieldSelect = row.querySelector( '[data-bulk-edit-field]' );

					if ( fieldSelect && String( fieldSelect.value ) === String( fieldId ) ) {
						setRowError( row, message, row.querySelector( '[data-bulk-edit-value]' ) );
						firstErrorRow = firstErrorRow || row;
						matched = true;
					}
				} );

				if ( !matched && message ) {
					unmatched.push( message );
				}
			} );

			if ( firstErrorRow ) {
				focusRow( firstErrorRow );
			}

			if ( unmatched.length ) {
				setError( unmatched.join( ' ' ) );
			}

			return !!firstErrorRow || !!unmatched.length;
		}

		function getReviewMessages( changes ) {
			var messages = [];
			var threshold = context.action && context.action.typedConfirmation ? context.action.typedConfirmation.threshold : 0;

			if ( changes.some( function ( change ) { return 'clear' === change.operation; } ) ) {
				messages.push( strings.bulk_actions_edit_clear_warning );
			}

			if ( threshold && context.selectedCount >= threshold ) {
				messages.push( strings.bulk_actions_edit_large_warning.replace( '[count]', context.selectedCount ) );
			}

			return messages;
		}

		function showReview( changes, messages ) {
			reviewList.innerHTML = '';
			changes.forEach( function ( change ) {
				var item = document.createElement( 'li' );

				item.textContent = getChangeSummary( change );
				reviewList.appendChild( item );
			} );

			review.setAttribute( 'data-change-signature', getChangesSignature( changes ) );
			reviewWarning.textContent = messages.concat( strings.bulk_actions_edit_review_required ).join( ' ' );
			review.hidden = false;
			setReviewError( '' );
			reviewCheckbox.setAttribute( 'aria-describedby', reviewWarningId + ' ' + reviewListId );
			reviewCheckbox.setAttribute( 'aria-required', 'true' );
			reviewCheckbox.focus();
		}

		function setReviewError( text ) {
			var hasError = !!text;
			var describedBy = reviewWarningId + ' ' + reviewListId;

			reviewError.textContent = text || '';
			reviewError.hidden = !hasError;
			review.classList.toggle( 'gv-bulk-actions-edit-review-has-error', hasError );

			if ( hasError ) {
				describedBy += ' ' + reviewErrorId;
				reviewCheckbox.setAttribute( 'aria-invalid', 'true' );
				reviewCheckbox.setAttribute( 'aria-errormessage', reviewErrorId );
				reviewCheckbox.focus();
			} else {
				reviewCheckbox.removeAttribute( 'aria-invalid' );
				reviewCheckbox.removeAttribute( 'aria-errormessage' );
			}

			if ( review.hidden && !hasError ) {
				reviewCheckbox.removeAttribute( 'aria-describedby' );
				return;
			}

			reviewCheckbox.setAttribute( 'aria-describedby', describedBy );
		}

		function writeChanges( changes ) {
			removeActionInputs( context.form, actionKey );
			changes.forEach( function ( change, index ) {
				appendActionInput( context.form, actionKey, '[changes][' + index + '][field_id]', change.field_id );
				appendActionInput( context.form, actionKey, '[changes][' + index + '][operation]', change.operation );
				appendActionInput( context.form, actionKey, '[changes][' + index + '][value]', change.value );
			} );
		}

		function cleanup( confirmed ) {
			document.removeEventListener( 'keydown', onKeydown, true );
			modal.remove();
			unlockBodyScroll();

			if ( previousFocus && 'function' === typeof previousFocus.focus ) {
				previousFocus.focus();
			}

			return confirmed;
		}

		function finishDialog( confirmed ) {
			if ( settled ) {
				return;
			}

			settled = true;
			resolveDialog( cleanup( confirmed ) );
		}

		function onKeydown( event ) {
			var focusable;
			var first;
			var last;

			if ( 'Escape' === event.key ) {
				event.preventDefault();
				finishDialog( false );
				return;
			}

			if ( 'Tab' !== event.key ) {
				return;
			}

			focusable = toArray( dialog.querySelectorAll( 'button, input, select, textarea, [tabindex]' ) ).filter( function ( element ) {
				return !element.disabled && !element.hidden && -1 !== element.tabIndex;
			} );
			first = focusable[0];
			last = focusable[ focusable.length - 1 ];

			if ( first && !dialog.contains( document.activeElement ) ) {
				event.preventDefault();
				first.focus();
				return;
			}

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( !event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		}

		var resolveDialog = function () {};

		modal.className = 'gv-bulk-actions-confirmation-modal gv-bulk-actions-edit-modal';
		dialog.className = 'gv-bulk-actions-confirmation-dialog gv-bulk-actions-edit-dialog';
		dialog.setAttribute( 'role', 'dialog' );
		dialog.setAttribute( 'aria-modal', 'true' );
		dialog.setAttribute( 'aria-labelledby', titleId );
		dialog.setAttribute( 'aria-describedby', messageId + ' ' + errorId );
		title.id = titleId;
		title.textContent = strings.bulk_actions_edit_title;
		message.id = messageId;
		message.textContent = ( 1 === context.selectedCount ? strings.bulk_actions_edit_message_singular : strings.bulk_actions_edit_message ).replace( '[count]', context.selectedCount );
		error.id = errorId;
		error.className = 'gv-bulk-actions-edit-error';
		error.setAttribute( 'role', 'alert' );
		error.tabIndex = -1;
		error.hidden = true;
		rows.className = 'gv-bulk-actions-edit-rows';
		review.className = 'gv-bulk-actions-edit-review';
		review.hidden = true;
		reviewWarning.id = reviewWarningId;
		reviewWarning.className = 'gv-bulk-actions-edit-review-warning';
		reviewList.id = reviewListId;
		reviewList.className = 'gv-bulk-actions-edit-review-list';
		reviewError.id = reviewErrorId;
		reviewError.className = 'gv-bulk-actions-edit-review-error';
		reviewError.setAttribute( 'role', 'alert' );
		reviewError.hidden = true;
		reviewCheckbox.type = 'checkbox';
		reviewCheckbox.addEventListener( 'change', function () {
			acknowledgedSignature = reviewCheckbox.checked ? review.getAttribute( 'data-change-signature' ) || '' : '';

			if ( reviewCheckbox.checked ) {
				setReviewError( '' );
			}
		} );
		reviewLabel.appendChild( reviewCheckbox );
		reviewLabel.appendChild( document.createTextNode( ' ' + strings.bulk_actions_edit_review_acknowledge ) );
		review.appendChild( reviewWarning );
		review.appendChild( reviewList );
		review.appendChild( reviewLabel );
		review.appendChild( reviewError );
		addButton.type = 'button';
		addButton.className = 'gv-bulk-actions-edit-add';
		addButton.textContent = strings.bulk_actions_edit_add_field;
		addButton.addEventListener( 'click', function () {
			resetReview();
			addRow();
		} );
		actions.className = 'gv-bulk-actions-confirmation-actions';
		confirmButton.type = 'button';
		confirmButton.className = 'gv-bulk-actions-confirmation-confirm';
		confirmButton.textContent = strings.bulk_actions_edit_update_entries;
		cancelButton.type = 'button';
		cancelButton.className = 'gv-bulk-actions-confirmation-cancel';
		cancelButton.textContent = strings.bulk_actions_cancel;
		confirmButton.addEventListener( 'click', function () {
			var changes;
			var originalText = confirmButton.textContent;
			var reviewMessages;
			var reviewSignature;

			changes = collectChanges();

			if ( null === changes ) {
				return;
			}

			if ( !changes.length ) {
				setError( strings.bulk_actions_edit_choose_field );
				return;
			}

			reviewMessages = getReviewMessages( changes );
			reviewSignature = getChangesSignature( changes );

			if ( reviewMessages.length && ( !reviewCheckbox.checked || acknowledgedSignature !== reviewSignature ) ) {
				if ( review.hidden || review.getAttribute( 'data-change-signature' ) !== reviewSignature ) {
					showReview( changes, reviewMessages );
				}

				setReviewError( strings.bulk_actions_edit_review_required );
				setError( '' );
				return;
			}

			writeChanges( changes );
			confirmButton.disabled = true;
			setButtonBusy( confirmButton, true, strings.bulk_actions_validating );
			dialog.setAttribute( 'aria-busy', 'true' );

			validateActionInput( context ).then( function () {
				finishDialog( true );
			} ).catch( function ( error ) {
				removeActionInputs( context.form, actionKey );
				confirmButton.disabled = false;
				setButtonBusy( confirmButton, false, originalText );
				dialog.removeAttribute( 'aria-busy' );

				if ( error && applyFieldErrors( error.fieldErrors ) ) {
					return;
				}

				setError( error && error.message ? error.message : strings.bulk_actions_validate_error );
			} );
		} );
		cancelButton.addEventListener( 'click', function () {
			finishDialog( false );
		} );
		modal.addEventListener( 'click', function ( event ) {
			if ( event.target === modal ) {
				finishDialog( false );
			}
		} );
		dialog.appendChild( title );
		dialog.appendChild( message );
		dialog.appendChild( error );
		dialog.appendChild( rows );
		dialog.appendChild( addButton );
		dialog.appendChild( review );
		actions.appendChild( cancelButton );
		actions.appendChild( confirmButton );
		dialog.appendChild( actions );
		modal.appendChild( dialog );

		return new Promise( function ( resolve ) {
			resolveDialog = resolve;

			if ( !fields.length ) {
				setError( strings.bulk_actions_edit_no_fields );
				addButton.disabled = true;
				confirmButton.disabled = true;
			} else {
				addRow();
			}

			document.addEventListener( 'keydown', onKeydown, true );
			lockBodyScroll();
			document.body.appendChild( modal );

			( dialog.querySelector( 'select, button' ) || dialog ).focus();
		} );
	}

	function defaultConfirmationHandler( confirmation, context ) {
		var message = confirmation.message || confirmation.title;

		if ( context && 'resend_notifications' === context.actionKey ) {
			if ( shouldSkipResendNotificationsPicker( context ) ) {
				writeResendNotificationsInput( context, [ getResendNotificationsData( context ).notifications[0].token ], '' );
			} else {
				return showResendNotificationsDialog( context ).then( function ( confirmed ) {
					if ( !confirmed ) {
						removeActionInputs( context.form, context.actionKey );
						return false;
					}

					if ( context.action && context.action.typedConfirmation && context.action.typedConfirmation.enabled && context.selectedCount >= context.action.typedConfirmation.threshold ) {
						return showTypedConfirmation( confirmation, context ).then( function ( typedConfirmed ) {
							if ( !typedConfirmed ) {
								removeActionInputs( context.form, context.actionKey );
							}

							return typedConfirmed;
						} );
					}

					return true;
				} );
			}
		}

		if ( context && 'edit_entries' === context.actionKey && getBulkEditFields( context ).length ) {
			return showBulkEditDialog( context ).then( function ( confirmed ) {
				if ( !confirmed ) {
					removeActionInputs( context.form, context.actionKey );
					return false;
				}

				if ( context.action && context.action.typedConfirmation && context.action.typedConfirmation.enabled && context.selectedCount >= context.action.typedConfirmation.threshold ) {
					return showTypedConfirmation( confirmation, context ).then( function ( typedConfirmed ) {
						if ( !typedConfirmed ) {
							removeActionInputs( context.form, context.actionKey );
						}

						return typedConfirmed;
					} );
				}

				return true;
			} );
		}

		if ( !confirmation.enabled ) {
			return true;
		}

		if ( context && context.action && context.action.typedConfirmation && context.action.typedConfirmation.enabled && context.selectedCount >= context.action.typedConfirmation.threshold ) {
			return showTypedConfirmation( confirmation, context );
		}

		if ( !message ) {
			return false;
		}

		return window.confirm( message );
	}

	function resolveConfirmation( result, callback ) {
		if ( result && 'function' === typeof result.then ) {
			result.then( function ( confirmed ) {
				callback( !!confirmed );
			} );
			return;
		}

		callback( !!result );
	}

	function escapeHtml( value ) {
		var node = document.createElement( 'div' );
		node.textContent = value || '';

		return node.innerHTML;
	}

	function getBackgroundMessageText( html ) {
		var wrapper = document.createElement( 'div' );
		var message;

		wrapper.innerHTML = html || '';
		message = wrapper.querySelector( '.gv-bulk-actions-background-message-text' );

		return message ? message.textContent : wrapper.textContent;
	}

	function dispatchBackgroundStatus( viewId, payload, renderInstance ) {
		var event;
		var detail = {
			view_id: viewId,
			render_instance: renderInstance || '',
			payload: payload || {}
		};

		if ( 'function' === typeof window.CustomEvent ) {
			event = new window.CustomEvent( 'gk.gravityview.bulkActions.backgroundStatus', {
				detail: detail
			} );
		} else {
			event = document.createEvent( 'CustomEvent' );
			event.initCustomEvent( 'gk.gravityview.bulkActions.backgroundStatus', false, false, detail );
		}

		document.dispatchEvent( event );
	}

	function updateBackgroundNoticeClass( element, payload ) {
		var notice = element.closest ? element.closest( '.gv-notice' ) : null;

		if ( notice && payload.notice_class ) {
			notice.className = 'gv-notice ' + payload.notice_class;
		}
	}

	function updateBackgroundStatusElement( element, payload ) {
		var html;
		var currentMessage;
		var nextMessage;
		var renderInstance;
		var viewId;

		if ( !payload || 'object' !== typeof payload ) {
			return;
		}

		if ( payload.html ) {
			html = payload.html;
		} else if ( payload.message ) {
			html = '<p class="gv-bulk-actions-background-message">' + escapeHtml( payload.message ) + '</p>';
		}

		if ( html ) {
			currentMessage = element.querySelector( '.gv-bulk-actions-background-message-text' );
			nextMessage = getBackgroundMessageText( html );
		}

		if ( html && ( !currentMessage || currentMessage.textContent !== nextMessage ) ) {
			element.innerHTML = html;
		}

		renderInstance = String( payload.render_instance || element.getAttribute( 'data-render-instance' ) || '' );
		payload.render_instance = renderInstance;
		element.setAttribute( 'data-status', payload.status || '' );
		element.setAttribute( 'data-polling', payload.polling ? '1' : '0' );
		updateBackgroundNoticeClass( element, payload );
		viewId = String( payload.view_id || element.getAttribute( 'data-view-id' ) || '' );
		dispatchBackgroundStatus( viewId, payload, renderInstance );
	}

	function requestBackgroundStatus( element ) {
		var ajaxUrl = element.getAttribute( 'data-ajax-url' ) || strings.bulk_actions_ajax_url;
		var formData;

		if ( !ajaxUrl || !window.fetch ) {
			return Promise.reject( new Error( strings.bulk_actions_ajax_error ) );
		}

		formData = new window.FormData();
		formData.append( 'action', element.getAttribute( 'data-ajax-action' ) || 'gv_bulk_action_status' );
		formData.append( 'gv_bulk_view_id', element.getAttribute( 'data-view-id' ) || '' );
		formData.append( 'gv_bulk_job_token', element.getAttribute( 'data-token' ) || '' );
		formData.append( 'gv_bulk_job_nonce', element.getAttribute( 'data-nonce' ) || '' );
		formData.append( 'gv_bulk_render_instance', element.getAttribute( 'data-render-instance' ) || '' );
		formData.append( 'gv_bulk_reload_url', element.getAttribute( 'data-reload-url' ) || window.location.href );

		return window.fetch( ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: formData
		} ).then( function ( response ) {
			return response.json().then( function ( data ) {
				return {
					data: data,
					status: response.status
				};
			} );
		} ).then( function ( response ) {
			var message;
			var error;

			if ( response.data && response.data.success && response.data.data ) {
				return response.data.data;
			}

			message = response.data && response.data.data && response.data.data.message ? response.data.data.message : strings.bulk_actions_ajax_error;
			error = new Error( message );
			error.status = response.status;

			throw error;
		} );
	}

	function removeBackgroundStatusElement( element ) {
		var notice = element.closest ? element.closest( '.gv-notice' ) : null;
		var target = notice || element;

		if ( target && target.parentNode ) {
			target.parentNode.removeChild( target );
		}
	}

	function handleBackgroundStatusError( element, error ) {
		var viewId = element.getAttribute( 'data-view-id' ) || '';
		var renderInstance = element.getAttribute( 'data-render-instance' ) || '';

		if ( 404 === error.status ) {
			updateBackgroundStatusElement( element, {
				active: false,
				polling: false,
				render_instance: renderInstance,
				status: 'not_found',
				view_id: viewId
			} );
			removeBackgroundStatusElement( element );
			return;
		}

		updateBackgroundStatusElement( element, {
			active: false,
			message: error && error.message ? error.message : strings.bulk_actions_ajax_error,
			notice_class: 'gv-bulk-actions-message gv-error error',
			render_instance: renderInstance,
			status: 'failed',
			view_id: viewId
		} );
	}

	function handleBackgroundStatusPayload( element, payload ) {
		updateBackgroundStatusElement( element, payload );

		if ( payload.should_reload && payload.reload_url ) {
			window.setTimeout( function () {
				window.location.href = payload.reload_url;
			}, 500 );
			return true;
		}

		return false;
	}

	function revalidateBackgroundStatusElement( element ) {
		requestBackgroundStatus( element ).then( function ( payload ) {
			handleBackgroundStatusPayload( element, payload );
		} ).catch( function ( error ) {
			handleBackgroundStatusError( element, error );
		} );
	}

	function initBackgroundStatus( element ) {
		var polling = 1 === toInt( element.getAttribute( 'data-polling' ) );
		var interval = Math.max( 1, toInt( element.getAttribute( 'data-poll-interval' ) ) ) * 1000;
		var viewId = element.getAttribute( 'data-view-id' ) || '';
		var renderInstance = element.getAttribute( 'data-render-instance' ) || '';
		var timer = null;

		if ( element.getAttribute( 'data-gv-bulk-background-initialized' ) ) {
			return;
		}

		element.setAttribute( 'data-gv-bulk-background-initialized', '1' );
		dispatchBackgroundStatus( viewId, {
			active: 'complete' !== element.getAttribute( 'data-status' ) && 'failed' !== element.getAttribute( 'data-status' ) && 'canceled' !== element.getAttribute( 'data-status' ),
			render_instance: renderInstance,
			status: element.getAttribute( 'data-status' ) || '',
			view_id: viewId
		}, renderInstance );

		if ( !polling || ( 'queued' !== element.getAttribute( 'data-status' ) && 'running' !== element.getAttribute( 'data-status' ) ) ) {
			return;
		}

		function stop() {
			if ( timer ) {
				window.clearTimeout( timer );
				timer = null;
			}
		}

		function schedule() {
			stop();
			timer = window.setTimeout( poll, interval );
		}

		function poll() {
			requestBackgroundStatus( element ).then( function ( payload ) {
				if ( handleBackgroundStatusPayload( element, payload ) ) {
					return;
				}

				if ( payload.active && payload.polling ) {
					schedule();
					return;
				}

				stop();
			} ).catch( function ( error ) {
				stop();
				handleBackgroundStatusError( element, error );
			} );
		}

		schedule();
	}

	function initForm( form ) {
		var scope = form.closest( '.gv-container' ) || document;
		var viewId = form.getAttribute( 'data-view-id' );
		var renderInstance = form.getAttribute( 'data-render-instance' ) || '';
		var pageEntries = toInt( form.getAttribute( 'data-page-entries' ) );
		var totalEntries = toInt( form.getAttribute( 'data-total-entries' ) );
		var canSelectAll = 1 === toInt( form.getAttribute( 'data-can-select-all' ) );
		var selectionBehavior = form.getAttribute( 'data-selection-behavior' ) || 'across_pages';
		var isPersistentSelection = 'current_page' !== selectionBehavior;
		var selectionTtl = toInt( form.getAttribute( 'data-selection-ttl' ) );
		var storageKey = form.getAttribute( 'data-storage-key' ) || '';
		var isSelectionMode = 1 === toInt( form.getAttribute( 'data-selection-mode-active' ) );
		var showSelectedEnabled = 1 === toInt( form.getAttribute( 'data-show-selected-enabled' ) );
		var isBackgroundJobActive = 1 === toInt( form.getAttribute( 'data-background-job-active' ) );
		var storage = storageFor( storageKey );
		var currentState = {
			all: false,
			ids: [],
			excluded: []
		};
		var entryCheckboxes = [];
		var pageToggles = [];
		var entriesInput = form.querySelector( 'input[name="gv_bulk_entries"]' );
		var excludedInput = form.querySelector( 'input[name="gv_bulk_excluded_entries"]' );
		var selectAllInput = form.querySelector( 'input[name="gv_bulk_select_all"]' );
		var showSelectedInput = form.querySelector( 'input[name="gv_bulk_show_selected"]' );
		var selects = [];
		var select = null;
		var applyButtons = [];
		var counts = [];
		var selectionSummaries = [];
		var pageSelections = [];
		var allSelectedMessages = [];
		var selectAllButtons = [];
		var showSelectedButtons = [];
		var showAllLinks = [];
		var clearButtons = [];
		var elements = {};
		var actions;
		var lastEntryId = 0;
		var pendingCheckboxClick = null;

		function collectElements( instanceId ) {
			return {
				entryCheckboxes: findElements( scope, 'entry-checkbox', '.gv-bulk-actions-entry', viewId, instanceId ),
				pageToggles: findElements( scope, 'page-toggle', '.gv-bulk-actions-toggle-page', viewId, instanceId ),
				selects: findElements( scope, 'action-select', '.gv-bulk-actions-select', viewId, instanceId ),
				select: findFirstElement( form, scope, 'action-select', '.gv-bulk-actions-select', viewId, instanceId ),
				applyButtons: findElements( scope, 'apply', '.gv-bulk-actions-apply', viewId, instanceId ),
				counts: findElements( scope, 'selected-count', '.gv-bulk-actions-count', viewId, instanceId ),
				selectionSummaries: findElements( scope, 'selection-summary', '.gv-bulk-actions-selection-summary', viewId, instanceId ),
				pageSelections: findElements( scope, 'page-selection', '.gv-bulk-actions-page-selection', viewId, instanceId ),
				allSelectedMessages: findElements( scope, 'all-selected', '.gv-bulk-actions-all-selected', viewId, instanceId ),
				selectAllButtons: findElements( scope, 'select-all', '.gv-bulk-actions-select-all', viewId, instanceId ),
				showSelectedButtons: findElements( scope, 'show-selected', '.gv-bulk-actions-show-selected', viewId, instanceId ),
				showAllLinks: findElements( scope, 'show-all', '.gv-bulk-actions-show-all', viewId, instanceId ),
				clearButtons: findElements( scope, 'clear', '.gv-bulk-actions-clear', viewId, instanceId )
			};
		}

		function emptyElements() {
			return {
				entryCheckboxes: [],
				pageToggles: [],
				selects: [],
				select: null,
				applyButtons: [],
				counts: [],
				selectionSummaries: [],
				pageSelections: [],
				allSelectedMessages: [],
				selectAllButtons: [],
				showSelectedButtons: [],
				showAllLinks: [],
				clearButtons: []
			};
		}

		function refreshElements() {
			var refreshedElements;

			if ( !renderInstance ) {
				refreshedElements = emptyElements();

				if ( window.console && window.console.warn ) {
					window.console.warn( 'GravityView Bulk Actions: render-instance markup is missing for View ' + viewId + '. Bulk Actions will not bind this form.' );
				}
			} else {
				refreshedElements = collectElements( renderInstance );
			}

			elements = applyFilters( 'gk.gravityview.bulkActions.elements', refreshedElements, form, scope, viewId, renderInstance ) || refreshedElements;
			entryCheckboxes = elements.entryCheckboxes || [];
			pageToggles = elements.pageToggles || [];
			selects = elements.selects || [];
			select = elements.select || selects[0] || null;
			applyButtons = elements.applyButtons || [];
			counts = elements.counts || [];
			selectionSummaries = elements.selectionSummaries || [];
			pageSelections = elements.pageSelections || [];
			allSelectedMessages = elements.allSelectedMessages || [];
			selectAllButtons = elements.selectAllButtons || [];
			showSelectedButtons = elements.showSelectedButtons || [];
			showAllLinks = elements.showAllLinks || [];
			clearButtons = elements.clearButtons || [];
		}

		refreshElements();

		if ( !select || !entriesInput || !excludedInput || !selectAllInput ) {
			return;
		}

		if ( '1' === form.getAttribute( 'data-gv-bulk-actions-initialized' ) ) {
			return;
		}

		form.setAttribute( 'data-gv-bulk-actions-initialized', '1' );

		actions = getActions( select, form );
		selects.forEach( function ( actionSelect ) {
			if ( actionSelect !== select ) {
				syncActionOptions( actionSelect, actions );
			}
		} );

		function setBackgroundJobActive( active ) {
			isBackgroundJobActive = !!active;
			form.setAttribute( 'data-background-job-active', isBackgroundJobActive ? '1' : '0' );
			updateUi( getState() );
		}

		function formIsConnected() {
			return document.documentElement.contains( form );
		}

		function onBackgroundStatus( event ) {
			var detail = event.detail || {};
			var payload = detail.payload || {};

			if ( !formIsConnected() ) {
				return;
			}

			if ( String( detail.view_id || payload.view_id || '' ) !== viewId ) {
				return;
			}

			if ( renderInstance && String( detail.render_instance || payload.render_instance || '' ) !== renderInstance ) {
				return;
			}

			setBackgroundJobActive( !!payload.active );
		}

		function submitForm() {
			window.HTMLFormElement.prototype.submit.call( form );
		}

		function getSelectedAction() {
			var action = actions[ select.value ] || getOptionAction( select.options[ select.selectedIndex ] );

			action = applyFilters( 'gk.gravityview.bulkActions.action', action, form ) || action;
			action.key = action.key || select.value;

			return action;
		}

		function getState() {
			if ( !isPersistentSelection ) {
				return {
					all: currentState.all,
					ids: currentState.ids.slice( 0 ),
					excluded: currentState.excluded.slice( 0 )
				};
			}

			var stored = storage.getItem();

			if ( !stored ) {
				return {
					all: false,
					ids: [],
					excluded: []
				};
			}

			try {
				var parsed = JSON.parse( stored );

				if ( parsed && 'object' === typeof parsed && !Array.isArray( parsed ) ) {
					if ( selectionTtl && ( !parsed.updatedAt || toInt( parsed.updatedAt ) + selectionTtl < Math.floor( Date.now() / 1000 ) ) ) {
						storage.removeItem();

						return {
							all: false,
							ids: [],
							excluded: []
						};
					}

					return {
						all: !!parsed.all,
						ids: normalizeIds( parsed.ids ),
						excluded: normalizeIds( parsed.excluded )
					};
				}

				storage.removeItem();

				return {
					all: false,
					ids: [],
					excluded: []
				};
			} catch ( e ) {
				storage.removeItem();

				return {
					all: false,
					ids: [],
					excluded: []
				};
			}
		}

		function getSelectedCount( state ) {
			if ( state.all ) {
				return Math.max( 0, totalEntries - state.excluded.length );
			}

			return state.ids.length;
		}

		function updateHiddenFields( state ) {
			entriesInput.value = state.ids.join( ',' );
			excludedInput.value = state.all ? state.excluded.join( ',' ) : '';
			selectAllInput.value = state.all ? '1' : '';
		}

		function getStoredState( state ) {
			return {
				all: !!state.all,
				ids: normalizeIds( state.ids ),
				excluded: normalizeIds( state.excluded ),
				updatedAt: Math.floor( Date.now() / 1000 )
			};
		}

		function broadcastSelectionChanged() {
			var event;

			if ( !isPersistentSelection || !storageKey ) {
				return;
			}

			if ( 'function' === typeof window.CustomEvent ) {
				event = new window.CustomEvent( selectionChangedEvent, {
					detail: {
						form: form,
						renderInstance: renderInstance,
						storageKey: storageKey,
						viewId: viewId
					}
				} );
			} else {
				event = document.createEvent( 'CustomEvent' );
				event.initCustomEvent( selectionChangedEvent, false, false, {
					form: form,
					renderInstance: renderInstance,
					storageKey: storageKey,
					viewId: viewId
				} );
			}

			document.dispatchEvent( event );
		}

		function updateRowSelectionState( checkbox, isSelected ) {
			var row = checkbox.closest ? checkbox.closest( 'tr' ) : null;

			if ( !row ) {
				return;
			}

			row.classList.toggle( 'gv-bulk-actions-row-selected', isSelected );

			if ( isSelected ) {
				row.setAttribute( 'data-gv-bulk-selected', '1' );
				return;
			}

			row.removeAttribute( 'data-gv-bulk-selected' );
		}

		function applyEntrySelectionToState( state, entryId, checked ) {
			var index;

			if ( state.all ) {
				index = state.excluded.indexOf( entryId );

				if ( checked && -1 !== index ) {
					state.excluded.splice( index, 1 );
				} else if ( !checked && -1 === index ) {
					state.excluded.push( entryId );
				}

				return state;
			}

			index = state.ids.indexOf( entryId );

			if ( checked && -1 === index ) {
				state.ids.push( entryId );
			} else if ( !checked && -1 !== index ) {
				state.ids.splice( index, 1 );
			}

			return state;
		}

		function applyEntryRangeToState( state, fromEntryId, toEntryId, checked ) {
			var pageIds;
			var fromIndex;
			var toIndex;
			var start;
			var end;

			refreshElements();

			pageIds = entryCheckboxes.map( function ( checkbox ) {
				return toInt( checkbox.value );
			} ).filter( function ( entryId ) {
				return !!entryId;
			} );

			fromIndex = pageIds.indexOf( fromEntryId );
			toIndex = pageIds.indexOf( toEntryId );

			if ( -1 === fromIndex || -1 === toIndex ) {
				return applyEntrySelectionToState( state, toEntryId, checked );
			}

			start = Math.min( fromIndex, toIndex );
			end = Math.max( fromIndex, toIndex );

			pageIds.slice( start, end + 1 ).forEach( function ( entryId ) {
				applyEntrySelectionToState( state, entryId, checked );
			} );

			return state;
		}

		function normalizeState( state ) {
			state = {
				all: isPersistentSelection && !!state.all,
				ids: normalizeIds( state.ids ),
				excluded: isPersistentSelection ? normalizeIds( state.excluded ) : []
			};

			if ( state.all && totalEntries && state.excluded.length >= totalEntries ) {
				state = {
					all: false,
					ids: [],
					excluded: []
				};
			}

			return state;
		}

		function applyState( state ) {
			updateHiddenFields( state );
			updateUi( state );
		}

		function syncStateFromStorage() {
			applyState( normalizeState( getState() ) );
		}

		function setState( state, options ) {
			options = options || {};
			state = normalizeState( state );

			if ( !isPersistentSelection ) {
				currentState = {
					all: false,
					ids: state.ids,
					excluded: []
				};
			} else if ( false !== options.persist ) {
				if ( state.all ) {
					storage.setItem( JSON.stringify( getStoredState( state ) ) );
				} else if ( state.ids.length ) {
					storage.setItem( JSON.stringify( getStoredState( state ) ) );
				} else {
					storage.removeItem();
				}
			}

			applyState( state );

			if ( false !== options.persist ) {
				broadcastSelectionChanged();
			}
		}

		function updateUi( state ) {
			var pageIds = [];

			refreshElements();

			entryCheckboxes.forEach( function ( checkbox ) {
				var entryId = toInt( checkbox.value );
				var isChecked = state.all ? -1 === state.excluded.indexOf( entryId ) : -1 !== state.ids.indexOf( entryId );

				pageIds.push( entryId );
				checkbox.checked = isChecked;
				updateRowSelectionState( checkbox, isChecked );
			} );

			var selectedOnPage = pageIds.filter( function ( entryId ) {
				return state.all ? -1 === state.excluded.indexOf( entryId ) : -1 !== state.ids.indexOf( entryId );
			} ).length;
			var selectedCount = getSelectedCount( state );
			var hasSelection = selectedCount > 0;
			var showPageSelection = canSelectAll && !state.all && pageIds.length > 0 && selectedOnPage === pageIds.length;
			var showSelectAll = showPageSelection && selectedCount < totalEntries;
			var showAllSelected = hasSelection && state.all && 0 === state.excluded.length;
			var showSelected = showSelectedEnabled && hasSelection && !isSelectionMode;
			var showAll = showSelectedEnabled && isSelectionMode;

			pageToggles.forEach( function ( toggle ) {
				toggle.checked = pageIds.length > 0 && selectedOnPage === pageIds.length;
				toggle.indeterminate = selectedOnPage > 0 && selectedOnPage < pageIds.length;
				toggle.disabled = isBackgroundJobActive;
			} );

			entryCheckboxes.forEach( function ( checkbox ) {
				checkbox.disabled = isBackgroundJobActive;
			} );

			selects.forEach( function ( syncedSelect ) {
				syncedSelect.disabled = isBackgroundJobActive;
			} );

			applyButtons.forEach( function ( button ) {
				button.disabled = isBackgroundJobActive || !hasSelection || !select.value;
			} );
			selectionSummaries.forEach( function ( summary ) {
				if ( 'page-selection' === summary.getAttribute( 'data-gv-bulk-display' ) ) {
					summary.hidden = !hasSelection && !showSelectAll && !showAllSelected && !showAll;
					return;
				}

				summary.hidden = !hasSelection && !showSelectAll && !showAllSelected && !showAll;
			} );
			pageSelections.forEach( function ( pageSelection ) {
				pageSelection.hidden = isBackgroundJobActive || !showSelectAll;
			} );
			allSelectedMessages.forEach( function ( message ) {
				message.hidden = isBackgroundJobActive || !showAllSelected;
			} );
			showSelectedButtons.forEach( function ( button ) {
				button.hidden = isBackgroundJobActive || !showSelected;
			} );
			showAllLinks.forEach( function ( link ) {
				link.hidden = isBackgroundJobActive || !showAll;
			} );
			clearButtons.forEach( function ( button ) {
				button.hidden = isBackgroundJobActive || !hasSelection;
			} );
			counts.forEach( function ( count ) {
				var showCount = !isBackgroundJobActive && hasSelection && !showAllSelected;

				count.hidden = !showCount;
				count.textContent = selectedCount ? formatSelectedCount( selectedCount ) : '';
			} );
		}

		scope.addEventListener( 'change', function ( event ) {
			var target = event.target;
			var state;
			var entryId;
			var index;
			var checked;

			if ( isBackgroundJobActive ) {
				return;
			}

			if ( matchesViewElement( target, 'entry-checkbox', '.gv-bulk-actions-entry', viewId, renderInstance ) ) {
				state = getState();
				entryId = toInt( target.value );
				checked = target.checked;

				if ( pendingCheckboxClick && pendingCheckboxClick.entryId === entryId && pendingCheckboxClick.shiftKey && lastEntryId ) {
					state = applyEntryRangeToState( state, lastEntryId, entryId, checked );
				} else {
					state = applyEntrySelectionToState( state, entryId, checked );
				}

				setState( state );
				lastEntryId = entryId;
				pendingCheckboxClick = null;
				return;
			}

			if ( matchesViewElement( target, 'page-toggle', '.gv-bulk-actions-toggle-page', viewId, renderInstance ) ) {
				state = getState();
				checked = target.checked;
				refreshElements();

				if ( state.all ) {
					entryCheckboxes.forEach( function ( checkbox ) {
						entryId = toInt( checkbox.value );
						index = state.excluded.indexOf( entryId );

						if ( checked && -1 !== index ) {
							state.excluded.splice( index, 1 );
						} else if ( !checked && -1 === index ) {
							state.excluded.push( entryId );
						}
					} );

					setState( state );
					return;
				}

				entryCheckboxes.forEach( function ( checkbox ) {
					entryId = toInt( checkbox.value );
					index = state.ids.indexOf( entryId );

					if ( checked && -1 === index ) {
						state.ids.push( entryId );
					} else if ( !checked && -1 !== index ) {
						state.ids.splice( index, 1 );
					}
				} );

				setState( state );
				return;
			}

			if ( matchesViewElement( target, 'action-select', '.gv-bulk-actions-select', viewId, renderInstance ) ) {
				select.value = target.value;
				refreshElements();
				selects.forEach( function ( syncedSelect ) {
					syncedSelect.value = target.value;
				} );
				updateUi( getState() );
			}
		} );

		scope.addEventListener( 'click', function ( event ) {
			var target = event.target;
			var state;
			var matchedTarget;

			if ( isBackgroundJobActive ) {
				return;
			}

			if ( matchesViewElement( target, 'entry-checkbox', '.gv-bulk-actions-entry', viewId, renderInstance ) ) {
				pendingCheckboxClick = {
					entryId: toInt( target.value ),
					shiftKey: !!event.shiftKey
				};

				return;
			}

			matchedTarget = closestViewElement( target, 'select-all', '.gv-bulk-actions-select-all', viewId, renderInstance );

			if ( matchedTarget ) {
				event.preventDefault();

				if ( !isPersistentSelection ) {
					return;
				}

				setState( {
					all: true,
					ids: [],
					excluded: []
				} );
				return;
			}

			matchedTarget = closestViewElement( target, 'show-selected', '.gv-bulk-actions-show-selected', viewId, renderInstance );

			if ( matchedTarget ) {
				event.preventDefault();

				if ( !showSelectedInput || !showSelectedEnabled ) {
					return;
				}

				state = getState();

				if ( !getSelectedCount( state ) ) {
					window.alert( strings.bulk_actions_select_entries );
					return;
				}

				updateHiddenFields( state );
				showSelectedInput.value = '1';
				submitForm();
				return;
			}

			matchedTarget = closestViewElement( target, 'clear', '.gv-bulk-actions-clear', viewId, renderInstance );

			if ( matchedTarget ) {
				event.preventDefault();
				setState( {
					all: false,
					ids: [],
					excluded: []
				} );
			}
		} );

		form.addEventListener( 'submit', function ( event ) {
			var state = getState();
			var action;
			var context;
			var confirmation;
			var confirmationHandler;

			if ( isBackgroundJobActive ) {
				event.preventDefault();
				return;
			}

			if ( !getSelectedCount( state ) ) {
				event.preventDefault();
				window.alert( strings.bulk_actions_select_entries );
				return;
			}

			if ( !select.value ) {
				event.preventDefault();
				window.alert( strings.bulk_actions_select_action );
				return;
			}

			event.preventDefault();

			if ( showSelectedInput ) {
				showSelectedInput.value = '';
			}

			updateHiddenFields( state );
			action = getSelectedAction();
			confirmation = normalizeConfirmation( action.confirmation || {} );
			context = {
				form: form,
				elements: elements,
				select: select,
				action: action,
				actionKey: action.key,
				state: state,
				selectedCount: getSelectedCount( state ),
				entryIds: state.ids.slice( 0 ),
				excludedEntryIds: state.excluded.slice( 0 ),
				isSelectAll: !!state.all,
				selectionBehavior: selectionBehavior
			};
			confirmation = resolveConfirmationForCount( applyFilters( 'gk.gravityview.bulkActions.confirmation', confirmation, context ), context.selectedCount );
			context.confirmation = confirmation;
			confirmationHandler = applyFilters( 'gk.gravityview.bulkActions.confirmationHandler', defaultConfirmationHandler, context );
			confirmationHandler = 'function' === typeof confirmationHandler ? confirmationHandler : defaultConfirmationHandler;

			doAction( 'gk.gravityview.bulkActions.beforeConfirm', context );

			resolveConfirmation( confirmationHandler( confirmation, context ), function ( confirmed ) {
				if ( !confirmed ) {
					removeActionInputs( form, action.key );
					doAction( 'gk.gravityview.bulkActions.cancelled', context );
					return;
				}

				if ( isBackgroundJobActive || '1' === form.getAttribute( 'data-background-job-active' ) ) {
					removeActionInputs( form, action.key );
					window.alert( strings.bulk_actions_background_active );
					return;
				}

				doAction( 'gk.gravityview.bulkActions.beforeSubmit', context );

				if ( isBackgroundJobActive || '1' === form.getAttribute( 'data-background-job-active' ) ) {
					removeActionInputs( form, action.key );
					window.alert( strings.bulk_actions_background_active );
					return;
				}

				submitForm();
			} );
		} );

		function onPageShow() {
			if ( !formIsConnected() ) {
				return;
			}

			syncStateFromStorage();
		}

		function onStorage( event ) {
			if ( !isPersistentSelection || !formIsConnected() ) {
				return;
			}

			if ( event.key === storageKey ) {
				syncStateFromStorage();
			}
		}

		function onSelectionChanged( event ) {
			var detail = event.detail || {};

			if ( !isPersistentSelection || !formIsConnected() || detail.form === form ) {
				return;
			}

			if ( detail.storageKey === storageKey && String( detail.renderInstance || '' ) === renderInstance ) {
				syncStateFromStorage();
			}
		}

		if ( !isPersistentSelection || 1 === toInt( form.getAttribute( 'data-clear-selection' ) ) ) {
			storage.removeItem();
		}

		registerInitializedForm( {
			isConnected: formIsConnected,
			onBackgroundStatus: onBackgroundStatus,
			onPageShow: onPageShow,
			onSelectionChanged: onSelectionChanged,
			onStorage: onStorage
		} );

		setState( getState() );
		doAction( 'gk.gravityview.bulkActions.ready', {
			form: form,
			elements: elements,
			actions: actions,
			getState: getState,
			setState: setState,
			updateUi: updateUi,
			selectionBehavior: selectionBehavior,
			renderInstance: renderInstance,
			isBackgroundJobActive: isBackgroundJobActive
		} );
	}

	function markBackgroundFormSubmitting( form ) {
		var button = form.querySelector( 'button[type="submit"]' );

		form.setAttribute( 'aria-busy', 'true' );

		if ( button ) {
			button.disabled = true;
		}
	}

	ready( function () {
		document.addEventListener( 'submit', function ( event ) {
			var form = event.target;

			if ( !form || !form.classList || ( !form.classList.contains( 'gv-bulk-actions-dismiss-job' ) && !form.classList.contains( 'gv-bulk-actions-cancel-job' ) ) ) {
				return;
			}

			markBackgroundFormSubmitting( form );

			if ( !form.classList.contains( 'gv-bulk-actions-dismiss-job' ) ) {
				return;
			}

			doAction( 'gk.gravityview.bulkActions.beforeDismiss', {
				form: form,
				viewId: form.querySelector( '[name="gv_bulk_view_id"]' ) ? form.querySelector( '[name="gv_bulk_view_id"]' ).value : '',
				token: form.querySelector( '[name="gv_bulk_job_token"]' ) ? form.querySelector( '[name="gv_bulk_job_token"]' ).value : ''
			} );
		} );

		toArray( document.querySelectorAll( '.gv-bulk-actions' ) ).forEach( initForm );
		toArray( document.querySelectorAll( '[data-gv-bulk-background-status]' ) ).forEach( initBackgroundStatus );

		window.addEventListener( 'pageshow', function ( event ) {
			if ( event && !event.persisted ) {
				return;
			}

			toArray( document.querySelectorAll( '[data-gv-bulk-background-status]' ) ).forEach( revalidateBackgroundStatusElement );
		} );
	} );
}() );
