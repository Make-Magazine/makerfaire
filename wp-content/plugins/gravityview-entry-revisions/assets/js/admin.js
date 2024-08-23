/* jshint esversion: 6 */
jQuery( document ).ready( function ( $ ) {

	var $table = $( 'form#entry_form' ), NOTICE_TIMEOUT = 6000, LOADER_MIN_TIME = 600;

	$table
		.on( 'change', '.toggle-all-revisions', function () {
			if ( $( this ).is( ':checked' ) ) {
				$( '.diff-deletedline input[type=\'radio\']', $table ).trigger( 'click' );
			} else {
				$( '.diff-addedline input[type=\'radio\']', $table ).trigger( 'click' );
			}
		} )
		.on( 'change', function ( e ) {

			var button_text = gvRevisions.restore.plural;

			if ( 1 === $( '.diff-deletedline .revision_checkbox:checked', $table ).length ) {
				button_text = gvRevisions.restore.singular;
			}

			// Show and hide the submit button based on whether any changes are selected
			if ( $( '.diff-deletedline input[type=\'radio\']', $table ).filter( ':checked' ).length > 0 ) {
				$( '.button-primary', $table ).prop( 'value', button_text ).fadeIn( 150 );
			} else {
				$( '.button-primary', $table ).fadeOut( 150, function () {
					$( this ).prop( 'value', button_text );
				} );
			}

			// If all the revision radios are checked, check the "all" checkbox
			$( '.toggle-all-revisions' ).prop( 'checked', (0 === $( '.diff-deletedline input[type=\'radio\']', $table ).not( ':checked' ).length) );
		} )
		.on( 'submit', function ( e ) {
			return confirm( gvRevisions.confirm );
		} )
		.on( 'click', 'td', function ( e ) {
			if ( $( e.target ).is( 'td' ) ) {
				$( 'input[type="radio"]', e.target ).click();
			}
		} )
		.on( 'click', 'input[type="radio"]', function () {

			$( this ).prop( 'checked', true );

			// Only check the current <td> and its siblings
			$( this ).parents( 'tr' ).find( 'input[type="radio"]' ).each( function () {
				$( this ).parents( 'td' ).toggleClass( 'diff-enabled', this.checked );
			} );
		} );

	$( '.toggle-all-revisions', $table ).first().trigger( 'change' );

	var formRevisions = function () {

		if ( !$( '#tab_form-revisions' ).length ) {
			return;
		}

		var showNotice = function ( text, type ) {
				$( '#gv-notice' ).remove();
				var $notice = $( '<div id="gv-notice" class="notice notice-' + type + ' gv-notice" style="display: none"></div>' );
				$( '#gf-admin-notices-wrapper' ).append( $notice.html( text ) );
				$notice.fadeIn();
				setTimeout( function () {
					$notice.fadeOut( function () {
						$notice.remove();
					} );
				}, NOTICE_TIMEOUT );
			},

			doRevisionAction = function ( data, callback = null ) {
				var $loader = $( '<span class="gform-loader gform-loader--simple"></span>' );
				$( '#notification_list_form' ).find( '.top' ).after( $loader );

				$.post( ajaxurl, data, function ( result ) {
					if ( callback ) {
						callback( result );
					}
					setTimeout( function () {
						$loader.remove();
						var type = result.success ? 'info' : 'error';
						showNotice( result.data, type );
					}, LOADER_MIN_TIME );
				} );
			},

			stopEvent = function ( e ) {
				e.preventDefault();
				e.stopPropagation();
			};

		$( '.gv-form-revision-restore' ).click( function ( e ) {
			stopEvent( e );

			if ( confirm( gvRevisions.confirmRestore ) ) {
				doRevisionAction( {
					action: 'gv_restore_revision',
					id: $( this ).data( 'id' ),
					form_id: $( this ).data( 'form-id' ),
					nonce: $( this ).data( 'nonce' ),
				} );
			}
		} );

		$( '.gv-form-revision-delete' ).click( function ( e ) {
			stopEvent( e );
			var $el = $( this );

			if ( confirm( gvRevisions.confirmDelete ) ) {
				doRevisionAction( {
					action: 'gv_delete_revision',
					id: $el.data( 'id' ),
					nonce: $el.data( 'nonce' ),
				}, function ( result ) {
					if ( result.success ) {
						var $row = $el.closest( 'tr' );
						$row.fadeOut( function () {
							$row.remove();
						} );
					}
				} );
			}
		} );

		$( '#doaction' ).click( function ( e ) {
			stopEvent( e );

			var $el = $( this ), $form = $( this ).closest( 'form' ),
				$checked = $form.find( 'input[name="revisions[]"]:checked' ),
				ids = $checked.map( function () {
					return this.value;
				} ).get();

			if ( ids.length && 'delete' === $( '#bulk-action-selector-top' ).val() ) {

				let confirmText = gvRevisions.confirmDelete;

				if ( ids.length > 1 ) {
					confirmText = gvRevisions.confirmDeletes;
				}

				if ( !confirm( confirmText ) ) {
					return false;
				}

				$el.attr( 'disabled', 'disabled' );

				doRevisionAction( {
					action: 'gv_delete_revisions',
					ids: ids,
					nonce: $( '#gform_form_revision_list_action' ).val(),
				}, function ( result ) {
					$el.removeAttr( 'disabled' );
					if ( result.success ) {
						$rows = $checked.closest( 'tr' );
						$rows.fadeOut( function () {
							$rows.remove();
						} );
						$( '#cb-select-all-1,#cb-select-all-2' ).prop( 'checked', false );
					}
				} );
			}
		} );
	};

	formRevisions();

} );
