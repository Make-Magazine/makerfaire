<?php

namespace DynamicContentForElementor\Dashboard;

use DynamicContentForElementor;
use DynamicContentForElementor\Notice;
use DynamicContentForElementor\Helper;
abstract class Features
{
    public static function get_name()
    {
        return 'features';
    }
    public static function get_label()
    {
        return __('Features', 'dynamic-content-for-elementor');
    }
    public static function calculate_usage($feature, $elementor_controls_usage)
    {
    }
    public static function display_form()
    {
    }
    public static function show_feature($feature_class, $feature_info, $excluded_features = [])
    {
        $feature_activated = !isset($excluded_features[$feature_class]);
        $plugin_dependencies_not_satisfied = Helper::check_plugin_dependencies(\true, $feature_info['plugin_depends']);
        $php_version_not_satisfied = isset($feature_info['minimum_php']) && \version_compare(\phpversion(), $feature_info['minimum_php'], '<');
        ?>

		<div class="dce-feature dce-feature-group-<?php 
        echo \strtolower($feature_class);
        ?> dce-feature-<?php 
        echo \urlencode(\strtolower($feature_info['title']));
        ?>
			<?php 
        if (!empty($plugin_dependencies_not_satisfied)) {
            echo ' required-plugin';
        }
        if ($feature_activated) {
            echo ' widget-activated';
        }
        ?>
			">

		<?php 
        if ($php_version_not_satisfied) {
            ?>
			<p class="php-version"><?php 
            \printf(__('This feature requires PHP v%1$s+', 'dynamic-content-for-elementor'), $feature_info['minimum_php']);
            ?></p>
		<?php 
        }
        ?>

		<?php 
        if (empty($plugin_dependencies_not_satisfied) && !$php_version_not_satisfied) {
            ?>
			<div class="dce-check">
				<input type="checkbox" name="dce-feature[<?php 
            echo $feature_class;
            ?>]" value="true" id="dce-feature-<?php 
            echo $feature_class;
            ?>" class="dce-checkbox" <?php 
            if ($feature_activated) {
                ?> checked="checked"<?php 
            }
            ?>>
				<label for="dce-feature-<?php 
            echo $feature_class;
            ?>"><div id="tick_mark"></div></label>
			</div>
		<?php 
        }
        ?>
		<?php 
        if (isset($feature_info['icon'])) {
            ?>
			<p><i class="icon <?php 
            echo $feature_info['icon'];
            ?>" aria-hidden="true"></i></p>
		<?php 
        }
        ?>
		<h4><?php 
        echo esc_html($feature_info['title']);
        ?></h4>

		<?php 
        if (!empty($plugin_dependencies_not_satisfied)) {
            ?>
			<small class="warning text-red red"><span class="dashicons dashicons-warning"></span> <?php 
            _e('Required plugin', 'dynamic-content-for-elementor');
            ?>: <?php 
            echo \implode(', ', $plugin_dependencies_not_satisfied);
            ?></small>
			<?php 
        }
        ?>
		
		<?php 
        if (isset($feature_info['description'])) {
            ?>
			<p><?php 
            echo $feature_info['description'];
            ?></p>
			<?php 
        }
        ?>

		<?php 
        if (isset($feature_info['doc_url'])) {
            ?>
			<p style="margin-top: -10px"><a href="<?php 
            echo $feature_info['doc_url'];
            ?>" target="_blank"><?php 
            _e('Documentation', 'dynamic-content-for-elementor');
            ?></a></p>
		<?php 
        }
        $elementor_controls_usage = get_option('elementor_controls_usage');
        $feature_used = static::calculate_usage($feature_info['name'], $elementor_controls_usage);
        if ($feature_used) {
            if (1 === $feature_used) {
                \printf(__('%1$sUsed %2$s time%3$s', 'dynamic-content-for-elementor'), '<p class="used">', $feature_used, '</p>');
            } else {
                \printf(__('%1$sUsed %2$s times%3$s', 'dynamic-content-for-elementor'), '<p class="used">', $feature_used, '</p>');
            }
        }
        ?>
		<?php 
        if (isset($feature_info['legacy'])) {
            ?>
			<p class="legacy"><?php 
            _e('This feature is deprecated. We recommend to use the new version, but we will not remove this version.', 'dynamic-content-for-elementor');
            ?></p>
		<?php 
        }
        ?> 
		</div>
		<?php 
    }
}
