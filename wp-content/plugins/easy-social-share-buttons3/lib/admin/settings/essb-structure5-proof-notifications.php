<?php

// Creating setup for the Social Proof Notifications
ESSBControlCenter::register_sidebar_section_menu('proof-notifications', 'setup', esc_html__('Setup', 'essb'));

ESSBOptionsStructureHelper::help('proof-notifications', 'setup', '', '', array('Help With Settings' => 'https://docs.socialsharingplugin.com/knowledgebase/showing-social-proof-notifications-for-shares/'));

ESSBOptionsStructureHelper::panel_start('proof-notifications', 'setup', esc_html__('Enbable display of social proof notifications for shares', 'essb'), esc_html__('The social proof notifications for sharing requires share counters to run on your site. The generated shares during the share counter update are also the source where the plugin takes data.', 'essb'), 'fa21 fa fa-share-alt', array("mode" => "switch", 'switch_id' => 'proofnotifications_show'));
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_start', esc_html__('Delay before start showing', 'essb'), esc_html__('A numeric value for seconds that plugin will wait before showing the first notification (if blank default is 10)', 'essb') );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_counter', esc_html__('Number of notifications', 'essb'), '' );
ESSBOptionsStructureHelper::field_textbox('proof-notifications', 'setup', 'proofnotifications_min', esc_html__('Minimal share number value', 'essb'), '' );
ESSBOptionsStructureHelper::field_switch('proof-notifications', 'setup', 'proofnotifications_loop', esc_html__('Loop notifications', 'essb'), '');
$appear_options = array ("left" => esc_html__('Bottom left', 'essb'), "right" => esc_html__('Bottom right', 'essb'));
ESSBOptionsStructureHelper::field_select('proof-notifications', 'setup', 'proofnotifications_appear', esc_html__('Appear at', 'essb'), '', $appear_options);
ESSBOptionsStructureHelper::field_component('proof-notifications', 'setup', 'essb5_advanced_proof_promo', 'false');

ESSBOptionsStructureHelper::panel_end('proof-notifications', 'setup');

function essb5_advanced_proof_promo() {
	echo '<div class="ao-settings-section ao-settings-section-activate ao-deactivate_module_metrics-panel ao-additional-features-activate">';
	echo '<div>';
	esc_html_e('Do you need additional options? Social Proof Notifications for Easy Social Share Buttons is a powerful notification message plugin. You will have access to many placement and customization options, multiple messages per type, share notifications, subscribe notifications, followers notifications or WooCommerce orders.', 'essb');
	echo '<br/><br/><a href="https://1.envato.market/eP1aQ" target="_blank"><b>'.esc_html__('Learn more for Social Proof Notifications Extension', 'essb').'</b></a>';
	echo '</div>';
	echo '</div>';
}