<?php

namespace DynamicContentForElementor\AdminPages;

use DynamicContentForElementor\Assets;
use DynamicContentForElementor\LicenseSystem;
use DynamicContentForElementor\Helper;
class License
{
    public static function show_license_form()
    {
        ?>

		<div class="wrap">

		<h1><?php 
        echo esc_html(get_admin_page_title());
        ?></h1>

		<?php 
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            if (!isset($_POST['dce-settings-page']) || !wp_verify_nonce($_POST['dce-settings-page'], 'dce-settings-page')) {
                wp_die('Nonce verification error.');
            }
        }
        $licence_key = DCE_LICENSE;
        if (isset($_POST['licence_key'])) {
            if (\intval($_POST['licence_status'])) {
                $res = LicenseSystem::call_api('deactivate', $licence_key);
                if ($res) {
                    update_option('dce_license_activated', 0);
                }
            }
            $licence_key = sanitize_text_field($_POST['licence_key']);
            if (DCE_LICENSE != $licence_key || !\intval($_POST['licence_status'])) {
                // aggiorno la chiave di licenza inserita
                update_option('dce_license_key', $licence_key);
                // provo ad attivare con la nuova chiave
                $res = LicenseSystem::call_api('activate', $licence_key, \true);
                // mi salvo lo stato della licenza per non effettuare troppe chiamate al server
                if ($res) {
                    update_option('dce_license_activated', 1);
                    update_option('dce_license_domain', \base64_encode(DCE_INSTANCE));
                } else {
                    update_option('dce_license_activated', 0);
                    $licence_key = '';
                }
            }
        }
        if (isset($_POST['beta_status'])) {
            if (isset($_POST['dce_beta'])) {
                update_option('dce_beta', 1);
            } else {
                update_option('dce_beta', 0);
            }
        }
        if (isset($_POST['backup_status'])) {
            if (isset($_POST['dce_backup_disable'])) {
                update_option('dce_backup_disable', 0);
            } else {
                update_option('dce_backup_disable', 1);
            }
        }
        $licence_check = isset($_GET['licence_check']) ? sanitize_text_field($_GET['licence_check']) : \false;
        $license_data = LicenseSystem::call_api('status-check', $licence_key, $licence_check, \true);
        if ($license_data) {
            $expiration_date = LicenseSystem::get_expiration_date($license_data);
            update_option('dce_license_expiration', $expiration_date);
        }
        $licence_status = $licence_key && LicenseSystem::is_active($license_data);
        $licence_key_hidden = '';
        $licence_pieces = \explode('-', $licence_key);
        if (isset($licence_pieces[1]) && isset($licence_pieces[2])) {
            $licence_pieces[1] = 'xxxxxxxx';
            $licence_pieces[2] = 'xxxxxxxx';
            $licence_key_hidden = \implode('-', $licence_pieces);
        }
        $dce_domain = \base64_decode(get_option('dce_license_domain'));
        $dce_activated = get_option('dce_license_activated', 0);
        $classes = $licence_status ? 'dce-success dce-notice-success' : 'dce-error dce-notice-error';
        if ($dce_activated && $licence_status && $dce_domain && $dce_domain != DCE_INSTANCE) {
            $classes = 'dce-warning dce-notice-warning';
        }
        ?>
		<div class="dce-notice <?php 
        echo $classes;
        ?>">
			<h2><?php 
        echo esc_html(__('License Status', 'dynamic-content-for-elementor'));
        ?> <a href="?<?php 
        echo Helper::recursive_sanitize_text_field($_SERVER['QUERY_STRING']);
        ?>&licence_check=1"><span class="dashicons dashicons-info"></span></a></h2>

			<form action="" method="post">
				<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        ?>
				<?php 
        _e('Your key', 'dynamic-content-for-elementor');
        ?>: <input type="text" name="licence_key" value="<?php 
        if ($dce_activated) {
            echo $licence_key_hidden;
        }
        ?>" id="licence_key" placeholder="dce-xxxxxxxx-xxxxxxxx-xxxxxxxx" style="width: 240px; max-width: 100%;">
				<input type="hidden" name="licence_status" value="<?php 
        echo $licence_status;
        ?>" id="licence_status">
			<?php 
        $licence_status ? submit_button('Deactivate', 'cancel') : submit_button(__('Save Key and Activate', 'dynamic-content-for-elementor'));
        ?>
			</form>
		<?php 
        if ($licence_status) {
            if ($dce_domain && $dce_domain != DCE_INSTANCE) {
                ?>
					<p><strong style="color:#f0ad4e;"><?php 
                _e('Your license is valid but there is something wrong: <b>License Mismatch</b>.', 'dynamic-content-for-elementor');
                ?></strong></p>
					<p><?php 
                _e('Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and reactivate it', 'dynamic-content-for-elementor');
                ?></p>
				<?php 
            } else {
                ?>
					<p><strong style="color:#46b450;"><?php 
                _e('Your license is valid and active.', 'dynamic-content-for-elementor');
                ?></strong></p>
					<p><?php 
                _e('Thank you for using our plugin.', 'dynamic-content-for-elementor');
                ?><br><?php 
                _e('Feel free to create your new dynamic and creative website.', 'dynamic-content-for-elementor');
                ?><br><?php 
                _e('If you like our plugin then please recommend it to your friends.', 'dynamic-content-for-elementor');
                ?></p>
				<?php 
            }
        } else {
            ?>
				<p><?php 
            _e('Enter your license here to keep the plugin updated, obtaining new features, future compatibility, more stability and security.', 'dynamic-content-for-elementor');
            ?></p>
				<p><?php 
            _e('You still don’t have one?', 'dynamic-content-for-elementor');
            ?> <a href="https://www.dynamic.ooo" class="button button-small" target="_blank"><?php 
            _e('Get it now!', 'dynamic-content-for-elementor');
            ?></a></p>
		<?php 
        }
        ?>
		</div>

		<?php 
        if ($licence_status) {
            $dce_beta = get_option('dce_beta');
            ?>
			<div class="dce-notice dce-warning dce-notice-warning">
				<h3><?php 
            _e('Beta release', 'dynamic-content-for-elementor');
            ?></h3>
				<form action="" method="post">
					<?php 
            wp_nonce_field('dce-settings-page', 'dce-settings-page');
            ?>
					<label><input type="checkbox" name="dce_beta" value="beta"<?php 
            if ($dce_beta) {
                ?> checked="checked"<?php 
            }
            ?>> <?php 
            _e('Enable beta releases (IMPORTANT: do NOT enable it if you need a stable version).', 'dynamic-content-for-elementor');
            ?></label>
					<input type="hidden" name="beta_status" value="1" id="beta_status">
			<?php 
            submit_button('Save my preference');
            ?>
				</form>
			</div>

			<?php 
            if (\extension_loaded('zip')) {
                $dce_backup = !get_option('dce_backup_disable');
                ?>
				<div class="dce-notice dce-<?php 
                echo $dce_backup ? 'success' : 'error';
                ?> dce-notice-<?php 
                echo $dce_backup ? 'success' : 'error';
                ?>">
					<h3><?php 
                _e('Safe upgrade', 'dynamic-content-for-elementor');
                ?></h3>
					<form action="" method="post">
						<?php 
                wp_nonce_field('dce-settings-page', 'dce-settings-page');
                ?>
						<label><input type="checkbox" name="dce_backup_disable" value="backup"<?php 
                if ($dce_backup) {
                    ?> checked="checked"<?php 
                }
                ?>> <?php 
                _e('Perform a backup of the current plugin version before the update action that allows easy rollback.', 'dynamic-content-for-elementor');
                ?></label>
						<input type="hidden" name="backup_status" value="1" id="backup_status">
				<?php 
                submit_button('Save my preference');
                ?>
					</form>
				</div>
				<?php 
            }
            $rollback_versions = array(DCE_VERSION => DCE_VERSION);
            $backups = \glob(\DynamicContentForElementor\Plugin::$backup_path . '/dynamic-content-for-elementor_*.zip');
            if (!empty($backups)) {
                foreach ($backups as $bak) {
                    list($pkg, $bak_version) = \explode('_', \str_replace('.zip', '', \basename($bak)));
                    $rollback_versions[$bak_version] = $bak_version;
                }
                ?>
				<div class="dce-notice dce-success dce-notice-success">
					<h3><?php 
                _e('Rollback version', 'dynamic-content-for-elementor');
                ?></h3>
					<form action="" method="post">
						<?php 
                wp_nonce_field('dce-settings-page', 'dce-settings-page');
                ?>
						<h4><?php 
                _e('Your current version', 'dynamic-content-for-elementor');
                ?>: <?php 
                echo DCE_VERSION;
                ?></h4>
						<p><?php 
                echo \sprintf(__('Experiencing an issue with Dynamic.ooo - Dynamic Content for Elementor version %s? Rollback to a previous version before the issue appeares.', 'dynamic-content-for-elementor'), DCE_VERSION);
                ?>
						<label><?php 
                _e('Select version', 'dynamic-content-for-elementor');
                ?>:</label>
						<select name="dce_version" id="dce_version">
							<?php 
                if (!empty($rollback_versions)) {
                    foreach ($rollback_versions as $aversion) {
                        ?>
									<option value="<?php 
                        echo $aversion;
                        ?>"><?php 
                        echo $aversion;
                        ?></option>
									<?php 
                    }
                }
                ?>
						</select>
						<?php 
                submit_button('Rollback NOW');
                ?>
					</form>
				</div>
				<?php 
            }
        }
        ?>
	</div>
		<?php 
    }
}
