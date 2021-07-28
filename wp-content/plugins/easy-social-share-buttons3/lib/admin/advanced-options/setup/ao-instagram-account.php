<?php 
$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();
$account_id = isset($loadingOptions['account']) ? $loadingOptions['account'] : '';

if (!class_exists('ESSBInstagramFeed')) {
    include_once (ESSB3_MODULES_PATH . 'instagram-feed/class-instagram-feed.php');
}

$account_settings = ESSBInstagramFeed::get_account($account_id);

if (function_exists('essb_advancedopts_settings_group')) {
	essb_advancedopts_settings_group('essb_options_ig_accounts');
}

essb_advancedopts_section_open('ao-small-values');

echo '<input type="hidden" name="ig_account_id" id="ig_account_id" value="'.$account_id.'"/>';

$account_types = array('personal' => esc_html__('Personal', 'essb'));
essb5_draw_select_option('ig_account_type', esc_html__('Account type', 'essb'), '', $account_types, true, essb_array_value('account_type', $account_settings));


essb5_draw_textarea_option('token', esc_html__('Personal Token', 'essb'), '', true, essb_array_value('token', $account_settings));

echo '<div class="essb-options-helprow"><div class="help-details" style="display: block;">';

echo '<div class="desc">'.esc_html__('You need to put a valid personal Instagram token. This token is required to connect with the new Instagram Basic Display API. Find out below how to generate your token.', 'essb').'</div>';
echo '<div class="buttons">';
echo '<div class="single-button"><a href="https://docs.socialsharingplugin.com/knowledgebase/how-to-get-an-instagram-token-for-showing-feed-on-your-website/" target="_blank" class="button-help">How to get my token<i class="fa fa-external-link"></i></a></div>';

echo '<div class="shortcode-button" style="display: flex; margin-top: 10px; width: 100%;">';
echo '<a href="#" class="ao-options-btn ao-validate-ig-token" style="color:#fff; width: 100%; text-align:center; font-size: 15px;">'.esc_html__('Validate Token Key', 'essb').'</a>';
echo '</div>';

echo '</div>';
echo '</div></div>';

essb5_draw_input_option('userid', esc_html__('User ID', 'essb'), '', true, true, essb_array_value('userid', $account_settings));
essb5_draw_input_option('username', esc_html__('Username', 'essb'), '', true, true, essb_array_value('username', $account_settings));

essb5_draw_heading('Bio', '5');
essb5_draw_input_option('display_name', esc_html__('Profile name', 'essb'), '', true, true, essb_array_value('display_name', $account_settings));
essb5_draw_editor_option('display_bio', esc_html__('Profile bio', 'essb'), esc_html__('HTML code and shortcodes are supported', 'essb'), 'htmlmixed', true, essb_array_value('display_bio', $account_settings));
essb5_draw_file_option('display_pic', esc_html__('Profile pic', 'essb'), '', true, essb_array_value('display_pic', $account_settings));


