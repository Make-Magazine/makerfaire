<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor\Notice;
class Extensions extends \DynamicContentForElementor\Dashboard\Features
{
    public static function get_name()
    {
        return 'extensions';
    }
    public static function get_label()
    {
        return __('Extensions', 'dynamic-content-for-elementor');
    }
    public static function display_form()
    {
        $all_extensions = \DynamicContentForElementor\Extensions::get_extensions_info();
        $non_legacy_extensions = \array_filter($all_extensions, function ($info) {
            return !(isset($info['legacy']) && $info['legacy']);
        });
        ?>
		<form action="" method="post">
		<?php 
        wp_nonce_field('dce-settings-page', 'dce-settings-page');
        if (isset($_POST['save-dce-feature'])) {
            $excluded_extensions = [];
            foreach ($non_legacy_extensions as $extension_class => $extension_info) {
                $excluded_extensions[$extension_class] = !isset($_POST['dce-feature'][$extension_class]);
            }
            // just updating would reset legacy extensions:
            $option = \json_decode(get_option('dce_excluded_extensions', '[]'), \true);
            update_option('dce_excluded_extensions', wp_json_encode($excluded_extensions + $option));
            Notice::dce_admin_notice__success(__('Your preferences have been saved.', 'dynamic-content-for-elementor'));
        }
        $excluded_extensions = \DynamicContentForElementor\Extensions::get_excluded_extensions();
        ?>

		<div class="dce-check dce-check-all">
			<table>
				<tr>
					<td width="30">
						<input type="checkbox" name="dce-feature[]" value="true" class="dce-checkbox" id="dce-feature-all"
					<?php 
        if (!$excluded_extensions) {
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
        foreach ($non_legacy_extensions as $extension_class => $extension_info) {
            static::show_feature($extension_class, $extension_info, $excluded_extensions);
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
