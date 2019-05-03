<?php

namespace LazyLoadOptimizer\Admin\Customizer;

class CustomizeLabel extends \WP_Customize_Control
{
    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        </label>
        <?php
    }

}