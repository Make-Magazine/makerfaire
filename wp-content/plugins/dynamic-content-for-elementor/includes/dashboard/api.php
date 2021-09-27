<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor\Assets;
use DynamicContentForElementor\License;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Notice;
class Api
{
    public static function display_form()
    {
        ?>
	<div class="wrap">
	  <h1><?php 
        echo esc_html(get_admin_page_title());
        ?></h1>

		<form action="" method="post" autocomplete="off">
			<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if (isset($_POST['save-dce-apis'])) {
            update_option('dce_google_maps_api', sanitize_text_field($_POST['dce_google_maps_api']));
            if (isset($_POST['dce_google_maps_api_acf'])) {
                update_option('dce_google_maps_api_acf', sanitize_text_field($_POST['dce_google_maps_api_acf']));
            } else {
                update_option('dce_google_maps_api_acf', '');
            }
            update_option('dce_paypal_api_client_id_live', sanitize_text_field($_POST['dce_paypal_api_client_id_live']));
            update_option('dce_paypal_api_client_secret_live', sanitize_text_field($_POST['dce_paypal_api_client_secret_live']));
            update_option('dce_paypal_api_client_id_sandbox', sanitize_text_field($_POST['dce_paypal_api_client_id_sandbox']));
            update_option('dce_paypal_api_client_secret_sandbox', sanitize_text_field($_POST['dce_paypal_api_client_secret_sandbox']));
            update_option('dce_paypal_api_currency', sanitize_text_field($_POST['dce_paypal_api_currency']));
            update_option('dce_paypal_api_mode', sanitize_text_field($_POST['dce_paypal_api_mode']));
            update_option('dce_stripe_api_publishable_key_live', sanitize_text_field($_POST['dce_stripe_api_publishable_key_live']));
            update_option('dce_stripe_api_secret_key_live', sanitize_text_field($_POST['dce_stripe_api_secret_key_live']));
            update_option('dce_stripe_api_publishable_key_test', sanitize_text_field($_POST['dce_stripe_api_publishable_key_test']));
            update_option('dce_stripe_api_secret_key_test', sanitize_text_field($_POST['dce_stripe_api_secret_key_test']));
            update_option('dce_stripe_api_mode', sanitize_text_field($_POST['dce_stripe_api_mode']));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $dce_google_maps_api = get_option('dce_google_maps_api');
        $dce_google_maps_api_acf = get_option('dce_google_maps_api_acf');
        $dce_paypal_api_currency = get_option('dce_paypal_api_currency', 'USD');
        $dce_paypal_api_client_id_live = get_option('dce_paypal_api_client_id_live');
        $dce_paypal_api_client_secret_live = get_option('dce_paypal_api_client_secret_live');
        $dce_paypal_api_client_id_sandbox = get_option('dce_paypal_api_client_id_sandbox');
        $dce_paypal_api_client_secret_sandbox = get_option('dce_paypal_api_client_secret_sandbox');
        $dce_paypal_api_mode = get_option('dce_paypal_api_mode');
        $dce_stripe_api_publishable_key_live = get_option('dce_stripe_api_publishable_key_live');
        $dce_stripe_api_secret_key_live = get_option('dce_stripe_api_secret_key_live');
        $dce_stripe_api_publishable_key_test = get_option('dce_stripe_api_publishable_key_test');
        $dce_stripe_api_secret_key_test = get_option('dce_stripe_api_secret_key_test');
        $dce_stripe_api_mode = get_option('dce_stripe_api_mode');
        ?>
			<table class="widefat dce-form-table">
				<thead>
					<tr>
						<th>
							<h3>Google Maps</h3>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<label style="font-weight: bold;" for="dce-apis-gmaps">
								Google Maps JavaScript API Key
							</label>
							<input class="dce-apis" type="text" name="dce_google_maps_api" id="dce-apis-gmaps" value="<?php 
        echo isset($dce_google_maps_api) ? $dce_google_maps_api : '';
        ?>"><br>
							<?php 
        if (Helper::is_acf_active()) {
            ?>
							<input class="dce-apis dce-apis-gmaps_acf" type="checkbox" name="dce_google_maps_api_acf" id="dce-apis-gmaps_acf"<?php 
            echo !empty($dce_google_maps_api_acf) ? ' checked' : '';
            ?>> <label for="dce-apis-gmaps_acf"><?php 
            _e('Set this API also in ACF Configuration.', 'dynamic-content-for-elementor');
            ?> <a href="https://www.advancedcustomfields.com/blog/google-maps-api-settings/" target="_blank"><?php 
            _e('Why?', 'dynamic-content-for-elementor');
            ?></a></label>
							<?php 
        }
        ?>
							<div class="dce-apis-gmaps-note">&nbsp;To learn more about the API Key for Google Maps <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a></div>
						</td>
					</tr>
				</tbody>
			</table>

			<?php 
        submit_button('Save APIs');
        ?>

			<table class="widefat dce-form-table">
				<thead>
					<tr>
						<th colspan="2">
							<h3>PayPal</h3>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<label for="cars"><?php 
        _e('PayPal Mode', 'dynamic-content-for-elementor');
        ?></label>
							<select id="dce-paypal-api-mode" name="dce_paypal_api_mode">
								<option value="sandbox" <?php 
        echo isset($dce_paypal_api_mode) && $dce_paypal_api_mode == 'sandbox' ? 'selected' : '';
        ?>>Sandbox</option>
								<option value="live" <?php 
        echo isset($dce_paypal_api_mode) && $dce_paypal_api_mode == 'live' ? 'selected' : '';
        ?>>Live</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<label for="dce-paypal-api-currency"><?php 
        _e('PayPal Currency Code', 'dynamic-content-for-elementor');
        ?><sup><a href="https://developer.paypal.com/docs/reports/sftp-reports/reference/paypal-supported-currencies/">(list)</a></label>
							<input type="text" id="dce-paypal-api-currency" value="<?php 
        echo $dce_paypal_api_currency;
        ?>" name="dce_paypal_api_currency">
						</td>
					</tr>
					<td>
						<label style="font-weight: bold;" for="dce-api-paypal-id-sandbox">
							Sandbox Client ID
						</label>
						<input class="dce-apis" type="text" name="dce_paypal_api_client_id_sandbox" id="dce-api-paypal-id-sandbox" value="<?php 
        echo isset($dce_paypal_api_client_id_sandbox) ? $dce_paypal_api_client_id_sandbox : '';
        ?>"><br>
					</td>
					<td>
						<label style="font-weight: bold;" for="dce-api-paypal-secret-sandbox">
							Sandbox Client Secret
						</label>
						<input class="dce-apis" type="password" name="dce_paypal_api_client_secret_sandbox" id="dce-api-paypal-secret-sandbox" value="<?php 
        echo isset($dce_paypal_api_client_secret_sandbox) ? $dce_paypal_api_client_secret_sandbox : '';
        ?>"><br>
					</td>
				</tr>
				<tr>
					<td>
						<label style="font-weight: bold;" for="dce-api-paypal-id-live">
							Live Client ID
						</label>
						<input class="dce-apis" type="text" name="dce_paypal_api_client_id_live" id='dce-api-paypal-id-live' value="<?php 
        echo isset($dce_paypal_api_client_id_live) ? $dce_paypal_api_client_id_live : '';
        ?>"><br>
					</td>
					<td>
						<label style="font-weight: bold;" for="dce-api-paypal-secret-live">
							Live Client Secret
						</label>
						<input class="dce-apis" type="password" name="dce_paypal_api_client_secret_live" id="dce-api-paypal-secret-live" value="<?php 
        echo isset($dce_paypal_api_client_secret_live) ? $dce_paypal_api_client_secret_live : '';
        ?>"><br>
					</td>
				</tr>
				<tr>
				</tbody>
			</table>

			<?php 
        submit_button('Save APIs');
        ?>

			<table class="widefat dce-form-table">
				<thead>
					<tr>
						<th colspan="2">
							<h3>Stripe</h3>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							<label for="cars"><?php 
        _e('Stripe Mode', 'dynamic-content-for-elementor');
        ?></label>
							<select id="dce-stripe-api-mode" name="dce_stripe_api_mode">
								<option value="sandbox" <?php 
        echo isset($dce_stripe_api_mode) && $dce_stripe_api_mode == 'test' ? 'selected' : '';
        ?>>Test</option>
								<option value="live" <?php 
        echo isset($dce_stripe_api_mode) && $dce_stripe_api_mode == 'live' ? 'selected' : '';
        ?>>Live</option>
							</select>
						</td>
					</tr>
					<td>
						<label style="font-weight: bold;" for="dce-api-stripe-id-sandbox">
							Test Publishable Key
						</label>
						<input class="dce-apis" type="text" name="dce_stripe_api_publishable_key_test" id="dce-api-stripe-id-sandbox" value="<?php 
        echo isset($dce_stripe_api_publishable_key_test) ? $dce_stripe_api_publishable_key_test : '';
        ?>"><br>
					</td>
					<td>
						<label style="font-weight: bold;" for="dce-api-stripe-secret-sandbox">
							Test Secret Key
						</label>
						<input class="dce-apis" type="password" name="dce_stripe_api_secret_key_test" id="dce-api-stripe-secret-sandbox" value="<?php 
        echo isset($dce_stripe_api_secret_key_test) ? $dce_stripe_api_secret_key_test : '';
        ?>"><br>
					</td>
				</tr>
				<tr>
					<td>
						<label style="font-weight: bold;" for="dce-api-stripe-id-live">
							Live Publishable Key
						</label>
						<input class="dce-apis" type="text" name="dce_stripe_api_publishable_key_live" id='dce-api-stripe-id-live' value="<?php 
        echo isset($dce_stripe_api_publishable_key_live) ? $dce_stripe_api_publishable_key_live : '';
        ?>"><br>
					</td>
					<td>
						<label style="font-weight: bold;" for="dce-api-stripe-secret-live">
							Live Secret Key
						</label>
						<input class="dce-apis" type="password" name="dce_stripe_api_secret_key_live" id="dce-api-stripe-secret-live" value="<?php 
        echo isset($dce_stripe_api_secret_key_live) ? $dce_stripe_api_secret_key_live : '';
        ?>"><br>
					</td>
				</tr>
				<tr>
				</tbody>
			</table>

			<input type="hidden" name="save-dce-apis" value="1" />
			<?php 
        submit_button('Save APIs');
        ?>
		</form>
		  <?php 
    }
}
