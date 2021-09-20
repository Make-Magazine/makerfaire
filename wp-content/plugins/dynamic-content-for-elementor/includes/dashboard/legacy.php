<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor;
use DynamicContentForElementor\Notice;
class Legacy extends \DynamicContentForElementor\Dashboard\Features
{
    public static function get_name()
    {
        return 'legacy';
    }
    public static function get_label()
    {
        return __('Legacy', 'dynamic-content-for-elementor');
    }
    public static function display_form()
    {
        $legacy_widgets = \DynamicContentForElementor\Widgets::get_legacy_widgets();
        $legacy_extensions = \DynamicContentForElementor\Extensions::get_legacy_extensions();
        $widgets = \DynamicContentForElementor\Widgets::get_widgets_info();
        $extensions = \DynamicContentForElementor\Extensions::get_extensions_info();
        ?>
		<form action="" method="post">
		<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if (isset($_POST['save-dce-feature'])) {
            // Set excluded widgets
            $excluded_widgets = [];
            foreach ($legacy_widgets as $widget_class => $widget_info) {
                $excluded_widgets[$widget_class] = !isset($_POST['dce-feature'][$widget_class]);
            }
            $option = \json_decode(get_option('dce_excluded_widgets', '[]'), \true);
            update_option('dce_excluded_widgets', wp_json_encode($excluded_widgets + $option));
            // Set excluded extensions
            $excluded_extensions = [];
            foreach ($legacy_extensions as $extensions_class => $extensions_info) {
                $excluded_extensions[$extensions_class] = !isset($_POST['dce-feature'][$extensions_class]);
            }
            $option = \json_decode(get_option('dce_excluded_extensions', '[]'), \true);
            update_option('dce_excluded_extensions', wp_json_encode($excluded_extensions + $option));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $excluded_extensions = \DynamicContentForElementor\Extensions::get_excluded_extensions();
        $excluded_widgets = \DynamicContentForElementor\Widgets::get_excluded_widgets();
        ?>

			<h3><?php 
        _e('Widgets', 'dynamic-content-for-elementor');
        ?></h3>
			<div class="dce-modules">
			<?php 
        foreach ($legacy_widgets as $widget_class => $widget_info) {
            static::show_feature($widget_class, $widget_info, $excluded_widgets);
        }
        ?>
			</div>
			<?php 
        submit_button(__('Save', 'dynamic-content-for-elementor') . ' ' . static::get_label());
        ?>

			<h3><?php 
        _e('Extensions', 'dynamic-content-for-elementor');
        ?></h3>
			<div class="dce-modules">
			<?php 
        foreach ($legacy_extensions as $extensions_class => $extensions_info) {
            static::show_feature($extensions_class, $extensions_info, $excluded_extensions);
        }
        ?>
			</div>
			<?php 
        submit_button(__('Save', 'dynamic-content-for-elementor') . ' ' . static::get_label());
        ?>

			<input type="hidden" name="save-dce-feature" value="1" />
		</form>
		<?php 
    }
}
