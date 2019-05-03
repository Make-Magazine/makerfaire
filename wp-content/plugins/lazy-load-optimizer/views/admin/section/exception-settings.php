<?php

if ( ! defined('WPINC')) {
    die;
}

use  LazyLoadOptimizer\Admin\Settings;

?>

<table class="form-table">
    <tbody>

    <tr>
        <th scope="row"><?php _e('CSS classes to exclude','lazy-load-optimizer') ?></th>
        <td>
            <input type="text" name="<?=Settings::OPTIONS?>[cssClasses]" value="<?php echo esc_attr( $cssClasses ) ?>" />
            <p class="description" id="menu-height-description">
                <?php _e('CSS classes to exclude them from lazy loading (comma separated, e.g. class-1,class-2).','lazy-load-optimizer') ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row"><?php _e('Exclude pages by URI','lazy-load-optimizer') ?></th>
        <td>
            <textarea cols="50" rows="10" name="<?=Settings::OPTIONS?>[excludeUrl]"/><?php echo esc_attr( $excludeUrl ) ?></textarea>
            <p class="description" id="menu-height-description">
                <?php _e('Add parts of URLs (e.g. category), 1 per line, that are to be excluded from Lazy Load.','lazy-load-optimizer') ?>
            </p>
        </td>

    </tr>
    <tr>
        <th scope="row"><?php _e('Exclude pages by page type','lazy-load-optimizer') ?></th>
        <td>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadFrontPage]" value="1" <?php checked(true, $loadFrontPage); ?>>
                <?php
                _e('Front Page', 'lazy-load-optimizer');
                ?>
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadPosts]" value="1" <?php checked(true, $loadPosts); ?>>
                <?php
                _e('Posts', 'lazy-load-optimizer');
                ?>
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadPages]" value="1" <?php checked(true, $loadPages); ?>>
                <?php
                _e('Pages', 'lazy-load-optimizer');
                ?>
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadCategory]" value="1" <?php checked(true, $loadCategory); ?>>
                <?php
                _e('Post Categories', 'lazy-load-optimizer');
                ?>
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo Settings::OPTIONS; ?>[loadTag]" value="1" <?php checked(true, $loadTag); ?>>
                <?php
                _e('Post Tags', 'lazy-load-optimizer');
                ?>
            </label>
        </td>

    </tr>
    <input type="hidden" name="section" value="exception" />
    </tbody>
</table>
