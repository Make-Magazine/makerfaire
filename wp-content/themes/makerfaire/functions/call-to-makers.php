<?php 

function call_for_makers_settings_page() {
    add_settings_section("section", "", null, "call_for_makers");
    add_settings_field("call_for_makers_checkbox", "Flip this switch to activate the call for makers button on the manage entries page", "call_for_makers_checkbox_display", "call_for_makers", "section");  
    add_settings_field("call_for_makers_start_date", "Enter the Start Date", "call_for_makers_start_date_display", "call_for_makers", "section"); 
    add_settings_field("call_for_makers_end_date", "Enter the End Date", "call_for_makers_end_date_display", "call_for_makers", "section"); 
    register_setting("section", "call_for_makers_checkbox");
    register_setting("section", "call_for_makers_start_date");
    register_setting("section", "call_for_makers_end_date");
}

function call_for_makers_checkbox_display() {
   ?>
        <!-- Here we are comparing stored value with 1. Stored value is 1 if user checks the checkbox otherwise empty string. -->
        <input type="checkbox" name="call_for_makers_checkbox" value="1" <?php checked(1, get_option('call_for_makers_checkbox'), true); ?> />
   <?php
}

function call_for_makers_start_date_display() {
   ?>
        <input type="input" name="call_for_makers_start_date" value="<?php echo esc_attr( get_option('call_for_makers_start_date') ); ?>" />
   <?php
}
function call_for_makers_end_date_display() {
   ?>
        <input type="input" name="call_for_makers_end_date" value="<?php echo esc_attr( get_option('call_for_makers_end_date') ); ?>" />
   <?php
}

add_action("admin_init", "call_for_makers_settings_page");

function call_for_makers_page() {
  ?>
      <div class="wrap">
         <h1>Call For Makers</h1>
 
         <form method="post" action="options.php">
            <?php
               settings_fields("section");
 
               do_settings_sections("call_for_makers");
                 
               submit_button();
            ?>
         </form>
      </div>
   <?php
}

function menu_item() {
  add_submenu_page("options-general.php", "Call for Makers", "Call for Makers", "manage_options", "call_for_makers", "call_for_makers_page");
}
 
add_action("admin_menu", "menu_item");