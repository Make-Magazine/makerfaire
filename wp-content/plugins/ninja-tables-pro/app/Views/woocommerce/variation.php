<?php

defined('ABSPATH') || exit;
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to plugins/woocommerce/templates/single-product/add-to-cart/variable.php.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 */

$attribute_keys  = array_keys($attributes);
$variations_json = wp_json_encode($available_variations);
$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars(
    $variations_json,
    ENT_QUOTES,
    'UTF-8',
    true
);
$formId          = 'nt_variation_form_' . $product->get_id();

do_action('woocommerce_before_add_to_cart_form'); ?>

    <form class="variations_form cart nt_variations_form" id="<?php echo esc_attr($formId); ?>" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo $variations_attr; ?>">
        <?php do_action('woocommerce_before_variations_form'); ?>

        <?php if (empty($available_variations) && false !== $available_variations) : ?>
            <p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?></p>
        <?php else : ?>
            <div class="variations">
                <?php foreach ($attributes as $attribute_name => $options) : ?>
                    <div class="value">
                        <?php
                        wc_dropdown_variation_attribute_options(
                            array(
                                'options'          => $options,
                                'attribute'        => $attribute_name,
                                'product'          => $product,
                                'selected'         => isset($selected_attributes[$attribute_name]) ? $selected_attributes[$attribute_name] : '',
                                'show_option_none' => wc_attribute_label($attribute_name),
                                'class'            => 'nt_variation_select',
                            )
                        );
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php do_action('woocommerce_after_variations_table'); ?>

            <div class="single_variation_wrap">
                <?php do_action('woocommerce_single_variation'); ?>
            </div>
        <?php endif; ?>

        <?php do_action('woocommerce_after_variations_form'); ?>
    </form>

<?php do_action('woocommerce_after_add_to_cart_form');
?>
