<?php 

function call_to_makers_settings_page()
{
    add_settings_section("section", "Section", null, "demo");
    add_settings_field("call-to-makers-checkbox", "Call to Makers", "call-to-makers_checkbox_display", "call-to-makers", "section");  
    register_setting("section", "call-to-makers-checkbox");
}

function call_to_makers_checkbox_display()
{
   ?>
        <!-- Here we are comparing stored value with 1. Stored value is 1 if user checks the checkbox otherwise empty string. -->
        <input type="checkbox" name="demo-checkbox" value="1" <?php checked(1, get_option('demo-checkbox'), true); ?> />
   <?php
}

add_action("admin_init", "call_to_makers_settings_page");

function call_to_makers_page()
{
  ?>
      <div class="wrap">
         <h1>Call To Makers</h1>
 
         <form method="post" action="options.php">
            <?php
               settings_fields("section");
 
               do_settings_sections("call_to_makers");
                 
               submit_button();
            ?>
         </form>
      </div>
   <?php
}

function menu_item()
{
  add_submenu_page("options-general.php", "Call to Makers", "Call to Makers", "manage_options", "call-to-makers", "call_to_makers_page");
}
 
add_action("admin_menu", "menu_item");