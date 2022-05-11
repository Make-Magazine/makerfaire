<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * Notices for admin pages
 */
class Notice
{
    /**
     * Notice for license not activated
     *
     * @return void
     */
    public static function license()
    {
        ?>
		<div class="error notice-error notice dce-generic-notice">
			<div class="img-responsive pull-left"><img class='dce-logo' src="<?php 
        echo DCE_URL;
        ?>/assets/media/dce.png" title="<?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?>"></div>
			<p><strong><?php 
        _e('Welcome to Dynamic.ooo - Dynamic Content for Elementor!', 'dynamic-content-for-elementor');
        ?></strong><br />
			<?php 
        \printf(__('It seems that your copy is not activated, please %1$sactivate it%2$s or %3$sbuy a new license%4$s.', 'dynamic-content-for-elementor'), '<a href="' . admin_url() . 'admin.php?page=dce-license">', '</a>', '<a href="https://www.dynamic.ooo/pricing" target="blank">', '</a>');
        ?></p>
		</div>
	<?php 
    }
    /**
     * Notice for server errors
     *
     * @param string $msg
     * @return void
     */
    public static function server_error($msg = '')
    {
        ?>
		<div class="error notice-error notice">
			<div class="img-responsive pull-left"><img class='dce-logo' src="<?php 
        echo DCE_URL;
        ?>/assets/media/dce.png" title="<?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?>"></div>
			<p><strong><?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?></strong><br />
			<?php 
        if ($msg) {
            echo wp_kses_post($msg);
        } else {
            _e('There was a problem establishing a connection to the API server', 'dynamic-content-for-elementor');
        }
        ?></p>
		</div>
	<?php 
    }
    /**
     * Notice to send a success message
     *
     * @param string $msg
     * @return void
     */
    public static function success($msg = '')
    {
        ?>
		<div class="success notice-success notice updated">
			<div class="img-responsive pull-left"><img class='dce-logo' src="<?php 
        echo DCE_URL;
        ?>/assets/media/dce.png" title="<?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?>"></div>
			<p><strong><?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?></strong><br />
			<?php 
        if ($msg) {
            echo wp_kses_post($msg);
        }
        ?></p>
		</div>
	<?php 
    }
    /**
     * Notice to send a warning message
     *
     * @param string $msg
     * @return void
     */
    public static function warning($msg = '')
    {
        ?>
		<div class="warning notice-warning notice">
			<div class="img-responsive pull-left"><img class='dce-logo' src="<?php 
        echo DCE_URL;
        ?>/assets/media/dce.png" title="<?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?>"></div>
			<p><strong><?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?></strong><br />
			<?php 
        if ($msg) {
            echo wp_kses_post($msg);
        }
        ?></p>
		</div>
	<?php 
    }
    /**
     * Notice to send an error message
     *
     * @param string $msg
     * @return void
     */
    public static function error($msg = '')
    {
        ?>
		<div class="notice-danger notice error">
			<div class="img-responsive pull-left"><img class='dce-logo' src="<?php 
        echo DCE_URL;
        ?>/assets/media/dce.png" title="<?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?>"></div>
			<p><strong><?php 
        echo DCE_PRODUCT_NAME_LONG;
        ?></strong><br />
			<?php 
        if ($msg) {
            echo wp_kses_post($msg);
        }
        ?></p>
		</div>
	<?php 
    }
}
