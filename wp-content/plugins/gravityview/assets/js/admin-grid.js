( ( $ ) => {
	const activateGrid = ( selector ) => {
		$( selector ).find( '.gv-grid > .gv-grid-rows-container' ).each( ( i, grid ) => {
			let options = {
				handle: '> .gv-grid-row-actions > .gv-grid-row-handle',
				items: '> .gv-grid-row.is-sortable',
				distance: 2,
				revert: 75,
				placeholder: 'grid-row-placeholder',
				forcePlaceholderSize: true,
				receive: function ( event, ui ) {
					const sender_area = ui.sender.closest( '.gv-grid' ).data( 'grid-context' );
					const receiver_area = $( this ).closest( '.gv-grid' ).data( 'grid-context' );

					ui.item.attr( 'data-context', receiver_area );
					ui.item.find( '[data-context]' ).attr( 'data-context', receiver_area );
					ui.item.find( '[data-areaid]' ).attr( 'data-areaid', ( _, area_id ) => {
						return area_id.replace( sender_area + '_', receiver_area + '_' );
					} );

					ui.item.find( '[name*="[' + sender_area + '_"]' ).each( function () {
						const name = $( this ).attr( 'name' );
						$( this ).attr( 'name', name.replace( '[' + sender_area + '_', '[' + receiver_area + '_' ) );
					} );
			}
			};

			let connectWith = $( grid ).closest( '.gv-grid' ).data( 'grid-connect' );
			if ( connectWith !== undefined ) {
				options.connectWith = '[data-grid-connect="' + connectWith + '"] > .gv-grid-rows-container';
				options.start = () => {
					$( selector ).find( '[data-grid-connect="' + connectWith + '"]' ).addClass( 'is-receivable' );
				};
				options.stop = () => {
					$( selector ).find( '[data-grid-connect="' + connectWith + '"]' ).removeClass( 'is-receivable' );
				};
			}

			$( grid ).sortable( options );
		} );
	};

	const rowReorder = {
		container( $row ) {
			return $row.closest( '.gv-grid-rows-container' );
		},

		markUnsaved() {
			window?.gvAdminActions?.setUnsavedChanges?.( true );
		},

		announce( message ) {
			window?.wp?.a11y?.speak?.( message, 'polite' );
		},

		syncButtons( $container ) {
			const $rows = $container.children( '.gv-grid-row.is-sortable' );
			const last = $rows.length - 1;
			const single = $rows.length <= 1;

			$rows.each( function ( index ) {
				const $actions = $( this ).children( '.gv-grid-row-actions' );
				const $up = $actions.children( '.gv-grid-row-move-up' );
				const $down = $actions.children( '.gv-grid-row-move-down' );

				$up.attr( 'aria-disabled', index === 0 ? 'true' : 'false' );
				$down.attr( 'aria-disabled', index === last ? 'true' : 'false' );

				$up.add( $down )
					.attr( 'hidden', single ? 'hidden' : null )
					.attr( 'aria-hidden', single ? 'true' : null );
			} );
		},

		syncAll() {
			$( '.gv-grid > .gv-grid-rows-container' ).each( function () {
				rowReorder.syncButtons( $( this ) );
			} );
		},

		move( $row, offset, $button ) {
			const $container = rowReorder.container( $row );
			const $rows = $container.children( '.gv-grid-row.is-sortable' );
			const total = $rows.length;
			const target = $rows.index( $row ) + offset;

			if ( target < 0 ) {
				rowReorder.announce( gvGlobals.row_already_at_top );
				return;
			}

			if ( target > total - 1 ) {
				rowReorder.announce( gvGlobals.row_already_at_bottom );
				return;
			}

			if ( offset < 0 ) {
				$rows.eq( target ).before( $row );
			} else {
				$rows.eq( target ).after( $row );
			}

			rowReorder.markUnsaved();
			rowReorder.syncButtons( $container );
			$button.trigger( 'focus' );
			rowReorder.announce(
				gvGlobals.row_moved_to_position
					.replace( '%1$d', target + 1 )
					.replace( '%2$d', total )
			);

			$( document.body ).trigger( 'gravityview/row-moved', $row );
		}
	};

	const fieldReorder = {
		container( $field ) {
			return $field.closest( '.active-drop' );
		},

		fields( $container ) {
			return $container.children( '.gv-fields:not(.gv-bulk-actions-ghost-field)' );
		},

		announce( message ) {
			window?.wp?.a11y?.speak?.( message, 'polite' );
		},

		syncButtons( $container ) {
			const $fields = fieldReorder.fields( $container );
			const last = $fields.length - 1;
			const single = $fields.length <= 1;

			$fields.each( function ( index ) {
				const $field = $( this );
				const $actions = $field.find( '.gv-field-actions' ).first();
				const $up = $actions.find( '.gv-field-move-up' ).first();
				const $down = $actions.find( '.gv-field-move-down' ).first();

				$up.attr( 'aria-disabled', index === 0 ? 'true' : 'false' );
				$down.attr( 'aria-disabled', index === last ? 'true' : 'false' );

				$up.add( $down )
					.attr( 'hidden', single ? 'hidden' : null )
					.attr( 'aria-hidden', single ? 'true' : null );

				const $areas = fieldReorder.areas( $field );
				const area_index = $areas.index( $container );
				const at_first_area = area_index <= 0;
				const at_last_area = area_index >= $areas.length - 1;

				const left_label = fieldReorder.moveLabel( at_first_area ? $() : $areas.eq( area_index - 1 ), gvGlobals.label_move_field_prev );
				const right_label = fieldReorder.moveLabel( at_last_area ? $() : $areas.eq( area_index + 1 ), gvGlobals.label_move_field_next );

				$actions.find( '.gv-field-move-left' ).first()
					.attr( 'hidden', at_first_area ? 'hidden' : null )
					.attr( 'aria-hidden', at_first_area ? 'true' : null )
					.attr( 'aria-label', left_label )
					.attr( 'title', left_label );
				$actions.find( '.gv-field-move-right' ).first()
					.attr( 'hidden', at_last_area ? 'hidden' : null )
					.attr( 'aria-hidden', at_last_area ? 'true' : null )
					.attr( 'aria-label', right_label )
					.attr( 'title', right_label );

				const has_visible = $actions.children( 'button' ).filter( ':not([hidden])' ).length > 0;
				$actions
					.attr( 'hidden', has_visible ? null : 'hidden' )
					.attr( 'aria-hidden', has_visible ? null : 'true' );
			} );
		},

		syncAll() {
			$( '.active-drop' ).each( function () {
				fieldReorder.syncButtons( $( this ) );
			} );
		},

		/**
		 * The area's title, or an empty string for areas in sortable rows, whose hidden titles are generic column names.
		 */
		areaName( $area ) {
			if ( $area.closest( '.gv-grid-row' ).hasClass( 'is-sortable' ) ) {
				return '';
			}

			return $area
				.closest( '.gv-droppable-area' )
				.children( '.gv-droppable-area-header' )
				.find( '.gv-droppable-area-header-title strong' )
				.first()
				.text()
				.trim();
		},

		/**
		 * The move-button label for a target area: its name when it has one, the given fallback otherwise.
		 */
		moveLabel( $area, fallback ) {
			const name = $area.length ? fieldReorder.areaName( $area ) : '';

			return name ? gvGlobals.label_move_field_to_area.replace( '%s', name ) : fallback;
		},

		/**
		 * All drop areas in the field's row, in DOM order. A column can hold multiple areas, so left/right movement steps by area, not by column.
		 */
		areas( $field ) {
			return $field
				.closest( '[class*="gv-grid-col"]' )
				.parent()
				.children( '[class*="gv-grid-col"]' )
				.find( '.active-drop' );
		},

		adjacentArea( $field, direction ) {
			const $areas = fieldReorder.areas( $field );
			const target = $areas.index( fieldReorder.container( $field ) ) + direction;

			return target < 0 ? $() : $areas.eq( target );
		},

		remapArea( $field, sender, receiver ) {
			if ( ! sender || ! receiver || sender === receiver ) {
				return;
			}

			$field.find( '[name]' ).each( function () {
				const name = $( this ).attr( 'name' );

				if ( name && name.indexOf( sender ) !== -1 ) {
					$( this ).attr( 'name', name.split( sender ).join( receiver ) );
				}
			} );

			$field.find( '[data-areaid]' ).addBack( '[data-areaid]' ).each( function () {
				if ( $( this ).attr( 'data-areaid' ) === sender ) {
					$( this ).attr( 'data-areaid', receiver );
				}
			} );
		},

		moveColumn( $field, direction, $button ) {
			const $targetArea = fieldReorder.adjacentArea( $field, direction );

			if ( ! $targetArea.length ) {
				return;
			}

			const $sourceArea = fieldReorder.container( $field );

			$field.appendTo( $targetArea );
			fieldReorder.remapArea( $field, $sourceArea.attr( 'data-areaid' ), $targetArea.attr( 'data-areaid' ) );

			window?.gvAdminActions?.setUnsavedChanges?.( true );
			fieldReorder.syncButtons( $sourceArea );
			fieldReorder.syncButtons( $targetArea );

			const $pressed = $field.find( direction < 0 ? '.gv-field-move-left' : '.gv-field-move-right' ).first();
			const $opposite = $field.find( direction < 0 ? '.gv-field-move-right' : '.gv-field-move-left' ).first();
			( $pressed.is( '[hidden]' ) ? $opposite : $pressed ).trigger( 'focus' );

			const $areas = fieldReorder.areas( $field );
			fieldReorder.announce(
				gvGlobals.field_moved_to_area
					.replace( '%1$d', $areas.index( $targetArea ) + 1 )
					.replace( '%2$d', $areas.length )
			);

			$( document.body ).trigger( 'gravityview/field-moved', $field );
		},

		move( $field, offset, $button ) {
			const $container = fieldReorder.container( $field );
			const $fields = fieldReorder.fields( $container );
			const total = $fields.length;
			const index = $fields.index( $field );

			if ( index < 0 ) {
				return;
			}

			const target = index + offset;

			if ( target < 0 ) {
				fieldReorder.announce( gvGlobals.field_already_at_top );
				return;
			}

			if ( target > total - 1 ) {
				fieldReorder.announce( gvGlobals.field_already_at_bottom );
				return;
			}

			if ( offset < 0 ) {
				$fields.eq( target ).before( $field );
			} else {
				$fields.eq( target ).after( $field );
			}

			window?.gvAdminActions?.setUnsavedChanges?.( true );
			fieldReorder.syncButtons( $container );
			$button.trigger( 'focus' );
			fieldReorder.announce(
				gvGlobals.field_moved_to_position
					.replace( '%1$d', target + 1 )
					.replace( '%2$d', total )
			);

			$( document.body ).trigger( 'gravityview/field-moved', $field );
		}
	};

	const roving = {
		buttons( $toolbar ) {
			return $toolbar.children( 'button' ).filter( ':not([hidden])' );
		},

		init( $toolbar ) {
			const $all = $toolbar.children( 'button' );
			const $buttons = roving.buttons( $toolbar );

			if ( ! $buttons.length ) {
				return;
			}

			if ( $buttons.filter( '[tabindex="0"]' ).length ) {
				$all.not( $buttons ).attr( 'tabindex', '-1' );
				return;
			}

			$all.attr( 'tabindex', '-1' );

			const $enabled = $buttons.filter( ':not([aria-disabled="true"])' ).first();
			( $enabled.length ? $enabled : $buttons.first() ).attr( 'tabindex', '0' );
		},

		initAll() {
			$( '.post-type-gravityview [role="toolbar"]' ).each( function () {
				roving.init( $( this ) );
			} );
		}
	};

	$( () => {
		activateGrid( document );
		rowReorder.syncAll();
		fieldReorder.syncAll();
		roving.initAll();

		if ( window?.gvAdminActions !== undefined ) {
			window.gvAdminActions.activateGrid = activateGrid;
		}

		$( document )
			.on( 'click', '.gv-grid-row-move-up', function () {
				const $button = $( this );
				rowReorder.move( $button.closest( '.gv-grid-row' ), -1, $button );
			} )
			.on( 'click', '.gv-grid-row-move-down', function () {
				const $button = $( this );
				rowReorder.move( $button.closest( '.gv-grid-row' ), 1, $button );
			} )
			.on( 'click', '.gv-field-move-up', function () {
				const $button = $( this );
				fieldReorder.move( $button.closest( '.gv-fields' ), -1, $button );
			} )
			.on( 'click', '.gv-field-move-down', function () {
				const $button = $( this );
				fieldReorder.move( $button.closest( '.gv-fields' ), 1, $button );
			} )
			.on( 'click', '.gv-field-move-left', function () {
				const $button = $( this );
				fieldReorder.moveColumn( $button.closest( '.gv-fields' ), -1, $button );
			} )
			.on( 'click', '.gv-field-move-right', function () {
				const $button = $( this );
				fieldReorder.moveColumn( $button.closest( '.gv-fields' ), 1, $button );
			} )
			.on( 'sortupdate sortstop', () => {
				rowReorder.syncAll();
				fieldReorder.syncAll();
				roving.initAll();
			} );

		$( document.body ).on( 'gravityview/row-added gravityview/row-removed', () => {
			rowReorder.syncAll();
			roving.initAll();
		} );
		$( document.body ).on( 'gravityview/field-added gravityview/field-removed', () => {
			fieldReorder.syncAll();
			roving.initAll();
		} );
		$( document.body ).on( 'gravityview/view-config-updated gravityview/loaded', () => {
			rowReorder.syncAll();
			fieldReorder.syncAll();
			roving.initAll();
		} );

		$( document )
			.on( 'focusin', '.post-type-gravityview [role="toolbar"] > button', function () {
				const $button = $( this );
				roving.buttons( $button.parent() ).attr( 'tabindex', '-1' );
				$button.attr( 'tabindex', '0' );
			} )
			.on( 'keydown', '.post-type-gravityview [role="toolbar"] > button', function ( e ) {
				const $button = $( this );
				const $buttons = roving.buttons( $button.parent() );
				const rtl = 'rtl' === $button.parent().css( 'direction' );
				const index = $buttons.index( $button );
				let next;

				switch ( e.key ) {
					case 'ArrowRight':
						next = index + ( rtl ? -1 : 1 );
						break;
					case 'ArrowLeft':
						next = index + ( rtl ? 1 : -1 );
						break;
					case 'Home':
						next = 0;
						break;
					case 'End':
						next = $buttons.length - 1;
						break;
					default:
						return;
				}

				e.preventDefault();
				$buttons.eq( Math.max( 0, Math.min( $buttons.length - 1, next ) ) ).trigger( 'focus' );
			} );

		$( document.body )
			.on( 'gravityview/dialog-opened', ( e, dialog ) => {
				$( dialog ).closest( '.gv-grid-row' ).addClass( 'gv-grid-row--dialog' );
				$( dialog ).closest( '.gv-fields' ).addClass( 'gv-fields--dialog' );
			} )
			.on( 'gravityview/dialog-closed', ( e, dialog ) => {
				$( dialog ).closest( '.gv-grid-row' ).removeClass( 'gv-grid-row--dialog' );
				$( dialog ).closest( '.gv-fields' ).removeClass( 'gv-fields--dialog' );
			} );

		$( document ).on( 'click', '.gv-grid-row-delete', function () {
			const $row = $( this ).closest( '.gv-grid-row' );
			const $fields = $row.find( '.gv-fields' );

			if (
				$fields.length > 0
				&& !confirm( $( this ).data( 'confirm' ) )
			) {
				return;
			}

			$row.fadeOut( 'fast', () => {
				$fields.each( function () {
					$( this ).remove();
					$( document.body ).trigger( 'gravityview/field-removed', $( this ) );
				} );

				$row.remove();
				$( document.body ).trigger( 'gravityview/row-removed', $row );
			} );
		} );

		// Open the row settings dialog when clicking the gear icon.
		$( document ).on( 'click', '.gv-grid-row-settings-toggle', function () {
			const $row = $( this ).closest( '.gv-grid-row' );
			const $dialog = $row.find( '.gv-row-settings-dialog' ).filter( function () {
				return $( this ).closest( '.gv-grid-row' ).is( $row );
			} ).first();

			if ( $dialog.length && window?.gvAdminActions?.showDialog ) {
				const buttons = [
					{
						text: gvGlobals.label_close,
						class: 'button button-link',
						click: function () {
							$( this ).dialog( 'close' );
						}
					}
				];

				window.gvAdminActions.showDialog( $dialog, buttons );
			}
		} );

		// Validate duplicate Custom HTML IDs across rows.
		$( document ).on( 'input', '.gv-row-settings-dialog input[name$="[custom_id]"]', function () {
			const $input = $( this );
			const value = $input.val().trim();
			const $container = $input.closest( '.gv-setting-container' );

			$container.find( '.gv-row-id-duplicate-warning' ).remove();

			if ( ! value ) {
				return;
			}

			const isDuplicate = $( '.gv-row-settings-dialog input[name$="[custom_id]"]' )
				.not( this )
				.filter( function () {
					return $( this ).val().trim() === value;
				} )
				.length > 0;

			if ( isDuplicate ) {
				$container.append(
					'<span class="gv-row-id-duplicate-warning" style="color: #d63638; display: block; margin-top: 4px;">'
					+ gvGlobals.label_duplicate_row_id
					+ '</span>'
				);
			}
		} );

		$( document )
			.on( 'click', '.gv-grid-add-row .gv-toggle', function ( e ) {
				const $toggle = $( this );
				const $add_row = $toggle.closest( '.gv-grid-add-row' );
				const $wrapper = $add_row.find( '.gv-grid-row-layouts-wrapper' );
				const is_open = ! $add_row.hasClass( 'open' );

				$add_row.toggleClass( 'open', is_open );
				$toggle.attr( 'aria-expanded', is_open );
				$wrapper.prop( 'inert', ! is_open );

				if ( is_open ) {
					$wrapper.find( '[data-add-row]' ).first().trigger( 'focus' );
				}
			} )
			.on( 'keydown', '.gv-grid-add-row.open', function ( e ) {
				if ( 'Escape' !== e.key ) {
					return;
				}

				const $add_row = $( this );
				const $toggle = $add_row.find( '.gv-toggle' );

				$add_row.removeClass( 'open' );
				$toggle.attr( 'aria-expanded', false ).trigger( 'focus' );
				$add_row.find( '.gv-grid-row-layouts-wrapper' ).prop( 'inert', true );
			} )
			.on( 'click', '.gv-grid-add-row [data-add-row]', function ( e ) {
				const $add_row_button = $( this );
				const $add_row = $( this ).closest( '.gv-grid-add-row' );

				const zone = $add_row_button.data( 'add-row' );
				const template_id = $add_row_button.data( 'template-id' );
				const type = $add_row_button.data( 'type' );
				const row_type = $add_row_button.data( 'row-type' );

				$.post( ajaxurl, {
					action: 'gv_create_row',
					template_id,
					nonce: gvGlobals.nonce,
					zone,
					type,
					row_type,
					dataType: 'json'
				} )
					.always( () => {
						$add_row.removeClass( 'open' );
						$add_row.find( '.gv-toggle' ).attr( 'aria-expanded', false ).trigger( 'focus' );
						$add_row.find( '.gv-grid-row-layouts-wrapper' ).prop( 'inert', true );
					} )
					.done( ( response => {
						const result = JSON.parse( response );
						const $row = $( result?.row );

						$row.appendTo( $add_row.closest( '.gv-grid' ).find( '> .gv-grid-rows-container' ) );

						$( document.body ).trigger(
							'gravityview/row-added',
							$row,
							{
								type,
								row_type,
								zone,
								template_id
							}
						);

						window?.gvAdminActions?.initTooltips();
						window?.gvAdminActions?.initDroppables( $row );
					} ) );
			} );
	} );
} )( jQuery );
