<?php
/**
 * Config
 *
 * @package WordPress
 * @subpackage seed_wnb
 * @since 0.1.0
 */

/**
 * Config
 */
add_action('init', 'seed_wnb_init');
function seed_wnb_init(){
    global $seed_wnb;
/**
 * Create new menus
 * Required: type => "add_options_page|"
 */
$seed_wnb->options[ ] = array(
    "type" => "menu",
    "menu_type" => "add_options_page",
    "page_name" => __( "Notification Bar", 'wordpress-notification-bar' ),
    "menu_slug" => "seed_wnb",
    "layout" => "2-col" 
);

/**
 * Tabs are optional
 */
// $seed_wnb->options[ ] = array(
//     "type" => "tab",
//     "id" => "seed_wnb_tab_1",
//     "label" => __( "Settings", 'wordpress-notification-bar' )
// );

/**
 * Settings for tab
 * Create 'validate_function' if using custom validation function.
 */
$seed_wnb->options[ ] = array(
    "type" => "setting",
    "id" => "seed_wnb_settings_1"
);

/**
 * Create unique id, label, create 'desc_callback' if you need custom description.
 */
$seed_wnb->options[ ] = array(
    "type" => "section",
    "id" => "seed_wnb_section_1",
    "label" => __( "Message", 'wordpress-notification-bar' ),
    "desc_callback" => 'seed_wnb_section_1_callback',
);

/**
 * Choose type, id, label, attach to a section and setting id.
 * Create 'callback' function if you are creating a custom field.
 * Optional desc, default_value, class, option_values, validate
 * Types upload, textbox, select, textarea, radio, checkbox, color
 */


$seed_wnb->options[ ] = array(
    "type" => "checkbox",
    "id" => "enabled",
    "label" => __( "Enabled", 'wordpress-notification-bar' ),
    "desc" => __( "Check if you want to enable the notification bar on your site.", "wordpress-notification-bar" ),
    "option_values" => array(
        '1' => __( 'Yes', 'wordpress-notification-bar' )
    )
);


$seed_wnb->options[ ] = array(
    "type" => "textbox",
    "id" => "msg",
    "label" => __( "Message", 'wordpress-notification-bar' ),
    "desc" => __( "Enter your message.", 'wordpress-notification-bar' ),
);

$seed_wnb->options[ ] = array(
    "type" => "textbox",
    "id" => "button_label",
    "label" => __( "Button Label", 'wordpress-notification-bar' ),
    "desc" => __( "Enter a title for your call to action button. If not entered the button will not appear.", 'wordpress-notification-bar' ),
);

$seed_wnb->options[ ] = array(
    "type" => "textbox",
    "id" => "button_link",
    "label" => __( "Button Link", 'wordpress-notification-bar' ),
    "desc" => __( "Enter a link for the button.", 'wordpress-notification-bar' ),
    "validate" => 'sanitize_url',
);

$seed_wnb->options[ ] = array(
    "type" => "checkbox",
    "id" => "button_target",
    "label" => __( "Button Target", 'wordpress-notification-bar' ),
    "desc" => __( "Open link in new window. If this is not checked the link will open in the same window.", 'wordpress-notification-bar' ),
    "option_values" => array(
        '1' => __( 'Yes', 'wordpress-notification-bar' )
    )
);


// Section 2
$seed_wnb->options[ ] = array(
    "type" => "section",
    "id" => "seed_wnb_section_2",
    "label" => __( "Style", 'wordpress-notification-bar' )
);

$seed_wnb->options[ ] = array(
    "type" => "checkbox",
    "id" => "position",
    "label" => __( "Position", 'wordpress-notification-bar' ),
    "option_values" => array(
        '1' => __( 'Sticky, stays at the top of the page even when you scroll down.', 'wordpress-notification-bar' ),
    )
);

$seed_wnb->options[ ] = array(
    "type" => "color",
    "id" => "bg_color",
    "label" => __( "Background Color", 'wordpress-notification-bar' ),
    "default_value" => "#fae985",
    "validate" => 'color',
);
}



function seed_wnb_section_1_callback( )
{
    _e('<p>Enter your information then click "Save Changes" to see a preview above. Check "Enabled" when you are ready to go live.</p>', 'wordpress-notification-bar');
}
