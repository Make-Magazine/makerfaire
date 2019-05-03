<?php

if ( ! defined('WPINC')) {
    die;
}

use  LazyLoadOptimizer\Admin\Settings;

?>
<?php echo '<h3>'.__('Appearance settings', 'lazy-load-optimizer').' <a href="' .
    wp_kses(esc_url(add_query_arg(array(
        'autofocus' => array(
            'section' => 'lazyload_settings',
        ),
        'url' => home_url(),
    ), admin_url('customize.php'))), array(
        'a' => array(
            'href' => array(),
            'title' => array(),
        ),
    ))

    . '" aria-label="' . esc_attr__('View Lazy Load Optimizer settings', 'lazy-load-optimizer') . '">' . esc_html__('in the Customizer', 'lazy-load-optimizer') . '</a></h3>';
?>
<table class="form-table">
    <tbody>

    <tr>
        <th scope="row"><?php _e('lazy loading for iframes','lazy-load-optimizer') ?></th>

        <td>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadIframes]" value="1" <?php checked(true, $loadIframes); ?>>
                <?php
                _e('enable', 'lazy-load-optimizer');
                ?>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Expand (default: 370-500)','lazy-load-optimizer') ?></th>
        <td>
            <input type="number"  name="<?=Settings::OPTIONS?>[expand]" value="<?php echo esc_attr( $expand ) ?>" />
            <p class="description" id="menu-height-description">
                <?php _e('The <code>expand</code> option expands the calculated visual viewport area in all directions, so that elements can be loaded before they become visible. The default value is calculated depending on the viewport size of the device. (Note: Reasonable values are between <code>300</code> and <code>1000</code> (depending on the <code>expFactor</code> option.) In case you have a lot of small images or you are using the LQIP pattern you can lower the value, in case you have larger images set it to a higher value. Also note, that lazySizes will dynamically shrink this value to 0 if the browser is currently downloading and expand it if the browser network is currently idling and the user not scrolling (by multiplying the expand option with 1.5 (expFactor)). This option can also be overridden with the <code>[data-expand]</code> attribute.','lazy-load-optimizer') ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Exp. Factor (default: 1.5)','lazy-load-optimizer') ?></th>
        <td>
            <input type="number" step="0.1"  name="<?=Settings::OPTIONS?>[expFactor]" value="<?php echo esc_attr( $expFactor ) ?>" />
            <p class="description" id="menu-height-description">
                <?php _e('The <code>expFactor</code> is used to calculate the "preload expand", by multiplying the normal expand with the expFactor which is used to preload assets while the browser is idling (no important network traffic and no scrolling). (Reasonable values are between 1.5 and 4 depending on the expand option).','lazy-load-optimizer') ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row"><?php _e('Load hidden images','lazy-load-optimizer') ?></th>

        <td>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadHidden]" value="1" <?php checked(true, $loadHidden); ?>>
                <?php
                _e('load', 'lazy-load-optimizer');
                ?>
            </label>
            <p class="description">
                <?php _e('Wether to load visibility: hidden elements.','anchor-smooth-scroll') ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row"><?php _e('Init lazysizes','lazy-load-optimizer') ?></th>

        <td>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[init]" value="1" <?php checked(true, $init); ?>>
                <?php
                _e('init', 'lazy-load-optimizer');
                ?>
            </label>
            <p class="description">
                <?php _e('By default lazysizes initializes itself, to load in view assets as soon as possible. In the unlikely case you need to setup/configure something with a later script you can set this option to <code>false</code> and call <code>lazySizes.init();</code> later explicitly.','anchor-smooth-scroll') ?>
            </p>
        </td>
    </tr>
    <input type="hidden" name="section" value="main" />
    </tbody>
</table>
