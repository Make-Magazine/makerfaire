<?php
/**
 * @file inline-javascript.php
 * @global GravityView_Edit_Entry_Render $object
 *
 * Newlines are stripped from this content, so avoid using inline comments, as this will break the JavaScript.
 * Even though `DeleteFile` can contain the logic of `EndDeleteFile`, we keep this separate an Ajax Response
 * might call the `EndDeleteFile` method.
 *
 * The `gvFixDeleteFileButtons` block gives Gravity Forms' uploaded-file delete buttons an explicit
 * `type="button"`. GF renders them without a type, so they default to `type="submit"` and become the
 * form's implicit default button: pressing Enter in any field then triggers the delete instead of
 * submitting, wiping the uploaded file and its Post Image metadata. The MutationObserver re-applies the
 * fix to buttons GF recreates after a new file is uploaded.
 */

?>
<script type="text/javascript">

	function DeleteFile( leadId, fieldId, deleteButton ) {
		if ( confirm( '<?php echo esc_js( __( "Would you like to permanently delete this file? 'Cancel' to stop. 'OK' to delete", 'gk-gravityview' ) ); ?>' ) ) {
			const fileIndex = jQuery( deleteButton ).parent().index();
			const preview_div = jQuery( deleteButton ).closest( '.gfield--type-fileupload' );
			preview_div.find( 'button.gform_button_select_files' ).prop( "disabled", false );

			EndDeleteFile( fieldId, fileIndex );
			return true;
		}
	}

	function EndDeleteFile( fieldId, fileIndex ) {
		const previewFileSelector = "#preview_existing_files_" + fieldId + " .ginput_preview";
		const $previewFiles = jQuery( previewFileSelector );

		const $preview_div = $previewFiles.closest( '.gfield--type-fileupload' );
		const $input_field = $preview_div.find( 'input[name="input_' + fieldId + '"]' );

		if ( $input_field.attr( 'type' ) === 'hidden' ) {
			const files = JSON.parse( $input_field.val() );
			files.splice( fileIndex, 1 );
			$input_field.val( JSON.stringify( files ) );
		}

		$previewFiles.eq( fileIndex ).remove();
		const $visiblePreviewFields = jQuery( previewFileSelector );
		if ( $visiblePreviewFields.length === 0 ) {
			jQuery( '#preview_' + fieldId ).hide();
			jQuery( '#upload_' + fieldId ).show( 'slow' );

			if ( $input_field.attr( 'type' ) === 'file' ) {
				$input_field.attr( 'disabled', false );
			}
		}
	}

	( function() {
		const fixDeleteFileButtons = function( root ) {
			const scope = root && root.querySelectorAll ? root : document;
			const buttons = scope.querySelectorAll( 'button.gform_delete_file:not([type])' );
			buttons.forEach( function( button ) {
				button.setAttribute( 'type', 'button' );
			} );
		};
		const gvFixDeleteFileButtons = function() {
			fixDeleteFileButtons( document );
			if ( ! window.MutationObserver ) {
				return;
			}
			const observer = new MutationObserver( function( mutations ) {
				mutations.forEach( function( mutation ) {
					mutation.addedNodes.forEach( function( node ) {
						if ( node.nodeType !== 1 ) {
							return;
						}
						if ( node.matches && node.matches( 'button.gform_delete_file:not([type])' ) ) {
							node.setAttribute( 'type', 'button' );
						}
						fixDeleteFileButtons( node );
					} );
				} );
			} );
			observer.observe( document.body, { childList: true, subtree: true } );
		};
		if ( document.readyState === 'loading' ) {
			document.addEventListener( 'DOMContentLoaded', gvFixDeleteFileButtons );
		} else {
			gvFixDeleteFileButtons();
		}
	}() );

</script>
