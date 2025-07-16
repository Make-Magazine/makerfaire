<?php

class GWPerksPage {

	// TODO Multi Site notices in new UI

	public static function load_page() {
		$asset_file = include GravityPerks::get_base_path() . '/js/built/dashboard.asset.php';
		wp_set_script_translations( 'gw-spellbook-dashboard', 'spellbook', GravityPerks::get_base_path() . 'languages/' );

		// Enqueue
		wp_enqueue_script( 'gw-spellbook-dashboard', GravityPerks::get_base_url() . '/js/built/dashboard.js', $asset_file['dependencies'], $asset_file['version'], true );
		wp_enqueue_style( 'gw-spellbook-dashboard', GravityPerks::get_base_url() . '/assets/css/built/dashboard.css', array(), $asset_file['version'] );
		wp_enqueue_style( 'gform_admin' );
		wp_enqueue_style( 'wp-components' );
		?>

		<div id="gwiz-spellbook" class="spellbook-admin"></div>

		<?php
	}

	// PERK DISPLAY VIEWS //
	public static function load_perk_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die( __( 'You don\'t have permission to access this page.' ) );
		}

		$perk = GWPerk::get_perk( gwget( 'slug' ) );
		$perk->load_perk_data();

		if ( isset( $_POST['gwp_save_settings'] ) ) {

			check_admin_referer( 'gp_save_settings', 'security' );

			$setting_keys = array();

			if ( method_exists( $perk, 'register_settings' ) ) {
				$setting_keys = $perk->register_settings( $perk );
				if ( empty( $setting_keys ) ) {
					$setting_keys = array();
				}
			}

			$settings = self::get_submitted_settings( $perk, $setting_keys );

			if ( ! empty( $settings ) ) {
				GWPerk::save_perk_settings( $perk->get_id(), $settings );
				$notice = new GWNotice( __( 'Settings saved successfully.', 'spellbook' ) );
			} else {
				$notice = new GWNotice( __( 'Settings were not saved.', 'spellbook' ), array( 'class' => 'error' ) );
			}
		}

		$page_title = sprintf( __( '%s Settings', 'spellbook' ), $perk->data['Name'] );

		?>

		<!DOCTYPE html>
		<html>

		<head>
		<title><?php echo $page_title; ?></title>
		<?php
			// Resolves issues with the 3rd party scripts checking for get_current_screen().
			remove_all_actions( 'wp_print_styles' );
			remove_all_actions( 'wp_print_scripts' );
			wp_print_styles( array( 'gwp-admin', 'wp-admin', 'buttons', 'colors-fresh' ) );
			wp_print_scripts( array( 'jquery', 'gwp-admin' ) );
		?>
		</head>

		<body class="perk-iframe wp-core-ui">

			<div class="wrap perk-settings">
				<form action="" method="post">
					<div class="header">
						<h1 class="page-title"><?php echo $page_title; ?></h1>
						<?php
						if ( isset( $notice ) ) :
							$notice->display();
						endif;
						?>
					</div>
					<div class="content">
						<?php echo $perk->get_settings(); ?>
					</div>
					<div class="content-footer">
						<?php wp_nonce_field( 'gp_save_settings', 'security' ); ?>
						<input type="submit" id="gwp_save_settings" name="gwp_save_settings" class="button button-primary" value="<?php _e( 'Save Settings', 'spellbook' ); ?>" />
					</div>
				</form>
			</div>

			<script type="text/javascript">
			setTimeout('jQuery(".updated").slideUp();', 5000);
			</script>

		</body>
		</html>

		<?php
		exit;
	}

	public static function get_submitted_settings( $perk, $setting_keys, $flush_values = false ) {

		$settings = array();

		foreach ( $setting_keys as $setting_key => $setting_children ) {

			if ( ! is_array( $setting_children ) ) {
				$setting_key      = $setting_children;
				$key              = $perk->get_id() . "_{$setting_key}";
				$settings[ $key ] = $flush_values ? false : gwpost( $key );
			} else {
				$key              = $perk->get_id() . "_{$setting_key}";
				$settings[ $key ] = $flush_values ? false : gwpost( $key );
				$settings         = array_merge( $settings, self::get_submitted_settings( $perk, $setting_children, ! $settings[ $key ] ) );
			}
		}

		return $settings;
	}

}
