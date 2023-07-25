<?php

namespace GravityKit\MultipleForms;

/**
 * Class AJAX
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms
 */
class AJAX extends AbstractSingleton {

	/**
	 * @var string AJAX action to get rendered form(s) fields
	 */
	public const AJAX_ACTION_GET_FIELD_MARKUP = 'gv_multiple_forms_get_field_markup';

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void {
		add_action( 'wp_ajax_' . static::AJAX_ACTION_GET_FIELD_MARKUP, [ $this, 'get_field_markup' ] );

	}

	/**
	 * AJAX action to get HTML markup for rendered form(s) fields using all 3 contexts
	 *
	 * @return void Exit with JSON response (HTML markup) or terminate request with error code
	 */
	public function get_field_markup(): void {

		// Validate AJAX request
		$is_valid_nonce  = wp_verify_nonce( rgpost( 'nonce' ), 'gravityview-multiple-forms' );
		$is_valid_action = static::AJAX_ACTION_GET_FIELD_MARKUP === sanitize_text_field( rgpost( 'action' ) );
		$form_ids        = rgpost( 'form_ids' );

		if ( ! $is_valid_nonce || ! $is_valid_action || empty( $form_ids ) ) {
			// Return 'forbidden' response if nonce is invalid, otherwise it's a 'bad request'
			wp_die( false, false, [ 'response' => ( ! $is_valid_nonce ) ? 403 : 400 ] );
		}

		$data = [
			'directory' => '',
			'single'    => '',
			'edit'      => '',
		];
		foreach ( $data as $context => $markup ) {
			ob_start();

			do_action( 'gravityview_render_field_pickers', $context, $form_ids );

			$data[ $context ] = trim( ob_get_clean() );
		}

		wp_send_json_success( $data );
	}
}
