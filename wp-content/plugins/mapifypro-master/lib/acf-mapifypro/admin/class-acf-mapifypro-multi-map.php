<?php

/**
 * The class responsible for customizing the `Multi Map` options page.
 * 
 * @since    1.0.0
 */

/**
 * class Acf_Mapifypro_Multi_Map
 * 
 * @since    1.0.0
 */
class Acf_Mapifypro_Multi_Map {
	
	/**
	 * Remove submit button from multi-map option page.
	 * Then add our own "Generate Shorcode" button as action to display generated shortcode on modal.
	 * This action run on `acf/input/admin_head` hook
	 * 
	 * @since    1.0.0
	 */
	public function acf_admin_head() {
		if ( ! $this->is_right_screen() ) return;

		// remove submit button
		remove_meta_box( 'submitdiv', 'acf_options_page', 'side' );

		// add generate-shortcode button
		add_meta_box( 'mpfymultimap', __( 'Action', 'acf-mapifypro' ), array( $this, 'postbox_mpfymultimap' ), 'acf_options_page', 'side', 'high' );

	}

	/**
	 * Metabox for generate shorcode button
	 * 
	 * @since    1.0.0
	 */
	public function postbox_mpfymultimap( $post, $args ) {		
		?>
		<div id="minor-publishing">
			<?php esc_html_e( 'Press button below to generate Multi Map shortcode based on your settings aside.', 'acf-mapifypro' ) ?>
		</div>

		<div id="major-publishing-actions">
			<div id="publishing-action">
				<span class="spinner"></span>
				<a href="javascript:;" class="button button-primary button-large" id="mpfy-multi-map-generate-shorcode"><?php esc_html_e( 'Generate Shortcode', 'acf-mapifypro' ) ?></a>
			</div>
			
			<div class="clear"></div>		
		</div>

		<div id="mpfy-multi-map-shortcode-modal" style="display:none;" title="<?php esc_attr_e( 'Multi Map Shortcode', 'acf-mapifypro' ) ?>">
			<table class="table-content">
				<tr>
					<td>
						<textarea id="mpfy-multi-map-shortcode" rows=3></textarea>
						<label><?php esc_html_e( 'Use above shorcode to display the Multi Map based on your settings.', 'acf-mapifypro' ) ?></label>
					</td>
				</tr>
			</table>
		</div>

		<?php
	}

	/**
	 * Register the scripts for this multi-map page.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_right_screen() ) return;

		// register jQuery UI dialog script
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-dialog' );	

		// register jQuery UI dialog style
    	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	}

	/**
	 * Make sure the current screen is multi-map page
	 * 
	 * @since    1.0.0
	 * @return   bool    Will return true on the right page, otherwise false.
	 * @access   private
	 */
	private function is_right_screen() {
		if ( ! function_exists( 'get_current_screen' ) ) return false;

		$sc = get_current_screen();

		// must be run at the multi-map option page
		if ( ! isset( $sc->id ) || 'mapifypro_page_mapifypro-multi-map' !== $sc->id ) {
			return false;
		} else {
			return true;
		}
	}

}