/**
 * Bulk Actions widget admin interactions.
 *
 * @since 3.0.0
 */
( function ( $ ) {
	'use strict';

	const bulkActionsAdmin = {
		/**
		 * Initializes delegated handlers.
		 *
		 * @since 3.0.0
		 *
		 * @return {void}
		 */
		init: function () {
			$( document.body )
				.on( 'click', '[data-bulk-action-settings-open]', bulkActionsAdmin.openSettingsPanel )
				.on( 'click', '[data-bulk-action-settings-close]', bulkActionsAdmin.closeSettingsPanel )
				.on( 'click', '[data-bulk-action-settings].has-options-panel', bulkActionsAdmin.closeSettingsPanelOutside )
				.on( 'change', '.gv-dialog-options select[name$="[bulk_actions][]"]', bulkActionsAdmin.syncActionSettings )
				.on( 'gravityview/field-added gravityview/field-removed', bulkActionsAdmin.syncActionSettings )
				.on( 'dialogbeforeclose', '.gv-dialog-options', bulkActionsAdmin.beforeDialogClose )
				.on( 'dialogclose dialogdestroy', '.gv-dialog-options', bulkActionsAdmin.onDialogClosed );

			document.addEventListener( 'keydown', bulkActionsAdmin.handleSettingsPanelEscapeCapture, true );
			$( document.body ).on( 'gravityview/dialog-opened', bulkActionsAdmin.syncActionSettings );
			window.jQuery( document.body ).on( 'gravityview_form_change', bulkActionsAdmin.hideUnavailableNotices );
			bulkActionsAdmin.syncActionSettings();
		},

		/**
		 * Opens a per-action settings panel.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQueryEvent} e Click event.
		 * @return {void}
		 */
		openSettingsPanel: function ( e ) {
			e.preventDefault();

			const actionKey = $( this ).data( 'bulkActionSettingsOpen' );
			const $wrapper = $( this ).closest( '[data-bulk-action-settings]' );
			const $panel = $wrapper.find( '[data-bulk-action-settings-panel="' + actionKey + '"]' );

			if ( ! $panel.length ) {
				return;
			}

			bulkActionsAdmin.closeSettingsPanel( e, true );
			bulkActionsAdmin.getDialog( $wrapper ).addClass( 'gv-bulk-action-settings-dialog-open' );
			$wrapper.data( 'bulkActionSettingsTrigger', this );

			$wrapper
				.addClass( 'has-options-panel' )
				.find( '[data-bulk-action-settings-row]' )
				.removeClass( 'has-options-panel' );

			$( this ).closest( '[data-bulk-action-settings-row]' ).addClass( 'has-options-panel' );

			$panel
				.prop( 'hidden', false )
				.attr( 'aria-hidden', 'false' )
				.addClass( 'is-active' );

			bulkActionsAdmin.trapPanelFocus( $panel );

			setTimeout( function () {
				const $focusTarget = $panel
					.find( ':tabbable' )
					.filter( ':visible' )
					.first();

				if ( $focusTarget.length ) {
					$focusTarget.trigger( 'focus' );
				}
			} );
		},

		/**
		 * Closes the active per-action settings panel.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQueryEvent} e       Click or keydown event.
		 * @param {boolean}     isQuick Whether the close is internal cleanup.
		 * @return {void}
		 */
		closeSettingsPanel: function ( e, isQuick ) {
			const isEvent = e && e.target && 'function' === typeof e.preventDefault;
			const $target = isEvent ? $( e.target ) : $( e );
			const $wrapper = $target.closest( '[data-bulk-action-settings]' );

			if ( isEvent && ! isQuick ) {
				e.preventDefault();
			}

			if ( ! $wrapper.length ) {
				return;
			}

			$wrapper
				.removeClass( 'has-options-panel' )
				.find( '[data-bulk-action-settings-row]' )
				.removeClass( 'has-options-panel' );

			$wrapper
				.find( '[data-bulk-action-settings-panel]' )
				.attr( 'aria-hidden', 'true' )
				.prop( 'hidden', true )
				.removeClass( 'is-active' );

			$wrapper
				.find( '[data-bulk-action-settings-panel]' )
				.off( 'keydown.bulkActionsFocusTrap' );

			bulkActionsAdmin.getDialog( $wrapper ).removeClass( 'gv-bulk-action-settings-dialog-open' );

			if ( ! isQuick ) {
				const trigger = $wrapper.data( 'bulkActionSettingsTrigger' );

				if ( trigger && $( trigger ).is( ':visible' ) ) {
					$( trigger ).trigger( 'focus' );
				}
			}
		},

		/**
		 * Closes the settings panel when clicking the overlay.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQueryEvent} e Click event.
		 * @return {void}
		 */
		closeSettingsPanelOutside: function ( e ) {
			if ( ! $( e.target ).is( '[data-bulk-action-settings]' ) ) {
				return;
			}

			bulkActionsAdmin.closeSettingsPanel( e );
		},

		/**
		 * Closes the active settings drawer before jQuery UI closes the parent dialog.
		 *
		 * @since 3.0.0
		 *
		 * @param {KeyboardEvent} e Keydown event.
		 * @return {void}
		 */
		handleSettingsPanelEscapeCapture: function ( e ) {
			if ( ! bulkActionsAdmin.isEscapeEvent( e ) ) {
				return;
			}

			if ( bulkActionsAdmin.shouldDeferEscape( e ) ) {
				return;
			}

			const $wrapper = bulkActionsAdmin.getOpenSettingsWrapper( e );

			if ( ! $wrapper.length ) {
				return;
			}

			e.preventDefault();
			e.stopPropagation();

			if ( e.stopImmediatePropagation ) {
				e.stopImmediatePropagation();
			}

			if ( window.gvAdminActions && 'function' === typeof window.gvAdminActions.ignoreEscape ) {
				window.gvAdminActions.ignoreEscape();
			}

			bulkActionsAdmin.closeSettingsPanel( $wrapper );
		},

		/**
		 * Prevents the parent widget dialog from closing before the action drawer closes.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQueryEvent} e Dialog close event.
		 * @return {boolean|void}
		 */
		beforeDialogClose: function ( e ) {
			if ( ! bulkActionsAdmin.isEscapeEvent( e ) ) {
				return;
			}

			const $wrapper = $( this )
				.find( '[data-bulk-action-settings].has-options-panel' )
				.filter( ':visible' )
				.last();

			if ( ! $wrapper.length ) {
				return;
			}

			if ( bulkActionsAdmin.shouldDeferEscape( e ) ) {
				if ( window.gvAdminActions && 'function' === typeof window.gvAdminActions.ignoreEscape ) {
					window.gvAdminActions.ignoreEscape();
				}

				return false;
			}

			if ( window.gvAdminActions && 'function' === typeof window.gvAdminActions.ignoreEscape ) {
				window.gvAdminActions.ignoreEscape();
			}

			bulkActionsAdmin.closeSettingsPanel( $wrapper );

			return false;
		},

		/**
		 * Cleans up drawer state when the parent widget dialog closes.
		 *
		 * @since 3.0.0
		 *
		 * @return {void}
		 */
		onDialogClosed: function () {
			$( this ).find( '[data-bulk-action-settings]' ).each( function () {
				bulkActionsAdmin.closeSettingsPanel( $( this ), true );
			} );
		},

		/**
		 * Hides load-time unavailable notices after the View data source changes.
		 *
		 * @since 3.1.0
		 *
		 * @return {void}
		 */
		hideUnavailableNotices: function () {
			$( '[data-bulk-action-settings]' ).each( function () {
				const $wrapper = $( this );

				$wrapper
					.find( '[data-bulk-action-unavailable]' )
					.prop( 'hidden', true );
			} );
		},

		/**
		 * Shows per-action settings only for actions selected in the widget.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQueryEvent} e Change or dialog event.
		 * @return {void}
		 */
		syncActionSettings: function ( e ) {
			let $context = e && e.target ? $( e.target ).closest( '.gv-dialog-options' ) : $( document.body );

			if ( ! $context.length ) {
				$context = $( document.body );
			}

			setTimeout( function () {
				$context.find( '[data-bulk-action-settings]' ).each( function () {
					const $wrapper = $( this );
					const $dialog = $wrapper.closest( '.gv-dialog-options' );
					const selected = bulkActionsAdmin.getSelectedActions( $dialog );
					let visibleRows = 0;

					$wrapper.find( '[data-bulk-action-settings-row]' ).each( function () {
						const $row = $( this );
						const actionKey = String( $row.data( 'bulkActionSettingsRow' ) || '' );
						const isSelected = -1 !== selected.indexOf( actionKey );
						const $panel = $wrapper.find( '[data-bulk-action-settings-panel="' + actionKey + '"]' );

						$row.prop( 'hidden', ! isSelected );

						if ( ! isSelected && $panel.hasClass( 'is-active' ) ) {
							bulkActionsAdmin.closeSettingsPanel( $panel, true );
						}

						if ( ! $panel.hasClass( 'is-active' ) ) {
							$panel
								.prop( 'hidden', true )
								.attr( 'aria-hidden', 'true' );
						}

						if ( isSelected ) {
							visibleRows++;
						}
					} );

					const $list = $wrapper.find( '.gv-bulk-action-settings-list' );

					selected.forEach( function ( actionKey ) {
						const $row = $list.children( '[data-bulk-action-settings-row="' + actionKey + '"]' );

						if ( $row.length ) {
							$list.append( $row );
						}
					} );

					$list.prop( 'hidden', 0 === visibleRows );

					$wrapper
						.find( '[data-bulk-action-settings-none-selected]' )
						.prop( 'hidden', 0 !== visibleRows );
				} );
			} );
		},

		/**
		 * Returns selected bulk action keys from a widget settings dialog.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQuery} $dialog Widget settings dialog.
		 * @return {string[]}
		 */
		getSelectedActions: function ( $dialog ) {
			const selected = [];

			$dialog
				.find( 'select[name$="[bulk_actions][]"]' )
				.each( function () {
					const values = this.tomselect && Array.isArray( this.tomselect.items )
						? this.tomselect.items
						: $( this ).val();

					( Array.isArray( values ) ? values : [ values ] ).forEach( function ( value ) {
						value = String( value || '' );

						if ( value ) {
							selected.push( value );
						}
					} );
				} );

			return selected;
		},

		/**
		 * Returns the containing jQuery UI dialog element.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQuery} $wrapper Action settings wrapper.
		 * @return {jQuery}
		 */
		getDialog: function ( $wrapper ) {
			const $dialog = $wrapper.closest( '.gv-dialog' );

			return $dialog.length ? $dialog : $wrapper;
		},

		/**
		 * Finds the visible Bulk Actions settings wrapper that currently owns Escape.
		 *
		 * @since 3.0.0
		 *
		 * @param {Event|jQueryEvent} e Keyboard event.
		 * @return {jQuery}
		 */
		getOpenSettingsWrapper: function ( e ) {
			const $target = e && e.target ? $( e.target ) : $();
			const $targetDialog = $target.closest( '.gv-dialog, .ui-dialog-content, .gv-dialog-options' );
			let $wrapper = $();

			if ( $target.closest( '[data-bulk-action-settings].has-options-panel' ).length ) {
				$wrapper = $target.closest( '[data-bulk-action-settings].has-options-panel' );
			} else if ( $targetDialog.length ) {
				$wrapper = $targetDialog
					.find( '[data-bulk-action-settings].has-options-panel' )
					.filter( ':visible' )
					.last();
			}

			if ( $wrapper.length ) {
				return $wrapper;
			}

			return $( '[data-bulk-action-settings].has-options-panel' )
				.filter( ':visible' )
				.last();
		},

		/**
		 * Keeps Tab navigation inside the open action settings panel.
		 *
		 * @since 3.0.0
		 *
		 * @param {jQuery} $panel Action settings panel.
		 * @return {void}
		 */
		trapPanelFocus: function ( $panel ) {
			$panel.off( 'keydown.bulkActionsFocusTrap' ).on( 'keydown.bulkActionsFocusTrap', function ( e ) {
				if ( 'Tab' !== e.key ) {
					return;
				}

				const $elements = $panel.find( ':tabbable' ).filter( ':visible' );

				if ( ! $elements.length ) {
					return;
				}

				const first = $elements.first()[ 0 ];
				const last = $elements.last()[ 0 ];
				const focused = document.activeElement;

				if ( e.shiftKey && focused === first ) {
					e.preventDefault();
					last.focus();
				} else if ( ! e.shiftKey && focused === last ) {
					e.preventDefault();
					first.focus();
				}
			} );
		},

		/**
		 * Lets child popups close themselves before the drawer consumes Escape.
		 *
		 * @since 3.0.0
		 *
		 * @param {Event|jQueryEvent} e Keyboard event.
		 * @return {boolean}
		 */
		shouldDeferEscape: function ( e ) {
			const $target = e && e.target ? $( e.target ) : $();

			if ( $( '.ui-autocomplete:visible, .ts-dropdown:visible, .CodeMirror-hints:visible' ).length ) {
				return true;
			}

			return 0 < $target.closest( '.ui-autocomplete, .ts-dropdown, .CodeMirror-hints' ).length;
		},

		/**
		 * Checks whether an event was caused by Escape.
		 *
		 * @since 3.0.0
		 *
		 * @param {Event|jQueryEvent} e Event object.
		 * @return {boolean}
		 */
		isEscapeEvent: function ( e ) {
			const originalEvent = e && e.originalEvent ? e.originalEvent : e;
			const key = e && e.key ? e.key : originalEvent && originalEvent.key;
			const keyCode = e && e.keyCode ? e.keyCode : originalEvent && originalEvent.keyCode;

			return 'Escape' === key || 'Esc' === key || 27 === keyCode;
		}
	};

	$( bulkActionsAdmin.init );
}( jQuery ) );
