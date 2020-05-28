<?php
if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options');
}

essb_advancedopts_section_open('ao-small-values');

essb5_draw_switch_option('sso_imagesize', esc_html__('Generate image size tags', 'essb'), esc_html__('Image size tags are not required - they are optional but in some cases without them Facebook may not recongnize the correct image. In case this happens to you try activating this option.', 'essb'));
essb5_draw_switch_option('sso_gifimages', esc_html__('GIF images support', 'essb'), esc_html__('Set this to Yes if your site uses GIF images as featured or optimized for sharing and you wish to see that appearing inside Facebook sharing', 'essb'));
essb5_draw_switch_option('sso_external_images', esc_html__('Allow usage of external images', 'essb'), esc_html__('The default configuration of social optimization on each post does not allow the selection of images that are not part of the media library. If you are using offloaded images on CDN or content network you may need to set this option to Yes. The activation will allow setting up a manual image URL along with the file picker.', 'essb'));
essb5_draw_switch_option('sso_deactivate_woogallery', esc_html__('Deactivate WooCommerce gallery integration', 'essb'), esc_html__('Set this option to Yes if you wish to avoid generation of all gallery images into social share optimization tags.', 'essb'));
essb5_draw_switch_option('sso_deactivate_woocommerce', esc_html__('Deactivate WooCommerce product tags', 'essb'), esc_html__('Set this option to Yes if you need to deactivate the generation of product social share optimization tags. Those tags contains deataials about pricing, stock available and etc.', 'essb'));
essb5_draw_switch_option('sso_multipleimages', esc_html__('Allow more than one share image', 'essb'), esc_html__('This option will allow to choose up to 5 additional images on each post that will appear in social share optimization tags.', 'essb'));
essb5_draw_switch_option('sso_deactivate_analyzer', esc_html__('Stop the Social Media Assistant', 'essb'), esc_html__('Set to Yes if you wish to stop the assistant from analyzing your content and provide feedback for improvements. Setting this option to Yes will not reflect the work of social share optimization tags.', 'essb'));
essb5_draw_switch_option('sso_httpshttp', esc_html__('Use http version of page in social tags', 'essb'), esc_html__('If you recently move from http to https and realize that shares are gone please activate this option and check are they back.', 'essb'));
essb5_draw_switch_option('sso_apply_the_content', esc_html__('Extract full content when generating description', 'essb'), esc_html__('If you see shortcodes in your description activate this option to extract as full rendered content. Warning! Activation of this option may affect work of other plugins or may lead to missing share buttons. If you notice something that is not OK with site immediately deactivate it.', 'essb'));



essb_advancedopts_section_close();