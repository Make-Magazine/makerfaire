<?php

$sbsw_license    = trim( get_option( 'sbsw_license_key' ) );
$sbsw_status     = get_option( 'sbsw_license_status' );
?>

	<form name="form1" method="post" action="options.php">

		<?php settings_fields('sbsw_license'); ?>

		<?php
		// data to send in our API request
		$sbsw_api_params = array(
			'edd_action'=> 'check_license',
			'license'   => $sbsw_license,
			'item_name' => urlencode( SBSW_PLUGIN_EDD_NAME ) // the name of our product in EDD
		);

		// Call the custom API.
		$sbsw_response = wp_remote_get( add_query_arg( $sbsw_api_params, SBSW_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

		// decode the license data
		$sbsw_license_data = (array) json_decode( wp_remote_retrieve_body( $sbsw_response ) );

		//Store license data in db unless the data comes back empty as wasn't able to connect to our website to get it
		if( !empty($sbsw_license_data) ) update_option( 'sbsw_license_data', $sbsw_license_data );

		?>

		<table class="form-table">
			<tbody>
			<h3><?php _e('License', $text_domain ); ?></h3>

			<tr valign="top">
				<th scope="row" valign="top">
					<?php _e('Enter your license key', $text_domain ); ?>
				</th>
				<td>
					<input id="sbsw_license_key" name="sbsw_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $sbsw_license ); ?>" />

					<?php if( false !== $sbsw_license ) { ?>

						<?php if( $sbsw_status !== false && $sbsw_status == 'valid' ) { ?>
							<?php wp_nonce_field( 'sbsw_nonce', 'sbsw_nonce' ); ?>
							<input type="submit" class="button-secondary" name="sbsw_license_deactivate" value="<?php _e('Deactivate License', $text_domain ); ?>"/>

							<?php if($sbsw_license_data['license'] == 'expired'){ ?>
								<span class="sbsw_license_status" style="color:red;"><?php _e('Expired', $text_domain ); ?></span>
							<?php } else { ?>
								<span class="sbsw_license_status" style="color:green;"><?php _e('Active', $text_domain ); ?></span>
							<?php } ?>

						<?php } else {
							wp_nonce_field( 'sbsw_nonce', 'sbsw_nonce' ); ?>
							<input type="submit" class="button-secondary" name="sbsw_license_activate" value="<?php _e('Activate License', $text_domain ); ?>"/>

							<?php if($sbsw_license_data['license'] == 'expired'){ ?>
								<span class="sbsw_license_status" style="color:red;"><?php _e('Expired', $text_domain ); ?></span>
							<?php } else { ?>
								<span class="sbsw_license_status" style="color:red;"><?php _e('Inactive', $text_domain ); ?></span>
							<?php } ?>

						<?php } ?>
					<?php } ?>

					<br />
					<i style="color: #666; font-size: 11px;"><?php _e('The license key you received when purchasing the plugin.', $text_domain ); ?></i>
					<?php global $sbsw_download_id; ?>
					<p style="font-size: 13px;">
						<a href='https://smashballoon.com/checkout/?edd_license_key=<?php echo trim($sbsw_license) ?>&amp;download_id=<?php echo $sbsw_download_id ?>' target='_blank'><?php _e("Renew your license", $text_domain ); ?></a>
						&nbsp;&nbsp;&nbsp;&middot;
						<a class="sbspf_tooltip_link" href="JavaScript:void(0);"><?php _e("Upgrade your license", $text_domain ); ?></a>
						<span class="sbspf_tooltip sbspf_more_info">
                                    <?php _e("You can upgrade your license in two ways:", $text_domain); ?><br />
                                    &bull;&nbsp; <?php echo sprintf( __( "Log into %s and click on the 'Upgrade my License' tab", $text_domain ), '<a href="https://smashballoon.com/account" target="_blank">' . __('your Account', $text_domain ) . '</a>'); ?><br />
                                    &bull;&nbsp; <a href='https://smashballoon.com/contact/?utm_source=plugin-pro&utm_campaign=sbsw' target='_blank'><?php _e( 'Contact us directly', $text_domain ); ?></a>
                                </span>

					</p>


				</td>
			</tr>

			</tbody>
		</table>


		<p style="margin: 20px 0 0 0; height: 35px;">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>">
			<button name="sbsw-test-license" id="sbsw-test-license-btn" class="button button-secondary"><?php _e( 'Test Connection', $text_domain ); ?></button>
		</p>

		<div id="sbsw-test-license-connection" style="display: none;">
			<?php
			if( isset( $sbsw_license_data['item_name']) ){
				echo '<p class="sbsw-success" style="display: inline-block; padding: 10px 15px; border-radius: 5px; margin: 0; background: #dceada; border: 1px solid #6ca365; color: #3e5f1c;"><i class="fa fa-check"></i> &nbsp;Connection Successful</p>';
			} else {
				echo '<div class="sbsw-test-license-error">';
				highlight_string( var_export($sbsw_response, true) );
				echo '<br />';
				highlight_string( var_export($sbsw_license_data, true) );
				echo '</div>';
			}
			?>
		</div>
		<script type="text/javascript">
            jQuery('#sbsw-test-license-btn').on('click', function(e){
                e.preventDefault();
                jQuery('#sbsw-test-license-connection').toggle();
            });
		</script>
	</form>
