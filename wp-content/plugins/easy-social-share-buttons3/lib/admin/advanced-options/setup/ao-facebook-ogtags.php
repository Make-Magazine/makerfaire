<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('sso_deactivate_analyzer', esc_html__('Disable social media optimization assistance', 'essb'), esc_html__('The option will disable messages you see on posts for potential improvement of the social media sharing details. Enabling the option won\'t affect the generation of the tags or work of the sharing.', 'essb'));

essb5_draw_heading( esc_html__('Images', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_switch_option('sso_imagesize', esc_html__('Generate image size tags', 'essb'), esc_html__('Image size tags are not required but may help social networks to identify the shared image faster.', 'essb'));
essb5_draw_switch_option('sso_external_images', esc_html__('Allow external images', 'essb'), esc_html__('Include a text field where you can provide the image URL. By default, the plugin allows only the usage of images located in the WordPress media library.', 'essb'));
essb5_draw_switch_option('sso_multipleimages', esc_html__('Allow multiple images', 'essb'), esc_html__('Add fields for up to 5 additional images you can select for social media sharing.', 'essb'));
essb5_draw_switch_option('sso_gifimages', esc_html__('GIF images support', 'essb'), esc_html__('Set Yes if you have featured image animated GIF images (not required for static GIF images).', 'essb'));
essb5_draw_field_group_close();
essb5_draw_heading( esc_html__('WooCommerce', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_switch_option('sso_deactivate_woogallery', esc_html__('Deactivate gallery integration', 'essb'), esc_html__('Don\'t include in the social media tags the gallery images.', 'essb'));
essb5_draw_switch_option('sso_deactivate_woocommerce', esc_html__('Deactivate product tags', 'essb'), esc_html__('Don\'t create product-specific tags - price, availability, promotion, etc. Enabling the option won\'t prevent your products from sharing - they just will have the regular tags like a post or page (not like a product).', 'essb'));
essb5_draw_field_group_close();
essb5_draw_heading( esc_html__('Expert', 'essb'), '6');
essb5_draw_field_group_open();
essb5_draw_switch_option('sso_httpshttp', esc_html__('Use http version of page in social tags', 'essb'), esc_html__('If you recently move from http to https and realize that shares are gone please activate this option and check are they back.', 'essb'));
essb5_draw_switch_option('sso_apply_the_content', esc_html__('Extract full content when generating description', 'essb'), esc_html__('If you see shortcodes in your description activate this option to extract as full rendered content. Warning! Activation of this option may affect work of other plugins or may lead to missing share buttons. If you notice something that is not OK with site immediately deactivate it.', 'essb'));
essb5_draw_field_group_close();

essb_advancedopts_section_close();