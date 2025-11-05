<?php
/**
 * Plugin Name: Spellbook
 * Plugin URI: https://gravitywiz.com/
 * Description: Spellbook will allow you to install and update all other Gravity Wiz plugins directly from your WordPress admin. It feels like magic. ✨
 * Version: 3.0.16
 * Author: Gravity Wiz
 * Author URI: https://gravitywiz.com/
 * License: GPL2
 * Text Domain: spellbook
 * Domain Path: /languages
 * Update URI: https://gravitywiz.com/updates/spellbook
 */
if (!defined('ABSPATH')) exit;

define( 'SPELLBOOK_VERSION', '3.0.16' );

if (!defined('GRAVITY_PERKS_VERSION')) {
	define( 'GRAVITY_PERKS_VERSION',  SPELLBOOK_VERSION );
}

/**
 * Load our files and initialize the plugin.
 */
add_action( 'plugins_loaded', function() {
	if (is_plugin_active('gravityperks/gravityperks.php') || class_exists('GravityPerks')) {
		// Deactivate old Gravity Perks install
		deactivate_plugins('gravityperks/gravityperks.php', true);

		// Show notice about the transition
		add_action('admin_notices', function() {
			?>
			<div class="notice notice-info">
				<p>
					<strong>Gravity Perks has been updated to Spellbook!</strong>
					All your perks will continue to function normally.
				</p>
			</div>
			<?php
		});

		// Let Gravity Perks handle this final request.
		return;
	}

	require_once( plugin_dir_path( __FILE__ ) . 'model/perk.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-gp-late-static-binding.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'class-gravityperks.php' );

	add_action( 'init', array( 'GravityPerks', 'init' ) );
	add_action( 'gform_loaded', array( 'GravityPerks', 'init_perk_as_plugin_functionality' ) );
}, -1 );

