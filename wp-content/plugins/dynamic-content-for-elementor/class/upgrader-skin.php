<?php

namespace DynamicContentForElementor;

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
class Upgrader_Skin extends \WP_Upgrader_Skin
{
    public function feedback($string, ...$args)
    {
        if (isset($this->upgrader->strings[$string])) {
            $string = $this->upgrader->strings[$string];
        }
        if (\strpos($string, '%') !== \false) {
            if ($args) {
                $args = \array_map('strip_tags', $args);
                $args = \array_map('esc_html', $args);
                $string = \vsprintf($string, $args);
            }
        }
        if (empty($string)) {
            return;
        }
    }
}
