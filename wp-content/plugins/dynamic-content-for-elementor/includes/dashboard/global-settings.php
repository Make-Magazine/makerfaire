<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor;
use DynamicContentForElementor\Notice;
class GlobalSettings extends \DynamicContentForElementor\Dashboard\Features
{
    public static function get_name()
    {
        return 'global-settings';
    }
    public static function get_label()
    {
        return __('Global Settings', 'dynamic-content-for-elementor');
    }
    public static function display_form()
    {
        $global_settings = \DynamicContentForElementor\GlobalSettings::get_global_settings_info();
        ?>
	<form action="" method="post">
		<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if (isset($_POST['save-dce-feature'])) {
            $excluded_global_settings = [];
            foreach ($global_settings as $global_settings_class => $global_settings_info) {
                if (!isset($_POST['dce-feature'][$global_settings_class])) {
                    $excluded_global_settings[$global_settings_class] = $global_settings_info;
                }
            }
            update_option('dce_excluded_global_settings', wp_json_encode($excluded_global_settings));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $excluded_global_settings = \DynamicContentForElementor\GlobalSettings::get_excluded_settings();
        ?>

	<div class="dce-check dce-check-all">
		<table>
			<tr>
				<td width="30">
					<input type="checkbox" name="dce-feature[]" value="true" class="dce-checkbox" id="dce-feature-all"
				<?php 
        if (!$excluded_global_settings) {
            checked(\true);
        }
        ?>>
					<label for="dce-feature-all"><div id="tick_mark_small"></div></label>
				</td>
				<td>
					<?php 
        echo __('All', 'dynamic-content-for-elementor') . ' ' . static::get_label();
        ?>
				</td>
			</tr>
		</table>
	</div>

		<?php 
        submit_button(__('Save', 'dynamic-content-for-elementor') . ' ' . static::get_label());
        ?>

		<div class="dce-modules">
			<?php 
        foreach ($global_settings as $global_settings_class => $global_settings_info) {
            static::show_feature($global_settings_class, $global_settings_info, $excluded_global_settings);
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
