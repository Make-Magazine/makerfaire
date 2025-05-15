<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

class ExtraShortCodeHandler
{
    public function register()
    {
        add_shortcode('nt_ratings', array($this, 'ratings'));
        add_shortcode('nt_icon', array($this, 'icon'));
    }

    public function ratings($atts)
    {
        $data = shortcode_atts(array(
            'value'     => 5,
            'color'     => '#ffcc00',
            'alt_color' => '#dddddd',
            'max'       => 5
        ), $atts);

        $value     = intval($data['value']);
        $max       = intval($data['max']);
        $color     = sanitize_text_field($data['color']);
        $alt_color = sanitize_text_field($data['alt_color']);

        if ($value > $max) {
            $max = $value;
        }

        $reminder = $max - $value;

        return '<span class="nt_review_icon">' .
               $this->getIcons($value, 'star', $color) .
               $this->getIcons($reminder, 'star-o', $alt_color) .
               '<span style="display: none !important;">' . esc_html($value) . '</span></span>';
    }

    public function icon($atts)
    {
        $data = shortcode_atts(array(
            'number' => 1,
            'color'  => 'black',
            'icon'   => 'star'
        ), $atts);

        $number = intval($data['number']);
        $color  = sanitize_text_field($data['color']);
        $icon   = sanitize_text_field($data['icon']);

        if (!$icon) {
            return '';
        }

        return '<span class="nt_icon">' .
               $this->getIcons($number, $icon, $color) .
               '<span style="display: none !important;">' . esc_html($icon) . '</span></span>';
    }

    private function getIcons($number, $icon_class, $color)
    {
        $html       = '';
        $icon_class = sanitize_text_field($icon_class);
        $color      = sanitize_text_field($color);

        for ($i = 0; $i < $number; $i++) {
            $icon_dir = NINJA_TABLES_DIR_PATH . 'assets/libs/icons/' . $icon_class . '.svg';
            if (file_exists($icon_dir)) {
                $icon_url = NINJA_TABLES_DIR_URL . 'assets/libs/icons/' . $icon_class . '.svg';

                $html .= '<span class="' . esc_attr($icon_class) . '" style="
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    background-color: ' . esc_attr($color) . ';
                    -webkit-mask-image: url(' . esc_url($icon_url) . ') !important;
                    mask-image: url(' . esc_url($icon_url) . ') !important;
                "> </span>';
            }
        }

        return $html;
    }
}
