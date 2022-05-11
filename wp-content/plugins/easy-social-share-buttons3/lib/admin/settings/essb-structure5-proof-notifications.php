<?php

// Creating setup for the Social Proof Notifications
ESSBControlCenter::register_sidebar_section_menu('proof-notifications', 'setup', esc_html__('Setup', 'essb'));

ESSBOptionsStructureHelper::help('proof-notifications', 'setup', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/showing-social-proof-notifications-for-shares/'));

ESSBOptionsStructureHelper::panel_start('proof-notifications', 'setup', esc_html__('Enbable social proof notifications for shares', 'essb'), '', 'fa21 fa fa-share-alt', array("mode" => "switch", 'switch_id' => 'proofnotifications_show'));
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_start', esc_html__('Delay before start showing', 'essb'), esc_html__('A numeric value for seconds that plugin will wait before showing the first notification (if blank default is 10)', 'essb') );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_stay', esc_html__('Display time', 'essb'), esc_html__('Set how long to stay on screen each notificataion (in seconds).', 'essb') );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_wait', esc_html__('Delay between', 'essb'), esc_html__('Set how long to wait before showing next notificataion (in seconds).', 'essb') );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_counter', esc_html__('Number of notifications', 'essb'), '' );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_min', esc_html__('Minimal share number value', 'essb'), '' );
ESSBControlCenter::set_description_inline('proofnotifications_message');
ESSBOptionsStructureHelper::field_textarea('proof-notifications', 'setup', 'proofnotifications_message', esc_html__('Customize message', 'essb'), '{title} is highly popular post having {value} {network} shares[nl]Share with your friends');
ESSBOptionsStructureHelper::field_switch('proof-notifications', 'setup', 'proofnotifications_loop', esc_html__('Loop notifications', 'essb'), '');
$appear_options = array ("left" => esc_html__('Bottom left', 'essb'), "right" => esc_html__('Bottom right', 'essb'));
ESSBOptionsStructureHelper::field_select('proof-notifications', 'setup', 'proofnotifications_appear', esc_html__('Appear at', 'essb'), '', $appear_options);

ESSBOptionsStructureHelper::field_switch('proof-notifications', 'setup', 'proofnotifications_activity', esc_html__('Show share activity notification', 'essb'), esc_html__('Require the internal plugin analytics to run for getting the source of activity. Showing notifications displaying the recent sharing over the social networks.', 'essb'));
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_activity_counter', esc_html__('Number of activity notifications', 'essb'), '' );
ESSBControlCenter::set_description_inline('proofnotifications_activity_message');
ESSBOptionsStructureHelper::field_textarea('proof-notifications', 'setup', 'proofnotifications_activity_message', esc_html__('Customize activity message', 'essb'), 'Someone share {title} on {network}[nl]{value} ago');
ESSBControlCenter::relation_enabled('setup', 'proofnotifications_activity', array('proofnotifications_activity_counter', 'proofnotifications_activity_message'));

ESSBOptionsStructureHelper::field_component('proof-notifications', 'setup', 'essb5_advanced_proof_promo', 'false');

ESSBOptionsStructureHelper::panel_end('proof-notifications', 'setup');

function essb5_advanced_proof_promo() {
	echo '<div class="ao-settings-section ao-settings-section-activate ao-deactivate_module_metrics-panel ao-additional-features-activate">';
	echo '<div>';
    echo '
You are using the lite version of Social Proof Notifications included for free in the Easy Social Share Buttons for WordPress. Do you need additional options? Learn more for the Social Proof Notifications Pro add-on:

<ul>
<li><i class="fa fa-check" aria-hidden="true"></i> Removing the branding from the message</li>
<li><i class="fa fa-check" aria-hidden="true"></i> Detailed control over appearance</li>
<li><i class="fa fa-check" aria-hidden="true"></i> Advanced notifications for share counter including networks selection and message variations (require the share counters)</li>
<li><i class="fa fa-check" aria-hidden="true"></i> Notifications for followers via the Social Followers Counter module</li>
<li><i class="fa fa-check" aria-hidden="true"></i> Subscribe to mailing list message via the subscribe forms module</li>
<li><i class="fa fa-check" aria-hidden="true"></i> WooCommerce recent orders</li>
<li><i class="fa fa-check" aria-hidden="true"></i> Advanced share activity messages</li>
</ul>
    ';
	echo '<a href="https://1.envato.market/eP1aQ" target="_blank"><b>'.esc_html__('Learn more for Social Proof Notifications Extension', 'essb').'</b></a>';
	echo '</div>';
	echo '</div>';
}