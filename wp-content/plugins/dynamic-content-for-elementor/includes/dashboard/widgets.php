<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor;
use DynamicContentForElementor\Notice;
class Widgets extends \DynamicContentForElementor\Dashboard\Features
{
    public static function get_name()
    {
        return 'widgets';
    }
    public static function get_label()
    {
        return __('Widgets', 'dynamic-content-for-elementor');
    }
    public static function calculate_usage($feature, $elementor_controls_usage)
    {
        $calculate_usage = 0;
        foreach ($elementor_controls_usage as $key => $value) {
            if (isset($elementor_controls_usage[$key][$feature])) {
                $calculate_usage += $elementor_controls_usage[$key][$feature]['count'];
            }
        }
        return $calculate_usage;
    }
    public static function display_form()
    {
        $grouped_widgets = \DynamicContentForElementor\Widgets::get_widgets_by_group();
        $legacy_widgets = \DynamicContentForElementor\Widgets::get_legacy_widgets();
        ?>
		<form action="" method="post">
		<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        $elementor_controls_usage = get_option('elementor_controls_usage');
        if (isset($_POST['save-dce-feature'])) {
            $excluded_widgets = [];
            foreach ($grouped_widgets as $group => $widgets) {
                foreach ($widgets as $widget_class => $widget_info) {
                    if (!isset($_POST['dce-feature'][$widget_class])) {
                        $excluded_widgets[$widget_class] = \true;
                    }
                }
            }
            update_option('dce_excluded_widgets', wp_json_encode($excluded_widgets));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $excluded_widgets = \DynamicContentForElementor\Widgets::get_excluded_widgets();
        ?>

		<div class="dce-check dce-check-all">
			<table>
				<tr>
					<td width="30">
						<input type="checkbox" name="dce-feature[]" value="true" class="dce-checkbox" id="dce-feature-all"
					<?php 
        if (!$excluded_widgets) {
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
        foreach ($grouped_widgets as $group => $grouped_widgets_info) {
            ?>
				<h3><?php 
            echo esc_html($group);
            ?></h3>
				<div class="dce-modules">
				<?php 
            foreach ($grouped_widgets_info as $widget_class => $widget_info) {
                if (!isset($legacy_widgets[$widget_class])) {
                    static::show_feature($widget_class, $widget_info, $excluded_widgets);
                }
            }
            ?>
				</div>
				<?php 
            submit_button(__('Save', 'dynamic-content-for-elementor') . ' ' . static::get_label());
        }
        ?>

			<input type="hidden" name="save-dce-feature" value="1" />
			</form>
		<?php 
    }
}
