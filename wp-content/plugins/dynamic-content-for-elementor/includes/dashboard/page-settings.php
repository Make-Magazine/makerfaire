<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor;
use DynamicContentForElementor\Notice;
class PageSettings extends \DynamicContentForElementor\Dashboard\Features
{
    public static function get_name()
    {
        return 'page-settings';
    }
    public static function get_label()
    {
        return __('Page Settings', 'dynamic-content-for-elementor');
    }
    public static function display_form()
    {
        $page_settings = \DynamicContentForElementor\PageSettings::get_page_settings_info();
        ?>
	<form action="" method="post">
		<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if (isset($_POST['save-dce-feature'])) {
            $excluded_page_settings = [];
            foreach ($page_settings as $page_settings_class => $page_settings_info) {
                if (!isset($_POST['dce-feature'][$page_settings_class])) {
                    $excluded_page_settings[$page_settings_class] = $page_settings_info;
                }
            }
            update_option('dce_excluded_page_settings', wp_json_encode($excluded_page_settings));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $excluded_page_settings = \DynamicContentForElementor\PageSettings::get_excluded_page_settings();
        ?>

	<div class="dce-check dce-check-all">
		<table>
			<tr>
				<td width="30">
					<input type="checkbox" name="dce-feature[]" value="true" class="dce-checkbox" id="dce-feature-all"
				<?php 
        if (!$excluded_page_settings) {
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
        foreach ($page_settings as $page_settings_class => $page_settings_info) {
            static::show_feature($page_settings_class, $page_settings_info, $excluded_page_settings);
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
