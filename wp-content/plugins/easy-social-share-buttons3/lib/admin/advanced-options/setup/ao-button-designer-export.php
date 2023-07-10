<?php
/**
 * Create and manage new share buttons
 */

$loadingOptions = isset($_REQUEST['loadingOptions']) ? $_REQUEST['loadingOptions'] : array();
$network = isset($loadingOptions['network']) ? $loadingOptions['network'] : '';
$network_mode = isset($loadingOptions['network_mode']) ? $loadingOptions['network_mode'] : 'export';
$network_setup = array();

if ($network_mode == 'export') {
    $network_code = '';
    
    if ($network != '') {
        $network_setup = essb_get_custom_button_settings($network);
        $network_code = json_encode($network_setup);
        $network_code = base64_encode($network_code);
    }
}

if (function_exists('essb_advancedopts_settings_group')) {
    essb_advancedopts_settings_group('essb_options_customshare_networks');
}



/**
 * Button parameters
 */
essb_advancedopts_section_open('ao-small-values');

if ($network_mode == 'export') {
    essb5_draw_textarea_option('export_share_output_code', esc_html__('Export Code', 'essb'), esc_html__('This is the export code of the network you choose. Copy this code and paste it into the Import screen of another Easy Social Share Buttons for a WordPress installation. ', 'essb'), true, $network_code);
}

if ($network_mode == 'import') {
    essb5_draw_textarea_option('input_share_network_code', esc_html__('Network Code', 'essb'), esc_html__('Place the network code in the field below and press the Import button.', 'essb'), true, '');
    
    echo '<div class="essb-flex-grid-r">';
    echo '<div class="essb-flex-grid-c">';
    echo '<span class="ao-new-subscribe-design ao-save-import-sharecustom-button" data-for="essb_options_icon" data-picker="ao-svg-file"><span class="essb_icon fa fa-upload"></span><span>Import</span></span>';
    echo '</div>';
    echo '</div>';
}

essb_advancedopts_section_close();