<?php

namespace DynamicContentForElementor;

use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Dashboard;
use DynamicContentForElementor\GlobalSettings;
use DynamicContentForElementor\Widgets;
if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Settings Class
 *
 * Settings page
 *
 * @since 0.0.1
 */
class Settings
{
    private $options = array();
    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        $this->options = get_option(DCE_OPTIONS);
        if (is_admin()) {
            $dce_google_maps_api = get_option('dce_google_maps_api');
            $dce_google_maps_api_acf = get_option('dce_google_maps_api_acf');
            if (!empty($dce_google_maps_api) && !empty($dce_google_maps_api_acf)) {
                if (Helper::is_acfpro_active()) {
                    add_action('acf/init', function () use($dce_google_maps_api) {
                        acf_update_setting('google_api_key', $dce_google_maps_api);
                    });
                } elseif (Helper::is_acf_active()) {
                    add_filter('acf/fields/google_map/api', function ($api) use($dce_google_maps_api) {
                        $api['key'] = $dce_google_maps_api;
                        return $api;
                    });
                }
            }
        }
    }
    public function dce_setting_page($tplsys = \false)
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            if (!isset($_POST['dce-settings-page']) || !wp_verify_nonce($_POST['dce-settings-page'], 'dce-settings-page')) {
                wp_die('Nonce verification error.');
            }
        }
        // add error/update messages
        // check if the user have submitted the settings
        // WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // add settings saved message with the class of "updated"
            add_settings_error('dce_messages', 'dce_message', __('Settings Saved', 'dynamic-content-for-elementor'), 'updated');
        }
        \DynamicContentForElementor\Assets::dce_icon();
        if (!$tplsys) {
            $excluded_widgets = Widgets::get_excluded_widgets();
            if (!isset($excluded_widgets['DCE_Widget_GoogleMaps'])) {
                $dce_google_maps_api = get_option('dce_google_maps_api');
                if (empty($dce_google_maps_api)) {
                    \DynamicContentForElementor\Notice::dce_admin_notice__warning(__('Please provide API keys to use 3rd party services.', 'dynamic-content-for-elementor') . ' <a href="' . admin_url() . 'admin.php?page=dce-apis">' . __('Set it now', 'dynamic-content-for-elementor') . '</a>.');
                }
            }
        }
        \DynamicContentForElementor\LicenseSystem::dce_active_domain_check();
        \DynamicContentForElementor\LicenseSystem::dce_expired_license_notice();
        // show error/update messages
        settings_errors('dce_messages');
        ?>
		<div class="wrap">
			<h1><?php 
        echo esc_html(get_admin_page_title());
        ?></h1>

			<div id="dce-settings-tabs-wrapper" class="nav-tab-wrapper">
				<a id="dce-settings-tab-widgets" class="nav-tab<?php 
        if (!isset($_GET['tab']) || isset($_GET['tab']) && $_GET['tab'] == 'widgets') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=widgets">
					<?php 
        _e('Widgets', 'dynamic-content-for-elementor');
        ?>
					<span class="dce-badge"><?php 
        $widgets = Widgets::get_widgets_info();
        echo \count($widgets);
        ?></span>
				</a>
				<a id="dce-settings-tab-extensions" class="nav-tab<?php 
        if (isset($_GET['tab']) && $_GET['tab'] == 'extensions') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=extensions">
					<?php 
        _e('Extensions', 'dynamic-content-for-elementor');
        ?>
					<span class="dce-badge"><?php 
        $extensions = \DynamicContentForElementor\Extensions::get_extensions_info();
        echo \count($extensions);
        ?></span>
				</a>
				<a id="dce-settings-tab-controls" class="nav-tab<?php 
        if (isset($_GET['tab']) && $_GET['tab'] == 'page-settings') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=page-settings">
					<?php 
        _e('Page Settings', 'dynamic-content-for-elementor');
        ?>
					<span class="dce-badge"><?php 
        $page_settings = \DynamicContentForElementor\PageSettings::get_page_settings_info();
        echo \count($page_settings);
        ?></span>
				</a>
				<a id="dce-settings-tab-globals" class="nav-tab<?php 
        if (isset($_GET['tab']) && $_GET['tab'] == 'global-settings') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=global-settings">
					<?php 
        _e('Global Settings', 'dynamic-content-for-elementor');
        ?>
					<span class="dce-badge"><?php 
        $globals = GlobalSettings::get_global_settings_info();
        echo \count($globals);
        ?></span>
				</a>
				<a id="dce-settings-tab-frontend-navigator" class="nav-tab<?php 
        if (isset($_GET['tab']) && $_GET['tab'] == 'frontend-navigator') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=frontend-navigator">
					<?php 
        _e('Frontend Navigator', 'dynamic-content-for-elementor');
        ?>
				</a>
				<a id="dce-settings-tab-legacy" class="nav-tab<?php 
        if (isset($_GET['tab']) && $_GET['tab'] == 'legacy') {
            ?> nav-tab-active<?php 
        }
        ?>" href="?page=dce-features&tab=legacy">
					<?php 
        _e('Legacy', 'dynamic-content-for-elementor');
        ?>
				</a>
			</div>

			<div class="dce-container" style="display: block;">
				<?php 
        if (isset($_GET['tab']) && $_GET['tab'] === 'page-settings') {
            Dashboard\PageSettings::display_form();
        } elseif (isset($_GET['tab']) && $_GET['tab'] === 'extensions') {
            Dashboard\Extensions::display_form();
        } elseif (isset($_GET['tab']) && $_GET['tab'] === 'global-settings') {
            Dashboard\GlobalSettings::display_form();
        } elseif (isset($_GET['tab']) && $_GET['tab'] === 'legacy') {
            Dashboard\Legacy::display_form();
        } elseif (isset($_GET['tab']) && $_GET['tab'] === 'frontend-navigator') {
            Dashboard\FrontendNavigator::display_form();
        } else {
            Dashboard\Widgets::display_form();
            ?>
				<?php 
        }
        ?>
		  </div>
		</div>
		<?php 
    }
}
