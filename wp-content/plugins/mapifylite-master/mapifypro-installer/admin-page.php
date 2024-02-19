<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php wp_nonce_field( 'mapifypro_installer_action_nonce', 'nonce' ); ?>

<div class="wrap" id="mapifypro-installer">
	<div class="postbox ">
		<div class="hndle ui-sortable-handle">
			<span><?php _e( 'MapifyPro Installer', 'mpfy' ) ?></span>
		</div>
		<div class="inside">
			<div class="field-group">
				<label><?php _e( 'MapifyPro API Key', 'mpfy' ) ?></label>
				<input type="text" name="mapifypro_api_key" id="mapifypro_api_key" value="<?php esc_attr_e( $api_key ) ?>" placeholder="Your MapifyPro API key here">
				<span><?php _e( 'You can get your API key from your MapifyPro account here', 'mpfy' ) ?>: <a href="https://mapifypro.com/my-account/api-keys/" target="_blank">https://mapifypro.com/my-account/api-keys/</a></span>
			</div>
			
			<div class="field-group">
				<a href="javascript:;" class="button" id="install-mapifypro"><?php _e( 'Verify and Install MapifyPro', 'mpfy' ) ?></a>
				<span class="spinner"></span>
			</div>

			<div class="field-group">
				<label><?php _e( 'Output', 'mpfy' ) ?></label>
				<div id="mapifypro-installer-output"><span><?php _e( 'Nothing to show at the moment.', 'mpfy' ) ?></span></div>
			</div>
		</div>
	</div>
</div>