<?php
/*
Plugin Name: SumoMe
Plugin URI: https://sumo.com
Description: Free Tools to automate your site growth from Sumo.com
Version: 1.25
Author: SumoMe
Author URI: https://www.Sumo.com
*/

define('SUMOME__PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('SUMOME__PLUGIN_FILE', __FILE__);

include 'classes/class_sumome.php';
$wp_plugin_sumome = new WP_Plugin_SumoMe();

register_activation_hook( __FILE__, array('WP_Plugin_SumoMe', 'activate_SumoMe_plugin'));
register_deactivation_hook(__FILE__, array('WP_Plugin_SumoMe', 'deactivate_SumoMe_plugin'));

function sumome_plugin_settings_link($links)
{
  $settings_link = '<a href="options-general.php?page=sumo">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter('plugin_action_links_'.$plugin, 'sumome_plugin_settings_link');
